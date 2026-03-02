<template>
    <div class="space-y-8">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Plan Architect</h1>
                <p class="text-slate-500 font-medium mt-1">Configure platform tiers and student limitations.</p>
            </div>
            <button @click="openModal()" class="bg-rose-600 text-white px-8 py-4 rounded-2xl font-black shadow-lg shadow-rose-500/20 hover:scale-105 active:scale-95 transition-all uppercase tracking-widest text-xs">
                Create New Tier
            </button>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div v-for="plan in plans" :key="plan.id" 
                class="bg-white p-8 rounded-[40px] shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 group relative overflow-hidden">
                <div class="relative z-10 flex flex-col h-full">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                        </div>
                        <div class="flex space-x-2">
                             <button @click="openModal(plan)" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                             </button>
                             <button @click="$emit('delete', plan.id)" class="text-slate-400 hover:text-rose-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                             </button>
                        </div>
                    </div>

                    <h3 class="text-2xl font-black text-slate-900 mb-2 truncate font-inter group-hover:text-rose-600 transition-colors">{{ plan.name }}</h3>
                    <p class="text-3xl font-black text-slate-900 mb-8 font-inter">
                        ${{ plan.price }}
                        <span class="text-sm font-bold text-slate-400 uppercase tracking-widest">/ {{ plan.duration_days }} Days</span>
                    </p>

                    <div class="flex-1 space-y-4 mb-8">
                        <div class="flex items-center text-sm font-bold text-slate-600">
                            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Up to {{ plan.max_students }} Students
                        </div>
                        <div class="flex items-center text-sm font-bold text-slate-600">
                            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            {{ plan.max_live_classes || 'Unlimited' }} Live Classes
                        </div>
                    </div>

                    <p class="text-slate-400 text-xs italic">{{ plan.description || 'Professional platform access for growing academies.' }}</p>
                </div>
            </div>
        </div>

        <!-- Plan Form Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-6" @click.self="showModal = false">
            <div class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl p-10 animate-in zoom-in duration-300">
                <h2 class="text-3xl font-black text-slate-900 mb-8 font-inter">{{ editingPlan ? 'Edit' : 'Create' }} Platform Tier</h2>
                <form @submit.prevent="savePlan" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Plan Name</label>
                        <input v-model="formData.name" required type="text" placeholder="e.g. Master Academy" 
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 font-bold transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Price ($)</label>
                            <input v-model="formData.price" required type="number" step="0.01" 
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 font-bold transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Duration (Days)</label>
                            <input v-model="formData.duration_days" required type="number" 
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 font-bold transition-all">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Student Limit</label>
                            <input v-model="formData.max_students" required type="number" 
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 font-bold transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Class Limit</label>
                            <input v-model="formData.max_live_classes" type="number" placeholder="Optional"
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 font-bold transition-all">
                        </div>
                    </div>
                    <div class="pt-4 flex justify-end space-x-4">
                        <button type="button" @click="showModal = false" class="px-8 py-4 font-black text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest text-xs">Cancel</button>
                        <button type="submit" class="bg-rose-600 text-white px-10 py-4 rounded-2xl font-black shadow-xl shadow-rose-500/30 hover:scale-105 transition-all uppercase tracking-widest text-xs">
                            {{ editingPlan ? 'Update Plan' : 'Establish Plan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    plans: Array
});

const emit = defineEmits(['save', 'delete']);

const showModal = ref(false);
const editingPlan = ref(null);
const formData = ref({
    name: '',
    price: 0,
    duration_days: 30,
    max_students: 50,
    max_live_classes: null,
    description: ''
});

const openModal = (plan = null) => {
    editingPlan.value = plan;
    if (plan) {
        formData.value = { ...plan };
    } else {
        formData.value = {
            name: '',
            price: 0,
            duration_days: 30,
            max_students: 50,
            max_live_classes: null,
            description: ''
        };
    }
    showModal.value = true;
};

const savePlan = () => {
    emit('save', { id: editingPlan.value?.id, ...formData.value });
    showModal.value = false;
};
</script>
