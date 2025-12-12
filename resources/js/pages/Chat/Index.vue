<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { store as storeConversation } from '@/routes/conversations';
import MarkdownIt from 'markdown-it';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css';

const props = defineProps({
    conversations: Array,
    currentConversation: Object,
    models: Array,
    selectedModel: String,
    error: String,
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
    }
});

// Form pour nouveau message
const messageForm = useForm({
    content: '',
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

// Fermer le dropdown quand on clique en dehors
const handleClickOutside = (event: MouseEvent) => {
    if (modelSelectorRef.value && !modelSelectorRef.value.contains(event.target as Node)) {
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

    router.patch(`/conversations/${props.currentConversation.id}`, {
        model: modelId,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showModelSelector.value = false;
        },
    });
};

// Obtenir le nom du modèle actuel
const currentModelName = computed(() => {
    if (!props.currentConversation) return props.selectedModel;
    const model = props.models?.find(m => m.id === props.currentConversation.model);
    return model?.name || props.currentConversation.model;
});

// Créer une nouvelle conversation
const createConversation = () => {
    newConversationForm.post(storeConversation().url, {
        preserveState: true,
    });
};

// Envoyer un message
const sendMessage = () => {
    if (!props.currentConversation || !messageForm.content.trim()) return;

    isLoadingResponse.value = true;

    messageForm.post(`/conversations/${props.currentConversation.id}/messages`, {
        preserveScroll: true,
        onSuccess: () => {
            messageForm.reset();
            isLoadingResponse.value = false;
            // Focus automatique sur le champ de texte
            setTimeout(() => {
                messageInputRef.value?.focus();
            }, 100);
        },
        onError: () => {
            isLoadingResponse.value = false;
        },
    });
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
            <div class="w-64 border-r border-gray-200 dark:border-gray-700 overflow-y-auto bg-white dark:bg-gray-800">
                <div class="p-4">
                    <button
                        @click="createConversation"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
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
                            'p-3 rounded-lg cursor-pointer mb-2 group relative',
                            currentConversation?.id === conversation.id
                                ? 'bg-blue-50 dark:bg-blue-900/20'
                                : 'hover:bg-gray-100 dark:hover:bg-gray-700'
                        ]"
                    >
                        <div class="flex justify-between items-start">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ conversation.title || 'Nouvelle conversation' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ new Date(conversation.updated_at).toLocaleDateString('fr-FR') }}
                                </p>
                            </div>
                            <button
                                @click.stop="deleteConversation(conversation.id)"
                                class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-600 transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zone principale -->
            <div class="flex-1 flex flex-col">
                <!-- Header avec sélecteur de modèle -->
                <div v-if="currentConversation" class="border-b border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ currentConversation.title || 'Nouvelle conversation' }}
                        </h2>
                        <div class="relative" ref="modelSelectorRef">
                            <button
                                @click="showModelSelector = !showModelSelector"
                                class="flex items-center space-x-2 px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ currentModelName }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown des modèles -->
                            <div
                                v-if="showModelSelector"
                                class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 max-h-96 overflow-y-auto z-10"
                            >
                                <div class="p-2">
                                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                        Sélectionner un modèle
                                    </div>
                                    <button
                                        v-for="model in models"
                                        :key="model.id"
                                        @click="changeModel(model.id)"
                                        :class="[
                                            'w-full text-left px-3 py-2 rounded-lg transition',
                                            currentConversation.model === model.id
                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400'
                                                : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'
                                        ]"
                                    >
                                        <div class="font-medium text-sm">{{ model.name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ model.description }}</div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zone des messages -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    <div v-if="!currentConversation" class="h-full flex items-center justify-center text-gray-500">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p class="text-lg">Sélectionnez une conversation ou créez-en une nouvelle</p>
                        </div>
                    </div>

                    <div v-else>
                        <!-- Messages -->
                        <div
                            v-for="message in currentConversation.messages"
                            :key="message.id"
                            :class="[
                                'flex',
                                message.role === 'user' ? 'justify-end' : 'justify-start'
                            ]"
                        >
                            <div
                                :class="[
                                    'max-w-3xl rounded-lg p-4 mb-2',
                                    message.role === 'user'
                                        ? 'bg-blue-600 text-white ml-3'
                                        : 'bg-gray-100 dark:bg-gray-700 mr-4'
                                ]"
                            >
                                <div v-if="message.role === 'user'" class="whitespace-pre-wrap">
                                    {{ message.content }}
                                </div>
                                <div
                                    v-else
                                    class="prose dark:prose-invert prose-slate max-w-none"
                                    v-html="md.render(message.content)"
                                />
                            </div>
                        </div>

                        <!-- Loader pendant la réponse -->
                        <div v-if="isLoadingResponse" class="flex justify-start">
                            <div class="max-w-3xl rounded-lg p-4 bg-gray-100 dark:bg-gray-700 m-2">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zone de saisie -->
                <div v-if="currentConversation" class="border-t border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
                    <form @submit.prevent="sendMessage" class="flex items-end space-x-2">
                        <textarea
                            ref="messageInputRef"
                            v-model="messageForm.content"
                            @keydown.enter.exact.prevent="sendMessage"
                            placeholder="Tapez votre message... (Entrée pour envoyer)"
                            rows="3"
                            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                            :disabled="isLoadingResponse"
                        ></textarea>
                        <button
                            type="submit"
                            :disabled="!messageForm.content.trim() || isLoadingResponse"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            Envoyer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
