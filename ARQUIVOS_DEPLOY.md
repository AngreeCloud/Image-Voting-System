# 📦 Arquivos de Deploy - Render.com

Todos os arquivos necessários para deploy no Render foram criados com sucesso!

## ✅ Arquivos Criados

### 🐳 Docker Configuration
- **`Dockerfile`** - Container principal da aplicação
  - Base: PHP 8.2 FPM Alpine
  - NGINX + Supervisor
  - PostgreSQL e MySQL support
  - Otimizado para produção

### ⚙️ Render Configuration
- **`render.yaml`** - Blueprint (Infrastructure as Code)
  - Web Service configurado
  - PostgreSQL Database
  - Persistent Disk (1GB para uploads)
  - Variáveis de ambiente automáticas

### 🔧 Docker Support Files
- **`docker/nginx.conf`** - Configuração principal do NGINX
- **`docker/default.conf`** - Virtual host Laravel
- **`docker/supervisord.conf`** - Gerenciador de processos
- **`docker/start.sh`** - Script de inicialização e migrations

### 📝 Environment & Optimization
- **`.env.example`** - Template com PostgreSQL configurado
- **`.dockerignore`** - Otimização de build (exclui desnecessários)

### 🧪 Testing Scripts
- **`test-docker.sh`** - Teste local em Linux/Mac
- **`test-docker.ps1`** - Teste local em Windows PowerShell

### 📚 Documentation
- **`DEPLOY_RENDER.md`** - Guia completo (passo-a-passo detalhado)
- **`DEPLOY_QUICK.md`** - Guia rápido (deploy em 3 passos)
- **`DEPLOY.md`** - Atualizado com opção Render

## 🚀 Próximos Passos

### 1. Commit e Push
```bash
git add .
git commit -m "feat: Adicionar configuração Docker para deploy no Render"
git push origin main
```

### 2. Deploy no Render
Siga o guia: [DEPLOY_QUICK.md](DEPLOY_QUICK.md)

Ou em resumo:
1. Acesse [render.com](https://render.com)
2. New + → Blueprint
3. Conecte o repositório GitHub
4. Apply (aguarde ~5-10 min)

### 3. Acessar Aplicação
- URL: `https://seu-app.onrender.com`
- Owner: `owner@example.com` / `password`

⚠️ **Altere a password do owner imediatamente!**

## 🧰 Comandos Úteis

### Testar Docker Localmente
```bash
# Linux/Mac
chmod +x test-docker.sh
./test-docker.sh

# Windows PowerShell
.\test-docker.ps1
```

### Build Manual
```bash
docker build -t laravel-voting .
```

### Run Manual
```bash
docker run -d -p 8080:8080 \
  -e APP_KEY=base64:... \
  -e DB_CONNECTION=sqlite \
  laravel-voting
```

### Ver Logs
```bash
docker logs -f <container-id>
```

## 📊 O que o Deploy Faz Automaticamente

1. **Build Docker**
   - Instala PHP 8.2 + extensões
   - Instala NGINX + Supervisor
   - Composer install (otimizado)
   - NPM build (Vite)

2. **Inicialização** (start.sh)
   - Aguarda conexão DB
   - Limpa caches
   - Otimiza para produção
   - Executa migrations
   - Cria owner (seed)
   - Configura permissões

3. **Runtime**
   - NGINX escuta porta 8080
   - PHP-FPM processa requests
   - Supervisor gerencia processos
   - Auto-restart em caso de crash

## 🔒 Segurança Incluída

✅ **APP_KEY** gerado automaticamente pelo Render
✅ **SSL/HTTPS** configurado automaticamente
✅ **PostgreSQL** com credenciais seguras
✅ **Uploads** em disk persistente (não perde dados)
✅ **Sessions** armazenadas na DB (stateless containers)
✅ **Logs** vão para stderr (monitorização Render)

## 💰 Custos

### Free Plan (Default)
- Web Service: **Grátis**
- PostgreSQL: **Grátis** (1GB)
- Disk: **Grátis** (1GB)
- SSL: **Grátis**
- **Total: €0/mês**

### Limitações Free Plan
- ⏱️ Suspende após 15 min inativo
- 🐌 Cold start ~30-60s
- 🔄 750 horas/mês

### Starter Plan ($7/mês)
- ✅ Sem suspensão
- ✅ CPU/RAM dedicadas
- ✅ Melhor performance
- Recomendado para produção real

## 🆘 Troubleshooting

### Build falha
1. Testar localmente: `./test-docker.sh`
2. Verificar logs no Render
3. Verificar sintaxe Dockerfile

### DB não conecta
1. Verificar PostgreSQL está "Available"
2. Copiar Internal Connection String
3. Atualizar variáveis DB_* no Web Service
4. Redeploy

### Uploads não funcionam
1. Verificar Disk está montado
2. Path: `/var/www/html/public/uploads`
3. Verificar permissões no start.sh

### Erro 500
1. Verificar `APP_KEY` está definida
2. Ver logs: Dashboard → Logs
3. Verificar migrations executadas

## 📚 Recursos

- [Render Docs](https://render.com/docs)
- [Laravel Docs](https://laravel.com/docs)
- [Docker Docs](https://docs.docker.com)

## ✨ Features do Deploy

✅ **Zero-downtime deploys**
✅ **Auto-scaling** (paid plans)
✅ **Health checks** automáticos
✅ **Rollback** com um clique
✅ **Environment por branch** (staging/prod)
✅ **Logs em tempo real**
✅ **Metrics** (CPU, RAM, requests)
✅ **Backup automático** da DB (paid plans)

## 🎉 Conclusão

Tudo pronto para deploy no Render! 

Arquivos criados totalmente automáticos - só fazer push e criar o Blueprint.

**Boa sorte com o deploy!** 🚀

---

**Dúvidas?** Consulte [DEPLOY_RENDER.md](DEPLOY_RENDER.md) para guia completo.
