<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Plus, Search, Trash2, AlertTriangle } from 'lucide-vue-next';
import { ref, watch, reactive } from 'vue';
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
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { useDebounceFn } from '@vueuse/core';

interface User {
    id: number;
    name: string;
    username: string;
    created_at: string;
    roles: string[];
}

interface Props {
    users: {
        data: User[];
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

// Breadcrumbs definition
defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Master Data', href: '#' },
            { title: 'User', href: '/users' },
        ],
    },
});

const filterState = reactive({
    search: props.filters.search || '',
    per_page: props.filters.per_page || '10',
});

// Search functionality
const debouncedSearch = useDebounceFn(() => {
    router.get(
        '/users',
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

// Delete confirmation handling
const isDeleteDialogOpen = ref(false);
const userToDelete = ref<User | null>(null);

const confirmDelete = (user: User) => {
    userToDelete.value = user;
    isDeleteDialogOpen.value = true;
};

const executeDelete = () => {
    if (userToDelete.value) {
        router.delete(`/users/${userToDelete.value.id}`, {
            onSuccess: () => {
                isDeleteDialogOpen.value = false;
                userToDelete.value = null;
            },
        });
    }
};

const perPageOptions = ['10', '20', '30', '40', '50', '100'];
</script>

<template>
    <Head title="User Management" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div
            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <div class="flex flex-1 items-center gap-2">
                <div class="relative w-full max-w-sm">
                    <Search
                        class="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground"
                    />
                    <Input
                        v-model="filterState.search"
                        type="search"
                        placeholder="Search users..."
                        class="pl-8"
                    />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <Label
                        class="text-xs whitespace-nowrap text-muted-foreground"
                        >Show</Label
                    >
                    <Select v-model="filterState.per_page">
                        <SelectTrigger class="h-8 w-[70px] text-xs">
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
                </div>
                <Button as-child>
                    <Link href="/users/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Create User
                    </Link>
                </Button>
            </div>
        </div>

        <div class="rounded-md border">
            <div class="relative w-full overflow-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr
                            class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                        >
                            <th
                                class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                            >
                                Name
                            </th>
                            <th
                                class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                            >
                                Username
                            </th>
                            <th
                                class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                            >
                                Roles
                            </th>
                            <th
                                class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                            >
                                Created At
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
                            v-for="user in users.data"
                            :key="user.id"
                            class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                        >
                            <td class="p-4 align-middle font-medium">
                                {{ user.name }}
                            </td>
                            <td class="p-4 align-middle">
                                {{ user.username }}
                            </td>
                            <td class="p-4 align-middle">
                                <div class="flex flex-wrap gap-1">
                                    <Badge
                                        v-for="role in user.roles"
                                        :key="role"
                                        variant="secondary"
                                        class="text-[10px]"
                                    >
                                        {{ role }}
                                    </Badge>
                                </div>
                            </td>
                            <td class="p-4 align-middle">
                                {{
                                    new Date(
                                        user.created_at,
                                    ).toLocaleDateString()
                                }}
                            </td>
                            <td class="p-4 text-right align-middle">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                    >
                                        <Link :href="`/users/${user.id}/edit`">
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="text-destructive hover:text-destructive"
                                        @click="confirmDelete(user)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td
                                colspan="5"
                                class="p-4 text-center text-muted-foreground"
                            >
                                No users found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between gap-4">
            <div class="text-sm text-muted-foreground">
                Showing {{ users.from }} to {{ users.to }} of
                {{ users.total }} entries
            </div>
            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="users.current_page === 1"
                    @click="router.get(users.links[0].url, { ...filterState })"
                >
                    Previous
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="users.current_page === users.last_page"
                    @click="
                        router.get(users.links[users.links.length - 1].url, {
                            ...filterState,
                        })
                    "
                >
                    Next
                </Button>
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
                        Are you sure you want to delete user
                        <strong>{{ userToDelete?.name }}</strong
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
                        >Delete User</Button
                    >
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
