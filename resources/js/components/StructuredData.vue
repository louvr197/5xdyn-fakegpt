<script setup lang="ts">
import { onMounted } from 'vue';

interface Props {
    data: Record<string, any>;
}

const props = defineProps<Props>();

onMounted(() => {
    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.textContent = JSON.stringify(props.data);
    document.head.appendChild(script);
});
</script>

<template>
    <!-- This component injects JSON-LD structured data -->
</template>
