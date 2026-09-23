<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { usePermission } from '@/Composables/usePermission';

const props = defineProps({
    leaves: {
        type: Array,
        default: () => [],
    },
});

const { hasPermission } = usePermission();

const form = useForm({
    type: 'permission',
    start_date: '',
    end_date: '',
    reason: '',
});

const submitLeave = () => {
    form.post(route('leave.store'), {
        onSuccess: () => form.reset(),
    });
};

const approveLeave = (id) => {
    useForm({ status: 'approved' }).post(route('leave.approve', id));
};

const rejectLeave = (id) => {
    const note = prompt('Alasan Penolakan:');
    if (note) {
        useForm({ status: 'rejected', rejection_note: note }).post(route('leave.approve', id));
    }
};
</script>

<template>
    <Head title="Pengajuan Izin & Sakit" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengajuan Izin & Sakit</h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Form Pengajuan Izin/Sakit -->
            <div v-if="hasPermission('create leave request')" class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Buat Pengajuan</h3>
                <form @submit.prevent="submitLeave" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pengajuan</label>
                        <select v-model="form.type" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="permission">Izin</option>
                            <option value="sick">Sakit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input v-model="form.start_date" type="date" class="w-full rounded-md border-gray-300 text-sm" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input v-model="form.end_date" type="date" class="w-full rounded-md border-gray-300 text-sm" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
                        <input v-model="form.reason" type="text" class="w-full rounded-md border-gray-300 text-sm" placeholder="Isi alasan pengajuan..." required />
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-4 py-2 rounded-md font-medium text-sm hover:bg-blue-700 transition">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Pengajuan -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Daftar Pengajuan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3">Nama Karyawan</th>
                                <th class="px-4 py-3">Tipe</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Alasan</th>
                                <th class="px-4 py-3">Status</th>
                                <th v-if="hasPermission('approve leave request')" class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in props.leaves" :key="item.id" class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ item.user?.name || '-' }}</td>
                                <td class="px-4 py-3 uppercase font-semibold text-xs">{{ item.type }}</td>
                                <td class="px-4 py-3">{{ item.start_date }} s/d {{ item.end_date }}</td>
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
                                <td v-if="hasPermission('approve leave request')" class="px-4 py-3 space-x-2">
                                    <button v-if="item.status === 'pending'" @click="approveLeave(item.id)" class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">
                                        Setujui
                                    </button>
                                    <button v-if="item.status === 'pending'" @click="rejectLeave(item.id)" class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                                        Tolak
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="props.leaves.length === 0">
                                <td colspan="6" class="px-4 py-4 text-center text-gray-400">Belum ada data pengajuan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
