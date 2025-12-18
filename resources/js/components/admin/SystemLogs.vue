<template>
  <div class="logs-page">
    <div class="page-header">
      <h1>Ações do Sistema</h1>
      <p>Histórico de atividades e auditoria.</p>
    </div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Data/Hora</th>
            <th>Usuário</th>
            <th>Ação</th>
            <th>Descrição</th>
            <th>IP</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="log in logs" :key="log.id">
            <td>{{ formatDateTime(log.created_at) }}</td>
            <td>
              <div v-if="log.user">
                <div class="user-name">{{ log.user.name }}</div>
                <div class="user-email">{{ log.user.email }}</div>
              </div>
              <span v-else class="guest">Visitante / Sistema</span>
            </td>
            <td>
              <span class="action-badge">{{ log.action }}</span>
            </td>
            <td class="description-cell">{{ log.description }}</td>
            <td>{{ log.ip_address }}</td>
          </tr>
          <tr v-if="logs.length === 0">
            <td colspan="5" class="empty-state">Nenhum registro encontrado.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.last_page > 1" class="pagination">
      <button 
        :disabled="pagination.current_page === 1" 
        @click="changePage(pagination.current_page - 1)"
        class="page-btn"
      >
        Anterior
      </button>
      <span class="page-info">Página {{ pagination.current_page }} de {{ pagination.last_page }}</span>
      <button 
        :disabled="pagination.current_page === pagination.last_page" 
        @click="changePage(pagination.current_page + 1)"
        class="page-btn"
      >
        Próxima
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const logs = ref([]);
const pagination = ref({
  current_page: 1,
  last_page: 1
});

onMounted(() => {
  fetchLogs();
});

const fetchLogs = async (page = 1) => {
  try {
    const response = await axios.get(`/api/admin/logs?page=${page}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    logs.value = response.data.data;
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page
    };
  } catch (error) {
    console.error('Erro ao buscar logs:', error);
  }
};

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchLogs(page);
  }
};

const formatDateTime = (dateString) => {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleString('pt-BR');
};
</script>

<style scoped>
.logs-page { padding: 1rem; }

.page-header { margin-bottom: 2rem; }
.page-header h1 { font-size: 1.5rem; color: #1e293b; margin: 0 0 0.5rem 0; }
.page-header p { color: #64748b; margin: 0; }

.table-container {
  background: white; border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow-x: auto;
}

table { width: 100%; border-collapse: collapse; }
th { text-align: left; padding: 1rem 1.5rem; background: #f8fafc; color: #64748b; font-size: 0.875rem; border-bottom: 1px solid #e2e8f0; }
td { padding: 1rem 1.5rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: top; }

.user-name { font-weight: 600; font-size: 0.875rem; }
.user-email { font-size: 0.75rem; color: #64748b; }
.guest { color: #94a3b8; font-style: italic; }

.action-badge {
  background: #f1f5f9; color: #475569;
  padding: 0.25rem 0.5rem; border-radius: 4px;
  font-family: monospace; font-size: 0.75rem; font-weight: 600;
}

.description-cell { max-width: 400px; white-space: normal; }
.empty-state { text-align: center; color: #94a3b8; padding: 3rem; }

.pagination { display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 2rem; }
.page-btn {
  background: white; border: 1px solid #e2e8f0;
  padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;
  color: #64748b; font-weight: 500;
}
.page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.page-btn:hover:not(:disabled) { background: #f8fafc; border-color: #cbd5e1; }
.page-info { color: #64748b; font-size: 0.875rem; }
</style>
