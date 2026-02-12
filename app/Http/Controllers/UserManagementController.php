<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Listar todos os admins (apenas owner)
     */
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->withCount('images')
            ->latest()
            ->get();
        
        return view('admin.users.index', compact('admins'));
    }

    /**
     * Mostrar formulário de criar admin (apenas owner)
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Criar novo admin (apenas owner)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'can_view_votes' => ['boolean'],
            'can_view_statistics' => ['boolean'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'can_view_votes' => $request->boolean('can_view_votes'),
            'can_view_statistics' => $request->boolean('can_view_statistics'),
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin criado com sucesso!');
    }

    /**
     * Mostrar formulário de editar permissões (apenas owner)
     */
    public function edit(User $user)
    {
        // Não permitir editar owners
        if ($user->isOwner()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Não é possível editar um owner.');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Atualizar permissões do admin (apenas owner)
     */
    public function update(Request $request, User $user)
    {
        // Não permitir editar owners
        if ($user->isOwner()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Não é possível editar um owner.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'can_view_votes' => ['boolean'],
            'can_view_statistics' => ['boolean'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'can_view_votes' => $request->boolean('can_view_votes'),
            'can_view_statistics' => $request->boolean('can_view_statistics'),
        ]);

        // Atualizar password se fornecida
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Permissões atualizadas com sucesso!');
    }

    /**
     * Remover admin (apenas owner)
     */
    public function destroy(User $user)
    {
        // Não permitir deletar owners
        if ($user->isOwner()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Não é possível remover um owner.');
        }

        // Verificar se o admin tem imagens
        if ($user->images()->count() > 0) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Não é possível remover um admin que tem imagens carregadas. Remova as imagens primeiro.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin removido com sucesso!');
    }
}
