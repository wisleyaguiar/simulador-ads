<template>
  <div class="relative group cursor-pointer" @drop.prevent="onDrop" @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @click="triggerFileInput">
    <div class="border-2 border-dashed rounded-xl p-12 flex flex-col items-center justify-center transition-all"
         :class="[ dragover ? 'bg-primary/10 border-primary' : 'bg-surface-container-low border-outline-variant/50 group-hover:bg-primary/5 group-hover:border-primary' ]">
      <span class="material-symbols-outlined text-5xl mb-4 transition-transform text-primary/40 group-hover:scale-110" data-icon="cloud_upload">cloud_upload</span>
      <p class="text-on-surface font-medium text-center" v-if="!file">Arraste e solte sua planilha aqui</p>
      <p class="text-xs text-on-surface-variant mt-2" v-if="!file">ou clique para selecionar o arquivo</p>
      
      <p class="text-primary font-bold text-center" v-if="file">{{ file.name }}</p>
    </div>
    <input type="file" ref="fileInput" class="hidden" accept=".csv" @change="onFileChange" />
  </div>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['file-selected']);

const dragover = ref(false);
const file = ref(null);
const fileInput = ref(null);

const triggerFileInput = () => {
    fileInput.value.click();
};

const onFileChange = (e) => {
    const selected = e.target.files[0];
    if (selected) {
        file.value = selected;
        emit('file-selected', selected);
    }
};

const onDrop = (e) => {
    dragover.value = false;
    const droppedFile = e.dataTransfer.files[0];
    if (droppedFile) {
        file.value = droppedFile;
        emit('file-selected', droppedFile);
    }
};
</script>
