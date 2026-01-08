<template>
    <main class="flex-1 min-w-0 overflow-hidden relative bg-black">
        <Mapbox :selected-city="selectedCity" :mapbox-token="mapboxToken" />
    </main>
</template>

<script setup lang="ts">
import Mapbox from './Mapbox.vue';
import type { SelectedCity } from '../../types';

interface Props {
    selectedCity?: SelectedCity | null;
    mapboxToken?: string | null;
}

withDefaults(defineProps<Props>(), {
    selectedCity: null,
    mapboxToken: null
});
</script>
