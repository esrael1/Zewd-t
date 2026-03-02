<template>
    <div class="space-y-8">
        <header>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">System Oversight 📈</h1>
            <p class="text-slate-500 font-medium mt-1">Global platform activity and performance metrics.</p>
        </header>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="stat in statCards" :key="stat.label" 
                class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:shadow-rose-500/5 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-2xl mb-4 flex items-center justify-center transition-transform group-hover:scale-110" :class="stat.color">
                    <component :is="stat.icon" class="w-6 h-6" />
                </div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">{{ stat.label }}</p>
                <p class="text-4xl font-black text-slate-900 leading-none">{{ stat.value }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-gradient-to-br from-rose-600 to-pink-700 rounded-[32px] p-8 text-white relative overflow-hidden shadow-2xl shadow-rose-500/20">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-4 font-inter">Pending Approvals</h2>
                    <p class="text-rose-100 font-medium mb-8 max-w-md">There are teachers and subscriptions waiting for your review.</p>
                    <div class="flex space-x-4">
                        <button @click="$emit('select', 'Teachers')" class="bg-white text-rose-700 px-6 py-3 rounded-2xl font-black shadow-lg hover:scale-105 transition-transform">
                            Review Teachers
                        </button>
                        <button @click="$emit('select', 'Subscriptions')" class="bg-rose-500/30 backdrop-blur-sm text-white border border-rose-400/30 px-6 py-3 rounded-2xl font-black hover:bg-rose-500/40 transition-all">
                            Verify Payments
                        </button>
                    </div>
                </div>
                <div class="absolute right-[-20px] bottom-[-20px] opacity-10">
                    <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.28 14.3H3.72L12 5.45z"/></svg>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[32px] shadow-sm border border-slate-100">
                <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center">
                    <span class="w-2 h-8 bg-rose-500 rounded-full mr-3"></span>
                    Platform Health
                </h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="font-bold text-slate-700">Database Connection</span>
                        </div>
                        <span class="px-3 py-1 bg-emerald-500 text-white text-[10px] font-black uppercase rounded-full tracking-widest">Active</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <span class="font-bold text-slate-700">Jitsi Meet Cluster</span>
                        </div>
                        <span class="px-3 py-1 bg-emerald-500 text-white text-[10px] font-black uppercase rounded-full tracking-widest">Online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    stats: Object
});

defineEmits(['select']);

const statCards = computed(() => [
    { 
        label: 'Total Teachers', 
        value: props.stats.teachers_total || 0, 
        icon: 'TeachersIcon', 
        color: 'bg-rose-50 text-rose-600' 
    },
    { 
        label: 'Global Students', 
        value: props.stats.students_total || 0, 
        icon: 'StudentsIcon', 
        color: 'bg-indigo-50 text-indigo-600' 
    },
    { 
        label: 'Live Classes', 
        value: props.stats.active_classes || 0, 
        icon: 'LiveIcon', 
        color: 'bg-emerald-50 text-emerald-600' 
    },
    { 
        label: 'Pending Subs', 
        value: props.stats.pending_subscriptions || 0, 
        icon: 'VaultIcon', 
        color: 'bg-amber-50 text-amber-600' 
    }
]);

const TeachersIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>' };
const StudentsIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>' };
const LiveIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>' };
const VaultIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>' };
</script>
