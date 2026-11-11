<script setup>
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";

const props = defineProps({
    inbounds: Object,
    filters: Object,
});

// State untuk filter
const search = ref(props.filters.search || "");
const startDate = ref(props.filters.start_date || "");
const endDate = ref(props.filters.end_date || "");

// Watcher untuk auto-submit filter saat diketik/dipilih
watch([search, startDate, endDate], ([q, start, end]) => {
    router.get(
        route("inbounds.index"),
        { search: q, start_date: start, end_date: end },
        { preserveState: true, replace: true },
    );
});

const resetFilter = () => {
    search.value = "";
    startDate.value = "";
    endDate.value = "";
};

// Fungsi Cetak (Print)
const printReport = () => {
    window.print();
};
</script>

<template>
    <Head title="Laporan Barang Masuk" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between print:hidden">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-slate-800">
                        Laporan Riwayat Bahan Masuk (Inbound)
                    </h2>
                    <p class="mt-1 text-xs text-slate-500">
                        Rekapitulasi seluruh penerimaan barang dari vendor ke logistik.
                    </p>
                </div>
                <button
                    @click="printReport"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-sm transition active:scale-95"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Laporan (PDF/Print)
                </button>
            </div>
        </template>

        <div class="px-4 py-6 mx-auto space-y-6 sm:px-6 lg:px-8 max-w-7xl">
            <!-- HEADER CETAK -->
            <div class="hidden pb-4 mb-8 text-center border-b-2 print:block border-slate-800">
                <h1 class="text-2xl font-black tracking-widest uppercase text-slate-900">
                    PT. BENTO FOOD INDONESIA
                </h1>
                <h2 class="mt-1 text-lg font-bold text-slate-700">
                    LAPORAN PENERIMAAN BARANG (GOODS RECEIPT)
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Periode: {{ startDate ? startDate : "Awal" }} s/d {{ endDate ? endDate : "Sekarang" }}
                </p>
            </div>

            <!-- FILTER BOX -->
            <div class="flex flex-col items-center gap-4 p-4 bg-white border shadow-sm rounded-2xl border-slate-100 md:flex-row print:hidden">
                <div class="w-full md:flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pencarian</label>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari Kode Item, Nama Bahan, atau No DO..."
                        class="w-full px-4 py-2 text-xs rounded-xl border-slate-200 focus:border-blue-500 bg-slate-50/50"
                    />
                </div>

                <div class="w-full md:w-48">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Dari Tanggal</label>
                    <input
                        v-model="startDate"
                        type="date"
                        class="w-full px-3 py-2 text-xs rounded-xl border-slate-200 focus:border-blue-500 bg-slate-50/50"
                    />
                </div>

                <div class="w-full md:w-48">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input
                        v-model="endDate"
                        type="date"
                        class="w-full px-3 py-2 text-xs rounded-xl border-slate-200 focus:border-blue-500 bg-slate-50/50"
                    />
                </div>

                <div class="self-end w-full md:w-auto">
                    <button
                        @click="resetFilter"
                        class="w-full px-4 py-2 text-xs font-bold border rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border-rose-100"
                    >
                        Reset
                    </button>
                </div>
            </div>

            <!-- TABEL LAPORAN -->
            <div class="overflow-hidden bg-white border shadow-sm rounded-2xl border-slate-100 print:shadow-none print:border-none print:rounded-none">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-slate-600 print:text-black">
                        <thead class="bg-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 print:bg-gray-200">
                            <tr>
                                <th class="p-4">Tanggal Masuk</th>
                                <th class="p-4">No. DO / Vendor</th>
                                <th class="p-4">Kode Item</th>
                                <th class="p-4">Nama Material</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4 text-right">Stok Sebelum</th>
                                <th class="p-4 text-right">Kuantitas Masuk</th>
                                <th class="p-4">Lokasi Simpan</th>
                                <th class="p-4">Suhu Masuk</th>
                            </tr>
                        </thead>
                        <tbody class="font-medium divide-y divide-slate-100 print:border-b print:border-black">
                            <tr v-if="inbounds.data.length === 0">
                                <td colspan="9" class="p-8 italic text-center text-slate-400">
                                    Tidak ada data barang masuk yang ditemukan.
                                </td>
                            </tr>
                            <tr v-for="log in inbounds.data" :key="log.id" class="transition hover:bg-slate-50/70">
                                <td class="p-4 text-slate-800">
                                    {{ new Date(log.tanggal_masuk).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) }}
                                </td>
                                <td class="p-4">
                                    <span v-if="log.no_dokumen" class="font-bold text-slate-700">{{ log.no_dokumen }}</span>
                                    <span v-else class="text-slate-300">-</span>
                                </td>
                                <td class="p-4 font-mono font-bold text-blue-600 print:text-black">
                                    {{ log.raw_material?.item_code }}
                                </td>
                                <td class="p-4 font-bold text-slate-800">
                                    {{ log.raw_material?.name }}
                                </td>
                                <td class="p-4 uppercase text-[10px]">
                                    {{ log.raw_material?.category }}
                                </td>
                                <td class="p-4 font-mono text-sm font-semibold text-right text-slate-500 print:text-black">
                                    {{ Number(log.stok_sebelum || 0) }}
                                    <span class="text-[10px]">{{ log.raw_material?.unit }}</span>
                                </td>
                                <td class="p-4 font-mono text-sm font-black text-right text-emerald-600 print:text-black">
                                    +{{ Number(log.qty_masuk) }}
                                    <span class="text-xs font-semibold text-slate-500">{{ log.raw_material?.unit }}</span>
                                </td>
                                <!-- DATA LOKASI DAN SUHU -->
                                <td class="p-4 text-slate-700">
                                    {{ log.lokasi_simpan ? log.lokasi_simpan : '-' }}
                                </td>
                                <td class="p-4 font-mono font-bold text-slate-700 print:text-black">
                                    {{ (log.suhu_masuk !== null && log.suhu_masuk !== undefined) ? log.suhu_masuk + ' °C' : '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="flex items-center justify-between p-4 border-t border-slate-100 bg-slate-50 print:hidden" v-if="inbounds.links && inbounds.links.length > 3">
                    <div class="text-xs text-slate-500">
                        Menampilkan halaman {{ inbounds.current_page }} dari {{ inbounds.last_page }}
                    </div>
                    <div class="flex gap-1">
                        <template v-for="(link, k) in inbounds.links" :key="k">
                            <div v-if="link.url === null" class="px-3 py-1 text-xs bg-white border rounded-lg cursor-not-allowed text-slate-400 border-slate-100" v-html="link.label"></div>
                            <Link v-else :href="link.url" class="px-3 py-1 text-xs font-bold transition border rounded-lg" :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100'" v-html="link.label"></Link>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    @page {
        size: landscape;
        margin: 1cm;
    }
    aside, nav, header, button, .print\:hidden {
        display: none !important;
    }
    main {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .lg\:ml-64, .md\:ml-64 {
        margin-left: 0 !important;
    }
    body {
        background-color: white !important;
        color: black !important;
    }
}
</style>
