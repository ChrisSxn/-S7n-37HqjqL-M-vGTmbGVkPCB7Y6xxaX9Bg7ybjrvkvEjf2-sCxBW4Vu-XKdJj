import type { City, CityDetails } from '../types';

interface ApiResponse<T> {
    status: 'success' | 'error';
    message?: string;
    data?: T;
}

interface CityLikeResponse {
    readonly id: number;
    readonly name: string;
    readonly latitude?: number | null;
    readonly longitude?: number | null;
}

function isCity(value: unknown): value is City {
    return (
        typeof value === 'object' &&
        value !== null &&
        'name' in value &&
        typeof (value as City).name === 'string'
    );
}

function isCityArray(value: unknown): value is City[] {
    return Array.isArray(value) && value.every(isCity);
}

function isCityDetails(value: unknown): value is CityDetails {
    return (
        typeof value === 'object' &&
        value !== null &&
        'city' in value &&
        typeof (value as CityDetails).city === 'string'
    );
}

function isCityLikeResponse(value: unknown): value is CityLikeResponse {
    return (
        typeof value === 'object' &&
        value !== null &&
        'id' in value &&
        typeof (value as CityLikeResponse).id === 'number' &&
        'name' in value &&
        typeof (value as CityLikeResponse).name === 'string'
    );
}

class ApiService {
    private baseUrl = '/api';

    private async request<T>(
        endpoint: string,
        options: RequestInit = {}
    ): Promise<ApiResponse<T>> {
        const response = await fetch(`${this.baseUrl}${endpoint}`, {
            ...options,
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...options.headers,
            },
        });

        if (!response.ok) {
            const error = await response.json().catch(() => ({ message: 'Request failed' }));
            throw new Error(error.message || 'Request failed');
        }

        return response.json();
    }

    async searchCities(searchTerm: string): Promise<City[]> {
        const response = await this.request<City[]>(`/cities/${encodeURIComponent(searchTerm)}/search`, {
            method: 'GET',
        });

        if (response.status !== 'success' || !response.data) {
            throw new Error(response.message || 'Failed to search cities');
        }

        if (!isCityArray(response.data)) {
            throw new Error('Invalid response format: expected City[]');
        }

        return response.data;
    }

    async getCityDetailsByName(cityName: string): Promise<CityDetails> {
        const response = await this.request<CityDetails>(`/cities/${encodeURIComponent(cityName)}/details`, {
            method: 'GET',
        });

        if (response.status !== 'success' || !response.data) {
            throw new Error(response.message || 'Failed to fetch city details');
        }

        if (!isCityDetails(response.data)) {
            throw new Error('Invalid response format: expected CityDetails');
        }

        return response.data;
    }

    getLikedCitiesStream(): Promise<ReadableStream<Uint8Array>> {
        return fetch(`${this.baseUrl}/cities`, {
            credentials: 'include',
            headers: { Accept: 'application/x-ndjson' },
        }).then((response) => {
            if (!response.ok) {
                throw new Error('Failed to load favorites');
            }

            if (!response.body) {
                throw new Error('Response body is null');
            }

            return response.body;
        });
    }

    async likeCity(cityName: string): Promise<CityLikeResponse> {
        const response = await this.request<CityLikeResponse>('/cities', {
            method: 'POST',
            body: JSON.stringify({ city_name: cityName }),
        });

        if (response.status !== 'success' || !response.data) {
            throw new Error(response.message || 'Failed to like city');
        }

        if (!isCityLikeResponse(response.data)) {
            throw new Error('Invalid response format: expected CityLikeResponse');
        }

        return response.data;
    }

    async unlikeCity(cityId: number): Promise<void> {
        const response = await this.request<void>(`/cities/${cityId}`, {
            method: 'DELETE',
        });

        if (response.status !== 'success') {
            throw new Error(response.message || 'Failed to unlike city');
        }
    }

    async login(email: string, password: string): Promise<void> {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const response = await fetch(`${this.baseUrl}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include',
            body: JSON.stringify({ email, password }),
        });

        if (!response.ok) {
            const error = await response.json().catch(() => ({ message: 'Login failed' }));
            throw new Error(error.message || 'Login failed');
        }
    }

    async logout(): Promise<void> {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const response = await fetch(`${this.baseUrl}/logout`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include',
        });

        if (!response.ok) {
            throw new Error('Logout failed');
        }
    }
}

export const apiService = new ApiService();

