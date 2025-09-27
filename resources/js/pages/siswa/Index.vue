<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import siswa from '@/routes/siswa';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { CircleCheckBig, CircleX } from 'lucide-vue-next';

interface Siswa {
    id: number;
    nama: string;
    kelas: string;
    created_at: string;
    updated_at: string;
}

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    data_siswa: {
        data: Siswa[];
    } & PaginationMeta;
}

const props = defineProps<Props>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Data Siswa',
        href: siswa.index().url,
    },
];

const handleDelete = (id: number) => {
    if (confirm('Apakah Anda yakin menghapus data siswa ini?')) {
        router.delete(siswa.destroy({ siswa: id }), {
            onSuccess: () => {
                // Refresh data setelah delete
                router.reload();
            },
        });
    }
};
</script>

<template>
    <Head title="Data Siswa" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
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
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Data Siswa</h1>
                <Link :href="siswa.create()">
                    <div>
                        <Link :href="siswa.create()">
                            <Button>Tambah siswa baru</Button>
                        </Link>
                    </div>
                </Link>
            </div>

            <!-- Table -->
            <div>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>ID</TableHead>
                            <TableHead>Nama</TableHead>
                            <TableHead>Kelas</TableHead>
                            <TableHead class="text-center">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="data in props.data_siswa.data"
                            :key="data.id"
                        >
                            <TableCell>{{ data.id }}</TableCell>
                            <TableCell>{{ data.nama }}</TableCell>
                            <TableCell>{{ data.kelas }}</TableCell>
                            <TableCell class="space-x-2 text-center">
                                <Link :href="siswa.edit({ siswa: data.id })">
                                    <Button class="bg-blue-200">Ubah</Button>
                                </Link>
                                <Button
                                    class="bg-red-600"
                                    @click="handleDelete(data.id)"
                                >
                                    Hapus
                                </Button>
                            </TableCell>
                        </TableRow>

                        <!-- Empty state -->
                        <TableRow v-if="props.data_siswa.data.length === 0">
                            <TableCell colspan="4" class="py-4 text-center">
                                Tidak ada data siswa
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div
                v-if="props.data_siswa.last_page > 1"
                class="flex justify-center"
            >
                <div class="flex space-x-2">
                    <Button
                        v-for="page in props.data_siswa.last_page"
                        :key="page"
                        @click="router.get(siswa.index(), { page })"
                        :class="
                            page === props.data_siswa.current_page
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-200'
                        "
                    >
                        {{ page }}
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
