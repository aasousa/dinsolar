# Ferramenta para Dimensionamento de Sistemas Fotovoltaicos

Este guia descreve os passos necessários para configurar e executar este projeto Laravel com Filament utilizando Docker. O ambiente é composto por três serviços principais: `app` (PHP), `nginx` (servidor web) e `db` (banco de dados MySQL).

## Pré-requisitos

- [Docker](https://www.docker.com/get-started) instalado.
- [Docker Compose](https://docs.docker.com/compose/install/) instalado.

---

## Estrutura de Arquivos Docker

O ambiente Docker é definido pelos seguintes arquivos na raiz do projeto:

- `Dockerfile`: Define a imagem customizada para a aplicação PHP, instalando as extensões necessárias (`pdo_mysql`, `gd`, etc.).
- `docker-compose.yml`: Orquestra a criação e comunicação entre os contêineres (`app`, `nginx`, `db`).
- `docker/nginx/default.conf`: Arquivo de configuração do NGINX para servir a aplicação.

### Conteúdo dos Arquivos Docker

**1. `Dockerfile`**

```dockerfile
# Dockerfile
FROM php:8.2-fpm

# Argumentos para UID/GID para evitar problemas de permissão
ARG UID
ARG GID

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libgd-dev

# Limpa o cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensões do PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Cria o usuário da aplicação
RUN groupadd -g $GID -o laravel
RUN useradd -m -u $UID -g laravel -s /bin/bash laravel

# Define o diretório de trabalho
WORKDIR /var/www

# Muda o proprietário dos arquivos para o usuário laravel
COPY --chown=laravel:laravel . /var/www

# Muda para o usuário laravel
USER laravel

# Expõe a porta 9000
EXPOSE 9000

# Comando padrão
CMD ["php-fpm"]
```

**2. `docker-compose.yml`**

```yaml
# docker-compose.yml
version: '3.8'

services:
  # Serviço da Aplicação (PHP)
  app:
    build:
      context: .
      dockerfile: Dockerfile
      args:
        UID: ${UID:-1000}
        GID: ${GID:-1000}
    container_name: laravel_app
    restart: unless-stopped
    volumes:
      - .:/var/www
    networks:
      - laravel

  # Servidor Web (Nginx)
  nginx:
    image: nginx:alpine
    container_name: laravel_nginx
    restart: unless-stopped
    ports:
      - "${APP_PORT:-8000}:80"
    volumes:
      - .:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    networks:
      - laravel
    depends_on:
      - app

  # Banco de Dados (MySQL)
  db:
    image: mysql:8.0
    container_name: laravel_db
    restart: unless-stopped
    ports:
      - "${DB_PORT:-3306}:3306"
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_USER: ${DB_USERNAME}
    volumes:
      - ./docker/mysql:/var/lib/mysql
    networks:
      - laravel

networks:
  laravel:
    driver: bridge
```

**3. `docker/nginx/default.conf`**

```nginx
# docker/nginx/default.conf
server {
    listen 80;
    server_name localhost;
    root /var/www/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## Guia de Instalação Passo a Passo

Siga os passos abaixo para configurar e rodar o projeto.

### 1. Clonar o Repositório

Se você ainda não fez isso, clone o repositório do projeto para a sua máquina local.

```bash
git clone <URL_DO_SEU_REPOSITORIO>
cd <NOME_DO_PROJETO>
```

### 2. Configuração do Ambiente

Primeiro, crie seu arquivo de variáveis de ambiente e gere a chave da aplicação.

```bash
# Copia o arquivo de exemplo para criar o .env
cp .env.example .env

# Sobe os contêineres para poder executar os próximos comandos
docker-compose up -d --build

# Gera a APP_KEY do Laravel
docker-compose exec app php artisan key:generate
```

**Importante:** Abra o arquivo `.env` e verifique se as configurações do banco de dados correspondem às definidas no `docker-compose.yml`.

```dotenv
# .env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel # Nome do banco de dados
DB_USERNAME=laraveluser # Nome do usuário
DB_PASSWORD=secret # Senha
```

### 3. Instalar Dependências do Composer

Com os contêineres em execução, instale as dependências do PHP via Composer.

```bash
docker-compose exec app composer install
```

### 4. Executar as Migrações do Banco de Dados

Crie as tabelas no banco de dados executando as migrations do Laravel.

```bash
docker-compose exec app php artisan migrate
```

### 5. Instalar o Filament Shield e Criar o Super Admin

O Filament Shield é usado para gerenciar permissões e papéis.

```bash
# Instala o Filament Shield (siga os prompts no terminal)
docker-compose exec app php artisan shield:install

# Cria o usuário Super Admin (siga os prompts para definir nome, email e senha)
docker-compose exec app php artisan shield:super-admin
```

### 6. Importar Dados de Localizações (CSV)

Este passo assume que você tem:
1.  Um arquivo CSV em `database/csv/locations.csv`.
2.  Uma classe `LocationsTableSeeder` para ler o CSV e inserir os dados no banco.

> **Nota:** Se o seeder não existir, você pode criá-lo com `php artisan make:seeder LocationsTableSeeder` e adicionar a lógica para importar o CSV.

Execute o seeder para popular a tabela `locations`:

```bash
docker-compose exec app php artisan db:seed --class=LocationsTableSeeder
```

---

## Acesso à Aplicação

Após seguir todos os passos, a aplicação estará disponível no seu navegador:

-   **URL da Aplicação:** [http://localhost:8000](http://localhost:8000) (ou a porta que você definiu em `APP_PORT` no `.env`).
-   **Painel Admin (Filament):** [http://localhost:8000/admin](http://localhost:8000/admin)

Use as credenciais do **Super Admin** criadas no passo 5 para fazer login.

## Comandos Úteis do Docker

-   **Parar os contêineres:**
    ```bash
    docker-compose down
    ```

-   **Ver os logs em tempo real:**
    ```bash
    docker-compose logs -f
    ```

-   **Acessar o terminal do contêiner da aplicação:**
    ```bash
    docker-compose exec app bash
    ```

-   **Acessar o terminal do contêiner do banco de dados:**
    ```bash
    docker-compose exec db bash
    