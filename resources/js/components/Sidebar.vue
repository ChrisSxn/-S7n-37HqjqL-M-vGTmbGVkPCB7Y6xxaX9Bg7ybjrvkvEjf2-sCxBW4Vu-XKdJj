<template>
    <aside class="w-24 border-r flex flex-col items-center py-4 flex-shrink-0" style="background-color: var(--sidebar-bg); border-color: #333333;">
        <div class="flex flex-col items-center h-full w-full">
            <a href="#" class="w-12 h-12 rounded-lg bg-gray-800 hover:bg-gray-700 transition-colors mb-8 flex items-center justify-center cursor-pointer overflow-hidden">
                <img :src="logoImage" alt="Logo" class="w-full h-full object-cover rounded-lg" />
            </a>
            
            <!-- Navigation Icons - Centered -->
            <nav class="flex flex-col space-y-4 flex-1 justify-center">
                <!-- Home -->
                <button 
                    @click="navigateToHome"
                    :class="[
                        'w-12 h-12 flex items-center justify-center rounded-lg transition-colors cursor-pointer',
                        activeView === 'home' ? 'bg-orange-500' : 'hover:bg-gray-700'
                    ]"
                >
                    <svg 
                        :class="[
                            'w-6 h-6',
                            activeView === 'home' ? 'text-white' : 'text-gray-300'
                        ]" 
                        fill="none" 
                        stroke="currentColor" 
                        stroke-width="2" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </button>
                
                <!-- Map (Active - White icon on Orange background) -->
                <button 
                    @click="navigateToExplore"
                    :class="[
                        'w-12 h-12 flex items-center justify-center rounded-lg transition-colors cursor-pointer',
                        activeView === 'explore' ? 'bg-orange-500' : 'hover:bg-gray-700'
                    ]"
                >
                    <svg 
                        :class="[
                            'w-6 h-6',
                            activeView === 'explore' ? 'text-white' : 'text-gray-300'
                        ]" 
                        fill="none" 
                        stroke="currentColor" 
                        stroke-width="2" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </button>
                
                <!-- Heart -->
                <button 
                    @click="navigateToCities"
                    :class="[
                        'w-12 h-12 flex items-center justify-center rounded-lg transition-colors cursor-pointer',
                        activeView === 'favorites' ? 'bg-orange-500' : 'hover:bg-gray-700'
                    ]"
                >
                    <svg 
                        :class="[
                            'w-6 h-6',
                            activeView === 'favorites' ? 'text-white' : 'text-gray-300'
                        ]" 
                        fill="none" 
                        stroke="currentColor" 
                        stroke-width="2" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </nav>
            
            <!-- Bottom Icon - Only Logout -->
            <div class="flex flex-col mt-auto">
                <!-- Logout (Red icon, transparent background) -->
                <button 
                    @click="handleLogout"
                    class="w-12 h-12 flex items-center justify-center rounded-lg hover:bg-gray-800 transition-colors cursor-pointer"
                >
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </div>
    </aside>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import logoImage from '../../images/logo.png';
import { apiService } from '../services/api.ts';

interface Props {
    activeView?: 'home' | 'explore' | 'favorites';
}

const props = withDefaults(defineProps<Props>(), {
    activeView: 'home'
});

const emit = defineEmits<{
    'view-change': [view: 'home' | 'explore' | 'favorites'];
}>();

const loading = ref<boolean>(false);

const navigateToHome = () => {
    emit('view-change', 'home');
};

const navigateToExplore = () => {
    emit('view-change', 'explore');
};

const navigateToCities = () => {
    emit('view-change', 'favorites');
};

const handleLogout = async () => {
    if (loading.value) {
        return;
    }

    loading.value = true;

    try {
        await apiService.logout();
        router.visit('/login');
    } catch (err) {
        console.error('Logout failed:', err);
    } finally {
        loading.value = false;
    }
};
</script>
