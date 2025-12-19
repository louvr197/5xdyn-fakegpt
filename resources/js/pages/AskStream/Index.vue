<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useStream } from '@laravel/stream-vue';
import { ref } from 'vue';

// Props reçues du backend
const props = defineProps({
    models: Array,
    selectedModel: String,
});

//les refs et le hook useStream
const message = ref('');
const model = ref(props.selectedModel);
const temperature = ref(1.0);
const reasoningEffort = ref<'low' | 'medium' | 'high'| null>(null);

const { data, isStreaming,isFetching,send,cancel}= useStream(

        '/ask-stream',{
            onData:()=>{},
            onFinish:()=>{
                message.value='';
            },
            onError: (err: Error) => {
                console.error('Erreur streaming:', err);
            },

        }

);
// Fonction pour soumettre le formulaire
const submit = () => {
    if (!message.value.trim()) return; // Si le message est vide, ne rien faire

    send({
        message: message.value,
        model: model.value,
        temperature: temperature.value,
        reasoning_effort: reasoningEffort.value,
    });
};
</script>
<template>
    <AppLayout>
        <div class="container mx-auto p-4">
            <h1 class="text-2xl mb-4">Test Streaming</h1>

            <!-- Formulaire -->
            <form @submit.prevent="submit" class="mb-4">
                <textarea v-model="message" placeholder="Votre message..."></textarea>

                <select v-model="model">
                    <!-- Boucle sur props.models -->
                </select>

                <input type="range" v-model="temperature" min="0" max="2" step="0.1" />

                <button type="submit" :disabled="isStreaming">Envoyer</button>
                <button v-if="isStreaming" @click="cancel()">Annuler</button>
            </form>

            <!-- Affichage -->
            <div v-if="data" class="border p-4">
                <pre>{{ data }}</pre>
            </div>

            <div v-if="isStreaming">Streaming en cours...</div>
        </div>
    </AppLayout>
</template>
