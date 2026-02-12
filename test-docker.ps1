# Docker Local Testing (PowerShell)

Write-Host "[*] Testando build Docker localmente..." -ForegroundColor Cyan

# Verificar se Docker está instalado
Write-Host "[*] Verificando instalacao do Docker..." -ForegroundColor Yellow
$dockerInstalled = Get-Command docker -ErrorAction SilentlyContinue

if (-not $dockerInstalled) {
    Write-Host ""
    Write-Host "[X] Docker nao encontrado!" -ForegroundColor Red
    Write-Host ""
    Write-Host "[INFO] Docker Desktop nao esta instalado ou nao esta no PATH." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Para testar localmente, instale o Docker Desktop:" -ForegroundColor Cyan
    Write-Host "  1. Download: https://www.docker.com/products/docker-desktop" -ForegroundColor White
    Write-Host "  2. Execute o instalador" -ForegroundColor White
    Write-Host "  3. Reinicie o computador" -ForegroundColor White
    Write-Host "  4. Inicie o Docker Desktop" -ForegroundColor White
    Write-Host "  5. Execute este script novamente" -ForegroundColor White
    Write-Host ""
    Write-Host "[INFO] Alternativa: Deploy diretamente no Render (nao precisa Docker local)" -ForegroundColor Cyan
    Write-Host "  Ver: DEPLOY_QUICK.md" -ForegroundColor White
    Write-Host ""
    exit 1
}

# Verificar se Docker está rodando
Write-Host "[*] Verificando se Docker Desktop esta rodando..." -ForegroundColor Yellow
docker ps 2>&1 | Out-Null

if ($LASTEXITCODE -ne 0) {
    Write-Host ""
    Write-Host "[X] Docker Desktop nao esta rodando!" -ForegroundColor Red
    Write-Host ""
    Write-Host "[INFO] Por favor:" -ForegroundColor Yellow
    Write-Host "  1. Abra o Docker Desktop" -ForegroundColor White
    Write-Host "  2. Aguarde iniciar completamente" -ForegroundColor White
    Write-Host "  3. Execute este script novamente" -ForegroundColor White
    Write-Host ""
    exit 1
}

Write-Host "[OK] Docker instalado e rodando!" -ForegroundColor Green
Write-Host ""

# Build da imagem
Write-Host "[*] Building Docker image..." -ForegroundColor Yellow
docker build -t laravel-voting-test .

if ($LASTEXITCODE -ne 0) {
    Write-Host "[X] Erro no build da imagem Docker" -ForegroundColor Red
    exit 1
}

Write-Host "[OK] Build concluido com sucesso!" -ForegroundColor Green

# Gerar APP_KEY
$appKey = "base64:" + [Convert]::ToBase64String([System.Text.Encoding]::UTF8.GetBytes((New-Guid).ToString()))

# Executar container
Write-Host "[*] Iniciando container..." -ForegroundColor Yellow
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
    Write-Host "[X] Erro ao iniciar container" -ForegroundColor Red
    exit 1
}

Start-Sleep -Seconds 10

# Testar se está respondendo
Write-Host "[*] Testando resposta do servidor..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080" -UseBasicParsing -TimeoutSec 5
    Write-Host "[OK] Aplicacao respondendo corretamente!" -ForegroundColor Green
    Write-Host "[>>] Acesse: http://localhost:8080" -ForegroundColor Cyan
} catch {
    Write-Host "[X] Aplicacao nao esta respondendo" -ForegroundColor Red
    docker logs laravel-voting-test
}

Write-Host ""
Write-Host "[INFO] Comandos uteis:" -ForegroundColor Cyan
Write-Host "  Ver logs:     docker logs -f laravel-voting-test"
Write-Host "  Parar:        docker stop laravel-voting-test"
Write-Host "  Remover:      docker rm laravel-voting-test"
Write-Host "  Shell:        docker exec -it laravel-voting-test sh"


