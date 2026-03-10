<template>
    <div class="min-h-screen bg-slate-50 flex overflow-x-hidden">
        <!-- Sidebar -->
        <Sidebar 
            :active-tab="currentTab" 
            :user-name="authStore.user?.name"
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
                <img :src="logoUrl" alt="Zewd" class="w-12 h-12 rounded-lg bg-white p-1.5 shadow-sm object-contain" />
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 p-6 lg:p-10 min-h-screen pt-24 lg:pt-10 transition-all duration-300">
            <transition name="fade-slide" mode="out-in">
                <div :key="currentTab">
                    <SystemOverview v-if="currentTab === 'Overview'" 
                        :stats="stats" 
                        @select="currentTab = $event"
                    />

                    <TeacherApprovalHub v-else-if="currentTab === 'Teachers'" 
                        :teachers="teachers" 
                        @approve="approveTeacher"
                        @reject="rejectTeacher"
                    />

                    <SubscriptionVault v-else-if="currentTab === 'Subscriptions'" 
                        :subscriptions="subscriptions" 
                        @approve="approveSubscription"
                    />

                    <UniversalStudentDirectory v-else-if="currentTab === 'Students'" 
                        :students="students" 
                    />

                    <DatabaseExplorer v-else-if="currentTab === 'Database'"
                        :database-data="databaseData"
                        @refresh="loadDashboardData"
                    />

                    <PlanArchitect v-else-if="currentTab === 'Plans'" 
                        :plans="plans" 
                        @save="handlePlanSave"
                        @delete="deletePlan"
                    />

                    <Settings v-else-if="currentTab === 'Settings'" 
                        :user-name="authStore.user?.name"
                        @updatePassword="handlePasswordUpdate"
                    />
                </div>
            </transition>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';
import apiClient from '../../services/api';
import logoUrl from '../../assets/Zewd.png';

// Components
import Sidebar from './components/Sidebar.vue';
import SystemOverview from './components/SystemOverview.vue';
import TeacherApprovalHub from './components/TeacherApprovalHub.vue';
import SubscriptionVault from './components/SubscriptionVault.vue';
import PlanArchitect from './components/PlanArchitect.vue';
import UniversalStudentDirectory from './components/UniversalStudentDirectory.vue';
import DatabaseExplorer from './components/DatabaseExplorer.vue';
import Settings from './components/Settings.vue';

const authStore = useAuthStore();
const router = useRouter();

const currentTab = ref('Overview');
const isSidebarOpen = ref(false);
const stats = ref({});
const teachers = ref([]);
const subscriptions = ref([]);
const students = ref([]);
const plans = ref([]);
const databaseData = ref({ counts: {}, tables: {} });

const loadDashboardData = async () => {
    const [statsRes, teachersRes, subsRes, studentsRes, plansRes, dbRes] = await Promise.allSettled([
        apiClient.get('/admin/dashboard'),
        apiClient.get('/admin/teachers'),
        apiClient.get('/admin/subscriptions'),
        apiClient.get('/admin/students'),
        apiClient.get('/admin/plans'),
        apiClient.get('/admin/database-data')
    ]);

    if (statsRes.status === 'fulfilled') stats.value = statsRes.value.data;
    else console.error('Failed to load admin dashboard stats', statsRes.reason);

    if (teachersRes.status === 'fulfilled') teachers.value = teachersRes.value.data;
    else console.error('Failed to load teachers', teachersRes.reason);

    if (subsRes.status === 'fulfilled') subscriptions.value = subsRes.value.data;
    else console.error('Failed to load subscriptions', subsRes.reason);

    if (studentsRes.status === 'fulfilled') students.value = studentsRes.value.data;
    else console.error('Failed to load students', studentsRes.reason);

    if (plansRes.status === 'fulfilled') plans.value = plansRes.value.data;
    else console.error('Failed to load plans', plansRes.reason);

    if (dbRes.status === 'fulfilled') databaseData.value = dbRes.value.data;
    else console.error('Failed to load database data', dbRes.reason);
};

const approveTeacher = async (id) => {
    try {
        await apiClient.post(`/admin/teachers/${id}/approve`);
        loadDashboardData();
    } catch (e) {
        alert('Failed to approve teacher');
    }
};

const rejectTeacher = async (id) => {
    try {
        await apiClient.post(`/admin/teachers/${id}/reject`);
        loadDashboardData();
    } catch (e) {
        alert('Failed to reject teacher');
    }
};

const approveSubscription = async (id) => {
    try {
        await apiClient.post(`/admin/subscriptions/${id}/approve`);
        loadDashboardData();
    } catch (e) {
        alert('Failed to activate subscription');
    }
};

const handlePlanSave = async (plan) => {
    try {
        if (plan.id) {
            await apiClient.put(`/admin/plans/${plan.id}`, plan);
        } else {
            await apiClient.post('/admin/plans', plan);
        }
        loadDashboardData();
    } catch (e) {
        alert('Failed to save plan');
    }
};

const deletePlan = async (id) => {
    if (confirm('Permanently delete this platform tier?')) {
        try {
            await apiClient.delete(`/admin/plans/${id}`);
            loadDashboardData();
        } catch (e) {
            alert('Failed to delete plan');
        }
    }
};

const handlePasswordUpdate = async (data) => {
    try {
        await apiClient.put('/update-password', data);
        alert('Admin password updated successfully!');
    } catch (err) {
        console.error(err);
        alert(err.response?.data?.message || 'Failed to update admin password');
    }
};

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};

onMounted(() => {
    loadDashboardData();
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
