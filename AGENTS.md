# Repository Guidelines

## Project Structure & Module Organization
- `app/`: Core Laravel application code (controllers, models, policies, jobs).
- `routes/`: Route definitions such as `routes/web.php`.
- `resources/`: Blade views and frontend assets (`resources/views`, `resources/js`, `resources/css`).
- `public/`: Web root and built assets (Vite emits to `public/build`).
- `database/`: Migrations, factories, and seeders.
- `tests/`: Pest tests in `tests/Feature` and `tests/Unit`.
- `config/`, `bootstrap/`, `storage/`: Framework configuration, bootstrapping, and runtime files.
- `vendor/`: Composer dependencies (do not edit).

## Build, Test, and Development Commands
- `composer install`: Install PHP dependencies.
- `npm install`: Install frontend tooling.
- `composer run setup`: One-time setup (installs deps, copies `.env`, generates key, runs migrations, builds assets).
- `composer run dev`: Start local server, queue listener, and Vite in parallel.
- `php artisan serve`: Run the app server only.
- `npm run dev` / `npm run build`: Vite dev server or production build.
- `composer test` or `php artisan test`: Run the full test suite.

## Coding Style & Naming Conventions
- Indentation is 4 spaces, LF line endings, and trailing whitespace trimmed (see `.editorconfig`).
- YAML files use 2-space indentation.
- PHP follows PSR-4 autoloading (`App\` in `app/`), with class names matching file names.
- Use Laravel Pint for PHP formatting: `vendor/bin/pint`.
- Tests are named `*Test.php` (see `tests/Feature/ExampleTest.php`).

## Testing Guidelines
- Test runner is Pest (via `php artisan test` or `vendor/bin/pest`).
- Unit tests live in `tests/Unit`, feature tests in `tests/Feature`.
- The default test DB is in-memory SQLite as configured in `phpunit.xml`.

## Commit & Pull Request Guidelines
- No Git history is present in this checkout, so there is no established commit convention.
- Use concise, imperative commit messages (e.g., "Add OCR upload validation").
- PRs should include a short description, test steps, and screenshots for UI changes.
- Call out any new env keys, migrations, or queue changes in the PR body.

## Security & Configuration Tips
- Do not commit `.env`; copy from `.env.example` and set `APP_KEY`.
- Store uploads and generated files under `storage/` and use `php artisan storage:link` if needed.
