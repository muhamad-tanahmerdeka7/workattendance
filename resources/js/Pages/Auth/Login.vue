<script setup>
import { ref } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <!-- KONTAINER UTAMA LENGKAP PENUH DARI UJUNG KE UJUNG -->
    <div class="min-h-screen w-full bg-slate-900 flex items-center justify-center p-4 sm:p-6 lg:p-8 relative overflow-hidden">

        <!-- DEKORASI BACKGROUND GRADIENT ELEGAN -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- KARTU UTAMA LOGIN -->
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl shadow-slate-950/50 border border-slate-100 overflow-hidden relative z-10 my-auto">

            <!-- HEADER LOGO & HEADING -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-6 sm:p-8 text-white text-center relative overflow-hidden">
                <!-- Hiasan Wave Transparan -->
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-md shadow-inner mb-3 border border-white/20">
                    <ApplicationLogo class="w-9 h-9 fill-current text-white" />
                </div>

                <h1 class="text-xl sm:text-2xl font-black tracking-tight">
                    Work<span class="text-blue-200">Attendance</span>
                </h1>
                <p class="text-xs text-blue-100 font-medium mt-1">
                    Sistem Manajemen Presensi & Kehadiran Karyawan
                </p>
            </div>

            <!-- BODY FORM -->
            <div class="p-6 sm:p-8 space-y-5">

                <div class="space-y-1">
                    <h2 class="text-base sm:text-lg font-bold text-slate-800">Selamat Datang Kembali</h2>
                    <p class="text-xs text-slate-400">Silakan masukkan akun Anda untuk melanjutkan</p>
                </div>

                <div v-if="status" class="text-xs font-semibold text-emerald-700 bg-emerald-50 p-3.5 rounded-2xl border border-emerald-100">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-4">

                    <!-- INPUT EMAIL -->
                    <div>
                        <InputLabel for="email" value="Email / Username" class="text-xs font-bold text-slate-700 mb-1" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <TextInput
                                id="email"
                                type="email"
                                class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="nama@perusahaan.com"
                            />
                        </div>
                        <InputError class="mt-1" :message="form.errors.email" />
                    </div>

                    <!-- INPUT PASSWORD -->
                    <div>
                        <InputLabel for="password" value="Password" class="text-xs font-bold text-slate-700 mb-1" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <TextInput
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full pl-10 pr-10 py-2.5 text-xs sm:text-sm rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition"
                            >
                                <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.98 8.98 0 013.122-.563c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.002 3.538m-3.411 2.38A3.001 3.001 0 0112 15c-.657 0-1.264-.212-1.758-.57M12 9a3 3 0 00-3 3c0 .324.052.635.148.927" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <InputError class="mt-1" :message="form.errors.password" />
                    </div>

                    <!-- REMEMBER ME & LUPA PASSWORD -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                            <span class="text-slate-600 font-medium">Ingat Saya</span>
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-blue-600 hover:text-blue-700 font-bold transition"
                        >
                            Lupa Password?
                        </Link>
                    </div>

                    <!-- TOMBOL SUBMIT LOGIN -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-blue-600/30 text-xs sm:text-sm transition disabled:opacity-50"
                        >
                            Masuk ke Akun
                        </button>
                    </div>
                </form>

            </div>

            <!-- FOOTER HAK CIPTA -->
            <div class="bg-slate-50 p-4 border-t border-slate-100 text-center text-[11px] text-slate-400 font-medium">
                &copy; {{ new Date().getFullYear() }} WorkAttendance. All rights reserved Muhamad S.Kom.
            </div>

        </div>

    </div>
</template>
