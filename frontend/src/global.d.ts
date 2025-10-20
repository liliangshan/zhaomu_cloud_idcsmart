declare interface Window{
    VITE_BASE_DOMAIN: String;
    APP_CONFIG: {
        isAdmin: boolean;
        version: string;
        environment: string;
        customParam: string | null;
    };
}