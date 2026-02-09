# Guia de Personalização - Sistema de Votação

Este guia mostra como personalizar a aplicação para suas necessidades específicas.

---

## 🎨 Personalização Visual

### 1. Alterar Cores do Tema

**Ficheiro:** `resources/views/layouts/app.blade.php`

```css
/* Localizar esta seção no <style> */
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* EXEMPLOS DE GRADIENTES ALTERNATIVOS: */

/* Azul para verde */
body {
    background: linear-gradient(135deg, #667eea 0%, #48c6ef 100%);
}

/* Laranja para rosa */
body {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

/* Verde para azul escuro */
body {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

/* Escuro moderno */
body {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
}
```

### 2. Alterar Botões

```css
.btn-primary {
    background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
    border: none;
}
```

### 3. Alterar Logo/Título

**Ficheiro:** `resources/views/layouts/app.blade.php`

```html
<!-- Localizar -->
<a class="navbar-brand fw-bold" href="{{ route('gallery') }}">
    <i class="fas fa-images text-primary"></i> Sistema de Votação
</a>

<!-- Alterar para -->
<a class="navbar-brand fw-bold" href="{{ route('gallery') }}">
    <i class="fas fa-star text-warning"></i> Meu Concurso
</a>
```

### 4. Adicionar Logo Personalizado

```html
<a class="navbar-brand fw-bold" href="{{ route('gallery') }}">
    <img src="{{ asset('logo.png') }}" alt="Logo" height="30">
    Minha Marca
</a>
```

---

## 📝 Personalização de Textos

### 1. Alterar Mensagens da Galeria

**Ficheiro:** `resources/views/gallery.blade.php`

```html
<!-- Alterar título -->
<h1 class="text-white fw-bold">
    <i class="fas fa-trophy"></i> Concurso de Fotografia 2026
</h1>

<!-- Alterar descrição -->
<p class="text-white fs-5">Vote na sua foto favorita!</p>
```

### 2. Alterar Texto do Modal de Voto

```html
<div class="alert alert-info mb-0">
    <i class="fas fa-lightbulb"></i> 
    <strong>Importante:</strong> Você só pode votar uma vez. Escolha com sabedoria!
</div>
```

### 3. Alterar Mensagens de Sucesso/Erro

**Ficheiro:** `app/Http/Controllers/VoteController.php`

```php
// Sucesso
return back()->with('success', 'Voto registado! Muito obrigado pela participação 🎉');

// Erro
return back()->with('error', 'Ops! Este email já participou da votação.');
```

---

## ⚙️ Personalização Funcional

### 1. Limitar Número de Uploads por Admin

**Ficheiro:** `app/Http/Controllers/ImageController.php`

```php
public function upload(Request $request)
{
    // Adicionar limite
    $totalImages = Image::where('user_id', Auth::id())->count();
    if ($totalImages >= 10) {
        return back()->with('error', 'Limite de 10 imagens atingido!');
    }
    
    // ... resto do código
}
```

### 2. Permitir Múltiplos Votos por Email (Remover Restrição)

**Ficheiro:** `database/migrations/2026_02_09_000002_create_votes_table.php`

```php
// REMOVER esta linha:
$table->string('email')->unique();

// SUBSTITUIR por:
$table->string('email');

// E adicionar índice composto para evitar duplicatas na mesma imagem:
$table->unique(['email', 'image_id']);
```

**Nota:** Depois execute `php artisan migrate:fresh --seed`

### 3. Adicionar Campo de Nome ao Voto

**Criar nova migration:**
```bash
php artisan make:migration add_name_to_votes_table
```

```php
public function up()
{
    Schema::table('votes', function (Blueprint $table) {
        $table->string('name')->after('email');
    });
}
```

**Atualizar Model Vote:**
```php
protected $fillable = [
    'email',
    'name',
    'image_id',
];
```

**Atualizar View:**
```html
<div class="mb-3">
    <label for="name" class="form-label fw-bold">Seu Nome</label>
    <input type="text" class="form-control" name="name" required>
</div>
```

### 4. Permitir Descrição nas Imagens

**Criar migration:**
```bash
php artisan make:migration add_description_to_images_table
```

```php
public function up()
{
    Schema::table('images', function (Blueprint $table) {
        $table->text('description')->nullable()->after('path');
    });
}
```

**Atualizar formulário de upload:**
```html
<div class="mb-3">
    <label for="description">Descrição</label>
    <textarea name="description" class="form-control" rows="3"></textarea>
</div>
```

---

## 🔐 Personalização de Segurança

### 1. Alterar Credenciais do Admin Padrão

**Ficheiro:** `database/seeders/AdminSeeder.php`

```php
User::create([
    'name' => 'Seu Nome',
    'email' => 'seu@email.com',
    'password' => Hash::make('sua_senha_segura'),
    'email_verified_at' => now(),
]);
```

