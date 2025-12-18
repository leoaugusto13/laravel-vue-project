<template>
  <div class="admin-directorates">
    <div class="header-section">
      <h1 class="page-title">Diretorias e Coordenadorias</h1>
      <button @click="openModal()" class="btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Nova Diretoria
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Total</h3>
          <p class="value">{{ directorates.length }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Ativas</h3>
          <p class="value">{{ activeDirectorates }}</p>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Sigla</th>
              <th>Descrição</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="directorate in directorates" :key="directorate.id">
              <td>
                <span class="acronym-badge">{{ directorate.acronym }}</span>
              </td>
              <td>
                <span class="directorate-desc">{{ directorate.description || '-' }}</span>
              </td>
              <td>
                <span class="status-badge" :class="directorate.status">
                  {{ directorate.status === 'active' ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td>
                <div class="actions">
                  <button @click="openModal(directorate)" class="btn-icon edit" title="Editar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </button>
                  <button @click="deleteDirectorate(directorate)" class="btn-icon delete" title="Excluir">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="directorates.length === 0">
              <td colspan="3" class="empty-state">
                Nenhuma diretoria encontrada.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-backdrop" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h3>{{ isEditing ? 'Editar Diretoria' : 'Nova Diretoria' }}</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        <form @submit.prevent="saveDirectorate" class="modal-body">
          <div class="form-row">
             <div class="form-group">
              <label>Sigla</label>
              <input v-model="form.acronym" type="text" required placeholder="Ex: DTIC" maxlength="10" />
            </div>
          </div>
          
          <div class="form-group">
            <label>Descrição</label>
            <textarea v-model="form.description" rows="3" placeholder="Descrição opcional..."></textarea>
          </div>

          <div class="form-group">
            <label>Status</label>
            <div class="status-toggle-wrapper">
              <button 
                type="button" 
                class="status-choice" 
                :class="{ active: form.status === 'active' }"
                @click="form.status = 'active'">
                Ativo
              </button>
              <button 
                type="button" 
                class="status-choice" 
                :class="{ inactive: form.status === 'inactive' }"
                @click="form.status = 'inactive'">
                Inativo
              </button>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn-secondary">Cancelar</button>
            <button type="submit" class="btn-primary" :disabled="loading">
              {{ loading ? 'Salvando...' : 'Salvar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from 'vue';
import axios from 'axios';

const directorates = ref([]);
const activeDirectorates = computed(() => directorates.value.filter(d => d.status === 'active').length);
const showModal = ref(false);
const isEditing = ref(false);
const loading = ref(false);
const currentId = ref(null);

const form = reactive({
  description: '',
  acronym: '',
  status: 'active'
});

const loadDirectorates = async () => {
  try {
    const response = await axios.get('/api/admin/directorates', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    directorates.value = response.data;
  } catch (error) {
    console.error('Error loading directorates:', error);
    alert('Erro ao carregar diretorias');
  }
};

const openModal = (directorate = null) => {
  isEditing.value = !!directorate;
  if (directorate) {
    currentId.value = directorate.id;
    form.description = directorate.description;
    form.acronym = directorate.acronym;
    form.status = directorate.status;
  } else {
    currentId.value = null;
    form.description = '';
    form.acronym = '';
    form.status = 'active';
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveDirectorate = async () => {
  loading.value = true;
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem('token')}` };
    if (isEditing.value) {
      await axios.put(`/api/admin/directorates/${currentId.value}`, form, { headers });
    } else {
      await axios.post('/api/admin/directorates', form, { headers });
    }
    await loadDirectorates();
    closeModal();
  } catch (error) {
    console.error('Error saving directorate:', error);
    alert('Erro ao salvar diretoria: ' + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const deleteDirectorate = async (directorate) => {
  if (!confirm(`Tem certeza que deseja excluir "${directorate.name}"?`)) return;

  try {
    await axios.delete(`/api/admin/directorates/${directorate.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    await loadDirectorates();
  } catch (error) {
    console.error('Error deleting directorate:', error);
    alert('Erro ao excluir diretoria');
  }
};

onMounted(() => {
  loadDirectorates();
});
</script>

<style scoped>
.admin-directorates {
  padding: 1rem;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.875rem;
  font-weight: 800;
  color: #1e293b;
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

.card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  overflow: hidden;
}

.table-container {
  overflow-x: auto;
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
  color: #64748b;
  font-size: 0.875rem;
  border-bottom: 1px solid #e2e8f0;
}

.data-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  color: #334155;
  vertical-align: middle;
}

.directorate-name {
  font-weight: 600;
  color: #0f172a;
}

.directorate-desc {
  font-size: 0.875rem;
  color: #64748b;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-icon svg {
  width: 20px;
  height: 20px;
  display: block;
}

.btn-icon.edit { 
  color: #3b82f6; 
  background: #eff6ff;
}
.btn-icon.edit:hover { 
  background: #dbeafe; 
}

.btn-icon.delete { 
  color: #ef4444; 
  background: #fef2f2;
}
.btn-icon.delete:hover { 
  background: #fee2e2; 
}

/* Buttons */
.btn-primary {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
  transition: all 0.2s;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3);
}

.btn-secondary {
  background: white;
  border: 1px solid #cbd5e1;
  color: #64748b;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: #f8fafc;
  color: #334155;
}

/* Modal */
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
  backdrop-filter: blur(4px);
}

.modal {
  background: white;
  border-radius: 24px;
  width: 100%;
  max-width: 500px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: modalSlide 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalSlide {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #94a3b8;
  cursor: pointer;
  transition: color 0.2s;
}

.close-btn:hover { color: #64748b; }

.modal-body {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #475569;
}

.form-group input,
.form-group textarea {
  padding: 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.95rem;
  transition: all 0.2s;
  color: #1e293b;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.modal-footer {
  padding: 1.5rem;
  background: #f8fafc;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  border-radius: 0 0 24px 24px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 1rem;
}

.acronym-badge {
  font-family: monospace;
  background: #f1f5f9;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-weight: 700;
  color: #475569;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-badge.active {
  background: #eff6ff;
  color: #2563eb;
}

.status-badge.inactive {
  background: #fef2f2;
  color: #dc2626;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #64748b;
}
</style>
