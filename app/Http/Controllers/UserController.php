<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Validation\Rules\Password;

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

        return Inertia::render('Users/Index', [
            'users' => $query->get()
        ]);
    }

    // Método para Ativar/Desativar
    public function toggleStatus(User $user)
    {
        // SEGURANÇA: Não permite desativar a si mesmo
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Você não pode desativar sua própria conta.');
        }

        $user->update(['is_active' => !$user->is_active]);

        return redirect()->back()->with('message', 'Status atualizado!');
    }

    public function store(Request $request)
    {
        // 1. Definimos as REGRAS (Note que removi o $user->id, pois aqui o usuário ainda não existe)
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Sem o ID, pois o e-mail deve ser único entre todos
            'password' => [
                'required', // No STORE a senha é OBRIGATÓRIA
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'access_level' => 'required|integer',
            'is_active' => 'required|boolean',
        ];

        // 2. Definimos as MENSAGENS
        $messages = [
            'name.required' => 'O campo nome é obrigatório.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'As senhas não coincidem.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
        ];

        // 3. EXECUTA A VALIDAÇÃO (Este passo estava faltando no seu código)
        // O validate() retorna apenas os dados que passaram na validação
        $validatedData = $request->validate($rules, $messages);

        // 4. CRIPTOGRAFA A SENHA
        $validatedData['password'] = Hash::make($validatedData['password']);

        // 5. CRIA O USUÁRIO com os dados validados
        User::create($validatedData);

        return redirect()->route('users.index')->with('message', 'Usuário criado com sucesso!');
    }



    public function edit(User $user)
    {
        // SEGURANÇA: Se não for admin e tentar editar outro ID, bloqueia
        if (auth()->user()->access_level !== 1 && auth()->id() !== $user->id) {
            abort(403, 'Você só pode editar seu próprio perfil.');
        }

        return Inertia::render('Users/Edit', [
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        // 1. Definimos as REGRAS em um array limpo
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => [
                'nullable', // No edit a senha é opcional
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'access_level' => 'required|integer',
            'is_active' => 'required|boolean',
        ];

        // 2. Definimos as MENSAGENS em outro array limpo
        $messages = [
            'name.required' => 'O campo nome é obrigatório.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.confirmed' => 'As senhas não coincidem.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            // O objeto Password lança erros específicos, mas você pode simplificar aqui
        ];

        // 3. Chamamos o validate passando as duas variáveis separadas
        // O erro acontecia aqui porque o parêntese estava fechando antes da hora
        $data = $request->validate($rules, $messages);

        // Só criptografa e atualiza a senha se ela for preenchida
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']); // Remove do array para não zerar a senha no DB
        }

        $user->update($data);

        return redirect()->route('users.index')->with('message', 'Usuário atualizado com sucesso!');
    }




    // O "Esqueci minha senha" versão Admin
    public function resetPassword(User $user)
    {
        $user->update([
            'password' => Hash::make('Mudar@123') // Senha padrão temporária
        ]);

        return redirect()->back()->with('message', 'Senha resetada para: Mudar@123');
    }



    public function destroy(User $user)
    {
        // Segurança: Não permite que o usuário logado se exclua
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Você não pode excluir sua própria conta.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('message', 'Usuário excluído com sucesso!');
    }
}