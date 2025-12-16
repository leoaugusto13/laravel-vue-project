<template>
  <div>
    <div class="welcome-banner">
      <div class="banner-text">
        <h1>Olá, {{ user?.name }}! 👋</h1>
        <p>Tenha um ótimo dia de trabalho.</p>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Usuários</h3>
          <p class="value">145</p>
        </div>
      </div>
      <div class="stat-card">
         <div class="stat-icon purple">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Cadastros</h3>
          <p class="value">24</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const user = ref(null);

onMounted(() => {
  const userData = localStorage.getItem('user');
  if (userData) {
    user.value = JSON.parse(userData);
  }
});
</script>

<style scoped>
.welcome-banner {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 2rem;
  color: white;
  margin-bottom: 2rem;
  box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
}
.welcome-banner h1 { margin: 0 0 0.5rem 0; font-size: 1.5rem; }

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}
.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.stat-icon {
  width: 3rem; height: 3rem; border-radius: 12px;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.stat-icon svg { width: 1.5rem; height: 1.5rem; }
.stat-icon.blue { background: #eff6ff; color: #3b82f6; }
.stat-icon.purple { background: #f3e8ff; color: #9333ea; }
.stat-info h3 { margin: 0; color: #6b7280; font-size: 0.875rem; font-weight: 500; }
.stat-info .value { margin: 0.25rem 0; color: #1f2937; font-size: 1.5rem; font-weight: 700; }
</style>
