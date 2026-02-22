<script setup lang="ts">
import { useForm, Head, router } from '@inertiajs/vue3';
import { update, edit } from '@/routes/custom-instructions';
import { store as storeConversation } from '@/routes/conversations';
import { ref, onMounted } from 'vue';
import axios from 'axios';

import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Sparkles, Trash2 } from 'lucide-vue-next';

interface Props {
    customInstructions: {
        about: string | null;
        behavior: string | null;
        commands: string | null;
    };
    personalization: {
        tone_style: string | null;
        conciseness: string | null;
        titles_lists: string | null;
        warmth: string | null;
        enthusiasm: string | null;
        formality: string | null;
        emojis: string | null;
    };
}

interface Preset {
    id: number;
    name: string;
    description: string;
    icon: string;
    about: string | null;
    behavior: string | null;
    commands: string | null;
    preferred_model: string | null;
    is_system: boolean;
    user_id: number | null;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Instructions personnalisées',
        href: edit().url,
    },
];

const form = useForm({
    custom_instructions_about: props.customInstructions.about || '',
    custom_instructions_behavior: props.customInstructions.behavior || '',
    custom_instructions_commands: props.customInstructions.commands || '',
    tone_style: props.personalization.tone_style || 'default',
    conciseness: props.personalization.conciseness || 'default',
    titles_lists: props.personalization.titles_lists || 'default',
    warmth: props.personalization.warmth || 'default',
    enthusiasm: props.personalization.enthusiasm || 'default',
    formality: props.personalization.formality || 'default',
    emojis: props.personalization.emojis || 'default',
});

const systemPresets = ref<Preset[]>([]);
const userPresets = ref<Preset[]>([]);
const showPresetModal = ref(false);
const presetForm = ref({
    name: '',
    description: '',
    icon: '⭐',
});

const loadPresets = async () => {
    try {
        const response = await axios.get('/presets');
        systemPresets.value = response.data.system;
        userPresets.value = response.data.user;
    } catch (error) {
        console.error('Erreur lors du chargement des presets:', error);
    }
};

const loadPreset = (preset: Preset) => {
    form.custom_instructions_about = preset.about || '';
    form.custom_instructions_behavior = preset.behavior || '';
    form.custom_instructions_commands = preset.commands || '';
};

const saveAsPreset = async () => {
    try {
        await axios.post('/presets', {
            name: presetForm.value.name,
            description: presetForm.value.description,
            icon: presetForm.value.icon,
            about: form.custom_instructions_about,
            behavior: form.custom_instructions_behavior,
            commands: form.custom_instructions_commands,
            preferred_model: null,
        });

        showPresetModal.value = false;
        presetForm.value = { name: '', description: '', icon: '⭐' };
        await loadPresets();
    } catch (error) {
        console.error('Erreur lors de la sauvegarde du preset:', error);
    }
};

const deletePreset = async (presetId: number) => {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce preset ?')) return;

    try {
        await axios.delete(`/presets/${presetId}`);
        await loadPresets();
    } catch (error) {
        console.error('Erreur lors de la suppression du preset:', error);
    }
};

const startConversationWithPreset = (preset: Preset) => {
    router.post(storeConversation().url, {
        model: preset.preferred_model || 'anthropic/claude-sonnet-4',
        instruction_preset_id: preset.id,
    });
};

onMounted(() => {
    loadPresets();
});

