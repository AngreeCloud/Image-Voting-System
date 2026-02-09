# Sistema de Votação de Imagens - Laravel

Aplicação web Laravel para upload e votação de imagens.

<details>
<summary>📸 <strong>Ver Screenshots</strong> (clique para expandir)</summary>

<br>

<table>
  <tr>
    <td width="50%">
      <img src="https://github.com/user-attachments/assets/c7711526-0ced-4f0c-abcb-b3fccb3f7cdd" alt="Galeria Pública" />
      <p align="center"><em>Upload de Imagens</em></p>
    </td>
    <td width="50%">
      <img src="https://github.com/user-attachments/assets/08a23332-8888-42b8-b5f5-6249eecb1755" alt="Login Admin" />
      <p align="center"><em>Imagens carregadas no painel de Admin</em></p>
    </td>
  </tr>
  <tr>
    <td width="50%">
      <img src="https://github.com/user-attachments/assets/12d8a263-f885-48fa-a126-015467df545b" alt="Dashboard Admin" />
      <p align="center"><em>Votos da Imagem</em></p>
    </td>
    <td width="50%">
      <img src="https://github.com/user-attachments/assets/39ff22cc-d1f8-4ee9-bf40-e79f9ff4afa1" alt="Estatísticas" />
      <p align="center"><em>Estatísticas</em></p>
    </td>
  </tr>
  <tr>
    <td width="50%">
      <img src="https://github.com/user-attachments/assets/c393c6c3-db26-4f31-b247-30ef1713c4a3" alt="Gestão de Imagens" />
      <p align="center"><em>Visualizar imagens</em></p>
    </td>
    <td width="50%">
      <img src="https://github.com/user-attachments/assets/2ac774ed-5240-47ae-9247-db289e7039bf" alt="Visualizar Votos" />
      <p align="center"><em>Confirmação do voto com email</em></p>
    </td>
  </tr>
</table>

</details>


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
