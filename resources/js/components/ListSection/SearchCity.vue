<template>
    <div class="relative mb-4">
        <input
            type="text"
            v-model="searchTerm"
            @input="handleInput"
            @focus="showDropdown = true"
            @blur="handleBlur"
            placeholder="Search places..."
            class="w-full px-4 py-2 rounded-lg border text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" style="background-color: #2a2a2a; border-color: #333333;"
        />
        
        <div
            v-if="showDropdown"
            class="absolute top-full left-0 right-0 mt-1 border rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto" style="background-color: #2a2a2a; border-color: #333333;"
        >
            <!-- Loading State -->
            <div v-if="loading" class="px-4 py-2 text-sm text-gray-400">
                Searching...
            </div>
            
            <!-- Error State -->
            <div v-else-if="error" class="px-4 py-2 text-sm text-red-300">
                {{ error }}
            </div>
            
            <!-- Results -->
            <div
                v-else-if="cities.length > 0"
            >
                <div
                    v-for="city in cities"
                    :key="city.name"
                    @mousedown="selectCity(city)"
                    class="px-4 py-2 hover:bg-gray-900 text-gray-100 cursor-pointer border-b last:border-b-0" style="border-color: #333333;"
                >
                    {{ city.name }}
                </div>
            </div>
            
            <!-- No Results -->
            <div v-else-if="searchTerm.length >= 2" class="px-4 py-2 text-sm text-gray-400">
                No cities found
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { City } from '../../types';
import { apiService } from '../../services/api.ts';

const emit = defineEmits<{
    'select': [city: City];
}>();

const searchTerm = ref('');
const cities = ref<City[]>([]);
const showDropdown = ref(false);
const searchTimeout = ref<ReturnType<typeof setTimeout> | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);

const handleInput = () => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }

    if (searchTerm.value.length < 2) {
        cities.value = [];
        showDropdown.value = false;
        error.value = null;
        loading.value = false;
        return;
    }

    searchTimeout.value = setTimeout(() => {
        searchCities();
    }, 300);
};

const searchCities = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        cities.value = await apiService.searchCities(searchTerm.value);
        showDropdown.value = true;
    } catch (err) {
        const errorMessage = err instanceof Error ? err.message : 'Unable to search cities. Please try again.';
        console.error('Error searching cities:', err);
        error.value = errorMessage;
        cities.value = [];
    } finally {
        loading.value = false;
    }
};

const selectCity = (city: City) => {
    searchTerm.value = '';
    showDropdown.value = false;
    cities.value = [];
    emit('select', city);
};

const handleBlur = () => {
    setTimeout(() => {
        showDropdown.value = false;
    }, 200);
};
</script>
