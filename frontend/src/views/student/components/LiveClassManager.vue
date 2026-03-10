<template>
    <div class="space-y-8">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Virtual Room</h1>
                <p class="text-slate-500 font-medium">Join your live interactive classes.</p>
            </div>
            <button @click="emit('refresh')" class="bg-slate-900 text-white px-5 py-3 rounded-2xl font-black hover:bg-slate-800 transition-colors">
                Refresh
            </button>
        </header>

        <div v-if="liveClasses.length === 0" class="flex flex-col items-center justify-center py-20 bg-white rounded-[40px] border border-slate-100">
             <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
             </div>
             <p class="text-slate-500 font-black text-xl mb-1">No classes scheduled right now</p>
             <p class="text-slate-400 font-medium text-center max-w-sm px-6">Your teacher will schedule your next session. Keep learning and check back soon!</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="cls in liveClasses" :key="cls.id" 
                class="bg-white p-8 rounded-[40px] shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 group relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest"
                            :class="cls.status === 'live' ? 'bg-emerald-100 text-emerald-700 animate-pulse' : 'bg-blue-100 text-blue-700'">
                            {{ cls.status === 'live' ? 'Online Now' : 'Scheduled' }}
                        </div>
                        <span class="text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                        </span>
                    </div>

                    <h3 class="text-2xl font-black text-slate-900 mb-2 truncate group-hover:text-emerald-600 transition-colors">{{ cls.title }}</h3>
                    <p class="text-slate-500 font-bold mb-8 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ new Date(cls.start_time).toLocaleString(undefined, { weekday: 'short', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                    </p>

                    <router-link v-if="cls.status === 'live'" :to="`/live-class/${cls.meeting_id}`"
                        class="w-full flex items-center justify-center py-4 bg-emerald-600 text-white rounded-2xl font-black shadow-lg shadow-emerald-600/20 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Join Room
                    </router-link>
                    <div v-else class="w-full flex items-center justify-center py-4 bg-slate-100 text-slate-400 rounded-2xl font-black cursor-not-allowed">
                        Waiting for Teacher...
                    </div>
                </div>
                
                <div class="absolute right-[-20px] bottom-[-20px] opacity-[0.03] group-hover:opacity-[0.05] transition-opacity">
                    <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const emit = defineEmits(['refresh']);

const props = defineProps({
    liveClasses: Array
});
</script>
