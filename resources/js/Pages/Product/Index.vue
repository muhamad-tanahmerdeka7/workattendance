<script setup>
import { ref, watch, computed, nextTick } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    products: Object,
    rawMaterials: Array,
    filters: Object,
});

const search = ref(props.filters.search || "");
const selectedProduct = ref(null);
const showRecipeModal = ref(false);
const showFormModal = ref(false);
const isEditing = ref(false);
const editingProductId = ref(null);
const skuInputRef = ref(null);

watch(search, (value) => {
    router.get(
        route("products.index"),
        { search: value },
        { preserveState: true, replace: true },
    );
});

// DAFTAR PRESET 8 MENU BENTO STANDAR KITCHEN
const bentoPresets = [
    {
        sku: "FG-CKN-DON",
        name: "RTH F.F. SLICE CHICKEN DON",
        price: 35000,
        desc: "Bento donburi ayam iris gurih dengan garlic rice dan saus mucyu.",
    },
    {
        sku: "FG-HMB-KTC",
        name: "FG RTH HAMBAGU KETCHUP RICE",
        price: 38000,
        desc: "Bento daging sapi hambagu cincang juicy dengan ketchup rice dan saus teriyaki.",
    },
    {
        sku: "FG-EBI-FRY",
        name: "RTH EBI FURAI BENTO SET",
        price: 42000,
        desc: "Bento udang goreng tepung renyah dengan nasi jepang dan saus tartar.",
    },
    {
        sku: "FG-CKN-KAT",
        name: "RTH CHICKEN KATSU BENTO SET",
        price: 35000,
        desc: "Bento ayam katsu fillet renyah dengan saus tonkatsu spesial.",
    },
    {
        sku: "FG-BEEF-YAK",
        name: "RTH BEEF YAKINIKU BENTO SET",
        price: 45000,
        desc: "Bento irisan daging sapi saus yakiniku manis gurih dengan bawang bombay.",
    },
    {
        sku: "FG-BEEF-TER",
        name: "RTH BEEF TERIYAKI BENTO SET",
        price: 45000,
        desc: "Bento irisan daging sapi saus teriyaki khas dapur kitchen.",
    },
    {
        sku: "FG-TORI-BAL",
        name: "RTH TORI BALL BENTO SET",
        price: 33000,
        desc: "Bento bola daging ayam olahan bumbu rempah jepang.",
    },
    {
        sku: "FG-EGG-ROL",
        name: "RTH EGG CHICKEN ROLL BENTO SET",
        price: 36000,
        desc: "Bento olahan daging ayam gulung telur dadar khas bento.",
    },
];

const selectedPreset = ref("");

// ============ DAFTAR SATUAN STANDAR (BISA DIKETIK MANUAL JUGA) ============
const standardUnits = [
    "PAC", "PCS", "G", "KG", "ML", "L",
    "BOX", "PACK", "BTL", "SACHET", "TRAY", "IKAT", "UNIT"
];

// ============ KATEGORI TABEL (REAKTIF, BISA DITAMBAH) ============
const tableGroupOptions = ref([
    "Tabel 1. Komposisi Utama / Lauk",
    "Tabel 2. Komposisi Nasi",
    "Tabel 3. Komposisi Saus",
    "Tabel 4. Komposisi Sayuran",
    "Tabel 5. Kemasan",
]);

const showNewGroupInput = ref(false);
const newGroupName = ref("");
const newGroupInputRef = ref(null);

// Buka form tambah kategori baru + fokus otomatis
const startAddNewGroup = () => {
    showNewGroupInput.value = true;
    nextTick(() => {
        if (newGroupInputRef.value) newGroupInputRef.value.focus();
    });
};

// Simpan kategori baru ke daftar opsi
const addNewGroup = () => {
    const name = newGroupName.value.trim();
    if (!name) return;
    if (!tableGroupOptions.value.includes(name)) {
        tableGroupOptions.value.push(name);
    }
    newGroupName.value = "";
    showNewGroupInput.value = false;
};

