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

        // Verificar se o email já votou
        $existingVote = Vote::where('email', $email)->first();

        if ($existingVote) {
            return back()->with('error', 'Este email já votou. Cada email só pode votar uma vez!');
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
