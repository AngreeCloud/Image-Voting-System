<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

// ROTA DE TESTE - REMOVER DEPOIS
Route::get('/test-auth', function () {
    $user = \App\Models\User::where('email', 'admin@example.com')->first();
    if (!$user) {
        return 'Utilizador não encontrado!';
    }
    
    $passwordCheck = \Illuminate\Support\Facades\Hash::check('password', $user->password);
    
    $attemptResult = \Illuminate\Support\Facades\Auth::attempt([
        'email' => 'admin@example.com',
        'password' => 'password'
    ]);
    
    return [
        'user_exists' => $user ? 'SIM' : 'NÃO',
        'user_email' => $user->email,
        'password_hash_check' => $passwordCheck ? 'CORRETO' : 'ERRADO',
        'auth_attempt' => $attemptResult ? 'SUCESSO' : 'FALHOU',
        'auth_check_after' => \Illuminate\Support\Facades\Auth::check() ? 'AUTENTICADO' : 'NÃO AUTENTICADO',
        'session_driver' => config('session.driver'),
    ];
});

// Página principal - Galeria de imagens (público)
Route::get('/', [VoteController::class, 'index'])->name('gallery');

// Processar voto (público)
Route::post('/vote', [VoteController::class, 'vote'])->name('vote');

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação Admin
|--------------------------------------------------------------------------
*/

// Login
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rotas Admin (Protegidas por autenticação)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->group(function () {
    // Logout
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    
    // Dashboard - Página de upload
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Upload de imagem
    Route::post('/upload', [ImageController::class, 'upload'])->name('admin.upload');
    
    // Estatísticas
    Route::get('/statistics', [ImageController::class, 'statistics'])->name('admin.statistics');
});
