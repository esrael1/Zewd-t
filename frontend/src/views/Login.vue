<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
      <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Login to ZEWD</h2>
        <form @submit.prevent="handleLogin">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
            <input v-model="form.email" type="email" id="email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
          </div>
          <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
            <input v-model="form.password" type="password" id="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
          </div>
           <div v-if="error" class="mb-4 text-red-500 text-sm text-center">{{ error }}</div>
          <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition duration-200">Login</button>
        </form>
        <div class="mt-4 text-center text-sm">
            Don't have an account? <router-link to="/register" class="text-indigo-600 hover:underline">Register</router-link>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useAuthStore } from '../stores/auth';
  import { useRouter } from 'vue-router';
  
  const form = ref({ email: '', password: '' });
  const authStore = useAuthStore();
  const router = useRouter();
  const error = ref('');
  
  const handleLogin = async () => {
      try {
          await authStore.login(form.value);
          if (authStore.isAdmin) router.push('/admin');
          else if (authStore.isTeacher) router.push('/teacher');
          else if (authStore.isStudent) router.push('/student');
      } catch (e) {
          error.value = e.response?.data?.message || 'Login failed';
      }
  };
  </script>
