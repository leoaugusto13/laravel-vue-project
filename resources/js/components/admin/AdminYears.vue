<template>
  <div class="admin-years">
    <div class="header">
      <h1 class="page-title">Gerenciar Anos</h1>
      <button @click="openModal()" class="btn-primary">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Novo Ano
      </button>
    </div>

    <!-- Stats Cards (Optional, maybe specific to Years later) -->
    
    <!-- Table -->
    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Ano</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="year in years" :key="year.id">
            <td class="font-medium">{{ year.year }}</td>
            <td>
              <span :class="['status-badge', year.is_closed ? 'status-inactive' : 'status-active']">
                {{ year.is_closed ? 'Encerrado' : 'Aberto' }}
              </span>
            </td>
            <td>
              <div class="actions">
                <button 
                  @click="toggleStatus(year)" 
                  class="btn-icon"
                  :class="year.is_closed ? 'status-reopen' : 'status-close'" 
                  :title="year.is_closed ? 'Reabrir Ano' : 'Encerrar Ano'"
                >
                  <svg v-if="year.is_closed" class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                  <svg v-else class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </button>
                <button @click="deleteYear(year)" class="btn-icon delete" title="Excluir">
                    <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="years.length === 0">
            <td colspan="3" class="text-center py-8 text-gray-500">
              Nenhum ano cadastrado.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title">Novo Ano</h2>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        
        <form @submit.prevent="saveYear" class="modal-form">
          <div class="form-group">
            <label>Ano</label>
            <input 
              v-model="form.year" 
              type="number" 
              min="2000" 
              max="2100" 
              required 
              class="form-input" 
              placeholder="Ex: 2024"
            >
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn-secondary">Cancelar</button>
            <button type="submit" class="btn-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';

const years = ref([]);
const showModal = ref(false);
const form = reactive({
  year: new Date().getFullYear(),
});

const fetchYears = async () => {
  try {
    const response = await axios.get('/api/admin/years', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    years.value = response.data;
  } catch (error) {
    console.error('Error fetching years:', error);
    alert('Erro ao carregar anos.');
  }
};

const openModal = () => {
  form.year = new Date().getFullYear();
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveYear = async () => {
  try {
    const token = localStorage.getItem('token');
    await axios.post('/api/admin/years', {
        year: form.year,
        is_closed: false 
    }, {
      headers: { Authorization: `Bearer ${token}` }
    });
    
    await fetchYears();
    closeModal();
    alert('Ano cadastrado com sucesso!');
  } catch (error) {
    console.error('Error creating year:', error);
    if (error.response?.status === 422) {
       alert(Object.values(error.response.data.errors).flat().join('\n'));
    } else {
       alert('Erro ao salvar ano.');
    }
  }
};

const toggleStatus = async (year) => {
    const newStatus = !year.is_closed;
    const action = newStatus ? 'encerrar' : 'reabrir';
    
    if (!confirm(`Tem certeza que deseja ${action} o ano ${year.year}?`)) return;

    try {
        const token = localStorage.getItem('token');
        await axios.put(`/api/admin/years/${year.id}`, {
            is_closed: newStatus
        }, {
             headers: { Authorization: `Bearer ${token}` }
        });
        await fetchYears();
    } catch (error) {
        console.error('Error updating status:', error);
        alert('Erro ao atualizar status.');
    }
};

const deleteYear = async (year) => {
    if (!confirm(`Tem certeza que deseja excluir o ano ${year.year}?`)) return;
    
    try {
        const token = localStorage.getItem('token');
        await axios.delete(`/api/admin/years/${year.id}`, {
             headers: { Authorization: `Bearer ${token}` }
        });
        await fetchYears();
    } catch (error) {
         console.error('Error deleting year:', error);
         alert('Erro ao excluir ano.');
    }
}

onMounted(() => {
  fetchYears();
});
</script>

<style scoped>
.admin-years {
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.875rem;
  font-weight: 800;
  color: #1e293b;
  letter-spacing: -0.025em;
}

/* Button Styles */
.btn-primary {
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.2s;
  box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
  border: none;
  cursor: pointer;
}
.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 15px -3px rgba(99, 102, 241, 0.3);
}

.btn-secondary {
  background: white;
  color: #64748b;
  border: 1px solid #e2e8f0;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-secondary:hover {
  background: #f8fafc;
  color: #334155;
  border-color: #cbd5e1;
}

/* Table Styles */
.table-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  border: 1px solid #f1f5f9;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  background: #f8fafc;
  padding: 1rem 1.5rem;
  text-align: left;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #64748b;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #e2e8f0;
}

.data-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  color: #334155;
}

.data-table tr:last-child td {
  border-bottom: none;
}

.data-table tr:hover td {
  background: #f8fafc;
}

/* Status Badges */
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-active {
  background: #dcfce7;
  color: #166534;
}

.status-inactive {
  background: #fee2e2;
  color: #991b1b;
}

/* Actions */
.actions {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.2s;
  border: none;
  background: transparent;
  cursor: pointer;
}
.btn-icon:hover {
  background: #f1f5f9;
}
.btn-icon.delete {
    color: #ef4444;
}
.btn-icon.delete:hover {
    color: #b91c1c;
    background: #fef2f2;
}

.status-reopen {
  color: #166534; /* green-800 */
}
.status-reopen:hover {
  background-color: #dcfce7; /* green-100 */
}

.status-close {
  color: #991b1b; /* red-800 */
}
.status-close:hover {
  background-color: #fee2e2; /* red-100 */
}

.icon-svg {
  width: 1.25rem; /* 20px */
  height: 1.25rem;
}


/* Modal */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
  padding: 1rem;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: white;
  border-radius: 20px;
  width: 100%;
  max-width: 500px;
  padding: 2rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  animation: modalSlide 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalSlide {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 2rem;
  color: #94a3b8;
  cursor: pointer;
  line-height: 1;
  padding: 0;
  transition: color 0.2s;
}
.close-btn:hover { color: #475569; }

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: #475569;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 1rem;
  color: #1e293b;
  transition: all 0.2s;
  outline: none;
}
.form-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}
</style>
