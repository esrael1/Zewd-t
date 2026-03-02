<template>
    <div class="max-w-4xl mx-auto space-y-8">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Profile Settings</h2>
            <button @click="saveProfile" :disabled="saving" class="bg-indigo-600 text-white px-8 py-3 rounded-2xl font-black shadow-lg shadow-indigo-600/20 hover:scale-105 transition-all disabled:opacity-50 disabled:scale-100 flex items-center">
                <span v-if="saving" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span>
                Save Changes
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Sidebar with Avatar -->
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[40px] shadow-sm border border-slate-100 flex flex-col items-center">
                    <div class="relative group">
                        <div class="w-32 h-32 bg-slate-100 rounded-[40px] flex items-center justify-center text-4xl font-black text-indigo-600 border-4 border-white shadow-xl ring-1 ring-slate-100 overflow-hidden">
                             <img v-if="profileData.profile_image_path" :src="profileData.profile_image_path" class="w-full h-full object-cover" />
                             <span v-else>{{ teacherName?.charAt(0) }}</span>
                        </div>
                        <input type="file" @change="handleAvatarUpload" class="absolute inset-0 opacity-0 cursor-pointer z-10" />
                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-white shadow-lg rounded-2xl flex items-center justify-center text-indigo-600 border border-slate-50 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-6 text-center">
                        <h4 class="text-xl font-black text-slate-900 leading-tight">{{ teacherName }}</h4>
                        <p class="text-xs text-slate-400 font-black uppercase tracking-widest mt-1">{{ profileData.specialization || 'Educator' }}</p>
                    </div>
                </div>

                <div class="bg-indigo-600 p-8 rounded-[40px] shadow-xl text-white">
                    <h5 class="font-black text-sm uppercase tracking-widest mb-4 opacity-70">Academy Status</h5>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        <span class="font-black uppercase tracking-wider text-xs">Active & Verified</span>
                    </div>
                    <p class="text-indigo-100 text-sm font-medium leading-relaxed">Your academy is public and students can find your profile.</p>
                </div>
            </div>

            <!-- Form Section -->
            <div class="md:col-span-2 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white p-10 rounded-[40px] shadow-sm border border-slate-100 space-y-6">
                    <h3 class="text-xl font-black text-slate-900 mb-2">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Academy Name</label>
                            <input v-model="profileData.academy_name" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Specialization</label>
                            <input v-model="profileData.specialization" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold" />
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Professional Bio</label>
                        <textarea v-model="profileData.bio" rows="4" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold resize-none"></textarea>
                    </div>

                     <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Contact Number</label>
                        <input v-model="profileData.phone_number" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold" />
                    </div>
                </div>

                <!-- Social Links -->
                <div class="bg-white p-10 rounded-[40px] shadow-sm border border-slate-100 space-y-6">
                    <h3 class="text-xl font-black text-slate-900 mb-2">Social Presence</h3>
                    <div class="space-y-4">
                        <div v-for="social in socials" :key="social.id" class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 shrink-0 border border-slate-100">
                                <component :is="social.icon" class="w-5 h-5" />
                            </div>
                            <input v-model="profileData.social_links[social.id]" :placeholder="social.placeholder" class="flex-1 px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold" />
                        </div>
                    </div>
                </div>

                <!-- Security Section -->
                <div class="bg-white p-10 rounded-[40px] shadow-sm border border-slate-100 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black text-slate-900">Security & Privacy</h3>
                        <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black uppercase rounded-lg tracking-widest">Update Required</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Current Password</label>
                            <input v-model="securityData.current_password" type="password" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 font-bold" placeholder="••••••••" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">New Password</label>
                                <input v-model="securityData.password" type="password" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 font-bold" placeholder="••••••••" />
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Confirm New Password</label>
                                <input v-model="securityData.password_confirmation" type="password" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 font-bold" placeholder="••••••••" />
                            </div>
                        </div>
                        <button @click="updatePassword" :disabled="updatingPass" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black shadow-lg hover:bg-slate-800 transition-all disabled:opacity-50 flex items-center justify-center">
                            <span v-if="updatingPass" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span>
                            Update Security Credentials
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    teacher: Object,
    teacherName: String
});

const emit = defineEmits(['save', 'updatePassword']);

const saving = ref(false);
const updatingPass = ref(false);
const profileData = ref({
    academy_name: props.teacher?.academy_name || '',
    bio: props.teacher?.bio || '',
    specialization: props.teacher?.specialization || '',
    phone_number: props.teacher?.phone_number || '',
    profile_image_path: props.teacher?.profile_image_path || null,
    social_links: props.teacher?.social_links || { twitter: '', linkedin: '', website: '' }
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

const socials = [
    { id: 'twitter', placeholder: 'Twitter Profile URL', icon: 'TwitterIcon' },
    { id: 'linkedin', placeholder: 'LinkedIn Profile URL', icon: 'LinkedinIcon' },
    { id: 'website', placeholder: 'Personal Website URL', icon: 'GlobeIcon' },
];

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

const saveProfile = async () => {
    saving.value = true;
    try {
        await emit('save', profileData.value);
    } finally {
        saving.value = false;
    }
};

const TwitterIcon = { template: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>' };
const LinkedinIcon = { template: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>' };
const GlobeIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9h18"></path></svg>' };
</script>
