<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Upload de imagem (apenas admin autenticado)
     */
    public function upload(Request $request)
    {
        // Obter configurações (pode vir de um form ou settings)
        $maxSize = $request->input('max_size', 10240); // KB, padrão 10MB
        $allowedTypes = $request->input('allowed_types', 'jpeg,png,jpg,gif,webp');
        
        $request->validate([
            'image' => "required|image|mimes:{$allowedTypes}|max:{$maxSize}",
            'max_size' => 'nullable|integer|min:100|max:51200', // 100KB a 50MB
            'allowed_types' => 'nullable|string',
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
     * Listar todas as imagens para gestão (apenas admin)
     */
    public function manage()
    {
        $images = Image::withCount('votes')
            ->with('user')
            ->latest()
            ->get();
        
        return view('admin.manage', compact('images'));
    }

    /**
     * Ver votos de uma imagem (apenas admin)
     */
    public function viewVotes($id)
    {
        // Verificar permissão
        if (!Auth::user()->canViewVotes()) {
            abort(403, 'Você não tem permissão para visualizar os votos.');
        }

        $image = Image::with(['votes' => function($query) {
            $query->latest();
        }, 'user'])->findOrFail($id);
        
        return view('admin.votes', compact('image'));
    }

    /**
     * Remover imagem (apenas admin)
     */
    public function delete($id)
    {
        $image = Image::findOrFail($id);
        
        // Verificar se o admin é o dono da imagem ou se é super admin
        if ($image->user_id !== Auth::id()) {
            return back()->with('error', 'Você não tem permissão para remover esta imagem.');
        }
        
        // Remover ficheiro físico
        $filePath = public_path($image->path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
        
        // Remover da base de dados (votos serão removidos automaticamente por cascade)
        $image->delete();
        
        return back()->with('success', 'Imagem removida com sucesso!');
    }

    /**
     * Mostrar estatísticas de votos (apenas admin)
     */
    public function statistics()
    {
        // Verificar permissão
        if (!Auth::user()->canViewStatistics()) {
            abort(403, 'Você não tem permissão para visualizar as estatísticas.');
        }

        // Carregar todas as imagens com contagem de votos
        $images = Image::withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->with('user')
            ->get();
        
        $totalVotes = \App\Models\Vote::count();
        
        return view('admin.statistics', compact('images', 'totalVotes'));
    }
}
