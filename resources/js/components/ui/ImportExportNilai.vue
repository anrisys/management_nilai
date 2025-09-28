<!-- resources/js/components/ImportExportNilai.vue -->
<script setup lang="ts">
import nilai from '@/routes/nilai';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const showImportModal = ref(false);
const selectedFile = ref<File | null>(null);
const fileInput = ref<HTMLInputElement>();

const exportExcel = () => {
    window.location.href = nilai.export().url;
};

const downloadTemplate = () => {
    window.location.href = nilai.template().url;
};

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        selectedFile.value = target.files[0];
    }
};

const importExcel = async () => {
    if (!selectedFile.value) return;

    const formData = new FormData();
    formData.append('file', selectedFile.value);

    try {
        await router.post(nilai.import(), formData, {
            forceFormData: true,
            onSuccess: () => {
                showImportModal.value = false;
                selectedFile.value = null;
                if (fileInput.value) fileInput.value.value = '';
            }
        });
    } catch (error) {
        console.error('Import error:', error);
    }
};
</script>

<template>
    <div class="flex gap-4 mb-6">
        <!-- Export Button -->
        <button 
            @click="exportExcel"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
        </button>

        <!-- Import Button -->
        <button 
            @click="showImportModal = true"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
            </svg>
            Import Excel
        </button>

        <!-- Template Button -->
        <button 
            @click="downloadTemplate"
            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M4 19h16"/>
            </svg>
            Download Template
        </button>
    </div>

    <!-- Import Modal -->
    <div v-if="showImportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4">Import Data Excel</h3>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">
                    Format file: .xlsx atau .xls<br>
                    Struktur kolom: No, nama, kelas, mapel, nilai
                </p>
            </div>

            <form @submit.prevent="importExcel">
                <div class="mb-4">
                    <input 
                        type="file" 
                        ref="fileInput"
                        @change="handleFileSelect"
                        accept=".xlsx,.xls"
                        class="w-full p-2 border border-gray-300 rounded"
                        required
                    >
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button 
                        type="button" 
                        @click="showImportModal = false"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="!selectedFile"
                        class="px-4 py-2 bg-blue-500 text-white rounded disabled:bg-gray-300"
                    >
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>