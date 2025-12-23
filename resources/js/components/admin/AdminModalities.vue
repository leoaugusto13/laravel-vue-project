<template>
  <div class="admin-modalities">
    <div class="header-section">
      <h1 class="page-title">Modalidades</h1>
      <button @click="openModal()" class="btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Nova Modalidade
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Total</h3>
          <p class="value">{{ modalities.length }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Ativas</h3>
          <p class="value">{{ activeModalities }}</p>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Descrição</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="modality in modalities" :key="modality.id">
              <td>
                <span class="modality-desc">{{ modality.description }}</span>
              </td>
              <td>
                <span class="status-badge" :class="modality.status">
                  {{ modality.status === 'active' ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td>
                <div class="actions">
                  <button @click="openLocationsModal(modality)" class="btn-icon locations" title="Locais">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                  </button>
                  <button @click="openModal(modality)" class="btn-icon edit" title="Editar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </button>
                  <button @click="deleteModality(modality)" class="btn-icon delete" title="Excluir">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="modalities.length === 0">
              <td colspan="3" class="empty-state">
                Nenhuma modalidade encontrada.
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
          <h3>{{ isEditing ? 'Editar Modalidade' : 'Nova Modalidade' }}</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        <form @submit.prevent="saveModality" class="modal-body">
          
          <div class="form-group">
            <label>Descrição</label>
            <input v-model="form.description" type="text" required placeholder="Ex: Presencial" />
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


    <!-- Locations Modal -->
    <div v-if="showLocationsModal" class="modal-backdrop" @click.self="closeLocationsModal">
      <div class="modal locations-modal">
        <div class="modal-header">
          <h3>Locais de Execução: {{ currentModality?.description }}</h3>
          <button @click="closeLocationsModal" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <!-- Add Location Form -->
          <div class="add-location-form">
            <h4>Adicionar Local</h4>
            <div class="location-inputs">
              <div class="form-group">
                <label>Município</label>
                <select v-model="locationForm.city_id" :disabled="locationForm.regional_id || locationForm.uaitec || locationForm.ead">
                  <option value="">Selecione...</option>
                  <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
                </select>
              </div>
              <div class="form-group">
                <label>Regional</label>
                <select v-model="locationForm.regional_id" :disabled="locationForm.city_id || locationForm.uaitec || locationForm.ead">
                  <option value="">Selecione...</option>
                  <option v-for="reg in regionals" :key="reg.id" :value="reg.id">{{ reg.name }}</option>
                </select>
              </div>
              <div class="checkbox-group">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="locationForm.uaitec" :disabled="locationForm.city_id || locationForm.regional_id || locationForm.ead">
                  UAITEC
                </label>
                <label class="checkbox-label">
                  <input type="checkbox" v-model="locationForm.ead" :disabled="locationForm.city_id || locationForm.regional_id || locationForm.uaitec">
                  EAD
                </label>
              </div>
              <button @click="addLocation" class="btn-primary" :disabled="isAddLocationDisabled">Adicionar</button>
            </div>
          </div>

          <!-- Locations List -->
          <div class="locations-list">
             <h4>Locais Cadastrados</h4>
             <table class="data-table small">
               <thead>
                 <tr>
                   <th>Tipo</th>
                   <th>Nome</th>
                   <th>Ações</th>
                 </tr>
               </thead>
               <tbody>
                 <tr v-for="location in locations" :key="location.id">
                   <td>{{ getLocationType(location) }}</td>
                   <td>{{ getLocationName(location) }}</td>
                   <td>
                     <button @click="deleteLocation(location)" class="btn-icon delete small">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                     </button>
                   </td>
                 </tr>
                 <tr v-if="locations.length === 0">
                   <td colspan="3" class="empty-state small">Nenhum local cadastrado.</td>
                 </tr>
               </tbody>
             </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from 'vue';
import axios from 'axios';

const modalities = ref([]);
const activeModalities = computed(() => modalities.value.filter(m => m.status === 'active').length);
const showModal = ref(false);
const isEditing = ref(false);
const loading = ref(false);
const currentId = ref(null);

const showLocationsModal = ref(false);
const currentModality = ref(null);
const locations = ref([]);
const cities = ref([]);
const regionals = ref([]);
const locationForm = reactive({
  city_id: '',
  regional_id: '',
  uaitec: false,
  ead: false
});

const form = reactive({
  description: '',
  status: 'active'
});

const loadModalities = async () => {
  try {
    const response = await axios.get('/api/admin/modalities', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    // Remove locations_count dependency if backend no longer sends it or frontend doesn't need it
    modalities.value = response.data.map(m => ({
        ...m,
        // status is usually part of the model now
    }));
  } catch (error) {
    console.error('Error loading modalities:', error);
    alert('Erro ao carregar modalidades');
  }
};

const loadDependencies = async () => {
  try {
    const [citiesRes, regionalsRes] = await Promise.all([
        axios.get('/api/admin/cities', { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }}),
        axios.get('/api/admin/regionals', { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }})
    ]);
    cities.value = citiesRes.data;
    regionals.value = regionalsRes.data;
  } catch (error) {
    console.error('Error loading dependencies', error);
  }
};

const loadLocations = async (modalityId) => {
    try {
        const response = await axios.get(`/api/modalities/${modalityId}/locations`, {
             headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        locations.value = response.data;
    } catch(error) {
        console.error('Error loading locations', error);
    }
}

const openModal = (modality = null) => {
  isEditing.value = !!modality;
  if (modality) {
    currentId.value = modality.id;
    form.description = modality.description;
    form.status = modality.status;
  } else {
    currentId.value = null;
    form.description = '';
    form.status = 'active';
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveModality = async () => {
  loading.value = true;
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem('token')}` };
    if (isEditing.value) {
      await axios.put(`/api/admin/modalities/${currentId.value}`, form, { headers });
    } else {
      await axios.post('/api/admin/modalities', form, { headers });
    }
    await loadModalities();
    closeModal();
  } catch (error) {
    console.error('Error saving modality:', error);
    alert('Erro ao salvar modalidade: ' + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const deleteModality = async (modality) => {
  if (!confirm(`Tem certeza que deseja excluir "${modality.description}"?`)) return;

  try {
    await axios.delete(`/api/admin/modalities/${modality.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    await loadModalities();
  } catch (error) {
    console.error('Error deleting modality:', error);
    alert('Erro ao excluir modalidade');
  }
};

onMounted(() => {
  loadModalities();
});

const openLocationsModal = (modality) => {
    currentModality.value = modality;
    locationForm.city_id = '';
    locationForm.regional_id = '';
    locationForm.uaitec = false;
    locationForm.ead = false;
    loadLocations(modality.id);
    showLocationsModal.value = true;
};

const closeLocationsModal = () => {
    showLocationsModal.value = false;
    currentModality.value = null;
    locations.value = [];
};

const isAddLocationDisabled = computed(() => {
    return !locationForm.city_id && !locationForm.regional_id && !locationForm.uaitec && !locationForm.ead;
});

const addLocation = async () => {
    if (isAddLocationDisabled.value) return;
    try {
        await axios.post('/api/locations', {
            modality_id: currentModality.value.id,
           ...locationForm
        }, {
             headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        // Reset form
        locationForm.city_id = '';
        locationForm.regional_id = '';
        locationForm.uaitec = false;
        locationForm.ead = false;
        await loadLocations(currentModality.value.id);
    } catch (error) {
        console.error('Error adding location', error);
        alert('Erro ao adicionar local');
    }
};

const deleteLocation = async (location) => {
    if(!confirm('Remover este local?')) return;
    try {
        await axios.delete(`/api/locations/${location.id}`, {
             headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        await loadLocations(currentModality.value.id);
    } catch (error) {
        console.error('Error deleting location', error);
        alert('Erro ao excluir local');
    }
};

const getLocationType = (location) => {
    if (location.city_id) return 'Município';
    if (location.regional_id) return 'Regional';
    if (location.uaitec) return 'UAITEC';
    if (location.ead) return 'EAD';
    return '-';
};

const getLocationName = (location) => {
    if (location.city) return location.city.name;
    if (location.regional) return location.regional.name;
    if (location.uaitec) return 'UAITEC';
    if (location.ead) return 'Plataforma EAD';
    return '-';
};
</script>

<style scoped>
.admin-modalities {
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

.modality-desc {
  font-weight: 600;
  color: #0f172a;
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

.btn-icon.locations {
    color: #8b5cf6;
    background: #f3f0ff;
}
.btn-icon.locations:hover {
    background: #e9e6ff;
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

.locations-modal {
    max-width: 700px;
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

.form-group input {
  padding: 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.95rem;
  transition: all 0.2s;
  color: #1e293b;
}

.form-group input:focus {
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

.status-toggle-wrapper {
  display: flex;
  background: #f1f5f9;
  padding: 0.25rem;
  border-radius: 12px;
  gap: 0.25rem;
}

.status-choice {
  flex: 1;
  border: none;
  background: transparent;
  padding: 0.6rem;
  border-radius: 8px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.status-choice:hover {
  background: rgba(255,255,255,0.5);
}

.status-choice.active {
  background: #eff6ff;
  color: #2563eb;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.status-choice.inactive {
  background: #fef2f2;
  color: #dc2626;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #64748b;
}

.empty-state.small {
    padding: 1.5rem;
}

.add-location-form {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.add-location-form h4, .locations-list h4 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    color: #475569;
}

.location-inputs {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
    flex-wrap: wrap;
}

.checkbox-group {
    display: flex;
    gap: 1rem;
    padding-bottom: 0.75rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
}

.data-table.small th, .data-table.small td {
    padding: 0.75rem;
    font-size: 0.85rem;
}

</style>
