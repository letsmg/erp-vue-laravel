<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SitemapController;


// --- ROTAS PÚBLICAS (Visitantes) ---
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

// --- ROTAS PROTEGIDAS (Usuários Autenticados) ---
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Principal
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Logout (Sempre POST por segurança)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('products', ProductController::class);        
        
    Route::resource('suppliers', SupplierController::class);

    // --- ÁREA DO ADMINISTRADOR ---
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        
        // CRUD de Usuários completo
        Route::resource('users', UserController::class);
        
        // Rota específica para o Reset de Senha (Esqueci minha senha via Admin)
        Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset');
        
        

        //altera outros usuarios entre ativo e inativo, mas não pode alterar o status do proprio usuario
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');          
    });

});