<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import StructuredData from '@/components/StructuredData.vue';
import { MessageSquare, Sparkles, Shield, Briefcase, FileText, Target, TrendingUp } from 'lucide-vue-next';

interface Props {
    canRegister?: boolean;
    presets?: any[];
}

const props = withDefaults(defineProps<Props>(), {
    canRegister: true,
    presets: () => [],
});

const page = usePage();
const user = page.props.auth.user;

// Mapping des features avec les noms de presets
const features = [
    {
        title: 'Optimisation CV ATS',
        description: 'Créez des CV optimisés pour passer les filtres automatiques (ATS) et attirer l\'attention des recruteurs.',
        icon: FileText,
        gradient: 'from-blue-500 to-indigo-500',
        borderColor: 'hover:border-blue-300 dark:hover:border-blue-600',
        presetName: 'Optimisation CV',
    },
    {
        title: 'Lettres de motivation',
        description: 'Générez des lettres personnalisées et convaincantes adaptées à chaque offre d\'emploi.',
        icon: MessageSquare,
        gradient: 'from-purple-500 to-pink-500',
        borderColor: 'hover:border-purple-300 dark:hover:border-purple-600',
        presetName: 'Lettre de motivation',
    },
    {
        title: 'Préparation entretiens',
        description: 'Préparez vos entretiens avec des simulations, réponses STAR et techniques de négociation salariale.',
        icon: Target,
        gradient: 'from-green-500 to-emerald-500',
        borderColor: 'hover:border-green-300 dark:hover:border-green-600',
        presetName: 'Préparation entretien',
    },
    {
        title: 'Stratégie carrière',
        description: 'Planifiez votre évolution professionnelle, reconversion ou négociation d\'augmentation avec un expert.',
        icon: TrendingUp,
        gradient: 'from-orange-500 to-red-500',
        borderColor: 'hover:border-orange-300 dark:hover:border-orange-600',
        presetName: 'Stratégie carrière',
    },
    {
        title: 'Conseil multi-sectoriel',
        description: 'Expertise adaptée à votre secteur : Tech, Finance, Marketing, Santé, Ingénierie et plus encore.',
        icon: Sparkles,
        gradient: 'from-yellow-500 to-amber-500',
        borderColor: 'hover:border-yellow-300 dark:hover:border-yellow-600',
        presetName: null, // Pas de preset spécifique
    },
    {
        title: 'LinkedIn & Personal Branding',
        description: 'Optimisez votre profil LinkedIn, développez votre marque personnelle et votre réseau professionnel.',
        icon: Shield,
        gradient: 'from-cyan-500 to-blue-500',
        borderColor: 'hover:border-cyan-300 dark:hover:border-cyan-600',
        presetName: 'Optimisation LinkedIn',
    },
];

// Créer une conversation avec un preset spécifique
const createConversationWithPreset = (presetName: string | null) => {
    if (!presetName) {
        // Si pas de preset, rediriger vers le dashboard
        router.visit('/dashboard');
        return;
    }

    const preset = props.presets.find((p: any) => p.name === presetName);

    if (preset) {
        router.post('/conversations', {
            model: preset.preferred_model || (user as any)?.last_model || 'anthropic/claude-sonnet-4',
            instruction_preset_id: preset.id,
        });
    } else {
        // Si preset non trouvé, rediriger vers le dashboard
        router.visit('/dashboard');
    }
};
</script>