const cancelAddNewGroup = () => {
    newGroupName.value = "";
    showNewGroupInput.value = false;
};

// ============ FORM PRODUK ============
const form = useForm({
    item_code: "",
    name: "",
    line_production: "Plant 8 - Kitchen",
    batch_yield: 80,
    unit: "PAC",
    selling_price: 35000,
    description: "",
    recipes: [],
});

// AUTOCOMPLETE SAAT PRESET DIPILIH
const onPresetSelect = (event) => {
    const sku = event.target.value;
    selectedPreset.value = sku;

    if (sku === "custom") {
        form.item_code = "";
        form.name = "";
        form.description = "";
        nextTick(() => {
            if (skuInputRef.value) skuInputRef.value.focus();
        });
        return;
    }

    const item = bentoPresets.find((p) => p.sku === sku);
    if (item) {
        form.item_code = item.sku;
        form.name = item.name;
        form.selling_price = item.price;
        form.description = item.desc;
        form.line_production = "Plant 8 - Kitchen";
        form.batch_yield = 80;
        form.unit = "PAC";
    }
};

const addRecipeRow = () => {
    form.recipes.push({
        raw_material_id: "",
        item_code: "",
        table_group: tableGroupOptions.value[0],
        quantity: 1,
        unit: "",
        storage_location: "",
    });
};

const removeRecipeRow = (index) => {
    form.recipes.splice(index, 1);
};

// AUTOCOMPLETE BAHAN
const onMaterialChange = (index, selectedId) => {
    const item = props.rawMaterials.find((m) => m.id === Number(selectedId));
    if (item) {
        form.recipes[index].raw_material_id = item.id;
        form.recipes[index].item_code = item.item_code;
        form.recipes[index].unit = item.unit; // Otomatis terisi dari master data
        form.recipes[index].storage_location = item.storage_location;
    } else {
        form.recipes[index].raw_material_id = "";
        form.recipes[index].item_code = "";
        form.recipes[index].unit = "";
        form.recipes[index].storage_location = "";
    }
};

// BUKA MODAL TAMBAH
const openCreateModal = () => {
    isEditing.value = false;
    editingProductId.value = null;
    form.reset();
    form.clearErrors();
    selectedPreset.value = "custom";
    form.recipes = [];
    addRecipeRow();
    showFormModal.value = true;
    nextTick(() => {
        if (skuInputRef.value) skuInputRef.value.focus();
    });
};

// BUKA MODAL EDIT - PENTING: JANGAN HILANGKAN KOMPOSISI LAMA
const openEditModal = (product) => {
    isEditing.value = true;
    editingProductId.value = product.id;
    form.clearErrors();

    const presetExists = bentoPresets.some((p) => p.sku === product.item_code);
    selectedPreset.value = presetExists ? product.item_code : "custom";

    form.item_code = product.item_code;
    form.name = product.name;
    form.line_production = product.line_production;
    form.batch_yield = product.batch_yield;
    form.unit = product.unit || "PAC";
    form.selling_price = product.selling_price;
    form.description = product.description || "";

    // LANGKAH KRITIS: Pastikan semua table_group dari resep lama
    // masuk ke daftar opsi, agar tidak ada yang hilang saat ditampilkan.
    if (product.recipes && product.recipes.length > 0) {
        product.recipes.forEach((r) => {
            const grp = r.table_group;
            if (grp && !tableGroupOptions.value.includes(grp)) {
                tableGroupOptions.value.push(grp);
            }
        });

        form.recipes = product.recipes.map((r) => ({
            raw_material_id: r.raw_material_id,
            item_code: r.raw_material?.item_code || "",
            table_group: r.table_group || tableGroupOptions.value[0],
            quantity: Number(r.quantity),
            unit: r.unit || r.raw_material?.unit || "",
            storage_location: r.raw_material?.storage_location || "",
        }));
    } else {
        form.recipes = [];
        addRecipeRow();
    }

    showRecipeModal.value = false;
    showFormModal.value = true;
};