const submit = () => {
    form.patch(update().url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Instructions personnalisées" />

        <div class="container mx-auto max-w-4xl px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-2">
                    🤖 Instructions personnalisées
                </h1>
                <p class="text-muted-foreground">
                    Personnalisez la façon dont l'IA vous répond en lui donnant du
                    contexte sur vous et vos attentes.
                </p>
            </div>

            <!-- Info Card -->
            <Card class="mb-8 p-4 bg-muted/50 border-muted">
                <div class="flex gap-3">
                    <div class="text-2xl">💡</div>
                    <div class="flex-1 text-sm">
                        <p class="font-medium mb-2">Comment ça fonctionne ?</p>
                        <p class="text-muted-foreground">
                            Ces instructions sont automatiquement ajoutées à chaque
                            conversation. L'IA adaptera son comportement, son ton et ses
                            réponses en fonction de ce que vous indiquez ici.
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Personnalisation du style -->
            <Card class="mb-8 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="text-2xl">🎛️</div>
                    <div>
                        <h2 class="text-xl font-semibold">Personnalisation</h2>
                        <p class="text-sm text-muted-foreground">Ajustez le style global des reponses.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="tone-style">Style et ton de base</Label>
                        <select
                            id="tone-style"
                            v-model="form.tone_style"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="default">Par defaut</option>
                            <option value="cynical">Cynique</option>
                            <option value="professional">Professionnel</option>
                            <option value="friendly">Amical</option>
                            <option value="coach">Coach</option>
                            <option value="technical">Technique</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="conciseness">Concis</Label>
                        <select
                            id="conciseness"
                            v-model="form.conciseness"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="default">Par defaut</option>
                            <option value="concise">Concis</option>
                            <option value="balanced">Equilibre</option>
                            <option value="detailed">Detaille</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="titles-lists">Titres et listes</Label>
                        <select
                            id="titles-lists"
                            v-model="form.titles_lists"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="default">Par defaut</option>
                            <option value="minimal">Minimal</option>
                            <option value="standard">Standard</option>
                            <option value="rich">Riche</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="warmth">Chaleureux</Label>
                        <select
                            id="warmth"
                            v-model="form.warmth"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="default">Par defaut</option>
                            <option value="low">Moins</option>
                            <option value="high">Plus</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="enthusiasm">Enthousiaste</Label>
                        <select
                            id="enthusiasm"
                            v-model="form.enthusiasm"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="default">Par defaut</option>
                            <option value="low">Moins</option>
                            <option value="high">Plus</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="formality">Formel</Label>
                        <select
                            id="formality"
                            v-model="form.formality"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="default">Par defaut</option>
                            <option value="low">Moins</option>
                            <option value="high">Plus</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="emojis">Emojis</Label>
                        <select
                            id="emojis"
                            v-model="form.emojis"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="default">Par defaut</option>
                            <option value="none">Aucun</option>
                            <option value="low">Peu</option>
                            <option value="high">Plus</option>
                        </select>
                    </div>
                </div>
            </Card>

            <!-- Section Presets -->
            <div v-if="systemPresets.length > 0 || userPresets.length > 0" class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <Sparkles class="w-6 h-6 text-primary" />
                    <h2 class="text-xl font-semibold">Presets rapides</h2>
                </div>
                <p class="text-sm text-muted-foreground mb-4">
                    Chargez rapidement des configurations prédéfinies pour vos cas d'usage courants.
                </p>

                <!-- System Presets -->
                <div v-if="systemPresets.length > 0" class="mb-6">
                    <h3 class="text-sm font-medium mb-3 text-muted-foreground">Presets système</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Card
                            v-for="preset in systemPresets"
                            :key="preset.id"
                            class="p-4 hover:border-primary transition-all hover:shadow-md group"
                        >
                            <div class="flex items-start gap-3 mb-3">
                                <div class="text-3xl group-hover:scale-110 transition-transform">
                                    {{ preset.icon }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-sm mb-1">
                                        {{ preset.name }}
                                    </h4>
                                    <p class="text-xs text-muted-foreground line-clamp-2">
                                        {{ preset.description }}
                                    </p>
                                    <p v-if="preset.preferred_model" class="text-xs text-primary mt-2 font-medium">
                                        {{ preset.preferred_model }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    class="flex-1 text-xs"
                                    @click="loadPreset(preset)"
                                >
                                    Charger
                                </Button>
                                <Button
                                    size="sm"
                                    class="flex-1 text-xs"
                                    @click="startConversationWithPreset(preset)"
                                >
                                    Conversation
                                </Button>
                            </div>
                        </Card>
                    </div>
                </div>

                <!-- User Presets -->
                <div v-if="userPresets.length > 0" class="mb-6">
                    <h3 class="text-sm font-medium mb-3 text-muted-foreground">Mes presets</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Card
                            v-for="preset in userPresets"
                            :key="preset.id"
                            class="p-4 hover:border-primary transition-all hover:shadow-md group relative"
                        >
                            <div class="flex items-start gap-3 mb-3">
                                <div class="text-3xl group-hover:scale-110 transition-transform">
                                    {{ preset.icon }}
                                </div>
                                <div class="flex-1 min-w-0 pr-8">
                                    <h4 class="font-semibold text-sm mb-1">
                                        {{ preset.name }}
                                    </h4>
                                    <p class="text-xs text-muted-foreground line-clamp-2">
                                        {{ preset.description }}
                                    </p>
                                </div>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="absolute top-2 right-2 h-8 w-8 opacity-0 group-hover:opacity-100 transition-opacity text-destructive hover:text-destructive hover:bg-destructive/10"
                                    @click.stop="deletePreset(preset.id)"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </Button>
                            </div>
                            <div class="flex gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    class="flex-1 text-xs"
                                    @click="loadPreset(preset)"
                                >
                                    Charger
                                </Button>
                                <Button
                                    size="sm"
                                    class="flex-1 text-xs"
                                    @click="startConversationWithPreset(preset)"
                                >
                                    Conversation
                                </Button>
                            </div>
                        </Card>
                    </div>
                </div>
            </div>

            <!-- Formulaire -->
            <form @submit.prevent="submit" class="space-y-8">
                <!-- À propos de vous -->
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="text-3xl">👤</div>
                        <div class="flex-1">
                            <Label for="about" class="text-lg font-semibold">
                                À propos de vous
                            </Label>
                            <p class="text-sm text-muted-foreground mt-1 mb-3">
                                Qui êtes-vous ? Votre profession, vos centres d'intérêt,
                                votre niveau d'expertise...
                            </p>
                            <Textarea
                                id="about"
                                v-model="form.custom_instructions_about"
                                placeholder="Exemple : Je suis développeur web full-stack avec 5 ans d'expérience. Je travaille principalement avec Laravel, Vue.js et PostgreSQL. Je m'intéresse aussi au machine learning et à l'optimisation des performances."
                                rows="6"
                                class="resize-y bg-white dark:bg-gray-950 border-2 border-gray-300 dark:border-gray-700 focus:border-blue-500 dark:focus:border-blue-500"
                            />
                            <p
                                v-if="form.errors.custom_instructions_about"
                                class="text-sm text-destructive mt-2"
                            >
                                {{ form.errors.custom_instructions_about }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Comportement -->
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="text-3xl">🎭</div>
                        <div class="flex-1">
                            <Label for="behavior" class="text-lg font-semibold">
                                Comportement de l'assistant
                            </Label>
                            <p class="text-sm text-muted-foreground mt-1 mb-3">
                                Comment l'IA doit-elle vous répondre ? Ton, format,
                                longueur, style...
                            </p>
                            <Textarea
                                id="behavior"
                                v-model="form.custom_instructions_behavior"
                                placeholder="Exemple : Réponds de manière concise et directe. Utilise des listes à puces quand c'est possible. Fournis des exemples de code commentés. Si tu n'es pas sûr, dis-le clairement au lieu de deviner."
                                rows="6"
                                class="resize-y bg-white dark:bg-gray-950 border-2 border-gray-300 dark:border-gray-700 focus:border-blue-500 dark:focus:border-blue-500"
                            />
                            <p
                                v-if="form.errors.custom_instructions_behavior"
                                class="text-sm text-destructive mt-2"
                            >
                                {{ form.errors.custom_instructions_behavior }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Commandes -->
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="text-3xl">⚡</div>
                        <div class="flex-1">
                            <Label for="commands" class="text-lg font-semibold">
                                Commandes personnalisées
                            </Label>
                            <p class="text-sm text-muted-foreground mt-1 mb-3">
                                Créez des raccourcis pour vos tâches fréquentes. Format :
                                <code class="text-xs bg-muted px-1 py-0.5 rounded"
                                    >/commande → Action</code
                                >
                            </p>
                            <Textarea
                                id="commands"
                                v-model="form.custom_instructions_commands"
                                placeholder="Exemple : /review → Analyse ce code et suggère des améliorations en termes de performance, sécurité et bonnes pratiques. Format: liste numérotée par priorité.&#10;/explain → Explique ce concept comme si j'avais 12 ans, avec une analogie concrète."
                                rows="6"
                                class="resize-y bg-white dark:bg-gray-950 border-2 border-gray-300 dark:border-gray-700 focus:border-blue-500 dark:focus:border-blue-500"
                            />
                            <p
                                v-if="form.errors.custom_instructions_commands"
                                class="text-sm text-destructive mt-2"
                            >
                                {{ form.errors.custom_instructions_commands }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4 pt-4">
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        size="lg"
                        class="min-w-32"
                    >
                        {{ form.processing ? 'Enregistrement...' : 'Enregistrer' }}
                    </Button>

                    <Button
                        type="button"
                        variant="outline"
                        size="lg"
                        @click="showPresetModal = true"
                        class="gap-2"
                    >
                        <Sparkles class="w-4 h-4" />
                        Sauvegarder comme preset
                    </Button>

                    <Transition
                        enter-active-class="transition ease-in-out duration-300"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out duration-300"
                        leave-to-class="opacity-0"
                    >
                        <p
                            v-if="form.recentlySuccessful"
                            class="text-sm text-green-600 dark:text-green-400 font-medium"
                        >
                            ✓ Instructions sauvegardées !
                        </p>
                    </Transition>
                </div>
            </form>

            <!-- Modal Preset -->
            <Dialog v-model:open="showPresetModal">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Créer un preset</DialogTitle>
                        <DialogDescription>
                            Sauvegardez vos instructions actuelles comme preset pour les réutiliser facilement.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="space-y-4 py-4">
                        <div class="space-y-2">
                            <Label for="preset-name">Nom du preset</Label>
                            <Input
                                id="preset-name"
                                v-model="presetForm.name"
                                placeholder="Ex: Mon preset personnalisé"
                                required
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="preset-description">Description</Label>
                            <Textarea
                                id="preset-description"
                                v-model="presetForm.description"
                                placeholder="Courte description de ce preset..."
                                rows="3"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="preset-icon">Icône (emoji)</Label>
                            <Input
                                id="preset-icon"
                                v-model="presetForm.icon"
                                placeholder="⭐"
                                maxlength="4"
                                class="text-2xl text-center"
                            />
                            <p class="text-xs text-muted-foreground">
                                Vous pouvez copier-coller n'importe quel emoji (📄, ✉️, 🎯, etc.)
                            </p>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button variant="outline" @click="showPresetModal = false">
                            Annuler
                        </Button>
                        <Button @click="saveAsPreset" :disabled="!presetForm.name">
                            Créer le preset
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <!-- Tips -->
            <Card class="mt-12 p-6 border-l-4 border-l-primary">
                <h3 class="font-semibold mb-3 flex items-center gap-2">
                    <span>📚</span>
                    <span>Bonnes pratiques</span>
                </h3>
                <ul class="space-y-2 text-sm text-muted-foreground">
                    <li class="flex gap-2">
                        <span class="text-primary">✓</span>
                        <span
                            ><strong>Soyez spécifique :</strong> "Je suis dev Laravel"
                            plutôt que "Je fais de l'informatique"</span
                        >
                    </li>
                    <li class="flex gap-2">
                        <span class="text-primary">✓</span>
                        <span
                            ><strong>Priorisez :</strong> Mettez les informations les
                            plus importantes en premier</span
                        >
                    </li>
                    <li class="flex gap-2">
                        <span class="text-primary">✓</span>
                        <span
                            ><strong>Testez et ajustez :</strong> Essayez différentes
                            formulations pour trouver ce qui fonctionne le mieux</span
                        >
                    </li>
                    <li class="flex gap-2">
                        <span class="text-destructive">✗</span>
                        <span
                            ><strong>Évitez les contradictions :</strong> "Sois concis"
                            + "Détaille tout" = confusion</span
                        >
                    </li>
                </ul>
            </Card>
        </div>
    </AppLayout>
</template>
