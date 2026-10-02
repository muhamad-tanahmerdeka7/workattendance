<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <Head title="Profil Saya" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profil Saya</h2>
        </template>

        <div class="py-6 md:py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Form Informasi Profil -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl border border-slate-100">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <!-- Form Ubah Password -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl border border-slate-100">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <!-- Form Hapus Akun -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl border border-slate-100">
                <DeleteUserForm class="max-w-xl" />
            </div>

            <!-- TOMBOL LOGOUT (Tambahkan di bagian paling bawah ini) -->
            <div class="p-4 sm:p-6 bg-white shadow sm:rounded-2xl border border-rose-100">
                <div class="max-w-xl space-y-3">
                    <h3 class="text-base font-bold text-slate-800">Sesi Akun</h3>
                    <p class="text-xs text-slate-500">
                        Keluar dari sesi akun WorkAttendance di perangkat ini.
                    </p>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold py-2.5 px-5 rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar dari Akun (Log Out)</span>
                    </Link>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
