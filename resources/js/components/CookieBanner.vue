<script setup lang="ts">
import { onMounted, ref } from 'vue';

const STORAGE_KEY = 'cookie-consent-v1';

const visible = ref(false);
const settingsOpen = ref(false);
const showManage = ref(false);
const preferences = ref({
    analytics: false,
    marketing: false,
});

const loadConsent = () => {
    const raw = window.localStorage.getItem(STORAGE_KEY);
    if (!raw) {
        visible.value = true;
        return;
    }

    try {
        const data = JSON.parse(raw);
        preferences.value.analytics = Boolean(data.preferences?.analytics);
        preferences.value.marketing = Boolean(data.preferences?.marketing);
        showManage.value = true;
    } catch {
        visible.value = true;
    }
};

const saveConsent = (status: 'accepted' | 'rejected' | 'custom') => {
    window.localStorage.setItem(
        STORAGE_KEY,
        JSON.stringify({
            status,
            preferences: preferences.value,
            updatedAt: new Date().toISOString(),
        }),
    );
    visible.value = false;
    settingsOpen.value = false;
    showManage.value = true;
};

const acceptAll = () => {
    preferences.value.analytics = true;
    preferences.value.marketing = true;
    saveConsent('accepted');
};

const rejectAll = () => {
    preferences.value.analytics = false;
    preferences.value.marketing = false;
    saveConsent('rejected');
};

const openSettings = () => {
    settingsOpen.value = true;
    visible.value = true;
};

onMounted(() => {
    loadConsent();
});
</script>

<template>
    <div v-if="showManage && !visible" class="fixed bottom-4 left-4 z-50">
        <button
            type="button"
            class="rounded-full border border-blue-200/60 bg-white/90 px-4 py-2 text-xs font-semibold text-blue-700 shadow-lg transition hover:border-blue-400 hover:shadow-xl dark:bg-gray-900/90 dark:border-blue-700/60 dark:text-blue-300"
            @click="openSettings"
            aria-label="Gerer les cookies"
        >
            Cookies
        </button>
    </div>

    <div v-if="visible" class="fixed bottom-4 left-4 right-4 z-50">
        <div class="mx-auto max-w-4xl rounded-2xl border border-blue-200/60 bg-white/95 p-5 shadow-2xl backdrop-blur-xl dark:border-blue-800/40 dark:bg-gray-900/95">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    <p class="font-semibold text-gray-900 dark:text-white">Respect de votre vie privee</p>
                    <p class="mt-1">
                        Nous utilisons des cookies pour personnaliser votre experience et mesurer la performance.
                    </p>
                    <a href="/privacy" class="mt-2 inline-block text-xs text-blue-600 hover:underline">Voir la politique</a>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        @click="rejectAll"
                        aria-label="Refuser les cookies"
                    >
                        Refuser
                    </button>
                    <button
                        type="button"
                        class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
                        @click="() => (settingsOpen = true)"
                        aria-label="Personnaliser les cookies"
                    >
                        Personnaliser
                    </button>
                    <button
                        type="button"
                        class="rounded-xl bg-linear-to-r from-blue-600 to-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-lg transition hover:shadow-xl"
                        @click="acceptAll"
                        aria-label="Accepter les cookies"
                    >
                        Accepter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="settingsOpen" class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/40" @click="settingsOpen = false"></div>
        <div class="relative mx-auto mt-24 w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Preferences de cookies</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Choisissez les cookies optionnels. Les cookies essentiels sont toujours actifs.
            </p>

            <div class="mt-4 space-y-3">
                <label class="flex items-center justify-between rounded-xl border border-gray-200 p-3 text-sm dark:border-gray-700">
                    <span>Analytics</span>
                    <input
                        type="checkbox"
                        v-model="preferences.analytics"
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                </label>
                <label class="flex items-center justify-between rounded-xl border border-gray-200 p-3 text-sm dark:border-gray-700">
                    <span>Marketing</span>
                    <input
                        type="checkbox"
                        v-model="preferences.marketing"
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                </label>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                <button
                    type="button"
                    class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    @click="rejectAll"
                >
                    Tout refuser
                </button>
                <button
                    type="button"
                    class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
                    @click="saveConsent('custom')"
                >
                    Enregistrer
                </button>
                <button
                    type="button"
                    class="rounded-xl bg-linear-to-r from-blue-600 to-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-lg transition hover:shadow-xl"
                    @click="acceptAll"
                >
                    Tout accepter
                </button>
            </div>
        </div>
    </div>
</template>
