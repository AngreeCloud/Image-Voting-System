# Guia de Deploy em Produção

Este documento explica como fazer deploy da aplicação em um servidor de produção.

---

## 🚀 Deploy Rápido no Render (Recomendado)

**A forma mais fácil de fazer deploy - totalmente automatizado com Docker!**

### Vantagens:
- ✅ Deploy em menos de 10 minutos
- ✅ PostgreSQL gratuita incluída
- ✅ 1GB storage para uploads
- ✅ SSL/HTTPS automático
- ✅ Auto-deploy do GitHub
- ✅ Zero configuração de servidor

### Guias:
- **[DEPLOY_QUICK.md](DEPLOY_QUICK.md)** - Deploy em 3 passos (5 minutos)
- **[DEPLOY_RENDER.md](DEPLOY_RENDER.md)** - Documentação completa

### Passos Resumidos:
```bash
# 1. Push para GitHub
git push origin main

# 2. Criar Blueprint no Render
# Acesse render.com → New + → Blueprint → Conectar repositório

# 3. Aguardar deploy (~5-10 min)
```

**Pronto!** Aplicação online em `https://seu-app.onrender.com`

---

## ⚠️ Checklist Pré-Deploy

Antes de fazer deploy, certifique-se de:

- [ ] Aplicação testada completamente em local
- [ ] Base de dados configurada
- [ ] Todas as funcionalidades funcionando
- [ ] Imagens de teste removidas
- [ ] Credenciais de admin alteradas
- [ ] Backups configurados

---

## 🖥️ Opções de Hosting

### 1. Render (Recomendado) 🌟

**Deploy automatizado com Docker**

**Plano Free Inclui:**
- Web Service (grátis)
- PostgreSQL Database (1GB)
- Persistent Disk (1GB)
- SSL automático
- Auto-deploy GitHub

**Prós:**
- ✅ Setup mais fácil (3 passos)
- ✅ Infraestrutura como código (render.yaml)
- ✅ Zero manutenção de servidor
- ✅ Escalável (upgrade fácil)

**Contras:**
- ⚠️ Suspende após 15 min inativo (free plan)
- ⚠️ Cold start ~30-60s

**Documentação:** [DEPLOY_RENDER.md](DEPLOY_RENDER.md)

---

### 2. Shared Hosting (cPanel)

**Requisitos:**
- PHP 8.2+
- MySQL 5.7+
- Composer
- SSH access (recomendado)

**Provedores Recomendados:**
- Hostinger
- A2 Hosting
- SiteGround
- Bluehost

### 3. VPS (Virtual Private Server)

**Provedores:**
- DigitalOcean
- Linode
- Vultr
- AWS Lightsail

### 4. Cloud Platforms

**Plataformas Laravel-Friendly:**
- Laravel Forge + DigitalOcean
- Heroku (com buildpack PHP)
- AWS Elastic Beanstalk
- Google Cloud Platform

---

## 📦 Deploy em Shared Hosting (cPanel)

### Passo 1: Preparar Aplicação

```bash
# 1. Otimizar aplicação
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Criar arquivo zip
# Excluir: node_modules, .git, .env, storage/logs/*
```

### Passo 2: Upload via FTP

1. Conecte-se via FTP
2. Faça upload do ZIP para `public_html`
3. Extraia o arquivo

### Passo 3: Configurar .env

Crie `.env` com configurações de produção:

```env
APP_NAME="Sistema de Votação"
APP_ENV=production
APP_KEY=base64:... # gerar novo com php artisan key:generate
APP_DEBUG=false
APP_URL=https://seudominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=seu_database
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

SESSION_DRIVER=database
CACHE_DRIVER=database
```

### Passo 4: Configurar Permissões

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/uploads
```

### Passo 5: Mover Arquivos

**Estrutura correta em cPanel:**

```
public_html/
├── laravel/           # Toda a aplicação (exceto public)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   └── ...
│
└── (conteúdo de public/)  # Mover conteúdo de /public para raiz
    ├── .htaccess
    ├── index.php
    ├── uploads/
    └── ...
```

### Passo 6: Atualizar index.php

Edite `public_html/index.php`:

```php
require __DIR__.'/laravel/vendor/autoload.php';
$app = require_once __DIR__.'/laravel/bootstrap/app.php';
```

### Passo 7: Executar Migrations

```bash
cd laravel
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
```

---

## 🔧 Deploy em VPS (Ubuntu)

### Passo 1: Preparar Servidor

```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar LEMP Stack
sudo apt install nginx mysql-server php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip unzip git -y

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Passo 2: Configurar MySQL

```bash
sudo mysql_secure_installation

# Criar base de dados
sudo mysql -u root -p
```

