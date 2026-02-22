<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { store as storeConversation } from '@/routes/conversations';
import { router, useForm } from '@inertiajs/vue3';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css';
import {
    Bot,
    ChevronsDown,
    ChevronsUp,
    Download,
    ImagePlus,
    Send,
    User,
    X,
} from 'lucide-vue-next';
import MarkdownIt from 'markdown-it';
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    conversations: Array,
    currentConversation: Object,
    models: Array,
    selectedModel: String,
    error: String,
    failedModels: Array,
    systemPresets: Array,
    userPresets: Array,
});

// Markdown renderer
const md = new MarkdownIt({
    highlight: function (str, lang) {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return hljs.highlight(str, { language: lang }).value;
            } catch (__) {}
        }
        return '';
    },
});

// Form pour nouveau message
const messageForm = useForm({
    content: '',
    image: null as File | null,
});

// Form pour nouvelle conversation
const newConversationForm = useForm({
    model: props.selectedModel,
    instruction_preset_id: null as number | null,
});

const selectedPreset = ref<any>(null);

// Form pour changer le modèle
const modelForm = useForm({
    model: props.selectedModel,
});

// Instructions personnalisées par conversation
const showConversationInstructions = ref(false);
const conversationInstructionsForm = useForm({
    custom_instructions_about: '',
    custom_instructions_behavior: '',
    custom_instructions_commands: '',
});

const openConversationInstructions = () => {
    if (!props.currentConversation) return;
    const preset = props.currentConversation.instruction_preset || null;
    if (preset) {
        conversationInstructionsForm.custom_instructions_about =
            preset.about || '';
        conversationInstructionsForm.custom_instructions_behavior =
            preset.behavior || '';
        conversationInstructionsForm.custom_instructions_commands =
            preset.commands || '';
    } else {
        conversationInstructionsForm.custom_instructions_about =
            props.currentConversation.custom_instructions_about || '';
        conversationInstructionsForm.custom_instructions_behavior =
            props.currentConversation.custom_instructions_behavior || '';
        conversationInstructionsForm.custom_instructions_commands =
            props.currentConversation.custom_instructions_commands || '';
    }
    showConversationInstructions.value = true;
};

const saveConversationInstructions = () => {
    if (!props.currentConversation) return;

    conversationInstructionsForm.patch(
        `/conversations/${props.currentConversation.id}/custom-instructions`,
        {
            preserveScroll: true,
            onSuccess: () => {
                showConversationInstructions.value = false;
            },
        },
    );
};

const resetConversationInstructions = () => {
    conversationInstructionsForm.custom_instructions_about = '';
    conversationInstructionsForm.custom_instructions_behavior = '';
    conversationInstructionsForm.custom_instructions_commands = '';
};

const applyPresetToConversationInstructions = () => {
    if (!props.currentConversation?.instruction_preset) return;

    const preset = props.currentConversation.instruction_preset;
    conversationInstructionsForm.custom_instructions_about = preset.about || '';
    conversationInstructionsForm.custom_instructions_behavior =
        preset.behavior || '';
    conversationInstructionsForm.custom_instructions_commands =
        preset.commands || '';
};

// Édition du titre
const isEditingTitle = ref(false);
const titleForm = useForm({
    title: '',
});

const startEditingTitle = () => {
    titleForm.title =
        props.currentConversation?.title || 'Nouvelle conversation';
    isEditingTitle.value = true;
    nextTick(() => {
        document.getElementById('title-input')?.focus();
    });
};

const saveTitle = () => {
    if (!props.currentConversation) return;

    titleForm.patch(`/conversations/${props.currentConversation.id}/title`, {
        preserveScroll: true,
        onSuccess: () => {
            isEditingTitle.value = false;
        },
    });
};

const cancelEditingTitle = () => {
    isEditingTitle.value = false;
    titleForm.reset();
};

const regenerateTitle = () => {
    if (!props.currentConversation) return;

    router.post(
        `/conversations/${props.currentConversation.id}/regenerate-title`,
        {},
        {
            preserveScroll: true,
        },
    );
};

