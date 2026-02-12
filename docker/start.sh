#!/bin/sh
set -e

echo "🚀 Iniciando aplicação Laravel..."

# Esperar pela base de dados
echo "⏳ Aguardando conexão com a base de dados..."
until php artisan db:monitor --database=pgsql 2>/dev/null; do
    echo "Base de dados não disponível ainda - aguardando..."
    sleep 2
done

echo "✅ Base de dados conectada!"

# Limpar caches
echo "🧹 Limpando caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Otimizar para produção
echo "⚡ Otimizando aplicação..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Executar migrations
echo "📊 Executando migrations..."
php artisan migrate --force --no-interaction

# Criar owner se não existir
echo "👤 Verificando owner..."
php artisan db:seed --class=OwnerSeeder --force || echo "Owner já existe"

# Criar diretório de uploads se não existir
echo "📁 Configurando diretório de uploads..."
mkdir -p /var/www/html/public/uploads
chown -R www-data:www-data /var/www/html/public/uploads
chmod -R 755 /var/www/html/public/uploads

# Ajustar permissões
echo "🔐 Ajustando permissões..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

echo "✨ Aplicação pronta!"
echo "🌐 Servidor rodando na porta 8080"

# Iniciar supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