<template>
    <PublicLayout>
        <Head title="CVBuilder Pro - Votre Coach Carriere IA">
            <meta name="description" content="Décrochez le job de vos rêves avec CVBuilder Pro. Coach carrière IA pour créer des CV optimisés ATS, préparer vos entretiens et booster votre recherche d'emploi." />
            <meta property="og:title" content="CVBuilder Pro - Votre Coach Carrière IA" />
            <meta property="og:description" content="Créez des CV percutants, optimisez pour les ATS, maîtrisez vos entretiens. Votre coach carrière personnel disponible 24/7." />
            <meta property="og:image" content="/og-image.svg" />
            <meta property="twitter:title" content="CVBuilder Pro - Votre Coach Carrière IA" />
            <meta property="twitter:description" content="Créez des CV percutants, optimisez pour les ATS, maîtrisez vos entretiens." />
            <link rel="canonical" href="/" />
            <link rel="preconnect" href="https://rsms.me/" />
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        </Head>
        <StructuredData :data="{
            '@context': 'https://schema.org',
            '@type': 'WebApplication',
            'name': 'CVBuilder Pro',
            'description': 'Coach carrière IA pour créer des CV optimisés ATS, préparer vos entretiens et booster votre recherche d\'emploi',
            'url': '/',
            'applicationCategory': 'BusinessApplication',
            'offers': {
                '@type': 'Offer',
                'price': '0',
                'priceCurrency': 'EUR'
            },
            'featureList': [
                'Optimisation CV ATS',
                'Lettres de motivation personnalisées',
                'Préparation aux entretiens',
                'Stratégie de carrière',
                'Optimisation LinkedIn',
                'Coach carrière disponible 24/7'
            ]
        }" />
        <StructuredData :data="{
            '@context': 'https://schema.org',
            '@type': 'Organization',
            'name': 'CVBuilder Pro',
            'url': '/',
            'logo': '/favicon.svg',
            'sameAs': []
        }" />
        <!-- Hero Section -->
            <section class="container flex flex-col items-center justify-center gap-10 py-32 md:py-40 relative">
                <!-- Decorative elements -->
                <div class="absolute inset-0 -z-10 overflow-hidden">
                    <div class="absolute top-20 left-10 h-72 w-72 bg-blue-400/20 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-20 right-10 h-96 w-96 bg-indigo-400/20 rounded-full blur-3xl"></div>
                </div>

                <div class="flex max-w-4xl flex-col items-center gap-6 text-center">
                    <div class="inline-flex items-center rounded-full border-2 border-blue-200 bg-white/80 backdrop-blur-sm px-5 py-2 text-sm font-medium shadow-lg transition-all hover:shadow-xl hover:scale-105 dark:bg-gray-800/80 dark:border-blue-800">
                        <Target class="mr-2 h-4 w-4 text-blue-600" />
                        <span class="text-blue-700 dark:text-blue-400">Expert en recrutement propulsé par IA</span>
                    </div>
                    <h1 class="text-5xl font-extrabold tracking-tight sm:text-6xl md:text-7xl lg:text-8xl">
                        Décrochez le job de
                        <span class="bg-linear-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent mt-2 block">vos rêves</span>
                    </h1>
                    <p class="max-w-2xl text-xl text-gray-600 dark:text-gray-400 sm:text-2xl leading-relaxed">
                        Créez des CV percutants, optimisez pour les ATS, maîtrisez vos entretiens. Votre coach carrière personnel disponible 24/7.
                    </p>
                    <div class="flex flex-col gap-4 sm:flex-row mt-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="group inline-flex items-center justify-center rounded-2xl bg-linear-to-r from-blue-600 to-indigo-600 px-10 py-4 text-lg font-semibold text-white shadow-2xl transition-all hover:shadow-3xl hover:scale-105 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300"
                        >
                            <Briefcase class="mr-3 h-6 w-6 group-hover:animate-bounce" />
                            Optimiser mon CV
                        </Link>
                        <Link
                            v-else
                            :href="register()"
                            class="group inline-flex items-center justify-center rounded-2xl bg-linear-to-r from-blue-600 to-indigo-600 px-10 py-4 text-lg font-semibold text-white shadow-2xl transition-all hover:shadow-3xl hover:scale-105 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300"
                        >
                            <Briefcase class="mr-3 h-6 w-6 group-hover:animate-bounce" />
                            Créer mon CV gratuitement
                        </Link>
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="login()"
                            class="inline-flex items-center justify-center rounded-2xl border-2 border-gray-300 bg-white/80 backdrop-blur-sm px-10 py-4 text-lg font-medium shadow-lg transition-all hover:bg-gray-50 hover:shadow-xl hover:scale-105 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-gray-300 dark:bg-gray-800/80 dark:border-gray-700"
                        >
                            Se connecter
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="container py-20">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4">Tout pour réussir votre carrière</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Des outils professionnels propulsés par l'IA pour vous démarquer sur le marché du travail
                    </p>
                </div>
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <component
                        v-for="feature in features"
                        :key="feature.title"
                        :is="$page.props.auth.user ? 'button' : 'div'"
                        @click="$page.props.auth.user ? createConversationWithPreset(feature.presetName) : null"
                        :class="[
                            'group flex flex-col gap-5 rounded-2xl border-2 border-gray-200 bg-white/80 backdrop-blur-sm p-8 shadow-lg transition-all hover:shadow-2xl hover:scale-105 dark:bg-gray-800/80 dark:border-gray-700',
                            feature.borderColor,
                            $page.props.auth.user && feature.presetName ? 'cursor-pointer text-left' : ''
                        ]"
                    >
                        <div :class="['flex h-14 w-14 items-center justify-center rounded-xl bg-linear-to-br shadow-lg group-hover:scale-110 transition-transform', feature.gradient]">
                            <component :is="feature.icon" class="h-7 w-7 text-white" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ feature.title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ feature.description }}
                        </p>
                        <div v-if="$page.props.auth.user && feature.presetName" class="mt-auto pt-4 flex items-center text-sm font-medium text-blue-600 dark:text-blue-400">
                            <span>Créer une conversation</span>
                            <svg class="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </component>
                </div>
            </section>

            <!-- Pricing Section -->
            <section class="container py-20">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4">Des formules pour chaque ambition</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Choisissez le plan adapte a votre rythme. Passez a l'action en quelques minutes.
                    </p>
                </div>
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-2xl border-2 border-gray-200 bg-white/80 backdrop-blur-sm p-8 shadow-lg dark:bg-gray-800/80 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold">Starter</h3>
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Gratuit</span>
                        </div>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Ideal pour debuter et tester vos premiers CV.</p>
                        <div class="mt-6 text-4xl font-bold">0€<span class="text-base font-medium text-gray-500">/mois</span></div>
                        <ul class="mt-6 space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li>3 conversations par semaine</li>
                            <li>Optimisation ATS de base</li>
                            <li>Templates CV essentiels</li>
                        </ul>
                        <Link
                            :href="register()"
                            class="mt-8 inline-flex w-full items-center justify-center rounded-xl border-2 border-blue-200/60 bg-white/80 px-4 py-2.5 text-sm font-semibold text-blue-700 shadow-sm transition hover:border-blue-400 hover:shadow-md dark:bg-gray-800/80 dark:border-blue-700/60 dark:text-blue-300"
                        >
                            Commencer
                        </Link>
                    </div>

                    <div class="rounded-2xl border-2 border-blue-300 bg-linear-to-br from-blue-600/10 via-white/80 to-indigo-600/10 p-8 shadow-xl dark:border-blue-700 dark:from-blue-900/30 dark:via-gray-900/70 dark:to-indigo-900/30">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold">Pro</h3>
                            <span class="rounded-full bg-blue-600 px-3 py-1 text-xs font-semibold text-white">Populaire</span>
                        </div>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Pour les chercheurs d'emploi actifs et ambitieux.</p>
                        <div class="mt-6 text-4xl font-bold">19€<span class="text-base font-medium text-gray-500">/mois</span></div>
                        <ul class="mt-6 space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li>Conversations illimitees</li>
                            <li>Coaching entretien avance</li>
                            <li>Audit LinkedIn + lettres</li>
                        </ul>
                        <Link
                            :href="register()"
                            class="mt-8 inline-flex w-full items-center justify-center rounded-xl bg-linear-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:shadow-xl hover:scale-[1.02]"
                        >
                            Passer en Pro
                        </Link>
                    </div>

                    <div class="rounded-2xl border-2 border-gray-200 bg-white/80 backdrop-blur-sm p-8 shadow-lg dark:bg-gray-800/80 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold">Equipe</h3>
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">Sur devis</span>
                        </div>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Pour les cabinets de recrutement et RH.</p>
                        <div class="mt-6 text-4xl font-bold">99€<span class="text-base font-medium text-gray-500">/mois</span></div>
                        <ul class="mt-6 space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li>Espaces equipes multi-utilisateurs</li>
                            <li>Reporting et analytics</li>
                            <li>Support prioritaire</li>
                        </ul>
                        <a
                            href="/contact"
                            class="mt-8 inline-flex w-full items-center justify-center rounded-xl border-2 border-indigo-200/60 bg-white/80 px-4 py-2.5 text-sm font-semibold text-indigo-700 shadow-sm transition hover:border-indigo-400 hover:shadow-md dark:bg-gray-800/80 dark:border-indigo-700/60 dark:text-indigo-300"
                        >
                            Contacter l'equipe
                        </a>
                    </div>
                </div>
            </section>

            <!-- Testimonials Section -->
            <section class="container py-20">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4">Ils ont transforme leur carriere</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Des retours concrets d'utilisateurs qui ont accelere leur parcours.
                    </p>
                </div>
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-2xl border-2 border-gray-200 bg-white/80 backdrop-blur-sm p-6 shadow-lg dark:bg-gray-800/80 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400">"CVBuilder Pro m'a aide a clarifier mon pitch. J'ai signe en 3 semaines."</p>
                        <div class="mt-6 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-linear-to-br from-blue-500 to-indigo-600"></div>
                            <div>
                                <p class="text-sm font-semibold">Lea M.</p>
                                <p class="text-xs text-gray-500">Product Designer</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border-2 border-gray-200 bg-white/80 backdrop-blur-sm p-6 shadow-lg dark:bg-gray-800/80 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400">"Le coaching entretien est ultra precis. J'ai gagne en confiance."</p>
                        <div class="mt-6 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-linear-to-br from-purple-500 to-pink-500"></div>
                            <div>
                                <p class="text-sm font-semibold">Nadir K.</p>
                                <p class="text-xs text-gray-500">Data Analyst</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border-2 border-gray-200 bg-white/80 backdrop-blur-sm p-6 shadow-lg dark:bg-gray-800/80 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400">"J'ai restructure mon LinkedIn et recu 2 offres en une semaine."</p>
                        <div class="mt-6 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-linear-to-br from-emerald-500 to-teal-500"></div>
                            <div>
                                <p class="text-sm font-semibold">Camille R.</p>
                                <p class="text-xs text-gray-500">Marketing Manager</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ Section -->
            <section class="container py-20">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4">Questions frequentes</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Tout ce que vous devez savoir avant de commencer.
                    </p>
                </div>
                <div class="mx-auto max-w-3xl space-y-4">
                    <details class="group rounded-2xl border-2 border-gray-200 bg-white/80 p-6 shadow-md transition dark:bg-gray-800/80 dark:border-gray-700">
                        <summary class="cursor-pointer text-base font-semibold text-gray-900 dark:text-white">Comment les conversations sont-elles utilisees ?</summary>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Elles servent uniquement a produire vos reponses. Vous gardez la main sur vos donnees.</p>
                    </details>
                    <details class="group rounded-2xl border-2 border-gray-200 bg-white/80 p-6 shadow-md transition dark:bg-gray-800/80 dark:border-gray-700">
                        <summary class="cursor-pointer text-base font-semibold text-gray-900 dark:text-white">Puis-je changer de modele a tout moment ?</summary>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Oui, vous pouvez basculer entre plusieurs LLM selon votre besoin.</p>
                    </details>
                    <details class="group rounded-2xl border-2 border-gray-200 bg-white/80 p-6 shadow-md transition dark:bg-gray-800/80 dark:border-gray-700">
                        <summary class="cursor-pointer text-base font-semibold text-gray-900 dark:text-white">Les conseils remplacent-ils un recruteur ?</summary>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Non. L'IA est un outil d'aide a la decision. Vous restez responsable.</p>
                    </details>
                    <details class="group rounded-2xl border-2 border-gray-200 bg-white/80 p-6 shadow-md transition dark:bg-gray-800/80 dark:border-gray-700">
                        <summary class="cursor-pointer text-base font-semibold text-gray-900 dark:text-white">Puis-je exporter mes conversations ?</summary>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Oui ! Chaque conversation peut être exportée en JSON en cliquant sur l'icône de téléchargement dans le header de la discussion.</p>
                    </details>
                    <details class="group rounded-2xl border-2 border-gray-200 bg-white/80 p-6 shadow-md transition dark:bg-gray-800/80 dark:border-gray-700">
                        <summary class="cursor-pointer text-base font-semibold text-gray-900 dark:text-white">Les modeles utilises sont-ils transparents ?</summary>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Oui, chaque conversation affiche clairement le modele active.</p>
                    </details>
                </div>
            </section>

            <!-- Call to Action -->
            <section class="container py-16">
                <div class="flex flex-col items-center gap-6 rounded-lg border bg-linear-to-r from-primary/10 via-primary/5 to-primary/10 p-12 text-center">
                    <h2 class="text-3xl font-bold">Prêt à booster votre carrière ?</h2>
                    <p class="max-w-2xl text-lg text-muted-foreground">
                        Rejoignez des milliers de professionnels qui ont décroché leur job idéal grâce à CVBuilder Pro.
                    </p>
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="register()"
                        class="inline-flex items-center justify-center rounded-md bg-primary px-8 py-3 text-base font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    >
                        Créer mon compte gratuit
                    </Link>
                </div>
            </section>
    </PublicLayout>
</template>
