<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'filename',
        'path',
        'user_id',
    ];

    /**
     * Relação: Uma imagem pertence a um utilizador (admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação: Uma imagem pode ter vários votos
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Obter contagem de votos para esta imagem
     */
    public function votesCount()
    {
        return $this->votes()->count();
    }

    /**
     * Obter URL completa da imagem
     * (funciona com URLs externas do ImgBB ou paths locais)
     */
    public function getImageUrl()
    {
        // Se o path começa com http:// ou https://, é uma URL completa (ImgBB, S3, etc)
        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
            return $this->path;
        }
        
        // Caso contrário, é um path local - usar asset()
        return asset($this->path);
    }
}
