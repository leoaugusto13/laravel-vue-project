<template>
  <div class="admin-audiences">
    <div class="page-header">
      <div class="header-content">
        <h1>Público Alvo</h1>
        <p class="subtitle">Gerencie os públicos alvo disponíveis para os cursos</p>
      </div>
      <button @click="openModal()" class="btn-primary">
        <span class="icon">+</span>
        Novo Público
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Total</h3>
          <p class="value">{{ audiences.length }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Ativos</h3>
          <p class="value">{{ activeAudiences }}</p>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="audience in audiences" :key="audience.id">
            <td class="font-medium">{{ audience.name }}</td>
            <td class="text-muted">{{ audience.description || '-' }}</td>
            <td>
              <span :class="['status-badge', audience.status ? 'active' : 'inactive']">
                {{ audience.status ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td class="actions-cell">
              <button @click="openModal(audience)" class="btn-icon edit" title="Editar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </button>
              <button @click="deleteAudience(audience)" class="btn-icon delete" title="Excluir">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
          </tr>
          <tr v-if="audiences.length === 0">
            <td colspan="4" class="empty-state">
              Nenhum público alvo cadastrado
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Editar Público' : 'Novo Público' }}</h2>
          <button class="close-btn" @click="closeModal">&times;</button>
        </div>
        
        <form @submit.prevent="saveAudience" class="modal-body">
          <div class="form-group">
            <label>Nome do Público *</label>
            <input 
              v-model="form.name" 
              type="text" 
              placeholder="Ex: Servidores Estaduais" 
              required
              class="form-control"
            >
          </div>

          <div class="form-group">
            <label>Descrição</label>
            <textarea 
              v-model="form.description" 
              rows="3" 
              placeholder="Breve descrição sobre a quem se destina..."
              class="form-control"
            ></textarea>
          </div>

          <div class="form-group checkbox-group">
            <label class="switch">
              <input type="checkbox" v-model="form.status">
              <span class="slider round"></span>
            </label>
            <span class="label-text">Ativo</span>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn-secondary" @click="closeModal">Cancelar</button>
            <button type="submit" class="btn-primary" :disabled="loading">
              {{ loading ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Salvar') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const audiences = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const loading = ref(false);
const currentId = ref(null);

const form = ref({
  name: '',
  description: '',
  status: true
});

const activeAudiences = computed(() => audiences.value.filter(a => a.status).length);

const fetchAudiences = async () => {
  try {
    const response = await axios.get('/api/admin/target-audiences', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    audiences.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar públicos:', error);
  }
};

const openModal = (audience = null) => {
  if (audience) {
    isEditing.value = true;
    currentId.value = audience.id;
    form.value = {
      name: audience.name,
      description: audience.description,
      status: Boolean(audience.status)
    };
  } else {
    isEditing.value = false;
    currentId.value = null;
    form.value = {
      name: '',
      description: '',
      status: true
    };
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  form.value = { name: '', description: '', status: true };
};

const saveAudience = async () => {
  loading.value = true;
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem('token')}` };
    if (isEditing.value) {
      await axios.put(`/api/admin/target-audiences/${currentId.value}`, form.value, { headers });
    } else {
      await axios.post('/api/admin/target-audiences', form.value, { headers });
    }
    await fetchAudiences();
    closeModal();
  } catch (error) {
    console.error('Erro ao salvar:', error);
    alert('Erro ao salvar público alvo.');
  } finally {
    loading.value = false;
  }
};

const deleteAudience = async (audience) => {
  if (!confirm(`Deseja excluir "${audience.name}"?`)) return;
  
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem('token')}` };
    await axios.delete(`/api/admin/target-audiences/${audience.id}`, { headers });
    await fetchAudiences();
  } catch (error) {
    console.error('Erro ao excluir:', error);
    alert('Erro ao excluir registro.');
  }
};

onMounted(fetchAudiences);
</script>

<style scoped>
.admin-audiences {
  padding: 1rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header-content h1 {
  font-size: 1.5rem;
  color: #1e293b;
  margin: 0 0 0.5rem 0;
}

.subtitle {
  color: #64748b;
  margin: 0;
}

.btn-primary {
  background: #6366f1;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary:hover {
  background: #4f46e5;
  transform: translateY(-1px);
}

.btn-secondary {
  background: white;
  border: 1px solid #cbd5e1;
  color: #475569;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
}

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.stat-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon svg { width: 1.5rem; height: 1.5rem; }
.stat-icon.blue { background: #eff6ff; color: #3b82f6; }
.stat-icon.green { background: #dcfce7; color: #166534; }

.stat-info h3 { margin: 0; color: #64748b; font-size: 0.875rem; }
.stat-info .value { margin: 0.25rem 0 0 0; font-size: 1.5rem; font-weight: 700; color: #1e293b; }

/* Table */
.table-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  color: #475569;
  font-size: 0.875rem;
}

.data-table td {
  padding: 1rem 1.5rem;
  border-top: 1px solid #f1f5f9;
  color: #1e293b;
}

.font-medium { font-weight: 500; }
.text-muted { color: #64748b; font-size: 0.875rem; }

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 99px;
  font-size: 0.75rem;
  font-weight: 600;
}
.status-badge.active { background: #dcfce7; color: #166534; }
.status-badge.inactive { background: #f1f5f9; color: #475569; }

.actions-cell {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 8px;
  transition: background 0.2s;
  color: #64748b;
}
.btn-icon:hover { background: #f1f5f9; color: #1e293b; }
.btn-icon.delete:hover { background: #fee2e2; color: #ef4444; }
.btn-icon svg { width: 1.25rem; height: 1.25rem; }

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #64748b;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: white;
  border-radius: 20px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-header h2 { margin: 0; font-size: 1.25rem; color: #1e293b; }
.close-btn { background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; }

.modal-body { padding: 1.5rem; }

.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; margin-bottom: 0.5rem; color: #475569; font-weight: 500; font-size: 0.875rem; }
.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: border-color 0.2s;
  box-sizing: border-box; /* Fix padding issue */
}
.form-control:focus { outline: none; border-color: #6366f1; ring: 2px solid #e0e7ff; }

.checkbox-group {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

/* Switch */
.switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 24px;
}
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #cbd5e1;
  transition: .4s;
  border-radius: 24px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider { background-color: #10b981; }
input:checked + .slider:before { transform: translateX(24px); }

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}
</style>
