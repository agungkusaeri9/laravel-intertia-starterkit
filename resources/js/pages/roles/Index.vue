<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Plus, Trash2, Shield, AlertTriangle } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Badge } from '@/components/ui/badge';

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
    roles: Role[];
}

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Roles', href: '/roles' },
        ],
    },
});

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
</script>

<template>
    <Head title="Roles Management" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Roles Management</h1>
            <Button as-child>
                <Link href="/roles/create">
                    <Plus class="mr-2 h-4 w-4" />
                    Create Role
                </Link>
            </Button>
        </div>

        <div class="rounded-md border">
            <div class="relative w-full overflow-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr
                            class="border-b transition-colors hover:bg-muted/50"
                        >
                            <th
                                class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                            >
                                Name
                            </th>
                            <th
                                class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                            >
                                Permissions
                            </th>
                            <th
                                class="h-12 px-4 text-right align-middle font-medium text-muted-foreground"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <tr
                            v-for="role in roles"
                            :key="role.id"
                            class="border-b transition-colors hover:bg-muted/50"
                        >
                            <td class="p-4 align-middle font-medium">
                                <div class="flex items-center gap-2">
                                    <Shield class="h-4 w-4 text-primary" />
                                    {{ role.name }}
                                </div>
                            </td>
                            <td class="p-4 align-middle">
                                <div class="flex flex-wrap gap-1">
                                    <Badge
                                        v-if="role.name === 'super admin'"
                                        variant="default"
                                        >All Permissions</Badge
                                    >
                                    <template v-else>
                                        <Badge
                                            v-for="perm in role.permissions"
                                            :key="perm.id"
                                            variant="secondary"
                                            class="text-[10px]"
                                        >
                                            {{ perm.name }}
                                        </Badge>
                                        <span
                                            v-if="role.permissions.length === 0"
                                            class="text-xs text-muted-foreground italic"
                                        >
                                            No permissions assigned
                                        </span>
                                    </template>
                                </div>
                            </td>
                            <td class="p-4 text-right align-middle">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                        :disabled="role.name === 'super admin'"
                                    >
                                        <Link :href="`/roles/${role.id}/edit`">
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="text-destructive hover:text-destructive"
                                        @click="confirmDelete(role)"
                                        :disabled="role.name === 'super admin'"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                        <DialogTitle>Confirm Deletion</DialogTitle>
                    </div>
                    <DialogDescription>
                        Are you sure you want to delete role
                        <strong>{{ roleToDelete?.name }}</strong
                        >? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2 sm:gap-0">
                    <Button
                        variant="outline"
                        @click="isDeleteDialogOpen = false"
                        >Cancel</Button
                    >
                    <Button variant="destructive" @click="executeDelete"
                        >Delete Role</Button
                    >
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
