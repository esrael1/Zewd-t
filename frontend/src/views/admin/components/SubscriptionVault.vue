<template>
    <div class="space-y-8">
        <header>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Subscription Vault</h1>
            <p class="text-slate-500 font-medium mt-1">Verify payment screenshots and activate platform access.</p>
        </header>

        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <div class="flex space-x-2">
                    <button v-for="s in ['pending', 'active', 'expired']" :key="s" 
                        @click="filterStatus = s"
                        :class="[
                            'px-6 py-2 rounded-2xl font-black text-sm transition-all uppercase tracking-widest',
                            filterStatus === s ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30' : 'bg-slate-50 text-slate-400 hover:bg-slate-100'
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
                            <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Plan</th>
                            <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Evidence</th>
                            <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="sub in filteredSubs" :key="sub.id" class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div>
                                    <p class="font-black text-slate-900">{{ sub.teacher.user.name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ sub.teacher.academy_name }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-700">{{ sub.plan.name }}</span>
                                    <span class="text-xs font-black text-rose-600">${{ sub.plan.price }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <button v-if="sub.payment_screenshot" @click="viewScreenshot(sub.payment_screenshot)" class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-700 font-black text-xs uppercase tracking-widest">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>View Proof</span>
                                </button>
                                <span v-else class="text-slate-300 italic text-xs">No screenshot</span>
                            </td>
                            <td class="px-8 py-6">
                                <button v-if="sub.status === 'pending'" @click="$emit('approve', sub.id)" class="bg-amber-500 text-white px-6 py-2 rounded-xl font-black text-xs hover:bg-amber-600 shadow-lg shadow-amber-500/20 transition-all uppercase tracking-widest">Activate</button>
                                <div v-else class="flex flex-col">
                                    <span class="text-slate-400 font-black text-xs uppercase tracking-widest italic">{{ sub.status }}</span>
                                    <span v-if="sub.end_date" class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Exp: {{ new Date(sub.end_date).toLocaleDateString() }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredSubs.length === 0">
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    <p class="text-slate-400 font-black text-lg">No subscription requests</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Screenshot Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-6" @click.self="showModal = false">
            <div class="bg-white p-4 rounded-[32px] shadow-2xl animate-in zoom-in duration-300 max-w-2xl w-full">
                <img :src="`http://localhost:8000/storage/${currentScreenshot}`" class="w-full h-auto rounded-2xl shadow-inner border border-slate-100" />
                <div class="mt-4 flex justify-between items-center">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Payment Evidence</p>
                    <button @click="showModal = false" class="text-rose-600 font-black text-xs uppercase tracking-widest hover:underline">Close Preview</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    subscriptions: Array
});

const emit = defineEmits(['approve']);

const filterStatus = ref('pending');
const showModal = ref(false);
const currentScreenshot = ref('');

const filteredSubs = computed(() => {
    return props.subscriptions.filter(s => s.status === filterStatus.value);
});

const viewScreenshot = (path) => {
    currentScreenshot.value = path;
    showModal.value = true;
};
</script>