### 2. Aumentar Segurança da Senha

**Ficheiro:** `app/Http/Controllers/AdminController.php`

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'min:8'], // Mínimo 8 caracteres
    ]);
    
    // ... resto
}
```

### 3. Adicionar Limite de Tentativas de Login

**Usar throttle do Laravel:**

```php
Route::post('/admin/login', [AdminController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 tentativas por minuto
```

---

## 📊 Personalização de Estatísticas

### 1. Adicionar Gráfico de Votos por Data

**Instalar Chart.js:**

```html
<!-- No layout -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

**Adicionar canvas:**
```html
<canvas id="votesChart"></canvas>

<script>
const ctx = document.getElementById('votesChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($dates),
        datasets: [{
            label: 'Votos por Dia',
            data: @json($voteCounts),
            borderColor: 'rgb(75, 192, 192)',
        }]
    }
});
</script>
```

### 2. Exportar Estatísticas para Excel

**Instalar pacote:**
```bash
composer require maatwebsite/excel
```

**Adicionar botão:**
```html
<a href="{{ route('admin.export') }}" class="btn btn-success">
    <i class="fas fa-file-excel"></i> Exportar para Excel
</a>
```

---

## 🎭 Personalização de Imagens

### 1. Adicionar Watermark nas Imagens

**Instalar Intervention Image:**
```bash
composer require intervention/image
```

**No controller:**
```php
use Intervention\Image\Facades\Image;

public function upload(Request $request)
{
    // ... após salvar ficheiro
    
    $img = Image::make(public_path($path));
    $img->text('© Meu Concurso 2026', 10, 10);
    $img->save();
}
```

### 2. Redimensionar Imagens Automaticamente

```php
$img = Image::make($file);
$img->resize(1200, null, function ($constraint) {
    $constraint->aspectRatio();
    $constraint->upsize();
});
$img->save(public_path($path));
```

---

## 📧 Personalização de Notificações

### 1. Enviar Email ao Admin Quando Há Novo Voto

**Configurar email no .env:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu@email.com
MAIL_PASSWORD=sua_senha
MAIL_FROM_ADDRESS=seu@email.com
```

**Criar notification:**
```bash
php artisan make:notification NewVoteNotification
```

**No VoteController:**
```php
use App\Notifications\NewVoteNotification;

// Após registar voto
Auth::user()->notify(new NewVoteNotification($vote));
```

---

## 🌐 Personalização de Idioma

### 1. Trocar para Inglês

**Ficheiro:** `.env`
```env
APP_LOCALE=en
```

**Criar ficheiros de tradução:**
```php
// lang/en/messages.php
return [
    'vote_success' => 'Vote registered successfully!',
    'vote_error' => 'This email has already voted.',
];
```

**Usar nas views:**
```php
{{ __('messages.vote_success') }}
```

---

## 🚀 Otimizações de Performance

### 1. Adicionar Paginação na Galeria

**Controller:**
```php
$images = Image::withCount('votes')->latest()->paginate(12);
```

**View:**
```html
{{ $images->links() }}
```

### 2. Cache de Estatísticas

```php
$stats = Cache::remember('statistics', 3600, function () {
    return [
        'totalImages' => Image::count(),
        'totalVotes' => Vote::count(),
    ];
});
```

### 3. Lazy Loading de Imagens

```html
<img src="{{ asset($image->path) }}" loading="lazy" alt="...">
```

---

## 🔄 Recursos Adicionais

### 1. Adicionar Comentários nas Imagens

**Migration:**
```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('image_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->text('comment');
    $table->timestamps();
});
```

### 2. Sistema de Categorias

**Migration:**
```php
Schema::table('images', function (Blueprint $table) {
    $table->string('category')->nullable();
});
```

### 3. Galeria com Filtros

```html
<div class="mb-3">
    <button class="btn btn-sm btn-outline-primary" data-filter="all">Todas</button>
    <button class="btn btn-sm btn-outline-primary" data-filter="natureza">Natureza</button>
    <button class="btn btn-sm btn-outline-primary" data-filter="pessoas">Pessoas</button>
</div>
```

---

## 📱 Adicionar PWA (Progressive Web App)

**Criar manifest.json:**
```json
{
    "name": "Sistema de Votação",
    "short_name": "Votação",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#667eea",
    "theme_color": "#667eea",
    "icons": [{
        "src": "/icon.png",
        "sizes": "192x192",
        "type": "image/png"
    }]
}
```

---

## 🎯 Dicas Finais

1. **Faça backup** antes de fazer alterações grandes
2. **Teste localmente** antes de implementar em produção
3. **Use Git** para versionar suas mudanças
4. **Documente** personalizações importantes
5. **Otimize imagens** antes do upload

---

**Personalize à vontade e boa sorte com seu projeto! 🚀**
