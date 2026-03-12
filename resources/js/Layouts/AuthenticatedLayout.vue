<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { 
    LayoutDashboard, Users, Package, Truck, 
    LogOut, CheckCircle2, X, AlertTriangle 
} from 'lucide-vue-next';
import { ref, watch, onMounted, onUnmounted, computed } from 'vue';

const page = usePage();
const user = page.props.auth.user;

// --- Lógica de Notificações (Toast) ---
const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('success'); 

// 1. Declaramos a função primeiro
const triggerToast = () => {
    showToast.value = true;
    const duration = toastType.value === 'error' ? 6000 : 4000;
    setTimeout(() => { showToast.value = false; }, duration);
};

// 2. Depois chamamos no watch
watch(() => page.props.flash?.message, (newMessage) => {
    if (newMessage) {
        toastType.value = 'success';
        toastMessage.value = newMessage;
        triggerToast();
    }
}, { immediate: true });

const errors = computed(() => page.props.errors);
watch(errors, (newErrors) => {
    if (Object.keys(newErrors).length > 0) {
        toastType.value = 'error';
        toastMessage.value = Object.values(newErrors)[0];
        triggerToast();
    }
}, { deep: true });

// --- Atalhos Globais ---
const handleKeyDown = (e) => {
    if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'p') {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('magic-fill'));
    }
    if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'l') {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('magic-clear'));
    }
};

onMounted(() => { window.addEventListener('keydown', handleKeyDown); });
onUnmounted(() => { window.removeEventListener('keydown', handleKeyDown); });

const isUrl = (url) => page.url === url || page.url.startsWith(url + '/');
</script>


<template>
    <div class="min-h-screen bg-gray-50 flex">
        <aside class="w-64 bg-slate-950 text-white hidden md:flex flex-col fixed h-full shadow-2xl z-20">
            <div class="p-6 border-b border-slate-900 flex items-center gap-3">
                <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center text-lg font-black shadow-lg shadow-indigo-500/20">Z</div>
                <div class="flex flex-col">
                    <span class="text-sm font-black tracking-tighter leading-none">ERP Vue Laravel</span>
                    <span class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1">SaaS Edition</span>
                </div>
            </div>
            
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto custom-scrollbar">
                <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] px-3 mb-4 mt-2">Navegação</p>
                
                <Link :href="route('dashboard')" 
                    :class="[isUrl('/dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-100']"
                    class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-200 group cursor-pointer">
                    <LayoutDashboard class="w-5 h-5" />
                    <span class="font-bold text-sm">Dashboard</span>
                </Link>

                <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] px-3 mb-4 mt-8">Estoque</p>

                <Link :href="route('products.index')" 
                    :class="[isUrl('/products') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-100']"
                    class="flex items-center space-x-3 p-3 rounded-xl transition-all cursor-pointer group">
                    <Package class="w-5 h-5" />
                    <span class="font-bold text-sm">Produtos</span>
                </Link>

                <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] px-3 mb-4 mt-8">Administração</p>

                <Link :href="route('users.index')" 
                    :class="[isUrl('/users') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-100']"
                    class="flex items-center space-x-3 p-3 rounded-xl transition-all cursor-pointer group">
                    <Users class="w-5 h-5" />
                    <span class="font-bold text-sm">Usuários</span>
                </Link>

                <Link :href="route('suppliers.index')" 
                    :class="[isUrl('/suppliers') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-100']"
                    class="flex items-center space-x-3 p-3 rounded-xl transition-all cursor-pointer group">
                    <Truck class="w-5 h-5" />
                    <span class="font-bold text-sm">Fornecedores</span>
                </Link>
            </nav>

            <div class="p-4 border-t border-slate-900 bg-slate-950/50">
                <Link :href="route('logout')" method="post" as="button" class="flex items-center space-x-3 p-3 w-full text-slate-500 hover:text-red-400 hover:bg-red-500/5 rounded-xl transition-all text-left group cursor-pointer font-bold text-sm">
                    <LogOut class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
                    <span>Encerrar Sessão</span>
                </Link>
            </div>
        </aside>

        <div class="flex-1 flex flex-col md:ml-64">
            <header class="bg-white/80 backdrop-blur-md h-16 flex items-center justify-between px-8 sticky top-0 z-10 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <h1 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">
                        {{ $page.component.split('/').pop() }}
                    </h1>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-black text-gray-900 leading-none">{{ user.name }}</p>
                        <p class="text-[9px] text-indigo-600 mt-1 uppercase font-black tracking-widest">
                            {{ user.access_level === 1 ? 'Administrator' : 'Operator' }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 text-indigo-700 flex items-center justify-center rounded-xl font-black border border-indigo-200 uppercase">
                        {{ user.name.substring(0,2) }}
                    </div>
                </div>
            </header>

            <main class="p-8 flex-1 animate-in fade-in duration-500">
                <slot />
            </main>
        </div>

        <Transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showToast" 
                class="fixed bottom-8 right-8 z-[100] flex items-center bg-gray-950 text-white p-1 pr-6 rounded-2xl shadow-2xl border border-white/10 overflow-hidden min-w-[350px] max-w-md">
                
                <div :class="[
                    'p-4 rounded-xl text-white shadow-lg mr-4',
                    toastType === 'success' ? 'bg-emerald-500 shadow-emerald-500/20' : 'bg-red-500 shadow-red-500/20'
                ]">
                    <CheckCircle2 v-if="toastType === 'success'" class="w-6 h-6" />
                    <AlertTriangle v-else class="w-6 h-6" />
                </div>

                <div class="flex-1">
                    <p :class="[
                        'text-[10px] font-black uppercase tracking-widest',
                        toastType === 'success' ? 'text-emerald-400' : 'text-red-400'
                    ]">
                        {{ toastType === 'success' ? 'Sucesso' : 'Erro de Sistema' }}
                    </p>
                    <p class="text-sm font-bold text-gray-100 leading-tight">{{ toastMessage }}</p>
                </div>

                <button @click="showToast = false" class="ml-4 p-1 hover:bg-white/10 rounded-lg transition cursor-pointer">
                    <X class="w-4 h-4 text-gray-500" />
                </button>

                <div :class="[
                        'absolute bottom-0 left-0 h-1 animate-shrink',
                        toastType === 'success' ? 'bg-emerald-500' : 'bg-red-500'
                    ]" 
                    :style="{ animationDuration: toastType === 'error' ? '6s' : '4s' }">
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
@keyframes shrink { from { width: 100%; } to { width: 0%; } }
.animate-shrink { animation-name: shrink; animation-timing-function: linear; animation-fill-mode: forwards; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
</style>