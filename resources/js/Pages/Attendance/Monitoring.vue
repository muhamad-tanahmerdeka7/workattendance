<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    teamAttendances: Array,
});
</script>

<template>
    <Head title="Monitoring Presensi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Monitoring Presensi Karyawan</h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3">Nama Karyawan</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Masuk</th>
                                <th class="px-4 py-3">Pulang</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in props.teamAttendances" :key="item.id" class="border-b">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ item.user.name }}</td>
                                <td class="px-4 py-3">{{ item.date }}</td>
                                <td class="px-4 py-3">{{ item.time_in || '-' }}</td>
                                <td class="px-4 py-3">{{ item.time_out || '-' }}</td>
                                <td class="px-4 py-3">
                                    <span :class="{
                                        'bg-green-100 text-green-800': item.status === 'present',
                                        'bg-yellow-100 text-yellow-800': item.status === 'late',
                                        'bg-red-100 text-red-800': item.status === 'absent'
                                    }" class="px-2 py-1 rounded text-xs font-semibold uppercase">
                                        {{ item.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
