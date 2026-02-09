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
                    <div>
                        <a href="{{ route('admin.manage') }}" class="btn btn-outline-danger me-2">
                            <i class="fas fa-trash"></i> Gerir Imagens
                        </a>
                        <a href="{{ route('admin.statistics') }}" class="btn btn-outline-primary">
                            <i class="fas fa-chart-bar"></i> Ver Estatísticas
                        </a>
                    </div>
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
                    
                    <!-- Configurações de Upload -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="max_size" class="form-label fw-bold">
                                <i class="fas fa-weight-hanging"></i> Tamanho Máximo (KB)
                            </label>
                            <select class="form-select" id="max_size" name="max_size">
                                <option value="2048">2 MB (2048 KB)</option>
                                <option value="5120">5 MB (5120 KB)</option>
                                <option value="10240" selected>10 MB (10240 KB)</option>
                                <option value="20480">20 MB (20480 KB)</option>
                                <option value="51200">50 MB (51200 KB)</option>
                            </select>
                            <small class="text-muted">Tamanho máximo permitido para upload</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="allowed_types" class="form-label fw-bold">
                                <i class="fas fa-file-image"></i> Tipos Permitidos
                            </label>
                            <select class="form-select" id="allowed_types" name="allowed_types">
                                <option value="jpeg,jpg,png,gif,webp" selected>Todos (JPEG, PNG, GIF, WEBP)</option>
                                <option value="jpeg,jpg,png">Apenas JPEG e PNG</option>
                                <option value="jpeg,jpg">Apenas JPEG</option>
                                <option value="png">Apenas PNG</option>
                                <option value="gif">Apenas GIF</option>
                                <option value="webp">Apenas WEBP</option>
                            </select>
                            <small class="text-muted">Formatos de imagem aceites</small>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
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
                        <small class="text-muted" id="fileInfo">
                            <i class="fas fa-info-circle"></i> 
                            Selecione um ficheiro conforme as restrições acima
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
        const file = event.target.files[0];
        const fileInfo = document.getElementById('fileInfo');
        
        if (file) {
            // Mostrar informações do ficheiro
            const sizeMB = (file.size / 1024 / 1024).toFixed(2);
            const sizeKB = (file.size / 1024).toFixed(2);
            const maxSizeKB = document.getElementById('max_size').value;
            const maxSizeMB = (maxSizeKB / 1024).toFixed(2);
            
            let infoText = `<i class="fas fa-file-image"></i> ${file.name} - ${sizeMB} MB (${sizeKB} KB)`;
            
            if (file.size / 1024 > maxSizeKB) {
                fileInfo.innerHTML = `<i class="fas fa-exclamation-triangle text-danger"></i> 
                    <span class="text-danger">Ficheiro muito grande! Tamanho: ${sizeMB} MB, Máximo permitido: ${maxSizeMB} MB</span>`;
                fileInfo.classList.add('text-danger');
            } else {
                fileInfo.innerHTML = `<i class="fas fa-check-circle text-success"></i> ${infoText}`;
                fileInfo.classList.remove('text-danger');
                fileInfo.classList.add('text-success');
            }
            
            // Preview
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('preview');
                const previewContainer = document.getElementById('imagePreview');
                preview.src = reader.result;
                previewContainer.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    }
    
    // Atualizar info quando alterar tamanho máximo
    document.getElementById('max_size').addEventListener('change', function() {
        const fileInput = document.getElementById('image');
        if (fileInput.files.length > 0) {
            previewImage({ target: fileInput });
        }
    });
</script>
@endsection
