<template>
    <div class="max-w-4xl mx-auto space-y-8">
        <header>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Account Settings</h1>
            <p class="text-slate-500 font-medium">Manage your personal information and student profile.</p>
        </header>

        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-10 border-b border-slate-50">
                <div class="flex flex-col md:flex-row items-center space-y-6 md:space-y-0 md:space-x-8">
                    <div class="relative group">
                        <div class="w-32 h-32 bg-emerald-100 rounded-[40px] flex items-center justify-center text-emerald-600 font-bold text-4xl overflow-hidden border-4 border-white shadow-xl group-hover:scale-105 transition-transform duration-300">
                            <img v-if="profileData.profile_image_path" :src="profileData.profile_image_path" class="w-full h-full object-cover" />
                            <span v-else>{{ studentName?.charAt(0) }}</span>
                        </div>
                        <label class="absolute bottom-[-10px] right-[-10px] bg-emerald-600 text-white p-3 rounded-2xl shadow-lg cursor-pointer hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            <input type="file" class="hidden" @change="handleAvatarUpload" accept="image/*">
                        </label>
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-2xl font-black text-slate-900 mb-1">{{ studentName }}</h2>
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs mb-4">Personal Education Profile</p>
                        <div class="flex flex-wrap justify-center md:justify-start gap-2">
                            <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-sm font-bold border border-emerald-100">
                                {{ profileData.grade?.name || 'Grade' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="$emit('save', profileData)" class="p-10 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Phone Number</label>
                        <input v-model="profileData.phone_number" type="text" placeholder="+251 ..." 
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 font-bold transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Short Bio</label>
                    <textarea v-model="profileData.bio" rows="4" placeholder="Tell us about yourself..." 
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-[32px] focus:ring-4 focus:ring-emerald-500/10 font-bold transition-all resize-none"></textarea>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-emerald-600 text-white px-10 py-5 rounded-[24px] font-black shadow-xl shadow-emerald-500/30 hover:scale-105 active:scale-95 transition-all">
                        Update Student Profile
                    </button>
                </div>
            </form>

            <div class="p-10 bg-slate-50/50 space-y-8 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-slate-900">Security & Privacy</h3>
                        <p class="text-slate-500 text-sm font-medium">Protect your account with a strong password.</p>
                    </div>
                </div>

                <div class="space-y-6 max-w-2xl">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Current Password</label>
                        <input v-model="securityData.current_password" type="password" placeholder="••••••••" 
                            class="w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 font-bold transition-all">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">New Password</label>
                            <input v-model="securityData.password" type="password" placeholder="••••••••" 
                                class="w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 font-bold transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Confirm New Password</label>
                            <input v-model="securityData.password_confirmation" type="password" placeholder="••••••••" 
                                class="w-full px-6 py-4 bg-white border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 font-bold transition-all">
                        </div>
                    </div>

                    <button @click="updatePassword" :disabled="updatingPass" class="w-full py-5 bg-slate-900 text-white rounded-[24px] font-black shadow-xl hover:bg-slate-800 transition-all flex items-center justify-center disabled:opacity-50">
                        <span v-if="updatingPass" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-3"></span>
                        Update Security Credentials
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    student: Object,
    studentName: String
});

const emit = defineEmits(['save', 'updatePassword']);

const updatingPass = ref(false);
const profileData = ref({
    ...props.student,
    profile_image: null
});

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

const handleAvatarUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        profileData.value.profile_image = file;
        const reader = new FileReader();
        reader.onload = (res) => {
            profileData.value.profile_image_path = res.target.result;
        };
        reader.readAsDataURL(file);
    }
};
</script>
