<template>
  <div class="admin-trainings">
    <div class="header-section">
      <h1 class="page-title">Capacitações</h1>
      <button @click="openModal()" class="btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Nova Capacitação
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Total</h3>
          <p class="value">{{ trainings.length }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Ativas</h3>
          <p class="value">{{ activeTrainings }}</p>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nome da Ação</th>
              <th>Diretoria</th>
              <th>Local</th>
              <th>Modalidade</th>
              <th>Data</th>
              <th>Carga Hor.</th>
              <th>Ano</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="training in trainings" :key="training.id">
              <td>
                <div class="training-info">
                  <span class="training-title">{{ training.action_name }}</span>
                </div>
              </td>
              <td>
                <span class="directorate-badge">{{ training.directorate?.acronym || '-' }}</span>
              </td>
              <td>
                <span class="location-badge">{{ training.location?.name || '-' }}</span>
              </td>
              <td>
                <span class="modality-badge">{{ training.modality?.description || '-' }}</span>
              </td>
              <td>
                <span class="date-badge">{{ formatDate(training.start_date) }}</span>
              </td>
              <td>
                <span class="workload-badge">{{ training.workload || '-' }}</span>
              </td>
              <td>
                <span class="year-badge">{{ training.year }}</span>
              </td>
              <td>
                <span class="status-badge" :class="training.status">
                  {{ training.status === 'active' ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td>
                <div class="actions">
                  <button @click="openModal(training)" class="btn-icon edit" title="Editar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </button>
                  <button @click="deleteTraining(training)" class="btn-icon delete" title="Excluir">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="trainings.length === 0">
              <td colspan="4" class="empty-state">
                Nenhuma capacitação encontrada.
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
          <h3>{{ isEditing ? 'Editar Capacitação' : 'Nova Capacitação' }}</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        <form @submit.prevent="saveTraining" class="modal-body">
          <!-- Section 1: Basic Info -->
          <div class="form-section full-width">
            <h4 class="section-title">Informações Básicas</h4>
            <div class="form-grid">
              <div class="form-group full-width">
                <label>Nome da Ação</label>
                <input v-model="form.action_name" type="text" required placeholder="Ex: Capacitação 2025" />
              </div>

              <div class="form-group full-width">
                <label>Objetivo</label>
                <textarea v-model="form.objective" maxlength="500" rows="2" placeholder="Descreva o objetivo da capacitação..."></textarea>
              </div>
            </div>
          </div>

          <!-- Section 2: Classification & Details -->
          <div class="form-section">
            <h4 class="section-title">Classificação & Detalhes</h4>
            <div class="form-grid">
              <div class="form-group">
                <label>Diretoria / Coordenadoria</label>
                <select v-model="form.directorate_id" required>
                  <option value="" disabled>Selecione...</option>
                  <option v-for="dir in directorates" :key="dir.id" :value="dir.id">
                    {{ dir.acronym }}
                  </option>
                </select>
              </div>

              <div class="form-group">
                <label>Treinamento</label>
                <select v-model="form.training_type_id">
                  <option value="">Selecione...</option>
                  <option v-for="type in trainingTypes" :key="type.id" :value="type.id">
                    {{ type.name }}
                  </option>
                </select>
              </div>

              <div class="form-group">
                <label>Público Alvo</label>
                <select v-model="form.target_audience_id">
                  <option value="">Selecione...</option>
                  <option v-for="target in targetAudiences" :key="target.id" :value="target.id">
                    {{ target.name }}
                  </option>
                </select>
              </div>

              <div class="form-group">
                <label>Status</label>
                <select v-model="form.status">
                  <option value="active">Ativo</option>
                  <option value="inactive">Inativo</option>
                </select>
              </div>

               <div class="form-group">
                <label>Ano</label>
                <select v-model="form.year" required>
                  <option value="" disabled>Selecione...</option>
                  <option v-for="y in availableYears" :key="y.id" :value="y.year">
                    {{ y.year }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Section 3: Location & Schedule -->
          <div class="form-section">
             <h4 class="section-title">Localização & Agenda</h4>
             <div class="form-grid">
                <div class="form-group">
                  <label>Localização</label>
                  <select v-model="form.location_id">
                    <option value="">Selecione...</option>
                    <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                      {{ loc.name }}
                    </option>
                  </select>
                </div>

                <div class="form-group" v-if="showVenueField">
                   <label>Localidade (Local específico)</label>
                   <input v-model="form.venue" type="text" placeholder="Ex: Auditório da Escola X..." />
                </div>

                <div class="form-group">
                  <label>Modalidade</label>
                  <select v-model="form.modality_id" :disabled="!form.location_id">
                    <option value="">Selecione...</option>
                    <option v-for="mod in availableModalities" :key="mod.id" :value="mod.id">
                      {{ mod.description }}
                    </option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Data de Início</label>
                  <input v-model="form.start_date" type="date" required>
                </div>

                <div class="form-group">
                  <label>Carga Horária</label>
                  <input v-model="form.workload" type="text" placeholder="Ex: 40h">
                </div>
             </div>
          </div>

          <!-- Section 4: Strategies -->
          <div class="form-section full-width">
            <h4 class="section-title">Estratégias</h4>
            <div class="checkbox-group grid-cols-3">
              <label v-for="strategy in strategies" :key="strategy.id" class="checkbox-item">
                <input type="checkbox" :value="strategy.id" v-model="form.strategies" />
                <span class="checkbox-label">{{ strategy.description }}</span>
              </label>
            </div>
          </div>






          <!-- Section: Regionals Filter -->
          <div class="form-section full-width" v-if="isRegionalsLocation">
            <h4 class="section-title">Regionais</h4>
            <div class="checkbox-group grid-cols-3">
              <label v-for="regional in regionals" :key="regional.id" class="checkbox-item">
                <input type="checkbox" :value="regional.id" v-model="form.regionals" />
                <span class="checkbox-label">{{ regional.name }}</span>
              </label>
            </div>
             <div v-if="regionals.length === 0" class="empty-text">Nenhuma regional cadastrada.</div>
          </div>

          <!-- Section: States Filter -->
          <div class="form-section full-width" v-if="isMunicipalitiesLocation">
            <h4 class="section-title">Estados (Filtrar Municípios)</h4>
             <div class="checkbox-group states-list">
              <label v-for="state in states" :key="state.id" class="checkbox-item">
                <input type="checkbox" :value="state.id" v-model="selectedStates" @change="handleStateChange" />
                <span class="checkbox-label">{{ state.uf }}</span>
              </label>
            </div>
          </div>

          <!-- Section 5: Municipalities -->
          <div class="form-section full-width" v-if="isMunicipalitiesLocation">
            <div class="section-header-actions">
                <h4 class="section-title">Municípios Abrangidos</h4>
                <div class="section-actions">
                    <button type="button" class="btn-text" @click="selectAllCities">Marcar Todos</button>
                    <button type="button" class="btn-text" @click="deselectAllCities">Desmarcar Todos</button>
                </div>
            </div>
            
            <div class="city-search">
                <input type="text" v-model="citySearch" placeholder="Buscar município..." class="search-input">
            </div>

            <div class="checkbox-group grid-cols-3 cities-list">
              <label v-for="city in filteredCities" :key="city.id" class="checkbox-item">
                <input type="checkbox" :value="city.id" v-model="form.cities" />
                <span class="checkbox-label">{{ city.name }}</span>
              </label>
            </div>
             <div v-if="cities.length === 0" class="empty-text">Nenhum município carregado.</div>
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
import { ref, onMounted, computed, reactive, watch } from 'vue';
import axios from 'axios';

const trainings = ref([]);
const activeTrainings = computed(() => trainings.value.filter(t => t.status === 'active').length);
const directorates = ref([]);
const locations = ref([]);
const years = ref([]);
const states = ref([]);
const regionals = ref([]);
const trainingTypes = ref([]);
const targetAudiences = ref([]);
const strategies = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const loading = ref(false);
const currentId = ref(null);

const form = reactive({
  action_name: '',
  year: new Date().getFullYear(),
  status: 'active',
  directorate_id: '',
  location_id: '',
  venue: '',
  modality_id: '',
  training_type_id: '',
  target_audience_id: '',
  start_date: '',
  workload: '',
  objective: '',
  strategies: [],

  cities: [],
  regionals: []
});

const availableModalities = computed(() => {
    if (!form.location_id) return [];
    const location = locations.value.find(l => l.id === form.location_id);
    return location ? location.modalities.filter(m => m.status === 'active' || (form.modality_id && m.id === form.modality_id)) : [];
});



const cities = ref([]);
const selectedStates = ref([]);
const citySearch = ref('');
const filteredCities = computed(() => {
    if (!citySearch.value) return cities.value;
    const search = citySearch.value.toLowerCase();
    return cities.value.filter(c => c.name.toLowerCase().includes(search));
});

const showVenueField = computed(() => {
    return !!form.location_id;
});

const isMunicipalitiesLocation = computed(() => {
    if (!form.location_id) return false;
    // Ensure loosely typed comparison for IDs (string vs number)
    const location = locations.value.find(l => l.id == form.location_id);
    
    if (location) {
        // Normalize string to remove accents and check for "municip" base
        const normalizedName = location.name.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        return normalizedName.includes('municip');
    }
    return false;
});

const isRegionalsLocation = computed(() => {
    if (!form.location_id) return false;
    const location = locations.value.find(l => l.id == form.location_id);
    if (location) {
        const normalizedName = location.name.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        return normalizedName.includes('regionais') || normalizedName.includes('regional');
    }
    return false;
});

// Watch for location changes to clear cities if needed
watch(() => form.location_id, (newVal) => {
    if (!isMunicipalitiesLocation.value) {
        selectedStates.value = [];
        form.cities = [];
        cities.value = [];
    }
    if (!isRegionalsLocation.value) {
        form.regionals = [];
    }
});

const availableYears = computed(() => {
    // If editing, include the current year even if closed (to allow saving other changes)
    if (isEditing.value) {
        return years.value.filter(y => !y.is_closed || y.year == form.year);
    }
    // If creating, only show open years
    return years.value.filter(y => !y.is_closed);
});


const loadStates = async () => {
    try {
        const response = await axios.get('/api/admin/states', {
             headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        states.value = response.data;
    } catch (error) {
        console.error('Error loading states:', error);
    }
};

const loadRegionals = async () => {
    try {
        const response = await axios.get('/api/admin/regionals', {
             headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        regionals.value = response.data;
    } catch (error) {
        console.error('Error loading regionals:', error);
    }
};

const loadCities = async (stateIds = []) => {
  try {
    const params = {};
    if (stateIds.length > 0) {
        params.state_id = stateIds;
    } else {
        cities.value = []; // Don't load cities if no state is selected to improve performance
        return;
    }

    const response = await axios.get('/api/admin/cities', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
      params: params
    });
    cities.value = response.data;
  } catch (error) {
    console.error('Error loading cities:', error);
  }
};

const handleStateChange = () => {
    loadCities(selectedStates.value);
};

const selectAllCities = () => {
    const idsToAdd = filteredCities.value.map(c => c.id);
    const newCities = new Set([...form.cities, ...idsToAdd]);
    form.cities = Array.from(newCities);
};

const deselectAllCities = () => {
   const idsToRemove = new Set(filteredCities.value.map(c => c.id));
   form.cities = form.cities.filter(id => !idsToRemove.has(id));
};

const loadDirectorates = async () => {
  try {
    const response = await axios.get('/api/admin/directorates', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    directorates.value = response.data;
  } catch (error) {
    console.error('Error loading directorates:', error);
  }
};

const loadYears = async () => {
  try {
    const response = await axios.get('/api/admin/years', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    years.value = response.data;
  } catch (error) {
    console.error('Error loading years:', error);
  }
};

const loadLocations = async () => {
  try {
    const response = await axios.get('/api/admin/locations', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    locations.value = response.data;


  } catch (error) {
    console.error('Error loading locations:', error);
  }
};

const loadTrainingTypes = async () => {
  try {
    const response = await axios.get('/api/admin/training-types', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    trainingTypes.value = response.data;
  } catch (error) {
    console.error('Error loading training types:', error);
  }
};

const loadTargetAudiences = async () => {
  try {
    const response = await axios.get('/api/admin/target-audiences', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    targetAudiences.value = response.data;
  } catch (error) {
    console.error('Error loading target audiences:', error);
  }
};

const loadStrategies = async () => {
  try {
    const response = await axios.get('/api/admin/strategies', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    strategies.value = response.data;
  } catch (error) {
    console.error('Error loading strategies:', error);
  }
};

const loadTrainings = async () => {
  try {
    const response = await axios.get('/api/admin/trainings', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    trainings.value = response.data;
  } catch (error) {
    console.error('Error loading trainings:', error);
    alert('Erro ao carregar capacitações');
  }
};

const openModal = (training = null) => {
  isEditing.value = !!training;
  if (training) {
    currentId.value = training.id;
    form.action_name = training.action_name;
    form.year = training.year;

    form.status = training.status;
    form.directorate_id = training.directorate_id || '';
    form.location_id = training.location_id || '';
    form.venue = training.venue || '';
    form.modality_id = training.modality_id || '';
    form.training_type_id = training.training_type_id || '';
    form.target_audience_id = training.target_audience_id || '';
    form.start_date = training.start_date || '';
    form.workload = training.workload || '';
    form.objective = training.objective || '';
    form.strategies = training.strategies ? training.strategies.map(s => s.id) : [];
    form.cities = training.cities ? training.cities.map(c => c.id) : [];
    form.regionals = training.regionals ? training.regionals.map(r => r.id) : [];

    // Extract unique state IDs from the training's cities to pre-select states
    if (training.cities && training.cities.length > 0) {
        const stateIds = [...new Set(training.cities.map(c => c.state_id))];
        selectedStates.value = stateIds;
        loadCities(stateIds);
    } else {
        selectedStates.value = [];
        cities.value = [];
    }
  } else {
    currentId.value = null;
    form.action_name = '';
    form.year = new Date().getFullYear();
    form.status = 'active';
    form.directorate_id = '';
    form.location_id = '';
    form.venue = '';
    form.modality_id = '';
    form.training_type_id = '';
    form.target_audience_id = '';
    form.start_date = '';
    form.workload = '';
    form.objective = '';

    form.strategies = [];
    form.cities = [];
    form.regionals = [];
    selectedStates.value = [];
    cities.value = [];
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveTraining = async () => {
  loading.value = true;
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem('token')}` };
    if (isEditing.value) {
      await axios.put(`/api/admin/trainings/${currentId.value}`, form, { headers });
    } else {
      await axios.post('/api/admin/trainings', form, { headers });
    }
    await loadTrainings();
    closeModal();
  } catch (error) {
    console.error('Error saving training:', error);
    alert('Erro ao salvar capacitação: ' + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const deleteTraining = async (training) => {
  if (!confirm(`Tem certeza que deseja excluir "${training.action_name}"?`)) return;

  try {
    await axios.delete(`/api/admin/trainings/${training.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    await loadTrainings();
  } catch (error) {
    console.error('Error deleting training:', error);
    alert('Erro ao excluir capacitação');
  }
};

onMounted(() => {
  loadTrainings();
  loadDirectorates();
  loadLocations();
  loadYears();
  loadTrainingTypes();
  loadTargetAudiences();

  loadStrategies();
  loadStates();
  loadRegionals();
  // loadCities(); // Initial load removed, waiting for state selection
});

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const [year, month, day] = dateString.split('-');
  return new Date(year, month - 1, day).toLocaleDateString('pt-BR');
};
</script>

<style scoped>
.admin-trainings {
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

.training-info {
  display: flex;
  flex-direction: column;
}

.training-title {
  font-weight: 600;
  color: #0f172a;
}

.training-desc {
  font-size: 0.875rem;
  color: #64748b;
}

.directorate-badge {
  font-size: 0.875rem;
  color: #475569;
  font-weight: 500;
}

.year-badge {
  background: #eff6ff;
  color: #3b82f6;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-badge.active {
  background: #dcfce7;
  color: #166534;
}

.status-badge.inactive {
  background: #f1f5f9;
  color: #64748b;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  padding: 0.5rem;
  border-radius: 8px;
  border: none;
  background: transparent;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-icon.edit { color: #3b82f6; }
.btn-icon.edit:hover { background: #eff6ff; }

.btn-icon.delete { color: #ef4444; }
.btn-icon.delete:hover { background: #fef2f2; }

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
  width: 95%;
  max-width: 900px;
  max-height: 90vh;
  margin: 1rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: modalSlide 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  display: flex;
  flex-direction: column;
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
  font-size: 1.5rem;
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
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* Remove specific full-width hack as now everything is stacked */
.form-section.full-width {
  grid-column: auto;
}

.section-title {
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #94a3b8;
  font-weight: 700;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 0.5rem;
  margin-bottom: 0.5rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr); /* 3 columns for better density */
  gap: 1.5rem;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-height: 150px;
    overflow-y: auto;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
}

.checkbox-group.grid-cols-3 {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 0.75rem;
    max-height: none; /* Let it expand or control height if needed */
}

.checkbox-group.states-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 0.5rem;
    max-height: 150px;
    overflow-y: auto;
}

/* Rest of form styles */

.form-group label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #475569;
}

.form-group input,
.form-group textarea,
.form-group select {
  padding: 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.95rem;
  transition: all 0.2s;
  color: #1e293b;
  width: 100%;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.section-header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 0.5rem;
    margin-bottom: 0.5rem;
}

.section-header-actions .section-title {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.section-actions {
    display: flex;
    gap: 1rem;
}

.btn-text {
    background: none;
    border: none;
    color: #6366f1;
    font-size: 0.75rem;
    cursor: pointer;
    font-weight: 600;
}

.btn-text:hover {
    text-decoration: underline;
}

.city-search {
    margin-bottom: 1rem;
}

.cities-list {
    max-height: 200px;
}



.modal-footer {
  padding: 1.5rem;
  background: #f8fafc;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  border-top: 1px solid #f1f5f9;
  grid-column: 1 / -1; /* Make footer span full width */
  margin-top: 1rem;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #64748b;
}

@media (max-width: 768px) {
  .modal-body {
    grid-template-columns: 1fr;
  }
  .form-grid {
    grid-template-columns: 1fr;
  }
}

</style>
