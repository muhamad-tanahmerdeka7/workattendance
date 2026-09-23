<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { usePermission } from "@/Composables/usePermission";

const props = defineProps({
    leaveRequests: Array,
});

const user = usePage().props.auth.user;
const { hasRole, hasPermission } = usePermission();
const isLineHead = hasRole("Line Head") || hasPermission("approve leave request");

// State untuk fitur minimize / toggle (lipat)
const showForm = ref(true);
const showList = ref(true);

// Form handling Inertia untuk pengajuan izin
const form = useForm({
    type: "permission",
    start_date: "",
    end_date: "",
    reason: "",
});

const submitLeave = () => {
    form.post(route("leave.store"), {
        onSuccess: () => form.reset(),
    });
};

// Form handling Inertia untuk persetujuan Line Head
const approveForm = useForm({});

const approveRequest = (id) => {
    approveForm.post(route("leave.approve", id));
};
</script>

<template>
    <Head title="Pengajuan Izin & Sakit" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pengajuan Izin & Sakit
            </h2>
        </template>

        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- CARD 1: FORM BUAT PENGAJUAN (Bisa Di-minimize) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden transition-all duration-300">
                <div class="p-4 sm:px-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">Buat Pengajuan</h3>

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
                <div v-show="showForm" class="p-6">
                    <form @submit.prevent="submitLeave" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipe Pengajuan</label>
                                <select
                                    v-model="form.type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="permission">Izin</option>
                                    <option value="sick">Sakit</option>
                                </select>
                                <div v-if="form.errors.type" class="text-red-500 text-xs mt-1">{{ form.errors.type }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                <input
                                    v-model="form.start_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <div v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">{{ form.errors.start_date }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                                <input
                                    v-model="form.end_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <div v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">{{ form.errors.end_date }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alasan</label>
                                <textarea
                                    v-model="form.reason"
                                    rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Isi alasan pengajuan..."
                                ></textarea>
                                <div v-if="form.errors.reason" class="text-red-500 text-xs mt-1">{{ form.errors.reason }}</div>
                            </div>
                        </div>

                        <div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition disabled:opacity-50"
                            >
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CARD 2: DAFTAR PENGAJUAN (Bisa Di-minimize) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden transition-all duration-300">
                <div class="p-4 sm:px-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Pengajuan</h3>

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

                <!-- Body Tabel -->
                <div v-show="showList" class="p-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                <th class="p-3">Nama Karyawan</th>
                                <th class="p-3">Tipe</th>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Alasan</th>
                                <th class="p-3">Status</th>
                                <th class="p-3" v-if="isLineHead">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <tr v-for="item in props.leaveRequests" :key="item.id">
                                <td class="p-3 font-medium text-gray-800">{{ item.user?.name || '-' }}</td>
                                <td class="p-3 uppercase">{{ item.type }}</td>
                                <td class="p-3">{{ item.start_date }} s/d {{ item.end_date }}</td>
                                <td class="p-3">{{ item.reason }}</td>
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
                                    <button
                                        v-if="item.status === 'pending'"
                                        @click="approveRequest(item.id)"
                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition"
                                    >
                                        Approve
                                    </button>
                                    <span v-else class="text-xs text-gray-400">-</span>
                                </td>
                            </tr>
                            <tr v-if="!props.leaveRequests || props.leaveRequests.length === 0">
                                <td :colspan="isLineHead ? 6 : 5" class="p-4 text-center text-gray-500">
                                    Belum ada data pengajuan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
