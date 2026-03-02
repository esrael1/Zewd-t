<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Learning Hub</h2>
                <p class="text-slate-500 font-medium">Upload and organize study materials for your subjects.</p>
            </div>
            <button @click="showUploadModal = true" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black shadow-lg shadow-indigo-600/20 hover:scale-105 transition-transform flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Publish Material
            </button>
        </div>

        <!-- Material Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="mat in materials" :key="mat.id" 
                class="bg-white p-6 rounded-[32px] shadow-sm border border-slate-100 hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300 group flex flex-col">
                <div class="flex items-start justify-between mb-4">
                    <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center border-2 border-white shadow-sm ring-1 ring-slate-100', getTypeColor(mat.type)]">
                         <component :is="getTypeIcon(mat.type)" class="w-7 h-7" />
                    </div>
                    <button @click="$emit('delete', mat.id)" class="p-2 text-slate-300 hover:text-red-500 bg-slate-50 rounded-xl transition-colors opacity-0 group-hover:opacity-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
                
                <h4 class="text-xl font-black text-slate-900 line-clamp-2 leading-tight mb-2">{{ mat.title }}</h4>
                <div class="flex items-center space-x-2 mb-6">
                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-wider">{{ mat.subject?.name }}</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ mat.subject?.grade?.name }}</span>
                </div>

                <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ new Date(mat.created_at).toLocaleDateString() }}</p>
                    <a :href="baseUrl + '/storage/' + mat.file_path" target="_blank" class="flex items-center text-indigo-600 text-sm font-black group-hover:translate-x-1 transition-transform">
                        Access File
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="materials.length === 0" class="col-span-full py-20 text-center">
                 <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-dashed border-slate-200">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                </div>
                <h3 class="text-slate-900 font-black text-xl">Empty Classroom</h3>
                <p class="text-slate-400 font-medium">Your learning hub is currently empty. Start by uploading resources.</p>
            </div>
        </div>

        <!-- Upload Modal -->
        <div v-if="showUploadModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
             <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="px-8 py-6 bg-slate-50 border-b flex justify-between items-center">
                    <h3 class="text-2xl font-black text-slate-900">Upload Material</h3>
                    <button @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-8 space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Title</label>
                        <input v-model="newMaterial.title" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold" placeholder="e.g. Intro to Algebra PDF">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Subject</label>
                            <select v-model="newMaterial.subject_id" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold appearance-none">
                                <option value="">Select Subject</option>
                                <option v-for="sub in subjects" :key="sub.id" :value="sub.id">{{ sub.name }} ({{ sub.grade?.name }})</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Type</label>
                            <select v-model="newMaterial.type" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold appearance-none">
                                <option value="pdf">PDF</option>
                                <option value="video">Video</option>
                                <option value="image">Image</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">File Upload</label>
                        <div class="relative">
                            <input type="file" ref="fileInput" @change="handleFile" class="absolute inset-0 opacity-0 cursor-pointer z-10" />
                            <div class="w-full px-5 py-8 bg-indigo-50 border-2 border-dashed border-indigo-200 rounded-3xl flex flex-col items-center justify-center text-center">
                                <svg class="w-8 h-8 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-indigo-600 font-black text-sm">{{ fileName || 'Drop file here or click to browse' }}</p>
                                <p class="text-indigo-400 text-xs font-medium mt-1">Maximum size: 20MB</p>
                            </div>
                        </div>
                    </div>
                    <button @click="submitUpload" class="w-full py-5 bg-indigo-600 text-white rounded-3xl font-black shadow-xl shadow-indigo-600/20 hover:scale-[1.02] transition-transform mt-4">
                        Upload to Hub
                    </button>
                </div>
             </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    materials: Array,
    subjects: Array,
    baseUrl: String
});

const emit = defineEmits(['upload', 'delete']);

const showUploadModal = ref(false);
const fileName = ref('');
const fileInput = ref(null);
const newMaterial = ref({ title: '', subject_id: '', type: 'pdf' });

const handleFile = (e) => {
    const file = e.target.files[0];
    if (file) fileName.value = file.name;
};

const submitUpload = () => {
    const formData = new FormData();
    formData.append('title', newMaterial.value.title);
    formData.append('subject_id', newMaterial.value.subject_id);
    formData.append('type', newMaterial.value.type);
    if (fileInput.value.files[0]) {
        formData.append('file', fileInput.value.files[0]);
    }
    
    emit('upload', formData);
    showUploadModal.value = false;
    newMaterial.value = { title: '', subject_id: '', type: 'pdf' };
    fileName.value = '';
};

const getTypeColor = (type) => {
    switch (type) {
        case 'pdf': return 'bg-orange-50 text-orange-600';
        case 'video': return 'bg-rose-50 text-rose-600';
        case 'image': return 'bg-emerald-50 text-emerald-600';
        default: return 'bg-indigo-50 text-indigo-600';
    }
};

const getTypeIcon = (type) => {
    switch (type) {
        case 'pdf': return PdfIcon;
        case 'video': return VideoIcon;
        case 'image': return ImageIcon;
        default: return FileIcon;
    }
};

const PdfIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>' };
const VideoIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>' };
const ImageIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>' };
const FileIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' };
</script>
