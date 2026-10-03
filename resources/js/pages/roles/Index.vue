<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Edit,
    Plus,
    Trash2,
    Shield,
    AlertTriangle,
    Search,
    X,
    ChevronLeft,
    ChevronRight,
} from 'lucide-vue-next';
import { ref, watch, reactive } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { useDebounceFn } from '@vueuse/core';
import type { BreadcrumbItem } from '@/types';

interface Permission {
    id: number;
    name: string;
}

interface Role {
    id: number;
    name: string;
    permissions: Permission[];
}

interface Props {
    roles: {
        data: Role[];
        links: any[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
    };
    filters: {
        search?: string;
        per_page?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Peran & Hak Akses', href: '#' },
    { title: 'Manajemen Peran', href: '/roles' },
];

const filterState = reactive({
    search: props.filters.search || '',
    per_page: props.filters.per_page || '10',
});

// Search functionality
const debouncedSearch = useDebounceFn(() => {
    router.get(
        '/roles',
        { ...filterState },
        { preserveState: true, replace: true },
    );
}, 300);

watch(
    () => ({ ...filterState }),
    () => {
        debouncedSearch();
    },
);

const clearSearch = () => {
    filterState.search = '';
};

// Delete confirmation handling
const isDeleteDialogOpen = ref(false);
const roleToDelete = ref<Role | null>(null);

const confirmDelete = (role: Role) => {
    roleToDelete.value = role;
    isDeleteDialogOpen.value = true;
};

const executeDelete = () => {
    if (roleToDelete.value) {
        router.delete(`/roles/${roleToDelete.value.id}`, {
            onSuccess: () => {
                isDeleteDialogOpen.value = false;
                roleToDelete.value = null;
            },
        });
    }
};

const perPageOptions = ['10', '20', '30', '40', '50', '100'];
</script>

<template>
    <Head title="Manajemen Peran" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Breadcrumbs in Content -->
        <Breadcrumbs :breadcrumbs="breadcrumbs" />

        <!-- Page Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
                    Manajemen Peran
                </h1>
                <p class="text-sm text-muted-foreground">
                    Kelola peran pengguna dan konfigurasi hak akses izin sistem.
                </p>
            </div>

            <Button as-child class="shrink-0">
                <Link href="/roles/create">
                    <Plus class="mr-2 h-4 w-4" />
                    Tambah Peran
                </Link>
            </Button>
        </div>

        <!-- DataTable Card Container -->
        <div class="rounded-xl border bg-card shadow-xs">
            <!-- DataTable Top Controls -->
            <div
                class="flex flex-col gap-4 border-b p-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <span>Tampilkan</span>
                    <Select v-model="filterState.per_page">
                        <SelectTrigger class="h-8 w-[72px] text-xs">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="opt in perPageOptions"
                                :key="opt"
                                :value="opt"
                            >
                                {{ opt }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span>data</span>
                </div>

                <div class="relative w-full max-w-xs sm:w-64">
                    <Search
                        class="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground"
                    />
                    <Input
                        v-model="filterState.search"
                        type="search"
                        placeholder="Cari peran..."
                        class="h-8 pr-8 pl-8 text-xs"
                    />
                    <button
                        v-if="filterState.search"
                        type="button"
                        class="absolute top-2 right-2 text-muted-foreground hover:text-foreground"
                        @click="clearSearch"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="relative w-full overflow-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead class="bg-muted/50 [&_tr]:border-b">
                        <tr class="border-b transition-colors">
                            <th
                                class="h-10 w-16 px-4 text-center align-middle text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                            >
                                No
                            </th>
                            <th
                                class="h-10 px-4 text-left align-middle text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                            >
                                Nama Peran
                            </th>
                            <th
                                class="h-10 px-4 text-left align-middle text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                            >
                                Hak Akses / Permissions
                            </th>
                            <th
                                class="h-10 px-4 text-right align-middle text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border [&_tr:last-child]:border-0">
                        <tr
                            v-for="(role, index) in roles.data"
                            :key="role.id"
                            class="transition-colors hover:bg-muted/40"
                        >
                            <td class="p-3.5 text-center align-middle text-xs font-medium text-muted-foreground">
                                {{ (roles.from || 1) + index }}
                            </td>
                            <td class="p-3.5 align-middle font-medium">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                        <Shield class="h-4 w-4" />
                                    </div>
                                    <span class="font-semibold text-foreground capitalize">{{ role.name }}</span>
                                </div>
                            </td>
                            <td class="p-3.5 align-middle">
                                <div class="flex flex-wrap gap-1">
                                    <Badge
                                        v-if="role.name === 'super admin'"
                                        variant="default"
                                        class="bg-emerald-600 hover:bg-emerald-700"
                                    >
                                        Semua Hak Akses
                                    </Badge>
                                    <template v-else>
                                        <Badge
                                            v-for="perm in role.permissions"
                                            :key="perm.id"
                                            variant="secondary"
                                            class="text-[11px] font-medium"
                                        >
                                            {{ perm.name }}
                                        </Badge>
                                        <span
                                            v-if="role.permissions.length === 0"
                                            class="text-xs text-muted-foreground italic"
                                        >
                                            Belum ada hak akses
                                        </span>
                                    </template>
                                </div>
                            </td>
                            <td class="p-3.5 text-right align-middle">
                                <div class="flex justify-end gap-1">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8 text-muted-foreground hover:text-foreground"
                                        as-child
                                        :disabled="role.name === 'super admin'"
                                        title="Edit Peran"
                                    >
                                        <Link :href="`/roles/${role.id}/edit`">
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8 text-muted-foreground hover:text-destructive"
                                        :disabled="role.name === 'super admin'"
                                        title="Hapus Peran"
                                        @click="confirmDelete(role)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="roles.data.length === 0">
                            <td
                                colspan="4"
                                class="p-10 text-center"
                            >
                                <div class="flex flex-col items-center justify-center gap-2 text-muted-foreground">
                                    <div class="rounded-full bg-muted p-3">
                                        <Shield class="h-6 w-6" />
                                    </div>
                                    <p class="font-medium text-foreground">Data peran tidak ditemukan</p>
                                    <p class="text-xs">
                                        {{ filterState.search ? 'Coba ubah kata kunci pencarian Anda.' : 'Belum ada data peran yang dibuat.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- DataTable Footer / Pagination -->
            <div
                class="flex flex-col gap-3 border-t p-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-xs text-muted-foreground">
                    Menampilkan {{ roles.from || 0 }} sampai {{ roles.to || 0 }} dari
                    {{ roles.total }} data
                </div>

                <!-- Numbered Pagination -->
                <div class="flex flex-wrap items-center gap-1">
                    <template v-for="(link, i) in roles.links" :key="i">
                        <!-- Previous button -->
                        <Button
                            v-if="i === 0"
                            variant="outline"
                            size="icon"
                            class="h-8 w-8"
                            :disabled="!link.url"
                            @click="link.url && router.get(link.url, { ...filterState }, { preserveState: true })"
                            aria-label="Halaman sebelumnya"
                        >
                            <ChevronLeft class="h-4 w-4" />
                        </Button>
                        <!-- Next button -->
                        <Button
                            v-else-if="i === roles.links.length - 1"
                            variant="outline"
                            size="icon"
                            class="h-8 w-8"
                            :disabled="!link.url"
                            @click="link.url && router.get(link.url, { ...filterState }, { preserveState: true })"
                            aria-label="Halaman berikutnya"
                        >
                            <ChevronRight class="h-4 w-4" />
                        </Button>
                        <!-- Ellipsis -->
                        <span
                            v-else-if="link.label === '...'"
                            class="px-2 text-xs text-muted-foreground"
                        >
                            ...
                        </span>
                        <!-- Page Number buttons -->
                        <Button
                            v-else
                            :variant="link.active ? 'default' : 'outline'"
                            size="sm"
                            class="h-8 min-w-8 px-2.5 text-xs font-medium"
                            :disabled="!link.url"
                            @click="link.url && router.get(link.url, { ...filterState }, { preserveState: true })"
                        >
                            {{ link.label }}
                        </Button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Dialog
            :open="isDeleteDialogOpen"
            @update:open="isDeleteDialogOpen = $event"
        >
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <div class="mb-2 flex items-center gap-2 text-destructive">
                        <AlertTriangle class="h-5 w-5" />
                        <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    </div>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus peran
                        <strong>{{ roleToDelete?.name }}</strong
                        >? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-3 sm:gap-3">
                    <Button
                        variant="outline"
                        @click="isDeleteDialogOpen = false"
                    >
                        Batal
                    </Button>
                    <Button
                        variant="destructive"
                        @click="executeDelete"
                    >
                        Hapus Peran
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
