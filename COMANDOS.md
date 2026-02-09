# Comandos Úteis - Sistema de Votação Laravel

## Comandos Artisan Essenciais

### Servidor de Desenvolvimento
```bash
# Iniciar servidor
php artisan serve

# Iniciar servidor em porta específica
php artisan serve --port=8080

# Iniciar servidor acessível na rede
php artisan serve --host=0.0.0.0
```

### Base de Dados

```bash
# Executar migrations
php artisan migrate

# Reverter última migration
php artisan migrate:rollback

# Reverter todas as migrations e executar novamente
php artisan migrate:fresh

# Executar migrations e seeders
php artisan migrate:fresh --seed

# Executar seeder específico
php artisan db:seed --class=AdminSeeder
```

### Cache e Otimização

```bash
# Limpar cache da aplicação
php artisan cache:clear

# Limpar cache de configuração
php artisan config:clear

# Limpar cache de rotas
php artisan route:clear

# Limpar cache de views
php artisan view:clear

# Limpar todos os caches
php artisan optimize:clear

# Otimizar aplicação para produção
php artisan optimize
```

### Informações do Sistema

```bash
# Ver todas as rotas
php artisan route:list

# Ver rotas específicas (admin)
php artisan route:list --path=admin

# Ver informações da aplicação
php artisan about

# Ver versão do Laravel
php artisan --version
```

### Manutenção

```bash
# Ativar modo de manutenção
php artisan down

# Desativar modo de manutenção
php artisan up

# Ativar modo manutenção com segredo (acesso bypass)
php artisan down --secret="token-secreto"
```

## Comandos Composer

```bash
# Instalar dependências
composer install

# Atualizar dependências
composer update

# Adicionar pacote
composer require nome/pacote

# Remover pacote
composer remove nome/pacote

# Atualizar autoload
composer dump-autoload
```

## Comandos NPM (Opcional)

```bash
# Instalar dependências
npm install

# Compilar assets para desenvolvimento
npm run dev

# Compilar assets para produção
npm run build

# Watch mode (recompila automaticamente)
npm run dev -- --watch
```

## Comandos Git

```bash
# Inicializar repositório
git init

# Adicionar todos os ficheiros
git add .

# Commit
git commit -m "Primeira versão"

# Ver status
git status

# Ver diferenças
git diff
```

## Comandos MySQL

```bash
# Entrar no MySQL
mysql -u root -p

# Criar base de dados
CREATE DATABASE laravel_voting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Listar bases de dados
SHOW DATABASES;

# Usar base de dados
USE laravel_voting;

# Ver tabelas
SHOW TABLES;

# Ver estrutura de tabela
DESCRIBE votes;

# Ver dados de tabela
SELECT * FROM images;
SELECT * FROM votes;
SELECT * FROM users;
```

## Comandos Úteis Windows PowerShell

```bash
# Ver conteúdo de ficheiro
Get-Content .env

# Editar ficheiro
notepad .env

# Criar diretório
New-Item -ItemType Directory -Path "pasta"

# Copiar ficheiro
Copy-Item "origem" "destino"

# Ver versão PHP
php -v

# Ver módulos PHP
php -m

# Ver informações PHP
php -i

# Testar sintaxe PHP
php -l ficheiro.php
```

## Testes e Debug

```bash
# Executar testes
php artisan test

# Ver logs em tempo real
Get-Content storage\logs\laravel.log -Wait -Tail 50

# Verificar sintaxe de todos os ficheiros PHP
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

## Comandos Personalizados para Este Projeto

```bash
# Criar novo admin
php artisan tinker
>>> App\Models\User::create(['name' => 'Nome', 'email' => 'email@test.com', 'password' => bcrypt('password')]);

# Ver estatísticas no terminal
php artisan tinker
>>> App\Models\Image::withCount('votes')->get();
>>> App\Models\Vote::count();

# Resetar votos (cuidado!)
php artisan tinker
>>> App\Models\Vote::truncate();

# Resetar imagens (cuidado!)
php artisan tinker
>>> App\Models\Image::truncate();

# Ver utilizadores
php artisan tinker
>>> App\Models\User::all();
```

## Backup

```bash
# Backup da base de dados
mysqldump -u root -p laravel_voting > backup.sql

# Restaurar backup
mysql -u root -p laravel_voting < backup.sql

# Backup de ficheiros
Copy-Item -Recurse "public\uploads" "backup\uploads_$(Get-Date -Format 'yyyy-MM-dd')"
```

## Produção

```bash
# Otimizar para produção
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Atualizar .env para produção
APP_ENV=production
APP_DEBUG=false

# Permissões (Linux)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Resolução de Problemas

```bash
# Limpar tudo e recomeçar
php artisan optimize:clear
composer dump-autoload
php artisan migrate:fresh --seed

# Verificar configuração
php artisan config:show database

# Testar conexão à base de dados
php artisan tinker
>>> DB::connection()->getPdo();

# Ver erros detalhados
# Edite .env: APP_DEBUG=true
# Depois acesse a página que dá erro
```

---

**Dica:** Crie aliases no PowerShell para comandos frequentes:

```powershell
# Adicione ao seu perfil PowerShell
Set-Alias pa "php artisan"

# Depois pode usar:
pa serve
pa migrate
pa cache:clear
```
