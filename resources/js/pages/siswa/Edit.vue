<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import siswa from '@/routes/siswa';
import { Head, router, useForm } from '@inertiajs/vue3';

interface Siswa {
    id: number;
    nama: string;
    kelas: string;
}

const props = defineProps<{ data: Siswa }>();

const form = useForm({
    id: props.data.id,
    nama: props.data.nama,
    kelas: props.data.kelas,
});

const handleSubmit = () => {
    form.put(siswa.update({ siswa: props.data.id }).url);
};
</script>

<template>
    <Head title="Ubah data siswa" />
    <AppLayout
        :breadcrumbs="[
            {
                title: 'Ubah data siswa',
                href: `/siswa/${props.data.id}/edit`,
            },
        ]"
    >
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <form @submit.prevent="handleSubmit" class="w-8/12 space-y-4">
                <div class="space-y-2">
                    <Label for="nama">Nama</Label>
                    <Input
                        v-model="form.nama"
                        type="text"
                        placeholder="Nama Siswa"
                    />
                    <div class="text-sm text-red-600" v-if="form.errors.nama">
                        {{ form.errors.nama }}
                    </div>
                </div>
                <div class="space-y-2">
                    <Label for="kelas">Kelas</Label>
                    <Input
                        v-model="form.kelas"
                        type="text"
                        placeholder="Kelas Siswa"
                    />
                    <div class="text-sm text-red-600" v-if="form.errors.kelas">
                        {{ form.errors.kelas }}
                    </div>
                </div>
                <div class="flex gap-4 pt-4">
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-500 px-6 py-2 text-white hover:bg-blue-600 disabled:bg-gray-400"
                    >
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>Simpan Perubahan</span>
                    </Button>

                    <Button
                        type="button"
                        @click="router.visit(siswa.index())"
                        class="rounded-lg bg-gray-500 px-6 py-2 text-white hover:bg-gray-600"
                    >
                        Batal
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
