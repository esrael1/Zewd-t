<template>
    <div class="h-screen flex flex-col">
        <nav class="bg-gray-900 text-white px-6 py-3 flex justify-between items-center">
            <h1 class="font-bold">Live Class</h1>
            <button @click="leaveClass" class="bg-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-600">Exit Class</button>
        </nav>
        <div id="jitsi-container" class="flex-1 bg-black"></div>
    </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import apiClient from '../services/api';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
let api = null;
const jitsiToken = ref('');
const resolvedRole = ref('');
const useJwt = import.meta.env.VITE_JITSI_USE_JWT === 'true';
const meetingId = String(route.params.meetingId);
const isEndingClass = ref(false);
let studentStatusInterval = null;

const fetchToken = async () => {
    try {
        // Prefer persisted role from store/localStorage; user object may still be null in a new tab.
        let role = authStore.role || authStore.user?.role || localStorage.getItem('role');
        if (!role && authStore.token) {
            await authStore.fetchUser();
            role = authStore.role || authStore.user?.role || localStorage.getItem('role');
        }
        if (!role) {
            console.error('Unable to resolve role for live class token request');
            return false;
        }

        resolvedRole.value = role;
        const res = await apiClient.get(`/${role}/live-classes/${route.params.meetingId}/token`);
        jitsiToken.value = res.data.token;
        return true;
    } catch (err) {
        console.error('Failed to fetch Jitsi token', err);
        const serverMessage = err?.response?.data?.message;
        if (serverMessage) {
            alert(serverMessage);
        }
        return false;
    }
};

const ensureStudentCanJoin = async () => {
    const role = resolvedRole.value || authStore.role || authStore.user?.role || localStorage.getItem('role');
    if (role !== 'student') return true;

    try {
        const res = await apiClient.get('/student/live-classes');
        const current = Array.isArray(res.data)
            ? res.data.find((cls) => cls.meeting_id === meetingId)
            : null;

        if (!current || current.status !== 'live') {
            alert('Wait for teacher to start this class.');
            router.push('/student');
            return false;
        }
        return true;
    } catch (err) {
        console.error('Failed to validate class status', err);
        alert('Unable to join this class right now.');
        router.push('/student');
        return false;
    }
};

onMounted(async () => {
    resolvedRole.value = authStore.role || authStore.user?.role || localStorage.getItem('role') || 'student';

    if (useJwt) {
        const ok = await fetchToken();
        if (!ok || !jitsiToken.value) {
            alert('Unable to join this live class. Please re-open from your dashboard.');
            const role = authStore.role || authStore.user?.role;
            const redirectPath = role === 'teacher' ? '/teacher' : '/student';
            router.push(redirectPath);
            return;
        }
    }

    const canJoin = await ensureStudentCanJoin();
    if (!canJoin) return;
    startStudentStatusPolling();
    
    // Load Jitsi script from configured instance
    const jitsiUrl = import.meta.env.VITE_JITSI_URL || 'https://meet.jit.si';
    const script = document.createElement('script');
    script.src = `${jitsiUrl.replace(/\/$/, '')}/external_api.js`;
    script.async = true;
    script.onload = initJitsi;
    document.body.appendChild(script);
});

const initJitsi = () => {
    const jitsiUrl = import.meta.env.VITE_JITSI_URL || 'https://meet.jit.si';
    const domain = jitsiUrl.replace(/^https?:\/\//, '').replace(/\/$/, '');
    
    const options = {
        roomName: route.params.meetingId,
        width: '100%',
        height: '100%',
        parentNode: document.querySelector('#jitsi-container'),
        configOverwrite: {
            startWithAudioMuted: true,
            startWithVideoMuted: true
        },
        interfaceConfigOverwrite: {
            TOOLBAR_BUTTONS: [
                'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                'fodeviceselection', 'hangup', 'profile', 'chat', 'recording',
                'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
                'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
                'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone',
                'e2ee'
            ],
        },
    };
    if (useJwt && jitsiToken.value) {
        options.jwt = jitsiToken.value;
    }
    api = new window.JitsiMeetExternalAPI(domain, options);

    // Add listeners for meeting end.
    api.addEventListeners({
        videoConferenceTerminated: handleMeetingEnd,
        readyToClose: handleMeetingEnd
    });
};

const markClassEndedIfTeacher = async () => {
    const role = resolvedRole.value || authStore.role || authStore.user?.role || localStorage.getItem('role');
    if (role !== 'teacher' || isEndingClass.value) return;
    isEndingClass.value = true;
    try {
        await apiClient.post(`/teacher/live-classes/${meetingId}/end`);
    } catch (err) {
        console.error('Failed to mark class ended', err);
    } finally {
        isEndingClass.value = false;
    }
};

const checkStudentClassStatus = async () => {
    const role = resolvedRole.value || authStore.role || authStore.user?.role || localStorage.getItem('role');
    if (role !== 'student') return;

    try {
        const res = await apiClient.get('/student/live-classes');
        const current = Array.isArray(res.data)
            ? res.data.find((cls) => cls.meeting_id === meetingId)
            : null;

        if (!current || current.status === 'ended') {
            if (api) {
                api.dispose();
                api = null;
            }
            stopStudentStatusPolling();
            alert('Class has ended. Returning to dashboard.');
            router.push('/student');
        }
    } catch (err) {
        console.error('Failed to refresh class status', err);
    }
};

const startStudentStatusPolling = () => {
    const role = resolvedRole.value || authStore.role || authStore.user?.role || localStorage.getItem('role');
    if (role !== 'student') return;
    if (studentStatusInterval) clearInterval(studentStatusInterval);
    studentStatusInterval = setInterval(checkStudentClassStatus, 10000);
};

const stopStudentStatusPolling = () => {
    if (studentStatusInterval) {
        clearInterval(studentStatusInterval);
        studentStatusInterval = null;
    }
};

const handleMeetingEnd = async () => {
    await markClassEndedIfTeacher();

    const role = resolvedRole.value || authStore.role || authStore.user?.role || 'student';
    const redirectPath = role === 'teacher' ? '/teacher' : '/student';

    // Dispose the API and redirect.
    if (api) {
        api.dispose();
        api = null;
    }
    stopStudentStatusPolling();
    router.push(redirectPath);
};

const leaveClass = async () => {
    await markClassEndedIfTeacher();

    if (api) {
        try {
            api.executeCommand('hangup');
        } catch (err) {
            console.error('Failed to hang up cleanly', err);
            api.dispose();
            api = null;
            handleMeetingEnd();
        }
    } else {
        handleMeetingEnd();
    }
};

onBeforeUnmount(() => {
    stopStudentStatusPolling();
    if (api) {
        api.dispose();
    }
});
</script>
