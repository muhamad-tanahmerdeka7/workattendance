<template>
    <Head title="Bahan Keluar / Produksi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Riwayat Bahan Keluar / Produksi
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    v-if="$page.props.flash?.success"
                    class="p-4 mb-4 font-medium text-green-700 bg-green-100 rounded-lg shadow-sm"
                >
                    {{ $page.props.flash.success }}
                </div>

                <div
                    class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div
                        class="flex flex-col items-center justify-between gap-4 mb-6 md:flex-row"
                    >
                        <!-- Filter Pencarian -->
                        <div class="flex flex-wrap w-full gap-2 md:w-auto">
                            <input
                                v-model="searchQuery"
                                @keyup.enter="search"
                                type="text"
                                placeholder="Cari barang, kode, atau DO..."
                                class="text-sm border-gray-300 rounded-md shadow-sm"
                            />
                            <input
                                v-model="startDate"
                                type="date"
                                class="text-sm border-gray-300 rounded-md shadow-sm"
                            />
                            <input
                                v-model="endDate"
                                type="date"
                                class="text-sm border-gray-300 rounded-md shadow-sm"
                            />
                            <button
                                @click="search"
                                class="px-4 py-2 text-sm text-white transition bg-gray-800 rounded-md hover:bg-gray-700"
                            >
                                Filter
                            </button>
                            <button
                                @click="resetSearch"
                                class="px-4 py-2 text-sm text-gray-800 transition bg-gray-200 rounded-md hover:bg-gray-300"
                            >
                                Reset
                            </button>
                        </div>

                        <!-- Grup Tombol Kanan (PDF & Tambah) -->
                        <div
                            class="flex flex-col w-full gap-2 md:flex-row md:w-auto"
                        >
                            <!-- Tombol Print PDF -->
                            <a
                                :href="
                                    route('outbounds.print-pdf', {
                                        start_date: startDate,
                                        end_date: endDate,
                                    })
                                "
                                target="_blank"
                                class="flex items-center justify-center w-full gap-2 px-4 py-2 font-bold text-white transition bg-red-600 rounded-md shadow-sm md:w-auto hover:bg-red-700"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    ></path>
                                </svg>
                                Print PDF
                            </a>

                            <!-- Tombol Tambah Bahan Keluar -->
                            <button
                                @click="showModal = true"
                                class="w-full px-4 py-2 font-bold text-white transition bg-blue-600 rounded-md shadow-sm md:w-auto hover:bg-blue-700"
                            >
                                + Tambah Bahan Keluar
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Data -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-100"
                            >
                                <tr>
                                    <th class="px-4 py-3">Tanggal Keluar</th>
                                    <th class="px-4 py-3">No. DO / Surat</th>
                                    <th class="px-4 py-3">Kode Item</th>
                                    <th class="px-4 py-3">Nama Material</th>
                                    <th class="px-4 py-3 text-center">
                                        Kuantitas Keluar
                                    </th>
                                    <th class="px-4 py-3 text-center">
                                        Sisa Stok
                                    </th>
                                    <th class="px-4 py-3">Lokasi Tujuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in outbounds?.data || []"
                                    :key="item.id"
                                    class="border-b hover:bg-gray-50"
                                >
                                    <td class="px-4 py-3">
                                        {{ item.tanggal_keluar }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ item.no_dokumen || "-" }}
                                    </td>
                                    <td
                                        class="px-4 py-3 font-medium text-blue-600"
                                    >
                                        {{
                                            item.raw_material?.item_code ||
                                            "N/A"
                                        }}
                                    </td>
                                    <td
                                        class="px-4 py-3 font-semibold text-gray-800"
                                    >
                                        {{
                                            item.raw_material?.name ||
                                            "Terhapus"
                                        }}
                                    </td>
                                    <td
                                        class="px-4 py-3 font-bold text-center text-red-600"
                                    >
                                        -{{ Number(item.qty_keluar) }}
                                        <span
                                            class="text-xs font-normal text-gray-500"
                                            >{{ item.raw_material?.unit }}</span
                                        >
                                    </td>
                                    <td
                                        class="px-4 py-3 font-bold text-center text-gray-800"
                                    >
                                        {{ Number(item.sisa_stok) }}
                                        <span
                                            class="text-xs font-normal text-gray-500"
                                            >{{ item.raw_material?.unit }}</span
                                        >
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 border border-gray-200 rounded"
                                        >
                                            {{ item.lokasi_tujuan }}
                                        </span>
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        !outbounds?.data ||
                                        outbounds.data.length === 0
                                    "
                                >
                                    <td
                                        colspan="7"
                                        class="px-4 py-8 text-center text-gray-500"
                                    >
                                        Belum ada data riwayat bahan keluar.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="outbounds?.links"
                        class="flex flex-wrap gap-1 mt-6"
                    >
                        <Link
                            v-for="link in outbounds.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            v-html="link.label"
                            class="px-3 py-1 text-sm transition-colors border rounded"
                            :class="{
                                'bg-blue-600 text-white border-blue-600':
                                    link.active,
                                'text-gray-500 hover:bg-gray-50':
                                    !link.active && link.url,
                                'text-gray-300 cursor-not-allowed': !link.url,
                            }"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form Tambah Bahan Keluar -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
        >
            <div class="w-full max-w-lg p-6 bg-white shadow-2xl rounded-xl">
                <h3 class="pb-2 mb-4 text-lg font-bold text-gray-900 border-b">
                    Input Bahan Keluar (Produksi)
                </h3>

                <form @submit.prevent="submitForm">
                    <!-- Pilih Material -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700"
                            >Pilih Bahan Baku
                            <span class="text-red-500">*</span></label
                        >
                        <select
                            v-model="form.raw_material_id"
                            class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                            <option value="" disabled>
                                -- Pilih Bahan Baku --
                            </option>
                            <option
                                v-for="material in raw_materials || []"
                                :key="material.id"
                                :value="material.id"
                            >
                                {{ material.item_code }} - {{ material.name }}
                            </option>
                        </select>
                        <div
                            v-if="form.errors.raw_material_id"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.raw_material_id }}
                        </div>

                        <!-- Info Stok Terkini (SUDAH DIPERBAIKI) -->
                        <div
                            v-if="selectedMaterial"
                            class="flex justify-between items-center p-2 mt-2 text-sm text-blue-800 border border-blue-100 rounded-md bg-blue-50"
                        >
                            <span>Stok Tersedia Saat Ini:</span>
                            <span class="font-bold text-base">
                                {{ Number(selectedMaterial.current_stock) }} {{ selectedMaterial.unit }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <!-- Qty Keluar -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Kuantitas
                                <span class="text-red-500">*</span></label
                            >
                            <input
                                v-model="form.qty_keluar"
                                type="number"
                                step="0.01"
                                min="0.1"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            />
                            <div
                                v-if="form.errors.qty_keluar"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.qty_keluar }}
                            </div>
                        </div>

                        <!-- Tanggal Keluar -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Tanggal Keluar
                                <span class="text-red-500">*</span></label
                            >
                            <input
                                v-model="form.tanggal_keluar"
                                type="date"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            />
                            <div
                                v-if="form.errors.tanggal_keluar"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.tanggal_keluar }}
                            </div>
                        </div>
                    </div>

                    <!-- Lokasi Tujuan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700"
                            >Lokasi Tujuan Produksi
                            <span class="text-red-500">*</span></label
                        >

                        <!-- Mode Select Dropdown -->
                        <div v-if="!isManualLocation">
                            <select
                                v-model="dropdownLocation"
                                @change="handleLocationChange"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                <option value="" disabled>
                                    -- Pilih Lokasi --
                                </option>
                                <option value="Proses">Proses</option>
                                <option value="Assembling">Assembling</option>
                                <option value="Pembekuan (Denso)">
                                    Pembekuan (Denso)
                                </option>
                                <option
                                    value="MANUAL_INPUT"
                                    class="font-bold text-blue-600 bg-blue-50"
                                >
                                    ➕ Ketik Lokasi Lain (Manual)...
                                </option>
                            </select>
                        </div>

                        <!-- Mode Input Manual -->
                        <div v-else class="flex items-center gap-2 mt-1">
                            <input
                                v-model="form.lokasi_tujuan"
                                type="text"
                                placeholder="Ketik lokasi baru di sini..."
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            />
                            <button
                                type="button"
                                @click="resetToDropdown"
                                class="px-3 py-2 text-sm font-medium text-gray-700 transition bg-gray-200 rounded-md hover:bg-gray-300 shrink-0"
                                title="Kembali pilih dari daftar"
                            >
                                ✕ Batal
                            </button>
                        </div>
                        <div
                            v-if="form.errors.lokasi_tujuan"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.lokasi_tujuan }}
                        </div>
                    </div>

                    <!-- No Dokumen -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700"
                            >No. Dokumen / DO (Opsional)</label
                        >
                        <input
                            v-model="form.no_dokumen"
                            type="text"
                            class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700"
                            >Keterangan (Opsional)</label
                        >
                        <textarea
                            v-model="form.keterangan"
                            rows="2"
                            class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end gap-3 pt-2">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 font-medium text-gray-700 transition bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 font-medium text-white transition bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50"
                        >
                            Simpan Pengeluaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    outbounds: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
    raw_materials: { type: Array, default: () => [] },
});

