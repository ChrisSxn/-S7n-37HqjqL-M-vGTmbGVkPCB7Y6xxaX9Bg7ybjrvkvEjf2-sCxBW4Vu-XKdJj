<template>
    <div ref="mapContainer" class="h-full w-full absolute inset-0 bg-black" />
</template>

<script setup lang="ts">
import { ref, onMounted, watch, onBeforeUnmount, nextTick } from 'vue';
import mapboxgl from 'mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import type { SelectedCity } from '../../types';

interface Props {
    selectedCity?: SelectedCity | null;
    mapboxToken?: string | null;
}

const props = withDefaults(defineProps<Props>(), {
    selectedCity: null,
    mapboxToken: null
});

const mapContainer = ref<HTMLDivElement | null>(null);
const map = ref<any>(null);
const resizeObserver = ref<ResizeObserver | null>(null);


const initializeMap = () => {
    if (!props.mapboxToken || !mapContainer.value) return;

    mapboxgl.accessToken = props.mapboxToken;

    map.value = new mapboxgl.Map({
        container: mapContainer.value,
        style: 'mapbox://styles/mapbox/dark-v11',
        center: [5.2913, 52.1326],
        zoom: 6,
        attributionControl: false
    });
};


const setupCityWatcher = () => {
    watch(() => props.selectedCity, (city) => {
        if (city?.latitude && city?.longitude && map.value) {
            map.value.flyTo({
                center: [city.longitude, city.latitude],
                zoom: 12,
                duration: 1000
            });
        }
    }, { immediate: true });
};

const setupResizeObserver = () => {
    if (!mapContainer.value) return;

    resizeObserver.value = new ResizeObserver(() => {
        nextTick(() => map.value?.resize());
    });
    resizeObserver.value.observe(mapContainer.value);
};

onMounted(() => {
    initializeMap();

    map.value?.on('load', () => {
        setupCityWatcher();
        setupResizeObserver();
    });
});

onBeforeUnmount(() => {
    resizeObserver.value?.disconnect();
    map.value?.remove();
});
</script>
