<script setup>
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { usePermission } from "@/Composables/usePermission";

const props = defineProps({
    leaveRequests: Array,
    pageTitle: {
        type: String,
        default: "Pengajuan Izin, Cuti & Dispensasi",
    },
    allowedTypes: {
        type: Array,
        default: () => ["permission", "sick", "leave", "dispensation"],
    },
});

const user = usePage().props.auth.user;
const { hasRole, hasPermission } = usePermission();
const isLineHead = hasRole("Line Head") || hasPermission("approve leave request");

// State untuk fitur minimize / toggle (lipat)
const showForm = ref(true);
const showList = ref(true);

// State modal penolakan (Reject)
const showRejectModal = ref(false);
const selectedRequestId = ref(null);

// Form handling Inertia untuk pengajuan
const form = useForm({
    type: props.allowedTypes[0] || "permission",
    start_date: "",
    end_date: "",
    reason: "",
    attachment: null,
});

const submitLeave = () => {
    form.post(route("leave.store"), {
        onSuccess: () => {
            form.reset("start_date", "end_date", "reason", "attachment");
        },
    });
};

// Form handling Inertia untuk persetujuan Line Head
const approveForm = useForm({});
const rejectForm = useForm({
    rejection_note: "",
});

const approveRequest = (id) => {
    approveForm.post(route("leave.approve", id));
};

const openRejectModal = (id) => {
    selectedRequestId.value = id;
    rejectForm.reset();
    showRejectModal.value = true;
};

const submitReject = () => {
    if (!selectedRequestId.value) return;
    rejectForm.post(route("leave.reject", selectedRequestId.value), {
        onSuccess: () => {
            showRejectModal.value = false;
            selectedRequestId.value = null;
            rejectForm.reset();
        },
    });
};

// Helper teks label tipe pengajuan
const formatTypeLabel = (type) => {
    const labels = {
        permission: "Izin",
        sick: "Sakit",
        leave: "Cuti",
        dispensation: "Dispensasi",
    };
    return labels[type] || type?.toUpperCase() || "-";
};
</script>

