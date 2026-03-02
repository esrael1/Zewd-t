<template>
    <div class="space-y-8">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Welcome back, {{ studentName }}! 👋</h1>
                <p class="text-slate-500 font-medium mt-1">Study hard, dream big. Your academy journey continues.</p>
            </div>
            <div class="flex items-center space-x-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
                <div class="px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl font-bold text-sm">
                    {{ grade || 'Scanning Grade...' }}
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="stat in statCards" :key="stat.label" 
                class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-2xl mb-4 flex items-center justify-center transition-transform group-hover:scale-110" :class="stat.color">
                    <component :is="stat.icon" class="w-6 h-6" />
                </div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">{{ stat.label }}</p>
                <p class="text-4xl font-black text-slate-900 leading-none">{{ stat.value }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Learning Motivation Card -->
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-[32px] p-8 text-white relative overflow-hidden shadow-2xl shadow-emerald-500/20">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-4">Ready for your next session?</h2>
                    <p class="text-emerald-100 font-medium mb-8 max-w-md">Access your virtual room and join your teacher in a live interactive class.</p>
                    <button @click="$emit('goToLive')" class="bg-white text-emerald-700 px-8 py-4 rounded-2xl font-black shadow-lg hover:scale-105 transition-transform">
                        Join Live Class
                    </button>
                </div>
                <div class="absolute right-[-20px] bottom-[-20px] opacity-10">
                    <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.28 14.3H3.72L12 5.45z"/></svg>
                </div>
            </div>

            <!-- Academy Info -->
            <div class="bg-white p-8 rounded-[32px] shadow-sm border border-slate-100">
                <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center">
                    <span class="w-2 h-8 bg-emerald-500 rounded-full mr-3"></span>
                    Academy Profile
                </h3>
                <div class="space-y-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Mentor</p>
                            <p class="text-lg font-black text-slate-900">{{ teacherName || 'Assigning...' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    studentName: String,
    teacherName: String,
    grade: String
});

defineEmits(['goToLive']);

const statCards = computed(() => [
    { 
        label: 'My Subjects', 
        value: props.stats.subjects_count || 0, 
        icon: 'SubjectIcon', 
        color: 'bg-emerald-50 text-emerald-600' 
    },
    { 
        label: 'Upcoming Classes', 
        value: props.stats.live_classes_count || 0, 
        icon: 'LiveIcon', 
        color: 'bg-blue-50 text-blue-600' 
    },
    { 
        label: 'Notifications', 
        value: 0, 
        icon: 'BellIcon', 
        color: 'bg-purple-50 text-purple-600' 
    }
]);

const SubjectIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>' };
const LiveIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>' };
const BellIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>' };
</script>
