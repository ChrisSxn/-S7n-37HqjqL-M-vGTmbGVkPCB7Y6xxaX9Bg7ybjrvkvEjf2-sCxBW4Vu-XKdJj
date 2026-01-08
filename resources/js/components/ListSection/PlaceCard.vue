<template>
    <div class="bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-md hover:shadow-lg transition-shadow">
        <div class="p-4">
            <div class="aspect-video bg-gray-200 relative rounded-xl overflow-hidden">
                <img :src="imageUrl" :alt="place.name" class="w-full h-full object-cover" />
            </div>
        </div>
        
        <div class="p-4">
            <h3 class="font-bold text-gray-900 mb-2 text-lg">{{ place.name }}</h3>
            <p class="text-sm text-gray-900 mb-4">{{ place.description }}</p>
            
            <div class="flex items-center space-x-4 text-sm text-gray-900">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ place.distance }}</span>
                </div>
                
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                    </svg>
                    <span>{{ place.likes }}</span>
                </div>
                
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span>{{ place.rating }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Place {
    id?: number;
    name: string;
    description: string;
    distance: string;
    likes: number;
    rating: number;
}

interface Props {
    place: Place;
}

const props = defineProps<Props>();

const imageUrl = computed(() => {
    const seed = props.place.id || 1;
    return `https://picsum.photos/seed/${seed}/800/600`;
});
</script>
