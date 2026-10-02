<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { usePermission } from "@/Composables/usePermission";

const props = defineProps({
    todayAttendance: Object,
    attendances: Array,
});

const user = usePage().props.auth.user;
const { hasPermission } = usePermission();

const formCheckIn = useForm({ notes: "" });
const formCheckOut = useForm({});

const submitCheckIn = () => {
    formCheckIn.post(route("attendance.checkIn"));
};

const submitCheckOut = () => {
    formCheckOut.post(route("attendance.checkOut"));
};
</script>

<template>
    <Head title="Presensi Saya" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Presensi Kehadiran
            </h2>
        </template>

        <!-- ========================================== -->
        <!-- 1. TAMPILAN DESKTOP (Hanya Muncul di Laptop/PC) -->
        <!-- ========================================== -->
        <div class="hidden md:block py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Card Absen Hari Ini Desktop -->
            <div
                v-if="hasPermission('create attendance')"
                class="bg-white p-6 rounded-lg shadow-sm border border-gray-100"
            >
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    Absen Hari Ini
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Absen Masuk -->
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <p class="text-sm font-semibold text-gray-600">
                            Jam Masuk
                        </p>
                        <p class="text-2xl font-bold text-gray-800 my-2">
                            {{ props.todayAttendance?.time_in || "--:--" }}
                        </p>
                        <form
                            v-if="!props.todayAttendance?.time_in"
                            @submit.prevent="submitCheckIn"
                        >
                            <input
                                v-model="formCheckIn.notes"
                                type="text"
                                placeholder="Catatan (opsional)"
                                class="w-full text-sm rounded-md border-gray-300 mb-3"
                            />
                            <button
                                type="submit"
                                :disabled="formCheckIn.processing"
                                class="w-full bg-blue-600 text-white py-2 rounded-md font-medium hover:bg-blue-700 transition"
                            >
                                Absen Masuk
                            </button>
                        </form>
                        <span
                            v-else
                            class="text-xs text-green-600 font-semibold"
                            >Sudah Absen Masuk</span
                        >
                    </div>

                    <!-- Absen Pulang -->
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <p class="text-sm font-semibold text-gray-600">
                            Jam Pulang
                        </p>
                        <p class="text-2xl font-bold text-gray-800 my-2">
                            {{ props.todayAttendance?.time_out || "--:--" }}
                        </p>
                        <form
                            v-if="
                                props.todayAttendance?.time_in &&
                                !props.todayAttendance?.time_out
                            "
                            @submit.prevent="submitCheckOut"
                        >
                            <button
                                type="submit"
                                :disabled="formCheckOut.processing"
                                class="w-full bg-red-600 text-white py-2 rounded-md font-medium hover:bg-red-700 transition"
                            >
                                Absen Pulang
                            </button>
                        </form>
                        <span
                            v-else-if="props.todayAttendance?.time_out"
                            class="text-xs text-green-600 font-semibold"
                            >Sudah Absen Pulang</span
                        >
                        <span v-else class="text-xs text-gray-400"
                            >Belum Absen Masuk</span
                        >
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Presensi Desktop -->
            <div
                class="bg-white p-6 rounded-lg shadow-sm border border-gray-100"
            >
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    Riwayat Kehadiran
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50"
                        >
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Masuk</th>
                                <th class="px-4 py-3">Pulang</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in props.attendances"
                                :key="item.id"
                                class="border-b"
                            >
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ item.date }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ item.time_in || "-" }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ item.time_out || "-" }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="{
                                            'bg-green-100 text-green-800':
                                                item.status === 'present',
                                            'bg-yellow-100 text-yellow-800':
                                                item.status === 'late',
                                            'bg-red-100 text-red-800':
                                                item.status === 'absent',
                                        }"
                                        class="px-2 py-1 rounded text-xs font-semibold uppercase"
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ item.notes || "-" }}
                                </td>
                            </tr>
                            <tr v-if="!props.attendances || props.attendances.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                    Belum ada riwayat kehadiran.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 2. TAMPILAN MOBILE (Hanya Muncul di Layar Smartphone) -->
        <!-- ========================================== -->
        <div class="block md:hidden p-4 space-y-4">
            <!-- HERO CARD BIRU MOBILE -->
            <div
                v-if="hasPermission('create attendance')"
                class="bg-blue-600 rounded-3xl p-5 text-white shadow-lg relative overflow-hidden"
            >
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold">Halo, {{ user?.name }}</h2>
                        <p class="text-xs text-blue-100 font-medium">Karyawan</p>
                    </div>
                    <div class="flex items-center gap-1.5 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-[11px] font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ new Date().toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' }) }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 text-slate-800 shadow-md space-y-4">
                    <div class="flex items-center justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Kantor Pusat</p>
                                <p class="text-[10px] text-slate-500">Anda berada dalam area kerja</p>
                            </div>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-center py-1">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <p class="text-[11px] text-slate-400 font-medium">Jam Masuk</p>
                            <p class="text-base font-bold text-slate-800 mt-0.5">
                                {{ props.todayAttendance?.time_in || "--:--" }}
                            </p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <p class="text-[11px] text-slate-400 font-medium">Jam Pulang</p>
                            <p class="text-base font-bold text-slate-800 mt-0.5">
                                {{ props.todayAttendance?.time_out || "--:--" }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <form
                            v-if="!props.todayAttendance?.time_in"
                            @submit.prevent="submitCheckIn"
                            class="space-y-3"
                        >
                            <input
                                v-model="formCheckIn.notes"
                                type="text"
                                placeholder="Catatan (opsional)"
                                class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                            />
                            <button
                                type="submit"
                                :disabled="formCheckIn.processing"
                                class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2 text-sm disabled:opacity-50"
                            >
                                <span>Absen Masuk</span>
                            </button>
                        </form>

                        <form
                            v-else-if="!props.todayAttendance?.time_out"
                            @submit.prevent="submitCheckOut"
                        >
                            <button
                                type="submit"
                                :disabled="formCheckOut.processing"
                                class="w-full bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2 text-sm disabled:opacity-50"
                            >
                                <span>Absen Pulang</span>
                            </button>
                        </form>

                        <div v-else class="bg-emerald-50 text-emerald-700 text-xs font-bold py-3 px-4 rounded-xl text-center border border-emerald-100">
                            ✓ Absensi Hari Ini Telah Selesai
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIWAYAT KEHADIRAN KARTU MOBILE -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Riwayat Kehadiran</h3>
                    <span class="text-[11px] text-blue-600 font-semibold">Bulan Ini</span>
                </div>

                <div class="space-y-2.5">
                    <div
                        v-for="item in props.attendances"
                        :key="item.id"
                        class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between transition hover:border-blue-100"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                :class="{
                                    'bg-emerald-50 text-emerald-600': item.status === 'present',
                                    'bg-amber-50 text-amber-500': item.status === 'late',
                                    'bg-rose-50 text-rose-500': item.status === 'absent',
                                }"
                                class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 font-bold"
                            >
                                <svg v-if="item.status === 'present'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <svg v-else-if="item.status === 'late'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">{{ item.date }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    {{ item.time_in || '-' }} - {{ item.time_out || '-' }}
                                </p>
                                <p v-if="item.notes" class="text-[10px] text-slate-400 italic mt-0.5">
                                    Catatan: {{ item.notes }}
                                </p>
                            </div>
                        </div>

                        <span
                            :class="{
                                'bg-emerald-50 text-emerald-700 border-emerald-100': item.status === 'present',
                                'bg-amber-50 text-amber-700 border-amber-100': item.status === 'late',
                                'bg-rose-50 text-rose-700 border-rose-100': item.status === 'absent',
                            }"
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border"
                        >
                            {{ item.status }}
                        </span>
                    </div>

                    <div v-if="!props.attendances || props.attendances.length === 0" class="bg-white p-6 rounded-2xl border border-slate-100 text-center text-xs text-slate-400">
                        Belum ada riwayat kehadiran.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
