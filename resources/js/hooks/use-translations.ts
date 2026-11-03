import { usePage } from '@inertiajs/react';
import type { SharedTranslations } from '@/types';

export function useTranslations() {
    const { translations } = usePage<{ translations: SharedTranslations }>()
        .props;

    return translations;
}
