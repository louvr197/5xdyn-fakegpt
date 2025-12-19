<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { store as storeConversation } from '@/routes/conversations';
import { router, useForm } from '@inertiajs/vue3';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css';
import MarkdownIt from 'markdown-it';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    conversations: Array,
    currentConversation: Object,
    models: Array,
    selectedModel: String,
    error: String,
    failedModels: Array,
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
});

// Form pour changer le modèle
const modelForm = useForm({
    model: props.selectedModel,
});

const isLoadingResponse = ref(false);
const showModelSelector = ref(false);
const modelSelectorRef = ref<HTMLElement | null>(null);
const messageInputRef = ref<HTMLTextAreaElement | null>(null);
const showOnlyFree = ref(false);
const showOnlyWithImages = ref(false);
const hideFailedModels = ref(false);
const selectedImage = ref<File | null>(null);
const imagePreviewUrl = ref<string | null>(null);

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
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

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
    const model = props.models.find((m) => m.id === props.currentConversation.model);
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
    });
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

// Envoyer un message
const sendMessage = () => {
    if (!props.currentConversation || !messageForm.content.trim()) return;

    isLoadingResponse.value = true;

    messageForm.post(
        `/conversations/${props.currentConversation.id}/messages`,
        {
            preserveScroll: true,
            onSuccess: () => {
                messageForm.reset();
                removeImage(); // ← AJOUTER
                isLoadingResponse.value = false;
                setTimeout(() => {
                    messageInputRef.value?.focus();
                }, 100);
            },
            onError: () => {
                isLoadingResponse.value = false;
            },
        },
    );
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
        <div class="flex h-[calc(100vh-4rem)]">
            <!-- Sidebar - Liste des conversations -->
            <div
                class="w-64 overflow-y-auto border-r border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="p-4">
                    <button
                        @click="createConversation"
                        class="w-full rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
                    >
                        + Nouvelle conversation
                    </button>
                </div>

                <div class="px-2">
                    <div
                        v-for="conversation in conversations"
                        :key="conversation.id"
                        @click="selectConversation(conversation.id)"
                        :class="[
                            'group relative mb-2 cursor-pointer rounded-lg p-3',
                            currentConversation?.id === conversation.id
                                ? 'bg-blue-50 dark:bg-blue-900/20'
                                : 'hover:bg-gray-100 dark:hover:bg-gray-700',
                        ]"
                    >
                        <div class="flex items-start justify-between">
                            <div class="min-w-0 flex-1">
                                <p
                                    class="truncate text-sm font-medium text-gray-900 dark:text-gray-100"
                                >
                                    {{
                                        conversation.title ||
                                        'Nouvelle conversation'
                                    }}
                                </p>
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
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zone principale -->
            <div class="flex flex-1 flex-col">
                <!-- Header avec sélecteur de modèle -->
                <div
                    v-if="currentConversation"
                    class="border-b border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-center justify-between">
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            {{
                                currentConversation.title ||
                                'Nouvelle conversation'
                            }}
                        </h2>
                        <div class="relative" ref="modelSelectorRef">
                            <button
                                @click="showModelSelector = !showModelSelector"
                                class="flex items-center space-x-2 rounded-lg bg-gray-100 px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
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
                                class="absolute right-0 z-10 mt-2 max-h-96 w-80 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
                            >
                                <div
                                    class="border-b border-gray-200 p-3 dark:border-gray-700"
                                >
                                    <div
                                        class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400"
                                    >
                                        Sélectionner un modèle
                                    </div>

                                    <!-- Filtres -->
                                    <div class="mt-2 flex flex-col space-y-2">
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
                                            v-if="failedModels && failedModels.length > 0"
                                            class="flex cursor-pointer items-center space-x-2"
                                        >
                                            <input
                                                type="checkbox"
                                                v-model="hideFailedModels"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            <span
                                                class="text-sm text-gray-700 dark:text-gray-300"
                                                >Masquer modèles en erreur ({{ failedModels.length }})</span
                                            >
                                        </label>
                                    </div>

                                    <div class="mt-2 text-xs text-gray-500">
                                        {{ filteredModels.length }} modèle(s)
                                    </div>
                                </div>

                                <div class="p-2">
                                    <button
                                        v-for="model in filteredModels"
                                        :key="model.id"
                                        @click="changeModel(model.id)"
                                        :class="[
                                            'w-full rounded-lg px-3 py-2 text-left transition',
                                            currentConversation.model ===
                                            model.id
                                                ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400'
                                                : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700',
                                        ]"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div class="text-sm font-medium">
                                                {{ model.name }}
                                            </div>
                                            <div
                                                class="flex items-center space-x-1"
                                            >
                                                <span
                                                    v-if="model.is_free"
                                                    class="rounded bg-green-100 px-2 py-0.5 text-xs text-green-700"
                                                >
                                                    GRATUIT
                                                </span>
                                                <span
                                                    v-if="model.supports_image"
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
                                        Aucun modèle ne correspond aux filtres
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zone des messages -->
                <div class="flex-1 space-y-4 overflow-y-auto p-6">
                    <div
                        v-if="!currentConversation"
                        class="flex h-full items-center justify-center text-gray-500"
                    >
                        <div class="text-center">
                            <svg
                                class="mx-auto mb-4 h-16 w-16 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                />
                            </svg>
                            <p class="text-lg">
                                Sélectionnez une conversation ou créez-en une
                                nouvelle
                            </p>
                        </div>
                    </div>

                    <div v-else>
                        <!-- Messages -->
                        <div
                            v-for="message in currentConversation.messages"
                            :key="message.id"
                            :class="[
                                'flex',
                                message.role === 'system' ? 'justify-center' : message.role === 'user'
                                    ? 'justify-end'
                                    : 'justify-start',
                            ]"
                        >
                            <!-- Message système -->
                            <div
                                v-if="message.role === 'system'"
                                class="my-4 max-w-2xl rounded-lg border border-gray-300 bg-gray-50 px-4 py-2 text-center text-sm text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400"
                            >
                                <div
                                    class="prose prose-sm max-w-none prose-slate dark:prose-invert"
                                    v-html="md.render(message.content)"
                                />
                            </div>

                            <!-- Messages utilisateur et assistant -->
                            <div
                                v-else
                                :class="[
                                    'mb-2 max-w-3xl rounded-lg p-4',
                                    message.role === 'user'
                                        ? 'ml-3 bg-blue-600 text-white'
                                        : 'mr-4 bg-gray-100 dark:bg-gray-700',
                                ]"
                            >
                                <!-- Message utilisateur -->
                                <div v-if="message.role === 'user'">
                                    <template v-if="typeof message.content === 'string' && message.content.startsWith('[')">
                                        <!-- Message multimodal (texte + image) -->
                                        <template v-for="(part, index) in JSON.parse(message.content)" :key="index">
                                            <p v-if="part.type === 'text'" class="whitespace-pre-wrap mb-2">
                                                {{ part.text }}
                                            </p>
                                            <img
                                                v-if="part.type === 'image_url'"
                                                :src="part.image_url.url"
                                                alt="Image envoyée"
                                                class="max-w-xs rounded-lg mt-2"
                                            />
                                        </template>
                                    </template>
                                    <p v-else class="whitespace-pre-wrap">
                                        {{ message.content }}
                                    </p>
                                </div>

                                <!-- Message assistant -->
                                <div
                                    v-else
                                    class="prose max-w-none prose-slate dark:prose-invert"
                                    v-html="md.render(message.content)"
                                />
                            </div>
                        </div>

                        <!-- Loader pendant la réponse -->
                        <div
                            v-if="isLoadingResponse"
                            class="flex justify-start"
                        >
                            <div
                                class="m-2 max-w-3xl rounded-lg bg-gray-100 p-4 dark:bg-gray-700"
                            >
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="h-2 w-2 animate-bounce rounded-full bg-gray-400"
                                        style="animation-delay: 0s"
                                    ></div>
                                    <div
                                        class="h-2 w-2 animate-bounce rounded-full bg-gray-400"
                                        style="animation-delay: 0.2s"
                                    ></div>
                                    <div
                                        class="h-2 w-2 animate-bounce rounded-full bg-gray-400"
                                        style="animation-delay: 0.4s"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Zone de saisie -->
                <div
                    v-if="currentConversation"
                    class="border-t border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                >
                    <!-- Message d'erreur -->
                    <div
                        v-if="error"
                        class="mb-3 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400"
                    >
                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ error }}</span>
                        </div>
                    </div>

                    <!-- Preview de l'image -->
                    <div
                        v-if="imagePreviewUrl"
                        class="relative mb-3 inline-block"
                    >
                        <img
                            :src="imagePreviewUrl"
                            alt="Preview"
                            class="max-h-32 rounded-lg border border-gray-300"
                        />
                        <button
                            @click="removeImage"
                            type="button"
                            class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600"
                        >
                            ×
                        </button>
                    </div>

                    <form
                        @submit.prevent="sendMessage"
                        class="flex items-end space-x-2"
                    >
                        <!-- Bouton d'upload d'image -->
                        <label
                            v-if="currentModelSupportsImages"
                            class="cursor-pointer rounded-lg bg-gray-100 px-3 py-2 transition hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600"
                        >
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                @change="handleImageSelect"
                                class="hidden"
                            />
                            <svg
                                class="h-5 w-5 text-gray-600 dark:text-gray-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                                />
                            </svg>
                        </label>

                        <textarea
                            ref="messageInputRef"
                            v-model="messageForm.content"
                            @keydown.enter.exact.prevent="sendMessage"
                            placeholder="Tapez votre message... (Entrée pour envoyer)"
                            rows="3"
                            class="flex-1 resize-none rounded-lg border border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700"
                            :disabled="isLoadingResponse"
                        ></textarea>
                        <button
                            type="submit"
                            :disabled="
                                !messageForm.content.trim() || isLoadingResponse
                            "
                            class="rounded-lg bg-blue-600 px-6 py-2 text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Envoyer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
