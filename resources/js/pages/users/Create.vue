<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
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
import type { BreadcrumbItem } from '@/types';

interface Role {
    id: number;
    name: string;
}

defineProps<{
    roles: Role[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Data Master', href: '#' },
    { title: 'Manajemen Pengguna', href: '/users' },
    { title: 'Tambah Pengguna', href: '/users/create' },
];

const form = useForm({
    name: '',
    username: '',
    password: '',
    roles: [] as string[],
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
    form.post('/users');
};
</script>

<template>
    <Head title="Tambah Pengguna" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <Breadcrumbs :breadcrumbs="breadcrumbs" />

        <div class="space-y-1">
            <h1 class="text-2xl font-bold tracking-tight text-foreground">
                Tambah Pengguna Baru
            </h1>
            <p class="text-sm text-muted-foreground">
                Tambahkan akun pengguna baru ke dalam sistem.
            </p>
        </div>

        <Card class="w-full">
            <CardHeader>
                <CardTitle>Informasi Pengguna</CardTitle>
                <CardDescription
                    >Masukkan data lengkap untuk akun pengguna baru.</CardDescription
                >
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Nama Lengkap</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            placeholder="Masukkan nama lengkap"
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
                            placeholder="Masukkan username"
                        />
                        <p
                            v-if="form.errors.username"
                            class="text-xs text-destructive"
                        >
                            {{ form.errors.username }}
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">Kata Sandi</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder="Masukkan kata sandi (min. 8 karakter)"
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-xs text-destructive"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="grid gap-4">
                        <Label>Hak Akses / Peran (Roles)</Label>
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
                                    class="cursor-pointer text-sm leading-none font-medium capitalize peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
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
                            <Link href="/users">Batal</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{
                                form.processing ? 'Menyimpan...' : 'Simpan Pengguna'
                            }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
