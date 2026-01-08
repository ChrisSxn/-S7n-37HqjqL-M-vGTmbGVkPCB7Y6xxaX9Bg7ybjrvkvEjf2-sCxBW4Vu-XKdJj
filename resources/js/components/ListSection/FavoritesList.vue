<template>
    <section class="w-[560px] border-r flex flex-col flex-shrink-0 overflow-hidden" style="background-color: var(--sidebar-bg); border-color: #333333;">
        <div class="px-6 pt-6 pb-2 flex-shrink-0 h-[90px]">
            <h2 class="text-2xl font-semibold text-gray-100">Favorites</h2>
        </div>

        <div class="border-b" style="border-color: #333333;"></div>

        <div class="flex-1 overflow-y-auto">
            <div v-if="!loading && !favorites.length" class="flex items-center justify-center h-full">
                <div class="text-center text-gray-400">
                    <p class="text-lg mb-2">No favorites yet</p>
                    <p class="text-sm">Your favorite places will appear here</p>
                </div>
            </div>

            <div v-else class="px-4 pt-2 pb-4 space-y-4">
                <CityCard
                    v-for="(city, idx) in favorites"
                    :key="idx"
                    :city-details="city"
                    :liked="true"
                    @liked-updated="handleLikedUpdated"
                    @city-select="handleCitySelect"
                />
                <div v-if="loading" class="flex items-center justify-center py-4">
                    <div class="text-gray-400 text-sm">Loading...</div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import CityCard from './CityCard.vue';
import type { CityDetails } from '../../types';
import { apiService } from '../../services/api.ts';

interface Props {
    active?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    active: false
});

const emit = defineEmits<{
    'city-select': [city: { name: string; latitude?: number | null; longitude?: number | null }];
}>();

const favorites = ref<CityDetails[]>([]);
const loading = ref(false);
let currentReader: ReadableStreamDefaultReader<Uint8Array> | null = null;

const handleLikedUpdated = (payload: { city_name: string; liked: boolean }) => {
    if (!payload.liked) {
        favorites.value = favorites.value.filter((c) => c.city !== payload.city_name);
    }
};

const handleCitySelect = (city: { name: string; latitude?: number | null; longitude?: number | null }) => {
    emit('city-select', city);
};

const parseLine = (line: string) => {
    const trimmed = line.trim();
    if (!trimmed) return;
    
    try {
        const data = JSON.parse(trimmed);
        if (data.status === 'success' && data.data) {
            favorites.value.push(data.data as CityDetails);
        }
    } catch {
    }
};

const loadFavorites = async () => {
    if (currentReader) {
        await currentReader.cancel().catch(() => {});
        currentReader = null;
    }

    loading.value = true;
    favorites.value = [];

    try {
        const stream = await apiService.getLikedCitiesStream();
        const reader = stream.getReader();
        currentReader = reader;
        const decoder = new TextDecoder();
        let buffer = '';

        while (true) {
            const { done, value } = await reader.read();
            if (done) break;

            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split('\n');
            buffer = lines.pop() || '';

            lines.forEach(parseLine);
        }

        if (buffer.trim()) {
            parseLine(buffer);
        }

        currentReader = null;
    } catch {
        favorites.value = [];
    } finally {
        loading.value = false;
    }
};

onBeforeUnmount(() => {
    currentReader?.cancel().catch(() => {});
});

onMounted(() => {
    if (props.active) {
        loadFavorites();
    }
});

watch(() => props.active, (isActive) => {
    if (isActive) {
        loadFavorites();
    }
});
</script>

