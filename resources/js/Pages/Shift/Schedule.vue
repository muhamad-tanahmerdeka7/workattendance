<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";

const props = defineProps({
    employees: Array,
    dates: Array,
    startDate: String,
});

const currentStartDate = ref(props.startDate);

// Ambangan batas minimum personel masuk kerja per hari (Dapat disesuaikan)
const MIN_WORKING_EMPLOYEES = 5;

const changeStartDate = () => {
    router.get(route('shifts.schedule'), { start_date: currentStartDate.value }, { preserveState: true });
};

// Format Tanggal Singkat untuk Header Tabel (e.g., TGL 5)
const formatDateHeader = (dateStr) => {
    const d = new Date(dateStr);
    return `TGL ${d.getDate()}`;
};

// Format Hari Singkat untuk Tampilan Mobile (e.g., Sen, Sel, Rab)
const formatDayName = (dateStr) => {
    const days = ['Ming', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    const d = new Date(dateStr);
    return days[d.getDay()];
};

// Ambil Status Shift Karyawan pada Tanggal Tertentu
const getShiftStatus = (employee, date) => {
    const shiftData = employee.employee_shifts?.find(s => s.date === date);
    if (!shiftData || !shiftData.shift_id) return { label: 'OFF', isOff: true };

    return {
        label: shiftData.shift ? shiftData.shift.name.substring(0, 3).toUpperCase() : 'MSK',
        isOff: false
    };
};

// Hitung Total Hari Kerja per Karyawan (Horizontal)
const getTotalWorkDays = (employee) => {
    return employee.employee_shifts?.filter(s => s.shift_id !== null).length || 0;
};

// Hitung Total Karyawan Masuk Kerja per Tanggal (Vertikal)
const getDailyWorkingCount = (date) => {
    if (!props.employees) return 0;
    return props.employees.reduce((count, emp) => {
        const shiftData = emp.employee_shifts?.find(s => s.date === date);
        return (shiftData && shiftData.shift_id !== null) ? count + 1 : count;
    }, 0);
};

// Penentu Style Warna Rekap Harian Berdasarkan Minimum Karyawan
const getDailyBadgeClass = (count) => {
    if (count === 0) {
        return 'bg-rose-100 text-rose-700 border-rose-300 font-black ring-1 ring-rose-300';
    } else if (count < MIN_WORKING_EMPLOYEES) {
        return 'bg-amber-100 text-amber-800 border-amber-300 font-bold';
    }
    return 'bg-emerald-100 text-emerald-800 border-emerald-300 font-extrabold';
};
</script>

<template>
    <Head title="Matriks Jadwal Shift Karyawan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <h2 class="text-base font-bold leading-tight sm:text-xl text-slate-800">
                        Daftar Jadwal Shift Karyawan
                    </h2>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Status kehadiran dan hari libur tim secara real-time.</p>
                </div>

                <!-- TOMBOL EKSPOR & PICKER TANGGAL -->
                <div class="flex flex-wrap items-center justify-between w-full gap-2 sm:w-auto sm:justify-end">

                    <!-- Tombol Export Excel / WPS -->
                    <a
                        :href="route('shifts.schedule.export', { start_date: currentStartDate })"
                        class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Excel / WPS
                    </a>

                    <!-- Tombol Export PDF -->
                    <a
                        :href="route('shifts.schedule.exportPdf', { start_date: currentStartDate })"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        PDF
                    </a>

                    <!-- Filter Tanggal -->
                    <div class="flex items-center gap-2 bg-white px-3 py-1.5 border border-slate-200 rounded-xl shadow-sm">
                        <label class="text-xs font-bold text-slate-600 whitespace-nowrap">Mulai Tanggal:</label>
                        <input
                            type="date"
                            v-model="currentStartDate"
                            @change="changeStartDate"
                            class="p-0 text-xs font-semibold bg-transparent border-0 focus:ring-0 text-slate-800"
                        />
                    </div>
                </div>
            </div>
        </template>

        <div class="px-2 py-3 mx-auto sm:px-4 sm:py-6 max-w-7xl">

            <!-- Legend / Indikator Warna Status -->
            <div class="flex flex-wrap items-center justify-between gap-2 p-3 text-xs text-white bg-slate-900 rounded-t-2xl">
                <span class="font-bold tracking-wider uppercase text-[10px] sm:text-xs">Jadwal Shift Tim</span>
                <div class="flex flex-wrap items-center gap-3 text-[10px] sm:text-xs">
                    <span class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 bg-emerald-100 border border-emerald-500 rounded-sm inline-block"></span> Masuk
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 bg-rose-100 border border-rose-500 rounded-sm inline-block"></span> OFF
                    </span>
                    <span class="flex items-center gap-1 pl-3 border-l border-slate-700">
                        <span class="w-2.5 h-2.5 bg-amber-200 border border-amber-500 rounded-sm inline-block"></span> Personel &lt; {{ MIN_WORKING_EMPLOYEES }}
                    </span>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 1. TAMPILAN MOBILE KHUSUS HP (md:hidden)  -->
            <!-- ========================================== -->
            <div class="block p-2 space-y-2.5 bg-white border-b border-x border-slate-200 rounded-b-2xl md:hidden shadow-sm">
                <div
                    v-for="(emp, idx) in employees"
                    :key="emp.id"
                    class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2"
                >
                    <!-- Header Kartu: Nama, Bagian (Jabatan) & Total Kerja -->
                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-200">
                        <div class="flex items-center min-w-0 gap-2">
                            <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[10px] font-bold shrink-0">
                                {{ idx + 1 }}
                            </span>
                            <div class="truncate">
                                <h4 class="text-xs font-bold leading-tight capitalize truncate text-slate-800">{{ emp.name }}</h4>
                                <!-- MENAMPILKAN JABATAN/BAGIAN (OPERATOR / ADMIN / LOGISTIK / QUALITY) -->
                                <span class="text-[9px] font-bold uppercase text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200 inline-block">
                                    {{ emp.employee?.position || emp.employee?.designation || 'OPERATOR' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/80 border border-emerald-200 px-2 py-0.5 rounded-md">
                                {{ getTotalWorkDays(emp) }} Hari Kerja
                            </span>
                        </div>
                    </div>

                    <!-- Baris Deretan Tanggal (7 Hari Pas di Layar HP) -->
                    <div class="grid grid-cols-7 gap-1 text-center">
                        <div v-for="d in dates" :key="d" class="flex flex-col items-center">
                            <span class="text-[8px] font-semibold text-slate-400 uppercase leading-none mb-0.5">
                                {{ formatDayName(d) }}
                            </span>
                            <span class="text-[9px] font-bold text-slate-600 leading-none mb-1">
                                {{ new Date(d).getDate() }}
                            </span>
                            <span
                                class="w-full py-1 text-[9px] font-bold rounded border text-center leading-none"
                                :class="getShiftStatus(emp, d).isOff
                                    ? 'bg-rose-50 text-rose-600 border-rose-200'
                                    : 'bg-emerald-50 text-emerald-600 border-emerald-200'"
                            >
                                {{ getShiftStatus(emp, d).label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Rekap Total Masuk Kerja Per Hari (Mobile View) -->
                <div class="p-2.5 bg-slate-100 border border-slate-300 rounded-xl mt-3 space-y-1.5">
                    <p class="text-[10px] font-extrabold text-slate-700 text-center uppercase tracking-wider">
                        Total Masuk Kerja (Per Hari):
                    </p>
                    <div class="grid grid-cols-7 gap-1 text-center">
                        <div v-for="d in dates" :key="'m-total-'+d" class="flex flex-col items-center">
                            <span class="text-[8px] font-bold text-slate-500 uppercase">{{ formatDayName(d) }}</span>
                            <span
                                class="text-[11px] px-1 py-0.5 rounded w-full border mt-0.5 transition"
                                :class="getDailyBadgeClass(getDailyWorkingCount(d))"
                            >
                                {{ getDailyWorkingCount(d) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 2. TAMPILAN DESKTOP LAPTOP/PC (hidden md:block) -->
            <!-- ========================================== -->
            <div class="hidden overflow-hidden bg-white border-b shadow-sm md:block border-x border-slate-200 rounded-b-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-800 text-white text-[11px] font-bold uppercase tracking-wider">
                            <th class="w-10 p-3 text-center border-b border-slate-700">NO</th>
                            <th class="p-3 border-b border-slate-700">NAMA KARYAWAN</th>
                            <th class="p-3 border-b border-slate-700">BAGIAN</th>
                            <th v-for="d in dates" :key="d" class="p-3 text-center border-b border-slate-700">
                                {{ formatDateHeader(d) }}
                            </th>
                            <th class="p-3 text-center border-b border-slate-700 bg-slate-950">TOTAL KERJA</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100">
                        <tr v-for="(emp, idx) in employees" :key="emp.id" class="transition hover:bg-slate-50/80">
                            <td class="p-3 font-semibold text-center text-slate-400">{{ idx + 1 }}</td>
                            <td class="p-3 font-bold capitalize text-slate-800">{{ emp.name }}</td>
                            <td class="p-3">
                                <!-- MENAMPILKAN JABATAN/BAGIAN (ADMIN / OPERATOR / LOGISTIK / QUALITY) -->
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase text-blue-700 bg-blue-50 border border-blue-200 rounded-lg">
                                    {{ emp.employee?.position || emp.employee?.designation || 'OPERATOR' }}
                                </span>
                            </td>
                            <td v-for="d in dates" :key="d" class="p-2 text-center">
                                <span
                                    class="inline-block px-2.5 py-1 text-[10px] font-bold rounded-lg border min-w-[42px]"
                                    :class="getShiftStatus(emp, d).isOff
                                        ? 'bg-rose-50 text-rose-600 border-rose-200'
                                        : 'bg-emerald-50 text-emerald-600 border-emerald-200'"
                                >
                                    {{ getShiftStatus(emp, d).label }}
                                </span>
                            </td>
                            <td class="p-3 font-bold text-center bg-slate-50 text-emerald-700">
                                {{ getTotalWorkDays(emp) }} Hari
                            </td>
                        </tr>
                    </tbody>

                    <!-- FOOTER REKAPITULASI TOTAL MASUK KERJA -->
                    <tfoot>
                        <tr class="text-xs font-extrabold border-t-2 bg-slate-100/90 border-slate-300 text-slate-800">
                            <td colspan="3" class="p-3 pr-6 tracking-wider text-right uppercase text-slate-700">
                                Total Masuk Kerja (Per Hari):
                            </td>
                            <td v-for="d in dates" :key="'total-'+d" class="p-2.5 text-center">
                                <span
                                    class="inline-block px-2.5 py-1 rounded-md text-xs border transition"
                                    :class="getDailyBadgeClass(getDailyWorkingCount(d))"
                                >
                                    {{ getDailyWorkingCount(d) }}
                                </span>
                            </td>
                            <td class="p-3 bg-slate-200/60"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
