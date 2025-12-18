<template>
  <div class="courses-page">
    <div class="page-header">
      <div class="header-content">
        <h1>Gestão de Cursos</h1>
        <p>Crie e gerencie os cursos disponíveis na plataforma.</p>
      </div>
      <button @click="openCreateModal" class="btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Novo Curso
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Total</h3>
          <p class="value">{{ courses.length }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="stat-info">
          <h3>Ativos</h3>
          <p class="value">{{ activeCourses }}</p>
        </div>
      </div>
    </div>

    <!-- Courses Table -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Título</th>
            <th>Data de Início</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="course in courses" :key="course.id">
            <td>
              <div class="course-cell">
                <div class="course-icon" v-if="!course.image">{{ course.title.charAt(0) }}</div>
                <img v-else :src="`/storage/${course.image}`" class="course-thumb" alt="Capa">
                <div class="course-info">
                  <span class="title">{{ course.title }}</span>
                  <span class="description">{{ course.description.substring(0, 50) }}...</span>
                </div>
              </div>
            </td>
            <td>{{ formatDate(course.start_date) }}</td>
            <td>
              <span class="status-badge" :class="course.status">
                {{ course.status === 'active' ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td class="actions-cell">
              <button @click="openEditModal(course)" class="icon-btn edit" title="Editar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </button>
              <button @click="deleteCourse(course)" class="icon-btn delete" title="Excluir">
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
          <h3>{{ isEditing ? 'Editar Curso' : 'Novo Curso' }}</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveCourse">
            <div class="form-group">
              <label>Título do Curso</label>
              <input v-model="form.title" type="text" class="form-input" required placeholder="Ex: Curso de Laravel">
            </div>
            
            <div class="form-group">
              <label>Descrição</label>
              <textarea v-model="form.description" class="form-input" rows="3" required placeholder="Resumo do curso..."></textarea>
            </div>

            <div class="form-group">
              <label>Imagem de Capa</label>
              <input type="file" @change="handleFileUpload" class="form-input" accept="image/*">
              <div v-if="previewImage" class="image-preview">
                <img :src="previewImage" alt="Preview">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Data de Início</label>
                <input v-model="form.start_date" type="date" class="form-input" required>
              </div>
              <div class="form-group">
                 <label>Status</label>
                 <select v-model="form.status" class="form-input">
                   <option value="active">Ativo</option>
                   <option value="inactive">Inativo</option>
                 </select>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" @click="closeModal" class="btn-cancel">Cancelar</button>
              <button type="submit" class="btn-save">{{ isEditing ? 'Salvar Alterações' : 'Criar Curso' }}</button>
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

const courses = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const loading = ref(false);

const activeCourses = computed(() => courses.value.filter(c => c.status === 'active').length);
const form = reactive({
  id: null,
  title: '',
  description: '',
  start_date: '',
  status: 'active'
});

onMounted(() => {
  fetchCourses();
});

const fetchCourses = async () => {
  try {
    const response = await axios.get('/api/admin/courses', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    courses.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar cursos:', error);
  }
};

const openCreateModal = () => {
  isEditing.value = false;
  resetForm();
  showModal.value = true;
};

const openEditModal = (course) => {
  isEditing.value = true;
  Object.assign(form, {
    id: course.id,
    title: course.title,
    description: course.description,
    start_date: course.start_date,
    status: course.status
  });
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  form.id = null;
  form.title = '';
  form.description = '';
  form.start_date = '';
  form.status = 'active';
  selectedFile.value = null;
  previewImage.value = null;
};

const selectedFile = ref(null);
const previewImage = ref(null);

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    selectedFile.value = file;
    previewImage.value = URL.createObjectURL(file);
  }
};

const saveCourse = async () => {
  try {
    const headers = { 
      Authorization: `Bearer ${localStorage.getItem('token')}`,
      'Content-Type': 'multipart/form-data'
    };
    
    const formData = new FormData();
    formData.append('title', form.title);
    formData.append('description', form.description);
    formData.append('start_date', form.start_date);
    formData.append('status', form.status);
    
    if (selectedFile.value) {
      formData.append('image', selectedFile.value);
    }

    if (isEditing.value) {
      // Laravel requires _method: PUT for FormData updates via POST
      formData.append('_method', 'PUT');
      await axios.post(`/api/admin/courses/${form.id}`, formData, { headers });
    } else {
      await axios.post('/api/admin/courses', formData, { headers });
    }
    
    await fetchCourses();
    closeModal();
    alert(isEditing.value ? 'Curso atualizado!' : 'Curso criado com sucesso!');
  } catch (error) {
    console.error('Erro ao salvar:', error);
    alert('Erro ao salvar o curso. Verifique os dados.');
  }
};

const deleteCourse = async (course) => {
  if (!confirm(`Tem certeza que deseja excluir o curso "${course.title}"?`)) return;
  
  try {
    await axios.delete(`/api/admin/courses/${course.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    fetchCourses();
  } catch (error) {
    alert('Erro ao excluir curso.');
  }
};

const formatDate = (dateString) => {
  if (!dateString) return 'A definir';
  const [year, month, day] = dateString.split('-');
  return new Date(year, month - 1, day).toLocaleDateString('pt-BR');
};
</script>

<style scoped>
.courses-page { padding: 1rem; }

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

.course-cell { display: flex; align-items: center; gap: 1rem; }
.course-icon {
  width: 2.5rem; height: 2.5rem; background: #e0e7ff; color: #4338ca;
  border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700;
}
.course-info { display: flex; flex-direction: column; }
.course-info .title { font-weight: 600; }
.course-info .description { font-size: 0.75rem; color: #64748b; }

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
textarea.form-input { resize: vertical; }

.modal-footer { padding-top: 1rem; display: flex; justify-content: flex-end; gap: 1rem; }
.btn-cancel { background: white; border: 1px solid #e2e8f0; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 500; color: #64748b; }
.btn-save { background: #6366f1; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; color: white; font-weight: 600; }

/* Icon Utilities */
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }

.course-thumb {
  width: 2.5rem; height: 2.5rem;
  border-radius: 8px; object-fit: cover;
}
.image-preview {
  margin-top: 0.5rem;
}
.image-preview img {
  max-width: 100%; height: 150px; object-fit: cover; border-radius: 8px;
}
</style>
