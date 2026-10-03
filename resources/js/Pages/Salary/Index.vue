<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router } from "@inertiajs/vue3";

const props = defineProps({
    salaries: Array,
    isHead: Boolean,
    subordinates: Array,
});

const formatRupiah = (angka) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(angka);
};

// ==============================
// FITUR EXPORT EXCEL (CSV)
// ==============================
// ==============================
// FITUR EXPORT EXCEL (TERTATA RAPI DI SEMUA SPREADSHEET)
// ==============================
const exportExcel = () => {
    let html = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta http-equiv="content-type" content="text/html; charset=UTF-8">
            <style>
                .title { font-size: 16px; font-weight: bold; color: #1e3a8a; margin-bottom: 10px; }
                table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 11pt; }
                th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; border: 0.5pt solid #000000; padding: 8px 12px; text-align: center; vertical-align: middle; }
                td { border: 0.5pt solid #cbd5e1; padding: 6px 10px; vertical-align: middle; }
                .text-center { text-align: center; }
                .text-right { text-align: right; mso-number-format:"\\#\\,\\#0"; }
                .text-bold { font-weight: bold; }
            </style>
        </head>
        <body>
            <table>
                <tr>
                    <td colspan="9" class="title">LAPORAN REKAP GAJI KARYAWAN (PERIODE TUTUP BUKU 15 - 15)</td>
                </tr>
                <tr><td colspan="9"></td></tr>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>NIK</th>
                        <th>Periode Tutup Buku</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Potongan</th>
                        <th>Total Gaji (Net)</th>
                        <th>Status / Komplain</th>
                    </tr>
                </thead>
                <tbody>
    `;

    props.salaries.forEach((item, index) => {
        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>${item.user?.name || '-'}</td>
                <td class="text-center" style="mso-number-format:'\@';">${item.user?.employee?.employee_code || '-'}</td>
                <td>${item.period}</td>
                <td class="text-right">${item.base_salary}</td>
                <td class="text-right">${item.allowances}</td>
                <td class="text-right">${item.deductions}</td>
                <td class="text-right text-bold">${item.net_salary}</td>
                <td>${item.employee_note ? 'Komplain: ' + item.employee_note : 'Aman'}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </body>
        </html>
    `;

    // Download file dengan ekstensi .xls agar dibaca sebagai workbook oleh Excel/WPS/Google Sheets
    const blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = `Laporan_Rekap_Gaji_15_15_${new Date().toISOString().slice(0, 10)}.xls`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
};

// ==============================
// FITUR CETAK PDF / SLIP GAJI
// ==============================
const printSlip = (item) => {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Slip Gaji - ${item.user?.name}</title>
                <style>
                    @page {
                        size: A4 portrait;
                        margin: 10mm;
                    }
                    body {
                        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                        padding: 20px;
                        color: #1e293b;
                        max-width: 750px;
                        margin: 0 auto;
                        background: #fff;
                    }
                    .slip-container {
                        border: 2px solid #0f172a;
                        padding: 25px;
                        border-radius: 8px;
                    }
                    .header {
                        text-align: center;
                        border-bottom: 3px double #0f172a;
                        padding-bottom: 12px;
                        margin-bottom: 20px;
                    }
                    .header h2 { margin: 0; color: #0f172a; font-size: 22px; letter-spacing: 1px; }
                    .header p { margin: 4px 0 0; font-size: 11px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }

                    /* PERBAIKAN TATA LETAK INFORMASI (2 KOLOM RAPI) */
                    .info-section {
                        background: #f8fafc;
                        border: 1px solid #e2e8f0;
                        border-radius: 6px;
                        padding: 15px 18px;
                        margin-bottom: 20px;
                    }
                    .info-grid {
                        display: flex;
                        justify-content: space-between;
                        gap: 20px;
                    }
                    .info-column {
                        width: 48%;
                    }
                    .info-row {
                        display: flex;
                        margin-bottom: 8px;
                        font-size: 13px;
                    }
                    .info-row:last-child {
                        margin-bottom: 0;
                    }
                    .info-label {
                        width: 120px;
                        font-weight: bold;
                        color: #475569;
                        flex-shrink: 0;
                    }
                    .info-colon {
                        width: 15px;
                        font-weight: bold;
                        color: #475569;
                        flex-shrink: 0;
                    }
                    .info-value {
                        flex-grow: 1;
                        color: #0f172a;
                    }

                    .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    .table-data th, .table-data td { border: 1px solid #cbd5e1; padding: 10px 14px; font-size: 13px; text-align: left; }
                    .table-data th { background-color: #0f172a; color: #ffffff; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
                    .table-data td.number, .table-data th.number { text-align: right; }
                    .section-title { background: #e2e8f0; font-weight: bold; color: #334155; font-size: 12px; }

                    .total-box {
                        font-size: 15px;
                        font-weight: bold;
                        background: #f0fdf4;
                        color: #166534;
                        padding: 14px 18px;
                        border: 1px solid #bbf7d0;
                        border-radius: 6px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 30px;
                    }
                    .total-amount { font-size: 18px; color: #15803d; }

                    .footer { display: flex; justify-content: space-between; text-align: center; font-size: 13px; margin-top: 40px; page-break-inside: avoid; }
                    .sign-box { width: 220px; color: #334155; }
                    .sign-space { height: 60px; }
                    .sign-line { border-top: 1px solid #0f172a; display: block; padding-top: 6px; font-weight: bold; }

                    .watermark {
                        position: fixed;
                        top: 40%;
                        left: 25%;
                        font-size: 70px;
                        color: rgba(15, 23, 42, 0.03);
                        transform: rotate(-30deg);
                        z-index: -1;
                        font-weight: bold;
                        text-transform: uppercase;
                    }
                </style>
            </head>
            <body>
                <div class="watermark">CONFIDENTIAL</div>

                <div class="slip-container">
                    <div class="header">
                        <h2>PT HOKBEN PLANT 8</h2>
                        <p> Jl. Raya Poncol Gg. Cawang No.21, RT.9/RW.9, Ciracas, Kec. Ciracas, Kota Jakarta Timur, Kode Pos : 13750 &bull; Laporan Slip Gaji Karyawan</p>
                        <p>Telepon: (021) 29981234</p>
                        </div>

                    <!-- BAGIAN INFORMASI DITATA DENGAN 2 KOLOM FLEKSIBEL -->
                    <div class="info-section">
                        <div class="info-grid">
                            <!-- Kolom Kiri: Karyawan -->
                            <div class="info-column">
                                <div class="info-row">
                                    <div class="info-label">Nama Karyawan</div>
                                    <div class="info-colon">:</div>
                                    <div class="info-value"><b>${item.user?.name || '-'}</b></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Nomor Induk</div>
                                    <div class="info-colon">:</div>
                                    <div class="info-value">${item.user?.employee?.employee_code || '-'}</div>
                                </div>
                            </div>
                            <!-- Kolom Kanan: Periode & Tanggal -->
                            <div class="info-column">
                                <div class="info-row">
                                    <div class="info-label">Periode Gaji</div>
                                    <div class="info-colon">:</div>
                                    <div class="info-value">${item.period}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Tanggal Cetak</div>
                                    <div class="info-colon">:</div>
                                    <div class="info-value">${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table-data">
                        <thead>
                            <tr>
                                <th>Rincian Komponen Gaji</th>
                                <th class="number" width="180">Jumlah (IDR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="section-title">
                                <td colspan="2">P E N D A P A T A N</td>
                            </tr>
                            <tr>
                                <td>Gaji Pokok (Dihitung Berdasarkan Hari Masuk Kerja)</td>
                                <td class="number">Rp ${Number(item.base_salary).toLocaleString('id-ID')}</td>
                            </tr>
                            <tr>
                                <td>Tunjangan / Insentif Tambahan</td>
                                <td class="number">Rp ${Number(item.allowances).toLocaleString('id-ID')}</td>
                            </tr>
                            <tr class="section-title">
                                <td colspan="2">P O T O N G A N</td>
                            </tr>
                            <tr>
                                <td>Potongan Absensi / Denda Keterlambatan</td>
                                <td class="number" style="color: #dc2626;">(Rp ${Number(item.deductions).toLocaleString('id-ID')})</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="total-box">
                        <span>GAJI BERSIH DITERIMA (NET SALARY)</span>
                        <span class="total-amount">Rp ${Number(item.net_salary).toLocaleString('id-ID')}</span>
                    </div>

                    <div class="footer">
                        <div class="sign-box">
                            <p>Penerima,</p>
                            <div class="sign-space"></div>
                            <span class="sign-line">${item.user?.name || '-'}</span>
                        </div>
                        <div class="sign-box">
                            <p>Disetujui Oleh,</p>
                            <div class="sign-space"></div>
                            <span class="sign-line">HR & Finance Management</span>
                        </div>
                    </div>
                </div>

                <script>
                    window.onload = function() {
                        window.print();
                    }
                <\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
};
const showAddModal = ref(false);
const addForm = useForm({
    user_id: "",
    month: new Date().getMonth() + 1,
    year: new Date().getFullYear(),
    daily_rate: 272000,
    allowances: 0,
    deductions: 0,
});

const submitSalary = () => {
    addForm.post(route("salaries.store"), {
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset('user_id', 'allowances', 'deductions');
        },
    });
};

const deleteSalary = (id) => {
    if (confirm("Yakin ingin menghapus data gaji ini?")) {
        router.delete(route("salaries.destroy", id));
    }
};

const showComplainModal = ref(false);
const selectedSalaryId = ref(null);
const complainForm = useForm({
    employee_note: "",
});

const openComplain = (salary) => {
    selectedSalaryId.value = salary.id;
    complainForm.employee_note = salary.employee_note || "";
    showComplainModal.value = true;
};

const submitComplain = () => {
    complainForm.post(route("salaries.complain", selectedSalaryId.value), {
        onSuccess: () => {
            showComplainModal.value = false;
            complainForm.reset();
        },
    });
};
</script>

<template>
    <Head title="Monitoring Gaji" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Monitoring Gaji (Sistem Tutup Buku 15 - 15)
            </h2>
        </template>

        <div class="px-4 py-4 mx-auto space-y-4 sm:py-6 max-w-7xl sm:px-6 lg:px-8">

            <!-- TOMBOL AKSI (TAMBAH & EXPORT) -->
            <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
                <button
                    @click="exportExcel"
                    class="flex items-center justify-center w-full gap-2 px-4 py-2 text-sm font-bold transition shadow-sm sm:w-auto text-emerald-700 bg-emerald-100 hover:bg-emerald-200 rounded-xl"
                >
                    📊 Download Excel (CSV)
                </button>

                <button
                    v-if="isHead"
                    @click="showAddModal = true"
                    class="w-full px-4 py-2 text-sm font-bold text-white transition bg-blue-600 shadow-sm sm:w-auto rounded-xl hover:bg-blue-700 shadow-blue-500/30"
                >
                    + Hitung Gaji (Periode 15-15)
                </button>
            </div>

            <!-- TABEL GAJI -->
            <div class="overflow-hidden bg-white border shadow-sm rounded-2xl border-slate-100">
                <div class="p-4 sm:p-6">
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs font-semibold uppercase bg-slate-50 text-slate-500">
                                    <th v-if="isHead" class="p-3">Karyawan</th>
                                    <th class="p-3">Periode Tutup Buku (15 - 15)</th>
                                    <th class="p-3">Rincian Hitungan</th>
                                    <th class="p-3">Total Gaji (Net)</th>
                                    <th class="p-3">Status / Komplain</th>
                                    <th class="p-3 text-center">Aksi / Cetak</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">
                                <tr v-for="item in salaries" :key="item.id">
                                    <td v-if="isHead" class="p-3">
                                        <p class="font-semibold text-slate-800">{{ item.user?.name }}</p>
                                        <p class="text-[11px] font-mono text-slate-400">NIK: {{ item.user?.employee?.employee_code || '-' }}</p>
                                    </td>

                                    <td class="p-3 text-xs font-semibold text-blue-700">
                                        {{ item.period }}
                                    </td>

                                    <td class="p-3 text-xs text-slate-500">
                                        <p>Pokok: <span class="font-semibold text-slate-700">{{ formatRupiah(item.base_salary) }}</span></p>
                                        <p class="text-emerald-600">Tunjangan: +{{ formatRupiah(item.allowances) }}</p>
                                        <p class="text-red-500">Potongan: -{{ formatRupiah(item.deductions) }}</p>
                                    </td>

                                    <td class="p-3 text-base font-bold text-blue-700">
                                        {{ formatRupiah(item.net_salary) }}
                                    </td>

                                    <td class="p-3">
                                        <div v-if="!item.is_resolved" class="px-2 py-1 text-xs text-red-700 border border-red-100 rounded-md bg-red-50">
                                            <span class="font-bold">Komplain:</span> {{ item.employee_note }}
                                        </div>
                                        <div v-else-if="item.employee_note" class="px-2 py-1 text-xs border rounded-md bg-emerald-50 border-emerald-100 text-emerald-700">
                                            Selesai
                                        </div>
                                        <span v-else class="text-xs italic text-slate-400">Aman</span>
                                    </td>

                                    <td class="p-3 space-x-1 text-center">
                                        <button @click="printSlip(item)" class="px-3 py-1.5 text-xs font-bold text-blue-700 transition bg-blue-100 rounded-lg hover:bg-blue-200">
                                            Cetak PDF
                                        </button>
                                        <button v-if="isHead" @click="deleteSalary(item.id)" class="px-3 py-1.5 text-xs font-bold text-red-600 transition bg-red-100 rounded-lg hover:bg-red-200">
                                            Hapus
                                        </button>
                                        <button v-if="!isHead" @click="openComplain(item)" class="px-3 py-1.5 text-xs font-bold text-amber-700 transition bg-amber-100 rounded-lg hover:bg-amber-200">
                                            Komplain
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="salaries.length === 0">
                                    <td colspan="6" class="p-6 text-xs text-center text-slate-400">Belum ada data slip gaji periode 15-15.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- KARTU MOBILE -->
                    <div class="block space-y-3 md:hidden">
                        <div v-for="item in salaries" :key="item.id" class="p-4 space-y-3 border shadow-sm bg-slate-50 rounded-2xl border-slate-100">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                                <div>
                                    <p v-if="isHead" class="text-sm font-bold text-slate-800">{{ item.user?.name }}</p>
                                    <p class="text-[11px] font-bold text-blue-600 mt-1">{{ item.period }}</p>
                                </div>
                                <span v-if="!item.is_resolved" class="px-2 py-0.5 text-[10px] font-bold text-red-700 bg-red-100 rounded-full">Komplain</span>
                                <span v-else class="px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full">Aman</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <p class="text-slate-400">Gaji Pokok</p>
                                    <p class="font-semibold text-slate-700">{{ formatRupiah(item.base_salary) }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Total Net</p>
                                    <p class="font-bold text-blue-700">{{ formatRupiah(item.net_salary) }}</p>
                                </div>
                            </div>

                            <div v-if="item.employee_note" class="p-2 text-[10px] bg-white border rounded text-red-700 border-red-100">
                                <b>Komplain:</b> {{ item.employee_note }}
                            </div>

                            <div class="flex gap-2 pt-2 border-t border-slate-200">
                                <button @click="printSlip(item)" class="w-full py-2 text-xs font-bold text-blue-700 bg-blue-100 rounded-xl">Cetak PDF</button>
                                <button v-if="isHead" @click="deleteSalary(item.id)" class="w-full py-2 text-xs font-bold text-red-700 bg-red-100 rounded-xl">Hapus</button>
                                <button v-if="!isHead" @click="openComplain(item)" class="w-full py-2 text-xs font-bold text-amber-800 bg-amber-100 rounded-xl">Komplain</button>
                            </div>
                        </div>
                        <div v-if="salaries.length === 0" class="p-6 text-xs text-center text-slate-400">Belum ada data slip gaji.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL HITUNG GAJI 15-15 -->
        <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="w-full max-w-md p-6 bg-white shadow-xl rounded-3xl">
                <h3 class="mb-2 text-lg font-bold text-slate-800">Hitung Gaji (Tutup Buku 15 - 15)</h3>
                <form @submit.prevent="submitSalary" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700">Karyawan</label>
                        <select v-model="addForm.user_id" required class="w-full mt-1 text-sm border-slate-200 rounded-xl focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Karyawan</option>
                            <option v-for="sub in subordinates" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-slate-700">Bulan Cut-off (Sampai Tgl 15)</label>
                            <select v-model="addForm.month" required class="w-full mt-1 text-sm border-slate-200 rounded-xl focus:border-blue-500 focus:ring-blue-500">
                                <option :value="1">Januari</option>
                                <option :value="2">Februari</option>
                                <option :value="3">Maret</option>
                                <option :value="4">April</option>
                                <option :value="5">Mei</option>
                                <option :value="6">Juni</option>
                                <option :value="7">Juli</option>
                                <option :value="8">Agustus</option>
                                <option :value="9">September</option>
                                <option :value="10">Oktober</option>
                                <option :value="11">November</option>
                                <option :value="12">Desember</option>
                            </select>
                        </div>
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-slate-700">Tahun</label>
                            <input type="number" v-model="addForm.year" required class="w-full mt-1 text-sm border-slate-200 rounded-xl focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700">Tarif Gaji Harian (Rp)</label>
                        <input type="number" v-model="addForm.daily_rate" required class="w-full mt-1 text-sm border-blue-200 bg-blue-50 rounded-xl focus:border-blue-500 focus:ring-blue-500" />
                    </div>

                    <div class="flex gap-3 pt-2">
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-slate-700">Tunjangan (+)</label>
                            <input type="number" v-model="addForm.allowances" class="w-full mt-1 text-sm border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-emerald-500" />
                        </div>
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-slate-700">Potongan (-)</label>
                            <input type="number" v-model="addForm.deductions" class="w-full mt-1 text-sm border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" :disabled="addForm.processing" class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700">Hitung Gaji</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL KOMPLAIN -->
        <div v-if="showComplainModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="w-full max-w-md p-6 bg-white shadow-xl rounded-3xl">
                <h3 class="mb-1 text-lg font-bold text-slate-800">Komplain Gaji</h3>
                <form @submit.prevent="submitComplain">
                    <textarea v-model="complainForm.employee_note" rows="4" placeholder="Tulis keluhan..." required class="w-full text-sm border-slate-200 rounded-xl focus:border-amber-500 focus:ring-amber-500"></textarea>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" @click="showComplainModal = false" class="px-4 py-2 text-sm font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" :disabled="complainForm.processing" class="px-4 py-2 text-sm font-bold text-white bg-amber-600 rounded-xl hover:bg-amber-700">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
