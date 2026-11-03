import type { Auth } from '@/types/auth';
import type { SharedTranslations } from '@/types/i18n';

declare module 'react' {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            locale: string;
            locales: string[];
            translations: SharedTranslations;
            [key: string]: unknown;
        };
    }
}
