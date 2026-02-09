<?php

use Illuminate\Support\Facades\Hash;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Criar novo admin com email em português
try {
    $user = User::create([
        'name' => 'Administrador',
        'email' => 'admin@exemplo.com',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
    ]);
    
    echo "✓ Utilizador criado com sucesso!\n";
    echo "  Email: admin@exemplo.com\n";
    echo "  Password: password\n";
} catch (\Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        echo "✓ Utilizador admin@exemplo.com já existe!\n";
        echo "  Password: password\n";
    } else {
        echo "✗ Erro: " . $e->getMessage() . "\n";
    }
}

echo "\nUtilizadores na base de dados:\n";
User::all(['email'])->each(function($u) {
    echo "  - {$u->email}\n";
});
