@extends('layouts.app')

@section('title', 'Gerir Imagens')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-images"></i> Gerir Imagens
                        </h2>
                        <p class="text-muted mb-0">Remova imagens indesejadas</p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if($images->count() > 0)
    <div class="row g-4">
        @foreach($images as $image)
            <div class="col-md-4 col-lg-3">
                <div class="card h-100">
                    <img src="{{ asset($image->path) }}" class="card-img-top" alt="{{ $image->filename }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h6 class="card-title text-truncate" title="{{ $image->filename }}">
                            {{ $image->filename }}
                        </h6>
                        <div class="mb-3">
                            <small class="text-muted d-block">
                                <i class="fas fa-user"></i> {{ $image->user->name }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="far fa-calendar"></i> {{ $image->created_at->format('d/m/Y H:i') }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-heart text-danger"></i> {{ $image->votes_count }} votos
                            </small>
                        </div>
                        
                        <!-- Ver Votos (apenas se tiver permissão) -->
                        @if(Auth::user()->canViewVotes())
                            <a href="{{ route('admin.images.votes', $image->id) }}" class="btn btn-info btn-sm w-100 mb-2">
                                <i class="fas fa-eye"></i> Ver Votos ({{ $image->votes_count }})
                            </a>
                        @endif
                        
                        <!-- Remover Imagem -->
                        @if($image->user_id === Auth::id())
                            <form action="{{ route('admin.images.delete', $image->id) }}" method="POST" 
                                  onsubmit="return confirm('Tem certeza que deseja remover esta imagem? Esta ação não pode ser desfeita!');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="fas fa-trash"></i> Remover
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary btn-sm w-100" disabled>
                                <i class="fas fa-lock"></i> Apenas o autor pode remover
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="card text-center py-5">
                <div class="card-body">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h4>Nenhuma imagem disponível</h4>
                    <p class="text-muted">Faça upload da primeira imagem no dashboard.</p>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Fazer Upload
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Estatísticas -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h3 class="text-primary">{{ $images->count() }}</h3>
                        <p class="text-muted mb-0">Total de Imagens</p>
                    </div>
                    <div class="col-md-4">
                        <h3 class="text-success">{{ $images->where('user_id', Auth::id())->count() }}</h3>
                        <p class="text-muted mb-0">Minhas Imagens</p>
                    </div>
                    <div class="col-md-4">
                        <h3 class="text-info">{{ $images->sum('votes_count') }}</h3>
                        <p class="text-muted mb-0">Total de Votos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card-img-top {
        transition: transform 0.3s ease;
    }
    
    .card:hover .card-img-top {
        transform: scale(1.05);
    }
    
    .card {
        transition: box-shadow 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
</style>
@endsection
