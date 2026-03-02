<template>
    <div class="space-y-8">
        <header>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Global Student Directory</h1>
            <p class="text-slate-500 font-medium mt-1">Universal view of all learners across the platform.</p>
        </header>

        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <div class="relative w-full max-w-md">
                    <input v-model="searchQuery" type="text" placeholder="Search by name or academy..." 
                        class="w-full pl-12 pr-6 py-3 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 font-bold transition-all">
                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Student</th>
                            <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Grade</th>
                            <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Mentor (Teacher)</th>
                            <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="student in filteredStudents" :key="student.id" class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 font-black border-2 border-white shadow-sm overflow-hidden">
                                        <img v-if="student.profile_image_path" :src="student.profile_image_path" class="w-full h-full object-cover" />
                                        <span v-else>{{ student.user.name.charAt(0) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-900 line-clamp-1">{{ student.user.name }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ student.user.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-sm font-bold text-slate-600">
                                {{ student.grade?.name || 'Assessing...' }}
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-700">{{ student.teacher.user.name }}</span>
                                    <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest">{{ student.teacher.academy_name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-sm font-bold text-slate-400">
                                {{ new Date(student.created_at).toLocaleDateString() }}
                            </td>
                        </tr>
                        <tr v-if="filteredStudents.length === 0">
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                                    <p class="text-slate-400 font-black text-lg">No students matching search</p>
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
    students: Array
});

const searchQuery = ref('');

const filteredStudents = computed(() => {
    if (!searchQuery.value) return props.students;
    const q = searchQuery.value.toLowerCase();
    return props.students.filter(s => 
        s.user.name.toLowerCase().includes(q) || 
        s.teacher.academy_name?.toLowerCase().includes(q) ||
        s.teacher.user.name.toLowerCase().includes(q)
    );
});
</script>
