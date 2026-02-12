# 🎯 ImgBB Setup - Guia Completo

## ✅ O Que Foi Implementado

A aplicação agora **detecta automaticamente o ambiente** e usa:
- **Desenvolvimento (local):** Storage local temporário em `public/uploads/`
- **Produção (Render):** ImgBB para storage permanente

## 🔧 Como Funciona

### Detecção Automática

```php
// Usa ImgBB se:
APP_ENV=production  +  IMGBB_API_KEY definida

// Usa storage local se:
APP_ENV=local (ou qualquer outra coisa)
```

### Características

✅ **Model `Image`** tem método `getImageUrl()`:
- Detecta URLs externas (http/https) → retorna direto
- Detecta paths locais → usa `asset()` para URL completa

✅ **Controller `ImageController`**:
- Método `shouldUseExternalStorage()` verifica ambiente
- Método `uploadToImgBB()` faz upload para ImgBB
- Delete inteligente: só apaga ficheiros locais, não toca em URLs externas

✅ **Views atualizadas**:
- `gallery.blade.php`
- `admin/manage.blade.php`
- `admin/votes.blade.php`
- `admin/statistics.blade.php`
- Todas usam `$image->getImageUrl()` em vez de `asset($image->path)`

## 🧪 Testar Localmente

### 1. Seu ambiente local está configurado

Seu `.env` atual:
```env
APP_ENV=local              # ← Usa storage local
IMGBB_API_KEY=e029e58...  # ← Ignorada em local
```

### 2. Iniciar servidor

```bash
php artisan serve
```

### 3. Testar upload

1. Acesse: http://localhost:8000
2. Login admin: `owner@example.com` / `password`
3. Faça upload de uma imagem
4. ✅ Deve aparecer: **"Imagem carregada com sucesso (storage local)!"**
5. Verifique: Ficheiro deve estar em `public/uploads/`

### 4. Verificar que funciona

- ✅ Imagem aparece na galeria pública
- ✅ Imagem aparece no painel admin
- ✅ Pode deletar a imagem (ficheiro é removido)

## 🚀 Deploy para Render (Produção)

### Passo 1: Preparar Render Environment

No Render, **adicionar variável de ambiente**:

```env
IMGBB_API_KEY=e029e58eeece17b8d464c9685b19228d
```

**Como adicionar:**
1. Dashboard Render → Seu Web Service
2. **Environment** (menu lateral)
3. **Add Environment Variable**
   - Key: `IMGBB_API_KEY`
   - Value: `e029e58eeece17b8d464c9685b19228d`
4. **Save Changes**

### Passo 2: Verificar render.yaml

Confirme que o `render.yaml` tem:

```yaml
envVars:
  - key: APP_ENV
    value: production      # ← Importante!
  
  # ... outras variáveis ...
```

### Passo 3: Git Push e Deploy

```bash
# Adicionar tudo
git add .
git commit -m "feat: Add ImgBB integration for production storage"
git push origin main
```

### Passo 4: Criar/Atualizar Blueprint

- **Novo projeto:** Siga [DEPLOY_QUICK.md](DEPLOY_QUICK.md)
- **Projeto existente:** O redeploy automático vai pegar as mudanças

### Passo 5: Testar em Produção

1. Acesse: `https://seu-app.onrender.com`
2. Login: `owner@example.com` / `password`
3. Faça upload de uma imagem
4. ✅ Deve aparecer: **"Imagem carregada com sucesso no ImgBB!"**
5. Verifique: Imagem tem URL do ImgBB (ex: `https://i.ibb.co/...`)

## 🔍 Como Verificar Qual Storage Está Sendo Usado

### Método 1: Mensagem de Sucesso

Após upload, a mensagem indica:
- **"(storage local)"** → Usando filesystem local
- **"no ImgBB"** → Usando ImgBB

### Método 2: Inspecionar URL

Na galeria, clique direito na imagem → "Copiar endereço da imagem"

- **Local:** `http://localhost:8000/uploads/1234567_abc.jpg`
- **ImgBB:** `https://i.ibb.co/xxxxxx/image.jpg`

### Método 3: Verificar Base de Dados

```sql
SELECT id, filename, path FROM images LIMIT 5;
```

- **Local:** `path = 'uploads/1234567_abc.jpg'`
- **ImgBB:** `path = 'https://i.ibb.co/xxxxxx/image.jpg'`

## 📊 Comparação Dev vs Produção

| Aspecto | Desenvolvimento (Local) | Produção (Render + ImgBB) |
|---------|------------------------|---------------------------|
| **APP_ENV** | `local` | `production` |
| **Storage** | `public/uploads/` | ImgBB (cloud) |
| **Persistência** | ✅ Permanente no HD | ✅ Permanente na cloud |
| **Path na DB** | `uploads/xxx.jpg` | `https://i.ibb.co/xxx` |
| **Bandwidth** | Seu servidor | ImgBB (ilimitado) |
| **CDN** | ❌ Não | ✅ Sim |
| **Backup** | Manual | ImgBB cuida |

## 🆘 Troubleshooting

### Upload falha em produção com "IMGBB_API_KEY não configurada"

**Solução:**
1. Render Dashboard → Environment
2. Adicionar `IMGBB_API_KEY=sua_key`
3. Save Changes (redeploy automático)

### Imagens não aparecem após deploy

**Causa:** Imagens antigas eram storage local (perdidas)  
**Solução:** Normal! Fazer novo upload após deploy. Agora vão para ImgBB e ficam permanentes.

### Em local, upload vai para ImgBB (quer usar local)

**Solução:** Verificar `APP_ENV=local` no `.env`

### Em produção, upload usa local (quer usar ImgBB)

**Solução:**
1. Verificar `APP_ENV=production` no Render
2. Verificar `IMGBB_API_KEY` está definida no Render

## 📝 Resumo dos Arquivos Modificados

| Arquivo | Modificação |
|---------|-------------|
| `app/Models/Image.php` | ➕ Método `getImageUrl()` |
| `app/Http/Controllers/ImageController.php` | ➕ ImgBB upload<br>➕ Detecção de ambiente<br>🔧 Delete inteligente |
| `config/services.php` | ➕ Criado com config ImgBB |
| `.env.example` | ➕ `IMGBB_API_KEY` |
| `resources/views/*.blade.php` | 🔧 `asset()` → `getImageUrl()` |

## ✅ Checklist Final

Antes de fazer commit:

- [x] `.env` local tem `APP_ENV=local`
- [x] `.env.example` tem `IMGBB_API_KEY=`
- [x] `config/services.php` existe
- [x] Testado localmente: http://localhost:8000
- [x] Upload local funciona
- [x] Imagens aparecem corretamente

Para deploy:

- [ ] Git add, commit, push
- [ ] Adicionar `IMGBB_API_KEY` no Render Environment
- [ ] Deploy no Render (Blueprint ou manual)
- [ ] Testar upload em produção
- [ ] Verificar URL é do ImgBB

## 🎉 Pronto para Deploy!

Sua aplicação agora:
- ✅ Usa storage local em desenvolvimento
- ✅ Usa ImgBB em produção (storage permanente)
- ✅ Funciona perfeitamente no free plan do Render
- ✅ Sem configuração manual - detecção automática!

**Próximo passo:** 
```bash
git add .
git commit -m "feat: Add ImgBB integration for production"
git push origin main
```

Depois siga [DEPLOY_QUICK.md](DEPLOY_QUICK.md) para criar o Blueprint no Render!
