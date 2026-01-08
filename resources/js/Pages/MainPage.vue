<template>
    <div class="h-screen w-screen flex overflow-hidden" style="background-color: var(--sidebar-bg);">
        <Sidebar :active-view="view" @view-change="handleViewChange" />
        <FavoritesList v-show="view === 'favorites'" :active="view === 'favorites'" @city-select="handleCitySelect" />
        <PlacesList v-show="view === 'explore'" :selected-city="selectedCity" @city-select="handleCitySelect" />
        <ContentArea :selected-city="selectedCity" :mapbox-token="mapboxToken" />
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Sidebar from '../components/Sidebar.vue';
import PlacesList from '../components/ListSection/PlacesList.vue';
import FavoritesList from '../components/ListSection/FavoritesList.vue';
import ContentArea from '../components/ContentSection/ContentArea.vue';
import type { City, SelectedCity } from '../types';

interface Props {
    view?: 'home' | 'explore' | 'favorites';
}

const props = withDefaults(defineProps<Props>(), {
    view: 'home'
});

const page = usePage();
const mapboxToken = (page.props as { mapboxToken?: string | null }).mapboxToken ?? null;

const selectedCity = ref<SelectedCity | null>(null);

const handleViewChange = (newView: 'home' | 'explore' | 'favorites') => {
    let url = '/';
    if (newView === 'favorites') {
        url = '/cities';
    } else if (newView === 'explore') {
        url = '/explore';
    }
    
    router.get(url, {}, {
        only: ['view'],
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            if (newView === 'favorites' || newView === 'home') {
                selectedCity.value = null;
            }
        }
    });
};

watch(() => props.view, (newView) => {
    if (newView === 'favorites' || newView === 'home') {
        selectedCity.value = null;
    }
});

const handleCitySelect = (city: City | SelectedCity) => {
    if ('latitude' in city && 'longitude' in city) {
        selectedCity.value = city as SelectedCity;
    } else {
        selectedCity.value = {
            name: city.name,
            latitude: null,
            longitude: null
        };
    }
};
</script>

