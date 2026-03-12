<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { fillFormData, clearFormData } from '@/lib/utils';
import { 
    Save, ArrowLeft, DollarSign, 
    Star, Percent, Keyboard, Camera, X 
} from 'lucide-vue-next';

const props = defineProps({
    suppliers: Array
});

const activeTab = ref('geral');
const imagePreviews = ref([]); // Gerencia os URLs temporários das fotos

const form = useForm({
    // Geral
    supplier_id: null,
    description: '',
    brand: '',
    model: '',
    size: '',
    collection: '',
    gender: 'Unissex',
    barcode: '',
    stock_quantity: 0,
    is_active: true,
    is_featured: false,
    
    // Fotos
    images: [],

    // Preços e Promoção
    cost_price: 0,
    sale_price: 0,
    promo_price: null,
    promo_start_at: '',
    promo_end_at: '',

    // SEO (Relação Polimórfica)
    meta_title: '',
    meta_description: '',
});

// Lógica de Upload de Imagens
const handleImageUpload = (e) => {
    const files = Array.from(e.target.files);
    
    if (form.images.length + files.length > 6) {
        alert('Você pode enviar no máximo 6 fotos por produto.');
        return;
    }

    files.forEach(file => {
        form.images.push(file);
        imagePreviews.value.push(URL.createObjectURL(file));
    });
};

const removeImage = (index) => {
    form.images.splice(index, 1);
    imagePreviews.value.splice(index, 1);
};

// Listener de Atalhos
const handleKeydown = (e) => {
    const isP = e.key.toLowerCase() === 'p';
    const isL = e.key.toLowerCase() === 'l';

    if (e.ctrlKey && e.shiftKey && isP) {
        e.preventDefault();
        e.stopPropagation();
        fillFormData(form, props.suppliers);
    }

    if (e.ctrlKey && e.shiftKey && isL) {
        e.preventDefault();
        e.stopPropagation();
        clearFormData(form);
        imagePreviews.value = [];
    }
};

onMounted(() => window.addEventListener('keydown', handleKeydown));
onUnmounted(() => window.removeEventListener('keydown', handleKeydown));

const profitData = computed(() => {
    const cost = parseFloat(form.cost_price) || 0;
    const sale = parseFloat(form.sale_price) || 0;
    const profit = sale - cost;
    const margin = cost > 0 ? (profit / cost) * 100 : 0;
    return {
        value: profit.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }),
        percentage: margin.toFixed(2)
    };
});

