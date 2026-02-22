<script setup lang="ts">
import { Briefcase } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <Briefcase
        :class="className"
        v-bind="$attrs"
    />
</template>
