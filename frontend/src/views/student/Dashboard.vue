<template>
    <div class="min-h-screen bg-slate-50 flex overflow-x-hidden">
        <!-- Sidebar -->
        <Sidebar 
            :active-tab="currentTab" 
            :student="studentProfile" 
            :student-name="authStore.user?.name"
            :is-open="isSidebarOpen"
            @select="currentTab = $event" 
            @logout="handleLogout" 
            @close="isSidebarOpen = false"
        />

        <!-- Mobile Header -->
        <header class="fixed top-0 left-0 right-0 h-16 bg-white/80 backdrop-blur-md z-40 flex items-center px-6 border-b border-slate-100 lg:hidden">
            <button @click="isSidebarOpen = true" class="p-2 -ml-2 text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <div class="ml-4 flex items-center space-x-2">
                <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-bold italic">Z</div>
                <span class="font-bold text-slate-900 tracking-tight">ZEWD</span>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 p-6 lg:p-10 min-h-screen pt-24 lg:pt-10 transition-all duration-300">
            <transition name="fade-slide" mode="out-in">
                <div :key="currentTab">
                    <Overview v-if="currentTab === 'Overview'" 
                        :stats="stats" 
                        :student-name="authStore.user?.name"
                        :teacher-name="stats.teacher"
                        :grade="stats.grade"
                        @goToLive="currentTab = 'Live Classes'"
                    />

                    <StudentHub v-else-if="currentTab === 'Learning Hub'" 
                        :subjects="subjects" 
                        :base-url="baseUrl"
                    />

                    <LiveClassManager v-else-if="currentTab === 'Live Classes'" 
                        :live-classes="liveClasses" 
                        @refresh="loadDashboard"
                    />

                    <Settings v-else-if="currentTab === 'Profile'" 
                        :student="studentProfile" 
                        :student-name="authStore.user?.name"
                        @save="updateProfile"
                        @updatePassword="handlePasswordUpdate"
                    />
                </div>
            </transition>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';
import apiClient from '../../services/api';

// Components
import Sidebar from './components/Sidebar.vue';
import Overview from './components/Overview.vue';
import StudentHub from './components/StudentHub.vue';
import LiveClassManager from './components/LiveClassManager.vue';
import Settings from './components/Settings.vue';

const authStore = useAuthStore();
const router = useRouter();

const currentTab = ref('Overview');
const isSidebarOpen = ref(false);
const stats = ref({});
const studentProfile = ref({});
const subjects = ref([]);
const liveClasses = ref([]);
const baseUrl = 'http://localhost:8000';
let liveClassesInterval = null;

const loadDashboard = async () => {
    try {
        const statsRes = await apiClient.get('/student/dashboard');
        stats.value = statsRes.data;
        studentProfile.value = statsRes.data.student_profile || {};

        const subjectsRes = await apiClient.get('/student/subjects');
        subjects.value = subjectsRes.data;

        const classesRes = await apiClient.get('/student/live-classes');
        liveClasses.value = classesRes.data;
    } catch (e) {
        console.error(e);
    }
};

const loadLiveClasses = async () => {
    try {
        const classesRes = await apiClient.get('/student/live-classes');
        liveClasses.value = classesRes.data;
    } catch (e) {
        console.error(e);
    }
};

const startLiveClassesPolling = () => {
    if (liveClassesInterval) return;
    liveClassesInterval = setInterval(() => {
        if (currentTab.value === 'Live Classes') {
            loadLiveClasses();
        }
    }, 8000);
};

const stopLiveClassesPolling = () => {
    if (liveClassesInterval) {
        clearInterval(liveClassesInterval);
        liveClassesInterval = null;
    }
};

const updateProfile = async (data) => {
    try {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        if (data.bio) formData.append('bio', data.bio);
        if (data.phone_number) formData.append('phone_number', data.phone_number);
        if (data.profile_image) formData.append('profile_image', data.profile_image);

        await apiClient.post('/student/profile', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        loadDashboard();
    } catch (err) {
        alert('Failed to update profile');
    }
};

const handlePasswordUpdate = async (data) => {
    try {
        await apiClient.put('/update-password', data);
        alert('Password updated successfully!');
    } catch (err) {
        console.error(err);
        alert(err.response?.data?.message || 'Failed to update password');
    }
};

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};

onMounted(() => {
    loadDashboard();
    startLiveClassesPolling();
});

watch(currentTab, (newTab) => {
    if (newTab === 'Live Classes') {
        loadLiveClasses();
    }
});

onBeforeUnmount(() => {
    stopLiveClassesPolling();
});
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(10px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-10px);
}
</style>
