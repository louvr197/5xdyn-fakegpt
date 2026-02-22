<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
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
import { home } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    MessageSquare,
    Settings2,
    Home,
    BookOpen,
    Folder,
    Briefcase
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Home',
        href: home(),
        icon: Home,
    },
    {
        title: 'Chat',
        href: '/conversations',
        icon: MessageSquare,
    },
    {
        title: 'Instructions IA',
        href: '/custom-instructions',
        icon: Settings2,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="bg-white/80 backdrop-blur-xl border-r border-blue-200/50 shadow-xl">
        <SidebarHeader class="border-b border-blue-200/50 bg-gradient-to-r from-blue-50/50 to-indigo-50/50">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="home()" class="flex items-center gap-3 group" aria-label="Retour à l'accueil - CVBuilder Pro">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-200" aria-hidden="true">
                                <Briefcase class="h-4 w-4" aria-hidden="true" />
                            </div>
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-bold text-gray-900">CVBuilder Pro</span>
                                <span class="truncate text-xs font-medium text-blue-600">Coach Carrière IA</span>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
