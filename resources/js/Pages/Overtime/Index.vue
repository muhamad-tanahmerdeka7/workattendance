<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { usePermission } from '@/Composables/usePermission';

const props = defineProps({
    overtimes: Array,
});

const { hasPermission } = usePermission();

const form = useForm({
    date: '',
    start_time: '',
    end_time: '',
    reason: '',
});

const submitOvertime = () => {
    form.post(route('overtime.store'), {
        onSuccess: () => form.reset(),
    });
};

const approveOvertime = (id) => {
    useForm({ status: 'approved' }).post(route('overtime.approve', id));
};

const rejectOvertime = (id) => {
    const note = prompt('Alasan Penolakan:');
    if (note) {
        useForm({ status: 'rejected', rejection_note: note }).post(route('overtime.approve', id));
    }
};
</script>

<template>
    <Head title="Pengajuan Lembur" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengajuan Lembur</h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Form Pengajuan Lembur (Karyawan) -->
            <div v-if="hasPermission('create overtime request')" class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Buat Pengajuan Lembur</h3>
                <form @submit.prevent="submitOvertime" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input v-model="form.date" type="date" class="w-full rounded-md border-gray-300 text-sm" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                        <input v-model="form.start_time" type="time" class="w-full rounded-md border-gray-300 text-sm" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                        <input v-model="form.end_time" type="time" class="w-full rounded-md border-gray-300 text-sm" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan/Alasan</label>
                        <input v-model="form.reason" type="text" class="w-full rounded-md border-gray-300 text-sm" required />
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-4 py-2 rounded-md font-medium text-sm hover:bg-blue-700">
                            Kirim Pengajuan Lembur
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel List Lembur -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Daftar Pengajuan Lembur</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Jam</th>
                                <th class="px-4 py-3">Alasan</th>
                                <th class="px-4 py-3">Status</th>
                                <th v-if="hasPermission('approve overtime request')" class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in props.overtimes" :key="item.id" class="border-b">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ item.user.name }}</td>
                                <td class="px-4 py-3">{{ item.date }}</td>
                                <td class="px-4 py-3">{{ item.start_time }} - {{ item.end_time }}</td>
                                <td class="px-4 py-3">{{ item.reason }}</td>
                                <td class="px-4 py-3">
                                    <span :class="{
                                        'bg-yellow-100 text-yellow-800': item.status === 'pending',
                                        'bg-green-100 text-green-800': item.status === 'approved',
                                        'bg-red-100 text-red-800': item.status === 'rejected'
                                    }" class="px-2 py-1 rounded text-xs font-semibold uppercase">
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td v-if="hasPermission('approve overtime request')" class="px-4 py-3 space-x-2">
                                    <button v-if="item.status === 'pending'" @click="approveOvertime(item.id)" class="text-xs bg-green-600 text-white px-2 py-1 rounded">Setujui</button>
                                    <button v-if="item.status === 'pending'" @click="rejectOvertime(item.id)" class="text-xs bg-red-600 text-white px-2 py-1 rounded">Tolak</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
