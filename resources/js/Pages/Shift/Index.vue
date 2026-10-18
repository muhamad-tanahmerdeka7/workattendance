<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";

const props = defineProps({
    shifts: Array,
    subordinates: Array,
    selectedDate: String,
});

const currentDate = ref(props.selectedDate);
const assignMode = ref('daily'); // Pilihan: 'daily' (Harian) atau 'weekly' (Mingguan)

// Filter tanggal berubah
const changeDate = () => {
    router.get(route('shifts.index'), { date: currentDate.value }, { preserveState: true });
};

// Logika Drag
const onDragStart = (event, user) => {
    event.dataTransfer.setData('userId', user.id);
};

// Logika Drop ke kotak Shift Reguler
const onDrop = (event, shiftId) => {
    const userId = event.dataTransfer.getData('userId');
    if (!userId) return;

    router.post(route('shifts.assign'), {
        user_id: userId,
        shift_id: shiftId,
        date: currentDate.value,
        mode: assignMode.value,
        is_off: false,
    }, {
        preserveScroll: true,
    });
};

// Logika Drop khusus Kotak Libur (Day Off)
const onDropOff = (event) => {
    const userId = event.dataTransfer.getData('userId');
    if (!userId) return;

    router.post(route('shifts.assign'), {
        user_id: userId,
        shift_id: null,
        date: currentDate.value,
        mode: assignMode.value,
        is_off: true,
    }, {
        preserveScroll: true,
    });
};

const onDragOver = (event) => {
    event.preventDefault();
};
</script>

