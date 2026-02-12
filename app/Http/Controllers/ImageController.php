<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
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
            $filename = $file->getClientOriginalName();
            
            // Decidir onde fazer upload baseado no ambiente
            if ($this->shouldUseExternalStorage()) {
                // Produção: Usar ImgBB (storage externo)
                try {
                    $imageUrl = $this->uploadToImgBB($file);
                    
                    Image::create([
                        'filename' => $filename,
                        'path' => $imageUrl, // URL completa do ImgBB
                        'user_id' => Auth::id(),
                    ]);
                    
                    return back()->with('success', 'Imagem carregada com sucesso no ImgBB!');
                    
                } catch (\Exception $e) {
                    return back()->with('error', 'Erro ao carregar: ' . $e->getMessage());
                }
            } else {
                // Desenvolvimento: Usar storage local
                $uniqueFilename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $uniqueFilename);
                
                Image::create([
                    'filename' => $filename,
                    'path' => 'uploads/' . $uniqueFilename, // Path local
                    'user_id' => Auth::id(),
                ]);
                
                return back()->with('success', 'Imagem carregada com sucesso (storage local)!');
            }
        }
        
        return back()->with('error', 'Erro ao carregar imagem.');
    }

    /**
     * Verificar se deve usar storage externo (ImgBB)
     * Usa ImgBB se estiver em produção E tiver a API key configurada
     */
    private function shouldUseExternalStorage()
    {
        return config('app.env') === 'production' && !empty(config('services.imgbb.api_key'));
    }

    /**
     * Fazer upload para ImgBB
     */
    private function uploadToImgBB($file)
    {
        $apiKey = config('services.imgbb.api_key');
        
        if (empty($apiKey)) {
            throw new \Exception('IMGBB_API_KEY não configurada');
        }

        // Log do tamanho do arquivo
        $fileSize = $file->getSize();
        \Log::info("Tentando upload ImgBB", [
            'filename' => $file->getClientOriginalName(),
            'size_mb' => round($fileSize / 1024 / 1024, 2),
        ]);

        try {
            $startTime = microtime(true);
            
            $response = Http::timeout(120) // 2 minutos para arquivos grandes
                ->retry(2, 1000) // 2 tentativas com 1s de intervalo
                ->asMultipart()
                ->post('https://api.imgbb.com/1/upload', [
                    [
                        'name' => 'key',
                        'contents' => $apiKey
                    ],
                    [
                        'name' => 'image',
                        'contents' => fopen($file->getRealPath(), 'r'),
                        'filename' => $file->getClientOriginalName()
                    ]
                ]);

            $duration = round(microtime(true) - $startTime, 2);
            \Log::info("Upload ImgBB completo em {$duration}s");

            if (!$response->successful()) {
                $errorMsg = $response->json()['error']['message'] ?? $response->body();
                \Log::error("ImgBB erro", ['response' => $errorMsg]);
                throw new \Exception('ImgBB recusou: ' . $errorMsg);
            }

            $data = $response->json();
            
            if (!isset($data['data']['url'])) {
                throw new \Exception('Resposta inválida do ImgBB');
            }

            return $data['data']['url'];
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error("ImgBB timeout", [
                'message' => $e->getMessage(),
                'file_size_mb' => round($fileSize / 1024 / 1024, 2)
            ]);
            
            throw new \Exception('Falha ao enviar para ImgBB. Possíveis causas: arquivo muito grande (' . round($fileSize / 1024 / 1024, 1) . 'MB), conexão lenta ou firewall bloqueando.');
        } catch (\Exception $e) {
            \Log::error("ImgBB erro geral", ['error' => $e->getMessage()]);
            throw $e;
        }
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
        
        // Remover ficheiro físico APENAS se for storage local (não URL externa)
        if (!str_starts_with($image->path, 'http://') && !str_starts_with($image->path, 'https://')) {
            $filePath = public_path($image->path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
        // Se for URL externa (ImgBB, S3, etc), não fazemos nada - fica lá permanentemente
        
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