const submit = () => {
    form.post(route('products.store'), {
        preserveScroll: true,
        forceFormData: true, // Necessário para envio de arquivos
        onSuccess: () => {
            form.reset();
            imagePreviews.value = [];
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Novo Produto" />

        <div class="max-w-5xl mx-auto pb-20">
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <Link :href="route('products.index')" class="flex items-center text-[10px] font-black uppercase text-gray-400 hover:text-indigo-600 transition mb-2 tracking-widest">
                        <ArrowLeft class="w-3 h-3 mr-1" /> Voltar ao estoque
                    </Link>
                    <h2 class="text-3xl font-black text-gray-800 tracking-tighter uppercase">Novo Produto</h2>
                </div>
                
                <div class="flex bg-gray-100 p-1 rounded-xl border border-gray-200 shadow-inner">
                    <button type="button" @click="activeTab = 'geral'" :class="['px-4 py-2 text-[10px] font-black uppercase rounded-lg transition-all', activeTab === 'geral' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700']">Geral</button>
                    <button type="button" @click="activeTab = 'precos'" :class="['px-4 py-2 text-[10px] font-black uppercase rounded-lg transition-all', activeTab === 'precos' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700']">Financeiro</button>
                    <button type="button" @click="activeTab = 'seo'" :class="['px-4 py-2 text-[10px] font-black uppercase rounded-lg transition-all', activeTab === 'seo' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700']">SEO & Marketing</button>
                </div>
            </div>

            <div class="mb-6 flex items-center gap-4 text-indigo-500 bg-indigo-50/50 px-4 py-3 rounded-2xl border border-indigo-100 w-fit">
                <Keyboard class="w-5 h-5" />
                <div class="flex gap-4 items-center">
                    <span class="text-[10px] font-bold uppercase tracking-wider">
                        <b class="text-indigo-700">Ctrl + Shift + P</b> Popular
                    </span>
                    <span class="w-1 h-1 bg-indigo-200 rounded-full"></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider">
                        <b class="text-indigo-700">Ctrl + Shift + L</b> Limpar
                    </span>
                </div>
            </div>

            <div v-if="Object.keys(form.errors).length" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl">
                <p class="text-xs font-black text-red-600 uppercase mb-2">Erros de Validação:</p>
                <ul class="list-disc ml-4 tracking-tight">
                    <li v-for="(error, field) in form.errors" :key="field" class="text-[10px] text-red-500 font-bold uppercase">
                        {{ field }}: {{ error }}
                    </li>
                </ul>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                
                <div v-show="activeTab === 'geral'" class="animate-in fade-in slide-in-from-bottom-2 duration-500 space-y-6">
                    <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-4">Fotos do Produto (Máx 6)</label>
                        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                            <div v-for="(src, index) in imagePreviews" :key="index" class="relative group aspect-square rounded-2xl overflow-hidden border border-gray-100">
                                <img :src="src" class="w-full h-full object-cover" />
                                <button type="button" @click="removeImage(index)" class="absolute top-1 right-1 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                    <X class="w-3 h-3" />
                                </button>
                            </div>

                            <label v-if="form.images.length < 6" class="aspect-square border-2 border-dashed border-gray-100 rounded-2xl flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 transition group">
                                <Camera class="w-6 h-6 text-gray-300 group-hover:text-indigo-500 transition" />
                                <span class="text-[8px] font-black uppercase text-gray-400 mt-2">Adicionar</span>
                                <input type="file" class="hidden" multiple accept="image/*" @change="handleImageUpload" />
                            </label>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Descrição do Produto</label>
                            <input v-model="form.description" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl focus:ring-indigo-500 font-bold" placeholder="Ex: Tênis Runner Air..." required />
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Fornecedor</label>
                            <select v-model="form.supplier_id" class="w-full border-gray-100 bg-gray-50 rounded-2xl focus:ring-indigo-500 text-sm font-bold" required>
                                <option :value="null" disabled>Selecione um fornecedor</option>
                                <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.company_name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Código de Barras (EAN)</label>
                            <input v-model="form.barcode" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl" placeholder="EAN-13" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <input v-model="form.brand" type="text" placeholder="Marca" class="w-full border-gray-100 bg-gray-50 rounded-2xl" />
                            <input v-model="form.model" type="text" placeholder="Modelo" class="w-full border-gray-100 bg-gray-50 rounded-2xl" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <input v-model="form.collection" type="text" placeholder="Coleção" class="w-full border-gray-100 bg-gray-50 rounded-2xl" />
                            <input v-model="form.size" type="text" placeholder="Tamanho" class="w-full border-gray-100 bg-gray-50 rounded-2xl" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 md:col-span-2">
                            <select v-model="form.gender" class="w-full border-gray-100 bg-gray-50 rounded-2xl text-sm font-bold">
                                <option>Masculino</option><option>Feminino</option><option>Unissex</option><option>Infantil</option>
                            </select>
                            <input v-model="form.stock_quantity" type="number" placeholder="Estoque Inicial" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" />
                        </div>
                    </div>
                </div>

                <div v-show="activeTab === 'precos'" class="animate-in fade-in slide-in-from-bottom-2 duration-500">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                            <h3 class="flex items-center text-xs font-black uppercase text-gray-400 mb-6 italic"><DollarSign class="w-4 h-4 mr-2" /> Composição de Preço</h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Custo (R$)</label>
                                    <input v-model="form.cost_price" type="number" step="0.01" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-black text-lg" />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Venda (R$)</label>
                                    <input v-model="form.sale_price" type="number" step="0.01" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-black text-lg text-indigo-600" />
                                </div>
                            </div>
                            
                            <div class="mt-8 p-6 bg-green-50 rounded-2xl border border-green-100 flex justify-between items-center shadow-inner">
                                <div>
                                    <p class="text-[10px] font-black text-green-700 uppercase mb-1">Lucro Bruto</p>
                                    <p class="text-3xl font-black text-green-600 tracking-tighter">{{ profitData.value }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-green-700 uppercase mb-1">Markup %</p>
                                    <p class="text-3xl font-black text-green-600 tracking-tighter">{{ profitData.percentage }}%</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-indigo-900 p-8 rounded-3xl shadow-xl text-white relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 opacity-10"><Percent class="w-24 h-24" /></div>
                            <h3 class="flex items-center text-xs font-black uppercase opacity-60 mb-6 italic">Promoção Ativa</h3>
                            <div class="space-y-4">
                                <input v-model="form.promo_price" type="number" step="0.01" class="w-full bg-indigo-800 border-none rounded-2xl font-black text-white placeholder-indigo-400 focus:ring-2 focus:ring-white" placeholder="R$ 0,00" />
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase opacity-40 ml-2">Data Início</label>
                                    <input v-model="form.promo_start_at" type="datetime-local" class="w-full bg-indigo-800 border-none rounded-xl text-[10px] text-white" />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase opacity-40 ml-2">Data Fim</label>
                                    <input v-model="form.promo_end_at" type="datetime-local" class="w-full bg-indigo-800 border-none rounded-xl text-[10px] text-white" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-show="activeTab === 'seo'" class="animate-in fade-in slide-in-from-bottom-2 duration-500">
                    <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm space-y-8">
                        <div class="flex items-center justify-between p-6 bg-amber-50 rounded-3xl border border-amber-100 cursor-pointer transition-all hover:bg-amber-100/50" @click="form.is_featured = !form.is_featured">
                            <div class="flex items-center">
                                <div :class="['p-3 rounded-2xl transition-all', form.is_featured ? 'bg-amber-500 text-white shadow-lg' : 'bg-amber-100 text-amber-400']">
                                    <Star :class="['w-6 h-6', form.is_featured ? 'fill-white' : '']" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-black text-amber-900 uppercase leading-none mb-1">Destaque de Vitrine</p>
                                    <p class="text-[10px] text-amber-700 font-bold uppercase opacity-60 italic">Priorizar este item no carrossel inicial</p>
                                </div>
                            </div>
                            <div :class="['w-12 h-6 rounded-full transition-colors relative border border-amber-200', form.is_featured ? 'bg-amber-500' : 'bg-white']">
                                <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all shadow-sm', form.is_featured ? 'left-7' : 'left-1']"></div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-2 italic">Meta Title (SEO)</label>
                                <input v-model="form.meta_title" type="text" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-bold" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-2 italic">Meta Description</label>
                                <textarea v-model="form.meta_description" rows="3" class="w-full border-gray-100 bg-gray-50 rounded-2xl font-medium"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-end gap-6 border-t border-gray-100 pt-8">
                    <Link :href="route('products.index')" class="text-[10px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-[0.2em]">
                        Descartar Alterações
                    </Link>
                    <button type="submit" :disabled="form.processing" class="bg-black text-white px-12 py-5 rounded-3xl font-black uppercase text-[10px] tracking-[0.3em] shadow-2xl hover:bg-indigo-600 transition-all flex items-center gap-3 disabled:opacity-50">
                        <Save v-if="!form.processing" class="w-4 h-4" />
                        <span v-else class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
                        {{ form.processing ? 'Processando' : 'Salvar Produto' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-in { animation-duration: 400ms; }
</style>