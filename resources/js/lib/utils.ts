import { clsx } from 'clsx';
import type { ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export const fillFormData = (form: any, suppliers: any[] = []) => {
    if (!form) return;

    const ufs = ['SP', 'RJ', 'MG', 'PR', 'SC', 'RS', 'BA', 'GO', 'CE', 'PE'];

    const fakeData: Record<string, any> = {
        // --- Usuários e Fornecedores ---
        name: () => "Usuário de Teste " + Math.floor(Math.random() * 100),
        company_name: () => "Empresa Teste " + Math.random().toString(36).substring(7).toUpperCase(),
        email: () => `teste_${Math.random().toString(36).substring(5)}@zenite.com`,        
        cnpj: () => {
            const part1 = Math.floor(Math.random() * 90) + 10;
            const part2 = Math.floor(Math.random() * 900) + 100;
            return `${part1}.${part2}.000/0001-66`;
        },
        state_registration: () => "ISENTO",
        zip_code: () => "01001-000",
        address: () => "Rua de Teste, " + Math.floor(Math.random() * 999),
        neighborhood: () => "Bairro Industrial",
        city: () => "São Paulo",
        state: () => ufs[Math.floor(Math.random() * ufs.length)], // Adicionado para o Combobox
        contact_name_1: () => "Contato Principal",
        phone_1: () => "(11) 98888-7777",
        contact_name_2: () => "Contato Secundário", // Adicionado para evitar nulos
        phone_2: () => "(11) 4002-8922",            // Adicionado para evitar nulos
        password: () => "Mudar@123",
        password_confirmation: () => "Mudar@123",
        access_level: () => 0,
        is_active: () => true,

        // --- Produtos ---
        description: () => "Tênis Performance Turbo " + Math.floor(Math.random() * 1000),
        brand: () => "Nike",
        model: () => "Air Max 2026",
        size: () => "42",
        collection: () => "Verão 2026",
        gender: () => "Unissex",
        barcode: () => Math.floor(Math.random() * 1000000000000).toString(),
        stock_quantity: () => Math.floor(Math.random() * 100),
        cost_price: () => 150.00,
        sale_price: () => 449.90,
        promo_price: () => 389.90,
        promo_start_at: () => new Date().toISOString().slice(0, 16),
        promo_end_at: () => {
            const date = new Date();
            date.setDate(date.getDate() + 7);
            return date.toISOString().slice(0, 16);
        },
        is_featured: () => Math.random() > 0.5,
        meta_title: () => "Tênis Nike Air Max 2026 - Oferta Especial",
        meta_description: () => "O melhor tênis para corrida com amortecimento de ponta e design futurista.",
        
        supplier_id: () => (suppliers && suppliers.length > 0) ? suppliers[0].id : '',
    };

    Object.keys(form.data()).forEach((key) => {
        if (fakeData[key]) {
            form[key] = fakeData[key]();
        } else if (typeof form[key] === 'string' && !form[key]) {
            form[key] = "Teste " + key.charAt(0).toUpperCase() + key.slice(1);
        }
    });
};

export const clearFormData = (form: any) => {
    if (!form) return;

    Object.keys(form.data()).forEach((key) => {
        const value = form[key];
        // Adicionei 'state' e os contatos opcionais na lista de limpeza específica se desejar nulos
        if (['promo_price', 'promo_start_at', 'promo_end_at', 'supplier_id'].includes(key)) {
            form[key] = null;
        } 
        else if (typeof value === 'string') form[key] = '';
        else if (typeof value === 'number') form[key] = 0;
        else if (typeof value === 'boolean') form[key] = (key === 'is_active');
        else if (Array.isArray(value)) form[key] = [];
    });
    
    form.clearErrors();
};