<template>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Grades Section -->
        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 leading-none">Your Grades</h3>
                    <p class="text-slate-400 text-sm font-bold mt-2 uppercase tracking-widest">Levels of education</p>
                </div>
                <div class="flex gap-2">
                    <input v-model="newGrade" placeholder="New Grade Name" class="px-4 py-2 bg-white border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold shadow-sm">
                    <button @click="handleAddGrade" class="bg-indigo-600 text-white p-2 rounded-xl shadow-lg shadow-indigo-600/20 hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </button>
                </div>
            </div>
            <div class="flex-1 p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div v-for="grade in grades" :key="grade.id" class="p-4 bg-slate-50 rounded-2xl flex items-center justify-between group hover:bg-white hover:shadow-xl hover:shadow-indigo-500/5 transition-all">
                        <div class="flex-1">
                            <input v-if="editingGradeId === grade.id" v-model="editGradeName" @keyup.enter="saveGradeEdit(grade.id)" 
                                class="w-full bg-white border-2 border-indigo-100 rounded-lg px-2 py-1 font-black text-slate-700 outline-none">
                            <span v-else class="font-black text-slate-700 tracking-tight">{{ grade.name }}</span>
                        </div>
                        <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button v-if="editingGradeId === grade.id" @click="saveGradeEdit(grade.id)" class="text-emerald-500 hover:scale-110 transition-transform p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                            <button v-else @click="startGradeEdit(grade)" class="text-slate-400 hover:text-indigo-600 transition-colors p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button @click="$emit('deleteGrade', grade.id)" class="text-slate-300 hover:text-red-500 transition-colors p-1">
                                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subjects Section -->
        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-8 border-b border-slate-50 flex flex-col gap-4 bg-indigo-50/30">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 leading-none">Course Subjects</h3>
                    <p class="text-slate-400 text-sm font-bold mt-2 uppercase tracking-widest">Academic catalog</p>
                </div>
                <form @submit.prevent="handleAddSubject" class="flex flex-col sm:flex-row gap-2">
                     <select v-model="newSubject.grade_id" class="px-4 py-2 bg-white border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold shadow-sm sm:w-1/3" required>
                         <option value="">Grade</option>
                         <option v-for="grade in grades" :key="grade.id" :value="grade.id">{{ grade.name }}</option>
                     </select>
                    <input v-model="newSubject.name" placeholder="Subject Name" class="flex-1 px-4 py-2 bg-white border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold shadow-sm" required>
                    <button type="submit" class="bg-indigo-600 text-white p-2 rounded-xl shadow-lg shadow-indigo-600/20 hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </button>
                </form>
            </div>
            <div class="flex-1 p-8 space-y-3">
                <div v-for="subject in subjects" :key="subject.id" class="flex items-center space-x-4 p-1 rounded-2xl group hover:bg-slate-50 transition-colors">
                    <div class="w-12 h-12 bg-white border-2 border-slate-50 shadow-sm rounded-xl flex items-center justify-center text-indigo-600 font-black shrink-0 group-hover:border-indigo-100 transition-colors">
                        {{ subject.name.charAt(0) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <input v-if="editingSubjectId === subject.id" v-model="editSubjectName" @keyup.enter="saveSubjectEdit(subject.id)" 
                                class="w-full bg-white border-2 border-indigo-100 rounded-lg px-2 py-1 font-black text-slate-900 outline-none">
                        <p v-else class="font-black text-slate-900 truncate">{{ subject.name }}</p>
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">{{ subject.grade?.name }}</p>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                         <button v-if="editingSubjectId === subject.id" @click="saveSubjectEdit(subject.id)" class="text-emerald-500 hover:scale-110 transition-transform p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                        <button v-else @click="startSubjectEdit(subject)" class="text-slate-300 hover:text-indigo-600 transition-colors p-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    grades: Array,
    subjects: Array
});

const emit = defineEmits(['addGrade', 'deleteGrade', 'addSubject', 'updateGrade', 'updateSubject']);

const newGrade = ref('');
const newSubject = ref({ grade_id: '', name: '' });

const editingGradeId = ref(null);
const editGradeName = ref('');
const editingSubjectId = ref(null);
const editSubjectName = ref('');

const startGradeEdit = (grade) => {
    editingGradeId.value = grade.id;
    editGradeName.value = grade.name;
};

const saveGradeEdit = (id) => {
    if (!editGradeName.value.trim()) return;
    emit('updateGrade', { id, name: editGradeName.value });
    editingGradeId.value = null;
};

const startSubjectEdit = (subject) => {
    editingSubjectId.value = subject.id;
    editSubjectName.value = subject.name;
};

const saveSubjectEdit = (id) => {
    if (!editSubjectName.value.trim()) return;
    emit('updateSubject', { id, name: editSubjectName.value });
    editingSubjectId.value = null;
};

const handleAddGrade = () => {
    if (!newGrade.value.trim()) return;
    emit('addGrade', newGrade.value);
    newGrade.value = '';
};

const handleAddSubject = () => {
    if (!newSubject.value.name || !newSubject.value.grade_id) return;
    emit('addSubject', newSubject.value);
    newSubject.value = { grade_id: '', name: '' };
};
</script>
