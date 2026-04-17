<template>
  <div class="rounded-xl p-4 flex items-center gap-3" :class="[ isSuccess ? 'bg-emerald-50 border border-emerald-100' : 'bg-error-container/40 border border-error/10' ]">
    <span class="material-symbols-outlined" :class="isSuccess ? 'text-emerald-600' : 'text-error'" style="font-variation-settings: 'FILL' 1;">
      {{ isSuccess ? 'check_circle' : 'error' }}
    </span>
    <p class="text-sm font-medium" :class="isSuccess ? 'text-emerald-800' : 'text-error'">
      {{ message }}
    </p>
  </div>
</template>

<script setup>
const props = defineProps({
    isSuccess: Boolean,
    message: String
});
</script>
