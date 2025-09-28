<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import ImportExportNilai from '@/components/ui/ImportExportNilai.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import nilai from '@/routes/nilai';
import siswa from '@/routes/siswa';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { CircleCheckBig, CircleX } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Siswa {
    id: number;
    nama: string;
    kelas: string;
}

interface Nilai {
    id: number;
    siswa_id: number;
    kelas: string;
    mapel: string;
    nilai: number;
    created_at: string;
    updated_at: string;
    siswa: Siswa;
}

interface Props {
    nilais: {
        data: Nilai[];
        current_page: number;
        last_page: number;
        total: number;
        from: number;
        to: number;
    };
    filters?: {
        search?: string;
        kelas?: string;
        mapel?: string;
    };
    kelasList: string[];
    mapelList: string[];
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Data nilai',
        href: nilai.index().url,
    },
];

// Filter states
const search = ref(props.filters?.search || '');
const selectedKelas = ref(props.filters?.kelas || '');
const selectedMapel = ref(props.filters?.mapel || '');

// Perform search dengan debounce
let searchTimeout: number;
const performFilter = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            nilai.index(),
            {
                search: search.value,
                kelas: selectedKelas.value,
                mapel: selectedMapel.value,
            },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    }, 300);
};

// Watch filter changes
watch([search, selectedKelas, selectedMapel], performFilter);

// Reset filters
const resetFilters = () => {
    search.value = '';
    selectedKelas.value = '';
    selectedMapel.value = '';
};

// Handle delete
const handleDelete = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus data nilai ini?')) {
        router.delete(nilai.destroy({ nilai: id }), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        });
    }
};

// Format nilai dengan warna berdasarkan range
const getNilaiColor = (nilai: number) => {
    if (nilai >= 85) return 'text-green-600 bg-green-100';
    if (nilai >= 75) return 'text-blue-600 bg-blue-100';
    if (nilai >= 65) return 'text-yellow-600 bg-yellow-100';
    return 'text-red-600 bg-red-100';
};

// Get nilai huruf
const getNilaiHuruf = (nilai: number) => {
    if (nilai >= 85) return 'A';
    if (nilai >= 75) return 'B';
    if (nilai >= 65) return 'C';
    if (nilai >= 55) return 'D';
    return 'E';
};

