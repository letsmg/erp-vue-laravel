<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model que representa os Fornecedores no sistema.
 */
class Supplier extends Model
{
    // Define explicitamente os campos que podem ser preenchidos via formulário (Segurança)
    protected $fillable = [
        'company_name',
        'cnpj',
        'state_registration',
        'address',
        'neighborhood',
        'city',
        'zip_code',
        'state',
        'email',
        'contact_name_1',
        'phone_1',
        'contact_name_2',
        'phone_2',
        'is_active', // <--- ADICIONE ESTA LINHA
    ];

    /**
     * Relacionamento: Um fornecedor possui muitos produtos.
     * Isso permite fazer: $supplier->products
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}