<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { usePermission } from "@/Composables/usePermission";

const showingSidebar = ref(true);
const showingMobileDrawer = ref(false);
const { hasPermission } = usePermission();
const user = usePage().props.auth.user;

/**
 * Helper aman untuk memanggil route().
 * Mencegah aplikasi crash/blank jika nama route baru belum dibuat di web.php
 */
const safeRoute = (name, params = {}) => {
    try {
        return route().has(name) ? route(name, params) : "#";
    } catch (e) {
        return "#";
    }
};

/**
 * Helper aman untuk mengecek status aktif menu
 */
const isRouteActive = (pattern) => {
    try {
        return route().current(pattern);
    } catch (e) {
        return false;
    }
};
</script>

<template>
    <div class="flex flex-col min-h-screen bg-slate-50 md:flex-row">
        <!-- HEADER MOBILE (Tampil Hanya di HP) -->
        <header class="sticky top-0 z-40 flex items-center justify-between px-4 py-3 text-white bg-blue-600 shadow-sm md:hidden">
            <div class="flex items-center gap-2">
                <ApplicationLogo class="text-white fill-current w-7 h-7 shrink-0" />
                <span class="text-base font-bold tracking-wide whitespace-nowrap">WorkAttendance</span>
            </div>
            <div class="flex items-center gap-2">
                <Link
                    :href="safeRoute('profile.edit')"
                    class="flex items-center justify-center w-8 h-8 text-xs font-bold text-white uppercase rounded-full bg-white/20 shrink-0"
                    title="Profil Saya"
                >
                    {{ user?.name ? user.name.charAt(0) : 'U' }}
                </Link>
                <Link
                    :href="safeRoute('logout')"
                    method="post"
                    as="button"
                    class="p-1 transition rounded-lg text-white/80 hover:text-white"
                    title="Keluar"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </Link>
            </div>
        </header>

        <!-- SIDEBAR DESKTOP (Tampil di Laptop / PC) -->
        <aside
            :class="showingSidebar ? 'w-64 lg:w-72' : 'w-20'"
            class="sticky top-0 z-30 flex-col justify-between hidden h-screen transition-all duration-300 bg-white border-r md:flex border-slate-200 shrink-0"
        >
            <div class="flex flex-col flex-1 min-h-0">
                <!-- Header Sidebar & Logo -->
                <div class="flex items-center justify-between h-16 gap-2 px-4 border-b border-slate-100 shrink-0">
                    <Link
                        v-if="showingSidebar"
                        :href="safeRoute('dashboard')"
                        class="flex items-center gap-2.5 min-w-0 overflow-hidden"
                    >
                        <ApplicationLogo class="block w-auto h-8 text-blue-600 fill-current shrink-0" />
                        <span class="text-base font-extrabold tracking-tight text-slate-800 lg:text-lg whitespace-nowrap shrink-0">
                            WorkAttendance
                        </span>
                    </Link>

                    <!-- Logo saat sidebar di-minimize -->
                    <Link
                        v-else
                        :href="safeRoute('dashboard')"
                        class="mx-auto"
                    >
                        <ApplicationLogo class="block w-auto h-8 text-blue-600 fill-current shrink-0" />
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

                <!-- Navigation Links Desktop (Scrollable) -->
                <nav class="flex-1 p-3 space-y-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200">
                    <!-- ================= UTAMA ================= -->
                    <div v-if="showingSidebar" class="px-3 pt-1 pb-1 text-[10px] font-bold tracking-wider uppercase text-slate-400">
                        Menu Utama
                    </div>

                    <NavLink
                        :href="safeRoute('dashboard')"
                        :active="isRouteActive('dashboard')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Dashboard"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Dashboard</span>
                    </NavLink>

                    <!-- ================= KEPEGAWAIAN & ABSENSI ================= -->
                    <div v-if="showingSidebar" class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-wider uppercase text-slate-400">
                        Kepegawaian & Absensi
                    </div>
                    <div v-else class="my-2 border-t border-slate-100"></div>

                    <!-- Presensi Saya -->
                    <NavLink
                        :href="safeRoute('attendance.index')"
                        :active="isRouteActive('attendance.index')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Presensi Saya"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Presensi Saya</span>
                    </NavLink>

                    <!-- Monitoring Tim -->
                    <NavLink
                        v-if="hasPermission('view all attendance')"
                        :href="safeRoute('attendance.monitoring')"
                        :active="isRouteActive('attendance.monitoring')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Monitoring Tim"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Monitoring Tim</span>
                    </NavLink>

                    <!-- Jadwal Tim -->
                    <NavLink
                        :href="safeRoute('shifts.schedule')"
                        :active="isRouteActive('shifts.schedule')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Jadwal Tim"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Jadwal Tim</span>
                    </NavLink>

                    <!-- Atur Shift Kerja -->
                    <NavLink
                        v-if="hasPermission('manage employees')"
                        :href="safeRoute('shifts.index')"
                        :active="isRouteActive('shifts.index')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Atur Shift Kerja"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Atur Shift Kerja</span>
                    </NavLink>

                    <!-- Lembur & Tukar Shift (BARU) -->
                    <NavLink
                        :href="safeRoute('overtime.index')"
                        :active="isRouteActive('overtime.*')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Lembur & Tukar Shift"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Lembur & Tukar Shift</span>
                    </NavLink>

                    <!-- Izin & Sakit -->
                    <NavLink
                        :href="safeRoute('leave.index')"
                        :active="isRouteActive('leave.index')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Izin & Sakit"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Izin & Sakit</span>
                    </NavLink>

                    <!-- Cuti & Dispensasi (BARU) -->
                    <NavLink
                        :href="safeRoute('leave.dispensation.index')"
                        :active="isRouteActive('leave.dispensation.*')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Cuti & Dispensasi"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Cuti & Dispensasi</span>
                    </NavLink>

                    <!-- Log Absensi Mesin / GPS (BARU) -->
                    <NavLink
                        v-if="hasPermission('view all attendance')"
                        :href="safeRoute('attendance.logs')"
                        :active="isRouteActive('attendance.logs')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Log Mesin & GPS"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Log Mesin & GPS</span>
                    </NavLink>

                    <!-- Monitoring Gaji -->
                    <NavLink
                        :href="safeRoute('salaries.index')"
                        :active="isRouteActive('salaries.*')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Monitoring Gaji"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Monitoring Gaji</span>
                    </NavLink>

                    <!-- Kelola Karyawan -->
                    <NavLink
                        v-if="hasPermission('manage employees')"
                        :href="safeRoute('employees.index')"
                        :active="isRouteActive('employees.*')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Kelola Karyawan"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Kelola Karyawan</span>
                    </NavLink>

                    <!-- ================= INVENTORI & GUDANG (BARU) ================= -->
                    <div v-if="showingSidebar" class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-wider uppercase text-slate-400">
                        Inventori & Bahan Baku
                    </div>
                    <div v-else class="my-2 border-t border-slate-100"></div>

                    <!-- Katalog Bahan Baku -->
                    <NavLink
                        :href="safeRoute('materials.index')"
                        :active="isRouteActive('materials.*')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Katalog Bahan Baku"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Katalog Bahan Baku</span>
                    </NavLink>

                    <!-- Stok & Lokasi Rak -->
                    <NavLink
                        :href="safeRoute('inventory.stocks')"
                        :active="isRouteActive('inventory.stocks')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Stok & Lokasi Rak"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Stok & Lokasi Rak</span>
                    </NavLink>

                    <!-- Bahan Masuk (Inbound) -->
                    <NavLink
                        :href="safeRoute('inventory.inbound')"
                        :active="isRouteActive('inventory.inbound')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Bahan Masuk (Inbound)"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Bahan Masuk</span>
                    </NavLink>

                    <!-- Bahan Keluar / Produksi (Outbound) -->
                    <NavLink
                        :href="safeRoute('inventory.outbound')"
                        :active="isRouteActive('inventory.outbound')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Bahan Keluar / Produksi"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Bahan Keluar / Produksi</span>
                    </NavLink>

                    <!-- Stock Opname -->
                    <NavLink
                        :href="safeRoute('inventory.opname')"
                        :active="isRouteActive('inventory.opname')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Stock Opname"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Stock Opname</span>
                    </NavLink>

                    <!-- ================= ORDER & PENGADAAN (BARU) ================= -->
                    <div v-if="showingSidebar" class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-wider uppercase text-slate-400">
                        Order & Pengadaan (PO)
                    </div>
                    <div v-else class="my-2 border-t border-slate-100"></div>

                    <!-- Permintaan Pembelian (PR) -->
                    <NavLink
                        :href="safeRoute('purchase-requests.index')"
                        :active="isRouteActive('purchase-requests.*')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Permintaan Beli (PR)"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Permintaan Beli (PR)</span>
                    </NavLink>

                    <!-- Purchase Order (PO) Bahan Baku -->
                    <NavLink
                        :href="safeRoute('purchase-orders.index')"
                        :active="isRouteActive('purchase-orders.*')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Order Bahan Baku (PO)"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Purchase Order (PO)</span>
                    </NavLink>

                    <!-- Data Supplier / Vendor -->
                    <NavLink
                        :href="safeRoute('suppliers.index')"
                        :active="isRouteActive('suppliers.*')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Data Pemasok / Vendor"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Daftar Pemasok</span>
                    </NavLink>

                    <!-- ================= LAPORAN & SISTEM (BARU) ================= -->
                    <div v-if="showingSidebar" class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-wider uppercase text-slate-400">
                        Laporan & Sistem
                    </div>
                    <div v-else class="my-2 border-t border-slate-100"></div>

                    <!-- Laporan Terpadu -->
                    <NavLink
                        :href="safeRoute('reports.index')"
                        :active="isRouteActive('reports.*')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Laporan Terpadu"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Laporan Terpadu</span>
                    </NavLink>

                    <!-- Hak Akses & Role -->
                    <NavLink
                        v-if="hasPermission('manage employees')"
                        :href="safeRoute('roles.index')"
                        :active="isRouteActive('roles.*')"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition rounded-xl"
                        title="Hak Akses & Role"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap">Hak Akses & Role</span>
                    </NavLink>
                </nav>
            </div>
        </aside>

        <!-- KONTEN UTAMA HALAMAN -->
        <div class="flex flex-col flex-1 min-w-0 pb-16 overflow-hidden md:pb-0">
            <!-- NAVBAR ATAS DESKTOP (Termasuk Slot Header dan Profil Pojok Kanan Atas) -->
            <header class="sticky top-0 z-30 items-center justify-between hidden h-16 px-6 bg-white border-b border-slate-200 md:flex">
                <div>
                    <slot name="header" />
                </div>

                <!-- DROPDOWN PROFIL POJOK KANAN ATAS -->
                <div class="ml-auto">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                type="button"
                                class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-100 transition focus:outline-none"
                            >
                                <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white uppercase bg-blue-600 rounded-full shadow-sm shrink-0">
                                    {{ user?.name ? user.name.charAt(0) : 'U' }}
                                </div>
                                <div class="text-left hidden lg:block max-w-[140px]">
                                    <p class="text-xs font-bold truncate text-slate-800">{{ user?.name }}</p>
                                    <p class="text-[11px] text-slate-400 truncate">{{ user?.email }}</p>
                                </div>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Masuk Sebagai</p>
                                <p class="text-xs font-bold text-gray-800 truncate mt-0.5">{{ user?.name }}</p>
                                <p class="text-[11px] text-gray-500 truncate">{{ user?.email }}</p>
                            </div>

                            <DropdownLink :href="safeRoute('profile.edit')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Profil Saya</span>
                            </DropdownLink>

                            <DropdownLink :href="safeRoute('logout')" method="post" as="button" class="flex items-center gap-2 text-red-600 hover:text-red-700 hover:bg-red-50">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Log Out</span>
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <slot />
            </main>
        </div>

        <!-- DRAWER MENU LENGKAP MOBILE (Pop-up saat klik tombol "Menu" di HP) -->
        <div
            v-if="showingMobileDrawer"
            class="fixed inset-0 z-50 flex flex-col justify-end bg-slate-900/50 backdrop-blur-xs md:hidden"
            @click.self="showingMobileDrawer = false"
        >
            <div class="bg-white rounded-t-2xl max-h-[82vh] flex flex-col shadow-2xl overflow-hidden animate-in slide-in-from-bottom">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                    <span class="text-sm font-bold text-slate-800">Semua Menu Aplikasi</span>
                    <button
                        @click="showingMobileDrawer = false"
                        class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-4 pb-20 space-y-4 overflow-y-auto">
                    <!-- Grup Kepegawaian -->
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Kepegawaian & Shift</p>
                        <div class="grid grid-cols-2 gap-2">
                            <Link :href="safeRoute('leave.index')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>📅 Izin & Sakit</span>
                            </Link>
                            <Link :href="safeRoute('paid-leaves.index')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>✅ Cuti Tahunan</span>
                            </Link>
                            <Link :href="safeRoute('overtime.index')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>⚡ Lembur & Shift</span>
                            </Link>
                            <Link :href="safeRoute('salaries.index')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>💵 Monitoring Gaji</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Grup Inventori & Bahan Baku -->
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Inventori & Bahan Baku</p>
                        <div class="grid grid-cols-2 gap-2">
                            <Link :href="safeRoute('materials.index')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>📦 Katalog Bahan</span>
                            </Link>
                            <Link :href="safeRoute('inventory.stocks')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>🗄️ Stok & Rak</span>
                            </Link>
                            <Link :href="safeRoute('inventory.inbound')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>📥 Bahan Masuk</span>
                            </Link>
                            <Link :href="safeRoute('inventory.outbound')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>📤 Bahan Keluar</span>
                            </Link>
                            <Link :href="safeRoute('inventory.opname')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>📋 Stock Opname</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Grup Order & PO -->
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Order & Pengadaan (PO)</p>
                        <div class="grid grid-cols-2 gap-2">
                            <Link :href="safeRoute('purchase-requests.index')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>📝 Request Beli (PR)</span>
                            </Link>
                            <Link :href="safeRoute('purchase-orders.index')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>🛒 Order Bahan (PO)</span>
                            </Link>
                            <Link :href="safeRoute('suppliers.index')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>🏢 Data Supplier</span>
                            </Link>
                            <Link :href="safeRoute('reports.index')" @click="showingMobileDrawer = false" class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-medium text-slate-700">
                                <span>📊 Laporan Terpadu</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTTOM NAVIGATION BAR MOBILE -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 py-1.5 px-1 z-50 shadow-lg flex items-center justify-around overflow-x-auto">
            <!-- 1. Dashboard -->
            <Link
                :href="safeRoute('dashboard')"
                :class="isRouteActive('dashboard') ? 'text-blue-600 font-bold' : 'text-slate-400'"
                class="flex flex-col items-center justify-center min-w-[48px] py-1 px-1 transition-all"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[9px] min-[360px]:text-[10px] truncate max-w-[52px] mt-0.5">Dashboard</span>
            </Link>

            <!-- 2. Presensi Saya -->
            <Link
                :href="safeRoute('attendance.index')"
                :class="isRouteActive('attendance.index') ? 'text-blue-600 font-bold' : 'text-slate-400'"
                class="flex flex-col items-center justify-center min-w-[48px] py-1 px-1 transition-all"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-[9px] min-[360px]:text-[10px] truncate max-w-[52px] mt-0.5">Presensi</span>
            </Link>

            <!-- 3. Jadwal Tim -->
            <Link
                :href="safeRoute('shifts.schedule')"
                :class="isRouteActive('shifts.schedule') ? 'text-blue-600 font-bold' : 'text-slate-400'"
                class="flex flex-col items-center justify-center min-w-[48px] py-1 px-1 transition-all"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <span class="text-[9px] min-[360px]:text-[10px] truncate max-w-[52px] mt-0.5">Jadwal</span>
            </Link>

            <!-- 4. Inventori Bahan Baku (Mobile Shortcut) -->
            <Link
                :href="safeRoute('materials.index')"
                :class="isRouteActive('materials.*') || isRouteActive('inventory.*') ? 'text-blue-600 font-bold' : 'text-slate-400'"
                class="flex flex-col items-center justify-center min-w-[48px] py-1 px-1 transition-all"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="text-[9px] min-[360px]:text-[10px] truncate max-w-[52px] mt-0.5">Inventori</span>
            </Link>

            <!-- 5. Tombol Buka Semua Menu (Drawer Mobile) -->
            <button
                type="button"
                @click="showingMobileDrawer = !showingMobileDrawer"
                :class="showingMobileDrawer ? 'text-blue-600 font-bold' : 'text-slate-400'"
                class="flex flex-col items-center justify-center min-w-[48px] py-1 px-1 transition-all focus:outline-none"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="text-[9px] min-[360px]:text-[10px] truncate max-w-[52px] mt-0.5">Menu</span>
            </button>
        </nav>
    </div>
</template>
