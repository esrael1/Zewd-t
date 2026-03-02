# Deploy to Render (Docker, All-in-One)

This project deploys as **one Render Web Service** using the root `Dockerfile`.
Frontend is built by Vite and served by Laravel from `backend/public`.

## 1) Push Code

Push the latest branch containing:
- `Dockerfile`
- `render.yaml`

## 2) Create Render Blueprint

1. Open Render dashboard.
2. Click `New +` -> `Blueprint`.
3. Connect/select your Git repo.
4. Render detects `render.yaml` and provisions:
- Web service: `zewd`
- PostgreSQL database: `zewd-db`

## 3) Set Required Environment Variables

In the `zewd` web service, set these values if not already set:

- `APP_KEY` (required)
  - Must be a Laravel key, format: `base64:...`
  - Generate locally (inside `backend`):
    - `php artisan key:generate --show`
- `APP_URL` (required)
  - Use your Render service URL, e.g. `https://zewd.onrender.com`
- `JITSI_APP_ID` (if your app uses Jitsi JWT auth)
- `JITSI_JWT_SECRET` (if your app uses Jitsi JWT auth)

These are already handled by `render.yaml` defaults:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_*` values from `zewd-db`
- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`

## 4) Deploy

1. Trigger deploy (or wait for auto-deploy).
2. Watch logs until app is live.

Container startup runs:
- `php artisan package:discover --ansi`
- `php artisan storage:link` (non-fatal if already exists)
- `php artisan config:cache`
- `php artisan migrate --force`
- `php artisan serve --host=0.0.0.0 --port=$PORT`

## 5) Verify

After deploy:
1. Open `/` and confirm frontend loads.
2. Hit an API route, e.g. `/api/login` (expected method/auth behavior).
3. Check Render logs for migration or DB connection errors.

## Common Issues and Fixes

### A) `No application encryption key has been specified`
Set `APP_KEY` in Render env, then redeploy.

### B) DB connection errors (`SQLSTATE`, host/user/password)
Confirm `DB_*` env vars are linked from `zewd-db` in Render.

### C) 404 on frontend routes (deep links)
Laravel catch-all route in `backend/routes/web.php` should serve `public/index.html`.
If changed, restore that catch-all route and redeploy.

### D) Session/cache table errors
Keep:
- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
Unless you intentionally create and migrate DB-backed session/cache tables.

### E) Build fails during composer scripts
Current Dockerfile installs PHP deps with `--no-scripts` and runs artisan during startup.
If this behavior is changed, ensure app files exist before artisan script hooks run.