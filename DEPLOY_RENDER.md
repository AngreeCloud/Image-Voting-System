# Deploy no Render - Guia Completo

## 📋 Pré-requisitos

1. Conta no [Render.com](https://render.com) (gratuita)
2. Conta no [GitHub](https://github.com) (ou GitLab/Bitbucket)
3. Repositório Git com o código da aplicação

## 🚀 Passo 1: Preparar o Repositório

### 1.1 Criar Repositório no GitHub

```bash
# Inicializar Git (se ainda não tiver)
git init

# Adicionar todos os ficheiros
git add .

# Fazer primeiro commit
git commit -m "Initial commit - Laravel Voting System"

# Criar repositório no GitHub e adicionar remote
git remote add origin https://github.com/seu-usuario/laravel-voting.git

# Push para o GitHub
git branch -M main
git push -u origin main
```

### 1.2 Verificar Ficheiros Necessários

Certifique-se que o repositório contém:
- ✅ `Dockerfile`
- ✅ `render.yaml`
- ✅ `docker/nginx.conf`
- ✅ `docker/default.conf`
- ✅ `docker/supervisord.conf`
- ✅ `docker/start.sh`
- ✅ `.env.example`

## 🎯 Passo 2: Configurar no Render

### 2.1 Criar Conta e Conectar GitHub

1. Acesse [render.com](https://render.com)
2. Clique em **"Get Started"**
3. Conecte sua conta do GitHub
4. Autorize o Render a aceder aos seus repositórios

### 2.2 Criar Blueprint (Deploy Automático)

1. No dashboard do Render, clique em **"New +"**
2. Selecione **"Blueprint"**
3. Conecte o seu repositório GitHub
4. O Render detectará automaticamente o `render.yaml`
5. Clique em **"Apply"**

O Render criará automaticamente:
- ✅ Web Service (aplicação Laravel)
- ✅ PostgreSQL Database (base de dados gratuita)
- ✅ Persistent Disk (1GB para uploads)

### 2.3 Configuração Manual (Alternativa)

Se preferir criar manualmente:

#### Criar Base de Dados PostgreSQL:
1. Dashboard → **"New +"** → **"PostgreSQL"**
2. Nome: `laravel-voting-db`
3. Database Name: `voting_db`
4. User: `voting_user`
5. Region: `Frankfurt` (ou mais próximo)
6. Plan: **Free**
7. Clique **"Create Database"**

#### Criar Web Service:
1. Dashboard → **"New +"** → **"Web Service"**
2. Conecte o repositório GitHub
3. Configurações:
   - **Name**: `laravel-voting-app`
   - **Region**: `Frankfurt` (mesma da DB)
   - **Branch**: `main`
   - **Runtime**: `Docker`
   - **Dockerfile Path**: `./Dockerfile`
   - **Docker Build Context**: `.`
4. Plan: **Free**

⚠️ **Nota sobre Storage:** O free plan não tem persistent disk. Uploads serão temporários (perdidos em deploys).  
**Alternativas gratuitas:** Cloudinary, AWS S3, ImgBB - ver [STORAGE_ALTERNATIVAS.md](STORAGE_ALTERNATIVAS.md)

## 🔐 Passo 3: Configurar Variáveis de Ambiente

No Web Service, vá para **"Environment"** e adicione:

### Variáveis Obrigatórias:

```bash
APP_NAME=Laravel Voting System
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GERAR_AUTOMATICAMENTE_NO_RENDER
LOG_CHANNEL=stderr
LOG_LEVEL=error

# Base de Dados (copiar da PostgreSQL Database Internal URL)
DB_CONNECTION=pgsql
DB_HOST=dpg-xxxxx.frankfurt-postgres.render.com
DB_PORT=5432
DB_DATABASE=voting_db
DB_USERNAME=voting_user
DB_PASSWORD=xxxxxxxxxxxxx

# Sessões e Cache
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

### Gerar APP_KEY:

O Render pode gerar automaticamente, ou use:

```bash
php artisan key:generate --show
```

Copie o valor e adicione na variável `APP_KEY`.

### URL da Aplicação:

Após o deploy, atualize:
```bash
APP_URL=https://seu-app.onrender.com
```

## 📊 Passo 4: Deploy e Verificação

### 4.1 Processo de Deploy

1. O Render iniciará o build automaticamente
2. Acompanhe os logs em **"Logs"**
3. Aguarde a conclusão (5-10 minutos no primeiro deploy)

### 4.2 Verificar Deploy

Quando aparecer:
```
✨ Aplicação pronta!
🌐 Servidor rodando na porta 8080
```

A aplicação está online!

### 4.3 Aceder à Aplicação

URL: `https://seu-app.onrender.com`

**Credenciais Owner:**
- Email: `owner@example.com`
- Password: `password`

⚠️ **IMPORTANTE**: Altere a password do owner imediatamente!

## 🔧 Passo 5: Configurações Pós-Deploy

### 5.1 Alterar Password do Owner

Via SSH (console do Render):
```bash
php artisan tinker
$owner = User::where('role', 'owner')->first();
$owner->password = Hash::make('nova-senha-super-segura');
$owner->save();
exit
```

### 5.2 Configurar Email (Opcional)

Se quiser enviar emails, atualize as variáveis:
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_username
MAIL_PASSWORD=sua_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seu-dominio.com
```

### 5.3 Configurar Domínio Customizado (Opcional)

1. No Web Service, vá para **"Settings"** → **"Custom Domains"**
2. Adicione seu domínio
3. Configure DNS:
   - Tipo: `CNAME`
   - Nome: `@` ou `www`
   - Valor: `seu-app.onrender.com`

## 🔄 Atualizações Automáticas

O Render faz **auto-deploy** em cada push para o GitHub:

```bash
# Fazer alterações
git add .
git commit -m "Minha atualização"
git push origin main

# O Render detecta automaticamente e faz deploy
```

## 📈 Monitorização

### Logs em Tempo Real:
```
Dashboard → Web Service → Logs
```

### Métricas:
```
Dashboard → Web Service → Metrics
```

Mostra:
- CPU usage
- Memory usage
- Request rate
- Response time

## 🛠️ Comandos Úteis

### Aceder ao Shell (Console):

1. Dashboard → Web Service → **"Shell"**
2. Executar comandos Laravel:

```bash
# Verificar status
php artisan about

# Limpar caches
php artisan optimize:clear

# Ver migrações
php artisan migrate:status

# Criar admin
php artisan db:seed --class=OwnerSeeder
```

### Reiniciar Aplicação:

```
Dashboard → Web Service → Manual Deploy → Deploy Latest Commit
```

## 🐛 Troubleshooting

### Erro 500 - Internal Server Error

1. Verificar logs: `Dashboard → Logs`
2. Verificar `APP_KEY` está definida
3. Verificar conexão com base de dados

### Erro de Conexão à Base de Dados

1. Verificar que PostgreSQL Database está running
2. Copiar **Internal Connection String** da DB
3. Atualizar variáveis `DB_*` no Web Service
4. Fazer redeploy

### Uploads não funcionam / Uploads desaparecem

**Free Plan:** Uploads são temporários (storage efémero). Cada deploy ou restart apaga os ficheiros.

**Soluções:**
1. **Cloudinary** (recomendado) - 25GB grátis, CDN, otimização
2. **AWS S3** - 5GB grátis por 12 meses
3. **ImgBB** - Uploads ilimitados, simples

Ver guia completo: [STORAGE_ALTERNATIVAS.md](STORAGE_ALTERNATIVAS.md)

### Aplicação lenta no Free Plan

O Free Plan tem limitações:
- CPU compartilhada
- Suspensão após 15 min de inatividade
- Primeiro request após suspensão demora ~30-60s

Upgrade para **Starter Plan** ($7/mês) resolve isso.

## 💰 Custos

### Free Plan (Atual):
- ✅ Web Service: Grátis
- ✅ PostgreSQL: Grátis (1GB)
- ❌ Disk: **Não disponível no free plan**
- ⚠️ Limitações:
  - Suspende após 15 min inativo
  - 750 horas/mês (suficiente para um serviço)
  - CPU e RAM compartilhadas
  - **Storage efémero** (uploads perdidos em deploys)

**Soluções para storage:**
- **Grátis:** Cloudinary (25GB), AWS S3 (5GB), ImgBB - [STORAGE_ALTERNATIVAS.md](STORAGE_ALTERNATIVAS.md)
- **Pago:** Starter Plan ($7/mês) com persistent disk

### Starter Plan ($7/mês):
- ✅ Sem suspensão
- ✅ CPU e RAM dedicadas
- ✅ Persistent disk (1GB incluído)
- ✅ Mais recursos
- Recomendado para produção com uploads frequentes

## 📚 Recursos Adicionais

- [Documentação Render](https://render.com/docs)
- [Render Community](https://community.render.com)
- [Status Page](https://status.render.com)

## ✅ Checklist Final

Antes de considerar deploy completo:

- [ ] Aplicação acessível via URL
- [ ] Login owner funciona
- [ ] PostgreSQL conectada
- [ ] Uploads de imagens funcionam
- [ ] Votos são registados
- [ ] Estatísticas aparecem
- [ ] Password do owner alterada
- [ ] SSL/HTTPS ativo (automático no Render)
- [ ] Domínio customizado configurado (opcional)
- [ ] Logs sem erros críticos

## 🎉 Deploy Completo!

Sua aplicação Laravel está agora rodando em produção no Render com:
- ✅ HTTPS automático
- ✅ Base de dados PostgreSQL
- ✅ Storage persistente para uploads
- ✅ Auto-deploy do GitHub
- ✅ Logs e métricas em tempo real

---

**Suporte:** Para questões sobre o Render, contacte [support@render.com](mailto:support@render.com)
