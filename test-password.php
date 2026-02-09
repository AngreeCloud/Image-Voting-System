<?php

use Illuminate\Support\Facades\Hash;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = User::where('email', 'admin@example.com')->first();

if (!$user) {
    echo "ERRO: Utilizador admin@example.com não encontrado!\n";
    exit(1);
}

echo "Utilizador encontrado:\n";
echo "  ID: {$user->id}\n";
echo "  Nome: {$user->name}\n";
echo "  Email: {$user->email}\n";
echo "  Password Hash: " . substr($user->password, 0, 20) . "...\n\n";

$testPassword = 'password';

echo "Testando password '{$testPassword}'...\n";
if (Hash::check($testPassword, $user->password)) {
    echo "✓ SUCCESS: A password 'password' corresponde ao hash!\n";
} else {
    echo "✗ ERRO: A password 'password' NÃO corresponde ao hash!\n";
    echo "\nVou resetar a password...\n";
    $user->password = Hash::make('password');
    $user->save();
    echo "✓ Password resetada para 'password'\n";
}
