<script setup>
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    auth: Object
});

const form = useForm({
    name: '',
    email: '',
    password: '', 
    password_confirmation: '',
    access_level: 0,
    is_active: true,
});

// Validação em Tempo Real (Frontend)
const passwordErrors = computed(() => {
    const p = form.password;
    if (!p) return [];
    
    const errors = [];
    if (p.length < 8) errors.push("Mínimo de 8 caracteres.");
    if (!/[A-Z]/.test(p)) errors.push("Uma letra maiúscula.");
    if (!/[a-z]/.test(p)) errors.push("Uma letra minúscula.");
    if (!/[0-9]/.test(p)) errors.push("Um número.");
    if (!/[!@#$%^&*(),.?":{}|<>]/.test(p)) errors.push("Um caractere especial.");
    
    return errors;
});

const isPasswordConfirmed = computed(() => {
    return form.password.length > 0 && form.password === form.password_confirmation;
});

const submit = () => {
    // Trava o envio se houver erros de validação local
    if (passwordErrors.value.length > 0 || !isPasswordConfirmed.value) {
        alert('Por favor, corrija os erros da senha antes de enviar.');
        return;
    }

    form.post(route('users.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Novo Usuário" />

    <div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto">
            
            <div class="mb-6">
                <Link :href="route('users.index')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    &larr; Voltar para a lista
                </Link>
            </div>

            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <div class="px-4 py-6 sm:p-8">
                    <header class="mb-8">
                        <h2 class="text-xl font-semibold leading-7 text-gray-900">Cadastrar Novo Usuário</h2>
                    </header>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-900">Nome Completo</label>
                            <input v-model="form.name" type="text" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-600 sm:text-sm" />
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900">E-mail</label>
                            <input v-model="form.email" type="email" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-600 sm:text-sm" />
                            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-900">Senha</label>
                                <input v-model="form.password" type="password" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus: