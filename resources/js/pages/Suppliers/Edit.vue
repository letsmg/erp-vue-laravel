<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { maskPhone, maskCNPJ, maskCEP } from '@/lib/utils'; 
import { 
    Save, ArrowLeft, AlertCircle, Loader2, 
    Building2, MapPin, Phone, Mail 
} from 'lucide-vue-next';

const props = defineProps({
    supplier: Object
});

const form = useForm({
    company_name: props.supplier?.company_name || '',
    email: props.supplier?.email || '',
    cnpj: props.supplier?.cnpj || '',
    state_registration: props.supplier?.state_registration || '',
    address: props.supplier?.address || '',
    neighborhood: props.supplier?.neighborhood || '',
    city: props.supplier?.city || '',
    state: props.supplier?.state || '',
    zip_code: props.supplier?.zip_code || '',
    contact_name_1: props.supplier?.contact_name_1 || '',
    phone_1: props.supplier?.phone_1 || '',
    contact_name_2: props.supplier?.contact_name_2 || '',
    phone_2: props.supplier?.phone_2 || '',
    is_active: Boolean(props.supplier?.is_active ?? true),
});

// Estados para controle de erros de digitação (letras em campos de números)
const errors = ref({
    cnpj: false,
    zip_code: false,
    phone_1: false,
    phone_2: false
});

// Helper para validar e aplicar máscara
const validateAndMask = (field, value, maskFn) => {
    // Detecta se há letras no valor digitado antes da limpeza
    errors.value[field] = /[a-zA-Z]/.test(value);
    // Aplica a máscara (que remove as letras automaticamente)
    form[field] = maskFn(value);
};

// Handlers de Input
const handleCNPJ = (e) => validateAndMask('cnpj', e.target.value, maskCNPJ);
const handleCEP = (e) => validateAndMask('zip_code', e.target.value, maskCEP);
const handlePhone1 = (e) => validateAndMask('phone_1', e.target.value, maskPhone);
const handlePhone2 = (e) => validateAndMask('phone_2', e.target.value, maskPhone);

const submit = () => {
    form.put(route('suppliers.update', props.supplier.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="'Editar: ' + supplier.company_name" />

        <div class="max-w-5xl mx-auto pb-20 pt-10 px-4">
            <div class="mb-8">
                <Link :href="route('suppliers.index')" class="flex items-center text-[10px] font-black uppercase text-gray-400 hover:text-indigo-600 transition mb-2 tracking-widest">
                    <ArrowLeft class="w-3 h-3 mr-1" /> Voltar para listagem
                </Link>
                <h2 class="text-3xl font-black text-gray-800 tracking-tighter uppercase">Editar Fornecedor</h2>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                    <h3 class="flex items-center text-[11px] font-black uppercase text-indigo-500 mb-6 tracking-widest"><Building2 class="w-4 h-4 mr-2" /> Identificação</h3>
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                        <div class="md:col-span-4">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Razão Social</label>
                            <input v-model="form.company_name" type="text" maxlength="150" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold focus:ring-black" required />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">CNPJ</label>
                            <input :value="form.cnpj" @input="handleCNPJ" type="text" inputmode="numeric" maxlength="18" :class="{'border-red-500 ring-1 ring-red-500': errors.cnpj}" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" required />
                            <p v-if="errors.cnpj" class="text-[9px] text-red-500 font-bold mt-1 uppercase tracking-tighter animate-pulse">Apenas números</p>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">E-mail</label>
                            <div class="relative">
                                <Mail class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" />
                                <input v-model="form.email" type="email" class="w-full pl-12 border-gray-100 bg-gray-50 rounded-2xl font-bold focus:ring-black" required />
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Inscrição Estadual</label>
                            <input v-model="form.state_registration" type="text" maxlength="20" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" required />
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                    <h3 class="flex items-center text-[11px] font-black uppercase text-indigo-500 mb-6 tracking-widest"><MapPin class="w-4 h-4 mr-2" /> Localização</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="md:col-span-3">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Endereço</label>
                            <input v-model="form.address" type="text" maxlength="150" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" required />
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">CEP</label>
                            <input :value="form.zip_code" @input="handleCEP" type="text" inputmode="numeric" maxlength="10" :class="{'border-red-500 ring-1 ring-red-500': errors.zip_code}" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" required />
                            <p v-if="errors.zip_code" class="text-[9px] text-red-500 font-bold mt-1 uppercase tracking-tighter">Apenas números</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Bairro</label>
                            <input v-model="form.neighborhood" type="text" maxlength="100" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" required />
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Cidade</label>
                            <input v-model="form.city" type="text" maxlength="100" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" required />
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">UF</label>
                            <select v-model="form.state" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold h-[42px]" required>
                                <option value="" disabled>Selecione</option>
                                <option v-for="uf in ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO']" :key="uf" :value="uf">{{ uf }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                    <h3 class="flex items-center text-[11px] font-black uppercase text-indigo-500 mb-6 tracking-widest"><Phone class="w-4 h-4 mr-2" /> Contatos</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 transition-all" :class="{'bg-red-50 border-red-100': errors.phone_1}">
                            <label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Nome Contato 1</label>
                            <input v-model="form.contact_name_1" type="text" class="w-full mb-4 border-white bg-white rounded-xl font-bold" required />
                            <label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Telefone 1</label>
                            <input :value="form.phone_1" @input="handlePhone1" type="text" inputmode="numeric" :class="{'border-red-500 ring-1 ring-red-500': errors.phone_1}" class="w-full border-white bg-white rounded-xl font-bold" required />
                            <p v-if="errors.phone_1" class="text-[9px] text-red-500 font-bold mt-1 uppercase tracking-tighter">Letras não permitidas</p>
                        </div>
                        <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 transition-all" :class="{'bg-red-50 border-red-100': errors.phone_2}">
                            <label class="block text-[9px] font-bold text-gray-400 uppercase mb-1">Nome Contato 2 (Opcional)</label>
                            <input v-model="form.contact_name_2" type="text" class="w-full mb-4 border-white bg-white rounded-xl font-bold" />
                            <label class="block text-[9px] font-bold text-gray-400 uppercase mb-1">Telefone 2</label>
                            <input :value="form.phone_2" @input="handlePhone2" type="text" inputmode="numeric" :class="{'border-red-500 ring-1 ring-red-500': errors.phone_2}" class="w-full border-white bg-white rounded-xl font-bold" />
                            <p v-if="errors.phone_2" class="text-[9px] text-red-500 font-bold mt-1 uppercase tracking-tighter">Letras não permitidas</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-6 pt-4">
                    <Link :href="route('suppliers.index')" class="text-[10px] font-black uppercase text-gray-400 hover:text-red-500 tracking-widest transition">Descartar</Link>
                    <button type="submit" :disabled="form.processing" class="bg-black text-white px-10 py-5 rounded-3xl font-black uppercase text-[10px] tracking-[0.3em] shadow-xl hover:bg-indigo-600 transition-all flex items-center gap-3">
                        <Loader2 v-if="form.processing" class="w-4 h-4 animate-spin" />
                        <Save v-else class="w-4 h-4" />
                        {{ form.processing ? 'Processando...' : 'Salvar Alterações' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>