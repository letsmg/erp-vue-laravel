<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutDashboard, Users, Package, LogOut, Menu, CheckCircle2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const page = usePage();
const user = page.props.auth.user;

// Lógica de Notificação (Toast) com Proteção contra Undefined
const showToast = ref(false);
const toastMessage = ref('');

// Usamos uma função getter segura para o watch
watch(() => page.props.flash?.message, (newMessage) => {
    if (newMessage) {
        toastMessage.value = newMessage;
        showToast.value = true;
        
        // Limpa o timer anterior se existir para evitar bugs de sobreposição
        setTimeout(() => {
            showToast.value = false;
        }, 4000);
    }
}, { immediate: true });
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex">
        <aside class="w-64 bg-slate-900 text-white hidden md:flex flex-col fixed h-full">
            <div class="p-6 text-2xl font-bold border-b border-slate-800">
                ERP <span class="text-blue-400">Zenite</span>
            </div>
            
            <nav class="flex-1 p-4 space-y-2">
                <Link :href="route('dashboard')" :class="{'bg-slate-800': $page.component === 'Dashboard'}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-800 transition">
                    <LayoutDashboard class="w-5 h-5" />
                    <span>Dashboard</span>
                </Link>
                <Link :href="route('users.index')" :class="{'bg-slate-800': $page.component.startsWith('Users/')}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-800 transition">
                    <Users class="w-5 h-5" />
                    <span>Usuários</span>
                </Link>
                <Link href="/products" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-800 transition">
                    <Package class="w-5 h-5" />
                    <span>Produtos</span>
                </Link>
            </nav>

            <div class="p-4 border-t border-slate-800">
                <Link :href="route('logout')" method="post" as="button" class="flex items-center space-x-3 p-3 w-full text-red-400 hover:bg-slate-800 rounded-lg transition text-left">
                    <LogOut class="w-5 h-5" />
                    <span>Sair</span>
                </Link>
            </div>
        </aside>

        <div class="flex-1 flex flex-col md:ml-64">
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 sticky top-0 z-10">
                <h1 class="text-xl font-semibold text-gray-800 uppercase tracking-tight">
                    {{ $page.component.split('/').pop() }}
                </h1>
                <div class="flex items-center space-x-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-900 leading-none">{{ user.name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ user.email }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-600 text-white flex items-center justify-center rounded-full font-bold uppercase shadow-inner">
                        {{ user.name[0] }}
                    </div>
                </div>
            </header>

            <main class="p-8">
                <slot />
            </main>

            <footer class="mt-auto py-4 px-8 bg-white border-t text-center text-sm text-gray-400 font-medium">
                &copy; 2026 ERP Zenite - Gestão Inteligente
            </footer>
        </div>

        <Transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showToast" class="fixed top-5 right-5 z-[100] flex items-center bg-gray-900 text-white px-5 py-3 rounded-lg shadow-2xl border-l-4 border-green-500">
                <CheckCircle2 class="w-5 h-5 mr-3 text-green-500" />
                <span class="text-sm font-bold">{{ toastMessage }}</span>
            </div>
        </Transition>
    </div>
</template>