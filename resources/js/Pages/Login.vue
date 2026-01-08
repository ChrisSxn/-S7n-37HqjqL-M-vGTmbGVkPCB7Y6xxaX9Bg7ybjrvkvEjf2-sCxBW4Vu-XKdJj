<template>
    <div class="min-h-screen flex items-center justify-center" :style="{ backgroundColor: 'var(--sidebar-bg)' }">
        <div class="bg-white rounded-2xl p-8 shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Login</h1>
            
            <form @submit.prevent="handleLogin" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                        id="email"
                        v-model="email"
                        type="email"
                        required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        placeholder="Enter your email"
                    />
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input
                        id="password"
                        v-model="password"
                        type="password"
                        required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        placeholder="Enter your password"
                    />
                </div>
                
                <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <div class="text-red-800 text-sm">{{ error }}</div>
                </div>
                
                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="loading">Logging in...</span>
                    <span v-else>Login</span>
                </button>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { apiService } from '../services/api.ts';

const email = ref<string>('woub@example.com');
const password = ref<string>('woubcity2026');
const loading = ref<boolean>(false);
const error = ref<string | null>(null);

const handleLogin = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        await apiService.login(email.value, password.value);
        
        await new Promise(resolve => setTimeout(resolve, 100));
        
        router.visit('/explore');
    } catch (err: unknown) {
        const errorMessage = err instanceof Error ? err.message : 'Unable to login. Please try again.';
        error.value = errorMessage;
    } finally {
        loading.value = false;
    }
};
</script>