// SIMPAN
const submitForm = () => {
    form.recipes = form.recipes.filter(
        (r) => r.raw_material_id !== "" && r.raw_material_id !== null,
    );

    if (isEditing.value) {
        form.put(route("products.update", editingProductId.value), {
            onSuccess: () => {
                showFormModal.value = false;
                form.reset();
                selectedPreset.value = "";
            },
        });
    } else {
        form.post(route("products.store"), {
            onSuccess: () => {
                showFormModal.value = false;
                form.reset();
                selectedPreset.value = "";
            },
        });
    }
};

const deleteProduct = (product) => {
    if (
        confirm(
            `Yakin ingin menghapus produk "${product.name}" (${product.item_code}) beserta seluruh susunan resep BOM-nya?`,
        )
    ) {
        router.delete(route("products.destroy", product.id), {
            preserveScroll: true,
        });
    }
};

const openRecipeModal = (product) => {
    selectedProduct.value = product;
    showRecipeModal.value = true;
};

const closeRecipeModal = () => {
    showRecipeModal.value = false;
    selectedProduct.value = null;
};

const groupedRecipes = computed(() => {
    if (!selectedProduct.value || !selectedProduct.value.recipes) return {};
    return selectedProduct.value.recipes.reduce((groups, item) => {
        const group = item.table_group || "Komposisi Tambahan";
        if (!groups[group]) groups[group] = [];
        groups[group].push(item);
        return groups;
    }, {});
});
</script>

