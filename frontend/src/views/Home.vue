<template>
    <div class="min-h-screen bg-slate-50 font-inter overflow-x-hidden">
        <!-- Premium Navbar -->
        <nav class="fixed top-0 left-0 right-0 z-[100] bg-white/70 backdrop-blur-xl border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <div class="flex items-center space-x-2 group cursor-pointer">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black italic shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">Z</div>
                    <span class="text-2xl font-black text-slate-900 tracking-tighter uppercase">Zewd</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-slate-500 hover:text-indigo-600 font-bold transition-colors">Features</a>
                    <router-link to="/login" class="text-slate-900 font-black hover:text-indigo-600 transition-colors">Sign In</router-link>
                    <router-link to="/register" class="bg-indigo-600 text-white px-8 py-3 rounded-2xl font-black shadow-xl shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all">Get Started</router-link>
                </div>
                <!-- Mobile Menu Button (Basic) -->
                <button class="md:hidden p-2 text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </nav>

        <!-- Hero Section -->
        <header class="pt-40 pb-20 px-6 relative overflow-hidden">
            <div class="max-w-7xl mx-auto relative z-10 text-center">
                <div class="inline-flex items-center space-x-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-full text-xs font-black uppercase tracking-widest mb-8 animate-in slide-in-from-bottom duration-500">
                    <span class="flex h-2 w-2 rounded-full bg-indigo-600 animate-pulse"></span>
                    <span>Empowering Independent Educators</span>
                </div>
                <h1 class="text-5xl lg:text-7xl font-black text-slate-900 mb-8 tracking-tighter leading-[1.1] max-w-4xl mx-auto">
                    Master Your Future with <span class="bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">ZEWD</span>
                </h1>
                <p class="text-lg lg:text-xl text-slate-500 font-medium mb-12 max-w-2xl mx-auto leading-relaxed">
                    The ultimate command center for teachers to build academies and students to unlock their full potential through live interactive learning.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <router-link to="/register" class="w-full sm:w-auto bg-indigo-600 text-white px-10 py-5 rounded-[24px] font-black text-lg shadow-2xl shadow-indigo-500/30 hover:scale-105 active:scale-95 transition-all">
                        Launch Your Academy
                    </router-link>
                    <router-link to="/login" class="w-full sm:w-auto bg-white text-slate-900 border border-slate-200 px-10 py-5 rounded-[24px] font-black text-lg hover:bg-slate-50 transition-all">
                        Student Portal
                    </router-link>
                </div>
            </div>
            
            <!-- Abstract Background -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-indigo-400/10 rounded-full blur-[120px] pointer-events-none"></div>
        </header>

        <!-- Features Grid -->
        <section id="features" class="py-24 px-6 relative bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="feature in features" :key="feature.title" class="p-10 bg-slate-50 rounded-[40px] hover:bg-white hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 group border border-transparent hover:border-slate-100">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-8 shadow-sm group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                            <component :is="feature.icon" class="w-8 h-8" />
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 mb-4">{{ feature.title }}</h3>
                        <p class="text-slate-500 font-medium leading-relaxed">{{ feature.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dynamic CTA -->
        <section class="py-20 px-6">
            <div class="max-w-5xl mx-auto bg-slate-900 rounded-[48px] p-12 lg:p-20 relative overflow-hidden text-center">
                <div class="relative z-10">
                    <h2 class="text-4xl lg:text-5xl font-black text-white mb-6 tracking-tight">Ready to join the revolution?</h2>
                    <p class="text-slate-400 text-lg mb-12 max-w-xl mx-auto">Step into a world where education knows no boundaries. Start your journey today.</p>
                    <router-link to="/register" class="inline-block bg-white text-slate-900 px-12 py-5 rounded-[24px] font-black text-lg hover:scale-105 active:scale-95 transition-all">
                        Get Started for Free
                    </router-link>
                </div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-600/20 rounded-full blur-[80px] -mr-32 -mt-32"></div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 px-6 border-t border-slate-100 text-center">
            <p class="text-slate-400 font-bold text-sm uppercase tracking-widest">&copy; 2026 ZEWD Platform. Built for the Future.</p>
        </footer>
    </div>
</template>

<script setup>
const features = [
    { 
        title: 'For Educators', 
        desc: 'Establish your professional academy, manage pricing tiers, and mentor students with ease.',
        icon: 'UserIcon'
    },
    { 
        title: 'Live Classrooms', 
        desc: 'High-quality interactive sessions with Jitsi Meet integration and real-time dashboard control.',
        icon: 'MonitorIcon'
    },
    { 
        title: 'Global Resources', 
        desc: 'Access a centralized hub for learning materials, curriculum tracking, and progress management.',
        icon: 'BoltIcon'
    }
];

const UserIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>' };
const MonitorIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>' };
const BoltIcon = { template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>' };
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
.font-inter { font-family: 'Inter', sans-serif; }
</style>
