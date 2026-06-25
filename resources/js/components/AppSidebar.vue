<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Database,
    LayoutGrid,
    User,
    History,
    ShieldCheck,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Master Data',
        href: '#',
        icon: Database,
        items: [
            {
                title: 'User',
                href: '/users',
                icon: User,
            },
        ],
    },
    {
        title: 'Role & Permission',
        href: '#',
        icon: ShieldCheck,
        items: [
            {
                title: 'Roles',
                href: '/roles',
            },
            {
                title: 'Permissions',
                href: '/permissions',
            },
        ],
    },
    {
        title: 'Activity',
        href: '/activity',
        icon: History,
    },
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
