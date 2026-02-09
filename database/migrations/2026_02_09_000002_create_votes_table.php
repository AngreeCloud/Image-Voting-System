<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique(); // Email único - cada email só pode votar uma vez
            $table->foreignId('image_id')->constrained()->onDelete('cascade'); // Imagem votada
            $table->timestamps();
            
            // Índice para acelerar buscas
            $table->index('email');
            $table->index('image_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
