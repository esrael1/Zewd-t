<template>
    <div class="space-y-8">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Learning Hub</h1>
                <p class="text-slate-500 font-medium">Access your study materials and course resources.</p>
            </div>
        </header>

        <!-- Subjects Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="subject in subjects" :key="subject.id"
                class="bg-white p-6 rounded-[32px] shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 group cursor-pointer overflow-hidden relative"
                @click="selectedSubject = subject">
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-1">{{ subject.name }}</h3>
                    <p class="text-slate-500 font-bold text-sm">Click to view materials</p>
                </div>
                <div class="absolute right-[-10px] top-[-10px] opacity-5">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            </div>
        </div>

        <!-- Materials Detail View -->
        <div v-if="selectedSubject" class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-6">
            <div class="bg-white w-full max-w-4xl max-h-[80vh] rounded-[40px] shadow-2xl flex flex-col overflow-hidden animate-in zoom-in duration-300">
                <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900">{{ selectedSubject.name }} Materials</h2>
                        <p class="text-slate-500 font-medium">Resources shared by your teacher</p>
                    </div>
                    <button @click="selectedSubject = null" class="w-12 h-12 bg-white rounded-full shadow-sm border border-slate-100 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-8">
                    <div v-if="loading" class="flex flex-col items-center justify-center py-20">
                        <div class="w-12 h-12 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Loading Resources...</p>
                    </div>
                    
                    <div v-else-if="materials.length === 0" class="flex flex-col items-center justify-center py-20 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[32px]">
                        <svg class="w-20 h-20 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-slate-500 font-black text-lg">No materials available yet</p>
                        <p class="text-slate-400 font-medium">Check back later for updates from your teacher.</p>
                    </div>

                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="mat in materials" :key="mat.id" 
                            class="p-4 bg-white border border-slate-100 rounded-2xl hover:border-emerald-500 hover:shadow-lg transition-all flex items-center justify-between group">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                                    <svg v-if="mat.type === 'pdf'" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-black text-slate-900 truncate">{{ mat.title }}</p>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ mat.type }}</p>
                                </div>
                            </div>
                            <a :href="`${baseUrl}/storage/${mat.file_path}`" target="_blank" class="w-10 h-10 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import apiClient from '../../../services/api';

const props = defineProps({
    subjects: Array,
    baseUrl: String
});

const selectedSubject = ref(null);
const materials = ref([]);
const loading = ref(false);

watch(selectedSubject, async (newVal) => {
    if (newVal) {
        loading.value = true;
        try {
            const res = await apiClient.get(`/student/subjects/${newVal.id}/materials`);
            materials.value = res.data;
        } catch (e) {
            console.error(e);
        } finally {
            loading.value = false;
        }
    }
});
</script>
