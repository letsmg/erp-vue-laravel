<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Save, ArrowLeft, AlertCircle, Loader2, Building2, MapPin, Phone } from 'lucide-vue-next';

const props = defineProps({
    supplier: Object
});

const form = useForm({
    company_name: props.supplier?.company_name || '',
    cnpj: props.supplier?.cnpj || '',
    state_registration: props.supplier?.state_registration || '',
    address: props.supplier?.address || '',
    neighborhood: props.supplier?.neighborhood || '',
    city: props.supplier?.city || '',
    zip_code: props.supplier?.zip_code || '',
    state: props.supplier?.state || '',
    email: props.supplier?.email || '',
    contact_name_1: props.supplier?.contact_name_1 || '',
    phone_1: props.supplier?.phone_1 || '',
    contact_name_2: props.supplier?.contact_name_2 || '',
    phone_2: props.supplier?.phone_2 || '',
});

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
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <Link :href="route('suppliers.index')" class="flex items-center text-[10px] font-black uppercase text-gray-400 hover:text-indigo-600 transition mb-2 tracking-widest">
                        <ArrowLeft class="w-3 h-3 mr-1" /> Voltar
                    </Link>
                    <h2 class="text-3xl font-black text-gray-800 tracking-tighter uppercase">Editar Fornecedor</h2>
                </div>
            </div>

            <div v-if="form.hasErrors" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-2xl animate-in fade-in zoom-in">
                <div class="flex items-center mb-2">
                    <AlertCircle class="w-4 h-4 text-red-600 mr-2" />
                    <h3 class="text-red-800 font-black uppercase text-[10px]">Erros de Validação:</h3>
                </div>
                <ul class="text-red-600 text-xs font-bold space-y-1 ml-6 list-disc">
                    <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                </ul>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                    <h3 class="flex items-center text-[11px] font-black uppercase text-indigo-500 mb-6 tracking-widest">
                        <Building2 class="w-4 h-4 mr-2" /> Dados Principais
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-3">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Razão Social</label>
                            <input v-model="form.company_name" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold focus:ring-indigo-500" required />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">CNPJ</label>
                            <input v-model="form.cnpj" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Insc. Estadual</label>
                            <input v-model="form.state_registration" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" />
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">E-mail Corporativo</label>
                            <input v-model="form.email" type="email" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" />
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                    <h3 class="flex items-center text-[11px] font-black uppercase text-indigo-500 mb-6 tracking-widest">
                        <MapPin class="w-4 h-4 mr-2" /> Endereço
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="md:col-span-3">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Logradouro (Rua, Nº)</label>
                            <input v-model="form.address" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">CEP</label>
                            <input v-model="form.zip_code" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Bairro</label>
                            <input v-model="form.neighborhood" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Cidade</label>
                            <input v-model="form.city" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Estado (UF)</label>
                            <select v-model="form.state" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold">
                                <option value="">UF</option>
                                <option v-for="uf in ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO']" :key="uf" :value="uf">{{ uf }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                    <h3 class="flex items-center text-[11px] font-black uppercase text-indigo-500 mb-6 tracking-widest">
                        <Phone class="w-4 h-4 mr-2" /> Canais de Contato
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4 p-4 bg-gray-50 rounded-2xl">
                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-tighter">Contato Principal</p>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Nome</label>
                                <input v-model="form.contact_name_1" type="text" class="w-full border-white bg-white rounded-xl font-bold" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Telefone</label>
                                <input v-model="form.phone_1" type="text" class="w-full border-white bg-white rounded-xl font-bold" />
                            </div>
                        </div>
                        <div class="space-y-4 p-4 bg-gray-50 rounded-2xl">
                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-tighter">Contato Secundário</p>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Nome</label>
                                <input v-model="form.contact_name_2" type="text" class="w-full border-white bg-white rounded-xl font-bold" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Telefone</label>
                                <input v-model="form.phone_2" type="text" class="w-full border-white bg-white rounded-xl font-bold" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-6 pt-4">
                    <Link :href="route('suppliers.index')" class="text-[10px] font-black uppercase text-gray-400 hover:text-gray-600 flex items-center tracking-widest transition">Descartar Alterações</Link>
                    <button type="submit" :disabled="form.processing" class="bg-black text-white px-12 py-5 rounded-3xl font-black uppercase text-[10px] tracking-[0.3em] shadow-2xl hover:bg-indigo-600 transition-all flex items-center gap-3">
                        <Loader2 v-if="form.processing" class="w-4 h-4 animate-spin" />
                        <Save v-else class="w-4 h-4" />
                        {{ form.processing ? 'Processando' : 'Salvar Fornecedor' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>