const exportChat = () => {
    if (!props.currentConversation) return;

    const exportData = {
        title: props.currentConversation.title,
        model: props.currentConversation.model,
        created_at: props.currentConversation.created_at,
        updated_at: props.currentConversation.updated_at,
        messages:
            props.currentConversation.messages?.map((msg: any) => ({
                role: msg.role,
                content: msg.content,
                created_at: msg.created_at,
            })) || [],
    };

    // Créer le JSON
    const jsonString = JSON.stringify(exportData, null, 2);
    const blob = new Blob([jsonString], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `conversation_${props.currentConversation.id}_${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
};

const isLoadingResponse = ref(false);
const streamingContent = ref('');
const streamingRendered = ref('');
const showModelSelector = ref(false);
const modelSelectorRef = ref<HTMLElement | null>(null);
const messageInputRef = ref<HTMLTextAreaElement | null>(null);
const showOnlyFree = ref(false);
const showOnlyWithImages = ref(false);
const hideFailedModels = ref(false);
const selectedImage = ref<File | null>(null);
const streamQueue = ref<string[]>([]);
const isProcessingQueue = ref(false);

// Computed pour le rendu markdown optimisé
const streamingMarkdown = computed(() => {
    if (!streamingContent.value) return '';
    return md.render(streamingContent.value);
});

// Process stream queue avec throttling pour un effet "typewriter"
const processStreamQueue = async () => {
    if (isProcessingQueue.value || streamQueue.value.length === 0) return;

    isProcessingQueue.value = true;

    while (streamQueue.value.length > 0) {
        const chunk = streamQueue.value.shift();
        if (chunk) {
            streamingContent.value += chunk;
            scrollToBottom();

            // Attendre un peu pour l'effet typewriter (ajuster selon le besoin)
            // Plus le délai est court, plus c'est rapide
            if (chunk.length > 1) {
                await new Promise((resolve) => setTimeout(resolve, 5)); // 5ms de délai
            }
        }
    }

    isProcessingQueue.value = false;
};
const imagePreviewUrl = ref<string | null>(null);
const messagesContainer = ref<HTMLElement | null>(null);
const showScrollTop = ref(false);

// Fermer le dropdown quand on clique en dehors
const handleClickOutside = (event: MouseEvent) => {
    if (
        modelSelectorRef.value &&
        !modelSelectorRef.value.contains(event.target as Node)
    ) {
        showModelSelector.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    scrollToBottom();
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Auto-scroll vers le bas quand les messages changent
watch(
    () => props.currentConversation?.messages,
    () => {
        nextTick(() => {
            scrollToBottom();
        });
    },
    { deep: true },
);

// Détecter le scroll pour afficher le bouton "remonter"
const handleScroll = () => {
    if (messagesContainer.value) {
        showScrollTop.value = messagesContainer.value.scrollTop > 300;
    }
};

// Scroll vers le bas
const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop =
            messagesContainer.value.scrollHeight;
    }
};

// Scroll vers le haut (début de conversation)
const scrollToTop = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

// Scroll vers le dernier message
const scrollToLastMessage = () => {
    if (
        props.currentConversation?.messages &&
        props.currentConversation.messages.length > 0
    ) {
        const lastMessageId =
            props.currentConversation.messages[
                props.currentConversation.messages.length - 1
            ].id;
        const lastMessageElement = document.getElementById(
            `message-${lastMessageId}`,
        );
        if (lastMessageElement) {
            lastMessageElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });
        }
    }
};

// Changer le modèle de la conversation actuelle
const changeModel = (modelId: string) => {
    if (!props.currentConversation) return;

    router.patch(
        `/conversations/${props.currentConversation.id}`,
        {
            model: modelId,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                showModelSelector.value = false;
            },
        },
    );
};

// Obtenir le nom du modèle actuel
const currentModelName = computed(() => {
    if (!props.currentConversation) return props.selectedModel;
    const model = props.models?.find(
        (m) => m.id === props.currentConversation.model,
    );
    return model?.name || props.currentConversation.model;
});

// Vérifier si le modèle actuel supporte les images
const currentModelSupportsImages = computed(() => {
    if (!props.currentConversation || !props.models) return false;
    const model = props.models.find(
        (m) => m.id === props.currentConversation.model,
    );
    return model?.supports_image || false;
});

// Filtrer les modèles
const filteredModels = computed(() => {
    if (!props.models) return [];

    let filtered = props.models;

    if (showOnlyFree.value) {
        filtered = filtered.filter((m) => m.is_free);
    }

    if (showOnlyWithImages.value) {
        filtered = filtered.filter((m) => m.supports_image);
    }

    if (hideFailedModels.value && props.failedModels) {
        filtered = filtered.filter((m) => !props.failedModels.includes(m.id));
    }

    return filtered;
});

// Créer une nouvelle conversation
const createConversation = () => {
    newConversationForm.post(storeConversation().url, {
        preserveState: true,
        onSuccess: () => {
            selectedPreset.value = null;
            newConversationForm.instruction_preset_id = null;
        },
    });
};

// Sélectionner un preset
const selectPreset = (preset: any) => {
    selectedPreset.value = preset;
    newConversationForm.instruction_preset_id = preset.id;
    if (preset.preferred_model) {
        newConversationForm.model = preset.preferred_model;
    }
};

// Désélectionner le preset
const clearPreset = () => {
    selectedPreset.value = null;
    newConversationForm.instruction_preset_id = null;
};

// Gérer la sélection d'image
const handleImageSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        selectedImage.value = file;
        messageForm.image = file;

        // Créer une preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviewUrl.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

// Supprimer l'image sélectionnée
const removeImage = () => {
    selectedImage.value = null;
    messageForm.image = null;
    imagePreviewUrl.value = null;
};

// Envoyer un message avec streaming
const sendMessage = async () => {
    if (!props.currentConversation || !messageForm.content.trim()) return;

    isLoadingResponse.value = true;
    streamingContent.value = '';
    streamQueue.value = []; // Reset la queue

    const formData = new FormData();
    formData.append('content', messageForm.content);
    if (messageForm.image) {
        formData.append('image', messageForm.image);
    }

    console.log('[STREAM] Starting request...');

    try {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');
        const response = await fetch(
            `/conversations/${props.currentConversation.id}/messages/stream`,
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    Accept: 'text/event-stream',
                },
                body: formData,
            },
        );

        console.log('[STREAM] Response received, status:', response.status);

        if (!response.ok) {
            throw new Error('Erreur réseau');
        }

        const reader = response.body?.getReader();
        const decoder = new TextDecoder();
        let buffer = '';

        console.log('[STREAM] Starting to read chunks...');

        if (reader) {
            while (true) {
                const { done, value } = await reader.read();

                if (done) {
                    console.log('[STREAM] Stream done');
                    break;
                }

                const decoded = decoder.decode(value, { stream: true });

                buffer += decoded;
                const lines = buffer.split('\n\n');
                buffer = lines.pop() || '';

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        try {
                            const data = JSON.parse(line.slice(6));

                            if (data.error) {
                                console.error('Streaming error:', data.error);
                                streamingContent.value = '';
                                streamQueue.value = [];
                                break;
                            }

                            if (data.content) {
                                // Ajouter à la queue au lieu d'afficher directement
                                streamQueue.value.push(data.content);

                                // Démarrer le traitement de la queue si pas déjà en cours
                                if (!isProcessingQueue.value) {
                                    processStreamQueue();
                                }
                            }

                            if (data.done) {
                                console.log('[STREAM] Received done signal');
                                // Attendre que la queue soit vide avant de recharger
                                await new Promise((resolve) => {
                                    const checkQueue = setInterval(() => {
                                        if (
                                            streamQueue.value.length === 0 &&
                                            !isProcessingQueue.value
                                        ) {
                                            clearInterval(checkQueue);
                                            resolve(undefined);
                                        }
                                    }, 50);
                                });

                                streamingContent.value = '';
                                router.reload({
                                    only: [
                                        'conversations',
                                        'currentConversation',
                                    ],
                                });
                                break;
                            }
                        } catch (e) {
                            console.error('Parse error:', e, 'Line:', line);
                        }
                    }
                }
            }
        }

        messageForm.reset();
        removeImage();
        scrollToBottom();
    } catch (error) {
        console.error('Error sending message:', error);
        streamingContent.value = '';
    } finally {
        isLoadingResponse.value = false;
        setTimeout(() => {
            messageInputRef.value?.focus();
        }, 100);
    }
};

// Naviguer vers une conversation
const selectConversation = (conversationId: number) => {
    router.visit(`/conversations/${conversationId}`);
};

// Supprimer une conversation
const deleteConversation = (conversationId: number) => {
    if (confirm('Voulez-vous vraiment supprimer cette conversation ?')) {
        router.delete(`/conversations/${conversationId}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout>
        <div
            class="flex h-[calc(100vh-4rem)] overflow-visible bg-linear-to-br from-gray-50 via-blue-50/20 to-indigo-50/20 dark:from-gray-950 dark:via-blue-950/20 dark:to-indigo-950/20"
        >
            <!-- Sidebar - Liste des conversations -->
            <aside
                class="w-64 overflow-y-auto border-r border-blue-200/50 bg-blue-50/80 shadow-lg backdrop-blur-xl dark:border-blue-800/30 dark:bg-blue-950/80"
                aria-label="Liste des conversations"
                role="complementary"
            >
                <div class="space-y-3 p-4">
                    <!-- Sélection de preset optionnel -->
                    <div
                        v-if="systemPresets?.length || userPresets?.length"
                        class="space-y-2"
                    >
                        <label
                            id="preset-label"
                            class="text-xs font-medium text-gray-600 dark:text-gray-400"
                        >
                            Preset (optionnel)
                        </label>

                        <div
                            class="grid max-h-44 gap-1 overflow-y-auto"
                            role="radiogroup"
                            aria-labelledby="preset-label"
                        >
                            <!-- Option "Aucun" -->
                            <button
                                @click="clearPreset"
                                :class="[
                                    'group flex w-full items-center gap-2 overflow-hidden rounded-xl border px-3 py-2 text-left text-xs transition-all duration-200',
                                    !selectedPreset
                                        ? 'border-blue-300 bg-blue-100/70 text-blue-900 shadow-sm dark:border-blue-700 dark:bg-blue-900/70 dark:text-blue-200'
                                        : 'border-transparent text-blue-800 hover:border-blue-200 hover:bg-blue-50/70 dark:text-blue-400 dark:hover:border-blue-800/40 dark:hover:bg-blue-900/40',
                                ]"
                                role="radio"
                                :aria-checked="!selectedPreset"
                                type="button"
                            >
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50/80 text-base shadow-sm transition-transform duration-200 group-hover:scale-105 dark:bg-blue-950/70"
                                    aria-hidden="true"
                                >
                                    ⭕
                                </span>
                                <span
                                    class="min-w-0 flex-1 truncate font-medium whitespace-nowrap"
                                >
                                    Aucun preset
                                </span>
                                <span
                                    class="h-2 w-2 shrink-0 rounded-full border"
                                    :class="
                                        !selectedPreset
                                            ? 'border-blue-500 bg-blue-500 shadow-sm'
                                            : 'border-blue-300 dark:border-blue-600'
                                    "
                                ></span>
                            </button>

                            <button
                                v-for="preset in [
                                    ...(systemPresets || []),
                                    ...(userPresets || []),
                                ]"
                                :key="preset.id"
                                @click="selectPreset(preset)"
                                :class="[
                                    'group flex w-full items-center gap-2 overflow-hidden rounded-xl border px-3 py-2 text-left text-xs transition-all duration-200',
                                    selectedPreset?.id === preset.id
                                        ? 'border-blue-300 bg-linear-to-r from-blue-500/12 to-blue-600/12 text-blue-900 shadow-sm dark:border-blue-700 dark:from-blue-500/20 dark:to-blue-600/20 dark:text-blue-200'
                                        : 'border-transparent text-blue-900 hover:border-blue-200 hover:bg-blue-50/70 dark:text-blue-300 dark:hover:border-blue-800/40 dark:hover:bg-blue-900/60',
                                ]"
                            >
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50/80 text-base shadow-sm transition-transform duration-200 group-hover:scale-105 dark:bg-gray-900/70"
                                >
                                    {{ preset.icon }}
                                </span>
                                <span
                                    class="min-w-0 flex-1 truncate font-medium whitespace-nowrap"
                                >
                                    {{ preset.name }}
                                </span>
                                <span
                                    class="h-2 w-2 shrink-0 rounded-full border"
                                    :class="
                                        selectedPreset?.id === preset.id
                                            ? 'border-blue-500 bg-blue-500 shadow-sm'
                                            : 'border-gray-300 dark:border-gray-600'
                                    "
                                ></span>
                            </button>
                        </div>
                    </div>

                    <button
                        @click="createConversation"
                        class="w-full rounded-xl bg-linear-to-r from-blue-600 to-blue-700 px-4 py-3 font-medium text-white shadow-lg transition hover:scale-[1.02] hover:from-blue-700 hover:to-blue-800 hover:shadow-xl active:scale-95"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                            Nouvelle conversation
                        </span>
                    </button>
                </div>

                <div class="px-2">
                    <div
                        v-for="conversation in conversations"
                        :key="conversation.id"
                        @click="selectConversation(conversation.id)"
                        :class="[
                            'group relative mb-2 cursor-pointer rounded-xl border-2 p-3 transition-all duration-200',
                            currentConversation?.id === conversation.id
                                ? 'border-blue-300 bg-linear-to-r from-blue-50 to-blue-100 shadow-md dark:border-blue-700 dark:from-blue-900/30 dark:to-blue-800/30'
                                : 'border-transparent bg-blue-50/50 hover:border-blue-200 hover:shadow-md dark:bg-blue-900/30 dark:hover:border-blue-700',
                        ]"
                    >
                        <div class="flex items-start justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p
                                        class="truncate text-sm font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{
                                            conversation.title ||
                                            'Nouvelle conversation'
                                        }}
                                    </p>
                                    <!-- Badge preset dans sidebar -->
                                    <span
                                        v-if="conversation.instruction_preset"
                                        class="shrink-0 text-base"
                                        :title="
                                            conversation.instruction_preset.name
                                        "
                                    >
                                        {{
                                            conversation.instruction_preset.icon
                                        }}
                                    </span>
                                </div>
                                <p
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{
                                        new Date(
                                            conversation.updated_at,
                                        ).toLocaleDateString('fr-FR')
                                    }}
                                </p>
                            </div>
                            <button
                                @click.stop="
                                    deleteConversation(conversation.id)
                                "
                                class="text-gray-400 opacity-0 transition group-hover:opacity-100 hover:text-red-600"
                                aria-label="Supprimer la conversation"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Zone principale -->
            <div class="flex flex-1 flex-col overflow-visible">
                <!-- Header avec sélecteur de modèle -->
                <div
                    v-if="currentConversation"
                    class="relative z-20 overflow-visible border-b border-blue-200/50 bg-blue-50/80 p-5 shadow-lg backdrop-blur-xl dark:border-blue-800/30 dark:bg-blue-950/80"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                v-if="!isEditingTitle"
                                class="group flex items-center gap-2"
                            >
                                <h2
                                    class="text-xl font-bold text-gray-900 dark:text-gray-100"
                                >
                                    {{
                                        currentConversation.title ||
                                        'Nouvelle conversation'
                                    }}
                                </h2>
                                <button
                                    @click="startEditingTitle"
                                    class="rounded-lg p-1.5 text-gray-600 opacity-0 transition-opacity group-hover:opacity-100 hover:bg-blue-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-400"
                                    title="Modifier le titre"
                                    aria-label="Modifier le titre"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                        />
                                    </svg>
                                </button>
                                <button
                                    @click="regenerateTitle"
                                    class="rounded-lg p-1.5 text-gray-600 opacity-0 transition-opacity group-hover:opacity-100 hover:bg-purple-100 hover:text-purple-600 dark:text-gray-400 dark:hover:bg-purple-900/30 dark:hover:text-purple-400"
                                    title="Régénérer le titre avec l'IA"
                                    aria-label="Régénérer le titre avec l'IA"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"
                                        />
                                    </svg>
                                </button>
                                <button
                                    @click="exportChat"
                                    class="rounded-lg p-1.5 text-gray-600 opacity-0 transition-opacity group-hover:opacity-100 hover:bg-green-100 hover:text-green-600 dark:text-gray-400 dark:hover:bg-green-900/30 dark:hover:text-green-400"
                                    title="Exporter la conversation"
                                    aria-label="Exporter la conversation"
                                    type="button"
                                >
                                    <Download
                                        class="h-4 w-4"
                                        aria-hidden="true"
                                    />
                                </button>
                            </div>
                            <div v-else class="flex items-center gap-2">
                                <input
                                    id="title-input"
                                    v-model="titleForm.title"
                                    type="text"
                                    class="rounded-lg border-2 border-blue-300 bg-blue-50 px-3 py-1.5 text-xl font-bold text-blue-900 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-blue-700 dark:bg-blue-900 dark:text-blue-100"
                                    @keyup.enter="saveTitle"
                                    @keyup.escape="cancelEditingTitle"
                                />
                                <button
                                    @click="saveTitle"
                                    :disabled="titleForm.processing"
                                    class="rounded-lg bg-blue-600 p-1.5 text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    title="Enregistrer"
                                    aria-label="Enregistrer le titre"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </button>
                                <button
                                    @click="cancelEditingTitle"
                                    class="rounded-lg p-1.5 text-blue-600 hover:bg-blue-200 dark:text-blue-400 dark:hover:bg-blue-800"
                                    title="Annuler"
                                    aria-label="Annuler la modification"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <!-- Badge Preset -->
                            <div
                                v-if="currentConversation.instruction_preset"
                                class="flex items-center gap-2 rounded-full border-2 border-blue-300 bg-linear-to-r from-blue-100 to-indigo-100 px-4 py-2 shadow-md dark:border-blue-700 dark:from-blue-900/30 dark:to-indigo-900/30"
                            >
                                <span class="text-xl">{{
                                    currentConversation.instruction_preset.icon
                                }}</span>
                                <span
                                    class="text-sm font-bold text-blue-800 dark:text-blue-200"
                                >
                                    {{
                                        currentConversation.instruction_preset
                                            .name
                                    }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="openConversationInstructions"
                                class="inline-flex items-center gap-2 rounded-xl border-2 border-blue-200/60 bg-blue-50/80 px-3 py-2 text-sm font-semibold text-blue-900 shadow-sm transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-blue-700/60 dark:bg-blue-950/80 dark:text-blue-300"
                                title="Instructions pour ce chat"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 11a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 11v4m0-8h.01"
                                    />
                                </svg>
                                Chat
                            </button>
                            <div class="relative" ref="modelSelectorRef">
                                <button
                                    @click="
                                        showModelSelector = !showModelSelector
                                    "
                                    class="dark:to-gray-750 flex items-center gap-2 rounded-xl border-2 border-blue-200/50 bg-linear-to-r from-white to-gray-50 px-4 py-2.5 text-sm font-medium text-gray-700 shadow-md transition-all duration-200 hover:scale-[1.02] hover:border-blue-400 hover:shadow-lg dark:border-blue-700/50 dark:from-gray-700 dark:text-gray-300 dark:hover:border-blue-600"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <span>{{ currentModelName }}</span>
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </button>

                                <!-- Dropdown des modèles -->
                                <div
                                    v-if="showModelSelector"
                                    class="absolute right-0 z-50 mt-2 max-h-96 w-80 overflow-y-auto rounded-xl border border-blue-200/50 bg-blue-50/95 shadow-2xl backdrop-blur-xl dark:border-blue-800/30 dark:bg-gray-800/95"
                                >
                                    <div
                                        class="border-b border-blue-200/50 p-3 dark:border-blue-800/30"
                                    >
                                        <div
                                            class="px-3 py-2 text-xs font-bold tracking-wide text-blue-700 uppercase dark:text-blue-300"
                                        >
                                            Sélectionner un modèle
                                        </div>

                                        <!-- Filtres -->
                                        <div
                                            class="mt-2 flex flex-col space-y-2"
                                        >
                                            <label
                                                class="flex cursor-pointer items-center space-x-2"
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="showOnlyFree"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <span
                                                    class="text-sm text-gray-700 dark:text-gray-300"
                                                    >Gratuits uniquement</span
                                                >
                                            </label>

                                            <label
                                                class="flex cursor-pointer items-center space-x-2"
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="showOnlyWithImages"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <span
                                                    class="text-sm text-gray-700 dark:text-gray-300"
                                                    >Avec support images</span
                                                >
                                            </label>

                                            <label
                                                v-if="
                                                    failedModels &&
                                                    failedModels.length > 0
                                                "
                                                class="flex cursor-pointer items-center space-x-2"
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="hideFailedModels"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <span
                                                    class="text-sm text-gray-700 dark:text-gray-300"
                                                    >Masquer modèles en erreur
                                                    ({{
                                                        failedModels.length
                                                    }})</span
                                                >
                                            </label>
                                        </div>

                                        <div class="mt-2 text-xs text-gray-500">
                                            {{
                                                filteredModels.length
                                            }}
                                            modèle(s)
                                        </div>
                                    </div>

                                    <div class="p-2">
                                        <button
                                            v-for="model in filteredModels"
                                            :key="model.id"
                                            @click="changeModel(model.id)"
                                            :class="[
                                                'group mb-1.5 w-full rounded-xl border-2 px-3 py-2.5 text-left transition-all duration-200',
                                                currentConversation.model ===
                                                model.id
                                                    ? 'border-blue-300 bg-linear-to-r from-blue-50 to-indigo-50 text-blue-700 shadow-md dark:border-blue-700 dark:from-blue-900/30 dark:to-indigo-900/30 dark:text-blue-300'
                                                    : 'dark:hover:bg-gray-750 border-transparent text-gray-700 hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-600',
                                            ]"
                                        >
                                            <div
                                                class="flex items-center justify-between"
                                            >
                                                <div
                                                    class="text-sm font-medium"
                                                >
                                                    {{ model.name }}
                                                </div>
                                                <div
                                                    class="flex items-center space-x-1"
                                                >
                                                    <span
                                                        v-if="model.is_free"
                                                        class="rounded-full bg-linear-to-r from-green-500 to-emerald-500 px-2.5 py-0.5 text-xs font-bold text-white shadow-sm"
                                                    >
                                                        GRATUIT
                                                    </span>
                                                    <span
                                                        v-if="
                                                            model.supports_image
                                                        "
                                                        class="text-xs"
                                                        title="Support images"
                                                    >
                                                        🖼️
                                                    </span>
                                                </div>
                                            </div>
                                            <div
                                                class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                {{ model.description }}
                                            </div>
                                        </button>

                                        <div
                                            v-if="filteredModels.length === 0"
                                            class="py-4 text-center text-sm text-gray-500"
                                        >
                                            Aucun modèle ne correspond aux
                                            filtres
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <Dialog v-model:open="showConversationInstructions">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Instructions du chat</DialogTitle>
                            <DialogDescription>
                                Ces instructions s'appliquent uniquement a cette
                                conversation et ont priorite sur les presets.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="space-y-4 py-4">
                            <div class="space-y-2">
                                <Label for="chat-about"
                                    >A propos de l'utilisateur</Label
                                >
                                <Textarea
                                    id="chat-about"
                                    v-model="
                                        conversationInstructionsForm.custom_instructions_about
                                    "
                                    placeholder="Contexte personnel ou professionnel..."
                                    rows="3"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="chat-behavior"
                                    >Comportement attendu</Label
                                >
                                <Textarea
                                    id="chat-behavior"
                                    v-model="
                                        conversationInstructionsForm.custom_instructions_behavior
                                    "
                                    placeholder="Ton, style, format..."
                                    rows="3"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="chat-commands"
                                    >Commandes personnalisees</Label
                                >
                                <Textarea
                                    id="chat-commands"
                                    v-model="
                                        conversationInstructionsForm.custom_instructions_commands
                                    "
                                    placeholder="Commandes ou raccourcis propres a ce chat..."
                                    rows="3"
                                />
                            </div>
                        </div>

                        <DialogFooter>
                            <Button
                                variant="outline"
                                @click="resetConversationInstructions"
                            >
                                Vider
                            </Button>
                            <Button
                                v-if="currentConversation?.instruction_preset"
                                variant="outline"
                                @click="applyPresetToConversationInstructions"
                            >
                                Appliquer le preset
                            </Button>
                            <Button
                                variant="outline"
                                @click="showConversationInstructions = false"
                            >
                                Annuler
                            </Button>
                            <Button
                                @click="saveConversationInstructions"
                                :disabled="
                                    conversationInstructionsForm.processing
                                "
                            >
                                Enregistrer
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <!-- Zone des messages -->
                <main
                    ref="messagesContainer"
                    @scroll="handleScroll"
                    class="relative flex-1 space-y-4 overflow-y-auto bg-linear-to-b from-gray-50 to-white p-6 dark:from-gray-900 dark:to-gray-800"
                    role="main"
                    aria-label="Messages de conversation"
                >
                    <div
                        v-if="!currentConversation"
                        class="flex h-full items-center justify-center"
                    >
                        <div class="max-w-md space-y-6 px-6 text-center">
                            <div class="relative inline-block">
                                <div
                                    class="animate-pulse-soft absolute inset-0 rounded-full bg-blue-500/20 blur-3xl"
                                ></div>
                                <svg
                                    class="relative mx-auto h-24 w-24 text-blue-500/80 dark:text-blue-400/80"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                    />
                                </svg>
                            </div>
                            <div class="space-y-2">
                                <p
                                    class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    Commencez une conversation
                                </p>
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Sélectionnez une conversation existante ou
                                    créez-en une nouvelle pour démarrer
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-else>
                        <!-- Boutons de navigation -->
                        <div
                            class="fixed right-8 bottom-28 z-10 flex flex-col gap-3"
                        >
                            <!-- Début de conversation -->
                            <Transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 scale-95 translate-y-2"
                                enter-to-class="opacity-100 scale-100 translate-y-0"
                                leave-active-class="transition ease-in duration-150"
                                leave-from-class="opacity-100 scale-100 translate-y-0"
                                leave-to-class="opacity-0 scale-95 translate-y-2"
                            >
                                <button
                                    v-if="showScrollTop"
                                    @click="scrollToTop"
                                    class="group flex items-center gap-2 rounded-full border-2 border-blue-200 bg-blue-50/90 px-4 py-3 text-gray-700 shadow-xl backdrop-blur-sm transition-all hover:scale-105 hover:bg-linear-to-r hover:from-blue-600 hover:to-blue-700 hover:text-white hover:shadow-2xl dark:border-blue-700 dark:bg-gray-800/90 dark:text-gray-200"
                                    title="Début de conversation"
                                    aria-label="Aller au début de la conversation"
                                >
                                    <ChevronsUp
                                        class="h-5 w-5 group-hover:animate-bounce"
                                        aria-hidden="true"
                                    />
                                    <span
                                        class="hidden text-sm font-medium sm:inline"
                                        >Début</span
                                    >
                                </button>
                            </Transition>

                            <!-- Dernier message -->
                            <Transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 scale-95 translate-y-2"
                                enter-to-class="opacity-100 scale-100 translate-y-0"
                                leave-active-class="transition ease-in duration-150"
                                leave-from-class="opacity-100 scale-100 translate-y-0"
                                leave-to-class="opacity-0 scale-95 translate-y-2"
                            >
                                <button
                                    v-if="showScrollTop"
                                    @click="scrollToLastMessage"
                                    class="group flex items-center gap-2 rounded-full border-2 border-green-200 bg-blue-50/90 px-4 py-3 text-gray-700 shadow-xl backdrop-blur-sm transition-all hover:scale-105 hover:bg-linear-to-r hover:from-green-600 hover:to-emerald-700 hover:text-white hover:shadow-2xl dark:border-green-700 dark:bg-gray-800/90 dark:text-gray-200"
                                    title="Dernier message"
                                    aria-label="Aller au dernier message"
                                >
                                    <ChevronsDown
                                        class="h-5 w-5 group-hover:animate-bounce"
                                        aria-hidden="true"
                                    />
                                    <span
                                        class="hidden text-sm font-medium sm:inline"
                                        >Dernier</span
                                    >
                                </button>
                            </Transition>
                        </div>

                        <!-- Messages -->
                        <div
                            v-for="(
                                message, index
                            ) in currentConversation.messages"
                            :key="message.id"
                            :id="`message-${message.id}`"
                            class="animate-in scroll-mt-4 duration-500 fade-in slide-in-from-bottom-4"
                        >
                            <!-- Ancre de navigation -->
                            <a
                                v-if="index > 0"
                                :href="`#message-${currentConversation.messages[index - 1].id}`"
                                class="absolute mt-2 -ml-8 text-xs text-gray-400 opacity-0 transition-opacity hover:text-blue-600 hover:opacity-100"
                                title="Message précédent"
                            >
                                ↑
                            </a>

                            <!-- Message système -->
                            <div
                                v-if="message.role === 'system'"
                                class="mx-auto my-6 max-w-3xl"
                            >
                                <div
                                    class="rounded-xl border border-blue-200 bg-linear-to-r from-blue-50 to-indigo-50 px-6 py-4 shadow-sm dark:border-blue-800 dark:from-blue-950/50 dark:to-indigo-950/50"
                                >
                                    <div
                                        class="prose prose-sm max-w-none prose-slate dark:prose-invert"
                                        v-html="md.render(message.content)"
                                    />
                                </div>
                            </div>

                            <!-- Messages utilisateur et assistant -->
                            <div
                                v-else
                                :class="[
                                    'group mb-8 flex gap-4',
                                    message.role === 'user'
                                        ? 'flex-row-reverse'
                                        : 'flex-row',
                                ]"
                            >
                                <!-- Avatar -->
                                <div class="shrink-0">
                                    <div
                                        :class="[
                                            'flex h-10 w-10 items-center justify-center rounded-full shadow-md',
                                            message.role === 'user'
                                                ? 'bg-linear-to-br from-blue-500 to-blue-600 text-white'
                                                : 'bg-linear-to-br from-purple-500 to-indigo-600 text-white',
                                        ]"
                                    >
                                        <User
                                            v-if="message.role === 'user'"
                                            class="h-5 w-5"
                                            aria-hidden="true"
                                        />
                                        <Bot
                                            v-else
                                            class="h-5 w-5"
                                            aria-hidden="true"
                                        />
                                    </div>
                                </div>

                                <!-- Contenu du message -->
                                <div
                                    :class="[
                                        'max-w-3xl flex-1',
                                        message.role === 'user'
                                            ? 'mr-2'
                                            : 'ml-2',
                                    ]"
                                >
                                    <!-- Message utilisateur -->
                                    <div
                                        v-if="message.role === 'user'"
                                        class="rounded-2xl bg-linear-to-br from-blue-600 to-blue-700 px-5 py-3 text-white shadow-lg"
                                    >
                                        <template
                                            v-if="
                                                typeof message.content ===
                                                    'string' &&
                                                message.content.startsWith('[')
                                            "
                                        >
                                            <!-- Message multimodal (texte + image) -->
                                            <template
                                                v-for="(
                                                    part, index
                                                ) in JSON.parse(
                                                    message.content,
                                                )"
                                                :key="index"
                                            >
                                                <p
                                                    v-if="part.type === 'text'"
                                                    class="leading-relaxed whitespace-pre-wrap"
                                                >
                                                    {{ part.text }}
                                                </p>
                                                <img
                                                    v-if="
                                                        part.type ===
                                                        'image_url'
                                                    "
                                                    :src="part.image_url.url"
                                                    alt="Image jointe au message"
                                                    class="mt-3 max-w-sm rounded-xl border-2 border-white/20 shadow-lg"
                                                />
                                            </template>
                                        </template>
                                        <p
                                            v-else
                                            class="leading-relaxed whitespace-pre-wrap"
                                        >
                                            {{ message.content }}
                                        </p>
                                    </div>

                                    <!-- Message assistant -->
                                    <div
                                        v-else
                                        class="rounded-2xl border border-blue-100 bg-blue-50/80 px-5 py-4 shadow-md dark:border-gray-700 dark:bg-gray-800"
                                    >
                                        <div
                                            class="prose max-w-none prose-slate dark:prose-invert prose-p:leading-relaxed prose-pre:bg-gray-900 prose-pre:shadow-lg"
                                            v-html="md.render(message.content)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message en streaming -->
                        <div v-if="isLoadingResponse" class="mb-8 flex gap-4">
                            <!-- Avatar Bot -->
                            <div class="shrink-0">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-linear-to-br from-purple-500 to-indigo-600 text-white shadow-md"
                                    role="img"
                                    aria-label="Assistant IA"
                                >
                                    <Bot class="h-5 w-5" aria-hidden="true" />
                                </div>
                            </div>

                            <!-- Contenu en streaming ou loader -->
                            <div class="ml-2 max-w-3xl flex-1">
                                <div
                                    class="rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-md dark:border-gray-700 dark:bg-gray-800"
                                >
                                    <div
                                        v-if="streamingContent"
                                        class="prose max-w-none prose-slate dark:prose-invert prose-p:leading-relaxed prose-pre:bg-gray-900 prose-pre:shadow-lg"
                                    >
                                        <div v-html="streamingMarkdown"></div>
                                        <span
                                            class="ml-0.5 inline-block h-5 w-2 animate-pulse bg-purple-500"
                                        ></span>
                                    </div>
                                    <div
                                        v-else
                                        class="flex items-center space-x-2"
                                    >
                                        <div
                                            class="h-2.5 w-2.5 animate-bounce rounded-full bg-linear-to-r from-purple-500 to-indigo-500"
                                            style="animation-delay: 0s"
                                        ></div>
                                        <div
                                            class="h-2.5 w-2.5 animate-bounce rounded-full bg-linear-to-r from-purple-500 to-indigo-500"
                                            style="animation-delay: 0.2s"
                                        ></div>
                                        <div
                                            class="h-2.5 w-2.5 animate-bounce rounded-full bg-linear-to-r from-purple-500 to-indigo-500"
                                            style="animation-delay: 0.4s"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                <!-- Zone de saisie -->
                <div
                    v-if="currentConversation"
                    class="border-t border-gray-200 bg-linear-to-b from-white to-gray-50 p-6 dark:border-gray-700 dark:from-gray-800 dark:to-gray-900"
                >
                    <!-- Message d'erreur -->
                    <div
                        v-if="error"
                        class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 shadow-sm dark:border-red-800 dark:bg-red-900/20 dark:text-red-400"
                    >
                        <div class="flex items-start gap-3">
                            <svg
                                class="mr-2 h-5 w-5 shrink-0"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span>{{ error }}</span>
                        </div>
                    </div>

                    <!-- Preview de l'image -->
                    <div
                        v-if="imagePreviewUrl"
                        class="relative mb-4 inline-block"
                    >
                        <img
                            :src="imagePreviewUrl"
                            alt="Aperçu de l'image à envoyer"
                            class="max-h-40 rounded-xl border-2 border-blue-200 shadow-lg"
                        />
                        <button
                            @click="removeImage"
                            type="button"
                            class="absolute -top-3 -right-3 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white shadow-lg transition hover:scale-110 hover:bg-red-600"
                            aria-label="Supprimer l'image"
                        >
                            <X class="h-4 w-4" aria-hidden="true" />
                        </button>
                    </div>

                    <form
                        @submit.prevent="sendMessage"
                        class="flex items-end gap-3"
                    >
                        <!-- Bouton d'upload d'image -->
                        <button
                            v-if="currentModelSupportsImages"
                            type="button"
                            @click="() => $refs.fileInput?.click()"
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border-2 border-blue-200 bg-blue-50 text-gray-600 shadow-sm transition hover:border-blue-400 hover:bg-blue-100 hover:text-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:border-blue-500 dark:hover:bg-gray-600"
                            aria-label="Joindre une image"
                        >
                            <ImagePlus class="h-5 w-5" aria-hidden="true" />
                            <input
                                ref="fileInput"
                                type="file"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                @change="handleImageSelect"
                                class="hidden"
                                aria-label="Sélectionner un fichier image"
                            />
                        </button>

                        <div class="relative flex-1">
                            <label for="message-input" class="sr-only"
                                >Message</label
                            >
                            <textarea
                                id="message-input"
                                ref="messageInputRef"
                                v-model="messageForm.content"
                                @keydown.enter.exact.prevent="sendMessage"
                                placeholder="Écrivez votre message... (Entrée pour envoyer)"
                                rows="1"
                                class="w-full resize-none rounded-2xl border-2 border-blue-200 bg-blue-50 px-5 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:focus:border-blue-500 dark:focus:ring-blue-900/50"
                                :disabled="isLoadingResponse"
                                :aria-label="
                                    isLoadingResponse
                                        ? 'En attente de réponse'
                                        : 'Écrire un message'
                                "
                                style="min-height: 3rem; max-height: 12rem"
                                @input="
                                    (e) => {
                                        e.target.style.height = 'auto';
                                        e.target.style.height =
                                            Math.min(
                                                e.target.scrollHeight,
                                                192,
                                            ) + 'px';
                                    }
                                "
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            :disabled="
                                !messageForm.content.trim() || isLoadingResponse
                            "
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-r from-blue-600 to-blue-700 text-white shadow-lg transition hover:scale-105 hover:from-blue-700 hover:to-blue-800 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100"
                            :aria-label="
                                isLoadingResponse
                                    ? 'Envoi en cours'
                                    : 'Envoyer le message'
                            "
                        >
                            <Send class="h-5 w-5" aria-hidden="true" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
