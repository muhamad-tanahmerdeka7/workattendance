<script setup>
import { ref, watch, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    materials: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");
const category = ref(props.filters.category || "all");
const storage = ref(props.filters.storage || "all");

// State untuk Modal Tambah/Edit Master
const showModal = ref(false);
const isEditing = ref(false);
const editId = ref(null);

// State untuk Modal STOK MASUK
const showInboundModal = ref(false);
const inboundItemName = ref("");
const inboundItemId = ref(null);
const inboundUnit = ref("");

const form = useForm({
    item_code: "",
    name: "",
    category: "meat",
    unit: "PAC",
    storage_location: "Logistik Frozen",
    condition_note: "",
    current_stock: 0,
    minimum_stock: 20,
});

// Form khusus untuk memproses Stok Masuk
const inboundForm = useForm({
    qty_masuk: "",
    tanggal_masuk: new Date().toISOString().slice(0, 10), // Default hari ini
    no_dokumen: "", // Untuk nomor DO / PO
    lokasi_simpan: "", // TAMBAHAN: State untuk lokasi aktual
    suhu_masuk: "", // TAMBAHAN: State untuk suhu kedatangan
});

// State untuk Modal Riwayat (History)
const showHistoryModal = ref(false);
const historyItemName = ref("");
const historyLogs = ref([]);
const historyUnit = ref("");

const openHistoryModal = (item) => {
    historyItemName.value = item.name;
    historyUnit.value = item.unit;
    historyLogs.value = item.inbound_logs || []; // Ambil dari relasi backend
    showHistoryModal.value = true;
};
// Watcher untuk memantau perubahan filter dan melakukan request ke server
watch([search, category, storage], ([q, cat, stor]) => {
    router.get(
        route("raw-materials.index"),
        { search: q, category: cat, storage: stor },
        { preserveState: true, replace: true },
    );
});

// Ringkasan Statistik
const totalItems = computed(() => props.materials.total || 0);
const lowStockCount = computed(() => {
    return props.materials.data.filter(
        (i) => Number(i.current_stock) <= Number(i.minimum_stock),
    ).length;
});
const frozenCount = computed(() => {
    return props.materials.data.filter((i) =>
        i.storage_location?.toLowerCase().includes("frozen"),
    ).length;
});
const dryCount = computed(() => {
    return props.materials.data.filter((i) =>
        i.storage_location?.toLowerCase().includes("dry"),
    ).length;
});

// --- FUNGSI MASTER DATA ---
const openAddModal = () => {
    isEditing.value = false;
    editId.value = null;
    form.reset();
    showModal.value = true;
};

const openEditModal = (item) => {
    isEditing.value = true;
    editId.value = item.id;
    form.item_code = item.item_code;
    form.name = item.name;
    form.category = item.category;
    form.unit = item.unit;
    form.storage_location = item.storage_location;
    form.condition_note = item.condition_note || "";
    form.current_stock = Number(item.current_stock);
    form.minimum_stock = Number(item.minimum_stock);
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route("raw-materials.update", editId.value), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route("raw-materials.store"), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteItem = (id) => {
    if (confirm("Hapus bahan baku ini dari katalog?")) {
        router.delete(route("raw-materials.destroy", id));
    }
};

// --- FUNGSI STOK MASUK ---
const openInboundModal = (item) => {
    inboundItemId.value = item.id;
    inboundItemName.value = item.name;
    inboundUnit.value = item.unit;
    inboundForm.reset();
    inboundForm.tanggal_masuk = new Date().toISOString().slice(0, 10);
    showInboundModal.value = true;
};

const submitInbound = () => {
    // Akan melakukan POST ke route laravel khusus penambahan stok
    inboundForm.post(route("raw-materials.add-stock", inboundItemId.value), {
        preserveScroll: true,
        onSuccess: () => {
            showInboundModal.value = false;
            inboundForm.reset();
            alert("Stok Fisik berhasil ditambahkan!");
        },
    });
};

const categoryBadge = (cat) => {
    const map = {
        meat: "bg-rose-50 text-rose-700 border-rose-200",
        rice: "bg-amber-50 text-amber-700 border-amber-200",
        seasoning: "bg-orange-50 text-orange-700 border-orange-200",
        vegetable: "bg-emerald-50 text-emerald-700 border-emerald-200",
        packaging: "bg-sky-50 text-sky-700 border-sky-200",
    };
    return map[cat] || "bg-slate-50 text-slate-700 border-slate-200";
};
</script>

<template>
    <Head title="Katalog Bahan Baku & Kemasan" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2 class="text-xl font-bold leading-tight text-slate-800">
                        Katalog Bahan Baku & Material Kemasan
                    </h2>
                    <p class="mt-1 text-xs text-slate-500">
                        Monitoring inventori stok bahan baku mentah, bumbu
                        racik, dan logistik kemasan.
                    </p>
                </div>
                <button
                    @click="openAddModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition active:scale-95"
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
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    Tambah Bahan Baku Baru
                </button>
            </div>
        </template>

        <div class="px-4 py-6 mx-auto space-y-6 sm:px-6 lg:px-8 max-w-7xl">
            <!-- 4 KARTU STATISTIK RINGKAS -->
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div
                    class="p-4 bg-white border shadow-sm rounded-2xl border-slate-100"
                >
                    <span
                        class="text-[11px] font-bold text-slate-400 uppercase tracking-wider"
                        >Total Item Bahan</span
                    >
                    <div class="mt-1 text-2xl font-black text-slate-800">
                        {{ totalItems }}
                    </div>
                </div>
                <div
                    class="p-4 bg-white border shadow-sm rounded-2xl border-slate-100"
                >
                    <span
                        class="text-[11px] font-bold text-rose-500 uppercase tracking-wider"
                        >Perlu Restock</span
                    >
                    <div class="mt-1 text-2xl font-black text-rose-600">
                        {{ lowStockCount }} Item
                    </div>
                </div>
                <div
                    class="p-4 bg-white border shadow-sm rounded-2xl border-slate-100"
                >
                    <span
                        class="text-[11px] font-bold text-sky-500 uppercase tracking-wider"
                        >Logistik Frozen</span
                    >
                    <div class="mt-1 text-2xl font-black text-sky-700">
                        {{ frozenCount }} Item
                    </div>
                </div>
                <div
                    class="p-4 bg-white border shadow-sm rounded-2xl border-slate-100"
                >
                    <span
                        class="text-[11px] font-bold text-amber-500 uppercase tracking-wider"
                        >Logistik Dry</span
                    >
                    <div class="mt-1 text-2xl font-black text-amber-700">
                        {{ dryCount }} Item
                    </div>
                </div>
            </div>

            <!-- PENCARIAN & FILTER -->
            <div
                class="flex flex-col items-center gap-4 p-4 bg-white border shadow-sm rounded-2xl border-slate-100 md:flex-row"
            >
                <div class="relative w-full md:flex-1">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari kode item atau nama bahan baku..."
                        class="w-full px-4 py-2 text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50"
                    />
                </div>
                <div class="w-full md:w-56">
                    <select
                        v-model="category"
                        class="w-full px-3 py-2 text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50"
                    >
                        <option value="all">Semua Kategori</option>
                        <option value="meat">Daging / Protein</option>
                        <option value="rice">Beras / Nasi</option>
                        <option value="seasoning">Bumbu & Saus</option>
                        <option value="vegetable">Sayuran</option>
                        <option value="packaging">Kemasan (Packaging)</option>
                    </select>
                </div>
                <div class="w-full md:w-56">
                    <select
                        v-model="storage"
                        class="w-full px-3 py-2 text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50"
                    >
                        <option value="all">Semua Standar Penyimpanan</option>
                        <option value="Frozen">Logistik Frozen</option>
                        <option value="Dry">Logistik Dry</option>
                        <option value="Beras">Logistik Beras</option>
                        <option value="Seasoning">Logistik Seasoning</option>
                    </select>
                </div>
            </div>

            <!-- TABEL DAFTAR BAHAN BAKU -->
            <div
                class="overflow-hidden bg-white border shadow-sm rounded-2xl border-slate-100"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-slate-600">
                        <thead
                            class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100"
                        >
                            <tr>
                                <th class="p-4">Kode Item</th>
                                <th class="p-4">Nama Bahan / Material</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Standar Penyimpanan</th>
                                <th class="p-4">Standar Suhu & Penanganan</th>
                                <th class="p-4 text-center text-blue-600">
                                    Stok Masuk
                                </th>
                                <th class="p-4 text-right">Stok Fisik</th>
                                <th class="p-4 text-right">Min. Stok</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="font-medium divide-y divide-slate-100">
                            <tr
                                v-for="item in materials.data"
                                :key="item.id"
                                class="transition hover:bg-slate-50/70"
                            >
                                <td
                                    class="p-4 font-mono font-bold text-blue-600"
                                >
                                    {{ item.item_code }}
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-slate-800">
                                        {{ item.name }}
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span
                                        :class="[
                                            'inline-flex px-2 py-0.5 rounded-lg text-[10px] font-bold border uppercase',
                                            categoryBadge(item.category),
                                        ]"
                                    >
                                        {{ item.category }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700"
                                    >
                                        {{ item.storage_location }}
                                    </span>
                                </td>
                                <td class="p-4 text-slate-600">
                                    {{
                                        item.condition_note
                                            ? item.condition_note
                                            : "-"
                                    }}
                                </td>
                                <!-- TOMBOL TAMBAH STOK MASUK -->
                                <td class="p-4 text-center">
                                    <div
                                        class="flex flex-col items-center gap-1.5"
                                    >
                                        <button
                                            @click="openInboundModal(item)"
                                            class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white transition shadow-sm border border-blue-200"
                                            title="Catat barang masuk"
                                        >
                                            + Masuk
                                        </button>
                                        <!-- Tombol Lihat Riwayat -->
                                        <button
                                            @click="openHistoryModal(item)"
                                            class="text-[9px] font-semibold text-slate-400 hover:text-blue-600 underline"
                                        >
                                            Lihat Riwayat
                                        </button>
                                    </div>
                                </td>

                                <td
                                    class="p-4 font-mono text-sm font-bold text-right text-slate-800"
                                >
                                    {{ Number(item.current_stock) }}
                                    <span
                                        class="text-xs font-semibold text-slate-500"
                                        >{{ item.unit }}</span
                                    >
                                </td>
                                <td
                                    class="p-4 font-mono text-right text-slate-400"
                                >
                                    {{ Number(item.minimum_stock) }}
                                    {{ item.unit }}
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        v-if="
                                            Number(item.current_stock) <=
                                            Number(item.minimum_stock)
                                        "
                                        class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200"
                                    >
                                        Stok Rendah
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200"
                                    >
                                        Aman
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <div
                                        class="flex items-center justify-center gap-2"
                                    >
                                        <button
                                            @click="openEditModal(item)"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition"
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
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteItem(item.id)"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition"
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
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL FORM MASTER BAHAN BAKU -->
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
            >
                <div
                    class="w-full max-w-lg overflow-hidden bg-white border shadow-2xl rounded-3xl border-slate-100"
                >
                    <div
                        class="flex items-center justify-between p-5 border-b border-slate-100"
                    >
                        <h3 class="text-sm font-bold text-slate-800">
                            {{
                                isEditing
                                    ? "Edit Data Bahan Baku"
                                    : "Tambah Bahan Baku Baru"
                            }}
                        </h3>
                        <button
                            @click="showModal = false"
                            class="text-slate-400 hover:text-slate-600"
                        >
                            ✕
                        </button>
                    </div>
                    <form
                        @submit.prevent="submitForm"
                        class="p-6 space-y-4 text-xs"
                    >
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block mb-1 font-bold text-slate-700"
                                    >Kode Item</label
                                >
                                <input
                                    v-model="form.item_code"
                                    type="text"
                                    placeholder="Misal: C-00366"
                                    class="w-full text-xs rounded-xl border-slate-200"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="block mb-1 font-bold text-slate-700"
                                    >Satuan / Unit</label
                                >
                                <input
                                    v-model="form.unit"
                                    type="text"
                                    placeholder="G, PAC, PC, ROL"
                                    class="w-full text-xs rounded-xl border-slate-200"
                                    required
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 font-bold text-slate-700"
                                >Nama Bahan Baku / Material</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Nama item..."
                                class="w-full text-xs rounded-xl border-slate-200"
                                required
                            />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block mb-1 font-bold text-slate-700"
                                    >Kategori</label
                                >
                                <select
                                    v-model="form.category"
                                    class="w-full text-xs rounded-xl border-slate-200"
                                >
                                    <option value="meat">
                                        Daging / Protein
                                    </option>
                                    <option value="rice">Beras / Nasi</option>
                                    <option value="seasoning">
                                        Bumbu & Saus
                                    </option>
                                    <option value="vegetable">Sayuran</option>
                                    <option value="packaging">Kemasan</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block mb-1 font-bold text-slate-700"
                                    >Standar Penyimpanan</label
                                >
                                <input
                                    v-model="form.storage_location"
                                    type="text"
                                    placeholder="Logistik Frozen / Dry"
                                    class="w-full text-xs rounded-xl border-slate-200"
                                    required
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 font-bold text-slate-700"
                                >Standar Suhu & Penanganan (Opsional)</label
                            >
                            <input
                                v-model="form.condition_note"
                                type="text"
                                placeholder="Misal: Thawing (-6 C) - (-2 C)"
                                class="w-full text-xs rounded-xl border-slate-200"
                            />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block mb-1 font-bold text-slate-700"
                                    >Stok Fisik Awal</label
                                >
                                <input
                                    v-model="form.current_stock"
                                    type="number"
                                    step="0.01"
                                    class="w-full text-xs rounded-xl border-slate-200"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="block mb-1 font-bold text-slate-700"
                                    >Batas Minimum Stok</label
                                >
                                <input
                                    v-model="form.minimum_stock"
                                    type="number"
                                    step="0.01"
                                    class="w-full text-xs rounded-xl border-slate-200"
                                    required
                                />
                            </div>
                        </div>
                        <div
                            class="flex justify-end gap-3 pt-4 border-t border-slate-100"
                        >
                            <button
                                type="button"
                                @click="showModal = false"
                                class="px-4 py-2 font-bold rounded-xl bg-slate-100 text-slate-600"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700"
                            >
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL KHUSUS STOK MASUK -->
            <div
                v-if="showInboundModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
            >
                <div
                    class="w-full max-w-sm overflow-hidden bg-white border shadow-2xl rounded-3xl border-slate-100"
                >
                    <div
                        class="flex items-center justify-between p-5 border-b border-blue-100 bg-blue-50"
                    >
                        <div>
                            <h3 class="font-bold text-blue-800">
                                Catat Stok Masuk
                            </h3>
                            <p class="text-[11px] text-blue-600 mt-0.5">
                                {{ inboundItemName }}
                            </p>
                        </div>
                        <button
                            @click="showInboundModal = false"
                            class="text-blue-400 hover:text-blue-600"
                        >
                            ✕
                        </button>
                    </div>

                    <form
                        @submit.prevent="submitInbound"
                        class="p-6 space-y-4 text-xs"
                    >
                        <div>
                            <label class="block mb-1 font-bold text-slate-700"
                                >Kuantitas Masuk ({{ inboundUnit }})</label
                            >
                            <input
                                v-model="inboundForm.qty_masuk"
                                type="number"
                                step="0.01"
                                placeholder="Contoh: 150"
                                class="w-full text-sm font-bold text-blue-600 rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                                required
                            />
                        </div>

                        <div>
                            <label class="block mb-1 font-bold text-slate-700"
                                >Tanggal Terima</label
                            >
                            <input
                                v-model="inboundForm.tanggal_masuk"
                                type="date"
                                class="w-full text-xs rounded-xl border-slate-200"
                                required
                            />
                        </div>

                        <!-- LOKASI AKTUAL -->
                        <div>
                            <label class="block mb-1 font-bold text-slate-700"
                                >Lokasi Penempatan Aktual</label
                            >
                            <select
                                v-model="inboundForm.lokasi_simpan"
                                class="w-full text-xs rounded-xl border-slate-200"
                                required
                            >
                                <option value="" disabled>
                                    -- Pilih Lokasi Penyimpanan --
                                </option>
                                <option value="Ruang 1 Rawmat - Mesin (Blast)">
                                    Ruang 1 Rawmat - Mesin (Blast)
                                </option>
                                <option value="Ruang 1 Rawmat - Mesin Chiller">
                                    Ruang 1 Rawmat - Mesin Chiller
                                </option>
                                <option value="Ruang Proses - Mesin Blazt">
                                    Ruang Proses - Mesin Blazt
                                </option>
                                <option value="Ruang Proses - Mesin Chiller">
                                    Ruang Proses - Mesin Chiller
                                </option>
                                <option value="Ruang Assembling - Mesin Blazt">
                                    Ruang Assembling - Mesin Blazt
                                </option>
                                <option
                                    value="Ruang Assembling - Mesin Chiller"
                                >
                                    Ruang Assembling - Mesin Chiller
                                </option>
                            </select>
                        </div>

                        <!-- SUHU KEDATANGAN -->
                        <div>
                            <label class="block mb-1 font-bold text-slate-700"
                                >Suhu Kedatangan (°C)</label
                            >
                            <div class="relative shadow-sm rounded-xl">
                                <input
                                    v-model="inboundForm.suhu_masuk"
                                    type="number"
                                    step="0.1"
                                    placeholder="Misal: -18.5"
                                    class="w-full pr-10 text-xs rounded-xl border-slate-200"
                                    required
                                />
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                                >
                                    <span class="text-xs text-slate-400"
                                        >°C</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 font-bold text-slate-700"
                                >Nomor DO / Vendor (Opsional)</label
                            >
                            <input
                                v-model="inboundForm.no_dokumen"
                                type="text"
                                placeholder="Nomor Delivery Note / PO"
                                class="w-full text-xs rounded-xl border-slate-200"
                            />
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button
                                type="button"
                                @click="showInboundModal = false"
                                class="px-4 py-2 font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="inboundForm.processing"
                                class="flex items-center gap-2 px-4 py-2 font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700"
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
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>
                                Tambah ke Stok Fisik
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL RIWAYAT STOK MASUK -->
            <div
                v-if="showHistoryModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
            >
                <!-- DIPERLEBAR: max-w-lg diubah menjadi max-w-3xl agar tabel muat -->
                <div
                    class="flex flex-col w-full max-w-3xl overflow-hidden bg-white border shadow-2xl rounded-3xl border-slate-100 max-h-[80vh]"
                >
                    <div
                        class="flex items-center justify-between p-5 border-b shrink-0 border-slate-100 bg-slate-50"
                    >
                        <div>
                            <h3 class="font-bold text-slate-800">
                                Riwayat Stok Masuk
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                {{ historyItemName }}
                            </p>
                        </div>
                        <button
                            @click="showHistoryModal = false"
                            class="p-1 bg-white border rounded-lg text-slate-400 hover:text-slate-600"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="flex-1 p-5 overflow-y-auto">
                        <table class="w-full text-xs text-left text-slate-600">
                            <thead
                                class="font-bold uppercase rounded-t-lg bg-slate-100 text-[10px] text-slate-500"
                            >
                                <tr>
                                    <th class="p-3 rounded-tl-lg">
                                        Tanggal Terima
                                    </th>
                                    <th class="p-3 text-right">Qty Masuk</th>
                                    <!-- TAMBAHAN: Kolom Lokasi dan Suhu -->
                                    <th class="p-3">Lokasi Penempatan</th>
                                    <th class="p-3">Suhu</th>
                                    <th class="p-3 rounded-tr-lg">
                                        No. DO / Vendor
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="border-b divide-y divide-slate-100 border-slate-100"
                            >
                                <tr v-if="historyLogs.length === 0">
                                    <!-- UBAH COLSPAN: 3 menjadi 5 -->
                                    <td
                                        colspan="5"
                                        class="p-4 italic font-medium text-center text-slate-400"
                                    >
                                        Belum ada riwayat stok masuk untuk bahan
                                        baku ini.
                                    </td>
                                </tr>
                                <tr
                                    v-for="log in historyLogs"
                                    :key="log.id"
                                    class="hover:bg-slate-50"
                                >
                                    <td class="p-3 font-medium text-slate-800">
                                        {{
                                            new Date(
                                                log.tanggal_masuk,
                                            ).toLocaleDateString("id-ID", {
                                                day: "numeric",
                                                month: "short",
                                                year: "numeric",
                                            })
                                        }}
                                    </td>
                                    <td
                                        class="p-3 font-mono font-bold text-right text-blue-600"
                                    >
                                        +{{ Number(log.qty_masuk) }}
                                        <span
                                            class="text-[10px] text-slate-400 font-sans"
                                            >{{ historyUnit }}</span
                                        >
                                    </td>
                                    <!-- TAMBAHAN: Menampilkan Data Lokasi dan Suhu -->
                                    <td
                                        class="p-3 text-[11px] font-medium text-slate-600"
                                    >
                                        {{
                                            log.lokasi_simpan
                                                ? log.lokasi_simpan
                                                : "-"
                                        }}
                                    </td>
                                    <td
                                        class="p-3 font-mono font-bold text-slate-700"
                                    >
                                        {{
                                            log.suhu_masuk !== null &&
                                            log.suhu_masuk !== undefined
                                                ? log.suhu_masuk + " °C"
                                                : "-"
                                        }}
                                    </td>
                                    <td class="p-3 font-medium text-slate-600">
                                        <span
                                            v-if="log.no_dokumen"
                                            class="px-2 py-0.5 rounded bg-slate-100 border text-[10px]"
                                            >{{ log.no_dokumen }}</span
                                        >
                                        <span v-else class="text-slate-300"
                                            >-</span
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="flex justify-end p-4 bg-white border-t shrink-0 border-slate-100"
                    >
                        <button
                            @click="showHistoryModal = false"
                            class="px-5 py-2 text-xs font-bold transition rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700"
                        >
                            Tutup Jendela
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
