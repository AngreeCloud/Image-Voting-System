# 💾 Armazenamento de Imagens no Free Plan

## ⚠️ Limitação do Render Free Plan

O **Render Free Plan não suporta persistent disks**. Isso significa que uploads de imagens serão armazenados no filesystem temporário do container e **serão perdidos** quando:
- Fizer novo deploy
- Container reiniciar
- Aplicação for suspensa (inatividade)

## 🔄 Alternativas Gratuitas

### **Opção 1: Cloudinary (Recomendado)** ⭐

**Plano Gratuito Inclui:**
- ✅ 25 GB storage
- ✅ 25 GB bandwidth/mês
- ✅ Otimização automática de imagens
- ✅ CDN global
- ✅ Transformações (resize, crop, etc)

**Setup:**

1. **Criar conta:** https://cloudinary.com/users/register/free

2. **Instalar package:**
```bash
composer require cloudinary-labs/cloudinary-laravel
```

3. **Publicar config:**
```bash
php artisan vendor:publish --provider="CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider"
```

4. **Adicionar no `.env`:**
```env
CLOUDINARY_CLOUD_NAME=seu_cloud_name
CLOUDINARY_API_KEY=sua_api_key
CLOUDINARY_API_SECRET=seu_api_secret
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
```

5. **Atualizar ImageController:**
```php
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

public function upload(Request $request)
{
    // Upload para Cloudinary
    $uploadedFileUrl = Cloudinary::upload(
        $request->file('image')->getRealPath()
    )->getSecurePath();
    
    // Salvar URL na DB
    Image::create([
        'filename' => $request->file('image')->getClientOriginalName(),
        'path' => $uploadedFileUrl,
        'user_id' => Auth::id(),
    ]);
}
```

---

### **Opção 2: AWS S3 Free Tier**

**Plano Gratuito (12 meses):**
- ✅ 5 GB storage
- ✅ 20,000 GET requests
- ✅ 2,000 PUT requests

**Setup:**

1. **Criar conta AWS:** https://aws.amazon.com/free/

2. **Instalar package:**
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

3. **Configurar `.env`:**
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=sua_access_key
AWS_SECRET_ACCESS_KEY=seu_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=seu_bucket_name
AWS_URL=https://seu_bucket.s3.amazonaws.com
```

4. **Atualizar upload:**
```php
public function upload(Request $request)
{
    $path = $request->file('image')->store('uploads', 's3');
    $url = Storage::disk('s3')->url($path);
    
    Image::create([
        'filename' => $request->file('image')->getClientOriginalName(),
        'path' => $url,
        'user_id' => Auth::id(),
    ]);
}
```

---

### **Opção 3: ImgBB (Simples)**

**Plano Gratuito:**
- ✅ Storage ilimitado
- ✅ Sem bandwidth limit
- ✅ API simples
- ⚠️ Max 32 MB por imagem

**Setup:**

1. **API Key:** https://api.imgbb.com/

2. **Install Guzzle:**
```bash
composer require guzzlehttp/guzzle
```

3. **Helper no Controller:**
```php
use Illuminate\Support\Facades\Http;

private function uploadToImgBB($file)
{
    $response = Http::asMultipart()->post('https://api.imgbb.com/1/upload', [
        [
            'name' => 'key',
            'contents' => env('IMGBB_API_KEY')
        ],
        [
            'name' => 'image',
            'contents' => fopen($file->getRealPath(), 'r'),
            'filename' => $file->getClientOriginalName()
        ]
    ]);

    return $response->json()['data']['url'];
}
```

---

### **Opção 4: Storage Temporário (Sem mudanças)**

**Usar filesystem local** - Mais simples mas **perde uploads em cada deploy**.

**Quando usar:**
- ✅ Apenas para testes/desenvolvimento
- ✅ Dados não críticos
- ✅ Deploy ocasional

**Limitações:**
- ❌ Uploads são perdidos em deploy
- ❌ Uploads são perdidos em restart
- ❌ Não escala (múltiplos containers)

**Sem mudanças no código** - já está configurado assim!

---

## 📊 Comparação

| Solução | Storage | Bandwidth | Complexidade | Recomendado |
|---------|---------|-----------|--------------|-------------|
| **Cloudinary** | 25 GB | 25 GB/mês | Média | ⭐⭐⭐⭐⭐ |
| **AWS S3** | 5 GB | Limitado | Alta | ⭐⭐⭐⭐ |
| **ImgBB** | Ilimitado | Ilimitado | Baixa | ⭐⭐⭐ |
| **Local (temp)** | Container | N/A | Nenhuma | ⭐⭐ (apenas testes) |

## 🎯 Recomendação

### Para Produção:
**Use Cloudinary** - melhor plano gratuito, CDN incluído, otimização automática.

### Para Desenvolvimento/Testes:
**Use Local (temporário)** - sem configuração adicional, aceite perder uploads.

### Para Projetos Pequenos:
**Use ImgBB** - setup simples, storage ilimitado.

---

## 🚀 Deploy com Storage Temporário (Free Plan Atual)

**Aceitando a limitação:**

1. **Faça deploy normalmente:**
```bash
git add .
git commit -m "Deploy: Free plan sem persistent disk"
git push origin main
```

2. **No Render Blueprint:** Apply (funcionará agora)

3. **Aviso aos usuários:**
```
⚠️ Nota: Uploads são temporários e serão perdidos em deploys.
Para produção, recomendamos usar Cloudinary.
```

**Código funciona sem mudanças!** Apenas aceite que uploads são temporários.

---

## 💰 Upgrade para Persistent Disk

Se quiser persistent disk permanente no Render:

**Starter Plan: $7/mês**
- ✅ Persistent Disk incluído
- ✅ Sem suspensão automática
- ✅ Melhor performance
- ✅ Mais recursos

Para adicionar disk no Starter Plan, descomentar no `render.yaml`:
```yaml
disk:
  name: uploads-disk
  mountPath: /var/www/html/public/uploads
  sizeGB: 1
```

---

## 📝 Resumo

**Free Plan Render:**
- ❌ Não tem persistent disk
- ✅ Funciona com storage temporário
- ✅ Funciona com storage externo (Cloudinary, S3, etc)

**Melhor estratégia:**
1. **Agora:** Deploy com storage temporário (funciona de imediato)
2. **Depois:** Adicionar Cloudinary (30 min de setup)
3. **Futuro:** Upgrade para Starter Plan se precisar (disk persistente)

---

## 🆘 Precisa de Ajuda?

Ver implementação completa com Cloudinary em:
- Documentação oficial: https://github.com/cloudinary-labs/cloudinary-laravel
- Tutorial: https://cloudinary.com/documentation/laravel_integration

**Escolha a melhor opção para seu caso e siga em frente!** 🚀