// Format tanggal
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Data nilai" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <ImportExportNilai />
            <!-- Flash success -->
            <div v-if="page.props.flash?.success" class="mb-4">
                <Alert class="border-green-400 bg-green-100 text-green-800">
                    <CircleCheckBig class="h-4 w-4" />
                    <AlertTitle>Sukses!</AlertTitle>
                    <AlertDescription>
                        {{ page.props.flash.success }}
                    </AlertDescription>
                </Alert>
            </div>

            <!-- Flash error -->
            <div v-if="page.props.flash?.error" class="mb-4">
                <Alert class="border-red-400 bg-red-100 text-red-800">
                    <CircleX class="h-4 w-4" />
                    <AlertTitle>Terjadi Kesalahan!</AlertTitle>
                    <AlertDescription>
                        {{ page.props.flash.error }}
                    </AlertDescription>
                </Alert>
            </div>

            <!-- Header -->
            <div
                class="flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center"
            >
                <div>
                    <h1 class="text-2xl font-bold">Dashboard Nilai Siswa</h1>
                    <p>
                        Total: {{ props.nilais.total }} nilai • Menampilkan
                        {{ props.nilais.from }}-{{ props.nilais.to }} dari
                        {{ props.nilais.total }}
                    </p>
                </div>

                <div class="flex gap-3">
                    <Link
                        :href="siswa.index()"
                        class="flex items-center gap-2 rounded-lg bg-blue-500 px-4 py-2 text-white transition-colors hover:bg-blue-600"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                        Data Siswa
                    </Link>
                    <Link
                        :href="nilai.create()"
                        class="flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2 text-white transition-colors hover:bg-green-600"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                            />
                        </svg>
                        Tambah Nilai
                    </Link>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="rounded-lg border p-5">
                <div class="flex flex-col items-end gap-4 lg:flex-row">
                    <!-- Search Input -->
                    <div class="flex-1">
                        <label class="mb-2 block text-sm font-medium"
                            >Pencarian</label
                        >
                        <div class="relative">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari nama siswa, kelas, mapel, atau nilai..."
                                class="w-full rounded-lg border border-gray-300 py-2 pr-4 pl-10 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                            />
                            <svg
                                class="absolute top-2.5 left-3 h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                    </div>

                    <!-- Kelas Filter -->
                    <div class="w-full lg:w-48">
                        <label class="mb-2 block text-sm font-medium"
                            >Filter Kelas</label
                        >
                        <select
                            v-model="selectedKelas"
                            class="w-full rounded-lg border border-gray-300 p-2 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Semua Kelas</option>
                            <option
                                v-for="kelas in props.kelasList"
                                :key="kelas"
                                :value="kelas"
                            >
                                {{ kelas }}
                            </option>
                        </select>
                    </div>

                    <!-- Mapel Filter -->
                    <div class="w-full lg:w-48">
                        <label class="mb-2 block text-sm font-medium"
                            >Filter Mapel</label
                        >
                        <select
                            v-model="selectedMapel"
                            class="w-full rounded-lg border border-gray-300 p-2 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Semua Mapel</option>
                            <option
                                v-for="mapel in props.mapelList"
                                :key="mapel"
                                :value="mapel"
                            >
                                {{ mapel }}
                            </option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div>
                        <Button
                            @click="resetFilters"
                            class="rounded-lg px-4 py-2 transition-colors"
                        >
                            Reset
                        </Button>
                    </div>
                </div>

                <!-- Active Filters Badges -->
                <div
                    v-if="selectedKelas || selectedMapel || search"
                    class="mt-4 flex flex-wrap gap-2"
                >
                    <span
                        v-if="search"
                        class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-800"
                    >
                        Pencarian: "{{ search }}"
                        <button
                            @click="search = ''"
                            class="ml-1 hover:text-blue-600"
                        >
                            ×
                        </button>
                    </span>
                    <span
                        v-if="selectedKelas"
                        class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs text-green-800"
                    >
                        Kelas: {{ selectedKelas }}
                        <button
                            @click="selectedKelas = ''"
                            class="ml-1 hover:text-green-600"
                        >
                            ×
                        </button>
                    </span>
                    <span
                        v-if="selectedMapel"
                        class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-xs text-purple-800"
                    >
                        Mapel: {{ selectedMapel }}
                        <button
                            @click="selectedMapel = ''"
                            class="ml-1 hover:text-purple-600"
                        >
                            ×
                        </button>
                    </span>
                </div>
            </div>
            <!-- Table Nilai -->
            <div>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>No</TableHead>
                            <TableHead>Siswa</TableHead>
                            <TableHead>Kelas</TableHead>
                            <TableHead>Mata Pelajaran</TableHead>
                            <TableHead>Nilai</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Terakhir Diupdate</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <!-- Data Rows -->
                        <TableRow
                            v-for="(data, index) in props.nilais.data"
                            :key="data.id"
                        >
                            <TableCell>{{
                                props.nilais.from + index
                            }}</TableCell>

                            <TableCell>
                                <div class="font-medium">
                                    {{ data.siswa.nama }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    ID: {{ data.siswa.id }}
                                </div>
                            </TableCell>

                            <TableCell>
                                <span
                                    class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800"
                                >
                                    {{ data.kelas }}
                                </span>
                            </TableCell>

                            <TableCell>{{ data.mapel }}</TableCell>

                            <TableCell>
                                <span
                                    :class="[
                                        'rounded-full px-3 py-1 text-sm font-medium',
                                        getNilaiColor(data.nilai),
                                    ]"
                                >
                                    {{ data.nilai }} ({{
                                        getNilaiHuruf(data.nilai)
                                    }})
                                </span>
                            </TableCell>

                            <TableCell>
                                <span
                                    :class="[
                                        'rounded-full px-2 py-1 text-xs font-medium',
                                        data.nilai >= 65
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{
                                        data.nilai >= 65
                                            ? 'Lulus'
                                            : 'Tidak Lulus'
                                    }}
                                </span>
                            </TableCell>

                            <TableCell class="text-sm text-gray-500">
                                {{ formatDate(data.updated_at) }}
                            </TableCell>

                            <TableCell class="space-x-2 text-right">
                                <Link :href="nilai.edit({ nilai: data.id })">
                                    <Button class="bg-blue-200">Edit</Button>
                                </Link>
                                <Button
                                    class="bg-red-600"
                                    @click="handleDelete(data.id)"
                                >
                                    Hapus
                                </Button>
                            </TableCell>
                        </TableRow>

                        <!-- Empty State -->
                        <TableRow v-if="props.nilais.data.length === 0">
                            <TableCell colspan="8" class="py-8 text-center">
                                <div class="text-gray-500">
                                    <svg
                                        class="mx-auto h-12 w-12 text-gray-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <p class="mt-2 text-sm font-medium">
                                        Tidak ada data nilai
                                    </p>
                                    <p
                                        class="text-xs"
                                        v-if="
                                            search ||
                                            selectedKelas ||
                                            selectedMapel
                                        "
                                    >
                                        Tidak ditemukan hasil untuk filter yang
                                        dipilih
                                    </p>
                                    <p class="text-xs" v-else>
                                        Mulai dengan menambahkan nilai baru
                                    </p>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div
                v-if="props.nilais.last_page > 1"
                class="flex items-center justify-between"
            >
                <div class="text-sm text-gray-700">
                    Menampilkan {{ props.nilais.from }} hingga
                    {{ props.nilais.to }} dari {{ props.nilais.total }} hasil
                </div>
                <div class="flex space-x-1">
                    <button
                        v-for="page in props.nilais.last_page"
                        :key="page"
                        @click="
                            router.get(nilai.index(), {
                                page,
                                search: filters?.search,
                                kelas: filters?.kelas,
                                mapel: filters?.mapel,
                            })
                        "
                        :class="[
                            'rounded-md px-3 py-1 text-sm transition-colors',
                            page === props.nilais.current_page
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-200 text-gray-700 hover:bg-gray-300',
                        ]"
                    >
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
