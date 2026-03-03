<template>
    <div class="min-h-screen bg-slate-50 flex overflow-x-hidden">
        <!-- Sidebar -->
        <Sidebar 
            :active-tab="currentTab" 
            :teacher="teacherData" 
            :teacher-name="authStore.user?.name"
            :is-open="isSidebarOpen"
            @select="currentTab = $event" 
            @logout="handleLogout" 
            @close="isSidebarOpen = false"
        />

        <!-- Mobile Header (Visible only on mobile) -->
        <header class="fixed top-0 left-0 right-0 h-16 bg-white/80 backdrop-blur-md z-40 flex items-center px-6 border-b border-slate-100 lg:hidden">
            <button @click="isSidebarOpen = true" class="p-2 -ml-2 text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <div class="ml-4 flex items-center space-x-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold italic">Z</div>
                <span class="font-bold text-slate-900 tracking-tight">ZEWD</span>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 p-6 lg:p-10 min-h-screen pt-24 lg:pt-10 transition-all duration-300">
            <transition name="fade-slide" mode="out-in">
                <div :key="currentTab">
                    <Overview v-if="currentTab === 'Overview'" 
                        :stats="stats" 
                        :teacher-name="authStore.user?.name" 
                        @goToLive="currentTab = 'Live Classes'"
                    />

                    <StudentManager v-else-if="currentTab === 'Students'" 
                        :students="students" 
                        :grades="grades" 
                        @add="addStudent"
                        @toggleStatus="toggleStudentStatus"
                    />

                    <Academics v-else-if="currentTab === 'Academics'" 
                        :grades="grades" 
                        :subjects="subjects" 
                        @addGrade="addGrade"
                        @deleteGrade="deleteGrade"
                        @updateGrade="updateGrade"
                        @addSubject="addSubject"
                        @updateSubject="updateSubject"
                    />

                    <MaterialManager v-else-if="currentTab === 'Materials'" 
                        :materials="materials" 
                        :subjects="subjects" 
                        :base-url="baseUrl"
                        @upload="uploadMaterial"
                        @delete="deleteMaterial"
                    />

                    <LiveClassManager v-else-if="currentTab === 'Live Classes'" 
                        :live-classes="liveClasses" 
                        :grades="grades" 
                        :subjects="subjects" 
                        @create="createLiveClass"
                        @start="startClass"
                        @end="endClass"
                        @edit="editLiveClass"
                        @delete="deleteLiveClass"
                    />

                    <Settings v-else-if="currentTab === 'Settings'" 
                        :teacher="teacherData" 
                        :teacher-name="authStore.user?.name"
                        @save="updateProfile"
                        @updatePassword="handlePasswordUpdate"
                    />
                </div>
            </transition>
        </main>

        <!-- Subscription Overlay (if needed) -->
        <div v-if="subscription && !isSubscriptionActive(subscription)" class="fixed inset-0 z-[200] bg-slate-900/90 backdrop-blur-md flex items-center justify-center p-6">
            <div class="bg-white p-12 rounded-[48px] shadow-2xl max-w-lg text-center border-t-8 border-indigo-600 animate-in zoom-in duration-500">
                <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <h2 class="text-3xl font-black text-slate-900 mb-4">Account Pending</h2>
                <p class="mb-8 text-slate-500 font-medium leading-relaxed">Your professional academy is being verified. Access will be granted shortly after payment approval.</p>
                
                <div v-if="!subscription.payment_screenshot" class="space-y-4">
                     <select v-model="selectedPlanId" class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none font-bold text-sm">
                        <option :value="null" disabled>Select a plan</option>
                        <option v-for="plan in subscriptionPlans" :key="plan.id" :value="plan.id">
                            {{ plan.name }} - ${{ plan.price }} / {{ plan.duration_days }} days
                        </option>
                     </select>
                     <input type="file" ref="screenshotInput" class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none font-bold text-sm">
                     <button @click="uploadSubscription" :disabled="!subscriptionPlans.length" class="w-full bg-indigo-600 disabled:bg-slate-300 text-white py-5 rounded-3xl font-black shadow-xl shadow-indigo-600/30 hover:scale-105 transition-transform mt-4">Submit Payment Reference</button>
                     <p v-if="!subscriptionPlans.length" class="text-xs text-rose-500 font-semibold">No plans available. Ask admin to create a plan first.</p>
                </div>
                <div v-else class="py-4 bg-green-50 text-green-700 rounded-3xl font-bold flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Verification is under way
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';
import apiClient from '../../services/api';

// Components
import Sidebar from './components/Sidebar.vue';
import Overview from './components/Overview.vue';
import StudentManager from './components/StudentManager.vue';
import MaterialManager from './components/MaterialManager.vue';
import LiveClassManager from './components/LiveClassManager.vue';
import Academics from './components/Academics.vue';
import Settings from './components/Settings.vue';

const authStore = useAuthStore();
const router = useRouter();

const currentTab = ref('Overview');
const isSidebarOpen = ref(false);
const stats = ref({});
const subscription = ref(null);
const teacherData = ref({});
const students = ref([]);
const grades = ref([]);
const subjects = ref([]);
const materials = ref([]);
const liveClasses = ref([]);
const subscriptionPlans = ref([]);
const selectedPlanId = ref(null);
const screenshotInput = ref(null);
const baseUrl = 'http://localhost:8000';

