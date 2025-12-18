<template>
  <div class="states-page">
    <div class="page-header">
      <div class="header-content">
        <h1>Gestão de Estados</h1>
        <p>Gerencie os estados disponíveis no sistema.</p>
      </div>
      <button @click="openCreateModal" class="btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Novo Estado
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Total</h3>
          <p class="value">{{ states.length }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Ativos</h3>
          <p class="value">{{ activeStates }}</p>
        </div>
      </div>
    </div>

    <!-- States Table -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>UF</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="state in states" :key="state.id">
            <td>
              <div class="state-info">
                <span class="name">{{ state.name }}</span>
              </div>
            </td>
            <td>{{ state.uf }}</td>
            <td>
              <span class="status-badge" :class="state.active ? 'active' : 'inactive'">
                {{ state.active ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td class="actions-cell">
              <button @click="openEditModal(state)" class="icon-btn edit" title="Editar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </button>
              <button @click="deleteState(state)" class="icon-btn delete" title="Excluir">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h3>{{ isEditing ? 'Editar Estado' : 'Novo Estado' }}</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveState">
            <div class="form-group">
              <label>Nome do Estado</label>
              <input v-model="form.name" type="text" class="form-input" required placeholder="Ex: São Paulo">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                  <label>Sigla (UF)</label>
                  <input v-model="form.uf" type="text" class="form-input" required placeholder="Ex: SP" maxlength="2" style="text-transform: uppercase;">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select v-model="form.active" class="form-input">
                        <option :value="true">Ativo</option>
                        <option :value="false">Inativo</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" @click="closeModal" class="btn-cancel">Cancelar</button>
              <button type="submit" class="btn-save">{{ isEditing ? 'Salvar Alterações' : 'Criar Estado' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from 'vue';
import axios from 'axios';

const states = ref([]);
const showModal = ref(false);
const isEditing = ref(false);

const activeStates = computed(() => states.value.filter(s => s.active).length);

const form = reactive({
  id: null,
  name: '',
  uf: '',
  active: true
});

onMounted(() => {
  fetchStates();
});

const fetchStates = async () => {
  try {
    const response = await axios.get('/api/admin/states', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    states.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar estados:', error);
  }
};

const openCreateModal = () => {
  isEditing.value = false;
  resetForm();
  showModal.value = true;
};

const openEditModal = (state) => {
  isEditing.value = true;
  Object.assign(form, {
    id: state.id,
    name: state.name,
    uf: state.uf,
    active: !!state.active
  });
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  form.id = null;
  form.name = '';
  form.uf = '';
  form.active = true;
};

const saveState = async () => {
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem('token')}` };
    const payload = {
        name: form.name,
        uf: form.uf.toUpperCase(),
        active: form.active
    };

    if (isEditing.value) {
      await axios.put(`/api/admin/states/${form.id}`, payload, { headers });
    } else {
      await axios.post('/api/admin/states', payload, { headers });
    }
    
    await fetchStates();
    closeModal();
    alert(isEditing.value ? 'Estado atualizado!' : 'Estado criado com sucesso!');
  } catch (error) {
    console.error('Erro ao salvar:', error);
    alert('Erro ao salvar o estado. Verifique os dados (UF deve ser único).');
  }
};

const deleteState = async (state) => {
  if (!confirm(`Tem certeza que deseja excluir o estado "${state.name}"?`)) return;
  
  try {
    await axios.delete(`/api/admin/states/${state.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    fetchStates();
  } catch (error) {
    alert('Erro ao excluir estado. Verifique se existem cidades vinculadas.');
  }
};
</script>

<style scoped>
.states-page { padding: 1rem; }

.page-header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 2rem;
}
.page-header h1 { font-size: 1.5rem; color: #1e293b; margin: 0 0 0.5rem 0; }
.page-header p { color: #64748b; margin: 0; }

.btn-primary {
  display: flex; align-items: center; gap: 0.5rem;
  background: #6366f1; color: white; border: none;
  padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer;
  transition: background 0.2s;
}
.btn-primary:hover { background: #4f46e5; }

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
  background: white; border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow-x: auto;
}

table { width: 100%; border-collapse: collapse; }
th { text-align: left; padding: 1rem 1.5rem; background: #f8fafc; color: #64748b; font-size: 0.875rem; border-bottom: 1px solid #e2e8f0; }
td { padding: 1rem 1.5rem; color: #334155; border-bottom: 1px solid #f1f5f9; }

.state-info .name { font-weight: 600; }

.status-badge { padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600; }
.status-badge.active { background: #dcfce7; color: #166534; }
.status-badge.inactive { background: #f3f4f6; color: #4b5563; }

.actions-cell { display: flex; gap: 0.5rem; }
.icon-btn { background: none; border: none; cursor: pointer; padding: 0.5rem; border-radius: 6px; color: #64748b; transition: all 0.2s; }
.icon-btn:hover { background: #f1f5f9; color: #334155; }
.icon-btn.delete:hover { background: #fee2e2; color: #ef4444; }

/* Modal */
.modal-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100;
}
.modal { background: white; border-radius: 16px; width: 100%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
.modal-header { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { margin: 0; font-size: 1.25rem; }
.close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8; }
.modal-body { padding: 1.5rem; }

.form-group { margin-bottom: 1rem; }
.form-row { display: flex; gap: 1rem; }
.form-row .form-group { flex: 1; }
.form-group label { display: block; margin-bottom: 0.5rem; color: #475569; font-size: 0.875rem; font-weight: 500; }
.form-input { width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; font-family: inherit; }
.form-input:focus { border-color: #6366f1; }

.modal-footer { padding-top: 1rem; display: flex; justify-content: flex-end; gap: 1rem; }
.btn-cancel { background: white; border: 1px solid #e2e8f0; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 500; color: #64748b; }
.btn-save { background: #6366f1; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; color: white; font-weight: 600; }

/* Icon Utilities */
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }
</style>
