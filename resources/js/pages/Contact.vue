<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import StructuredData from '@/components/StructuredData.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <Head title="Contact - CVBuilder Pro">
            <meta name="description" content="Contactez l'équipe CVBuilder Pro pour toute question sur notre service de coaching carrière propulsé par l'IA." />
            <meta property="og:title" content="Contact - CVBuilder Pro" />
            <meta property="og:image" content="/og-image.svg" />
            <link rel="canonical" href="/contact" />
        </Head>
        <StructuredData :data="{
            '@context': 'https://schema.org',
            '@type': 'ContactPage',
            'name': 'Contact CVBuilder Pro',
            'description': 'Contactez l\'équipe CVBuilder Pro pour toute question sur notre service de coaching carrière',
            'url': '/contact'
        }" />
        <section class="container py-16">
            <div class="mx-auto max-w-3xl">
                <h1 class="text-4xl font-bold mb-6">Contact</h1>
                <p class="text-base text-gray-600 dark:text-gray-400">
                    Une question ? Une demande de demo ? Contactez notre equipe.
                </p>
                <div class="mt-8 space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <p><strong>Email:</strong> support@cvbuilder-pro.test</p>
                    <p><strong>Telephone:</strong> +33 1 84 60 00 00</p>
                    <p><strong>Adresse:</strong> 24 rue des Entrepreneurs, 75015 Paris, France</p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
