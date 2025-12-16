<template>
  <div class="reports-page">
    <h1>Relatórios do Sistema</h1>
    
    <div v-if="loading" class="loading">Carregando dados...</div>
    
    <div v-else class="content-wrapper">
      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-value">{{ reportData.total_users }}</div>
          <div class="stat-label">Usuários Totais</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ reportData.total_courses }}</div>
          <div class="stat-label">Cursos Ativos</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ reportData.total_enrollments }}</div>
          <div class="stat-label">Total de Inscrições</div>
        </div>
      </div>

      <!-- Charts/Tables Section -->
      <div class="charts-section">
        <!-- Top Courses by Enrollment -->
        <div class="chart-container">
          <h2>Cursos Mais Populares</h2>
          <div class="bar-chart">
            <div v-for="item in reportData.enrollments_by_course" :key="item.course_title" class="bar-item">
              <div class="bar-label">{{ item.course_title }}</div>
              <div class="bar-track">
                <div class="bar-fill" :style="{ width: calculatePercentage(item.count) + '%' }"></div>
                <span class="bar-count">{{ item.count }}</span>
              </div>
            </div>
            <div v-if="reportData.enrollments_by_course.length === 0" class="empty-chart">
              Sem dados de inscrições.
            </div>
          </div>
        </div>

        <!-- Recent Enrollments -->
        <div class="recent-list">
          <h2>Últimas Inscrições</h2>
          <table class="recent-table">
            <thead>
              <tr>
                <th>Usuário</th>
                <th>Curso</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="enrollment in reportData.recent_enrollments" :key="enrollment.id">
                <td>{{ enrollment.user.name }}</td>
                <td>{{ enrollment.course.title }}</td>
                <td>{{ formatDate(enrollment.created_at) }}</td>
              </tr>
              <tr v-if="reportData.recent_enrollments.length === 0">
                <td colspan="3" class="empty-table">Nenhuma inscrição recente.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(true);
const reportData = ref({
  total_users: 0,
  total_courses: 0,
  total_enrollments: 0,
  enrollments_by_course: [],
  recent_enrollments: []
});

onMounted(() => {
  fetchReports();
});

const fetchReports = async () => {
  try {
    const response = await axios.get('/api/reports', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    reportData.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar relatórios:', error);
  } finally {
    loading.value = false;
  }
};

const calculatePercentage = (count) => {
  if (reportData.value.total_enrollments === 0) return 0;
  // Calculate relative to the highest count to make bars look good
  const max = Math.max(...reportData.value.enrollments_by_course.map(i => i.count));
  return (count / max) * 100;
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('pt-BR');
};
</script>

<style scoped>
.reports-page { padding: 1.5rem; }
h1 { margin-bottom: 2rem; color: #1e293b; font-size: 1.5rem; }
h2 { font-size: 1.1rem; color: #334155; margin-bottom: 1rem; }

.loading { color: #64748b; font-style: italic; }

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white; padding: 1.5rem; border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: center;
}

.stat-value { font-size: 2.5rem; font-weight: 700; color: #6366f1; }
.stat-label { color: #64748b; font-size: 0.875rem; font-weight: 500; }

.charts-section {
  display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;
}
@media (max-width: 768px) {
  .charts-section { grid-template-columns: 1fr; }
}

.chart-container, .recent-list {
  background: white; padding: 1.5rem; border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.bar-item { margin-bottom: 1rem; }
.bar-label { font-size: 0.875rem; color: #475569; margin-bottom: 0.25rem; }
.bar-track { display: flex; align-items: center; gap: 0.5rem; }
.bar-fill {
  height: 8px; background: #6366f1; border-radius: 4px;
  transition: width 0.5s ease;
}
.bar-count { font-size: 0.75rem; color: #64748b; font-weight: 600; }

.recent-table { width: 100%; border-collapse: collapse; }
.recent-table th { text-align: left; padding: 0.5rem; font-size: 0.75rem; color: #94a3b8; border-bottom: 1px solid #e2e8f0; }
.recent-table td { padding: 0.75rem 0.5rem; font-size: 0.875rem; color: #334155; border-bottom: 1px solid #f1f5f9; }
.empty-table, .empty-chart { color: #94a3b8; font-style: italic; font-size: 0.875rem; padding: 1rem; text-align: center; }
</style>
