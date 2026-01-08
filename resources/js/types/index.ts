export interface City {
    name: string;
}

export interface Weather {
    temperature: number;
    description: string;
    icon: string;
    timezone: string;
    time: string;
    windspeed: number;
    winddirection: number;
    elevation: number;
    weathercode: number;
    is_day: number;
}

export interface Currency {
    symbol: string;
    code: string;
    name: string;
}

export interface Image {
    url: string;
}

export interface CityDetails {
    city: string;
    city_id?: number | null;
    image: string | Image | null;
    weather: Weather | null;
    currency: Currency | null;
    latitude?: number | null;
    longitude?: number | null;
    cache_age?: string | null;
}

export interface SelectedCity {
    name: string;
    latitude?: number | null;
    longitude?: number | null;
}

