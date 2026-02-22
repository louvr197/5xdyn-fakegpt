<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <Head title="Confidentialite - CVBuilder Pro">
            <meta name="description" content="Politique de confidentialité et protection des données personnelles de CVBuilder Pro. Conformité RGPD." />
            <meta property="og:image" content="/og-image.svg" />
            <link rel="canonical" href="/privacy" />
            <meta name="robots" content="noindex, follow" />
        </Head>
        <section class="container py-16">
            <div class="mx-auto max-w-3xl space-y-6">
                <h1 class="text-4xl font-bold">Politique de confidentialite</h1>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Cette politique explique quelles donnees sont collectees et comment elles sont utilisees dans
                    CVBuilder Pro.
                </p>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Donnees collectees</h2>
                    <p>Identite, email, preferences de compte, historiques de conversation, et metadonnees techniques.</p>
                </div>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Finalites</h2>
                    <p>Fournir le service, personnaliser les reponses, ameliorer la qualite, assurer la securite.</p>
                </div>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Base legale</h2>
                    <p>Execution du contrat, consentement et interet legitime selon les traitements.</p>
                </div>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Duree de conservation</h2>
                    <p>Les donnees sont conservees pendant la duree du compte et jusqu'a 24 mois apres inactivite.</p>
                </div>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Vos droits</h2>
                    <p>Acces, rectification, suppression, opposition, portabilite. Contact: privacy@cvbuilder-pro.test</p>
                </div>

                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Transferts hors UE</h2>
                    <p>Les traitements IA peuvent impliquer des fournisseurs hors UE, avec garanties adequates.</p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
