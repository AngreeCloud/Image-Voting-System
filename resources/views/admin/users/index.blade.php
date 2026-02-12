@extends('layouts.app')

@section('title', 'Gerir Admins')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-users-cog"></i> Gerir Admins
                        </h2>
                        <p class="text-muted mb-0">Criar e configurar permissões de utilizadores</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Criar Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($admins->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Lista de Administradores
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th width="120" class="text-center">
                                        <i class="fas fa-eye"></i><br>
                                        <small>Ver Votos</small>
                                    </th>
                                    <th width="120" class="text-center">
                                        <i class="fas fa-chart-bar"></i><br>
                                        <small>Estatísticas</small>
                                    </th>
                                    <th width="100" class="text-center">
                                        <i class="fas fa-images"></i><br>
                                        <small>Imagens</small>
                                    </th>
                                    <th width="150" class="text-center">Criado em</th>
                                    <th width="180" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admins as $index => $admin)
                                    <tr>
                                        <td class="fw-bold">{{ $index + 1 }}</td>
                                        <td>
                                            <i class="fas fa-user-circle text-primary"></i>
                                            {{ $admin->name }}
                                        </td>
                                        <td>{{ $admin->email }}</td>
                                        <td class="text-center">
                                            @if($admin->can_view_votes)
                                                <i class="fas fa-check-circle text-success fa-lg" title="Permitido"></i>
                                            @else
                                                <i class="fas fa-times-circle text-danger fa-lg" title="Negado"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($admin->can_view_statistics)
                                                <i class="fas fa-check-circle text-success fa-lg" title="Permitido"></i>
                                            @else
                                                <i class="fas fa-times-circle text-danger fa-lg" title="Negado"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $admin->images_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <small>{{ $admin->created_at->format('d/m/Y') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.users.edit', $admin->id) }}" 
                                               class="btn btn-warning btn-sm me-1" 
                                               title="Editar permissões">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $admin->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Tem certeza que deseja remover este admin?\n\nNota: Apenas admins sem imagens podem ser removidos.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-sm"
                                                        title="Remover admin"
                                                        @if($admin->images_count > 0) disabled @endif>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">{{ $admins->count() }}</h3>
                    <p class="text-muted mb-0">Total de Admins</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h3 class="fw-bold">{{ $admins->where('can_view_votes', true)->count() }}</h3>
                    <p class="text-muted mb-0">Com Acesso a Votos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                    <h3 class="fw-bold">{{ $admins->where('can_view_statistics', true)->count() }}</h3>
                    <p class="text-muted mb-0">Com Acesso a Estatísticas</p>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="card text-center py-5">
                <div class="card-body">
                    <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                    <h4>Nenhum admin criado ainda</h4>
                    <p class="text-muted">Crie o primeiro admin para começar.</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Criar Primeiro Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('styles')
<style>
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
    }

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.4;
    }
</style>
@endsection
