<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { FileText, MessageSquare, Target, TrendingUp, Sparkles, Briefcase, ArrowRight } from 'lucide-vue-next';
import { index as conversationsIndex } from '@/routes/conversations';
import { edit as editCustomInstructions } from '@/routes/custom-instructions';

const page = usePage();
const user = page.props.auth.user;

interface Props {
    presets?: any[];
}

const props = withDefaults(defineProps<Props>(), {
    presets: () => []
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Mapping des actions rapides avec les noms de presets
const quickActions = [
    {
        title: 'Optimiser mon CV',
        description: 'Créez ou améliorez votre CV avec l\'IA',
        icon: FileText,
        presetName: 'Optimisation CV',
        color: 'text-blue-600 dark:text-blue-400',
        bgColor: 'bg-blue-50 dark:bg-blue-950',
    },
    {
        title: 'Lettre de motivation',
        description: 'Générez une lettre personnalisée',
        icon: MessageSquare,
        presetName: 'Lettre de motivation',
        color: 'text-green-600 dark:text-green-400',
        bgColor: 'bg-green-50 dark:bg-green-950',
    },
    {
        title: 'Préparation entretien',
        description: 'Simulez un entretien d\'embauche',
        icon: Target,
        presetName: 'Préparation entretien',
        color: 'text-purple-600 dark:text-purple-400',
        bgColor: 'bg-purple-50 dark:bg-purple-950',
    },
    {
        title: 'Stratégie carrière',
        description: 'Planifiez votre évolution pro',
        icon: TrendingUp,
        presetName: 'Stratégie carrière',
        color: 'text-orange-600 dark:text-orange-400',
        bgColor: 'bg-orange-50 dark:bg-orange-950',
    },
];

const careerTips = [
    {
        title: 'Quantifiez vos réalisations',
        description: 'Utilisez des chiffres concrets : "Augmenté les ventes de 35%" plutôt que "Amélioré les ventes"',
    },
    {
        title: 'Optimisez pour les ATS',
        description: 'Les systèmes ATS scannent les mots-clés de l\'offre. Adaptez votre CV à chaque candidature.',
    },
    {
        title: 'Technique STAR pour les entretiens',
        description: 'Situation, Tâche, Action, Résultat - structurez vos réponses selon cette méthode.',
    },
];

// Créer une conversation avec un preset spécifique
const createConversationWithPreset = (presetName: string) => {
    // Trouver le preset par son nom
    const preset = props.presets.find((p: any) => p.name === presetName);

    if (preset) {
        router.post('/conversations', {
            model: preset.preferred_model || user.last_model || 'anthropic/claude-sonnet-4',
            instruction_preset_id: preset.id,
        });
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Welcome Section -->
            <header class="flex items-center justify-between" role="banner">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Bonjour, {{ user.name }} 👋
                    </h1>
                    <p class="text-muted-foreground">
                        Prêt à booster votre carrière aujourd'hui ?
                    </p>
                </div>
                <Link :href="conversationsIndex().url" class="cursor-pointer">
                    <Button size="lg" class="gap-2" aria-label="Créer une nouvelle conversation">
                        <Sparkles class="h-4 w-4" aria-hidden="true" />
                        Nouvelle conversation
                    </Button>
                </Link>
            </header>

            <!-- Quick Actions -->
            <section aria-labelledby="quick-actions-heading">
                <h2 id="quick-actions-heading" class="mb-4 text-xl font-semibold">Actions rapides</h2>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4" role="list">
                    <button
                        v-for="action in quickActions"
                        :key="action.title"
                        @click="createConversationWithPreset(action.presetName)"
                        class="group text-left"
                        :aria-label="`${action.title}: ${action.description}`"
                        type="button"
                        role="listitem"
                    >
                        <Card class="transition-all hover:shadow-lg hover:scale-105 cursor-pointer">
                            <CardHeader class="space-y-1">
                                <div
                                    :class="[action.bgColor, 'mb-2 inline-flex h-12 w-12 items-center justify-center rounded-lg']"
                                    aria-hidden="true"
                                >
                                    <component
                                        :is="action.icon"
                                        :class="[action.color, 'h-6 w-6']"
                                        aria-hidden="true"
                                    />
                                </div>
                                <CardTitle class="text-base">{{ action.title }}</CardTitle>
                                <CardDescription>{{ action.description }}</CardDescription>
                            </CardHeader>
                        </Card>
                    </button>
                </div>
            </section>

            <!-- Career Tips -->
            <div class="grid gap-6 lg:grid-cols-3">
                <section class="lg:col-span-2" aria-labelledby="tips-heading">
                    <h2 id="tips-heading" class="mb-4 text-xl font-semibold">Conseils du jour</h2>
                    <div class="space-y-4" role="list">
                        <Card
                            v-for="(tip, index) in careerTips"
                            :key="index"
                            role="listitem"
                        >
                            <CardHeader>
                                <CardTitle class="text-base"><span aria-hidden="true">💡</span> {{ tip.title }}</CardTitle>
                                <CardDescription class="text-sm">
                                    {{ tip.description }}
                                </CardDescription>
                            </CardHeader>
                        </Card>
                    </div>
                </section>

                <!-- Getting Started -->
                <aside aria-labelledby="getting-started-heading">
                    <h2 id="getting-started-heading" class="mb-4 text-xl font-semibold">Comment commencer</h2>
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Briefcase class="h-5 w-5" aria-hidden="true" />
                                Guide rapide
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <ol class="space-y-3 text-sm" role="list">
                                <li class="flex items-start gap-3">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground" aria-hidden="true">
                                        1
                                    </div>
                                    <p class="text-muted-foreground">
                                        Cliquez sur "Optimiser mon CV" pour commencer
                                    </p>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground" aria-hidden="true">
                                        2
                                    </div>
                                    <p class="text-muted-foreground">
                                        Partagez vos expériences et compétences
                                    </p>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground" aria-hidden="true">
                                        3
                                    </div>
                                    <p class="text-muted-foreground">
                                        Recevez des conseils personnalisés instantanés
                                    </p>
                                </li>
                            </ol>
                            <Link :href="editCustomInstructions().url" class="cursor-pointer">
                                <Button variant="outline" class="w-full gap-2" aria-label="Personnaliser les instructions de l'IA">
                                    Personnaliser l'IA
                                    <ArrowRight class="h-4 w-4" aria-hidden="true" />
                                </Button>
                            </Link>
                        </CardContent>
                    </Card>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>