```sql
CREATE DATABASE laravel_voting;
CREATE USER 'laravel'@'localhost' IDENTIFIED BY 'senha_forte';
GRANT ALL PRIVILEGES ON laravel_voting.* TO 'laravel'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Passo 3: Clonar Repositório

```bash
cd /var/www
sudo git clone seu-repositorio.git laravel_voting
cd laravel_voting

# Permissões
sudo chown -R www-data:www-data /var/www/laravel_voting
sudo chmod -R 755 /var/www/laravel_voting/storage
sudo chmod -R 755 /var/www/laravel_voting/bootstrap/cache
```

### Passo 4: Instalar Dependências

```bash
composer install --optimize-autoloader --no-dev
```

### Passo 5: Configurar .env

```bash
cp .env.example .env
nano .env
```

Edite com configurações de produção.

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Passo 6: Configurar Nginx

```bash
sudo nano /etc/nginx/sites-available/laravel_voting
```

```nginx
server {
    listen 80;
    server_name seudominio.com;
    root /var/www/laravel_voting/public;

    add_header X-Frame-Options "SAMEORIGIN";
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
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Ativar site
sudo ln -s /etc/nginx/sites-available/laravel_voting /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Passo 7: Configurar SSL (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d seudominio.com
```

---

## 🔐 Segurança em Produção

### 1. Configurações do .env

```env
APP_ENV=production
APP_DEBUG=false  # IMPORTANTE!
APP_KEY=... # Único e secreto
```

### 2. Proteger Arquivos Sensíveis

```bash
# .htaccess no diretório raiz
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

### 3. Firewall (UFW)

```bash
sudo ufw allow 22    # SSH
sudo ufw allow 80    # HTTP
sudo ufw allow 443   # HTTPS
sudo ufw enable
```

### 4. Atualizar Dependências Regularmente

```bash
composer update
php artisan optimize:clear
php artisan optimize
```

### 5. Configurar Backups Automáticos

**Instalar pacote:**
```bash
composer require spatie/laravel-backup
```

**Configurar cron:**
```bash
crontab -e
```

```cron
0 2 * * * cd /var/www/laravel_voting && php artisan backup:run >> /dev/null 2>&1
```

---

## 📊 Monitoramento

### 1. Logs de Erros

```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Ver últimas 100 linhas
tail -n 100 storage/logs/laravel.log
```

### 2. Monitoramento de Performance

**Instalar Laravel Telescope (dev):**
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### 3. Uptime Monitoring

**Serviços gratuitos:**
- UptimeRobot
- Pingdom
- StatusCake

---

## 🔄 Processo de Atualização

### Deploy de Novas Versões

```bash
cd /var/www/laravel_voting

# 1. Ativar modo manutenção
php artisan down

# 2. Fazer backup
php artisan backup:run

# 3. Atualizar código
git pull origin main

# 4. Atualizar dependências
composer install --optimize-autoloader --no-dev

# 5. Executar migrations
php artisan migrate --force

# 6. Limpar cache
php artisan optimize:clear
php artisan optimize

# 7. Desativar modo manutenção
php artisan up
```

---

## ⚡ Otimizações de Performance

### 1. OPcache (PHP)

```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### 2. Redis (Cache)

```bash
sudo apt install redis-server php8.2-redis
```

**.env:**
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### 3. Compressão (Gzip)

**Nginx:**
```nginx
gzip on;
gzip_vary on;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml image/svg+xml;
```

### 4. CDN para Assets

Use CloudFlare ou outro CDN para servir:
- Bootstrap
- Font Awesome
- Imagens estáticas

---

## 📈 Escalabilidade

### Quando Crescer

**Opções:**

1. **Load Balancer**: Múltiplos servidores
2. **Separar BD**: Servidor dedicado MySQL
3. **File Storage**: Amazon S3 para imagens
4. **Queue System**: Redis/RabbitMQ para jobs
5. **CDN**: CloudFlare/CloudFront

---

## 🆘 Troubleshooting

### Erro 500

```bash
# Verificar logs
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

### Permissões

```bash
sudo chown -R www-data:www-data /var/www/laravel_voting
sudo chmod -R 755 storage bootstrap/cache
```

### Limpar todos os caches

```bash
php artisan optimize:clear
composer dump-autoload
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## ✅ Checklist Pós-Deploy

- [ ] Site acessível via HTTPS
- [ ] Login admin funciona
- [ ] Upload de imagens funciona
- [ ] Sistema de votação funciona
- [ ] Estatísticas aparecem
- [ ] Emails funcionam (se configurado)
- [ ] Backups configurados
- [ ] Monitoramento ativo
- [ ] SSL válido
- [ ] Performance adequada

---

## 📞 Suporte

Em caso de problemas:
1. Verifique logs
2. Consulte documentação Laravel
3. Verifique permissões
4. Teste em local

---

**Deploy bem-sucedido! 🚀**

Sua aplicação está pronta para produção!
