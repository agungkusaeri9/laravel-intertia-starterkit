<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    Search,
    Clock,
    User as UserIcon,
    Monitor,
    MapPin,
    Filter,
    X,
} from 'lucide-vue-next';
import { ref, watch, reactive } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { useDebounceFn } from '@vueuse/core';

interface ActivityLog {
    id: number;
    activity: string;
    description: string;
    data: any;
    ip_address: string;
    user_agent: string;
    created_at: string;
    user?: {
        name: string;
    };
}

interface UserOption {
    id: number;
    name: string;
}

interface Props {
    logs: {
        data: ActivityLog[];
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
        user_id?: string;
        start_date?: string;
        end_date?: string;
    };
    users: UserOption[];
}

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Activity Logs', href: '/activity' },
        ],
    },
});

const filterState = reactive({
    search: props.filters.search || '',
    per_page: props.filters.per_page || '10',
    user_id: props.filters.user_id || 'all',
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || '',
});

const isFilterVisible = ref(false);

const applyFilters = useDebounceFn(() => {
    const params: any = { ...filterState };
    if (params.user_id === 'all') delete params.user_id;

    router.get('/activity', params, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch(
    () => ({ ...filterState }),
    () => {
        applyFilters();
    },
);

const resetFilters = () => {
    filterState.search = '';
    filterState.user_id = 'all';
    filterState.start_date = '';
    filterState.end_date = '';
    filterState.per_page = '10';
};

const formatData = (data: any) => {
    if (!data) return null;
    return JSON.stringify(data, null, 2);
};

const perPageOptions = ['10', '20', '30', '40', '50', '100'];
</script>

<template>
    <Head title="Activity Logs" />

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
                        placeholder="Search activity..."
                        class="pl-8"
                    />
                </div>
                <Button
                    variant="outline"
                    size="icon"
                    @click="isFilterVisible = !isFilterVisible"
                    :class="{ 'bg-muted': isFilterVisible }"
                >
                    <Filter class="h-4 w-4" />
                </Button>
                <Button
                    v-if="
                        Object.values(props.filters).some(
                            (v) => v && v !== 'all' && v !== '10',
                        )
                    "
                    variant="ghost"
                    size="sm"
                    @click="resetFilters"
                    class="h-8 text-xs"
                >
                    <X class="mr-1 h-3 w-3" /> Reset
                </Button>
            </div>

            <div class="flex items-center gap-2">
                <Label class="text-xs whitespace-nowrap text-muted-foreground"
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
        </div>

        <!-- Expanded Filters -->
        <div
            v-if="isFilterVisible"
            class="grid grid-cols-1 gap-4 rounded-lg border bg-muted/30 p-4 md:grid-cols-3"
        >
            <div class="space-y-2">
                <Label class="text-xs">User</Label>
                <Select v-model="filterState.user_id">
                    <SelectTrigger class="h-9">
                        <SelectValue placeholder="All Users" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Users</SelectItem>
                        <SelectItem
                            v-for="user in users"
                            :key="user.id"
                            :value="user.id.toString()"
                        >
                            {{ user.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="space-y-2">
                <Label class="text-xs">Start Date</Label>
                <Input
                    v-model="filterState.start_date"
                    type="date"
                    class="h-9"
                />
            </div>
            <div class="space-y-2">
                <Label class="text-xs">End Date</Label>
                <Input v-model="filterState.end_date" type="date" class="h-9" />
            </div>
        </div>

        <div class="rounded-md border">
            <div class="relative w-full overflow-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr
                            class="border-b transition-colors hover:bg-muted/50"
                        >
                            <th
                                class="h-12 w-[180px] px-4 text-left align-middle font-medium text-muted-foreground"
                            >
                                Time
                            </th>
                            <th
                                class="h-12 w-[150px] px-4 text-left align-middle font-medium text-muted-foreground"
                            >
                                User
                            </th>
                            <th
                                class="h-12 w-[150px] px-4 text-left align-middle font-medium text-muted-foreground"
                            >
                                Activity
                            </th>
                            <th
                                class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                            >
                                Description
                            </th>
                            <th
                                class="h-12 w-[150px] px-4 text-left align-middle font-medium text-muted-foreground"
                            >
                                Info
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <tr
                            v-for="log in logs.data"
                            :key="log.id"
                            class="border-b transition-colors hover:bg-muted/50"
                        >
                            <td class="p-4 align-middle whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <Clock
                                        class="h-3.5 w-3.5 text-muted-foreground"
                                    />
                                    {{
                                        new Date(
                                            log.created_at,
                                        ).toLocaleString()
                                    }}
                                </div>
                            </td>
                            <td class="p-4 align-middle">
                                <div class="flex items-center gap-2">
                                    <UserIcon
                                        class="h-3.5 w-3.5 text-muted-foreground"
                                    />
                                    {{ log.user?.name || 'System' }}
                                </div>
                            </td>
                            <td class="p-4 align-middle">
                                <span
                                    class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-semibold text-primary"
                                >
                                    {{ log.activity }}
                                </span>
                            </td>
                            <td class="p-4 align-middle">
                                <div>{{ log.description }}</div>
                                <details v-if="log.data" class="mt-1">
                                    <summary
                                        class="cursor-pointer text-xs text-muted-foreground hover:underline"
                                    >
                                        View Details
                                    </summary>
                                    <pre
                                        class="mt-2 overflow-x-auto rounded bg-muted p-2 text-[10px]"
                                        >{{ formatData(log.data) }}</pre
                                    >
                                </details>
                            </td>
                            <td class="p-4 align-middle">
                                <div
                                    class="flex flex-col gap-1 text-[11px] text-muted-foreground"
                                >
                                    <div class="flex items-center gap-1">
                                        <MapPin class="h-3 w-3" />
                                        {{ log.ip_address }}
                                    </div>
                                    <div
                                        class="flex max-w-[140px] items-center gap-1 truncate"
                                        :title="log.user_agent"
                                    >
                                        <Monitor class="h-3 w-3" />
                                        {{ log.user_agent }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="logs.data.length === 0">
                            <td
                                colspan="5"
                                class="p-4 text-center text-muted-foreground"
                            >
                                No activity logs found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between gap-4">
            <div class="text-sm text-muted-foreground">
                Showing {{ logs.from }} to {{ logs.to }} of
                {{ logs.total }} entries
            </div>
            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="logs.current_page === 1"
                    @click="router.get(logs.links[0].url, { ...filterState })"
                >
                    Previous
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="logs.current_page === logs.last_page"
                    @click="
                        router.get(logs.links[logs.links.length - 1].url, {
                            ...filterState,
                        })
                    "
                >
                    Next
                </Button>
            </div>
        </div>
    </div>
</template>
