# 🚀 Deploy Rápido no Render

## Passo 1: Preparar Repositório

```bash
# Adicionar tudo ao Git
git add .
git commit -m "Deploy: Laravel Voting System"

# Push para GitHub
git push origin main
```

## Passo 2: Criar no Render

1. Acesse [render.com](https://render.com)
2. **New +** → **Blueprint**
3. Conecte seu repositório GitHub
4. O `render.yaml` será detectado automaticamente
5. Clique **"Apply"**

## Passo 3: Aguardar Deploy

- ⏱️ Primeiro deploy: ~5-10 minutos
- 📊 Acompanhe em "Logs"

## Passo 4: Acessar Aplicação

- 🌐 URL: `https://seu-app.onrender.com`
- 👤 Owner: `owner@example.com` / `password`

⚠️ **Altere a password imediatamente!**

---

## 🧪 Testar Localmente (Antes do Deploy)

### Linux/Mac:
```bash
chmod +x test-docker.sh
./test-docker.sh
```

### Windows PowerShell:
```powershell
.\test-docker.ps1
```

Acesse: http://localhost:8080

---

## 📁 Arquivos de Deploy

| Arquivo | Descrição |
|---------|-----------|
| `Dockerfile` | Configuração do container |
| `render.yaml` | Blueprint do Render (web + db + disk) |
| `docker/nginx.conf` | NGINX principal |
| `docker/default.conf` | Virtual host Laravel |
| `docker/supervisord.conf` | Gerenciador de processos |
| `docker/start.sh` | Script de inicialização |
| `.dockerignore` | Arquivos ignorados no build |

---

## 🆘 Problemas Comuns

### Build falha:
```bash
# Verificar logs no Render
# Testar localmente primeiro
./test-docker.sh
```

### Erro 500:
```bash
# Verificar APP_KEY está definida
# Verificar DB_* variáveis estão corretas
```

### DB não conecta:
```bash
# Copiar Internal Connection String da PostgreSQL
# Atualizar variáveis DB_* no Web Service
# Redeploy
```

---

## 📚 Documentação Completa

Ver: [DEPLOY_RENDER.md](DEPLOY_RENDER.md)

---

**Criado para deploy no Render.com com Docker** 🐳
