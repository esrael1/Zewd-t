<template>
    <div class="space-y-8">
        <header>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Teacher Hub</h1>
            <p class="text-slate-500 font-medium mt-1">Manage platform educators and pending applications.</p>
        </header>

        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <div class="flex space-x-2">
                    <button v-for="s in ['pending', 'approved', 'rejected']" :key="s" 
                        @click="filterStatus = s"
                        :class="[
                            'px-6 py-2 rounded-2xl font-black text-sm transition-all uppercase tracking-widest',
                            filterStatus === s ? 'bg-rose-600 text-white shadow-lg shadow-rose-500/30' : 'bg-slate-50 text-slate-400 hover:bg-slate-100'
                        ]">
                        {{ s }}
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Educator</th>
                            <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Academy</th>
                            <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Specialization</th>
                            <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="teacher in filteredTeachers" :key="teacher.id" class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center text-rose-600 font-black overflow-hidden border-2 border-white shadow-sm">
                                        <img v-if="teacher.profile_image_path" :src="teacher.profile_image_path" class="w-full h-full object-cover" />
                                        <span v-else>{{ teacher.user.name.charAt(0) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-900">{{ teacher.user.name }}</p>
                                        <p class="text-xs font-bold text-slate-400">{{ teacher.user.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="font-bold text-slate-700">{{ teacher.academy_name || 'N/A' }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-xs font-black uppercase tracking-widest border border-indigo-100">
                                    {{ teacher.specialization || 'General' }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div v-if="teacher.status === 'pending'" class="flex space-x-2">
                                    <button @click="$emit('approve', teacher.id)" class="bg-emerald-500 text-white px-4 py-2 rounded-xl font-black text-xs hover:bg-emerald-600 shadow-lg shadow-emerald-500/20 transition-all uppercase tracking-widest">Approve</button>
                                    <button @click="$emit('reject', teacher.id)" class="bg-rose-500 text-white px-4 py-2 rounded-xl font-black text-xs hover:bg-rose-600 shadow-lg shadow-rose-500/20 transition-all uppercase tracking-widest">Reject</button>
                                </div>
                                <div v-else class="text-slate-400 font-black text-xs uppercase tracking-widest italic">
                                    {{ teacher.status }}
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredTeachers.length === 0">
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <p class="text-slate-400 font-black text-lg">No educators found</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    teachers: Array
});

const emit = defineEmits(['approve', 'reject']);

const filterStatus = ref('pending');

const filteredTeachers = computed(() => {
    return props.teachers.filter(t => t.status === filterStatus.value);
});
</script>
