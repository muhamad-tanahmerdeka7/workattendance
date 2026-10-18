<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    employees: Array,
    lineHeads: Array, // Opsional jika ingin memilih atasan langsung
});

const form = useForm({
    name: "",
    email: "",
    password: "",
    role: "Karyawan", // Default role hak akses sistem
    employee_code: "",
    department: "",
    position: "",
    join_date: "",
});

const submitEmployee = () => {
    form.post(route("employees.store"), {
        onSuccess: () => {
            form.reset();
            form.role = "Karyawan";
        },
    });
};
</script>

<template>
    <Head title="Kelola Karyawan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Manajemen Data Karyawan
            </h2>
        </template>

        <div class="py-12 mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            <!-- Form Tambah Karyawan Baru -->
            <div
                class="p-6 bg-white border border-gray-100 rounded-lg shadow-sm"
            >
                <h3 class="mb-4 text-lg font-bold text-gray-800">
                    Tambah Karyawan Baru
                </h3>
                <form
                    @submit.prevent="submitEmployee"
                    autocomplete="off"
                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                >
                    <div>
                        <label
                            class="block mb-1 text-sm font-medium text-gray-700"
                            >Nama Lengkap</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full text-sm border-gray-300 rounded-md"
                            placeholder="Masukkan nama lengkap"
                            required
                        />
                    </div>

                    <div>
                        <label
                            class="block mb-1 text-sm font-medium text-gray-700"
                            >Email Akun Login</label
                        >
                        <!-- autocomplete="new-password" mencegah browser autofill email muhamad -->
                        <input
                            v-model="form.email"
                            type="email"
                            autocomplete="new-password"
                            class="w-full text-sm border-gray-300 rounded-md"
                            placeholder="nama@perusahaan.com"
                            required
                        />
                    </div>

                    <div>
                        <label
                            class="block mb-1 text-sm font-medium text-gray-700"
                            >Password Akun</label
                        >
                        <!-- autocomplete="new-password" mencegah browser autofill password muhamad -->
                        <input
                            v-model="form.password"
                            type="password"
                            autocomplete="new-password"
                            class="w-full text-sm border-gray-300 rounded-md"
                            placeholder="Minimal 6 atau 8 karakter"
                            required
                        />
                    </div>

                    <div>
                        <label
                            class="block mb-1 text-sm font-medium text-gray-700"
                            >Role Hak Akses Sistem</label
                        >
                        <select
                            v-model="form.role"
                            class="w-full text-sm border-gray-300 rounded-md"
                            required
                        >
                            <option value="Karyawan">Karyawan (Absensi Mandiri)</option>
                            <option value="Line Head">Line Head (Kelola Shift & Pantau Tim)</option>
                            <option value="HR Manager">HR Manager (Akses Penuh / Super Admin)</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block mb-1 text-sm font-medium text-gray-700"
                            >NIK / Kode Karyawan</label
                        >
                        <input
                            v-model="form.employee_code"
                            type="text"
                            class="w-full text-sm border-gray-300 rounded-md"
                            placeholder="Contoh: 8160649"
                            required
                        />
                    </div>

                    <div>
                        <label
                            class="block mb-1 text-sm font-medium text-gray-700"
                            >Departemen</label
                        >
                        <input
                            v-model="form.department"
                            type="text"
                            class="w-full text-sm border-gray-300 rounded-md"
                            placeholder="Contoh: PLANT 8"
                            required
                        />
                    </div>

                    <div>
                        <label
                            class="block mb-1 text-sm font-medium text-gray-700"
                            >Jabatan Kerja</label
                        >
                        <input
                            v-model="form.position"
                            type="text"
                            class="w-full text-sm border-gray-300 rounded-md"
                            placeholder="Contoh: OPERATOR / SUPERVISOR"
                            required
                        />
                    </div>

                    <div>
                        <label
                            class="block mb-1 text-sm font-medium text-gray-700"
                            >Tanggal Bergabung</label
                        >
                        <input
                            v-model="form.join_date"
                            type="date"
                            class="w-full text-sm border-gray-300 rounded-md"
                            required
                        />
                    </div>

                    <div class="md:col-span-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                        >
                            Simpan Karyawan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Data Karyawan -->
            <div
                class="p-6 bg-white border border-gray-100 rounded-lg shadow-sm"
            >
                <h3 class="mb-4 text-lg font-bold text-gray-800">
                    Daftar Karyawan
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50"
                        >
                            <tr>
                                <th class="px-4 py-3">Kode</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Departemen</th>
                                <th class="px-4 py-3">Jabatan</th>
                                <th class="px-4 py-3">Tgl Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="emp in props.employees"
                                :key="emp.id"
                                class="border-b"
                            >
                                <td
                                    class="px-4 py-3 font-semibold text-gray-900"
                                >
                                    {{ emp.employee_code }}
                                </td>
                                <td class="px-4 py-3">{{ emp.user?.name }}</td>
                                <td class="px-4 py-3">{{ emp.user?.email }}</td>
                                <td class="px-4 py-3">{{ emp.department }}</td>
                                <td class="px-4 py-3">{{ emp.position }}</td>
                                <td class="px-4 py-3">{{ emp.join_date }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
