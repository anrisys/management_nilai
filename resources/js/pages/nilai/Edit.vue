<script setup lang="ts">
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import nilaiRoutes from '@/routes/nilai';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';

interface Siswa {
    id: number;
    nama: string;
    kelas: string;
}

interface NilaiData {
    id: number;
    siswa_id: number;
    kelas: string;
    mapel: string;
    nilai: number;
    siswa: Siswa;
}

interface Props {
    nilai: NilaiData;
    mapelList: string[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Data Nilai',
        href: nilaiRoutes.index().url,
    },
    {
        title: 'Edit Nilai',
        href: nilaiRoutes.edit({ nilai: props.nilai.id }).url,
    },
];

// Pre-fill form dengan data yang ada
const form = useForm({
    nilai: props.nilai.nilai.toString(), // Convert to string for input
});

const handleSubmit = () => {
    // Convert nilai to number
    const numericNilai = parseInt(form.nilai);

    // Validasi client-side sebelum kirim
    if (isNaN(numericNilai) || numericNilai < 0 || numericNilai > 100) {
        form.setError('nilai', 'Nilai harus antara 0-100');
        return;
    }

    form.put(nilaiRoutes.update({ nilai: props.nilai.id }).url, {
        onSuccess: () => {
            router.visit(dashboard());
        },
        onError: (errors) => {
            console.error('Error updating nilai:', errors);
            // Reset ke string untuk input
            form.nilai = numericNilai.toString();
        },
    });
};

// Auto-clear error ketika user mulai mengetik
const clearError = () => {
    if (form.errors.nilai) {
        form.clearErrors('nilai');
    }
};
</script>

<template>
    <Head :title="`Edit Nilai - ${props.nilai.siswa.nama}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <!-- Display Current Data (Read-only) -->
            <div
                class="w-full max-w-2xl space-y-4 rounded-lg border bg-gray-50 p-6"
            >
                <h3 class="text-lg font-semibold text-gray-900">
                    Data Saat Ini
                </h3>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Siswa:</span>
                        <p class="text-gray-900">
                            {{ props.nilai.siswa.nama }}
                        </p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Kelas:</span>
                        <p class="text-gray-900">{{ props.nilai.kelas }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700"
                            >Mata Pelajaran:</span
                        >
                        <p class="text-gray-900">{{ props.nilai.mapel }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700"
                            >Nilai Sebelumnya:</span
                        >
                        <p class="text-gray-900">{{ props.nilai.nilai }}</p>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <form
                @submit.prevent="handleSubmit"
                class="w-full max-w-2xl space-y-6"
            >
                <div class="space-y-2">
                    <Label
                        for="nilai"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Nilai Baru (0-100) *
                    </Label>
                    <div class="relative">
                        <Input
                            id="nilai"
                            v-model="form.nilai"
                            @input="clearError"
                            type="number"
                            min="0"
                            max="100"
                            placeholder="Masukkan nilai 0-100"
                            :class="[
                                'pr-16',
                                form.errors.nilai
                                    ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                                    : '',
                            ]"
                            required
                        />
                        <span
                            class="absolute top-1/2 right-3 -translate-y-1/2 transform text-gray-500"
                        >
                            /100
                        </span>
                    </div>

                    <!-- Error Messages -->
                    <div
                        v-if="form.errors.nilai"
                        class="rounded-md border border-red-200 bg-red-50 p-2 text-sm text-red-600"
                    >
                        <div class="flex items-center gap-2">
                            <svg
                                class="h-4 w-4 flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <span>{{ form.errors.nilai }}</span>
                        </div>
                    </div>

                    <!-- Nilai Preview -->
                    <div v-if="form.nilai && !form.errors.nilai" class="mt-2">
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600">Nilai Huruf:</span>
                                <span
                                    :class="[
                                        'rounded px-2 py-1 text-xs font-medium',
                                        Number(form.nilai) >= 85
                                            ? 'bg-green-100 text-green-800'
                                            : Number(form.nilai) >= 75
                                              ? 'bg-blue-100 text-blue-800'
                                              : Number(form.nilai) >= 65
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : Number(form.nilai) >= 55
                                                  ? 'bg-orange-100 text-orange-800'
                                                  : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{
                                        Number(form.nilai) >= 85
                                            ? 'A'
                                            : Number(form.nilai) >= 75
                                              ? 'B'
                                              : Number(form.nilai) >= 65
                                                ? 'C'
                                                : Number(form.nilai) >= 55
                                                  ? 'D'
                                                  : 'E'
                                    }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600">Status:</span>
                                <span
                                    :class="[
                                        'rounded px-2 py-1 text-xs font-medium',
                                        Number(form.nilai) >= 65
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{
                                        Number(form.nilai) >= 65
                                            ? 'Lulus'
                                            : 'Tidak Lulus'
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-6">
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-500 px-6 py-2 text-white hover:bg-blue-600 disabled:bg-gray-400"
                    >
                        <span v-if="form.processing">Memperbarui...</span>
                        <span v-else>Perbarui Nilai</span>
                    </Button>

                    <Button
                        type="button"
                        @click="router.visit(dashboard())"
                        class="rounded-lg bg-gray-500 px-6 py-2 text-white hover:bg-gray-600"
                    >
                        Batal
                    </Button>
                </div>
            </form>

            <!-- Debug Info (Optional) -->
            <div
                v-if="form.hasErrors"
                class="w-full max-w-2xl rounded-lg border border-yellow-200 bg-yellow-50 p-4"
            >
                <h4 class="font-medium text-yellow-800">Debug Information:</h4>
                <pre class="mt-2 text-xs text-yellow-700">{{
                    JSON.stringify(form.errors, null, 2)
                }}</pre>
            </div>
        </div>
    </AppLayout>
</template>
