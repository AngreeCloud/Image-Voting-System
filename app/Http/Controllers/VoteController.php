<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    /**
     * Mostrar galeria pública com imagens
     */
    public function index()
    {
        // Carregar todas as imagens com contagem de votos
        $images = Image::withCount('votes')
            ->latest()
            ->get();
        
        return view('gallery', compact('images'));
    }

    /**
     * Processar voto
     */
    public function vote(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'image_id' => 'required|exists:images,id',
        ]);

        $email = $request->email;
        $imageId = $request->image_id;
        $confirmRemove = $request->boolean('confirm_remove');

        // Verificar se o email já votou
        $existingVote = Vote::where('email', $email)->first();

        if ($existingVote) {
            // Se o utilizador confirmou a remoção do voto
            if ($confirmRemove) {
                // Remover o voto anterior
                $existingVote->delete();
                
                return back()->with([
                    'success' => 'Voto removido com sucesso! O email ' . $email . ' tem agora um voto livre.',
                    'vote_removed' => true,
                    'email' => $email,
                ]);
            }
            
            // Perguntar se quer remover o voto existente
            return back()->with([
                'ask_remove' => true,
                'existing_email' => $email,
                'new_image_id' => $imageId,
            ]);
        }

        // Verificar se a imagem existe
        $image = Image::find($imageId);
        if (!$image) {
            return back()->with('error', 'Imagem não encontrada.');
        }

        // Registar o voto
        try {
            Vote::create([
                'email' => $email,
                'image_id' => $imageId,
            ]);

            return back()->with('success', 'Voto registado com sucesso! Obrigado por participar.');
        } catch (\Exception $e) {
            // Se houver erro de email duplicado (por concorrência)
            if ($e->getCode() == 23000) {
                return back()->with('error', 'Este email já votou.');
            }
            
            return back()->with('error', 'Erro ao registar voto. Tente novamente.');
        }
    }
}
