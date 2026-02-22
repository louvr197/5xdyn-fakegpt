<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <Head title="Mentions légales - CVBuilder Pro">
            <meta name="description" content="Mentions légales de CVBuilder Pro. Informations sur l'éditeur, hébergement et conditions d'utilisation." />
            <meta property="og:image" content="/og-image.svg" />
            <link rel="canonical" href="/legal" />
            <meta name="robots" content="noindex, follow" />
        </Head>
        <section class="container py-16">
            <div class="mx-auto max-w-3xl space-y-6">
                <h1 class="text-4xl font-bold">Mentions legales</h1>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Editeur</h2>
                    <p>CVBuilder Pro, SAS fictive</p>
                    <p>24 rue des Entrepreneurs, 75015 Paris, France</p>
                    <p>RCS Paris 999 999 999</p>
                    <p>Email: legal@cvbuilder-pro.test</p>
                </div>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Directeur de publication</h2>
                    <p>Amelie Dupont</p>
                </div>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Hebergeur</h2>
                    <p>NovaCloud (fictif)</p>
                    <p>12 avenue des Data Centers, 69000 Lyon, France</p>
                    <p>Tel: +33 4 88 00 00 00</p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
