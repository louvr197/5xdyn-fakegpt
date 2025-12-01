<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { post } from '@/routes/ask';

const props = defineProps({
    models: Array,
    selectedModel: String,
    message: String,
    response: String,
    error: String,
});

const form = useForm({
    message: props.message ?? '',
    model: props.selectedModel,
});

const submit = () => {
    form.post(post().url);
};

import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css'; // ou un autre thème
import MarkdownIt from 'markdown-it';

const md = new MarkdownIt({
    highlight: function (str, lang) {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return hljs.highlight(str, { language: lang }).value;
            } catch (__) {}
        }
        return ''; // use external default escaping
    },
});

// Utilisation : md.render(props.response)
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-3xl p-4">
            <h1 class="mb-4 text-2xl font-bold">Ask a Question</h1>

            <form @submit.prevent="submit" class="space-y-4 mb-6">
                <div>
                    <label
                        for="model"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >Select Model</label
                    >
                    <select
                        v-model="form.model"
                        id="model"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                    >
                        <option
                            v-for="model in models"
                            :key="model.id"
                            :value="model.id"
                        >
                            {{ model.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label
                        for="message"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >Your Question</label
                    >
                    <textarea
                        v-model="form.message"
                        id="message"
                        rows="4"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                        placeholder="Posez votre question..."
                    ></textarea>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ form.processing ? 'Envoi en cours...' : 'Submit' }}
                    </button>
                </div>
            </form>

            <!-- Affichage de l'erreur -->
            <div v-if="props.error" class="rounded-md bg-red-50 dark:bg-red-900/20 p-4 text-red-600 dark:text-red-400 mb-6">
                <strong>Erreur :</strong> {{ props.error }}
            </div>

            <!-- Affichage de la réponse en Markdown -->
            <div v-if="props.response" class="mt-6">
                <h2 class="text-xl font-semibold mb-3">Réponse :</h2>
                <div
                    class="prose dark:prose-invert prose-slate max-w-none"
                    v-html="md.render(props.response)"
                />
            </div>
        </div>
    </AppLayout>
</template>
