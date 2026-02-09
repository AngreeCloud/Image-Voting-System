@extends('layouts.app')

@section('title', 'Estatísticas de Votos')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-chart-bar"></i> Estatísticas de Votos
                        </h2>
                        <p class="text-muted mb-0">Análise detalhada dos votos</p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumo Geral -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-images fa-3x text-primary mb-3"></i>
                <h3 class="fw-bold">{{ $images->count() }}</h3>
                <p class="text-muted mb-0">Total de Imagens</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-vote-yea fa-3x text-success mb-3"></i>
                <h3 class="fw-bold">{{ $totalVotes }}</h3>
                <p class="text-muted mb-0">Total de Votos</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-percentage fa-3x text-info mb-3"></i>
                <h3 class="fw-bold">
                    @if($images->count() > 0)
                        {{ number_format($totalVotes / $images->count(), 1) }}
                    @else
                        0
                    @endif
                </h3>
                <p class="text-muted mb-0">Média de Votos</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Estatísticas -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-list"></i> Detalhes por Imagem
                </h4>
            </div>
            <div class="card-body p-0">
                @if($images->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">#</th>
                                    <th width="150">Imagem</th>
                                    <th>Nome do Ficheiro</th>
                                    <th>Carregada por</th>
                                    <th>Data</th>
                                    <th width="120" class="text-center">
                                        <i class="fas fa-heart"></i> Votos
                                    </th>
                                    <th width="120" class="text-center">% Votos</th>
                                    <th width="150" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($images as $index => $image)
                                    <tr>
                                        <td class="fw-bold">{{ $index + 1 }}</td>
                                        <td>
                                            <img 
                                                src="{{ asset($image->path) }}" 
                                                alt="{{ $image->filename }}"
                                                class="img-thumbnail"
                                                style="width: 100px; height: 80px; object-fit: cover;"
                                            >
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $image->filename }}</small>
                                        </td>
                                        <td>
                                            <i class="fas fa-user"></i> {{ $image->user->name }}
                                        </td>
                                        <td>
                                            <small>
                                                <i class="far fa-calendar"></i> {{ $image->created_at->format('d/m/Y H:i') }}
                                                <br>
                                                <span class="text-muted">{{ $image->created_at->diffForHumans() }}</span>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary fs-6">
                                                {{ $image->votes_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($totalVotes > 0)
                                                <div class="progress" style="height: 25px;">
                                                    <div 
                                                        class="progress-bar bg-success" 
                                                        role="progressbar" 
                                                        style="width: {{ ($image->votes_count / $totalVotes) * 100 }}%"
                                                        aria-valuenow="{{ ($image->votes_count / $totalVotes) * 100 }}" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100"
                                                    >
                                                        {{ number_format(($image->votes_count / $totalVotes) * 100, 1) }}%
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">0%</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a 
                                                href="{{ route('admin.images.votes', $image->id) }}" 
                                                class="btn btn-info btn-sm"
                                                title="Ver emails dos votantes"
                                            >
                                                <i class="fas fa-eye"></i> Ver Votos
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h4>Nenhuma imagem disponível</h4>
                        <p class="text-muted">Faça upload da primeira imagem no dashboard.</p>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Fazer Upload
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Imagem mais votada -->
@if($images->count() > 0 && $totalVotes > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-trophy"></i> Imagem Mais Votada
                    </h4>
                </div>
                <div class="card-body text-center">
                    @php
                        $topImage = $images->first();
                    @endphp
                    <img 
                        src="{{ asset($topImage->path) }}" 
                        alt="{{ $topImage->filename }}"
                        class="img-fluid rounded mb-3"
                        style="max-height: 400px;"
                    >
                    <h3>{{ $topImage->votes_count }} votos ({{ number_format(($topImage->votes_count / $totalVotes) * 100, 1) }}%)</h3>
                    <p class="text-muted">Carregada por {{ $topImage->user->name }} em {{ $topImage->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
