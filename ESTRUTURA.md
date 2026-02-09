# Estrutura Completa do Projeto

## Sistema de Votação de Imagens - Laravel

Este documento descreve toda a estrutura do projeto criado.

---

## 📁 Estrutura de Diretórios

```
App Storage Test/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php       # Login/logout admin
│   │   │   ├── ImageController.php       # Upload e estatísticas
│   │   │   └── VoteController.php        # Sistema de votação
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php          # Redireciona não autenticados
│   │   │   ├── EncryptCookies.php
│   │   │   ├── PreventRequestsDuringMaintenance.php
│   │   │   ├── RedirectIfAuthenticated.php
│   │   │   ├── TrimStrings.php
│   │   │   ├── TrustProxies.php
│   │   │   ├── ValidateSignature.php
│   │   │   └── VerifyCsrfToken.php
│   │   └── Kernel.php
│   ├── Models/
│   │   ├── User.php                      # Model de utilizadores (admin)
│   │   ├── Image.php                     # Model de imagens
│   │   └── Vote.php                      # Model de votos
│   └── Providers/
│       └── AppServiceProvider.php
│
├── bootstrap/
│   ├── app.php                           # Bootstrap da aplicação
│   └── cache/
│       └── .gitignore
│
├── config/
│   ├── auth.php                          # Configuração de autenticação
│   └── database.php                      # Configuração da base de dados
│
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_02_09_000001_create_images_table.php
│   │   └── 2026_02_09_000002_create_votes_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── AdminSeeder.php               # Cria admin padrão
│
├── public/
│   ├── uploads/                          # Pasta para imagens carregadas
│   │   └── .gitkeep
│   ├── .htaccess
│   ├── index.php                         # Entry point da aplicação
│   └── robots.txt
│
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php             # Layout base
│       ├── admin/
│       │   ├── login.blade.php           # Página de login admin
│       │   ├── dashboard.blade.php       # Upload de imagens
│       │   └── statistics.blade.php      # Estatísticas de votos
│       └── gallery.blade.php             # Galeria pública (votação)
│
├── routes/
│   ├── console.php
│   └── web.php                           # Rotas da aplicação
│
├── storage/
│   ├── app/
│   │   └── public/
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
│
├── tests/
│   ├── Feature/
│   ├── Unit/
│   └── TestCase.php
│
├── .env.example                          # Exemplo de configuração
├── .gitignore
├── artisan                               # CLI do Laravel
├── composer.json                         # Dependências PHP
├── package.json                          # Dependências NPM
├── vite.config.js                        # Configuração Vite
├── phpunit.xml                           # Configuração de testes
│
├── README.md                             # Documentação principal
├── INSTALACAO.txt                        # Guia de instalação
├── COMANDOS.md                           # Comandos úteis
└── install.ps1                           # Script de instalação automática
```

---

## 🗄️ Base de Dados

### Tabelas Principais

#### `users` - Administradores
```sql
- id (PK)
- name
- email (único)
- password
- created_at
- updated_at
```

#### `images` - Imagens Carregadas
```sql
- id (PK)
- filename
- path
- user_id (FK → users.id)
- created_at
- updated_at
```

#### `votes` - Votos dos Utilizadores
```sql
- id (PK)
- email (único) ← APENAS 1 VOTO POR EMAIL
- image_id (FK → images.id)
- created_at
- updated_at
```

### Relacionamentos
- `User` hasMany `Image` (1:N)
- `Image` belongsTo `User` (N:1)
- `Image` hasMany `Vote` (1:N)
- `Vote` belongsTo `Image` (N:1)

---

## 🛣️ Rotas da Aplicação

### Rotas Públicas

| Método | URL     | Controller@Método      | Descrição                |
|--------|---------|------------------------|--------------------------|
| GET    | /       | VoteController@index   | Galeria de imagens       |
| POST   | /vote   | VoteController@vote    | Submeter voto            |

### Rotas de Autenticação

| Método | URL            | Controller@Método            | Descrição        |
|--------|----------------|------------------------------|------------------|
| GET    | /admin/login   | AdminController@showLoginForm| Formulário login |
| POST   | /admin/login   | AdminController@login        | Processar login  |

### Rotas Admin (Autenticadas)

| Método | URL                 | Controller@Método           | Descrição              |
|--------|---------------------|-----------------------------|------------------------|
| POST   | /admin/logout       | AdminController@logout      | Logout                 |
| GET    | /admin/dashboard    | AdminController@dashboard   | Painel de upload       |
| POST   | /admin/upload       | ImageController@upload      | Processar upload       |
| GET    | /admin/statistics   | ImageController@statistics  | Ver estatísticas       |

---

## 🎨 Views (Blade Templates)

### `layouts/app.blade.php`
- Layout base com Bootstrap 5
- Navbar responsivo
- Sistema de flash messages
- Links para todas as páginas

