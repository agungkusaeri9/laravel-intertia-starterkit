<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Key } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

interface Permission {
    id: number;
    name: string;
}

interface Props {
    permissions: Permission[];
}

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Permissions', href: '/permissions' },
        ],
    },
});
</script>

<template>
    <Head title="Permissions List" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Permissions List</h1>
        </div>

        <div class="rounded-md border">
            <div class="relative w-full overflow-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr class="border-b transition-colors hover:bg-muted/50">
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[80px]">ID</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Permission Name</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Guard</th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <tr
                            v-for="perm in permissions"
                            :key="perm.id"
                            class="border-b transition-colors hover:bg-muted/50"
                        >
                            <td class="p-4 align-middle font-medium">#{{ perm.id }}</td>
                            <td class="p-4 align-middle">
                                <div class="flex items-center gap-2">
                                    <Key class="h-4 w-4 text-primary" />
                                    <Badge variant="outline">{{ perm.name }}</Badge>
                                </div>
                            </td>
                            <td class="p-4 align-middle">
                                <Badge variant="secondary">web</Badge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