const isSubscriptionActive = (sub) => {
    if (!sub || sub.status !== 'active') return false;
    if (!sub.end_date) return true;

    const end = new Date(sub.end_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return end >= today;
};

const loadDashboard = async () => {
    try {
        const [subRes, plansRes] = await Promise.all([
            apiClient.get('/teacher/subscription'),
            apiClient.get('/teacher/plans')
        ]);

        subscription.value = subRes.data || { status: 'inactive' };
        subscriptionPlans.value = Array.isArray(plansRes.data) ? plansRes.data : [];
        if (!selectedPlanId.value && subscriptionPlans.value.length) {
            selectedPlanId.value = subscriptionPlans.value[0].id;
        }

        if (isSubscriptionActive(subscription.value)) {
             const dashRes = await apiClient.get('/teacher/dashboard');
             stats.value = dashRes.data;
             teacherData.value = dashRes.data.teacher_profile || {};
             
             const [stu, gra, sub, mat, liv] = await Promise.all([
                 apiClient.get('/teacher/students'),
                 apiClient.get('/teacher/grades'),
                 apiClient.get('/teacher/subjects'),
                 apiClient.get('/teacher/materials'),
                 apiClient.get('/teacher/live-classes')
             ]);
             
             students.value = stu.data;
             grades.value = gra.data;
             subjects.value = sub.data;
             materials.value = mat.data;
             liveClasses.value = liv.data;
        }
    } catch (e) {
        console.error(e);
    }
};

const uploadSubscription = async () => {
    try {
        const screenshot = screenshotInput.value?.files?.[0];

        if (!selectedPlanId.value) {
            alert('Please select a subscription plan');
            return;
        }

        if (!screenshot) {
            alert('Please upload a payment screenshot');
            return;
        }

        const formData = new FormData();
        formData.append('plan_id', String(selectedPlanId.value));
        formData.append('screenshot', screenshot);

        await apiClient.post('/teacher/subscription', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        alert('Subscription request submitted successfully');
        await loadDashboard();
    } catch (err) {
        console.error(err);
        alert(err.response?.data?.message || 'Failed to submit subscription request');
    }
};

const addStudent = async (data) => {
    try {
        await apiClient.post('/teacher/students', data);
        loadDashboard();
    } catch (err) {
        alert('Failed to add student');
    }
};

const toggleStudentStatus = async (id) => {
    try {
        const response = await apiClient.post(`/teacher/students/${id}/toggle-status`);
        const student = students.value.find(s => s.id === id);
        if (student) student.status = response.data.status;
    } catch (err) {
        console.error(err);
        alert('Failed to update student status');
    }
};

const addGrade = async (name) => {
    await apiClient.post('/teacher/grades', { name });
    loadDashboard();
};

const updateGrade = async ({ id, name }) => {
    try {
        await apiClient.put(`/teacher/grades/${id}`, { name });
        loadDashboard();
    } catch (err) {
        console.error(err);
        alert('Failed to update grade');
    }
};

const deleteGrade = async (id) => {
    await apiClient.delete(`/teacher/grades/${id}`);
    loadDashboard();
};

const addSubject = async (data) => {
    await apiClient.post('/teacher/subjects', data);
    loadDashboard();
};

const updateSubject = async ({ id, name }) => {
    try {
        await apiClient.put(`/teacher/subjects/${id}`, { name });
        loadDashboard();
    } catch (err) {
        console.error(err);
        alert('Failed to update subject');
    }
};

const uploadMaterial = async (formData) => {
    await apiClient.post('/teacher/materials', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    });
    loadDashboard();
};

const deleteMaterial = async (id) => {
    await apiClient.delete(`/teacher/materials/${id}`);
    loadDashboard();
};

const createLiveClass = async (data) => {
    await apiClient.post('/teacher/live-classes', data);
    loadDashboard();
};

const editLiveClass = async (cls) => {
    const title = prompt('Class title', cls.title);
    if (!title) return;

    const startValue = new Date(cls.start_time);
    const formatted = `${startValue.getFullYear()}-${String(startValue.getMonth() + 1).padStart(2, '0')}-${String(startValue.getDate()).padStart(2, '0')}T${String(startValue.getHours()).padStart(2, '0')}:${String(startValue.getMinutes()).padStart(2, '0')}`;
    const start_time = prompt('Start time (YYYY-MM-DDTHH:mm)', formatted);
    if (!start_time) return;

    await apiClient.put(`/teacher/live-classes/${cls.meeting_id}`, {
        title,
        start_time,
        grade_id: cls.grade_id,
        subject_id: cls.subject_id,
    });
    loadDashboard();
};

const deleteLiveClass = async (meetingId) => {
    if (!confirm('Delete this class?')) return;
    await apiClient.delete(`/teacher/live-classes/${meetingId}`);
    loadDashboard();
};

const startClass = async (meetingId) => {
    try {
        await apiClient.post(`/teacher/live-classes/${meetingId}/start`);
        loadDashboard();
        router.push(`/live-class/${meetingId}`);
    } catch (err) {
        alert('Failed to start class');
    }
};

const endClass = async (meetingId) => {
    if (!confirm('Stop session for all?')) return;
    await apiClient.post(`/teacher/live-classes/${meetingId}/end`);
    loadDashboard();
};

const updateProfile = async (data) => {
    try {
        const formData = new FormData();
        formData.append('_method', 'PUT'); // Laravel requirement for spoofing PUT with FormData
        if (data.academy_name) formData.append('academy_name', data.academy_name);
        if (data.bio) formData.append('bio', data.bio);
        if (data.specialization) formData.append('specialization', data.specialization);
        if (data.phone_number) formData.append('phone_number', data.phone_number);
        if (data.social_links) formData.append('social_links', JSON.stringify(data.social_links));
        if (data.profile_image) formData.append('profile_image', data.profile_image);

        await apiClient.post('/teacher/profile', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        loadDashboard();
    } catch (err) {
        console.error(err);
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
