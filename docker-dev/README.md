# Docker Development Environment

A self-contained Docker setup for running a Laravel application (such as
**RizqEngine**) locally. It runs four containers wired together on a private
network:

| Service      | Image             | Purpose                                    |
|--------------|-------------------|--------------------------------------------|
| `web`        | nginx:1.27-alpine | Serves `src/public`, proxies PHP to `app`  |
| `app`        | php:8.1-fpm (built)| Runs the Laravel code and Artisan/Composer |
| `db`         | mysql:8.0         | MySQL database                             |
| `phpmyadmin` | phpmyadmin:5      | Web UI for the database                    |

Your application code lives in **`src/`** and is bind-mounted into the
containers, so edits on your host are reflected instantly.

---

## Folder structure

```
docker-dev/
├── docker-compose.yml     # Orchestrates all four services
├── .env.example           # Container config template -> copy to .env
├── README.md              # This file
├── nginx/
│   └── default.conf       # Nginx vhost (root = src/public, php-fpm upstream)
├── php/
│   └── Dockerfile         # PHP 8.1-fpm image + extensions + Composer
└── src/                   # <-- YOUR LARAVEL PROJECT GOES HERE
    └── .gitkeep           # placeholder (remove once a project is present)
```

Everything below is run from **inside the `docker-dev/` directory**.

---

## First-time setup

### 1. Copy the environment file

```bash
cp .env.example .env
```

This `.env` configures the **containers** (ports, MySQL credentials, host
UID/GID). It is *not* the Laravel `.env`. On Linux/macOS, set your host user's
ids so files created by the container are owned by you:

```bash
# edit UID/GID in .env to match:
id -u    # -> UID
id -g    # -> GID
```

### 2. Put a Laravel project in `src/`

**Option A — you already have a project** (e.g. this RizqEngine repo):
copy or clone it into `src/` so that `src/artisan`, `src/composer.json`, and
`src/public/` exist.

```bash
# example: clone into src/
git clone https://github.com/FahimAnzamDip/triangle-pos.git src
```

**Option B — scaffold a fresh Laravel app** using the tooling in the image:

```bash
docker compose run --rm app composer create-project laravel/laravel .
```

(The `.` targets the working directory `/var/www/html`, which is `src/`.)

### 3. Configure Laravel's own `.env` (set `DB_HOST=db`)

Create Laravel's env file and point it at the MySQL **container** — the host is
the compose service name `db`, **not** `127.0.0.1`:

```bash
cp src/.env.example src/.env
```

Then edit `src/.env` so the database block matches the credentials in
`docker-dev/.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=db            # <-- the MySQL service name, this is the key line
DB_PORT=3306
DB_DATABASE=triangle_pos
DB_USERNAME=triangle
DB_PASSWORD=secret
```

### 4. Build and start the containers

```bash
docker compose up -d --build
```

### 5. Install dependencies & generate the app key

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

### 6. Run migrations (and seeders)

```bash
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

The `db` service has a healthcheck and `app` waits for it, so migrations will
connect successfully on first boot.

---

## URLs

| What              | URL                                            |
|-------------------|------------------------------------------------|
| Application       | http://localhost:8080                          |
| phpMyAdmin        | http://localhost:8081 (server `db`, user `triangle` / `secret`, or `root` / `root`) |
| MySQL (host port) | `localhost:3306`                               |

Ports are configurable via `APP_PORT`, `PMA_PORT`, and `DB_PORT` in `.env`.

---

## Common docker compose commands

Run these from `docker-dev/`.

```bash
# Start everything in the background
docker compose up -d

# Start and rebuild images after changing the Dockerfile
docker compose up -d --build

# View running services and their status
docker compose ps

# Follow logs (all services, or one)
docker compose logs -f
docker compose logs -f app

# Stop containers (keeps volumes/data)
docker compose stop

# Stop and remove containers + network (keeps the db volume)
docker compose down

# Remove EVERYTHING including the database volume (fresh start)
docker compose down -v
```

### Working inside the app container

```bash
# Open a shell in the PHP container
docker compose exec app bash

# Artisan
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker

# Composer
docker compose exec app composer install
docker compose exec app composer require vendor/package

# One-off command without a running stack (e.g. before first `up`)
docker compose run --rm app php artisan --version
```

### Front-end assets (if the project uses Node/Vite)

Node is not included in the PHP image. Run the build on your host, or add a
`node` service if you prefer to containerize it:

```bash
# on the host, inside src/
npm install && npm run dev
```

---

## Troubleshooting

- **`SQLSTATE[HY000] [2002] Connection refused`** — Laravel's `src/.env` still
  points at `127.0.0.1`. It must be `DB_HOST=db`.
- **Permission errors in `storage/` or `bootstrap/cache/`** — set `UID`/`GID`
  in `.env` to your host user and rebuild: `docker compose up -d --build`.
- **Port already in use** — change `APP_PORT` / `PMA_PORT` / `DB_PORT` in
  `.env` and re-run `docker compose up -d`.
