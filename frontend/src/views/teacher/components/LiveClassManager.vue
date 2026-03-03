<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Virtual Room</h2>
                <p class="text-slate-500 font-medium">Schedule and manage your live video teaching sessions.</p>
            </div>
            <button @click="showScheduleModal = true" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black shadow-lg shadow-indigo-600/20 hover:scale-105 transition-transform flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                New Session
            </button>
        </div>

        <!-- Live Sessions List -->
        <div class="space-y-4">
            <div v-for="cls in liveClasses" :key="cls.id" 
                class="bg-white p-6 rounded-[32px] shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300 group">
                <div class="flex items-center space-x-6 flex-1">
                    <div :class="['w-16 h-16 rounded-[24px] flex items-center justify-center shrink-0 border-2 border-white shadow-sm ring-1 ring-slate-100', 
                        cls.status === 'live' ? 'bg-green-50 text-green-600 animate-pulse' : 
                        cls.status === 'ended' ? 'bg-slate-100 text-slate-400' : 'bg-indigo-50 text-indigo-600']">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center space-x-2 mb-1">
                            <h4 class="text-lg font-black text-slate-900 truncate">{{ cls.title }}</h4>
                            <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest', 
                                cls.status === 'live' ? 'bg-green-100 text-green-700' : 
                                cls.status === 'ended' ? 'bg-slate-200 text-slate-600' : 'bg-indigo-100 text-indigo-700']">
                                {{ cls.status }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                            <span class="flex items-center text-slate-500">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                                {{ new Date(cls.start_time).toLocaleString() }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                {{ cls.subject?.name }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3 shrink-0">
                    <button v-if="cls.status === 'scheduled'" @click="$emit('start', cls.meeting_id)" 
                        class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-600/20 hover:scale-105 transition-transform flex items-center">
                        Launch Class
                    </button>
                    <button v-if="cls.status === 'live'" @click="$emit('end', cls.meeting_id)" 
                        class="px-8 py-3 bg-rose-600 text-white rounded-2xl font-black shadow-lg shadow-rose-600/20 hover:scale-105 transition-transform">
                        End Session
                    </button>
                    <button v-if="cls.status === 'ended'" @click="$emit('start', cls.meeting_id)" 
                        class="px-8 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black hover:bg-slate-200 transition-colors">
                        Restart
                    </button>
                    
                    <router-link v-if="cls.status === 'live'" :to="'/live-class/' + cls.meeting_id"
                        class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl hover:bg-indigo-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </router-link>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="liveClasses.length === 0" class="py-20 text-center bg-white rounded-[40px] border-2 border-dashed border-slate-100">
                <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-slate-900 font-black text-xl">No Sessions Scheduled</h3>
                <p class="text-slate-400 font-medium">Create your first virtual class to start teaching.</p>
            </div>
        </div>

        <!-- Schedule Modal (Simplified) -->
        <div v-if="showScheduleModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
             <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="px-8 py-6 bg-slate-50 border-b flex justify-between items-center">
                    <h3 class="text-2xl font-black text-slate-900">Schedule Session</h3>
                    <button @click="showScheduleModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-8 space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Class Title</label>
                        <input v-model="newClass.title" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold" placeholder="e.g. Advanced Physics Part 1">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Grade</label>
                            <select v-model="newClass.grade_id" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold appearance-none">
                                <option value="">Select Grade</option>
                                <option v-for="grade in grades" :key="grade.id" :value="grade.id">{{ grade.name }}</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Subject</label>
                            <select v-model="newClass.subject_id" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold appearance-none">
                                <option value="">Select Subject</option>
                                <option v-for="sub in filteredSubjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Start Time</label>
                        <input v-model="newClass.start_time" type="datetime-local" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold">
                    </div>
                    <button @click="submitSchedule" class="w-full py-5 bg-indigo-600 text-white rounded-3xl font-black shadow-xl shadow-indigo-600/20 hover:scale-[1.02] transition-transform mt-4">
                        Confirm Schedule
                    </button>
                </div>
             </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    liveClasses: Array,
    grades: Array,
    subjects: Array
});

const emit = defineEmits(['create', 'start', 'end']);

const showScheduleModal = ref(false);
const newClass = ref({ title: '', grade_id: '', subject_id: '', start_time: '' });

const filteredSubjects = computed(() => {
    return props.subjects.filter(s => s.grade_id == newClass.value.grade_id);
});

const submitSchedule = () => {
    emit('create', newClass.value);
    showScheduleModal.value = false;
    newClass.value = { title: '', grade_id: '', subject_id: '', start_time: '' };
};
</script>
