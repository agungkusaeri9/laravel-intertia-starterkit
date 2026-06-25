<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, Shield } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';

interface Permission {
    id: number;
    name: string;
}

interface Role {
    id: number;
    name: string;
    permissions: Permission[];
}

const props = defineProps<{
    role: Role;
    permissions: Permission[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Roles', href: '/roles' },
            { title: 'Edit', href: '#' },
        ],
    },
});

const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions.map(p => p.name),
});

const togglePermission = (name: string) => {
    const index = form.permissions.indexOf(name);
    if (index === -1) {
        form.permissions.push(name);
    } else {
        form.permissions.splice(index, 1);
    }
};

const submit = () => {
    form.patch(`/roles/${props.role.id}`);
};
</script>

<template>
    <Head title="Edit Role" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child>
                <Link href="/roles">
                    <ChevronLeft class="h-4 w-4" />
                </Link>
            </Button>
            <h1 class="text-xl font-semibold">Edit Role: {{ role.name }}</h1>
        </div>

        <Card class="max-w-4xl">
            <CardHeader>
                <CardTitle>Role Details</CardTitle>
                <CardDescription>Update role name and modify assigned permissions.</CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Role Name</Label>
                        <Input id="name" v-model="form.name" placeholder="Enter role name" />
                        <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
                    </div>

                    <div class="grid gap-4">
                        <Label>Permissions</Label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border rounded-lg p-4 bg-muted/30">
                            <div
                                v-for="perm in permissions"
                                :key="perm.id"
                                class="flex items-center space-x-2"
                            >
                                <Checkbox
                                    :id="`perm-${perm.id}`"
                                    :checked="form.permissions.includes(perm.name)"
                                    @update:checked="togglePermission(perm.name)"
                                />
                                <label
                                    :for="`perm-${perm.id}`"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer"
                                >
                                    {{ perm.name }}
                                </label>
                            </div>
                        </div>
                        <p v-if="form.errors.permissions" class="text-xs text-destructive">{{ form.errors.permissions }}</p>
                    </div>

                    <div class="flex justify-end gap-4">
                        <Button type="button" variant="outline" as-child>
                            <Link href="/roles">Cancel</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <Shield class="mr-2 h-4 w-4" />
                            {{ form.processing ? 'Updating...' : 'Update Role' }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
