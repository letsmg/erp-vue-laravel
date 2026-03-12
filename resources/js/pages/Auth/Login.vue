<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Eye, EyeOff, LogIn } from 'lucide-vue-next';

// Define as propriedades que o Laravel pode enviar (como erros de validação)
defineProps({ errors: Object });

// Inicializa o formulário do Inertia
const form = useForm({
    email: '1@1.com',
    password: '[SenhaNova2026]',
    remember: false,
});

// Estado para mostrar/esconder senha
const showPassword = ref(false);

// Função de submissão
const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
        onError: (errors) => {
            // Se o erro for especificamente de página expirada (comum em SPAs)
            // O Inertia lida com isso automaticamente recarregando, 
            // mas você pode forçar um refresh se notar que ele travou.
            if (Object.keys(errors).length === 0) {
                window.location.reload();
            }
        },
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">ERP Zenite</h2>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">E-mail</label>
                    <input v-model="form.email" type="email" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        :class="{ 'border-red-500': errors.email }">
                    <div v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</div>
                </div>

                <div class="mb-6 relative">
                    <label class="block text-sm font-medium text-gray-700">Senha</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" v-model="form.password" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-500': errors.password }">
                        
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <component :is="showPassword ? EyeOff : Eye" class="h-5 w-5" />
                        </button>
                    </div>
                    <div v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</div>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                    <LogIn v-if="!form.processing" class="w-5 h-5 mr-2" />
                    {{ form.processing ? 'Entrando...' : 'Entrar' }}
                </button>
            </form>

            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <input 
                        id="remember" 
                        v-model="form.remember" 
                        type="checkbox" 
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Lembrar de mim
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                        Esqueceu a senha?
                    </a>
                </div>
            </div>

            <div v-if="errors.email" class="text-red-500 text-sm mt-1">
                {{ errors.email }}
            </div>

        </div>
    </div>
</template>