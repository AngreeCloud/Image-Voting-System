@extends('layouts.app')

@section('title', 'Admin Dashboard - Upload')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-tachometer-alt"></i> Painel Admin
                        </h2>
                        <p class="text-muted mb-0">Bem-vindo, {{ Auth::user()->name }}!</p>
                    </div>
                    <a href="{{ route('admin.statistics') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar"></i> Ver Estatísticas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-cloud-upload-alt"></i> Upload de Imagem
                </h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="image" class="form-label fw-bold">
                            <i class="fas fa-image"></i> Selecionar Imagem
                        </label>
                        <input 
                            type="file" 
                            class="form-control form-control-lg @error('image') is-invalid @enderror" 
                            id="image" 
                            name="image"
                            accept="image/*"
                            required
                            onchange="previewImage(event)"
                        >
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Formatos aceites: JPEG, PNG, JPG, GIF, WEBP (máx. 10MB)
                        </small>
                    </div>

                    <!-- Preview da imagem -->
                    <div id="imagePreview" class="mb-4 text-center d-none">
                        <p class="fw-bold text-muted mb-2">Pré-visualização:</p>
                        <img id="preview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 300px;">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-upload"></i> Fazer Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Instruções -->
<div class="row mt-4">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-info-circle text-info"></i> Informações
                </h5>
                <ul class="mb-0">
                    <li>As imagens são guardadas na pasta <code>public/uploads</code></li>
                    <li>Cada imagem fica disponível para votação pública</li>
                    <li>Pode ver estatísticas de votos no painel de estatísticas</li>
                    <li>Os utilizadores só podem votar uma vez (por email)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');
            preview.src = reader.result;
            previewContainer.classList.remove('d-none');
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
