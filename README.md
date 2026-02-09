# Sistema de Votação de Imagens - Laravel

Aplicação web Laravel para upload e votação de imagens.

## Características

- **Admin**: Login, upload de imagens e visualização de estatísticas
- **Guest**: Visualização de imagens e votação (um voto por email)
- Sistema de votação com validação de email único
- Armazenamento local de imagens

## Requisitos

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js e NPM (para assets)

## Instalação

### 1. Instalar dependências

```bash
composer install
npm install
npm run build
```

### 2. Configurar base de dados

Copie o ficheiro `.env.example` para `.env`:

```bash
copy .env.example .env
```

Configure as credenciais do MySQL no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_voting
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Gerar chave da aplicação

```bash
php artisan key:generate
```

### 4. Criar base de dados

Crie manualmente a base de dados no MySQL:

```sql
CREATE DATABASE laravel_voting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Executar migrations e seeder

```bash
php artisan migrate
php artisan db:seed --class=AdminSeeder
```

**Credenciais do admin criadas:**
- Email: `admin@example.com`
- Password: `password`

### 6. Criar diretório de uploads

```bash
mkdir public\uploads
```

### 7. Iniciar servidor

```bash
php artisan serve
```

Acesse: `http://localhost:8000`

## Estrutura

### Rotas

**Públicas:**
- `GET /` - Galeria de imagens (votação)
- `POST /vote` - Submeter voto

**Admin (requer autenticação):**
- `GET /admin/login` - Página de login
- `POST /admin/login` - Processar login
- `POST /admin/logout` - Logout
- `GET /admin/dashboard` - Painel admin
- `POST /admin/upload` - Upload de imagem
- `GET /admin/statistics` - Estatísticas de votos

### Base de Dados

**users** - Utilizadores admin
- id, name, email, password

**images** - Imagens carregadas
- id, filename, path, user_id, created_at

**votes** - Votos (email único)
- id, email, image_id, created_at

## Uso

### Como Admin

1. Acesse `/admin/login`
2. Faça login com as credenciais
3. Faça upload de imagens
4. Veja estatísticas em `/admin/statistics`

### Como Visitante

1. Acesse `/`
2. Clique numa imagem para votar
3. Insira seu email
4. Cada email só pode votar uma vez

## Segurança

- Middleware `auth` protege rotas admin
- Validação de email no backend
- Foreign keys garantem integridade referencial
- Email único na tabela votes

## Tecnologias

- Laravel 11
- MySQL
- Bootstrap 5 (opcional)
- Blade templates
