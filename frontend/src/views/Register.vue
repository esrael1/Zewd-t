<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100 py-12">
      <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Create an Account</h2>
        <form @submit.prevent="handleRegister">
          <div class="grid grid-cols-2 gap-4 mb-4">
               <div>
                  <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                  <input v-model="form.name" type="text" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
               </div>
               <div>
                  <label class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                  <select v-model="form.role" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                      <option value="student">Student</option>
                      <option value="teacher">Teacher</option>
                  </select>
               </div>
          </div>
  
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input v-model="form.email" type="email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
          </div>
  
          <div class="mb-4">
             <label class="block text-gray-700 text-sm font-bold mb-2">Phone (Optional)</label>
             <input v-model="form.phone" type="text" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
           </div>
  
           <!-- Teacher Fields -->
           <div v-if="form.role === 'teacher'" class="mb-4 p-4 bg-gray-50 rounded border">
                <div class="mb-3">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Academy Name</label>
                    <input v-model="form.academy_name" type="text" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div>
                  <label class="block text-gray-700 text-sm font-bold mb-2">Bio</label>
                  <textarea v-model="form.bio" class="w-full px-3 py-2 border rounded-lg" rows="2"></textarea>
                </div>
           </div>
  
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input v-model="form.password" type="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
          </div>
  
          <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
          </div>
  
          <div v-if="error" class="mb-4 text-red-500 text-sm text-center">{{ error }}</div>
  
          <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition duration-200">Register</button>
        </form>
        <div class="mt-4 text-center text-sm">
            Already have an account? <router-link to="/login" class="text-indigo-600 hover:underline">Login</router-link>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useAuthStore } from '../stores/auth';
  import { useRouter } from 'vue-router';
  
  const form = ref({ 
      name: '', 
      email: '', 
      phone: '', 
      password: '', 
      password_confirmation: '', 
      role: 'student',
      academy_name: '',
      bio: ''
  });
  
  const authStore = useAuthStore();
  const router = useRouter();
  const error = ref('');
  
  const handleRegister = async () => {
      try {
          await authStore.register(form.value);
          // Redirect based on role
           if (authStore.isAdmin) router.push('/admin');
          else if (authStore.isTeacher) router.push('/teacher');
          else if (authStore.isStudent) router.push('/student');
      } catch (e) {
          error.value = e.response?.data?.message || 'Registration failed';
          console.error(e);
      }
  };
  </script>