<template>
    <Head title="Manajemen Shift & Libur Kerja" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Atur Shift & Hari Libur Kerja
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola jadwal shift harian atau mingguan dengan mudah (Drag & Drop).</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Tombol Download Laporan Shift / Rekap Excel -->
                    <a
                        :href="route('shifts.export')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white transition shadow-sm bg-emerald-600 hover:bg-emerald-700 rounded-xl"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Rekap Shift (Excel/CSV)
                    </a>

                    <!-- Tombol Download / Cetak Laporan PDF -->
                    <a
                        :href="route('shifts.exportPdf')"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white transition shadow-sm bg-rose-600 hover:bg-rose-700 rounded-xl"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Export PDF
                    </a>

                    <!-- Pilihan Mode Periode -->
                    <div class="flex p-1 bg-slate-200 rounded-xl">
                        <button
                            @click="assignMode = 'daily'"
                            :class="assignMode === 'daily' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600'"
                            class="px-3 py-1 text-xs font-bold transition rounded-lg"
                        >
                            Harian
                        </button>
                        <button
                            @click="assignMode = 'weekly'"
                            :class="assignMode === 'weekly' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600'"
                            class="px-3 py-1 text-xs font-bold transition rounded-lg"
                        >
                            Mingguan (1 Pekan)
                        </button>
                    </div>

                    <!-- Input Tanggal -->
                    <div class="flex items-center gap-2 bg-white px-3 py-1.5 border border-slate-200 rounded-xl shadow-sm">
                        <label class="text-xs font-bold text-slate-600">Tanggal:</label>
                        <input
                            type="date"
                            v-model="currentDate"
                            @change="changeDate"
                            class="p-0 text-xs font-semibold border-0 focus:ring-0 text-slate-800"
                        />
                    </div>
                </div>
            </div>
        </template>

        <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">

                <!-- KOLOM KIRI: DAFTAR KARYAWAN TIM -->
                <div class="p-4 bg-white border shadow-sm rounded-2xl border-slate-100 lg:col-span-1">
                    <h3 class="mb-1 text-sm font-bold tracking-wider uppercase text-slate-800">Daftar Karyawan</h3>
                    <p class="mb-4 text-xs text-slate-400">Mode Aktif: <span class="font-bold text-blue-600 uppercase">{{ assignMode === 'weekly' ? '1 Minggu Penuh' : 'Harian' }}</span></p>

                    <div class="space-y-2.5 max-h-[550px] overflow-y-auto pr-1">
                        <div
                            v-for="sub in subordinates"
                            :key="sub.id"
                            draggable="true"
                            @dragstart="onDragStart($event, sub)"
                            class="flex items-center justify-between p-3 transition border shadow-sm bg-slate-50 border-slate-200 rounded-xl cursor-grab active:cursor-grabbing hover:bg-blue-50 hover:border-blue-200"
                        >
                            <div>
                                <p class="text-xs font-bold text-slate-800">{{ sub.name }}</p>
                                <p class="text-[10px] text-slate-400">NIK: {{ sub.employee?.employee_code || '-' }}</p>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md"
                                :class="sub.employee_shifts.length > 0 ? (sub.employee_shifts[0].shift_id ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700') : 'bg-amber-100 text-amber-700'">
                                {{ sub.employee_shifts.length > 0 ? (sub.employee_shifts[0].shift_id ? 'Ada Shift' : 'Libur (Off)') : 'Belum' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN: KOTAK SHIFT & KOTAK LIBUR -->
                <div class="grid grid-cols-1 gap-4 lg:col-span-3 md:grid-cols-2 lg:grid-cols-4">

                    <!-- Kotak Shift Reguler (Pagi, Siang, Malam, dll) -->
                    <div
                        v-for="shift in shifts"
                        :key="shift.id"
                        @dragover="onDragOver"
                        @drop="onDrop($event, shift.id)"
                        class="bg-white border-2 border-dashed border-slate-300 rounded-2xl p-4 flex flex-col min-h-[420px] bg-slate-50/50 hover:border-blue-400 transition shadow-sm"
                    >
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                            <h4 class="text-sm font-bold text-slate-800">{{ shift.name }}</h4>
                            <span class="text-[10px] font-mono font-semibold text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">
                                {{ shift.start_time.substring(0,5) }} - {{ shift.end_time.substring(0,5) }}
                            </span>
                        </div>

                        <!-- Daftar Karyawan dalam Shift Ini -->
                        <div class="flex-1 space-y-2 overflow-y-auto">
                            <template v-for="sub in subordinates" :key="sub.id">
                                <div
                                    v-if="sub.employee_shifts.length > 0 && sub.employee_shifts[0].shift_id === shift.id"
                                    draggable="true"
                                    @dragstart="onDragStart($event, sub)"
                                    class="p-2.5 bg-white border border-slate-200 rounded-xl shadow-sm flex items-center justify-between cursor-grab hover:border-blue-300"
                                >
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">{{ sub.name }}</p>
                                        <p class="text-[10px] text-slate-400">{{ sub.employee?.department || 'Plant 8' }}</p>
                                    </div>
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                </div>
                            </template>

                            <div class="h-full flex items-center justify-center text-[11px] text-slate-400 italic pt-16 pointer-events-none text-center px-4">
                                Drop karyawan ke sini
                            </div>
                        </div>
                    </div>

                    <!-- KOTAK KHUSUS: HARI LIBUR (DAY OFF) -->
                    <div
                        @dragover="onDragOver"
                        @drop="onDropOff"
                        class="bg-red-50/30 border-2 border-dashed border-red-200 rounded-2xl p-4 flex flex-col min-h-[420px] hover:border-red-400 transition shadow-sm"
                    >
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-red-100">
                            <h4 class="text-sm font-bold text-red-700">Hari Libur (Off)</h4>
                            <span class="text-[10px] font-semibold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">
                                Day Off
                            </span>
                        </div>

                        <!-- Daftar Karyawan yang Libur -->
                        <div class="flex-1 space-y-2 overflow-y-auto">
                            <template v-for="sub in subordinates" :key="sub.id">
                                <div
                                    v-if="sub.employee_shifts.length > 0 && sub.employee_shifts[0].shift_id === null"
                                    draggable="true"
                                    @dragstart="onDragStart($event, sub)"
                                    class="p-2.5 bg-white border border-red-200 rounded-xl shadow-sm flex items-center justify-between cursor-grab hover:border-red-300"
                                >
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">{{ sub.name }}</p>
                                        <p class="text-[10px] text-red-500 font-semibold">Libur Terjadwal</p>
                                    </div>
                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                </div>
                            </template>

                            <div class="h-full flex items-center justify-center text-[11px] text-red-300 italic pt-16 pointer-events-none text-center px-4">
                                Drop ke sini untuk meliburkan
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
