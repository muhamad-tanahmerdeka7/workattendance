<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    employees: Array,
});

const form = useForm({
    name: "",
    email: "",
    password: "",
    employee_code: "",
    department: "",
    position: "",
    join_date: "",
});

const submitEmployee = () => {
    form.post(route("employees.store"), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Kelola Karyawan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Data Karyawan
            </h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Form Tambah Karyawan Baru -->
            <div
                class="bg-white p-6 rounded-lg shadow-sm border border-gray-100"
            >
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    Tambah Karyawan Baru
                </h3>
                <form
                    @submit.prevent="submitEmployee"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                >
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Nama Lengkap</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-md border-gray-300 text-sm"
                            required
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Email</label
                        >
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full rounded-md border-gray-300 text-sm"
                            required
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Password</label
                        >
                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full rounded-md border-gray-300 text-sm"
                            required
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >NIK/Kode Karyawan</label
                        >
                        <input
                            v-model="form.employee_code"
                            type="text"
                            class="w-full rounded-md border-gray-300 text-sm"
                            required
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Departemen</label
                        >
                        <input
                            v-model="form.department"
                            type="text"
                            class="w-full rounded-md border-gray-300 text-sm"
                            required
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Jabatan</label
                        >
                        <input
                            v-model="form.position"
                            type="text"
                            class="w-full rounded-md border-gray-300 text-sm"
                            required
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Tanggal Bergabung</label
                        >
                        <input
                            v-model="form.join_date"
                            type="date"
                            class="w-full rounded-md border-gray-300 text-sm"
                            required
                        />
                    </div>
                    <div class="md:col-span-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md font-medium text-sm hover:bg-blue-700"
                        >
                            Simpan Karyawan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Data Karyawan -->
            <div
                class="bg-white p-6 rounded-lg shadow-sm border border-gray-100"
            >
                <h3 class="text-lg font-bold text-gray-800 mb-4">
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
                                <td class="px-4 py-3">{{ emp.user.name }}</td>
                                <td class="px-4 py-3">{{ emp.user.email }}</td>
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
