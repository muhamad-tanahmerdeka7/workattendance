<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePermission } from "@/Composables/usePermission";

const props = defineProps({
    todayAttendance: Object,
    stats: {
        type: Object,
        default: () => ({ total_present: 0, total_late: 0, total_leave: 0 }),
    },
});

const user = usePage().props.auth.user;
const { hasRole, hasPermission } = usePermission();

// Cek apakah user adalah Line Head
const isLineHead = hasRole('Line Head') || hasPermission('view all attendance');
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Banner Selamat Datang -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">
                    Selamat Datang, {{ user?.name || "User" }}!
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ isLineHead ? 'Dashboard Pemantauan Presensi Tim' : 'Sistem Presensi & Manajemen Karyawan' }}
                </p>
            </div>

            <!-- Kartu Statistik Rekap -->
            <div>
                <h4 class="text-md font-semibold text-gray-700 mb-3">
                    {{ isLineHead ? 'Ringkasan Kehadiran Tim Hari Ini' : 'Ringkasan Kehadiran Saya' }}
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <p class="text-sm font-medium text-gray-500">Hadir</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">
                            {{ props.stats?.total_present ?? 0 }}
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <p class="text-sm font-medium text-gray-500">Terlambat</p>
                        <p class="text-3xl font-bold text-amber-500 mt-2">
                            {{ props.stats?.total_late ?? 0 }}
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <p class="text-sm font-medium text-gray-500">
                            Izin / Sakit Disetujui
                        </p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">
                            {{ props.stats?.total_leave ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kartu Status Presensi Hari Ini (Khusus Karyawan) -->
            <div v-if="!isLineHead" class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h4 class="text-md font-bold text-gray-800 mb-4">Status Absensi Hari Ini</h4>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Jam Masuk</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ props.todayAttendance?.time_in ?? '--:--:--' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jam Pulang</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ props.todayAttendance?.time_out ?? '--:--:--' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status</p>
                        <span
                            v-if="props.todayAttendance"
                            :class="{
                                'bg-green-100 text-green-800': props.todayAttendance.status === 'present',
                                'bg-yellow-100 text-yellow-800': props.todayAttendance.status === 'late',
                                'bg-red-100 text-red-800': props.todayAttendance.status === 'absent',
                            }"
                            class="px-2.5 py-1 rounded text-xs font-semibold uppercase"
                        >
                            {{ props.todayAttendance.status }}
                        </span>
                        <span v-else class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded text-xs font-semibold">
                            Belum Absen
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
