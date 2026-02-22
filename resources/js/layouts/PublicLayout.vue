<script setup lang="ts">
import AppHeader from '@/components/AppHeader.vue';
import { dashboard, login, register } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import { Briefcase } from 'lucide-vue-next';

const page = usePage();
const user = page.props.auth.user;
const canRegister = page.props.canRegister ?? true;
</script>

<template>
    <div class="flex min-h-screen flex-col bg-linear-to-br from-white via-blue-50/30 to-indigo-50/40 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
        <!-- Navigation pour utilisateur connecté -->
        <template v-if="user">
            <AppHeader />
        </template>

        <!-- Navigation simple pour utilisateur non connecté -->
        <template v-else>
            <header class="sticky top-0 z-50 w-full border-b bg-white/80 backdrop-blur-xl supports-backdrop-filter:bg-white/60 dark:bg-gray-900/80 shadow-sm">
                <div class="container flex h-20 items-center justify-between">
                    <Link href="/" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-blue-600 to-indigo-600 shadow-lg">
                            <Briefcase class="h-6 w-6 text-white" />
                        </div>
                        <span class="text-2xl font-bold bg-linear-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">CVBuilder Pro</span>
                    </Link>
                    <nav class="flex items-center gap-4">
                        <Link
                            :href="login()"
                            class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        >
                            Se connecter
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="inline-flex items-center justify-center rounded-xl bg-linear-to-r from-blue-600 to-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl hover:scale-105 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        >
                            S'inscrire
                        </Link>
                    </nav>
                </div>
            </header>
        </template>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t">
            <div class="container flex flex-col items-center justify-between gap-4 py-8 md:flex-row">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Briefcase class="h-4 w-4" />
                    <span>© 2025 CVBuilder Pro. Tous droits reserves.</span>
                </div>
                <div class="flex flex-wrap gap-6 text-sm text-muted-foreground">
                    <a href="/about" class="transition-colors hover:text-foreground">A propos</a>
                    <a href="/contact" class="transition-colors hover:text-foreground">Contact</a>
                    <a href="/legal" class="transition-colors hover:text-foreground">Mentions legales</a>
                    <a href="/privacy" class="transition-colors hover:text-foreground">Confidentialite</a>
                    <a href="/ai-act" class="transition-colors hover:text-foreground">Transparence IA</a>
                </div>
            </div>
        </footer>
    </div>
</template>
