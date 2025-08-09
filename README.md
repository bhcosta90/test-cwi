`# Test CWI - Backend Laravel + Microserviço de Posts

Este projeto contém:
- Um backend em Laravel exposto via Apache (serviço app)
- Um banco de dados MySQL (serviço mysql)
- Um microserviço Node.js para posts (serviço posts)
- Coleções do Postman para testar as APIs (pasta ./postman)

Default de portas (configuráveis via variáveis):
- Laravel (app): http://localhost:${APP_PORT:-8200}
- Microserviço posts: http://localhost:${MS_POSTS_PORT:-3200}
- MySQL: localhost:${FORWARD_DB_PORT:-3306}

Requisitos:
- Docker e Docker Compose instalados

## Subindo o projeto (modo rápido)
1. Instalar dependências do Laravel via Composer (dentro de um container):
   docker run --rm -u "$(id -u):$(id -g)" -v "$PWD/laravel-backend/:/var/www/html" -w /var/www/html composer install --ignore-platform-req=ext-gd
2. Subir os serviços (app, mysql, posts):
   docker compose up -d
3. Criar arquivo de ambiente do Laravel:
   docker compose exec -u "$(id -u):$(id -g)" app cp .env.example .env
4. Ajustar permissões de pastas de cache e storage:
   docker compose exec app chmod 777 -R storage bootstrap/cache
5. Executar migrações e seeds:
   docker compose exec -u "$(id -u):$(id -g)" app php artisan migrate
   docker compose exec -u "$(id -u):$(id -g)" app php artisan db:seed

Após esses passos:
- API Laravel: http://localhost:8200
- Posts service: http://localhost:3200

Observação: as portas podem ser alteradas exportando APP_PORT, MS_POSTS_PORT e FORWARD_DB_PORT antes do docker compose up.

## Variáveis de ambiente úteis
- APP_PORT: porta local para expor o Apache do Laravel (padrão 8200)
- MS_POSTS_PORT: porta local para o microserviço de posts (padrão 3200)
- FORWARD_DB_PORT: porta local do MySQL (padrão 3306)

## Banco de dados
- Host: 127.0.0.1
- Porta: ${FORWARD_DB_PORT:-3306}
- Base: laravel
- Usuário root, senha password (ver docker-compose.yaml)
- No .env do Laravel, os valores padrão devem funcionar ao usar Docker Compose.

## Comandos úteis do dia a dia
- Subir os serviços:
  docker compose up -d
- Ver logs:
  docker compose logs -f app
  docker compose logs -f posts
  docker compose logs -f mysql
- Executar comandos Artisan:
  docker compose exec -u "$(id -u):$(id -g)" app php artisan <comando>
- Rodar testes do Laravel:
  docker compose exec -u "$(id -u):$(id -g)" app php artisan test

## Testando com Postman
- Importe a coleção da pasta ./postman no Postman.
- Ajuste a variável de ambiente base_url para http://localhost:8200 (Laravel) e, quando necessário, posts_base_url para http://localhost:3200.

## Troubleshooting
- Portas em uso: altere APP_PORT/MS_POSTS_PORT/FORWARD_DB_PORT ou libere as portas ocupadas.
- Permissões em storage/bootstrap/cache: rode novamente o comando de chmod mostrado acima.
- Reset do banco (cuidado: destrói dados):
  docker compose down -v && docker compose up -d && docker compose exec -u "$(id -u):$(id -g)" app php artisan migrate --seed

## Estrutura dos serviços (docker-compose.yaml)
- app: constrói a imagem a partir do Dockerfile, monta ./laravel-backend em /var/www/html e depende de mysql e posts.
- mysql: imagem mysql:8.0 com persistência em ./data/mysql.
- posts: imagem node:20 que instala dependências e executa node server.js a partir de ./ms-posts.`