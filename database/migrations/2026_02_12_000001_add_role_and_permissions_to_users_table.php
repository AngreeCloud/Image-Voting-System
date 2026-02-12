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
        Schema::table('users', function (Blueprint $table) {
            // Adicionar role (owner ou admin)
            $table->enum('role', ['owner', 'admin'])->default('admin')->after('email');
            
            // Permissões específicas para admins
            $table->boolean('can_view_votes')->default(false)->after('role');
            $table->boolean('can_view_statistics')->default(false)->after('can_view_votes');
            
            // Upload é sempre permitido para todos (owner e admin)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'can_view_votes', 'can_view_statistics']);
        });
    }
};