<template>
    <Head title="List Produk & Resep (BOM)" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold leading-tight text-slate-800">
                    List Produk & Komposisi Finish Good
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    Katalog produk bento matang beserta susunan standar Bill of
                    Materials (BOM).
                </p>
            </div>
        </template>

        <!-- Datalist Global untuk Satuan (Agar tidak duplikat ID di dalam tabel) -->
        <datalist id="unitOptions">
            <option v-for="unit in standardUnits" :key="unit" :value="unit"></option>
        </datalist>

        <div class="px-4 py-6 mx-auto space-y-6 sm:px-6 lg:px-8 max-w-7xl">
            <!-- BAR PENCARIAN -->
            <div
                class="flex flex-col items-center justify-between gap-4 p-4 bg-white border shadow-sm rounded-2xl border-slate-100 md:flex-row"
            >
                <div class="relative w-full md:w-96">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama produk / kode SKU..."
                        class="w-full py-2 pl-10 pr-4 text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50"
                    />
                </div>

                <div class="flex items-center justify-between w-full gap-4 md:w-auto md:justify-end">
                    <span class="hidden text-xs font-medium text-slate-500 sm:inline">
                        Total:
                        <strong class="text-slate-800">{{ products.total }} Produk</strong>
                        (Standar 1 Resep = 80 Pack Bento)
                    </span>

                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white transition bg-blue-600 shadow-sm rounded-xl hover:bg-blue-700 active:scale-95 shrink-0"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Produk Baru
                    </button>
                </div>
            </div>

            <!-- TABEL PRODUK -->
            <div class="overflow-hidden bg-white border shadow-sm rounded-2xl border-slate-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left sm:text-sm text-slate-600">
                        <thead
                            class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100"
                        >
                            <tr>
                                <th class="p-4">Kode Item</th>
                                <th class="p-4">Nama Produk</th>
                                <th class="p-4">Line Produksi</th>
                                <th class="p-4 text-center">Batch Yield</th>
                                <th class="p-4 text-center">Total Bahan</th>
                                <th class="p-4 text-right">Harga Jual</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="font-medium divide-y divide-slate-100">
                            <tr
                                v-for="product in products.data"
                                :key="product.id"
                                class="transition hover:bg-slate-50/70"
                            >
                                <td class="p-4 font-mono font-bold text-blue-600">
                                    {{ product.item_code }}
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-slate-800">{{ product.name }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ product.description || "-" }}
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-100"
                                    >
                                        {{ product.line_production }}
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-center text-slate-700">
                                    {{ product.batch_yield }} {{ product.unit }}
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700"
                                    >
                                        {{ product.recipes ? product.recipes.length : 0 }} Item
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-right text-slate-800">
                                    Rp {{ Number(product.selling_price).toLocaleString("id-ID") }}
                                </td>
                                <td class="p-4 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <button
                                            @click="openRecipeModal(product)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white font-bold text-xs transition active:scale-95 shadow-sm"
                                            title="Lihat Resep (BOM)"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Lihat Resep (BOM)
                                        </button>

                                        <button
                                            @click="openEditModal(product)"
                                            class="p-1.5 rounded-xl bg-amber-50 hover:bg-amber-500 text-amber-600 hover:text-white border border-amber-200/60 transition active:scale-95 shadow-sm"
                                            title="Edit Produk & Resep"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <button
                                            @click="deleteProduct(product)"
                                            class="p-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200/60 transition active:scale-95 shadow-sm"
                                            title="Hapus Produk"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL FORM TAMBAH / EDIT PRODUK -->
            <div
                v-if="showFormModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-slate-950/60 backdrop-blur-sm"
            >
                <div
                    class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden my-8 max-h-[92vh] flex flex-col"
                >
                    <div
                        class="flex items-center justify-between p-5 text-white"
                        :class="
                            isEditing
                                ? 'bg-gradient-to-r from-amber-500 to-orange-600'
                                : 'bg-gradient-to-r from-blue-600 to-indigo-700'
                        "
                    >
                        <div>
                            <h3 class="text-base font-black">
                                {{
                                    isEditing
                                        ? "Edit Produk Finish Good & Resep (BOM)"
                                        : "Tambah Produk Finish Good & Resep (BOM)"
                                }}
                            </h3>
                            <p class="text-xs text-white/80 mt-0.5">
                                {{
                                    isEditing
                                        ? "Perbarui informasi produk atau takaran komposisi bahan baku."
                                        : "Pilih menu bento standar untuk mengisi SKU dan nama secara otomatis."
                                }}
                            </p>
                        </div>
                        <button
                            @click="showFormModal = false"
                            class="text-white/80 hover:text-white p-1.5 rounded-xl bg-white/10 hover:bg-white/20"
                        >
                            ✕
                        </button>
                    </div>

                    <form
                        @submit.prevent="submitForm"
                        class="flex-1 p-6 space-y-6 overflow-y-auto text-xs"
                    >
                        <!-- BAGIAN 1: IDENTITAS PRODUK -->
                        <div class="p-5 space-y-4 border border-blue-100 bg-blue-50/50 rounded-2xl">
                            <div class="flex items-center justify-between">
                                <span class="block text-xs font-bold tracking-wider text-blue-900 uppercase">
                                    Informasi Finish Good
                                </span>
                                <span class="text-[11px] text-blue-600 font-semibold">
                                    Pilih menu di bawah untuk auto-fill data
                                </span>
                            </div>

                            <!-- DROPDOWN PRESET -->
                            <div>
                                <label class="block mb-1 font-bold text-slate-700">
                                    Pilih Template Menu Bento (Auto-Fill)
                                </label>
                                <select
                                    v-model="selectedPreset"
                                    @change="onPresetSelect"
                                    class="w-full rounded-xl border-blue-200 focus:border-blue-500 focus:ring-blue-500 bg-white font-bold text-slate-800 text-xs py-2.5"
                                >
                                    <option value="" disabled>
                                        -- Klik di sini untuk memilih 1 dari 8 Menu Bento --
                                    </option>
                                    <option
                                        v-for="preset in bentoPresets"
                                        :key="preset.sku"
                                        :value="preset.sku"
                                    >
                                        {{ preset.name }} ({{ preset.sku }})
                                    </option>
                                    <option value="custom">
                                        -- Input Manual (Ketik Sendiri) --
                                    </option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 gap-4 pt-1 md:grid-cols-3">
                                <div>
                                    <label class="block mb-1 font-bold text-slate-700">Kode SKU Produk</label>
                                    <input
                                        ref="skuInputRef"
                                        v-model="form.item_code"
                                        type="text"
                                        placeholder="Ketik SKU / Auto terisi"
                                        class="w-full font-mono text-xs font-bold text-blue-600 bg-white rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    />
                                    <div
                                        v-if="form.errors.item_code"
                                        class="text-rose-600 text-[10px] mt-1 font-semibold"
                                    >
                                        {{ form.errors.item_code }}
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block mb-1 font-bold text-slate-700">Nama Produk Bento</label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Ketik nama produk / Auto terisi"
                                        class="w-full text-xs font-bold bg-white rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-slate-800"
                                        required
                                    />
                                    <div
                                        v-if="form.errors.name"
                                        class="text-rose-600 text-[10px] mt-1 font-semibold"
                                    >
                                        {{ form.errors.name }}
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block mb-1 font-bold text-slate-700">Deskripsi Produk (Opsional)</label>
                                <input
                                    v-model="form.description"
                                    type="text"
                                    placeholder="Keterangan singkat produk bento..."
                                    class="w-full text-xs bg-white rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-slate-700"
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                <div>
                                    <label class="block mb-1 font-bold text-slate-700">Line Produksi</label>
                                    <input
                                        v-model="form.line_production"
                                        type="text"
                                        class="w-full text-xs font-medium bg-white rounded-xl border-slate-200"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block mb-1 font-bold text-slate-700">Batch Yield (Pack)</label>
                                    <input
                                        v-model="form.batch_yield"
                                        type="number"
                                        class="w-full text-xs font-bold bg-white rounded-xl border-slate-200"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block mb-1 font-bold text-slate-700">Satuan</label>
                                    <!-- MENGGUNAKAN DATALIST AGAR BISA PILIH ATAU KETIK MANUAL -->
                                    <input
                                        v-model="form.unit"
                                        list="unitOptions"
                                        type="text"
                                        class="w-full text-xs font-bold bg-white rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block mb-1 font-bold text-slate-700">Harga Jual (Rp)</label>
                                    <input
                                        v-model="form.selling_price"
                                        type="number"
                                        class="w-full text-xs font-bold bg-white rounded-xl border-slate-200"
                                        required
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- BAGIAN 2: KOMPOSISI BAHAN BAKU -->
                        <div class="space-y-3">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <span class="block text-xs font-bold tracking-wider uppercase text-slate-700">
                                        2. Komposisi Bahan Baku (BOM Per 1 Resep)
                                    </span>
                                    <p class="text-[11px] text-slate-400">
                                        Pilih bahan baku dari katalog, kode item dan satuan akan terisi otomatis.
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="startAddNewGroup"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs"
                                    >
                                        + Tambah Nama Tabel Baru
                                    </button>
                                    <button
                                        type="button"
                                        @click="addRecipeRow"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs"
                                    >
                                        + Tambah Bahan
                                    </button>
                                </div>
                            </div>

                            <!-- FORM TAMBAH KATEGORI TABEL BARU -->
                            <div
                                v-if="showNewGroupInput"
                                class="flex flex-col gap-2 p-3 border rounded-xl bg-emerald-50 border-emerald-200 sm:flex-row sm:items-center"
                            >
                                <input
                                    ref="newGroupInputRef"
                                    v-model="newGroupName"
                                    type="text"
                                    placeholder="Ketik nama tabel baru, contoh: Tabel 6. Komposisi Chicken"
                                    class="flex-1 text-xs font-medium rounded-lg border-emerald-200"
                                    @keyup.enter.prevent="addNewGroup"
                                />
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        @click="addNewGroup"
                                        class="px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700"
                                    >
                                        Simpan
                                    </button>
                                    <button
                                        type="button"
                                        @click="cancelAddNewGroup"
                                        class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-200 text-slate-700 hover:bg-slate-300"
                                    >
                                        Batal
                                    </button>
                                </div>
                            </div>

                            <div class="overflow-hidden border border-slate-200 rounded-2xl">
                                <table class="w-full text-xs text-left text-slate-600">
                                    <thead
                                        class="bg-slate-100 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200"
                                    >
                                        <tr>
                                            <th class="p-3 w-52">Kelompok Sub-Tabel</th>
                                            <th class="p-3">Pilih Bahan Baku (Autocomplete)</th>
                                            <th class="p-3 w-28">Kode SKU</th>
                                            <th class="w-24 p-3 text-right">Takaran</th>
                                            <th class="w-20 p-3 text-center">Unit</th>
                                            <th class="w-10 p-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr v-if="form.recipes.length === 0">
                                            <td colspan="6" class="p-4 text-xs text-center text-slate-400">
                                                Belum ada baris bahan baku. Klik
                                                <strong>+ Tambah Bahan</strong>
                                                jika ingin menambahkan resep BOM.
                                            </td>
                                        </tr>
                                        <tr
                                            v-for="(row, idx) in form.recipes"
                                            :key="idx"
                                            class="hover:bg-slate-50/50"
                                        >
                                            <td class="p-2.5">
                                                <!-- DROPDOWN NATIVE: semua nilai lama + baru otomatis tersedia -->
                                                <select
                                                    v-model="row.table_group"
                                                    class="w-full rounded-lg border-slate-200 text-xs py-1.5 font-medium"
                                                    required
                                                >
                                                    <option
                                                        v-for="opt in tableGroupOptions"
                                                        :key="opt"
                                                        :value="opt"
                                                    >
                                                        {{ opt }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td class="p-2.5">
                                                <select
                                                    :value="row.raw_material_id"
                                                    @change="onMaterialChange(idx, $event.target.value)"
                                                    class="w-full rounded-lg border-slate-200 text-xs py-1.5 font-medium"
                                                >
                                                    <option value="">
                                                        -- Pilih Bahan Dari Katalog --
                                                    </option>
                                                    <option
                                                        v-for="mat in rawMaterials"
                                                        :key="mat.id"
                                                        :value="mat.id"
                                                    >
                                                        {{ mat.name }} ({{ mat.item_code }})
                                                    </option>
                                                </select>
                                                <div
                                                    v-if="row.storage_location"
                                                    class="text-[10px] text-slate-400 mt-0.5"
                                                >
                                                    Lokasi: {{ row.storage_location }}
                                                </div>
                                            </td>
                                            <td class="p-2.5">
                                                <input
                                                    :value="row.item_code"
                                                    type="text"
                                                    placeholder="Auto"
                                                    class="w-full rounded-lg border-slate-200 bg-slate-100 text-xs py-1.5 font-mono font-bold text-blue-600"
                                                    readonly
                                                />
                                            </td>
                                            <td class="p-2.5">
                                                <input
                                                    v-model="row.quantity"
                                                    type="number"
                                                    step="0.001"
                                                    placeholder="0"
                                                    class="w-full rounded-lg border-slate-200 text-xs py-1.5 text-right font-bold"
                                                />
                                            </td>
                                            <td class="p-2.5">
                                                <!-- MENGGUNAKAN DATALIST & TIDAK READONLY -->
                                                <input
                                                    v-model="row.unit"
                                                    list="unitOptions"
                                                    type="text"
                                                    placeholder="-"
                                                    class="w-full rounded-lg border-slate-200 bg-white text-xs py-1.5 text-center font-bold text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                                                />
                                            </td>
                                            <td class="p-2.5 text-center">
                                                <button
                                                    type="button"
                                                    @click="removeRecipeRow(idx)"
                                                    class="p-1 font-bold text-slate-400 hover:text-rose-600"
                                                >
                                                    ✕
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                            <button
                                type="button"
                                @click="showFormModal = false"
                                class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-bold"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-5 py-2.5 rounded-xl text-white font-bold transition"
                                :class="
                                    isEditing
                                        ? 'bg-amber-500 hover:bg-amber-600'
                                        : 'bg-blue-600 hover:bg-blue-700'
                                "
                            >
                                {{
                                    isEditing
                                        ? "Simpan Perubahan"
                                        : "Simpan Produk & Resep"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL DETAIL RESEP / BOM -->
            <div
                v-if="showRecipeModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-slate-950/60 backdrop-blur-sm"
            >
                <div
                    class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden my-8 max-h-[90vh] flex flex-col"
                >
                    <div class="flex items-center justify-between p-6 text-white bg-gradient-to-r from-blue-600 to-indigo-700">
                        <div>
                            <span class="text-[11px] font-bold tracking-wider uppercase px-2.5 py-0.5 rounded-md bg-white/20">
                                Form Standar Resep (BOM)
                            </span>
                            <h3 class="mt-1 text-lg font-black">
                                {{ selectedProduct?.name }}
                            </h3>
                            <p class="text-xs text-blue-100">
                                Kode: {{ selectedProduct?.item_code }} | Line:
                                {{ selectedProduct?.line_production }} | Output:
                                1 Resep = {{ selectedProduct?.batch_yield }}
                                {{ selectedProduct?.unit }}
                            </p>
                        </div>
                        <button
                            @click="closeRecipeModal"
                            class="p-2 text-white/80 hover:text-white rounded-xl bg-white/10 hover:bg-white/20"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="flex-1 p-6 space-y-6 overflow-y-auto">
                        <div
                            v-if="Object.keys(groupedRecipes).length === 0"
                            class="py-8 text-xs text-center text-slate-400"
                        >
                            Belum ada komposisi bahan baku (BOM) untuk produk ini.
                        </div>

                        <div
                            v-for="(items, groupName) in groupedRecipes"
                            :key="groupName"
                            class="overflow-hidden border bg-slate-50 rounded-2xl border-slate-200/80"
                        >
                            <div
                                class="bg-slate-100/80 px-4 py-2.5 font-bold text-xs text-slate-700 flex justify-between items-center border-b border-slate-200/80"
                            >
                                <span>{{ groupName }}</span>
                                <span class="text-[11px] text-slate-500 font-normal">
                                    {{ items.length }} Item
                                </span>
                            </div>
                            <table class="w-full text-xs text-left text-slate-600">
                                <thead
                                    class="bg-white text-[10px] uppercase font-bold text-slate-400 border-b border-slate-200"
                                >
                                    <tr>
                                        <th class="px-4 py-2">Kode Item</th>
                                        <th class="px-4 py-2">Nama Bahan / Material</th>
                                        <th class="px-4 py-2 text-right">Jumlah</th>
                                        <th class="px-4 py-2 text-center">Unit</th>
                                        <th class="px-4 py-2">Lokasi Simpan</th>
                                    </tr>
                                </thead>
                                <tbody class="font-medium divide-y divide-slate-100">
                                    <tr v-for="recipe in items" :key="recipe.id">
                                        <td class="px-4 py-2.5 font-mono text-slate-700 font-bold">
                                            {{ recipe.raw_material?.item_code }}
                                        </td>
                                        <td class="px-4 py-2.5 text-slate-800 font-semibold">
                                            {{ recipe.raw_material?.name }}
                                            <div
                                                v-if="recipe.raw_material?.condition_note"
                                                class="text-[10px] text-amber-600 font-normal"
                                            >
                                                {{ recipe.raw_material.condition_note }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2.5 text-right font-mono font-bold text-blue-600">
                                            {{ Number(recipe.quantity) }}
                                        </td>
                                        <td class="px-4 py-2.5 text-center font-bold text-slate-500">
                                            {{ recipe.unit }}
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-200/60 text-slate-700"
                                            >
                                                {{ recipe.raw_material?.storage_location }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 border-t bg-slate-50 border-slate-100">
                        <button
                            @click="openEditModal(selectedProduct)"
                            class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs inline-flex items-center gap-1.5"
                        >
                            Edit Resep / Produk Ini
                        </button>
                        <!-- ==================== TAMBAHAN TOMBOL PRINT PDF ==================== -->
        <a
            v-if="selectedProduct"
            :href="route('products.exportPdf', selectedProduct.id)"
            target="_blank"
            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print PDF
        </a>
                        <button
                            @click="closeRecipeModal"
                            class="px-5 py-2 text-xs font-bold rounded-xl bg-slate-200 text-slate-700"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
