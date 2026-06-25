<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';

interface Role {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    username: string;
    roles: string[];
}

const props = defineProps<{
    user: User;
    roles: Role[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Master Data', href: '#' },
            { title: 'User', href: '/users' },
            { title: 'Edit', href: '#' },
        ],
    },
});

const form = useForm({
    name: props.user.name,
    username: props.user.username,
    password: '',
    roles: props.user.roles || [],
});

const toggleRole = (name: string, checked: boolean | 'indeterminate') => {
    if (checked === true) {
        if (!form.roles.includes(name)) form.roles.push(name);
    } else {
        const index = form.roles.indexOf(name);
        if (index !== -1) form.roles.splice(index, 1);
    }
};

const submit = () => {
    console.log('Form roles:', form.roles);
    form.patch(`/users/${props.user.id}`);
};
</script>

<template>
    <Head title="Edit User" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child>
                <Link href="/users">
                    <ChevronLeft class="h-4 w-4" />
                </Link>
            </Button>
            <h1 class="text-xl font-semibold">Edit User: {{ user.name }}</h1>
        </div>

        <Card class="max-w-2xl">
            <CardHeader>
                <CardTitle>User Details</CardTitle>
                <CardDescription
                    >Update the user's information. Leave password blank to keep
                    current.</CardDescription
                >
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Full Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            placeholder="Enter full name"
                        />
                        <p
                            v-if="form.errors.name"
                            class="text-xs text-destructive"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="username">Username</Label>
                        <Input
                            id="username"
                            v-model="form.username"
                            placeholder="Enter username"
                        />
                        <p
                            v-if="form.errors.username"
                            class="text-xs text-destructive"
                        >
                            {{ form.errors.username }}
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">Password (Optional)</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder="********"
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-xs text-destructive"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="grid gap-4">
                        <Label>Roles</Label>
                        <div
                            class="grid grid-cols-2 gap-4 rounded-lg border bg-muted/30 p-4"
                        >
                            <div
                                v-for="role in roles"
                                :key="role.id"
                                class="flex items-center space-x-2"
                            >
                                <input
                                    type="checkbox"
                                    :id="`role-${role.id}`"
                                    :value="role.name"
                                    v-model="form.roles"
                                    class="size-4 rounded border-gray-300 text-primary focus:ring-primary"
                                />
                                <label
                                    :for="`role-${role.id}`"
                                    class="cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >
                                    {{ role.name }}
                                </label>
                            </div>
                        </div>
                        <p
                            v-if="form.errors.roles"
                            class="text-xs text-destructive"
                        >
                            {{ form.errors.roles }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-4">
                        <Button type="button" variant="outline" as-child>
                            <Link href="/users">Cancel</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{
                                form.processing ? 'Updating...' : 'Update User'
                            }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
