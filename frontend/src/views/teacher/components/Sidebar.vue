<template>
    <!-- Mobile Overlay -->
    <div v-if="isOpen" @click="$emit('close')" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] lg:hidden animate-in fade-in duration-300"></div>

    <div :class="[
        'h-screen w-64 bg-slate-900 text-slate-300 flex flex-col fixed left-0 top-0 z-[70] transition-transform duration-300 lg:translate-x-0',
        isOpen ? 'translate-x-0' : '-translate-x-full'
    ]">
        <div class="p-6 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img :src="logoUrl" alt="Zewd" class="w-14 h-14 rounded-xl bg-white p-1.5 shadow-lg shadow-indigo-500/20 object-contain" />
            </div>
            <!-- Mobile Close Button -->
            <button @click="$emit('close')" class="lg:hidden p-2 text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            <button v-for="item in menuItems" :key="item.id" @click="$emit('select', item.id); $emit('close')"
                :class="[
                    'w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group',
                    activeTab === item.id 
                        ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' 
                        : 'hover:bg-slate-800 hover:text-white'
                ]">
                <component :is="item.icon" class="w-5 h-5" />
                <span class="font-medium">{{ item.label }}</span>
            </button>
        </nav>

        <div class="p-4 border-t border-slate-800">
            <div class="p-4 bg-slate-800/50 rounded-2xl flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold overflow-hidden border-2 border-slate-700">
                    <img v-if="teacher?.profile_image_path" :src="teacher.profile_image_path" class="w-full h-full object-cover" />
                    <span v-else>{{ teacherName?.charAt(0) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ teacherName }}</p>
                    <p class="text-[10px] text-slate-400 truncate uppercase tracking-wider font-semibold">{{ teacher?.academy_name || 'Academy' }}</p>
                </div>
                <button @click="$emit('logout')" class="text-slate-500 hover:text-red-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import logoUrl from '../../../assets/Zewd.png';

const props = defineProps({
    activeTab: String,
    teacher: Object,
    teacherName: String,
    isOpen: Boolean
});

defineEmits(['select', 'logout', 'close']);

const menuItems = [
    { id: 'Overview', label: 'Dashboard', icon: 'DashboardIcon' },
    { id: 'Students', label: 'My Students', icon: 'StudentsIcon' },
    { id: 'Materials', label: 'Learning Hub', icon: 'MaterialsIcon' },
    { id: 'Live Classes', label: 'Virtual Room', icon: 'LiveIcon' },
    { id: 'Academics', label: 'Curriculum', icon: 'AcademicsIcon' },
    { id: 'Settings', label: 'Profile', icon: 'SettingsIcon' },
];

// Simple Icon Components as simple SVGs for now
const DashboardIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>' };
const StudentsIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>' };
const MaterialsIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>' };
const LiveIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>' };
const AcademicsIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>' };
const SettingsIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>' };
</script>
