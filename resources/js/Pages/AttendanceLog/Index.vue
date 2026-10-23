<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    logs: Object,
    filters: Object,
    isManager: Boolean,
    companyLocation: Object,
});

// State Filter Data
const filterDate = ref(props.filters?.date || "");
const filterSource = ref(props.filters?.source_type || "");

const applyFilter = () => {
    router.get(
        "/attendance-logs",
        {
            date: filterDate.value || undefined,
            source_type: filterSource.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

const resetFilter = () => {
    filterDate.value = "";
    filterSource.value = "";
    applyFilter();
};

// Form Pengaturan Titik Lokasi Kantor & Radius (Khusus Manager)
const locationForm = useForm({
    latitude: props.companyLocation?.latitude || 0,
    longitude: props.companyLocation?.longitude || 0,
    radius_meters: props.companyLocation?.radius_meters || 50,
});

const isDetectingLocation = ref(false);

const detectCurrentLocation = () => {
    if (!navigator.geolocation) {
        alert("Browser Anda tidak mendukung deteksi GPS.");
        return;
    }

    isDetectingLocation.value = true;
    navigator.geolocation.getCurrentPosition(
        (position) => {
            locationForm.latitude = position.coords.latitude;
            locationForm.longitude = position.coords.longitude;
            isDetectingLocation.value = false;
        },
        (error) => {
            alert("Gagal membaca sinyal GPS. Pastikan izin akses lokasi peramban sudah aktif.");
            isDetectingLocation.value = false;
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
};

const saveLocation = () => {
    locationForm.post(route("attendance.logs.location.update"), {
        preserveScroll: true,
        onSuccess: () => {
            alert("Titik koordinat dan radius kerja berhasil disimpan ke sistem!");
        },
    });
};
</script>

<template>
    <Head title="Log Mesin & GPS" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Log Mesin & GPS
            </h2>
        </template>

        <div class="py-4 sm:py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Card Pengaturan Lokasi & Radius Kerja (Khusus Manager / muhamad@gmail.com) -->
            <div v-if="props.isManager" class="bg-blue-50/70 border border-blue-200 rounded-2xl p-4 sm:p-6 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-blue-900 flex items-center gap-1.5">
                            📍 Pengaturan Lokasi Presensi & Radius Kerja
                        </h3>
                        <p class="text-xs text-blue-700 mt-0.5">
                            Tentukan titik koordinat pusat kerja dan jarak maksimal toleransi absensi karyawan (Geofence).
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="detectCurrentLocation"
                        :disabled="isDetectingLocation"
                        class="self-start sm:self-auto bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-sm disabled:opacity-50 flex items-center gap-2"
                    >
                        <span v-if="isDetectingLocation" class="animate-spin">⏳</span>
                        <span>{{ isDetectingLocation ? 'Membaca Sinyal GPS...' : 'Ambil Posisi Saya Saat Ini' }}</span>
                    </button>
                </div>

                <form @submit.prevent="saveLocation" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end pt-1">
                    <div>
                        <label class="block text-xs font-semibold text-blue-900 mb-1">Latitude</label>
                        <input
                            type="number"
                            step="any"
                            v-model="locationForm.latitude"
                            required
                            placeholder="-6.xxxxxxx"
                            class="w-full text-xs rounded-xl border-blue-200 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-blue-900 mb-1">Longitude</label>
                        <input
                            type="number"
                            step="any"
                            v-model="locationForm.longitude"
                            required
                            placeholder="106.xxxxxxx"
                            class="w-full text-xs rounded-xl border-blue-200 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-blue-900 mb-1">Radius Toleransi (Meter)</label>
                        <input
                            type="number"
                            v-model="locationForm.radius_meters"
                            required
                            min="10"
                            max="1000"
                            placeholder="Contoh: 50"
                            class="w-full text-xs rounded-xl border-blue-200 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <button
                            type="submit"
                            :disabled="locationForm.processing"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 rounded-xl shadow-sm transition disabled:opacity-50"
                        >
                            Simpan Titik Kerja
                        </button>
                    </div>
                </form>

                <div v-if="props.companyLocation?.updated_by" class="text-[11px] text-blue-600 border-t border-blue-100 pt-2">
                    Terakhir diperbarui oleh: <span class="font-semibold">{{ props.companyLocation.updated_by }}</span>
                </div>
            </div>

            <!-- Card Filter -->
            <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Pilih Tanggal</label>
                    <input
                        type="date"
                        v-model="filterDate"
                        @change="applyFilter"
                        class="text-sm rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sumber Presensi</label>
                    <select
                        v-model="filterSource"
                        @change="applyFilter"
                        class="text-sm rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">Semua Sumber</option>
                        <option value="machine">Mesin Absensi Fisik</option>
                        <option value="gps">GPS Ponsel / Web</option>
                    </select>
                </div>

                <button
                    v-if="filterDate || filterSource"
                    @click="resetFilter"
                    class="px-4 py-2 text-xs font-semibold text-gray-500 hover:text-gray-800 border rounded-xl hover:bg-gray-50 transition"
                >
                    Reset Filter
                </button>
            </div>

            <!-- Card Data Tabel & Mobile View -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 sm:px-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800">Riwayat Catatan Presensi Mentah</h3>
                    <span class="text-xs text-gray-400">Total: {{ props.logs?.total || 0 }} Data</span>
                </div>

                <!-- 1. Tampilan Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                <th class="p-3">Waktu Presensi</th>
                                <th class="p-3">Karyawan</th>
                                <th class="p-3">Jenis</th>
                                <th class="p-3">Sumber</th>
                                <th class="p-3">Perangkat / Mesin</th>
                                <th class="p-3">Detail Lokasi (GPS)</th>
                                <th class="p-3">Status Lokasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="item in props.logs?.data || []" :key="item.id" class="hover:bg-gray-50">
                                <td class="p-3 font-mono text-xs text-gray-700">
                                    {{ new Date(item.scanned_at).toLocaleString('id-ID') }}
                                </td>
                                <td class="p-3">
                                    <p class="font-medium text-gray-800">{{ item.user?.name || '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ item.nik || '-' }}</p>
                                </td>
                                <td class="p-3 uppercase font-bold text-xs">
                                    <span :class="item.scan_type === 'in' ? 'text-blue-600' : 'text-orange-600'">
                                        {{ item.scan_type }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span
                                        :class="item.source_type === 'machine' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200'"
                                        class="px-2.5 py-1 rounded-full text-xs font-medium border"
                                    >
                                        {{ item.source_type === 'machine' ? 'Mesin Fisik' : 'GPS Mobile' }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-gray-600">
                                    {{ item.device_name || '-' }}
                                </td>
                                <td class="p-3 text-xs text-gray-600">
                                    <template v-if="item.source_type === 'gps' && item.latitude">
                                        <a
                                            :href="`https://maps.google.com/?q=${item.latitude},${item.longitude}`"
                                            target="_blank"
                                            class="text-blue-600 hover:underline flex items-center gap-1 font-mono"
                                        >
                                            📍 {{ Number(item.latitude).toFixed(4) }}, {{ Number(item.longitude).toFixed(4) }}
                                        </a>
                                        <span class="text-[11px] text-gray-400">Jarak: {{ item.distance_meters ? Math.round(item.distance_meters) + ' m' : '-' }}</span>
                                    </template>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="p-3">
                                    <template v-if="item.source_type === 'gps'">
                                        <span
                                            :class="item.is_in_radius ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                            class="px-2 py-0.5 rounded text-[11px] font-bold"
                                        >
                                            {{ item.is_in_radius ? 'Di Dalam Radius' : 'Luar Radius' }}
                                        </span>
                                    </template>
                                    <span v-else class="text-xs text-gray-400">Terverifikasi Mesin</span>
                                </td>
                            </tr>
                            <tr v-if="!props.logs?.data || props.logs.data.length === 0">
                                <td colspan="7" class="p-6 text-center text-gray-400 text-sm">
                                    Tidak ada data log yang ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 2. Tampilan Kartu Mobile -->
                <div class="block md:hidden divide-y divide-gray-100">
                    <div v-for="item in props.logs?.data || []" :key="item.id" class="p-4 space-y-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold text-gray-800">{{ item.user?.name || '-' }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">{{ new Date(item.scanned_at).toLocaleString('id-ID') }}</p>
                            </div>
                            <span
                                :class="item.source_type === 'machine' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'"
                                class="px-2 py-0.5 rounded text-[10px] font-semibold"
                            >
                                {{ item.source_type === 'machine' ? 'Mesin' : 'GPS' }}
                            </span>
                        </div>

                        <div class="text-xs text-gray-600 space-y-1">
                            <p><span class="text-gray-400">Tipe:</span> <span class="font-bold uppercase">{{ item.scan_type }}</span></p>
                            <p><span class="text-gray-400">Perangkat:</span> {{ item.device_name || '-' }}</p>
                            <p v-if="item.source_type === 'gps' && item.latitude">
                                <span class="text-gray-400">Jarak:</span> {{ item.distance_meters ? Math.round(item.distance_meters) + ' m' : '-' }}
                                <span :class="item.is_in_radius ? 'text-green-600' : 'text-red-600'" class="ml-1 font-semibold">
                                    ({{ item.is_in_radius ? 'Valid' : 'Luar Radius' }})
                                </span>
                            </p>
                        </div>
                    </div>

                    <div v-if="!props.logs?.data || props.logs.data.length === 0" class="p-6 text-center text-xs text-gray-400">
                        Tidak ada data log yang ditemukan.
                    </div>
                </div>

            </div>

        </div>
    </AuthenticatedLayout>
</template>
