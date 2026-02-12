#!/bin/bash

# Script para testar Docker localmente antes do deploy

echo "🔍 Testando build Docker localmente..."

# Build da imagem
echo "📦 Building Docker image..."
docker build -t laravel-voting-test .

if [ $? -ne 0 ]; then
    echo "❌ Erro no build da imagem Docker"
    exit 1
fi

echo "✅ Build concluído com sucesso!"

# Executar container
echo "🚀 Iniciando container..."
docker run -d \
    --name laravel-voting-test \
    -p 8080:8080 \
    -e APP_KEY=base64:$(openssl rand -base64 32) \
    -e APP_ENV=local \
    -e APP_DEBUG=true \
    -e DB_CONNECTION=sqlite \
    -e DB_DATABASE=/var/www/html/database/database.sqlite \
    laravel-voting-test

if [ $? -ne 0 ]; then
    echo "❌ Erro ao iniciar container"
    exit 1
fi

sleep 10

# Testar se está respondendo
echo "🧪 Testando resposta do servidor..."
curl -s http://localhost:8080 > /dev/null

if [ $? -eq 0 ]; then
    echo "✅ Aplicação respondendo corretamente!"
    echo "🌐 Acesse: http://localhost:8080"
else
    echo "❌ Aplicação não está respondendo"
    docker logs laravel-voting-test
fi

echo ""
echo "📋 Comandos úteis:"
echo "  Ver logs:     docker logs -f laravel-voting-test"
echo "  Parar:        docker stop laravel-voting-test"
echo "  Remover:      docker rm laravel-voting-test"
echo "  Shell:        docker exec -it laravel-voting-test sh"
