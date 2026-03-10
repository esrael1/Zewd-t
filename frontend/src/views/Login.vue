<template>
    <div class="min-h-screen bg-slate-50 relative overflow-hidden">
        <div class="absolute -top-24 -left-24 w-72 h-72 bg-indigo-200/40 rounded-full blur-[80px]"></div>
        <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-emerald-200/40 rounded-full blur-[80px]"></div>

        <div class="relative z-10 min-h-screen flex items-center justify-center px-6 py-16">
            <div class="w-full max-w-lg">
                <div class="flex items-center justify-between mb-6">
                    <router-link to="/" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back to Home
                    </router-link>
                </div>

                <div class="bg-white/90 backdrop-blur-xl border border-slate-100 p-10 rounded-[32px] shadow-2xl shadow-indigo-500/10">
                    <div class="flex items-center gap-4 mb-8">
                        <img :src="logoUrl" alt="Zewd" class="w-14 h-14 rounded-2xl bg-white p-2 shadow-lg object-contain" />
                        <div>
                            <h2 class="text-2xl font-black text-slate-900">Welcome back</h2>
                            <p class="text-sm text-slate-500 font-medium">Sign in to continue your learning journey.</p>
                        </div>
                    </div>

                    <form @submit.prevent="handleLogin" class="space-y-5">
                        <div>
                            <label class="block text-slate-600 text-sm font-bold mb-2" for="email">Email</label>
                            <input v-model="form.email" type="email" id="email" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-slate-600 text-sm font-bold mb-2" for="password">Password</label>
                            <input v-model="form.password" type="password" id="password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>

                        <div v-if="error" class="text-red-500 text-sm text-center">{{ error }}</div>

                        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-black hover:bg-indigo-700 transition duration-200 shadow-lg shadow-indigo-500/20">
                            Login
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm text-slate-500">
                        Don't have an account?
                        <router-link to="/register" class="text-indigo-600 font-bold hover:underline">Register</router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
  
  <script setup>
  import { ref } from 'vue';
  import { useAuthStore } from '../stores/auth';
  import { useRouter } from 'vue-router';
  import logoUrl from '../assets/Zewd.png';
  
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
