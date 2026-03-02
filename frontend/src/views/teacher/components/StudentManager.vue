<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Student Directory</h2>
                <p class="text-slate-500 font-medium">Manage and monitor {{ students.length }} registered students.</p>
            </div>
            <button @click="showAddModal = true" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black shadow-lg shadow-indigo-600/20 hover:scale-105 transition-transform flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Register Student
            </button>
        </div>

        <!-- Search & Filter -->
        <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input v-model="searchQuery" placeholder="Search by name or email..." class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-medium text-slate-700" />
            </div>
            <select v-model="filterGrade" class="px-6 py-3 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-slate-600 appearance-none">
                <option value="">All Grades</option>
                <option v-for="grade in grades" :key="grade.id" :value="grade.id">{{ grade.name }}</option>
            </select>
        </div>

        <!-- Student Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="student in filteredStudents" :key="student.id" 
                class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300 group relative overflow-hidden">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center text-indigo-600 text-2xl font-black border-2 border-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
                        <img v-if="student.user.profile_image_path" :src="student.user.profile_image_path" class="w-full h-full object-cover" />
                        <span v-else>{{ student.user.name.charAt(0) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-lg font-black text-slate-900 truncate">{{ student.user.name }}</h4>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">{{ student.grade?.name || 'Unassigned' }}</p>
                    </div>
                </div>
                
                <div class="space-y-3 mb-6">
                    <div class="flex items-center text-sm text-slate-500 font-medium">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="truncate">{{ student.user.email }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                    <div class="flex items-center space-x-2">
                        <span :class="[
                            'px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border',
                            student.status === 'active' 
                                ? 'bg-emerald-50 text-emerald-600 border-emerald-100' 
                                : 'bg-red-50 text-red-600 border-red-100'
                        ]">
                            {{ student.status }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="$emit('toggleStatus', student.id)" 
                            :class="[
                                'p-2 rounded-xl transition-all duration-200',
                                student.status === 'active' 
                                    ? 'text-slate-400 hover:text-red-500 hover:bg-red-50' 
                                    : 'text-slate-400 hover:text-emerald-500 hover:bg-emerald-50'
                            ]"
                            :title="student.status === 'active' ? 'Suspend Student' : 'Activate Student'">
                            <svg v-if="student.status === 'active'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </button>
                        <button class="text-indigo-600 text-sm font-black hover:text-indigo-700">View Profile</button>
                    </div>
                </div>
            </div>
            
            <div v-if="filteredStudents.length === 0" class="col-span-full py-20 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="text-slate-900 font-black text-xl">No students found</h3>
                <p class="text-slate-400 font-medium">Try adjusting your search or filters.</p>
            </div>
        </div>

        <!-- Add Student Modal (Simplified for logic) -->
        <div v-if="showAddModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
             <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="px-8 py-6 bg-slate-50 border-b flex justify-between items-center">
                    <h3 class="text-2xl font-black text-slate-900 leading-none">Register New Student</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-8 space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                        <input v-model="newStudent.name" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold" placeholder="e.g. John Doe">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                        <input v-model="newStudent.email" type="email" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold" placeholder="e.g. john@example.com">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Initial Password</label>
                        <input v-model="newStudent.password" type="password" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Assign Grade</label>
                        <select v-model="newStudent.grade_id" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold appearance-none">
                            <option value="">Select Grade</option>
                            <option v-for="grade in grades" :key="grade.id" :value="grade.id">{{ grade.name }}</option>
                        </select>
                    </div>
                    <button @click="submitAdd" class="w-full py-5 bg-indigo-600 text-white rounded-3xl font-black shadow-xl shadow-indigo-600/20 hover:scale-[1.02] transition-transform mt-4">
                        Confirm Registration
                    </button>
                </div>
             </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    students: Array,
    grades: Array
});

const emit = defineEmits(['add']);

const searchQuery = ref('');
const filterGrade = ref('');
const showAddModal = ref(false);
const newStudent = ref({ name: '', email: '', password: '', grade_id: '' });

const filteredStudents = computed(() => {
    return props.students.filter(s => {
        const matchesSearch = s.user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                             s.user.email.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesGrade = !filterGrade.value || s.grade_id == filterGrade.value;
        return matchesSearch && matchesGrade;
    });
});

const submitAdd = () => {
    emit('add', newStudent.value);
    showAddModal.value = false;
    newStudent.value = { name: '', email: '', password: '', grade_id: '' };
};
</script>
