import { clsx } from 'clsx';
import type { ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

// --- NOVAS FUNÇÕES DE MÁSCARA (CENTRALIZADAS) ---

/**
 * Formata Telefone e bloqueia letras.
 * Aceita: (xx) xxxx-xxxx ou (xx) xxxxx-xxxx
 */
export const maskPhone = (value: string): string => {
    let r = value.replace(/\D/g, ""); // Remove tudo que não é número
    if (r.length > 11) r = r.substring(0, 11);

    if (r.length > 10) {
        r = r.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
    } else if (r.length > 6) {
        r = r.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
    } else if (r.length > 2) {
        r = r.replace(/^(\d{2})(\d{0,5})/, "($1) $2");
    } else if (r.length > 0) {
        r = "(" + r;
    }
    return r;
};

/**
 * Formata CNPJ e bloqueia letras.
 */
export const maskCNPJ = (value: string): string => {
    let r = value.replace(/\D/g, "");
    if (r.length > 14) r = r.substring(0, 14);

    return r
        .replace(/^(\d{2})(\d)/, "$1.$2")
        .replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3")
        .replace(/\.(\d{3})(\d)/, ".$1/$2")
        .replace(/(\d{4})(\d)/, "$1-$2");
};

/**
 * Formata CEP e bloqueia letras.
 */
export const maskCEP = (value: string): string => {
    let r = value.replace(/\D/g, "");
    if (r.length > 8) r = r.substring(0, 8);
    return r.replace(/^(\d{5})(\d)/, "$1-$2");
};

// --- FUNÇÕES DE TESTE EXISTENTES ---

export const fillFormData = (form: any, suppliers: any[] = []) => {
    if (!form) return;

    const ufs = ['SP', 'RJ', 'MG', 'PR', 'SC', 'RS', 'BA', 'GO', 'CE', 'PE'];

    const fakeData: Record<string, any> = {
        company_name: () => "Empresa Teste " + Math.random().toString(36).substring(7).toUpperCase(),
        email: () => `teste_${Math.random().toString(36).substring(5)}@zenite.com`,        
        cnpj: () => "42.123.456/0001-99", // Exemplo fixo válido
        state_registration: () => "ISENTO",
        zip_code: () => "01001-000",
        address: () => "Rua de Teste, " + Math.floor(Math.random() * 999),
        neighborhood: () => "Bairro Industrial",
        city: () => "São Paulo",
        state: () => ufs[Math.floor(Math.random() * ufs.length)],
        contact_name_1: () => "Contato Principal",
        phone_1: () => "(11) 98888-7777",
        contact_name_2: () => "Contato Secundário",
        phone_2: () => "(11) 4002-8922",
        is_active: () => true,
        // ... restante dos campos de produtos
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
        meta_description: () => "O melhor tênis para corrida...",
        supplier_id: () => (suppliers && suppliers.length > 0) ? suppliers[0].id : '',
    };

    Object.keys(form.data()).forEach((key) => {
        if (fakeData[key]) {
            form[key] = fakeData[key]();
        }
    });
};

export const clearFormData = (form: any) => {
    if (!form) return;
    Object.keys(form.data()).forEach((key) => {
        const value = form[key];
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