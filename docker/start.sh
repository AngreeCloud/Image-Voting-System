#!/bin/sh
set -e

echo "🚀 Iniciando aplicação Laravel..."

# Esperar pela base de dados
echo "⏳ Aguardando conexão com a base de dados..."
max_attempts=30
attempt=0

until php -r "
try {
    \$pdo = new PDO(
        'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    exit(0);
} catch (PDOException \$e) {
    exit(1);
}
" || [ $attempt -eq $max_attempts ]; do
    attempt=$((attempt + 1))
    echo "Base de dados não disponível ainda - tentativa $attempt/$max_attempts..."
    sleep 2
done

if [ $attempt -eq $max_attempts ]; then
    echo "❌ Falha ao conectar à base de dados após $max_attempts tentativas"
    echo "🔍 Verificando variáveis de ambiente:"
    echo "DB_CONNECTION: $DB_CONNECTION"
    echo "DB_HOST: $DB_HOST"
    echo "DB_PORT: $DB_PORT"
    echo "DB_DATABASE: $DB_DATABASE"
    exit 1
fi

echo "✅ Base de dados conectada!"

# Gerar APP_KEY se não existir
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Gerando APP_KEY..."
    php artisan key:generate --force
    echo "✅ APP_KEY gerada automaticamente!"
else
    echo "✅ APP_KEY já configurada"
fi

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
