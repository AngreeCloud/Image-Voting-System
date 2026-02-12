# Docker Local Testing (PowerShell)

Write-Host "🔍 Testando build Docker localmente..." -ForegroundColor Cyan

# Build da imagem
Write-Host "📦 Building Docker image..." -ForegroundColor Yellow
docker build -t laravel-voting-test .

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erro no build da imagem Docker" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Build concluído com sucesso!" -ForegroundColor Green

# Gerar APP_KEY
$appKey = "base64:" + [Convert]::ToBase64String([System.Text.Encoding]::UTF8.GetBytes((New-Guid).ToString()))

# Executar container
Write-Host "🚀 Iniciando container..." -ForegroundColor Yellow
docker run -d `
    --name laravel-voting-test `
    -p 8080:8080 `
    -e APP_KEY=$appKey `
    -e APP_ENV=local `
    -e APP_DEBUG=true `
    -e DB_CONNECTION=sqlite `
    -e DB_DATABASE=/var/www/html/database/database.sqlite `
    laravel-voting-test

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erro ao iniciar container" -ForegroundColor Red
    exit 1
}

Start-Sleep -Seconds 10

# Testar se está respondendo
Write-Host "🧪 Testando resposta do servidor..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080" -UseBasicParsing -TimeoutSec 5
    Write-Host "✅ Aplicação respondendo corretamente!" -ForegroundColor Green
    Write-Host "🌐 Acesse: http://localhost:8080" -ForegroundColor Cyan
} catch {
    Write-Host "❌ Aplicação não está respondendo" -ForegroundColor Red
    docker logs laravel-voting-test
}

Write-Host ""
Write-Host "📋 Comandos úteis:" -ForegroundColor Cyan
Write-Host "  Ver logs:     docker logs -f laravel-voting-test"
Write-Host "  Parar:        docker stop laravel-voting-test"
Write-Host "  Remover:      docker rm laravel-voting-test"
Write-Host "  Shell:        docker exec -it laravel-voting-test sh"
