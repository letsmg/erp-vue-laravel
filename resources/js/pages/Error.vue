<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { AlertCircle, Home } from 'lucide-vue-next';

// Recebe o status do erro (404, 500, etc) do Laravel
defineProps({ status: Number });
</script>

<template>
    <AuthenticatedLayout>
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
            <AlertCircle class="w-20 h-20 text-red-500 mb-4" />
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                {{ status === 404 ? 'Página não encontrada' : 'Erro no servidor' }}
            </h1>
            <p class="text-gray-600 mb-6">
                Desculpe, a página que você está procurando não existe ou foi movida.
            </p>
            <Link href="/" class="bg-blue-600 text-white px-6 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700">
                <Home class="w-5 h-5" />
                Voltar para o Início
            </Link>
        </div>
    </AuthenticatedLayout>
</template>