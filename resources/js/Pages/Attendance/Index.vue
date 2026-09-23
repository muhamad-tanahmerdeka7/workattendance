<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { usePermission } from "@/Composables/usePermission";

const props = defineProps({
    todayAttendance: Object,
    attendances: Array,
});

const { hasPermission } = usePermission();

const formCheckIn = useForm({ notes: "" });
const formCheckOut = useForm({});

const submitCheckIn = () => {
    formCheckIn.post(route("attendance.checkIn"));
};

const submitCheckOut = () => {
    formCheckOut.post(route("attendance.checkOut"));
};
</script>

<template>
    <Head title="Presensi Saya" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Presensi Kehadiran
            </h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Card Absen Hari Ini -->
            <div
                v-if="hasPermission('create attendance')"
                class="bg-white p-6 rounded-lg shadow-sm border border-gray-100"
            >
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    Absen Hari Ini
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Absen Masuk -->
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <p class="text-sm font-semibold text-gray-600">
                            Jam Masuk
                        </p>
                        <p class="text-2xl font-bold text-gray-800 my-2">
                            {{ props.todayAttendance?.time_in || "--:--" }}
                        </p>
                        <form
                            v-if="!props.todayAttendance?.time_in"
                            @submit.prevent="submitCheckIn"
                        >
                            <input
                                v-model="formCheckIn.notes"
                                type="text"
                                placeholder="Catatan (opsional)"
                                class="w-full text-sm rounded-md border-gray-300 mb-3"
                            />
                            <button
                                type="submit"
                                :disabled="formCheckIn.processing"
                                class="w-full bg-blue-600 text-white py-2 rounded-md font-medium hover:bg-blue-700"
                            >
                                Absen Masuk
                            </button>
                        </form>
                        <span
                            v-else
                            class="text-xs text-green-600 font-semibold"
                            >Sudah Absen Masuk</span
                        >
                    </div>

                    <!-- Absen Pulang -->
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <p class="text-sm font-semibold text-gray-600">
                            Jam Pulang
                        </p>
                        <p class="text-2xl font-bold text-gray-800 my-2">
                            {{ props.todayAttendance?.time_out || "--:--" }}
                        </p>
                        <form
                            v-if="
                                props.todayAttendance?.time_in &&
                                !props.todayAttendance?.time_out
                            "
                            @submit.prevent="submitCheckOut"
                        >
                            <button
                                type="submit"
                                :disabled="formCheckOut.processing"
                                class="w-full bg-red-600 text-white py-2 rounded-md font-medium hover:bg-red-700"
                            >
                                Absen Pulang
                            </button>
                        </form>
                        <span
                            v-else-if="props.todayAttendance?.time_out"
                            class="text-xs text-green-600 font-semibold"
                            >Sudah Absen Pulang</span
                        >
                        <span v-else class="text-xs text-gray-400"
                            >Belum Absen Masuk</span
                        >
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Presensi -->
            <div
                class="bg-white p-6 rounded-lg shadow-sm border border-gray-100"
            >
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    Riwayat Kehadiran
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50"
                        >
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Masuk</th>
                                <th class="px-4 py-3">Pulang</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in props.attendances"
                                :key="item.id"
                                class="border-b"
                            >
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ item.date }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ item.time_in || "-" }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ item.time_out || "-" }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="{
                                            'bg-green-100 text-green-800':
                                                item.status === 'present',
                                            'bg-yellow-100 text-yellow-800':
                                                item.status === 'late',
                                            'bg-red-100 text-red-800':
                                                item.status === 'absent',
                                        }"
                                        class="px-2 py-1 rounded text-xs font-semibold uppercase"
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ item.notes || "-" }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 
