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
        // Criar utilizador owner (ou pular se já existir)
        $owner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner',
                'role' => 'owner',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'can_view_votes' => true,
                'can_view_statistics' => true,
            ]
        );

        if ($owner->wasRecentlyCreated) {
            $this->command->info('✓ Owner criado com sucesso!');
            $this->command->info('  Email: owner@example.com');
            $this->command->info('  Password: password');
            $this->command->info('  Role: Owner (acesso total)');
        } else {
            $this->command->info('✓ Owner já existe - pulando...');
        }
    }
}
