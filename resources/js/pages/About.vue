<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import StructuredData from '@/components/StructuredData.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <Head title="A propos - CVBuilder Pro">
            <meta name="description" content="Découvrez CVBuilder Pro, votre coach carrière propulsé par l'IA. Mission : rendre la préparation professionnelle plus claire, rapide et efficace." />
            <meta property="og:title" content="A propos - CVBuilder Pro" />
            <meta property="og:description" content="Découvrez notre approche unique combinant IA, instructions personnalisées et presets métiers." />
            <meta property="og:image" content="/og-image.svg" />
            <link rel="canonical" href="/about" />
        </Head>
        <StructuredData :data="{
            '@context': 'https://schema.org',
            '@type': 'AboutPage',
            'name': 'A propos de CVBuilder Pro',
            'description': 'Découvrez CVBuilder Pro, votre coach carrière propulsé par l\'IA. Notre mission est de rendre la préparation professionnelle plus claire, rapide et efficace.',
            'url': '/about',
            'mainEntity': {
                '@type': 'Organization',
                'name': 'CVBuilder Pro',
                'description': 'Coach carrière IA pour optimiser votre recherche d\'emploi'
            }
        }" />
        <section class="container py-16">
            <div class="mx-auto max-w-3xl">
                <h1 class="text-4xl font-bold mb-6">A propos de CVBuilder Pro</h1>
                <p class="text-base text-gray-600 dark:text-gray-400">
                    CVBuilder Pro est un coach carriere propulse par l'IA. Notre mission est de rendre la preparation
                    professionnelle plus claire, plus rapide et plus efficace pour chaque candidat.
                </p>
                <div class="mt-8 space-y-4 text-sm text-gray-600 dark:text-gray-400">
                    <p>
                        Nous aidons a structurer les CV, clarifier les lettres de motivation et preparer les entretiens.
                    </p>
                    <p>
                        Notre approche combine des instructions personnalisees, des presets par metier et des models
                        de langage adaptes au contexte.
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
