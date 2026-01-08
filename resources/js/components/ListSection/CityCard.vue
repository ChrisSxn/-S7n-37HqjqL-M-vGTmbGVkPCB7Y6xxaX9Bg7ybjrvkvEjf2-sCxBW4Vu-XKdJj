<template>
    <div class="rounded-2xl overflow-hidden border shadow-md" style="background-color: #2a2a2a; border-color: #333333;">
        <!-- Image -->
        <div class="p-4">
            <div class="group aspect-video bg-gray-200 relative rounded-xl overflow-hidden cursor-pointer" @click.stop="handleToggleLike">
                <img :src="imageUrl" :alt="localCityDetails.city" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.02]" />
                <!-- Hover overlay -->
                <div class="absolute inset-0 bg-black/30 backdrop-blur-none opacity-0 transition-all duration-200 group-hover:opacity-100 group-hover:backdrop-blur-[2px]"></div>
                <!-- Action icon/text -->
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-black/60 text-white text-sm">
                        <svg v-if="!liked" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.657l-6.828-6.829a4 4 0 010-5.656z"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.657l-6.828-6.829a4 4 0 010-5.656zm9.9 1.758a2 2 0 00-2.829 0L10 7.172l-.243-.242a2 2 0 10-2.828 2.828L10 12.828l3.071-3.07a2 2 0 000-2.829z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ isLiked ? 'Remove from favorites' : 'Add to favorites' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-6 cursor-pointer" @click="handleCardClick">
            <!-- Title -->
            <h3 class="font-bold text-gray-100 mb-5 text-xl">{{ localCityDetails.city }}</h3>
            
            <!-- Weather Info -->
            <div v-if="localCityDetails.weather" class="space-y-4">
                <!-- Main Weather Display -->
                <div class="flex items-start gap-3 pb-4 border-b" style="border-color: #333333;">
                    <img v-if="localCityDetails.weather.icon" :src="localCityDetails.weather.icon" alt="Weather Icon" class="w-12 h-12 flex-shrink-0" />
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline gap-2 mb-1">
                            <span class="text-2xl font-semibold text-gray-100">{{ localCityDetails.weather.temperature }}°C</span>
                        </div>
                        <div class="text-sm text-gray-300 mb-1">{{ localCityDetails.weather.description }}</div>
                        <div class="text-xs text-gray-400">{{ formattedTime }} • {{ localCityDetails.weather.timezone }}</div>
                    </div>
                </div>

                <!-- Weather Details -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Wind -->
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <div>
                            <div class="text-gray-400 text-xs">Wind</div>
                            <div class="text-gray-200 font-medium">{{ localCityDetails.weather.windspeed }} km/h</div>
                        </div>
                    </div>

                    <!-- Direction -->
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.553 2.776A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        <div>
                            <div class="text-gray-400 text-xs">Direction</div>
                            <div class="text-gray-200 font-medium">{{ localCityDetails.weather.winddirection }}°</div>
                        </div>
                    </div>

                    <!-- Elevation -->
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <div>
                            <div class="text-gray-400 text-xs">Elevation</div>
                            <div class="text-gray-200 font-medium">{{ localCityDetails.weather.elevation }}m</div>
                        </div>
                    </div>

                    <!-- Day/Night -->
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <div>
                            <div class="text-gray-400 text-xs">Time</div>
                            <div class="text-gray-200 font-medium">{{ localCityDetails.weather.is_day === 1 ? 'Day' : 'Night' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Currency -->
            <div v-if="localCityDetails.currency" class="mt-4 pt-4 border-t flex items-center gap-2 text-sm" style="border-color: #333333;">
                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <div class="text-gray-400 text-xs">Currency</div>
                    <div class="text-gray-200 font-medium">{{ localCityDetails.currency.symbol }} {{ localCityDetails.currency.code }} ({{ localCityDetails.currency.name }})</div>
                </div>
            </div>

            <!-- Cache Age -->
            <div v-if="localCityDetails.cache_age" class="mt-4 pt-4 border-t flex items-center gap-2 text-sm" style="border-color: #333333;">
                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <div class="text-gray-400 text-xs">Cache Age</div>
                    <div class="text-gray-200 font-medium">{{ localCityDetails.cache_age }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import type { CityDetails, Image } from '../../types';
import { apiService } from '../../services/api.ts';

interface Props {
    cityDetails: CityDetails;
    liked?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    liked: false,
});

const emit = defineEmits<{
    'liked-updated': [payload: { city_name: string; liked: boolean; city_id?: number | null }];
    'city-select': [city: { name: string; latitude?: number | null; longitude?: number | null }];
    'city-details-updated': [cityDetails: CityDetails];
}>();

const isLiked = ref<boolean>(props.liked);
const localCityDetails = ref<CityDetails>(props.cityDetails);

watch(() => props.cityDetails, (newDetails) => {
    localCityDetails.value = newDetails;
}, { deep: true });

const imageUrl = computed((): string => {
    if (typeof localCityDetails.value.image === 'string') {
        return localCityDetails.value.image;
    }
    return (localCityDetails.value.image as Image)?.url || '';
});

const formattedTime = computed((): string => {
    if (!localCityDetails.value.weather?.time) {
        return '';
    }
    const date = new Date(localCityDetails.value.weather.time);
    return date.toLocaleString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        month: 'short',
        day: 'numeric'
    });
});

async function handleToggleLike() {
    const cityName = localCityDetails.value.city;
    if (!cityName) {
        return;
    }

    try {
        if (isLiked.value) {
            const cityId = localCityDetails.value.city_id;
            if (!cityId) {
                return;
            }
            
            await apiService.unlikeCity(cityId);
            isLiked.value = false;
            emit('liked-updated', { city_name: cityName, liked: false, city_id: cityId });
        } else {
            const cityData = await apiService.likeCity(cityName);
            localCityDetails.value = {
                ...localCityDetails.value,
                city_id: cityData.id ?? null,
                latitude: cityData.latitude ?? localCityDetails.value.latitude ?? null,
                longitude: cityData.longitude ?? localCityDetails.value.longitude ?? null
            };
            isLiked.value = true;
            emit('liked-updated', { city_name: cityName, liked: true, city_id: cityData.id });
            emit('city-details-updated', localCityDetails.value);
        }
    } catch (err) {
        console.error('Error toggling like:', err);
    }
}

function handleCardClick() {
    emit('city-select', {
        name: localCityDetails.value.city,
        latitude: localCityDetails.value.latitude ?? null,
        longitude: localCityDetails.value.longitude ?? null
    });
}
</script>
