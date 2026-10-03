import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import type { FlashToast } from '@/types/ui';

interface FlashProps {
    success?: string | null;
    error?: string | null;
    warning?: string | null;
    info?: string | null;
    toast?: FlashToast | null;
}

export function handleFlash(flash?: FlashProps | null): void {
    if (!flash) return;

    if (flash.success) {
        toast.success(flash.success);
    }
    if (flash.error) {
        toast.error(flash.error);
    }
    if (flash.warning) {
        toast.warning(flash.warning);
    }
    if (flash.info) {
        toast.info(flash.info);
    }
    if (flash.toast) {
        toast[flash.toast.type](flash.toast.message);
    }
}

export function initializeFlashToast(): void {
    router.on('success', (event) => {
        const flash = (event.detail.page.props as { flash?: FlashProps }).flash;
        handleFlash(flash);
    });

    router.on('flash' as any, (event: any) => {
        const flash = event.detail?.flash;
        handleFlash(flash);
    });
}
