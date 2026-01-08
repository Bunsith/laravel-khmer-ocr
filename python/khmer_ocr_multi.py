import json
import os
import sys
import tempfile

import cv2
import numpy as np

from recognize import recognize


def detect_line_boxes(gray):
    blur = cv2.GaussianBlur(gray, (3, 3), 0)
    _, thresh = cv2.threshold(blur, 0, 255, cv2.THRESH_BINARY_INV + cv2.THRESH_OTSU)

    kernel_width = max(25, gray.shape[1] // 40)
    kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (kernel_width, 3))
    dilated = cv2.dilate(thresh, kernel, iterations=1)

    contours, _ = cv2.findContours(dilated, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    boxes = []
    for contour in contours:
        x, y, w, h = cv2.boundingRect(contour)
        if h < 8 or w < 20:
            continue
        boxes.append((x, y, w, h))

    boxes.sort(key=lambda item: (item[1], item[0]))
    return boxes


def detect_word_boxes(gray):
    blur = cv2.GaussianBlur(gray, (3, 3), 0)
    _, thresh = cv2.threshold(blur, 0, 255, cv2.THRESH_BINARY_INV + cv2.THRESH_OTSU)

    kernel_width = max(10, gray.shape[1] // 70)
    kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (kernel_width, 3))
    dilated = cv2.dilate(thresh, kernel, iterations=1)

    contours, _ = cv2.findContours(dilated, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    boxes = []
    min_w = max(12, gray.shape[1] // 200)
    for contour in contours:
        x, y, w, h = cv2.boundingRect(contour)
        if h < 8 or w < min_w:
            continue
        boxes.append((x, y, w, h))

    boxes.sort(key=lambda item: item[0])
    return boxes


def crop_with_padding(gray, box):
    x, y, w, h = box
    pad_y = max(2, int(h * 0.12))
    pad_x = max(2, int(w * 0.02))
    y0 = max(0, y - pad_y)
    y1 = min(gray.shape[0], y + h + pad_y)
    x0 = max(0, x - pad_x)
    x1 = min(gray.shape[1], x + w + pad_x)
    return gray[y0:y1, x0:x1], (x0, y0, x1 - x0, y1 - y0)


def recognize_line(line_gray, tmp_dir, line_index, bbox=None):
    word_boxes = detect_word_boxes(line_gray)

    if len(word_boxes) <= 1:
        line_path = os.path.join(tmp_dir, f"line_{line_index}.png")
        cv2.imwrite(line_path, line_gray)
        result = recognize(line_path)
        result["line"] = line_index
        if bbox:
            result["bbox"] = bbox
        return result

    gaps = []
    for index in range(len(word_boxes) - 1):
        x, _, w, _ = word_boxes[index]
        next_x, _, _, _ = word_boxes[index + 1]
        gaps.append(next_x - (x + w))

    gap_threshold = max(12, line_gray.shape[1] // 50)
    if not any(gap >= gap_threshold for gap in gaps):
        line_path = os.path.join(tmp_dir, f"line_{line_index}.png")
        cv2.imwrite(line_path, line_gray)
        result = recognize(line_path)
        result["line"] = line_index
        if bbox:
            result["bbox"] = bbox
        return result

    word_texts = []
    word_confidences = []
    for word_index, box in enumerate(word_boxes, start=1):
        x, y, w, h = box
        crop = line_gray[y : y + h, x : x + w]
        word_path = os.path.join(tmp_dir, f"line_{line_index}_word_{word_index}.png")
        cv2.imwrite(word_path, crop)
        res = recognize(word_path)
        text = res.get("text", "")
        if text:
            word_texts.append(text)
        word_confidences.append(res.get("text_confidence", 0.0))

    line_confidence = float(np.mean(word_confidences)) if word_confidences else 0.0
    line_text = " ".join(word_texts)

    payload = {
        "text": line_text,
        "text_confidence": line_confidence,
        "line": line_index,
    }
    if bbox:
        payload["bbox"] = bbox
    return payload


def run(image_path):
    gray = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
    if gray is None:
        raise RuntimeError("Failed to read image file.")

    boxes = detect_line_boxes(gray)
    results = []

    with tempfile.TemporaryDirectory() as tmp_dir:
        if not boxes:
            results.append(recognize_line(gray, tmp_dir, 1))
        else:
            for index, box in enumerate(boxes, start=1):
                crop, rect = crop_with_padding(gray, box)
                res = recognize_line(
                    crop,
                    tmp_dir,
                    index,
                    {"x": rect[0], "y": rect[1], "w": rect[2], "h": rect[3]},
                )
                results.append(res)

    confidences = [item.get("text_confidence", 0.0) for item in results if isinstance(item, dict)]
    avg_confidence = float(np.mean(confidences)) if confidences else 0.0

    return {
        "text": "\n".join([item.get("text", "") for item in results if isinstance(item, dict)]),
        "text_confidence": avg_confidence,
        "lines": results,
        "line_count": len(results),
    }


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Missing image path."}))
        sys.exit(2)

    payload = run(sys.argv[1])
    print(json.dumps(payload, ensure_ascii=False))
