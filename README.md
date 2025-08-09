# Guia de Instalação e Uso

Este projeto utiliza Docker e Docker Compose para facilitar a configuração. Siga os passos abaixo com atenção. É muito importante executar os comandos de permissão para evitar erros de escrita/leitura no Laravel (storage e cache).

## Pré-requisitos
- Docker instalado
- Docker Compose instalado

## Passo a passo (Instalação)
1. Instalar dependências do Laravel (via Composer dentro do container):
   - docker run --rm -u "$(id -u):$(id -g)" -v "$PWD/laravel-backend/:/var/www/html" -w /var/www/html composer install --ignore-platform-req=ext-gd

2. Subir o serviço da aplicação:
   - docker compose up -d app

3. Copiar o arquivo de ambiente e gerar a key da aplicação:
   - docker compose exec -u "$(id -u):$(id -g)" app cp .env.example .env
   - docker compose exec -u "$(id -u):$(id -g)" app php artisan key:generate

4. (Muito importante) Ajustar permissões dos diretórios necessários pelo Laravel:
   - docker compose exec app chmod -R 777 storage bootstrap/cache
   - Opcional (recomendado em alguns ambientes):
     - docker compose exec app chown -R www-data:www-data storage bootstrap/cache

   Observação:
   - O comando chmod garante que o Laravel consiga escrever logs e cache.
   - O chown ajusta o proprietário para o usuário do servidor web dentro do container (www-data), evitando problemas intermitentes de permissão.

5. Rodar as migrações e os seeders:
   - docker compose exec -u "$(id -u):$(id -g)" app php artisan migrate
   - docker compose exec -u "$(id -u):$(id -g)" app php artisan db:seed

## Postman
- Importe a coleção localizada em: ./postman

## Comandos úteis
- Subir a aplicação (em segundo plano):
  - docker compose up -d app
- Ver logs da aplicação:
  - docker compose logs -f app
- Derrubar containers:
  - docker compose down

## Dicas de Troubleshooting
- Se aparecer erro de permissão (por exemplo, ao gravar logs):
  - Reexecute os comandos de permissão do Passo 4.
- Em ambientes Linux, o uso de -u "$(id -u):$(id -g)" evita que arquivos sejam criados como root no host.
