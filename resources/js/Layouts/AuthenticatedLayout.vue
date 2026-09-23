<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import { Link } from "@inertiajs/vue3";
import { usePermission } from "@/Composables/usePermission";

const showingSidebar = ref(true);

// Inisialisasi fungsi pengecekan hak akses
const { hasPermission } = usePermission();
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- SIDEBAR -->
        <aside
            :class="showingSidebar ? 'w-64' : 'w-20'"
            class="bg-white border-r border-gray-200 min-h-screen flex flex-col justify-between transition-all duration-300 z-30"
        >
            <div>
                <!-- Header Sidebar & Logo -->
                <div
                    class="h-16 flex items-center justify-between px-4 border-b border-gray-100"
                >
                    <Link
                        v-if="showingSidebar"
                        :href="route('dashboard')"
                        class="flex items-center gap-3 overflow-hidden"
                    >
                        <ApplicationLogo
                            class="block h-9 w-auto fill-current text-gray-800 shrink-0"
                        />
                        <span
                            class="font-bold text-gray-800 text-lg whitespace-nowrap"
                        >
                            WorkAttendance
                        </span>
                    </Link>

                    <button
                        @click="showingSidebar = !showingSidebar"
                        :class="showingSidebar ? '' : 'mx-auto'"
                        class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none transition"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-2">
                    <!-- Dashboard (Dapat diakses Karyawan & Line Head) -->
                    <NavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition"
                    >
                        <svg
                            class="w-5 h-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                            />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap"
                            >Dashboard</span
                        >
                    </NavLink>

                    <!-- Presensi Saya (Dapat diakses Karyawan & Line Head) -->
                    <NavLink
                        :href="route('attendance.index')"
                        :active="route().current('attendance.index')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition"
                    >
                        <svg
                            class="w-5 h-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap"
                            >Presensi Saya</span
                        >
                    </NavLink>

                    <!-- Monitoring Tim (KHUSUS LINE HEAD / Punya Permission 'view all attendance') -->
                    <NavLink
                        v-if="hasPermission('view all attendance')"
                        :href="route('attendance.monitoring')"
                        :active="route().current('attendance.monitoring')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition"
                    >
                        <svg
                            class="w-5 h-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                            />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap"
                            >Monitoring Tim</span
                        >
                    </NavLink>

                    <!-- Izin & Sakit (Dapat diakses Karyawan & Line Head) -->
                    <NavLink
                        :href="route('leave.index')"
                        :active="route().current('leave.index')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition"
                    >
                        <svg
                            class="w-5 h-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap"
                            >Izin & Sakit</span
                        >
                    </NavLink>

                    <!-- Kelola Karyawan (KHUSUS LINE HEAD / Punya Permission 'manage employees') -->
                    <NavLink
                        v-if="hasPermission('manage employees')"
                        :href="route('employees.index')"
                        :active="route().current('employees.*')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition"
                    >
                        <svg
                            class="w-5 h-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                            />
                        </svg>
                        <span v-if="showingSidebar" class="whitespace-nowrap"
                            >Kelola Karyawan</span
                        >
                    </NavLink>
                </nav>
            </div>

            <!-- Profile / User Bottom Sidebar -->
            <div class="p-4 border-t border-gray-100">
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button
                            type="button"
                            class="flex items-center gap-3 w-full text-left p-2 rounded-lg hover:bg-gray-100 transition"
                        >
                            <div
                                class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shrink-0 uppercase"
                            >
                                {{ $page.props.auth.user?.name ? $page.props.auth.user.name.charAt(0) : 'U' }}
                            </div>
                            <div v-if="showingSidebar" class="overflow-hidden">
                                <p
                                    class="text-sm font-semibold text-gray-800 truncate"
                                >
                                    {{ $page.props.auth.user?.name }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ $page.props.auth.user?.email }}
                                </p>
                            </div>
                        </button>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('profile.edit')">
                            Profile
                        </DropdownLink>
                        <DropdownLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                        >
                            Log Out
                        </DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </aside>

        <!-- AREA KONTEN UTAMA -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header
                class="bg-white shadow-sm h-16 flex items-center px-6"
                v-if="$slots.header"
            >
                <slot name="header" />
            </header>

            <main class="flex-1 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
