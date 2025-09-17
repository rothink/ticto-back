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
- 📝 **Exemplos de requisições** e respostas funcionais
- 🧪 **Interface interativa** para testar os endpoints
- 📖 **Descrições detalhadas** de cada funcionalidade
- 🔐 **Sistema de autenticação integrado**

### Como usar o Swagger UI:

#### 1. Acesse a documentação
Abra o navegador em: `http://localhost/api/documentation`

#### 2. Configure a autenticação
1. Clique no botão **"Authorize" (🔒)** no canto superior direito
2. Faça login primeiro no endpoint `/api/login`:
   - Email: `admin@ticto.com`
   - Password: `123456`
3. Copie o `token` da resposta
4. No modal de autorização, digite: `Bearer SEU_TOKEN_AQUI`
5. Clique em **"Authorize"**

#### 3. Teste os endpoints
Agora você pode testar todos os endpoints protegidos diretamente no Swagger:
- ✅ **Criar funcionário** (`POST /api/employees`)
- ✅ **Listar funcionários** (`GET /api/employees`)
- ✅ **Registrar ponto** (`POST /api/time-records`)
- ✅ **Visualizar relatórios** (`GET /api/reports`)

#### 4. Exemplo funcional
O Swagger já vem com um exemplo funcional para criar funcionários:
```json
{
  "name": "Rodrigo",
  "email": "rodrigoluz@ticto.com",
  "cpf": "943.399.260-19",
  "cargo": "dev",
  "data_nascimento": "1989-12-17",
  "cep": "20011-010",
  "endereco": "Beco dos Barbeiros",
  "numero": "902",
  "complemento": "apartamento",
  "bairro": "Centro",
  "cidade": "Rio de Janeiro",
  "estado": "RJ",
  "password": "123456",
  "password_confirmation": "123456"
}
```

### Regenerar documentação
Se você fizer alterações no código, regenere a documentação:

```bash
docker compose exec laravel.test php artisan l5-swagger:generate
```

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

5. **Erro "Unauthenticated" no Swagger**:
   - Certifique-se de fazer login primeiro no endpoint `/api/login`
   - Copie o token completo da resposta
   - Use o formato correto: `Bearer SEU_TOKEN_AQUI`
   - Verifique se o botão "Authorize" está configurado

6. **Erro "Acesso negado" ao criar funcionário**:
   - Use as credenciais do admin: `admin@ticto.com` / `123456`
   - Apenas usuários com `role = 'admin'` podem criar funcionários

7. **Swagger não mostra botão "Authorize"**:
   ```bash
   # Regenere a documentação
   docker compose exec laravel.test php artisan l5-swagger:generate
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

### 1. Credenciais de usuário admin

O sistema vem com um usuário admin pré-configurado:
- **Email**: `admin@ticto.com`
- **Senha**: `123456`
- **Role**: `admin` (necessário para criar funcionários)

### 2. Como fazer login

**Endpoint**: `POST /api/login`

```json
{
  "email": "admin@ticto.com",
  "password": "123456"
}
```

**Resposta de sucesso**:
```json
{
  "success": true,
  "message": "Login realizado com sucesso",
  "user": {
    "id": 1,
    "name": "admin",
    "email": "admin@ticto.com",
    "role": "admin"
  },
  "token": "1|abc123def456..."
}
```

### 3. Usar o token

Copie o `token` da resposta e inclua no header das requisições:

```
Authorization: Bearer 1|abc123def456...
```

### 4. Permissões

- **Admin**: Pode criar, editar, listar e excluir funcionários
- **Employer**: Pode registrar ponto e visualizar seus próprios dados

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