### `gallery.blade.php`
- Galeria de imagens em grid
- Cards clicáveis para votar
- Modal de votação com formulário
- Contador de votos por imagem

### `admin/login.blade.php`
- Formulário de login estilizado
- Validação de erros
- Opção "lembrar-me"

### `admin/dashboard.blade.php`
- Formulário de upload
- Preview de imagem antes do upload
- Informações sobre o sistema

### `admin/statistics.blade.php`
- Resumo geral (total imagens, votos, média)
- Tabela detalhada com todas as imagens
- Preview das imagens
- Percentagens e barras de progresso
- Destaque da imagem mais votada

---

## 🔐 Segurança Implementada

✅ **Autenticação**
- Middleware `auth` protege rotas admin
- Sessões encriptadas
- CSRF protection em todos os formulários

✅ **Validação**
- Validação de email no backend
- Validação de tipos de ficheiro (imagens)
- Limite de tamanho de ficheiro (10MB)

✅ **Base de Dados**
- Foreign keys para integridade referencial
- Email único na tabela votes
- Passwords hasheadas com bcrypt

✅ **Upload de Ficheiros**
- Validação de tipos MIME
- Nomes de ficheiro únicos (timestamp + random)
- Armazenamento local seguro

---

## 📦 Dependências

### Backend (Composer)
```json
{
  "php": "^8.2",
  "laravel/framework": "^11.0"
}
```

### Frontend (NPM)
```json
{
  "axios": "^1.7.4",
  "laravel-vite-plugin": "^1.0",
  "vite": "^5.0"
}
```

### Assets Externos (CDN)
- Bootstrap 5.3.0
- Font Awesome 6.4.0

---

## 🚀 Fluxo de Funcionamento

### Fluxo do Visitante (Guest)
1. Acessa `/` (galeria)
2. Vê todas as imagens carregadas
3. Clica numa imagem para votar
4. Insere email no modal
5. Sistema valida:
   - Email não votou antes?
   - Email válido?
6. Regista voto ou mostra erro
7. Redireciona com mensagem de sucesso/erro

### Fluxo do Admin
1. Acessa `/admin/login`
2. Faz login com credenciais
3. Redireciona para `/admin/dashboard`
4. Faz upload de imagem:
   - Seleciona ficheiro
   - Preview da imagem
   - Submete formulário
5. Imagem guardada em `public/uploads/`
6. Registo criado na BD
7. Pode ver estatísticas em `/admin/statistics`

---

## 📊 Funcionalidades

### ✅ Implementadas

**Admin:**
- ✅ Sistema de login/logout
- ✅ Upload de imagens (múltiplos formatos)
- ✅ Visualização de estatísticas completas
- ✅ Total de votos por imagem
- ✅ Percentagens de votos
- ✅ Identificação da imagem mais votada
- ✅ Preview antes do upload

**Visitantes:**
- ✅ Galeria de imagens responsiva
- ✅ Sistema de votação
- ✅ Validação de email único
- ✅ Contador de votos por imagem
- ✅ Modal de confirmação de voto
- ✅ Feedback visual (mensagens)

**Segurança:**
- ✅ Autenticação de admins
- ✅ Proteção CSRF
- ✅ Validação de formulários
- ✅ Email único garantido
- ✅ Foreign keys na BD

---

## 🔧 Configuração

### Variáveis de Ambiente (.env)

```env
APP_NAME="Sistema de Votação"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_voting
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
```

---

## 📝 Notas Importantes

1. **Pasta de Uploads**: As imagens são guardadas em `public/uploads/`
2. **Email Único**: Cada email só pode votar UMA VEZ (constraint na BD)
3. **Admin Padrão**: Email `admin@example.com`, Password `password`
4. **Bootstrap 5**: Usado via CDN para interface responsiva
5. **Sem API**: Aplicação MVC tradicional com Blade templates
6. **MySQL**: Base de dados obrigatória (configurar no .env)

---

## 🐛 Resolução de Problemas Comuns

### Erro: "Class not found"
```bash
composer dump-autoload
```

### Erro: Permissões (Windows)
```bash
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T
```

### Erro: Base de dados
1. Verificar se MySQL está a correr
2. Confirmar credenciais no .env
3. Criar base de dados manualmente

### Erro: Upload não funciona
1. Verificar se `public/uploads/` existe
2. Dar permissões de escrita

---

## 📚 Documentação de Referência

- [Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)
- [Blade Templates](https://laravel.com/docs/11.x/blade)
- [Eloquent ORM](https://laravel.com/docs/11.x/eloquent)

---

## 📄 Licença

MIT License - Software livre para uso pessoal e comercial.

---

**Data de Criação:** Fevereiro 2026  
**Versão:** 1.0.0  
**Laravel:** 11.x  
**PHP:** 8.2+
