@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h2 class="fw-bold">Admin Login</h2>
                    <p class="text-muted">Acesso restrito a administradores</p>
                </div>

                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input 
                            type="email" 
                            class="form-control form-control-lg @error('email') is-invalid @enderror" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            placeholder="admin@exemplo.com"
                            required 
                            autofocus
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input 
                            type="password" 
                            class="form-control form-control-lg @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password"
                            placeholder="••••••••"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="remember" 
                                id="remember"
                            >
                            <label class="form-check-label" for="remember">
                                Lembrar-me
                            </label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Entrar
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('gallery') }}" class="text-muted">
                        <i class="fas fa-arrow-left"></i> Voltar para a galeria
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
