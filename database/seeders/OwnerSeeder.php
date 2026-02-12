<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar utilizador owner
        User::create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'role' => 'owner',
            'password' => Hash::make('password'), // Password: password
            'email_verified_at' => now(),
            'can_view_votes' => true, // Owner tem todas as permissões
            'can_view_statistics' => true,
        ]);

        $this->command->info('✓ Owner criado com sucesso!');
        $this->command->info('  Email: owner@example.com');
        $this->command->info('  Password: password');
        $this->command->info('  Role: Owner (acesso total)');
    }
}
