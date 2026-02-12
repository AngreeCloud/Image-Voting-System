@extends('layouts.app')

@section('title', 'Criar Admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus"></i> Criar Novo Admin
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    
                    <!-- Nome -->
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-user"></i> Nome Completo <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required
                            autofocus
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">O admin usará este email para fazer login</small>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password" 
                            required
                            minlength="8"
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Mínimo 8 caracteres</small>
                    </div>

                    <!-- Confirmar Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock"></i> Confirmar Password <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required
                            minlength="8"
                        >
                    </div>

                    <hr>

                    <h5 class="mb-3">
                        <i class="fas fa-cog"></i> Permissões
                    </h5>
                    <p class="text-muted small">Configure as permissões que este admin terá. Upload de imagens é sempre permitido.</p>

                    <!-- Permissões -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="can_view_votes" 
                                    name="can_view_votes"
                                    value="1"
                                    {{ old('can_view_votes') ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="can_view_votes">
                                    <i class="fas fa-eye text-primary"></i> <strong>Ver Emails dos Votos</strong>
                                    <br>
                                    <small class="text-muted">Permitir visualizar os emails das pessoas que votaram em cada imagem</small>
                                </label>
                            </div>

                            <div class="form-check form-switch">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="can_view_statistics" 
                                    name="can_view_statistics"
                                    value="1"
                                    {{ old('can_view_statistics') ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="can_view_statistics">
                                    <i class="fas fa-chart-bar text-success"></i> <strong>Ver Estatísticas Gerais</strong>
                                    <br>
                                    <small class="text-muted">Permitir acesso à página de estatísticas com análise completa dos votos</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Permissão sempre ativa -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Nota:</strong> Todos os admins podem sempre fazer <strong>upload de imagens</strong> e <strong>gerir suas próprias imagens</strong>.
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Criar Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .form-check-input {
        width: 3em;
        height: 1.5em;
        cursor: pointer;
    }

    .form-check-label {
        cursor: pointer;
        margin-left: 10px;
    }
</style>
@endsection
