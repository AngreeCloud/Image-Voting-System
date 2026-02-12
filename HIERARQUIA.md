# Sistema de Hierarquia Owner/Admin

Este sistema implementa um controlo de acesso hierárquico com dois níveis de utilizadores.

## Níveis de Acesso

### 1. Owner (Proprietário)
- **Acesso total** ao sistema
- Pode criar e gerir contas de admin
- Pode configurar permissões de cada admin
- Pode remover admins (sem imagens)
- Tem aba exclusiva "Gerir Admins" no menu
- Badge "Owner" visível no perfil

**Credenciais padrão:**
- Email: `owner@example.com`
- Password: `password`

### 2. Admin (Administrador)
- Pode fazer upload de imagens (sempre permitido)
- Pode gerir suas próprias imagens
- Permissões configuráveis pelo owner:
  - **Ver emails dos votos**: Acesso à lista de votantes
  - **Ver estatísticas**: Acesso à página de estatísticas gerais

## Estrutura da Base de Dados

### Tabela: users

Campos adicionados pela migration `2026_02_12_000001_add_role_and_permissions_to_users_table.php`:

```sql
role ENUM('owner', 'admin') DEFAULT 'admin'
can_view_votes BOOLEAN DEFAULT FALSE
can_view_statistics BOOLEAN DEFAULT FALSE
```

## Permissões Detalhadas

| Funcionalidade | Owner | Admin | Admin c/ Permissão |
|---------------|-------|-------|-------------------|
| Upload de imagens | ✅ | ✅ | ✅ |
| Gerir próprias imagens | ✅ | ✅ | ✅ |
| Remover próprias imagens | ✅ | ✅ | ✅ |
| Ver emails dos votos | ✅ | ❌ | ✅ (se configurado) |
| Ver estatísticas gerais | ✅ | ❌ | ✅ (se configurado) |
| Criar admins | ✅ | ❌ | ❌ |
| Editar permissões de admins | ✅ | ❌ | ❌ |
| Remover admins | ✅ | ❌ | ❌ |

## Instalação do Sistema de Hierarquia

### 1. Executar Migration

```bash
php artisan migrate
```

Isso adiciona os campos `role`, `can_view_votes` e `can_view_statistics` à tabela `users`.

### 2. Criar Conta Owner

```bash
php artisan db:seed --class=OwnerSeeder
```

Cria a conta owner padrão.

### 3. Login como Owner

Acesse `/admin/login` e use:
- Email: `owner@example.com`
- Password: `password`

### 4. Criar Admins

1. Navegue para "Gerir Admins" no menu
2. Clique em "Criar Admin"
3. Preencha os dados:
   - Nome
   - Email
   - Password
4. Configure as permissões com checkboxes:
   - ☑️ Ver Emails dos Votos
   - ☑️ Ver Estatísticas Gerais

## Gestão de Admins (Owner)

### Criar Novo Admin

**Rota:** `/admin/users/create`

- Define nome, email e password
- Configura permissões iniciais via checkboxes
- Upload é sempre permitido automaticamente

### Editar Permissões de Admin

**Rota:** `/admin/users/{id}/edit`

- Alterar nome e email
- Atualizar password (opcional)
- Modificar permissões:
  - Ver emails dos votos
  - Ver estatísticas gerais
- Visualizar informações do admin (imagens carregadas, datas)

### Remover Admin

**Restrições:**
- Não é possível remover owners
- Admin não pode ter imagens carregadas
- Se tiver imagens, é necessário removê-las primeiro

### Lista de Admins

**Rota:** `/admin/users`

Exibe:
- Nome e email
- Status das permissões (✅/❌)
- Número de imagens carregadas
- Data de criação
- Ações: Editar | Remover

## Middleware e Rotas

### Middleware `OwnerOnly`

**Ficheiro:** `app/Http/Middleware/OwnerOnly.php`

Protege rotas exclusivas do owner:
- Verifica se utilizador está autenticado
- Verifica se `role === 'owner'`
- Retorna erro 403 se não for owner

**Uso:**
```php
Route::middleware(['auth', 'owner'])->group(function () {
    // Rotas exclusivas do owner
});
```

### Rotas Protegidas (Owner)

```php
GET    /admin/users              -> Listar admins
GET    /admin/users/create       -> Form criar admin
POST   /admin/users              -> Salvar admin
GET    /admin/users/{id}/edit    -> Form editar admin
PUT    /admin/users/{id}         -> Atualizar admin
DELETE /admin/users/{id}         -> Remover admin
```

