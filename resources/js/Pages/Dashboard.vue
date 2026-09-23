<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";

const props = defineProps({
    todayAttendance: Object,
    stats: {
        type: Object,
        default: () => ({ total_present: 0, total_late: 0, total_leave: 0 }),
    },
});

const user = usePage().props.auth.user;
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
            <div
                class="bg-white p-6 rounded-lg shadow-sm border border-gray-100"
            >
                <h3 class="text-lg font-bold text-gray-800">
                    Selamat Datang, {{ user?.name || "Karyawan" }}!
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    Sistem Presensi & Manajemen Karyawan
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                    class="bg-white p-6 rounded-lg shadow-sm border border-gray-100"
                >
                    <p class="text-sm font-medium text-gray-500">Hadir</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">
                        {{ props.stats?.total_present ?? 0 }}
                    </p>
                </div>
                <div
                    class="bg-white p-6 rounded-lg shadow-sm border border-gray-100"
                >
                    <p class="text-sm font-medium text-gray-500">Terlambat</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">
                        {{ props.stats?.total_late ?? 0 }}
                    </p>
                </div>
                <div
                    class="bg-white p-6 rounded-lg shadow-sm border border-gray-100"
                >
                    <p class="text-sm font-medium text-gray-500">
                        Izin / Sakit Disetujui
                    </p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">
                        {{ props.stats?.total_leave ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
