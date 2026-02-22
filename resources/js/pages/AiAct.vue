<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <Head title="Transparence IA - CVBuilder Pro">
            <meta name="description" content="Informations sur l'utilisation de l'intelligence artificielle dans CVBuilder Pro. Conformité AI Act européen." />
            <meta property="og:image" content="/og-image.svg" />
            <link rel="canonical" href="/ai-act" />
            <meta name="robots" content="noindex, follow" />
        </Head>
        <section class="container py-16">
            <div class="mx-auto max-w-3xl space-y-6">
                <h1 class="text-4xl font-bold">Transparence IA (AI Act)</h1>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Vous interagissez avec une intelligence artificielle. Les reponses peuvent contenir des erreurs.
                </p>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Limites du systeme</h2>
                    <p>L'IA ne remplace pas un professionnel. Verifiez toujours les informations critiques.</p>
                </div>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Modeles utilises</h2>
                    <p>Les conversations peuvent utiliser plusieurs modeles (OpenAI, Anthropic, Google) via OpenRouter.</p>
                </div>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Amelioration du service</h2>
                    <p>Les conversations peuvent etre analysees pour ameliorer le service, selon votre consentement.</p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
