<template>
    <section class="w-[560px] border-r flex flex-col flex-shrink-0 overflow-hidden" style="background-color: var(--sidebar-bg); border-color: #333333;">
        <div class="px-6 pt-6 pb-2 flex-shrink-0">
            <SearchCity @select="handleCitySelect" />
        </div>
        
        <div class="border-b" style="border-color: #333333;"></div>
        
        <div class="flex-1 overflow-y-auto">
            <div v-if="loading" class="flex items-center justify-center h-full">
                <div class="text-gray-400">Loading...</div>
            </div>
            
            <div v-else-if="cityDetails" class="px-4 pt-2 pb-4">
                <CityCard :city-details="cityDetails" @city-select="handleCitySelect" @city-details-updated="handleCityDetailsUpdated" />
            </div>
            
            <div v-else class="flex items-center justify-center h-full">
                <div class="text-center text-gray-400">
                    <p class="text-lg mb-2">Search for a city</p>
                    <p class="text-sm">Start exploring by searching for a place above</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import CityCard from './CityCard.vue';
import SearchCity from './SearchCity.vue';
import type { City, CityDetails, SelectedCity } from '../../types';
import { apiService } from '../../services/api.ts';

interface Props {
    selectedCity?: SelectedCity | null;
}

const props = withDefaults(defineProps<Props>(), {
    selectedCity: null
});

const emit = defineEmits<{
    'city-select': [city: City | SelectedCity];
}>();

const cityDetails = ref<CityDetails | null>(null);
const loading = ref(false);

const handleCitySelect = (city: City | SelectedCity) => {
    if ('latitude' in city && 'longitude' in city) {
        emit('city-select', city);
        if (cityDetails.value?.city === city.name) {
            return;
        }
    }
    
    if ('name' in city) {
        fetchCityDetailsByName(city.name);
    }
};

const handleCityDetailsUpdated = (updatedDetails: CityDetails) => {
    cityDetails.value = updatedDetails;
};

const emitCitySelect = (data: CityDetails) => {
    if (data.latitude && data.longitude) {
        emit('city-select', {
            name: data.city,
            latitude: data.latitude,
            longitude: data.longitude
        });
    }
};

const fetchCityDetailsByName = async (cityName: string) => {
    if (cityDetails.value?.city === cityName) {
        emitCitySelect(cityDetails.value);
        return;
    }
    
    loading.value = true;
    cityDetails.value = null;
    
    try {
        const data = await apiService.getCityDetailsByName(cityName);
        cityDetails.value = data;
        emitCitySelect(data);
    } catch {
        cityDetails.value = null;
    } finally {
        loading.value = false;
    }
};

watch(() => props.selectedCity, (newCity) => {
    if (!newCity) {
        cityDetails.value = null;
    }
}, { immediate: true });
</script>
