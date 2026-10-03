<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Shield } from 'lucide-vue-next';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
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

const props = defineProps<{
    role: Role;
    permissions: Permission[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Peran & Hak Akses', href: '#' },
    { title: 'Manajemen Peran', href: '/roles' },
    { title: `Edit Peran: ${props.role.name}`, href: '#' },
];

const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions.map((p) => p.name),
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
    <Head :title="`Edit Peran: ${role.name}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Breadcrumbs in Content -->
        <Breadcrumbs :breadcrumbs="breadcrumbs" />

        <div class="space-y-1">
            <h1 class="text-2xl font-bold tracking-tight text-foreground">
                Edit Peran: {{ role.name }}
            </h1>
            <p class="text-sm text-muted-foreground">
                Perbarui nama peran dan modifikasi hak akses (permissions) yang diberikan.
            </p>
        </div>

        <Card class="w-full">
            <CardHeader>
                <CardTitle>Informasi Peran</CardTitle>
                <CardDescription>Sesuaikan nama peran dan hak akses sistem.</CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Nama Peran</Label>
                        <Input id="name" v-model="form.name" placeholder="Masukkan nama peran" />
                        <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
                    </div>

                    <div class="grid gap-4">
                        <Label>Hak Akses / Permissions</Label>
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
                            <Link href="/roles">Batal</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <Shield class="mr-2 h-4 w-4" />
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
