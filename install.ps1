# Script de Instalação - Sistema de Votação Laravel
# Execute este ficheiro para configurar automaticamente a aplicação

Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "  Sistema de Votação - Instalação    " -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host ""

# 1. Verificar se o Composer está instalado
Write-Host "[1/8] Verificando Composer..." -ForegroundColor Yellow
if (!(Get-Command composer -ErrorAction SilentlyContinue)) {
    Write-Host "ERRO: Composer não encontrado. Por favor, instale o Composer primeiro." -ForegroundColor Red
    Write-Host "Download: https://getcomposer.org/download/" -ForegroundColor Red
    exit 1
}
Write-Host "OK: Composer encontrado!" -ForegroundColor Green
Write-Host ""

# 2. Instalar dependências do Composer
Write-Host "[2/8] Instalando dependências do Composer..." -ForegroundColor Yellow
composer install --no-interaction
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERRO: Falha ao instalar dependências do Composer" -ForegroundColor Red
    exit 1
}
Write-Host "OK: Dependências instaladas!" -ForegroundColor Green
Write-Host ""

# 3. Copiar .env.example para .env se não existir
Write-Host "[3/8] Configurando ficheiro .env..." -ForegroundColor Yellow
if (!(Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "OK: Ficheiro .env criado!" -ForegroundColor Green
} else {
    Write-Host "OK: Ficheiro .env já existe!" -ForegroundColor Green
}
Write-Host ""

# 4. Gerar chave da aplicação
Write-Host "[4/8] Gerando chave da aplicação..." -ForegroundColor Yellow
php artisan key:generate --ansi
Write-Host "OK: Chave gerada!" -ForegroundColor Green
Write-Host ""

# 5. Criar diretório de uploads
Write-Host "[5/8] Criando diretório de uploads..." -ForegroundColor Yellow
if (!(Test-Path "public\uploads")) {
    New-Item -ItemType Directory -Path "public\uploads" -Force | Out-Null
    Write-Host "OK: Diretório public\uploads criado!" -ForegroundColor Green
} else {
    Write-Host "OK: Diretório já existe!" -ForegroundColor Green
}
Write-Host ""

# 6. Configuração da base de dados
Write-Host "[6/8] Configuração da Base de Dados" -ForegroundColor Yellow
Write-Host "Por favor, configure as credenciais MySQL no ficheiro .env:" -ForegroundColor Cyan
Write-Host "  - DB_DATABASE=laravel_voting" -ForegroundColor White
Write-Host "  - DB_USERNAME=root" -ForegroundColor White
Write-Host "  - DB_PASSWORD=<sua_password>" -ForegroundColor White
Write-Host ""
Write-Host "Crie a base de dados manualmente no MySQL:" -ForegroundColor Cyan
Write-Host "  CREATE DATABASE laravel_voting;" -ForegroundColor White
Write-Host ""

$continue = Read-Host "Já configurou a base de dados? (s/n)"
if ($continue -ne "s" -and $continue -ne "S") {
    Write-Host "Configure a base de dados e execute novamente este script." -ForegroundColor Yellow
    exit 0
}

# 7. Executar migrations
Write-Host "[7/8] Executando migrations..." -ForegroundColor Yellow
php artisan migrate --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERRO: Falha ao executar migrations" -ForegroundColor Red
    Write-Host "Verifique as configurações da base de dados no .env" -ForegroundColor Red
    exit 1
}
Write-Host "OK: Migrations executadas!" -ForegroundColor Green
Write-Host ""

# 8. Executar seeder para criar admin
Write-Host "[8/8] Criando utilizador admin..." -ForegroundColor Yellow
php artisan db:seed --class=AdminSeeder --force
Write-Host "OK: Admin criado!" -ForegroundColor Green
Write-Host ""

# Instalar dependências NPM (opcional)
Write-Host "[OPCIONAL] Instalar dependências NPM?" -ForegroundColor Yellow
$installNpm = Read-Host "Deseja instalar dependências NPM? (s/n)"
if ($installNpm -eq "s" -or $installNpm -eq "S") {
    if (Get-Command npm -ErrorAction SilentlyContinue) {
        Write-Host "Instalando dependências NPM..." -ForegroundColor Yellow
        npm install
        Write-Host "OK: Dependências NPM instaladas!" -ForegroundColor Green
    } else {
        Write-Host "Aviso: NPM não encontrado. Ignorando..." -ForegroundColor Yellow
    }
}

# Conclusão
Write-Host ""
Write-Host "=======================================" -ForegroundColor Green
Write-Host "     INSTALAÇÃO CONCLUÍDA!           " -ForegroundColor Green
Write-Host "=======================================" -ForegroundColor Green
Write-Host ""
Write-Host "Credenciais do Admin:" -ForegroundColor Cyan
Write-Host "  Email:    admin@example.com" -ForegroundColor White
Write-Host "  Password: password" -ForegroundColor White
Write-Host ""
Write-Host "Para iniciar o servidor:" -ForegroundColor Cyan
Write-Host "  php artisan serve" -ForegroundColor White
Write-Host ""
Write-Host "Acesse: http://localhost:8000" -ForegroundColor Cyan
Write-Host ""
