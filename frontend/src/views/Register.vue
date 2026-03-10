<template>
    <div class="min-h-screen bg-slate-50 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-indigo-200/40 rounded-full blur-[80px]"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-emerald-200/40 rounded-full blur-[80px]"></div>

        <div class="relative z-10 min-h-screen flex items-center justify-center px-6 py-16">
            <div class="w-full max-w-2xl">
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
                            <h2 class="text-2xl font-black text-slate-900">Create your account</h2>
                            <p class="text-sm text-slate-500 font-medium">Join as a student or teacher and start learning.</p>
                        </div>
                    </div>

                    <form @submit.prevent="handleRegister" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-600 text-sm font-bold mb-2">Name</label>
                                <input v-model="form.name" type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-slate-600 text-sm font-bold mb-2">Role</label>
                                <select v-model="form.role" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="student">Student</option>
                                    <option value="teacher">Teacher</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-600 text-sm font-bold mb-2">Email</label>
                                <input v-model="form.email" type="email" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-slate-600 text-sm font-bold mb-2">Phone (Optional)</label>
                                <input v-model="form.phone" type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div v-if="form.role === 'teacher'" class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-600 text-sm font-bold mb-2">Academy Name</label>
                                    <input v-model="form.academy_name" type="text" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                </div>
                                <div>
                                    <label class="block text-slate-600 text-sm font-bold mb-2">Bio</label>
                                    <textarea v-model="form.bio" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-600 text-sm font-bold mb-2">Password</label>
                                <input v-model="form.password" type="password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-slate-600 text-sm font-bold mb-2">Confirm Password</label>
                                <input v-model="form.password_confirmation" type="password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </div>
                        </div>

                        <div v-if="error" class="text-red-500 text-sm text-center">{{ error }}</div>

                        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-black hover:bg-indigo-700 transition duration-200 shadow-lg shadow-indigo-500/20">
                            Register
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm text-slate-500">
                        Already have an account?
                        <router-link to="/login" class="text-indigo-600 font-bold hover:underline">Login</router-link>
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
