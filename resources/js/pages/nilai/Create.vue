<script setup lang="ts">
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import nilai from '@/routes/nilai';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface SiswaOption {
    id: number;
    nama: string;
    kelas: string;
}

interface Props {
    siswas: SiswaOption[];
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
        href: nilai.index().url,
    },
    {
        title: 'Tambah Nilai',
        href: nilai.create().url,
    },
];

const form = useForm({
    siswa_id: null as number | null,
    kelas: '',
    mapel: '',
    nilai: '',
});

// Gunakan ref terpisah untuk nilai preview
const nilaiPreview = ref<number | null>(null);

// Auto-fill kelas ketika siswa dipilih
watch(
    () => form.siswa_id,
    (newSiswaId) => {
        if (newSiswaId) {
            const selectedSiswa = props.siswas.find((s) => s.id == newSiswaId);
            if (selectedSiswa) {
                form.kelas = selectedSiswa.kelas;
            }
        } else {
            form.kelas = '';
        }
    },
);

// Watch nilai input untuk preview
watch(
    () => form.nilai,
    (newNilai) => {
        if (newNilai === '') {
            nilaiPreview.value = null;
        } else {
            const numValue = Number(newNilai);
            nilaiPreview.value = isNaN(numValue) ? null : numValue;
        }

        // Clear error ketika user mulai mengetik
        if (form.errors.nilai) {
            form.clearErrors('nilai');
        }
    },
);

const handleSubmit = () => {
    form.post(nilai.store().url);
};
</script>

<template>
    <Head title="Tambah Data Nilai" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <form
                @submit.prevent="handleSubmit"
                class="w-8/12 max-w-2xl space-y-6"
            >
                <!-- Siswa Selection -->
                <div class="space-y-2">
                    <Label
                        for="siswa_id"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Pilih Siswa *
                    </Label>
                    <select
                        id="siswa_id"
                        v-model="form.siswa_id"
                        :class="[
                            'w-full rounded-lg border border-gray-300 p-3 focus:border-transparent focus:ring-2 focus:ring-blue-500',
                            form.errors.siswa_id ? 'border-red-500' : '',
                        ]"
                        required
                    >
                        <option :value="null">-- Pilih Siswa --</option>
                        <option
                            v-for="siswaOption in props.siswas"
                            :key="siswaOption.id"
                            :value="siswaOption.id"
                        >
                            {{ siswaOption.nama }} - {{ siswaOption.kelas }}
                        </option>
                    </select>
                    <div
                        class="text-sm text-red-600"
                        v-if="form.errors.siswa_id"
                    >
                        {{ form.errors.siswa_id }}
                    </div>
                </div>

                <!-- Kelas (Auto-filled) -->
                <div class="space-y-2">
                    <Label
                        for="kelas"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Kelas *
                    </Label>
                    <Input
                        id="kelas"
                        v-model="form.kelas"
                        type="text"
                        placeholder="Kelas akan terisi otomatis"
                        :class="{ 'border-red-500': form.errors.kelas }"
                        readonly
                        required
                    />
                    <div class="text-sm text-red-600" v-if="form.errors.kelas">
                        {{ form.errors.kelas }}
                    </div>
                </div>

                <!-- Mata Pelajaran -->
                <div class="space-y-2">
                    <Label
                        for="mapel"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Mata Pelajaran *
                    </Label>
                    <select
                        id="mapel"
                        v-model="form.mapel"
                        :class="[
                            'w-full rounded-lg border border-gray-300 p-3 focus:border-transparent focus:ring-2 focus:ring-blue-500',
                            form.errors.mapel ? 'border-red-500' : '',
                        ]"
                        required
                    >
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <option
                            v-for="mapel in props.mapelList"
                            :key="mapel"
                            :value="mapel"
                        >
                            {{ mapel }}
                        </option>
                    </select>
                    <div class="text-sm text-red-600" v-if="form.errors.mapel">
                        {{ form.errors.mapel }}
                    </div>
                </div>

                <!-- Nilai -->
                <div class="space-y-2">
                    <Label for="nilai" class="text-sm font-medium">
                        Nilai (0-100) *
                    </Label>
                    <div class="relative">
                        <Input
                            id="nilai"
                            v-model="form.nilai"
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
                    <div class="text-sm text-red-600" v-if="form.errors.nilai">
                        {{ form.errors.nilai }}
                    </div>

                    <!-- Nilai Preview -->
                    <div
                        v-if="nilaiPreview !== null && !form.errors.nilai"
                        class="mt-2"
                    >
                        <div class="flex items-center gap-2 text-sm">
                            <span>Nilai Huruf:</span>
                            <span
                                :class="[
                                    'rounded px-2 py-1 text-xs font-medium',
                                    nilaiPreview >= 85
                                        ? 'bg-green-100 text-green-800'
                                        : nilaiPreview >= 75
                                          ? 'bg-blue-100 text-blue-800'
                                          : nilaiPreview >= 65
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : nilaiPreview >= 55
                                              ? 'bg-orange-100 text-orange-800'
                                              : 'bg-red-100 text-red-800',
                                ]"
                            >
                                {{
                                    nilaiPreview >= 85
                                        ? 'A'
                                        : nilaiPreview >= 75
                                          ? 'B'
                                          : nilaiPreview >= 65
                                            ? 'C'
                                            : nilaiPreview >= 55
                                              ? 'D'
                                              : 'E'
                                }}
                            </span>
                            <span
                                :class="[
                                    'rounded px-2 py-1 text-xs font-medium',
                                    nilaiPreview >= 65
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800',
                                ]"
                            >
                                {{
                                    nilaiPreview >= 65 ? 'Lulus' : 'Tidak Lulus'
                                }}
                            </span>
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
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>Simpan Nilai</span>
                    </Button>

                    <Button
                        type="button"
                        @click="router.visit(nilai.index())"
                        class="rounded-lg px-6 py-2"
                    >
                        Batal
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
