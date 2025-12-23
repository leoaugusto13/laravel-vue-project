<template>
  <div class="locations-page">
    <div class="page-header">
      <div class="header-content">
        <h1>Gestão de Localizações</h1>
        <p>Gerencie os locais e suas modalidades disponíveis.</p>
      </div>
      <button @click="openCreateModal" class="btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Nova Localização
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Total</h3>
          <p class="value">{{ locations.length }}</p>
        </div>
      </div>
    </div>

    <!-- Locations Table -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Nome do Local</th>
            <th>Modalidades</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="location in locations" :key="location.id">
            <td>
              <div class="location-info">
                <span class="name">{{ location.name }}</span>
              </div>
            </td>
            <td>
              <div class="tags">
                <span v-for="mod in location.modalities" :key="mod.id" class="tag">
                  {{ mod.description }}
                </span>
                <span v-if="!location.modalities?.length" class="text-muted">-</span>
              </div>
            </td>
            <td class="actions-cell">
               <button @click="openEditModal(location)" class="icon-btn edit" title="Editar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </button>
              <button @click="deleteLocation(location)" class="icon-btn delete" title="Excluir">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
          </tr>
           <tr v-if="locations.length === 0">
              <td colspan="3" class="empty-state">Nenhuma localização encontrada.</td>
            </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h3>{{ isEditing ? 'Editar Localização' : 'Nova Localização' }}</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveLocation">
            
            <div class="form-group">
                <label>Nome do Local</label>
                <input type="text" v-model="form.name" class="form-input" placeholder="Ex: Auditório Principal..." required>
            </div>

            <div class="form-group">
              <label>Modalidades Permitidas</label>
              <div class="checkbox-grid">
                <label v-for="modality in allModalities" :key="modality.id" class="checkbox-item">
                  <input type="checkbox" :value="modality.id" v-model="form.modalities">
                  {{ modality.description }}
                </label>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" @click="closeModal" class="btn-cancel">Cancelar</button>
              <button type="submit" class="btn-save" :disabled="!form.name">Salvar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';

const locations = ref([]);
const allModalities = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);

const form = reactive({
  name: '',
  modalities: []
});

onMounted(() => {
  fetchLocations();
  fetchModalities();
});

const fetchLocations = async () => {
  try {
    const response = await axios.get('/api/admin/locations', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    locations.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar localizações:', error);
  }
};

const fetchModalities = async () => {
    try {
        const response = await axios.get('/api/admin/modalities', {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        allModalities.value = response.data.filter(m => m.status === 'active');
    } catch (error) {
        console.error('Erro ao buscar modalidades:', error);
    }
};

const openCreateModal = () => {
  resetForm();
  isEditing.value = false;
  currentId.value = null;
  showModal.value = true;
};

const openEditModal = (location) => {
    form.name = location.name;
    form.modalities = location.modalities ? location.modalities.map(m => m.id) : [];
    currentId.value = location.id;
    isEditing.value = true;
    showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  form.name = '';
  form.modalities = [];
};

const saveLocation = async () => {
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem('token')}` };
    if (isEditing.value) {
        await axios.put(`/api/admin/locations/${currentId.value}`, form, { headers });
    } else {
        await axios.post('/api/admin/locations', form, { headers });
    }
    
    await fetchLocations();
    closeModal();
    alert('Localização salva com sucesso!');
  } catch (error) {
    console.error('Erro ao salvar:', error);
    alert('Erro ao salvar localização.');
  }
};

const deleteLocation = async (location) => {
  if (!confirm(`Tem certeza que deseja remover "${location.name}"?`)) return;
  
  try {
    await axios.delete(`/api/admin/locations/${location.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    fetchLocations();
  } catch (error) {
    alert('Erro ao excluir localização.');
  }
};
</script>

<style scoped>
.locations-page { padding: 1rem; }

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

.location-info .name { font-weight: 600; }

.tags { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.tag { background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 500; }
.text-muted { color: #94a3b8; font-size: 0.875rem; }

.actions-cell { display: flex; gap: 0.5rem; }
.icon-btn { background: none; border: none; cursor: pointer; padding: 0.5rem; border-radius: 6px; color: #64748b; transition: all 0.2s; }
.icon-btn:hover { background: #f1f5f9; color: #334155; }
.icon-btn.edit:hover { background: #eff6ff; color: #3b82f6; }
.icon-btn.delete:hover { background: #fee2e2; color: #ef4444; }

.empty-state { text-align: center; padding: 2rem; color: #94a3b8; }

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
.form-group label { display: block; margin-bottom: 0.5rem; color: #475569; font-size: 0.875rem; font-weight: 500; }
.form-input { width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; font-family: inherit; }
.form-input:focus { border-color: #6366f1; }

.checkbox-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }
.checkbox-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; cursor: pointer; }

.modal-footer { padding-top: 1rem; display: flex; justify-content: flex-end; gap: 1rem; }
.btn-cancel { background: white; border: 1px solid #e2e8f0; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 500; color: #64748b; }
.btn-save { background: #6366f1; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; color: white; font-weight: 600; }
.btn-save:disabled { background: #94a3b8; cursor: not-allowed; }

/* Icon Utilities */
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }
</style>
