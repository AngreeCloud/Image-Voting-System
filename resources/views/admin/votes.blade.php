@extends('layouts.app')

@section('title', 'Votos da Imagem')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-vote-yea"></i> Votos da Imagem
                        </h2>
                        <p class="text-muted mb-0">{{ $image->votes->count() }} voto(s) registado(s)</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.manage') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <a href="{{ route('admin.statistics') }}" class="btn btn-outline-primary">
                            <i class="fas fa-chart-bar"></i> Estatísticas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Detalhes da Imagem -->
    <div class="col-lg-4 mb-4">
        <div class="card sticky-top" style="top: 20px;">
            <img src="{{ $image->getImageUrl() }}" class="card-img-top" alt="{{ $image->filename }}" style="max-height: 300px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title">{{ $image->filename }}</h5>
                <hr>
                <p class="mb-2">
                    <i class="fas fa-user text-primary"></i> 
                    <strong>Carregada por:</strong><br>
                    {{ $image->user->name }}
                </p>
                <p class="mb-2">
                    <i class="far fa-calendar text-info"></i> 
                    <strong>Data:</strong><br>
                    {{ $image->created_at->format('d/m/Y H:i') }}
                </p>
                <p class="mb-2">
                    <i class="fas fa-clock text-muted"></i> 
                    <strong>Há:</strong><br>
                    {{ $image->created_at->diffForHumans() }}
                </p>
                <hr>
                <div class="text-center">
                    <h2 class="text-success mb-0">{{ $image->votes->count() }}</h2>
                    <p class="text-muted">{{ Str::plural('Voto', $image->votes->count()) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Votos -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> Lista de Votantes
                </h5>
                <button class="btn btn-light btn-sm" onclick="exportVotes()">
                    <i class="fas fa-download"></i> Exportar
                </button>
            </div>
            <div class="card-body p-0">
                @if($image->votes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="votesTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>
                                        <i class="fas fa-envelope"></i> Email
                                    </th>
                                    <th width="200">
                                        <i class="far fa-clock"></i> Data do Voto
                                    </th>
                                    <th width="150">
                                        <i class="fas fa-history"></i> Há
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($image->votes as $index => $vote)
                                    <tr>
                                        <td class="fw-bold">{{ $index + 1 }}</td>
                                        <td>
                                            <i class="fas fa-user-circle text-primary"></i>
                                            {{ $vote->email }}
                                        </td>
                                        <td>
                                            <small>{{ $vote->created_at->format('d/m/Y H:i:s') }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $vote->created_at->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5>Nenhum voto registado</h5>
                        <p class="text-muted">Esta imagem ainda não recebeu votos.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Estatísticas Detalhadas -->
        @if($image->votes->count() > 0)
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Estatísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h4 class="text-primary">{{ $image->votes->count() }}</h4>
                            <p class="text-muted mb-0">Total de Votos</p>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-success">{{ $image->votes->first()?->created_at->format('d/m/Y') ?? 'N/A' }}</h4>
                            <p class="text-muted mb-0">Último Voto</p>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-info">{{ $image->votes->last()?->created_at->format('d/m/Y') ?? 'N/A' }}</h4>
                            <p class="text-muted mb-0">Primeiro Voto</p>
                        </div>
                        <div class="col-md-3">
                            @php
                                $totalVotes = \App\Models\Vote::count();
                                $percentage = $totalVotes > 0 ? round(($image->votes->count() / $totalVotes) * 100, 1) : 0;
                            @endphp
                            <h4 class="text-warning">{{ $percentage }}%</h4>
                            <p class="text-muted mb-0">do Total</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function exportVotes() {
        // Criar CSV
        let csv = 'Nº,Email,Data do Voto,Timestamp\n';
        
        const table = document.getElementById('votesTable');
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach((row, index) => {
            const cells = row.querySelectorAll('td');
            const num = cells[0].textContent.trim();
            const email = cells[1].textContent.trim().replace('user-circle', '').trim();
            const date = cells[2].textContent.trim();
            
            csv += `${num},"${email}","${date}"\n`;
        });
        
        // Download
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', 'votos_imagem_{{ $image->id }}_{{ date("Y-m-d") }}.csv');
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection

@section('styles')
<style>
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.1);
    }
    
    @media (max-width: 991px) {
        .sticky-top {
            position: relative !important;
        }
    }
</style>
@endsection
