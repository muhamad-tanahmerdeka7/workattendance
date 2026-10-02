<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { usePermission } from "@/Composables/usePermission";

const showingSidebar = ref(true);
const { hasPermission } = usePermission();
const user = usePage().props.auth.user;
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex flex-col md:flex-row">
        <!-- HEADER MOBILE (Tampil Hanya di HP) -->
        <header class="md:hidden bg-blue-600 text-white px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-sm">
            <div class="flex items-center gap-2">
                <ApplicationLogo class="w-7 h-7 fill-current text-white shrink-0" />
                <span class="font-bold text-base tracking-wide whitespace-nowrap">WorkAttendance</span>
            </div>
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs uppercase text-white shrink-0">
                {{ user?.name ? user.name.charAt(0) : 'U' }}
            </div>
        </header>

        <!-- SIDEBAR DESKTOP (Tampil di Laptop / PC) -->
        <aside
            :class="showingSidebar ? 'w-64 lg:w-72' : 'w-20'"
            class="hidden md:flex bg-white border-r border-slate-200 min-h-screen flex-col justify-between transition-all duration-300 z-30 shrink-0"
        >
            <div>
                <!-- Header Sidebar & Logo (Disesuaikan agar teks WorkAttendance tidak terpotong) -->
                <div class="h-16 flex items-center justify-between px-4 border-b border-slate-100 gap-2">
                    <Link
                        v-if="showingSidebar"
                        :href="route('dashboard')"
                        class="flex items-center gap-2.5 min-w-0 overflow-hidden"
                    >
                        <ApplicationLogo class="block h-8 w-auto fill-current text-blue-600 shrink-0" />
                        <span class="font-extrabold text-slate-800 text-base lg:text-lg tracking-tight whitespace-nowrap shrink-0">
                            WorkAttendance
                        </span>
                    </Link>

                    <!-- Logo saat sidebar di-minimize -->
                    <Link
                        v-else
                        :href="route('dashboard')"
                        class="mx-auto"
                    >
                        <ApplicationLogo class="block h-8 w-auto fill-current text-blue-600 shrink-0" />
                    </Link>

                    <!-- Tombol Toggle Sidebar -->
                    <button
                        @click="showingSidebar = !showingSidebar"
                        type="button"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 focus:outline-none transition shrink-0"
                        :title="showingSidebar ? 'Ciutkan Sidebar' : 'Buka Sidebar'"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links Desktop -->
                <nav class="p-3 space-y-1.5">
                    <NavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Dashboard</span>
                    </NavLink>

                    <NavLink
                        :href="route('attendance.index')"
                        :active="route().current('attendance.index')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Presensi Saya</span>
                    </NavLink>

                    <NavLink
                        v-if="hasPermission('view all attendance')"
                        :href="route('attendance.monitoring')"
                        :active="route().current('attendance.monitoring')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Monitoring Tim</span>
                    </NavLink>

                    <NavLink
                        :href="route('leave.index')"
                        :active="route().current('leave.index')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Izin & Sakit</span>
                    </NavLink>

                    <NavLink
                        v-if="hasPermission('manage employees')"
                        :href="route('employees.index')"
                        :active="route().current('employees.*')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Kelola Karyawan</span>
                    </NavLink>
                </nav>
            </div>

            <!-- Profile Bottom Sidebar Desktop -->
            <div class="p-3 border-t border-slate-100">
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button type="button" class="flex items-center gap-3 w-full text-left p-2 rounded-xl hover:bg-slate-100 transition">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shrink-0 uppercase shadow-sm">
                                {{ user?.name ? user.name.charAt(0) : 'U' }}
                            </div>
                            <div v-if="showingSidebar" class="overflow-hidden">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ user?.name }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ user?.email }}</p>
                            </div>
                        </button>
                    </template>
                    <template #content>
                        <DropdownLink :href="route('profile.edit')">Profil Saya</DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </aside>

        <!-- KONTEN UTAMA HALAMAN -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden pb-16 md:pb-0">
            <header class="bg-white shadow-sm h-16 hidden md:flex items-center px-6" v-if="$slots.header">
                <slot name="header" />
            </header>

            <main class="flex-1 overflow-y-auto">
                <slot />
            </main>
        </div>

        <!-- BOTTOM NAVIGATION BAR MOBILE -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 py-1.5 px-1 z-50 shadow-lg flex items-center justify-around overflow-x-auto">
            <!-- 1. Dashboard -->
            <Link
                :href="route('dashboard')"
                :class="route().current('dashboard') ? 'text-blue-600 font-bold' : 'text-slate-400'"
                class="flex flex-col items-center justify-center min-w-[48px] py-1 px-1 transition-all"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[9px] min-[360px]:text-[10px] truncate max-w-[52px] mt-0.5">Dashboard</span>
            </Link>

            <!-- 2. Presensi Saya -->
            <Link
                :href="route('attendance.index')"
                :class="route().current('attendance.index') ? 'text-blue-600 font-bold' : 'text-slate-400'"
                class="flex flex-col items-center justify-center min-w-[48px] py-1 px-1 transition-all"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-[9px] min-[360px]:text-[10px] truncate max-w-[52px] mt-0.5">Presensi</span>
            </Link>

            <!-- 3. Monitoring Tim -->
            <Link
                v-if="hasPermission('view all attendance')"
                :href="route('attendance.monitoring')"
                :class="route().current('attendance.monitoring') ? 'text-blue-600 font-bold' : 'text-slate-400'"
                class="flex flex-col items-center justify-center min-w-[48px] py-1 px-1 transition-all"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[9px] min-[360px]:text-[10px] truncate max-w-[52px] mt-0.5">Tim</span>
            </Link>

            <!-- 4. Izin & Sakit -->
            <Link
                :href="route('leave.index')"
                :class="route().current('leave.index') ? 'text-blue-600 font-bold' : 'text-slate-400'"
                class="flex flex-col items-center justify-center min-w-[48px] py-1 px-1 transition-all"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-[9px] min-[360px]:text-[10px] truncate max-w-[52px] mt-0.5">Izin</span>
            </Link>

            <!-- 5. Kelola Karyawan -->
            <Link
                v-if="hasPermission('manage employees')"
                :href="route('employees.index')"
                :class="route().current('employees.*') ? 'text-blue-600 font-bold' : 'text-slate-400'"
                class="flex flex-col items-center justify-center min-w-[48px] py-1 px-1 transition-all"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-[9px] min-[360px]:text-[10px] truncate max-w-[52px] mt-0.5">Karyawan</span>
            </Link>

            <!-- 6. Profil -->
            <Link
                :href="route('profile.edit')"
                :class="route().current('profile.edit') ? 'text-blue-600 font-bold' : 'text-slate-400'"
                class="flex flex-col items-center justify-center min-w-[48px] py-1 px-1 transition-all"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[9px] min-[360px]:text-[10px] truncate max-w-[52px] mt-0.5">Profil</span>
            </Link>
        </nav>
    </div>
</template>
