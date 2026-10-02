<script setup>
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    teamAttendances: Array,
});

// State Filter & Pencarian
const search = ref("");
const statusFilter = ref("all");

// Filter data presensi secara langsung di frontend
const filteredAttendances = computed(() => {
    if (!props.teamAttendances) return [];

    return props.teamAttendances.filter((item) => {
        const matchesName = item.user?.name?.toLowerCase().includes(search.value.toLowerCase());
        const matchesStatus = statusFilter.value === "all" || item.status === statusFilter.value;
        return matchesName && matchesStatus;
    });
});
</script>

<template>
    <Head title="Monitoring Presensi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Monitoring Presensi Karyawan
            </h2>
        </template>

        <div class="px-4 py-4 mx-auto space-y-4 sm:py-6 max-w-7xl sm:px-6 lg:px-8">

            <!-- FILTER & PENCARIAN -->
            <div class="flex flex-col items-center justify-between gap-3 p-4 bg-white border shadow-sm rounded-2xl border-slate-100 sm:flex-row">
                <div class="relative w-full sm:w-72">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama karyawan..."
                        class="w-full py-2 pr-4 text-xs pl-9 md:text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                    />
                    <svg class="absolute w-4 h-4 text-slate-400 left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <div class="flex items-center w-full gap-2 sm:w-auto">
                    <select
                        v-model="statusFilter"
                        class="w-full text-xs sm:w-auto md:text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="all">Semua Status</option>
                        <option value="present">Hadir (Present)</option>
                        <option value="late">Terlambat (Late)</option>
                        <option value="absent">Alpa (Absent)</option>
                    </select>
                </div>
            </div>

            <!-- CONTAINER DATA MONITORING -->
            <div class="overflow-hidden bg-white border shadow-sm rounded-2xl border-slate-100">
                <div class="p-4 sm:p-6">

                    <!-- 1. TAMPILAN TABEL DESKTOP (Tampil di Laptop / PC) -->
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs font-semibold uppercase bg-slate-50 text-slate-500">
                                    <th class="p-3">Nama Karyawan</th>
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">Masuk</th>
                                    <th class="p-3">Pulang</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">
                                <tr v-for="item in filteredAttendances" :key="item.id">
                                    <td class="p-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center w-8 h-8 text-xs font-bold text-blue-600 uppercase bg-blue-100 rounded-full shrink-0">
                                                {{ item.user?.name?.charAt(0) || 'U' }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-800">{{ item.user?.name || '-' }}</p>
                                                <!-- MEMANGGIL EMPLOYEE CODE DARI RELASI EMPLOYEE DI TABEL DESKTOP -->
                                                <p class="text-[11px] font-mono text-slate-400">NIK: {{ item.user?.employee?.employee_code || '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3 text-slate-600">{{ item.date }}</td>
                                    <td class="p-3 font-mono text-slate-700">{{ item.time_in || '-' }}</td>
                                    <td class="p-3 font-mono text-slate-700">{{ item.time_out || '-' }}</td>
                                    <td class="p-3">
                                        <span
                                            :class="{
                                                'bg-green-100 text-green-800': item.status === 'present',
                                                'bg-yellow-100 text-yellow-800': item.status === 'late',
                                                'bg-red-100 text-red-800': item.status === 'absent',
                                            }"
                                            class="px-2.5 py-1 rounded-md text-xs font-bold uppercase"
                                        >
                                            {{ item.status }}
                                        </span>
                                    </td>
                                    <td class="max-w-xs p-3 truncate text-slate-600">
                                        <span v-if="item.notes" class="px-2 py-1 text-xs border rounded bg-slate-100 text-slate-700 border-slate-200">
                                            {{ item.notes }}
                                        </span>
                                        <span v-else class="text-xs italic text-slate-300">-</span>
                                    </td>
                                </tr>
                                <tr v-if="filteredAttendances.length === 0">
                                    <td colspan="6" class="p-6 text-xs text-center text-slate-400">
                                        Tidak ada data presensi tim yang ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 2. TAMPILAN CARD MOBILE (Tampil Khusus di Layar HP) -->
                    <div class="block space-y-3 md:hidden">
                        <div
                            v-for="item in filteredAttendances"
                            :key="item.id"
                            class="bg-slate-50 p-4 rounded-2xl border border-slate-100 shadow-sm space-y-2.5"
                        >
                            <div class="flex items-center justify-between border-b border-slate-200/60 pb-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex items-center justify-center text-xs font-bold text-white uppercase bg-blue-600 rounded-full shadow-sm shrink-0 w-9 h-9">
                                        {{ item.user?.name?.charAt(0) || 'U' }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">{{ item.user?.name || '-' }}</p>
                                        <!-- MEMANGGIL EMPLOYEE CODE DARI RELASI EMPLOYEE DI CARD MOBILE -->
                                        <p class="text-[10px] font-mono text-slate-500 mt-0.5">NIK: {{ item.user?.employee?.employee_code || '-' }} • {{ item.date }}</p>
                                    </div>
                                </div>
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800': item.status === 'present',
                                        'bg-yellow-100 text-yellow-800': item.status === 'late',
                                        'bg-red-100 text-red-800': item.status === 'absent',
                                    }"
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase shrink-0"
                                >
                                    {{ item.status }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-1 text-center">
                                <div class="p-2 bg-white border rounded-xl border-slate-100">
                                    <p class="text-[10px] text-slate-400 font-medium">Jam Masuk</p>
                                    <p class="text-xs font-bold text-slate-700 mt-0.5">{{ item.time_in || '--:--' }}</p>
                                </div>
                                <div class="p-2 bg-white border rounded-xl border-slate-100">
                                    <p class="text-[10px] text-slate-400 font-medium">Jam Pulang</p>
                                    <p class="text-xs font-bold text-slate-700 mt-0.5">{{ item.time_out || '--:--' }}</p>
                                </div>
                            </div>

                            <!-- KOTAK CATATAN DI HP -->
                            <div v-if="item.notes" class="bg-blue-50/60 p-2.5 rounded-xl border border-blue-100/80 text-xs">
                                <span class="font-bold text-blue-900 block text-[10px] uppercase">Catatan Karyawan:</span>
                                <p class="text-slate-700 mt-0.5">{{ item.notes }}</p>
                            </div>
                        </div>

                        <div v-if="filteredAttendances.length === 0" class="p-6 text-xs text-center text-slate-400">
                            Tidak ada data presensi tim yang ditemukan.
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