## Helper Methods no Model User

```php
// Verificar role
$user->isOwner()    // true se role === 'owner'
$user->isAdmin()    // true se role === 'admin'

// Verificar permissões
$user->canViewVotes()        // Owner sempre true, Admin verifica can_view_votes
$user->canViewStatistics()   // Owner sempre true, Admin verifica can_view_statistics
```

**Uso nas views:**
```blade
@if(Auth::user()->isOwner())
    <!-- Conteúdo exclusivo do owner -->
@endif

@if(Auth::user()->canViewVotes())
    <!-- Link para ver votos -->
@endif
```

## Menu de Navegação

O menu adapta-se automaticamente às permissões:

**Todos (autenticados):**
- Upload
- Gerir Imagens

**Apenas com permissão `can_view_statistics`:**
- Estatísticas

**Apenas Owner:**
- Gerir Admins (aba exclusiva)
- Badge "Owner" no nome

## Proteção em Controllers

### ImageController

**`viewVotes()` método:**
```php
if (!Auth::user()->canViewVotes()) {
    abort(403, 'Você não tem permissão para visualizar os votos.');
}
```

**`statistics()` método:**
```php
if (!Auth::user()->canViewStatistics()) {
    abort(403, 'Você não tem permissão para visualizar as estatísticas.');
}
```

## Fluxo de Trabalho Típico

### Configuração Inicial (Owner)

1. Login como owner
2. Aceder "Gerir Admins"
3. Criar admins com permissões apropriadas
4. Fazer upload de imagens (opcional)

### Uso Diário (Admin)

1. Login com credenciais de admin
2. Fazer upload de imagens
3. Gerir próprias imagens
4. Aceder funcionalidades permitidas:
   - Ver votos (se permitido)
   - Ver estatísticas (se permitido)

### Manutenção (Owner)

1. Monitorizar admins ativos
2. Ajustar permissões conforme necessário
3. Remover admins inativos (sem imagens)
4. Criar novos admins quando necessário

## Segurança

✅ **Middleware duplo** em rotas owner: `['auth', 'owner']`
✅ **Validação no controller** antes de mostrar dados sensíveis
✅ **Verificação no frontend** para esconder botões sem permissão
✅ **Proteção contra edição de owners** (não editáveis por ninguém)
✅ **Foreign keys** garantem integridade ao remover admins

## Limitações e Regras

1. Apenas um owner por sistema (recomendado)
2. Owners não podem ser editados via interface
3. Admins não podem auto-promover-se
4. Upload sempre permitido (não configurável)
5. Só admins sem imagens podem ser removidos
6. Owner tem todas as permissões automáticas

## Mensagens de Erro

| Situação | Mensagem |
|----------|----------|
| Admin tenta aceder gestão de users | 403: "Acesso negado. Apenas owners podem aceder a esta área." |
| Admin tenta ver votos sem permissão | 403: "Você não tem permissão para visualizar os votos." |
| Admin tenta ver stats sem permissão | 403: "Você não tem permissão para visualizar as estatísticas." |
| Tentar editar owner | "Não é possível editar um owner." |
| Tentar remover owner | "Não é possível remover um owner." |
| Remover admin com imagens | "Não é possível remover um admin que tem imagens carregadas." |

## Customização

### Adicionar Novas Permissões

1. Adicionar campo booleano na migration de users
2. Adicionar ao `$fillable` do User model
3. Criar método helper `canDoSomething()` no User model
4. Adicionar checkbox nos formulários create/edit
5. Verificar permissão nos controllers
6. Condicionar exibição nas views

### Exemplo: Adicionar "can_delete_others_images"

```php
// Migration
$table->boolean('can_delete_others_images')->default(false);

// User model
public function canDeleteOthersImages(): bool
{
    return $this->isOwner() || $this->can_delete_others_images;
}

// Controller
if (!Auth::user()->canDeleteOthersImages()) {
    abort(403);
}

// View
@if(Auth::user()->canDeleteOthersImages())
    <button>Remover qualquer imagem</button>
@endif
```

## Troubleshooting

### "Class OwnerOnly not found"
Execute: `php artisan optimize:clear`

### Middleware não funciona
Verifique `bootstrap/app.php` - alias 'owner' registado?

### Permissões não atualizam
Faça logout e login novamente

### Owner não vê aba "Gerir Admins"
Verifique: `SELECT role FROM users WHERE email = 'owner@example.com'`
Deve ser 'owner', não 'admin'
