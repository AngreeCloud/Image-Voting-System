<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Upload de imagem (apenas admin autenticado)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Gerar nome único para o ficheiro
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Mover para pasta public/uploads
            $file->move(public_path('uploads'), $filename);
            
            // Caminho relativo
            $path = 'uploads/' . $filename;
            
            // Guardar na base de dados
            Image::create([
                'filename' => $filename,
                'path' => $path,
                'user_id' => Auth::id(),
            ]);
            
            return back()->with('success', 'Imagem carregada com sucesso!');
        }
        
        return back()->with('error', 'Erro ao carregar imagem.');
    }

    /**
     * Mostrar estatísticas de votos (apenas admin)
     */
    public function statistics()
    {
        // Carregar todas as imagens com contagem de votos
        $images = Image::withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->with('user')
            ->get();
        
        $totalVotes = \App\Models\Vote::count();
        
        return view('admin.statistics', compact('images', 'totalVotes'));
    }
}
