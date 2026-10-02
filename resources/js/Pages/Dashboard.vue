<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
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

// Cek apakah user adalah Line Head / Supervisor / Admin
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

        <div class="py-4 sm:py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 md:space-y-6">

            <!-- 1. HERO CARD BANNER (Tampilan Biru ala Mobile Dashboard) -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-2xl md:rounded-3xl p-5 md:p-6 text-white shadow-lg relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg md:text-2xl font-bold">Halo, {{ user?.name || "User" }}</h3>
                        <p class="text-xs text-blue-100 font-medium mt-0.5">
                            {{ isLineHead ? 'Line Head / Supervisor' : 'Karyawan' }}
                        </p>
                    </div>
                    <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-[11px] font-medium text-white shrink-0">
                        {{ new Date().toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' }) }}
                    </span>
                </div>

                <!-- Card Status Presensi Ringkas Hari Ini -->
                <div class="mt-4 md:mt-5 bg-white/10 backdrop-blur-md rounded-xl md:rounded-2xl p-3.5 md:p-4 border border-white/15 flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[10px] md:text-[11px] text-blue-100 uppercase tracking-wider font-semibold">Status Hari Ini</p>
                        <p class="text-sm md:text-base font-bold mt-0.5">
                            {{ props.todayAttendance?.time_in ? (props.todayAttendance.time_in + ' (Masuk)') : 'Belum Absen Masuk' }}
                        </p>
                    </div>
                    <Link
                        :href="route('attendance.index')"
                        class="bg-white text-blue-600 font-bold text-xs px-3.5 md:px-4 py-2 md:py-2.5 rounded-xl shadow-md hover:bg-blue-50 transition active:scale-95 shrink-0"
                    >
                        {{ props.todayAttendance ? 'Lihat Presensi' : 'Absen Masuk' }}
                    </Link>
                </div>
            </div>

            <!-- 2. GRID AKSES CEPAT (Ditampilkan Konsisten untuk Semua Role di Mobile) -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Akses Cepat</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <!-- Izin & Sakit -->
                    <Link
                        :href="route('leave.index')"
                        class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center text-center gap-2 hover:border-blue-200 transition"
                    >
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold">
                            📋
                        </div>
                        <span class="text-xs font-bold text-slate-700">Izin & Sakit</span>
                    </Link>

                    <!-- Presensi / Lembur -->
                    <Link
                        :href="route('attendance.index')"
                        class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center text-center gap-2 hover:border-blue-200 transition"
                    >
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold">
                            ⏰
                        </div>
                        <span class="text-xs font-bold text-slate-700">Presensi Saya</span>
                    </Link>

                    <!-- Monitoring Tim (Jika Line Head) ATAU Riwayat Kehadiran (Jika Karyawan) -->
                    <Link
                        v-if="isLineHead"
                        :href="route('attendance.monitoring')"
                        class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center text-center gap-2 hover:border-blue-200 transition"
                    >
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                            📊
                        </div>
                        <span class="text-xs font-bold text-slate-700">Monitoring Tim</span>
                    </Link>
                    <Link
                        v-else
                        :href="route('attendance.index')"
                        class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center text-center gap-2 hover:border-blue-200 transition"
                    >
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                            📊
                        </div>
                        <span class="text-xs font-bold text-slate-700">Riwayat</span>
                    </Link>

                    <!-- Profil Saya -->
                    <Link
                        :href="route('profile.edit')"
                        class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center text-center gap-2 hover:border-blue-200 transition"
                    >
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                            👤
                        </div>
                        <span class="text-xs font-bold text-slate-700">Profil</span>
                    </Link>
                </div>
            </div>

            <!-- 3. KARTU STATISTIK REKAP (Judul & Angka Dinamis Sesuai Role) -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                    {{ isLineHead ? 'Ringkasan Kehadiran Tim Hari Ini' : 'Ringkasan Kehadiran Saya' }}
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                    <div class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-400">Hadir</p>
                            <p class="text-xl md:text-2xl font-black text-emerald-600 mt-1">
                                {{ props.stats?.total_present ?? 0 }}
                            </p>
                        </div>
                        <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs md:text-sm">
                            ✓
                        </div>
                    </div>

                    <div class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-400">Terlambat</p>
                            <p class="text-xl md:text-2xl font-black text-amber-500 mt-1">
                                {{ props.stats?.total_late ?? 0 }}
                            </p>
                        </div>
                        <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs md:text-sm">
                            !
                        </div>
                    </div>

                    <div class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-400">Izin / Sakit</p>
                            <p class="text-xl md:text-2xl font-black text-blue-600 mt-1">
                                {{ props.stats?.total_leave ?? 0 }}
                            </p>
                        </div>
                        <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs md:text-sm">
                            i
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
