<template>
    <div class="max-w-4xl mx-auto space-y-8">
        <header>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">System Settings</h1>
            <p class="text-slate-500 font-medium">Manage administrative security and profile configurations.</p>
        </header>

        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-10 border-b border-slate-50">
                <div class="flex flex-col md:flex-row items-center space-y-6 md:space-y-0 md:space-x-8">
                    <div class="w-32 h-32 bg-rose-100 rounded-[40px] flex items-center justify-center text-rose-600 font-black text-4xl border-4 border-white shadow-xl">
                        {{ userName?.charAt(0) }}
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-2xl font-black text-slate-900 mb-1">{{ userName }}</h2>
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Platform Administrator</p>
                    </div>
                </div>
            </div>

            <div class="p-10 bg-slate-50/50 space-y-8">
                <div>
                    <h3 class="text-xl font-black text-slate-900">Security Credentials</h3>
                    <p class="text-slate-500 text-sm font-medium">Update your administrative access password.</p>
                </div>

                <div class="space-y-6 max-w-2xl">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Current Password</label>
                        <input v-model="securityData.current_password" type="password" placeholder="••••••••" 
                            class="w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 font-bold transition-all shadow-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">New Password</label>
                            <input v-model="securityData.password" type="password" placeholder="••••••••" 
                                class="w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 font-bold transition-all shadow-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Confirm New Password</label>
                            <input v-model="securityData.password_confirmation" type="password" placeholder="••••••••" 
                                class="w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 font-bold transition-all shadow-sm">
                        </div>
                    </div>

                    <button @click="updatePassword" :disabled="updatingPass" class="w-full py-5 bg-rose-600 text-white rounded-[24px] font-black shadow-xl shadow-rose-500/20 hover:bg-rose-700 transition-all flex items-center justify-center disabled:opacity-50">
                        <span v-if="updatingPass" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-3"></span>
                        Update Admin Credentials
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    userName: String
});

const emit = defineEmits(['updatePassword']);

const updatingPass = ref(false);
const securityData = ref({
    current_password: '',
    password: '',
    password_confirmation: ''
});

const updatePassword = async () => {
    if (!securityData.value.current_password || !securityData.value.password) {
        alert('Please fill in password fields');
        return;
    }
    updatingPass.value = true;
    try {
        await emit('updatePassword', securityData.value);
        securityData.value = { current_password: '', password: '', password_confirmation: '' };
    } finally {
        updatingPass.value = false;
    }
};
</script>
