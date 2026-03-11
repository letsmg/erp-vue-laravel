<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { UserPlus, UserCog, UserMinus, Key, Trash2 } from 'lucide-vue-next';

const props = defineProps({ 
    users: Array,
    auth: Object 
});

const handleToggleStatus = (user) => {
    const acao = user.is_active ? 'desativar' : 'ativar';
    if (confirm(`Deseja realmente ${acao} o usuário ${user.name}?`)) {
        router.patch(route('users.toggle', user.id));
    }
};

const handleResetPassword = (user) => {
    if (confirm(`Resetar senha de ${user.name} para "Mudar@123"?`)) {
        router.patch(route('users.reset', user.id));
    }
};

const handleDelete = (user) => {
    if (confirm(`EXCLUIR PERMANENTEMENTE o usuário ${user.name}? Esta ação é irreversível.`)) {
        router.delete(route('users.destroy', user.id));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Gestão de Usuários" />

        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Usuários</h2>
                <p class="mt-1 text-sm text-gray-600">Lista de todos os colaboradores com acesso ao ERP Zenite.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <Link :href="route('users.create')" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    <UserPlus class="w-4 h-4 mr-2" />
                    Novo Usuário
                </Link>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Usuário</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nível</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-gray-100 rounded-full flex items-center justify-center font-bold text-gray-600">
                                        {{ user.name[0] }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ user.name }}</div>
                                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="user.access_level === 1 ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'" class="px-3 py-1 rounded-full text-xs font-bold">
                                    {{ user.access_level === 1 ? 'Admin' : 'Padrão' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="user.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-3 py-1 rounded-full text-xs font-bold">
                                    {{ user.is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button @click="router.get(route('users.edit', user.id))" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center" title="Editar">
                                    <UserCog class="w-4 h-4 mr-1" /> Editar
                                </button>

                                <template v-if="user.id !== auth.user.id">
                                    <button @click="handleToggleStatus(user)" :class="user.is_active ? 'text-red-600' : 'text-green-600'" class="font-bold inline-flex items-center">
                                        <UserMinus v-if="user.is_active" class="w-4 h-4 mr-1" />
                                        <UserPlus v-else class="w-4 h-4 mr-1" />
                                        {{ user.is_active ? 'Desativar' : 'Ativar' }}
                                    </button>

                                    <button @click="handleResetPassword(user)" class="text-amber-600 font-bold inline-flex items-center" title="Resetar Senha">
                                        <Key class="w-4 h-4 mr-1" /> Reset
                                    </button>

                                    <button @click="handleDelete(user)" class="text-gray-400 hover:text-red-600 transition" title="Excluir">
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </template>
                                
                                <span v-else class="text-gray-400 italic text-xs ml-2">(Você)</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>