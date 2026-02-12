# Instalação do Sistema de Hierarquia Owner/Admin

## Passos de Instalação

### 1. Executar Migration
```bash
php artisan migrate
```

**O que faz:**
- Adiciona campo `role` (owner/admin) na tabela users
- Adiciona campo `can_view_votes` (boolean)
- Adiciona campo `can_view_statistics` (boolean)

### 2. Criar Conta Owner
```bash
php artisan db:seed --class=OwnerSeeder
```

**Credenciais criadas:**
- Email: owner@example.com
- Password: password
- Role: owner
- Todas as permissões: ✅

### 3. Limpar Cache (Recomendado)
```bash
php artisan optimize:clear
```

**Limpa:**
- Cache de configuração
- Cache de rotas
- Cache de views
- Cache de eventos

### 4. Verificar Instalação

**Testar login owner:**
1. Acesse: http://localhost:8000/admin/login
2. Email: owner@example.com
3. Password: password

**Verificar menu:**
- Deve aparecer aba "Gerir Admins"
- Badge "Owner" no perfil
- Todas as abas visíveis

## Atualizar Admins Existentes (Se Houver)

Se já tinha admins criados antes da hierarquia, execute:

```sql
-- Ver admins atuais
SELECT * FROM users;

-- Promover um admin existente a owner (CUIDADO!)
UPDATE users SET role = 'owner', can_view_votes = 1, can_view_statistics = 1 WHERE email = 'seu@email.com';

-- Configurar permissões de um admin
UPDATE users SET can_view_votes = 1, can_view_statistics = 1 WHERE email = 'admin@example.com';
```

## Teste Completo

### Como Owner:

1. ✅ Login com owner@example.com
2. ✅ Ver aba "Gerir Admins"
3. ✅ Criar novo admin com permissões
4. ✅ Editar permissões de admin
5. ✅ Fazer upload de imagem
6. ✅ Ver votos
7. ✅ Ver estatísticas

### Como Admin (sem permissões):

1. ✅ Login com admin criado
2. ❌ NÃO vê "Gerir Admins"
3. ❌ NÃO vê "Estatísticas"
4. ✅ Pode fazer upload
5. ✅ Pode gerir suas imagens
6. ❌ Botão "Ver Votos" oculto/desabilitado

### Como Admin (com permissões):

1. ✅ Login com admin configurado
2. ✅ Vê "Estatísticas" (se permitido)
3. ✅ Pode ver votos (se permitido)
4. ✅ Upload funciona
5. ❌ NÃO vê "Gerir Admins"

## Rollback (Desfazer)

Se precisar reverter a hierarquia:

```bash
php artisan migrate:rollback --step=1
```

**ATENÇÃO:** Isso remove os campos role e permissões. Backup antes!

## Comandos Úteis

```bash
# Ver todas as migrations
php artisan migrate:status

# Ver todos os seeders
php artisan db:seed --list

# Recriar tudo do zero (CUIDADO: apaga dados!)
php artisan migrate:fresh --seed

# Apenas recriar owner
php artisan db:seed --class=OwnerSeeder --force
```

## Problemas Comuns

### "Column 'role' doesn't exist"
- Execute: `php artisan migrate`

### "Class OwnerSeeder not found"
- Execute: `composer dump-autoload`
- Execute: `php artisan db:seed --class=OwnerSeeder`

### "Middleware owner não funciona"
- Verifique bootstrap/app.php
- Deve ter: `'owner' => \App\Http\Middleware\OwnerOnly::class`

### "403 Acesso negado"
- Verifique no banco: `SELECT role FROM users WHERE email = 'seu@email.com'`
- Deve ser 'owner' para aceder rotas de gestão

## Estrutura de Ficheiros Criados

```
database/
├── migrations/
│   └── 2026_02_12_000001_add_role_and_permissions_to_users_table.php
└── seeders/
    └── OwnerSeeder.php

app/
├── Http/
│   ├── Middleware/
│   │   └── OwnerOnly.php
│   └── Controllers/
│       └── UserManagementController.php
└── Models/
    └── User.php (atualizado)

resources/views/admin/
└── users/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php

bootstrap/
└── app.php (atualizado)

routes/
└── web.php (atualizado)
```

## Próximos Passos

Após instalação:

1. ✅ Fazer backup da base de dados
2. ✅ Login como owner
3. ✅ Criar primeiro admin de produção
4. ✅ Testar permissões
5. ✅ Configurar credenciais seguras (trocar passwords!)
6. ✅ Documentar admins criados

## Segurança em Produção

⚠️ **IMPORTANTE:**

```bash
# Trocar password do owner
UPDATE users SET password = '$2y$...' WHERE role = 'owner';

# Ou via tinker:
php artisan tinker
>>> $user = User::where('role', 'owner')->first();
>>> $user->password = Hash::make('nova-senha-forte');
>>> $user->save();
```

🔒 Use senhas fortes com pelo menos:
- 12+ caracteres
- Letras maiúsculas e minúsculas
- Números
- Símbolos especiais

## Suporte

Para mais informações, consulte: `HIERARQUIA.md`
