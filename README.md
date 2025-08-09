## Installation

- docker run --rm -u "$(id -u):$(id -g)" -v "$PWD:/var/www/html" -w /var/www/html composer install --ignore-platform-req=ext-gd
- docker compose up -d app
- docker compose exec -u "$(id -u):$(id -g)" app cp .env.example .env
- docker compose exec app chmod 777 -R storage bootstrap/cache
- docker compose exec -u "$(id -u):$(id -g)" app php artisan migrate
- docker compose exec -u "$(id -u):$(id -g)" app php artisan db:seed

## Execute commands

- docker compose up -d app