# Test CWI - Backend Laravel + Posts Microservice

This project contains:
- A Laravel backend exposed via Apache (service app)
- A MySQL database (service mysql)
- A Node.js microservice for posts (service posts)
- Postman collections to test the APIs (folder ./postman)

Default ports (configurable via environment variables):
- Laravel (app): http://localhost:8200
- Posts microservice: http://localhost:3200
- MySQL: localhost:3306

Requirements:
- Docker and Docker Compose installed

## Starting the project (quick mode)
1. Install Laravel dependencies via Composer (inside a container):
   docker run --rm -u "$(id -u):$(id -g)" -v "$PWD/laravel-backend/:/var/www/html" -w /var/www/html composer install --ignore-platform-req=ext-gd
2. Start the services (app, mysql, posts):
   docker compose up -d
3. Create Laravel environment file:
   docker compose exec -u "$(id -u):$(id -g)" app cp .env.example .env
4. Adjust permissions for cache and storage folders:
   docker compose exec app chmod 777 -R storage bootstrap/cache
5. Run migrations and seeds:
   docker compose exec -u "$(id -u):$(id -g)" app php artisan migrate
   docker compose exec -u "$(id -u):$(id -g)" app php artisan db:seed

After these steps:
- Laravel API: http://localhost:8200
- Posts service: http://localhost:3200

Note: Ports can be changed by exporting APP_PORT, MS_POSTS_PORT, and FORWARD_DB_PORT before running docker compose up.

## Useful environment variables
- APP_PORT: local port to expose Laravel's Apache (default 8200)
- MS_POSTS_PORT: local port for the posts microservice (default 3200)
- FORWARD_DB_PORT: local MySQL port (default 3306)

## Database
- Host: 127.0.0.1
- Port: ${FORWARD_DB_PORT:-3306}
- Database: laravel
- User: root, password: password (see docker-compose.yaml)
- In Laravel's .env, the default values should work when using Docker Compose.

## Useful daily commands
- Start services:
  docker compose up -d
- View logs:
  docker compose logs -f app
  docker compose logs -f posts
  docker compose logs -f mysql
- Run Artisan commands:
  docker compose exec -u "$(id -u):$(id -g)" app php artisan <command>
- Run Laravel tests:
  docker compose exec -u "$(id -u):$(id -g)" app php artisan test

## Testing with Postman
- Import the collection from ./postman into Postman.
- Set the environment variable base_url to http://localhost:8200 (Laravel) and, when needed, posts_base_url to http://localhost:3200.

## Troubleshooting
- Ports in use: change APP_PORT/MS_POSTS_PORT/FORWARD_DB_PORT or free the occupied ports.
- Permissions in storage/bootstrap/cache: run the chmod command shown above again.
- Database reset (warning: destroys data):
  docker compose down -v && docker compose up -d && docker compose exec -u "$(id -u):$(id -g)" app php artisan migrate --seed

## Service structure (docker-compose.yaml)
- app: builds the image from Dockerfile, mounts ./laravel-backend to /var/www/html and depends on mysql and posts.
- mysql: mysql:8.0 image with persistence in ./data/mysql.
- posts: node:20 image that installs dependencies and runs node server.js from ./ms-posts.