<template>
    <Head :title="pageTitle" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ pageTitle }}
            </h2>
        </template>

        <div class="py-4 sm:py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- CARD 1: FORM BUAT PENGAJUAN (Bisa Di-minimize) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300">
                <div class="p-4 sm:px-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800">Buat Pengajuan</h3>

                    <!-- Tombol Minimize / Expand -->
                    <button
                        @click="showForm = !showForm"
                        type="button"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                        :title="showForm ? 'Sembunyikan Form' : 'Tampilkan Form'"
                    >
                        <svg v-if="showForm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <!-- Body Form -->
                <div v-show="showForm" class="p-4 sm:p-6">
                    <form @submit.prevent="submitLeave" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipe Pengajuan</label>
                                <select
                                    v-model="form.type"
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                >
                                    <option v-if="allowedTypes.includes('permission')" value="permission">Izin</option>
                                    <option v-if="allowedTypes.includes('sick')" value="sick">Sakit</option>
                                    <option v-if="allowedTypes.includes('leave')" value="leave">Cuti</option>
                                    <option v-if="allowedTypes.includes('dispensation')" value="dispensation">Dispensasi</option>
                                </select>
                                <div v-if="form.errors.type" class="text-red-500 text-xs mt-1">{{ form.errors.type }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lampiran Dokumen (Opsional)</label>
                                <input
                                    type="file"
                                    @input="form.attachment = $event.target.files[0]"
                                    class="mt-1 block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                <div v-if="form.errors.attachment" class="text-red-500 text-xs mt-1">{{ form.errors.attachment }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                <input
                                    v-model="form.start_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                />
                                <div v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">{{ form.errors.start_date }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                                <input
                                    v-model="form.end_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                />
                                <div v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">{{ form.errors.end_date }}</div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Alasan</label>
                                <textarea
                                    v-model="form.reason"
                                    rows="2"
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Isi alasan pengajuan..."
                                ></textarea>
                                <div v-if="form.errors.reason" class="text-red-500 text-xs mt-1">{{ form.errors.reason }}</div>
                            </div>
                        </div>

                        <div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition shadow-md disabled:opacity-50"
                            >
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CARD 2: DAFTAR PENGAJUAN (Bisa Di-minimize) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300">
                <div class="p-4 sm:px-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800">Daftar Pengajuan</h3>

                    <!-- Tombol Minimize / Expand -->
                    <button
                        @click="showList = !showList"
                        type="button"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                        :title="showList ? 'Sembunyikan Daftar' : 'Tampilkan Daftar'"
                    >
                        <svg v-if="showList" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <div v-show="showList" class="p-4 sm:p-6">

                    <!-- 1. TAMPILAN TABEL DESKTOP (PC / Laptop) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                    <th class="p-3">Nama Karyawan</th>
                                    <th class="p-3">Tipe</th>
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">Alasan</th>
                                    <th class="p-3">Lampiran</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3" v-if="isLineHead">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <tr v-for="item in props.leaveRequests" :key="item.id">
                                    <td class="p-3 font-medium text-gray-800">{{ item.user?.name || '-' }}</td>
                                    <td class="p-3 font-semibold text-xs uppercase">{{ formatTypeLabel(item.type) }}</td>
                                    <td class="p-3">{{ item.start_date }} s/d {{ item.end_date }}</td>
                                    <td class="p-3">
                                        {{ item.reason }}
                                        <div v-if="item.rejection_note" class="text-xs text-red-500 mt-1 italic">
                                            Catatan: {{ item.rejection_note }}
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <a
                                            v-if="item.attachment"
                                            :href="'/storage/' + item.attachment"
                                            target="_blank"
                                            class="text-blue-600 hover:underline text-xs inline-flex items-center gap-1"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Lihat
                                        </a>
                                        <span v-else class="text-xs text-gray-400">-</span>
                                    </td>
                                    <td class="p-3">
                                        <span
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': item.status === 'pending',
                                                'bg-green-100 text-green-800': item.status === 'approved',
                                                'bg-red-100 text-red-800': item.status === 'rejected',
                                            }"
                                            class="px-2.5 py-1 rounded text-xs font-bold uppercase"
                                        >
                                            {{ item.status }}
                                        </span>
                                    </td>
                                    <td class="p-3" v-if="isLineHead">
                                        <div v-if="item.status === 'pending'" class="flex items-center gap-1.5">
                                            <button
                                                @click="approveRequest(item.id)"
                                                :disabled="approveForm.processing"
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition"
                                            >
                                                Approve
                                            </button>
                                            <button
                                                @click="openRejectModal(item.id)"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold transition"
                                            >
                                                Reject
                                            </button>
                                        </div>
                                        <span v-else class="text-xs text-gray-400">-</span>
                                    </td>
                                </tr>
                                <tr v-if="!props.leaveRequests || props.leaveRequests.length === 0">
                                    <td :colspan="isLineHead ? 7 : 6" class="p-4 text-center text-gray-500">
                                        Belum ada data pengajuan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 2. TAMPILAN KARTU MOBILE (Layar HP) -->
                    <div class="block md:hidden space-y-3">
                        <div
                            v-for="item in props.leaveRequests"
                            :key="item.id"
                            class="bg-gray-50 p-4 rounded-2xl border border-gray-100 shadow-sm space-y-2"
                        >
                            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                                <div>
                                    <p class="text-xs font-bold text-gray-800">{{ item.user?.name || '-' }}</p>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase mt-0.5">
                                        {{ formatTypeLabel(item.type) }}
                                    </p>
                                </div>
                                <span
                                    :class="{
                                        'bg-yellow-100 text-yellow-800': item.status === 'pending',
                                        'bg-green-100 text-green-800': item.status === 'approved',
                                        'bg-red-100 text-red-800': item.status === 'rejected',
                                    }"
                                    class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase"
                                >
                                    {{ item.status }}
                                </span>
                            </div>

                            <div class="text-xs text-gray-600 space-y-1">
                                <p><span class="text-gray-400">Tanggal:</span> {{ item.start_date }} s/d {{ item.end_date }}</p>
                                <p><span class="text-gray-400">Alasan:</span> {{ item.reason }}</p>
                                <p v-if="item.rejection_note" class="text-red-500 italic">
                                    <span class="text-gray-400">Alasan Ditolak:</span> {{ item.rejection_note }}
                                </p>
                                <div v-if="item.attachment" class="pt-1">
                                    <a :href="'/storage/' + item.attachment" target="_blank" class="text-blue-600 font-medium hover:underline text-[11px]">
                                        📎 Buka Lampiran Berkas
                                    </a>
                                </div>
                            </div>

                            <div v-if="isLineHead && item.status === 'pending'" class="pt-2 border-t border-gray-200 grid grid-cols-2 gap-2">
                                <button
                                    @click="approveRequest(item.id)"
                                    :disabled="approveForm.processing"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-xl text-xs font-bold transition shadow-sm"
                                >
                                    Approve
                                </button>
                                <button
                                    @click="openRejectModal(item.id)"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-xl text-xs font-bold transition shadow-sm"
                                >
                                    Reject
                                </button>
                            </div>
                        </div>

                        <div v-if="!props.leaveRequests || props.leaveRequests.length === 0" class="p-6 text-center text-xs text-gray-400">
                            Belum ada data pengajuan.
                        </div>
                    </div>

                </div>
            </div>

            <!-- MODAL CATATAN PENOLAKAN (REJECT) -->
            <div v-if="showRejectModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
                    <h3 class="text-base font-bold text-gray-800">Tolak Pengajuan</h3>
                    <form @submit.prevent="submitReject" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Catatan / Alasan Penolakan</label>
                            <textarea
                                v-model="rejectForm.rejection_note"
                                rows="3"
                                class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                                placeholder="Jelaskan alasan pengajuan ditolak..."
                                required
                            ></textarea>
                            <div v-if="rejectForm.errors.rejection_note" class="text-red-500 text-xs mt-1">
                                {{ rejectForm.errors.rejection_note }}
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button
                                type="button"
                                @click="showRejectModal = false"
                                class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="rejectForm.processing"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-semibold shadow-sm disabled:opacity-50"
                            >
                                Simpan Penolakan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
