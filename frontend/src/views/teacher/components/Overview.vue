<template>
    <div class="space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Welcome back, {{ teacherName }}!</h2>
                <p class="text-slate-500 mt-1 font-medium italic">Here is what's happening in your academy today.</p>
            </div>
            <div class="flex items-center space-x-2 bg-white p-1 rounded-2xl shadow-sm border border-slate-100">
                <button class="px-4 py-2 bg-slate-50 text-slate-600 rounded-xl text-sm font-bold shadow-sm">Today</button>
                <button class="px-4 py-2 text-slate-400 hover:text-slate-600 rounded-xl text-sm font-bold transition-colors">This Week</button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="stat in statCards" :key="stat.label" 
                class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center transition-colors group-hover:scale-110 duration-300', stat.bg]">
                        <component :is="stat.icon" :class="['w-6 h-6', stat.color]" />
                    </div>
                </div>
                <h3 class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">{{ stat.label }}</h3>
                <p class="text-4xl font-black text-slate-900 leading-none">{{ stat.value }}</p>
                <div class="mt-4 flex items-center text-xs font-bold text-green-600 bg-green-50 w-max px-2 py-1 rounded-lg">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7l-1.293 1.293a1 1 0 01-1.414 0L8 7a1 1 0 011.414-1.414L10 6.172l1.293-1.293a1 1 0 011.414 1.414z" clip-rule="evenodd"></path></svg>
                    <span>+12% vs last month</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-xl font-black text-slate-900 mb-6">Recent Activity</h3>
                <div class="space-y-6">
                    <div v-for="i in 3" :key="i" class="flex items-start space-x-4">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mt-2"></div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">New material uploaded for Mathematics</p>
                            <p class="text-xs text-slate-400 font-medium">2 hours ago</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-2xl font-black mb-2">Ready to start?</h3>
                    <p class="text-indigo-100 mb-6 font-medium max-w-[200px]">Launch your virtual classroom and start teaching now.</p>
                    <button @click="$emit('goToLive')" class="bg-white text-indigo-600 px-6 py-3 rounded-2xl font-black shadow-lg shadow-indigo-900/20 hover:scale-105 transition-transform">
                        Launch Virtual Room
                    </button>
                </div>
                <!-- Abstract Design Elements -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-indigo-400/20 rounded-full blur-2xl mr-4 mb-4"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    stats: Object,
    teacherName: String
});

defineEmits(['goToLive']);

const statCards = [
    { label: 'Total Students', value: props.stats?.students_count || 0, icon: 'UsersIcon', color: 'text-blue-600', bg: 'bg-blue-50' },
    { label: 'Live Sessions', value: props.stats?.classes_count || 0, icon: 'MonitorIcon', color: 'text-green-600', bg: 'bg-green-50' },
    { label: 'Study Hub Items', value: props.stats?.materials_count || 0, icon: 'BookIcon', color: 'text-purple-600', bg: 'bg-purple-50' },
];

const UsersIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>' };
const MonitorIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>' };
const BookIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>' };
</script>
