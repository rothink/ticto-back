# Ticto Backend - API Laravel

Backend do sistema de ponto eletrônico desenvolvido com Laravel 11.

## 📋 Pré-requisitos

- Docker
- Docker Compose

## 🚀 Como executar o projeto

### 1º Passo - Clone do projeto

```bash
git clone https://github.com/rothink/ticto-back
cd ticto-back
```

### 2º Passo - Configuração do ambiente

Crie o arquivo `.env` baseado no `.env.example` (se existir) ou configure as variáveis de ambiente necessárias:

```bash
# Exemplo de configuração básica do .env
APP_NAME=Ticto
APP_ENV=local
APP_KEY=base64:SUA_CHAVE_AQUI
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

# Configurações do Laravel Sail
WWWGROUP=1000
WWWUSER=1000
APP_PORT=80
VITE_PORT=5173
```

### 3º Passo - Subir os containers

Execute o comando para subir todos os containers:

```bash
docker compose up --build -d
```

Este comando irá subir:
- **Laravel** (aplicação principal)
- **MySQL** (banco de dados)
- **Redis** (cache e sessões)
- **Meilisearch** (busca)
- **Mailpit** (interceptação de emails)
- **Selenium** (testes automatizados)

### 4º Passo - Configuração inicial

Após os containers estarem rodando, execute as migrações e seeders:

```bash
# Acessar o container do Laravel
docker compose exec laravel.test bash

# Dentro do container, executar:
php artisan migrate
php artisan db:seed
```

### 5º Passo - Acessar a aplicação

- **API**: http://localhost
- **Documentação Swagger**: http://localhost/api/documentation

## 📚 Documentação da API

A documentação completa da API está disponível através do **Swagger UI** em:
**http://localhost:80/api/documentation**

### O que você encontrará no Swagger:

- 📋 **Lista completa** de todos os endpoints disponíveis
- 🔧 **Parâmetros necessários** para cada endpoint
- 📝 **Exemplos de requisições** e respostas
- 🧪 **Interface interativa** para testar os endpoints
- 📖 **Descrições detalhadas** de cada funcionalidade

## 🛠️ Comandos úteis

### Gerenciamento de containers

```bash
# Parar os containers
docker compose down

# Ver logs dos containers
docker compose logs -f

# Ver logs específicos do Laravel
docker compose logs -f laravel.test

# Reiniciar containers
docker compose restart
```

### Comandos Laravel

```bash
# Acessar o container do Laravel
docker compose exec laravel.test bash

# Executar migrações
docker compose exec laravel.test php artisan migrate

# Executar seeders
docker compose exec laravel.test php artisan db:seed

# Limpar cache
docker compose exec laravel.test php artisan cache:clear

# Limpar configuração
docker compose exec laravel.test php artisan config:clear

# Executar testes
docker compose exec laravel.test php artisan test

# Gerar chave da aplicação
docker compose exec laravel.test php artisan key:generate
```

## 🗄️ Serviços disponíveis

| Serviço | URL | Porta | Descrição |
|---------|-----|-------|-----------|
| **Laravel API** | http://localhost | 80 | API principal |
| **MySQL** | localhost | 3306 | Banco de dados |
| **Redis** | localhost | 6379 | Cache e sessões |
| **Meilisearch** | http://localhost:7700 | 7700 | Motor de busca |
| **Mailpit** | http://localhost:8025 | 8025 | Interceptação de emails |

## 🏗️ Estrutura do projeto

```
api/
├── app/
│   ├── Http/Controllers/    # Controllers da API
│   ├── Models/             # Modelos Eloquent
│   ├── Services/           # Serviços de negócio
│   └── Repositories/       # Repositórios
├── database/
│   ├── migrations/         # Migrações do banco
│   └── seeders/           # Seeders para dados iniciais
├── routes/
│   └── api.php            # Rotas da API
├── config/                # Configurações
└── docker-compose.yml     # Configuração Docker
```

## 🔧 Tecnologias utilizadas

- **Laravel 11** - Framework PHP
- **MySQL 8.0** - Banco de dados
- **Redis** - Cache e sessões
- **Laravel Sanctum** - Autenticação API
- **Swagger/OpenAPI** - Documentação da API
- **Laravel Sail** - Ambiente Docker

## 🐛 Solução de problemas

### Problemas comuns:

1. **Porta 80 já em uso**:
   ```bash
   # Verificar o que está usando a porta
   sudo lsof -i :80
   # Ou alterar a porta no .env: APP_PORT=8080
   ```

2. **Erro de permissão**:
   ```bash
   sudo chown -R $USER:$USER .
   ```

3. **Container não sobe**:
   ```bash
   docker compose down
   docker compose up --build -d
   ```

4. **Erro de banco de dados**:
   ```bash
   # Verificar se o MySQL está rodando
   docker compose logs mysql
   ```

### Logs úteis:

```bash
# Logs do Laravel
docker compose logs -f laravel.test

# Logs do MySQL
docker compose logs -f mysql

# Logs do Redis
docker compose logs -f redis
```

## 📝 Notas importantes

- O banco de dados SQLite está configurado por padrão, mas o Docker Compose usa MySQL
- As migrações são executadas automaticamente quando o container sobe
- A documentação do Swagger é gerada automaticamente baseada nas anotações do código
- O frontend está configurado para se comunicar com esta API na porta 80

## 🔐 Autenticação

A API utiliza **Laravel Sanctum** para autenticação. Os endpoints protegidos requerem:

1. **Login** para obter o token
2. **Header Authorization**: `Bearer {token}`

## 🧪 Testes

```bash
# Executar todos os testes
docker compose exec laravel.test php artisan test

# Executar testes específicos
docker compose exec laravel.test php artisan test --filter=NomeDoTeste
```

## 📊 Monitoramento

- **Logs da aplicação**: `storage/logs/laravel.log`
- **Logs do Docker**: `docker compose logs -f`
- **Status dos containers**: `docker compose ps`