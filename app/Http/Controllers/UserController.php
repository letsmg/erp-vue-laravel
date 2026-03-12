<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        $query = User::orderBy('name');

        // REGRA: Usuário padrão não vê Administradores
        if ($currentUser->access_level !== 1) {
            $query->where('access_level', 0);
        }

        return Inertia::render('Users/Index', ['users' => $query->get()]);
    }

    // ESSENCIAL: Método que resolve o erro 500 ao clicar em "Novo Usuário"
    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'access_level' => 'required|integer',
            'is_active' => 'required|boolean',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ], [
            'email.unique' => 'Este e-mail já está em uso.',
            'password.confirmed' => 'As senhas não coincidem.',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('users.index')->with('message', 'Usuário criado com sucesso!');
    }

    public function edit(User $user)
    {
        // SEGURANÇA: Bloqueia edição de terceiros por não-admins
        if (auth()->user()->access_level !== 1 && auth()->id() !== $user->id) {
            abort(403, 'Você só pode editar seu próprio perfil.');
        }

        return Inertia::render('Users/Edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',            
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'access_level' => 'required|integer',
            'is_active' => 'required|boolean',
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ],[
            // Personalização aqui:
            'email.unique' => 'Este e-mail já está sendo utilizado por outro usuário.',
            'email.required' => 'O campo e-mail é obrigatório.',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return redirect()->route('users.index')->with('message', 'Usuário atualizado!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Você não pode desativar a si mesmo.');
        }
        $user->update(['is_active' => !$user->is_active]);
        return redirect()->back()->with('message', 'Status atualizado!');
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => Hash::make('Mudar@123')]);
        return redirect()->back()->with('message', 'Senha resetada para: Mudar@123');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Você não pode se excluir.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('message', 'Usuário excluído!');
    }
}