const searchQuery = ref(props.filters?.search || "");
const startDate = ref(props.filters?.start_date || "");
const endDate = ref(props.filters?.end_date || "");
const showModal = ref(false);

const isManualLocation = ref(false);
const dropdownLocation = ref("");

const form = useForm({
    raw_material_id: "",
    qty_keluar: "",
    tanggal_keluar: new Date().toISOString().split("T")[0],
    lokasi_tujuan: "",
    no_dokumen: "",
    keterangan: "",
});

const selectedMaterial = computed(() => {
    if (!form.raw_material_id || !props.raw_materials) return null;
    return props.raw_materials.find((m) => m.id === form.raw_material_id);
});

const handleLocationChange = () => {
    if (dropdownLocation.value === "MANUAL_INPUT") {
        isManualLocation.value = true;
        form.lokasi_tujuan = "";
    } else {
        form.lokasi_tujuan = dropdownLocation.value;
    }
};

const resetToDropdown = () => {
    isManualLocation.value = false;
    dropdownLocation.value = "";
    form.lokasi_tujuan = "";
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    isManualLocation.value = false;
    dropdownLocation.value = "";
};

const submitForm = () => {
    form.post(route("outbounds.store"), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
    });
};

const search = () => {
    router.get(
        route("outbounds.index"),
        {
            search: searchQuery.value,
            start_date: startDate.value,
            end_date: endDate.value,
        },
        { preserveState: true, replace: true },
    );
};

const resetSearch = () => {
    searchQuery.value = "";
    startDate.value = "";
    endDate.value = "";
    search();
};
</script>
