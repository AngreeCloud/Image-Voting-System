@extends('layouts.app')

@section('title', 'Galeria de Imagens - Vote!')

@section('content')
<div class="row">
    <div class="col-12 text-center mb-4">
        <h1 class="text-white fw-bold">
            <i class="fas fa-trophy"></i> Galeria de Imagens
        </h1>
        <p class="text-white fs-5">Clique na sua imagem favorita para votar!</p>
    </div>
</div>

@if($images->count() > 0)
    <div class="row g-4">
        @foreach($images as $image)
            <div class="col-md-4 col-lg-3">
                <div class="card image-card h-100" data-bs-toggle="modal" data-bs-target="#voteModal" onclick="selectImage({{ $image->id }})">
                    <img src="{{ $image->getImageUrl() }}" class="card-img-top" alt="{{ $image->filename }}">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">
                                <i class="fas fa-heart"></i> {{ $image->votes_count }} votos
                            </span>
                            <small class="text-muted">
                                <i class="far fa-clock"></i> {{ $image->created_at->diffForHumans() }}
                            </small>
                        </div>
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
                    <i class="fas fa-images fa-4x text-muted mb-3"></i>
                    <h3>Nenhuma imagem disponível</h3>
                    <p class="text-muted">Ainda não há imagens para votar.</p>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Modal de Votação -->
<div class="modal fade" id="voteModal" tabindex="-1" aria-labelledby="voteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="voteModalLabel">
                    <i class="fas fa-vote-yea"></i> Confirmar Voto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vote') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="image_id" id="selectedImageId">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">
                            <i class="fas fa-envelope"></i> Seu Email
                        </label>
                        <input 
                            type="email" 
                            class="form-control form-control-lg" 
                            id="email" 
                            name="email" 
                            placeholder="seuemail@exemplo.com"
                            required
                        >
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Cada email só pode votar uma vez.
                        </small>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-lightbulb"></i> 
                        <strong>Dica:</strong> Escolha bem! Você só pode votar uma vez.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Confirmar Voto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Remoção -->
<div class="modal fade" id="removeVoteModal" tabindex="-1" aria-labelledby="removeVoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="removeVoteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Voto Existente Detectado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vote') }}" method="POST">
                @csrf
                <input type="hidden" name="confirm_remove" value="1">
                <input type="hidden" name="email" id="removeEmail" value="{{ session('existing_email') }}">
                <input type="hidden" name="image_id" value="{{ session('new_image_id') }}">
                
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        <strong>O email <span class="text-primary">{{ session('existing_email') }}</span> já votou anteriormente.</strong>
                    </div>
                    
                    <p class="mb-3">
                        <i class="fas fa-question-circle"></i> <strong>Deseja remover o seu voto anterior?</strong>
                    </p>
                    
                    <p class="text-muted mb-0">
                        Após remover, você terá um voto livre e poderá votar novamente em qualquer imagem.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Não, Manter Voto
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Sim, Remover Voto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectImage(imageId) {
        document.getElementById('selectedImageId').value = imageId;
    }
    
    // Se houver pedido para confirmar remoção, mostrar modal
    @if(session('ask_remove'))
        document.addEventListener('DOMContentLoaded', function() {
            var removeModal = new bootstrap.Modal(document.getElementById('removeVoteModal'));
            removeModal.show();
        });
    @endif
</script>
@endsection
