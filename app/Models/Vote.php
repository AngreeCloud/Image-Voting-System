<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'image_id',
    ];

    /**
     * Relação: Um voto pertence a uma imagem
     */
    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
