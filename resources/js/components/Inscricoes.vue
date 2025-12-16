<template>
  <div class="enrollments-page">
    <div class="page-header">
      <h1>Inscrições Abertas</h1>
      <p>Confira os cursos disponíveis e garanta sua vaga.</p>
    </div>

    <div class="courses-grid">
      <div v-for="course in courses" :key="course.id" class="course-card">
        <div class="course-image">
          <div v-if="!course.image" class="placeholder-img">{{ course.title.charAt(0) }}</div>
          <img v-else :src="`/storage/${course.image}`" class="course-cover" alt="Cover">
        </div>
        <div class="course-content">
          <span :class="['status-tag', { 'status-closed': course.status !== 'active' }]">
            {{ course.status === 'active' ? 'Inscrições Abertas' : 'Inscrições Encerradas' }}
          </span>
          <h3>{{ course.title }}</h3>
          <p>{{ course.description }}</p>
          <div class="course-footer">
            <span class="date">Início: {{ formatDate(course.start_date) }}</span>
            <button 
              class="btn-enroll" 
              :disabled="course.status !== 'active'"
              @click="openEnrollModal(course)"
            >
              Inscrever-se
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Enrollment Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Ficha de Inscrição</h2>
          <button class="close-btn" @click="closeModal">&times;</button>
        </div>
        
        <div class="modal-body">
          <div class="course-summary">
            <span class="label">Curso:</span>
            <span class="value">{{ selectedCourse?.title }}</span>
          </div>

          <form @submit.prevent="submitEnrollment" class="enrollment-form">
            <div class="form-group">
              <label>Nome Completo</label>
              <input type="text" :value="user.name" disabled class="input-disabled" />
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" :value="user.email" disabled class="input-disabled" />
            </div>

            <div class="form-group">
              <label>Telefone</label>
              <input 
                v-model="formData.phone" 
                type="tel" 
                placeholder="(00) 00000-0000" 
                required 
                class="input-field"
              />
            </div>

            <div class="form-group">
              <label>Endereço Completo</label>
              <input 
                v-model="formData.address" 
                type="text" 
                placeholder="Rua, Número, Bairro, Cidade" 
                required 
                class="input-field"
              />
            </div>

            <div class="form-group">
              <label>Motivação</label>
              <textarea 
                v-model="formData.motivation" 
                placeholder="Por que você deseja fazer este curso?" 
                rows="3" 
                required 
                class="input-field textarea"
              ></textarea>
            </div>

            <div class="modal-actions">
              <button type="button" class="btn-cancel" @click="closeModal">Cancelar</button>
              <button type="submit" class="btn-confirm" :disabled="loading">
                {{ loading ? 'Enviando...' : 'Confirmar Inscrição' }}
              </button>
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

const courses = ref([]);
const user = ref({});
const showModal = ref(false);
const selectedCourse = ref(null);
const loading = ref(false);

const formData = reactive({
  phone: '',
  address: '',
  motivation: ''
});

onMounted(async () => {
  await fetchUser();
  await fetchCourses();
});

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user', {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    user.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar usuário', error);
  }
};

const fetchCourses = async () => {
  try {
    const response = await axios.get('/api/courses', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    courses.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar cursos:', error);
  }
};

const openEnrollModal = (course) => {
  selectedCourse.value = course;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedCourse.value = null;
  formData.phone = '';
  formData.address = '';
  formData.motivation = '';
};

const submitEnrollment = async () => {
  if (!selectedCourse.value) return;
  
  loading.value = true;
  try {
    await axios.post('/api/enrollments', {
      course_id: selectedCourse.value.id,
      ...formData
    }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    
    alert('Inscrição realizada com sucesso!');
    closeModal();
  } catch (error) {
    console.error('Erro na inscrição:', error);
    alert('Erro ao realizar inscrição. Verifique os dados.');
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return 'A definir';
  const [year, month, day] = dateString.split('-');
  return new Date(year, month - 1, day).toLocaleDateString('pt-BR');
};
</script>

<style scoped>
.enrollments-page {
  padding: 1rem;
}

.page-header h1 {
  font-size: 1.5rem;
  color: #1e293b;
  margin-bottom: 0.5rem;
}
.page-header p {
  color: #64748b;
  margin-bottom: 2rem;
}

.courses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.course-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s;
  border: 1px solid #f1f5f9;
}

.course-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

.course-image {
  height: 160px;
  background: linear-gradient(135deg, #a5b4fc, #c084fc);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden; /* Ensure image doesn't overflow */
}

.course-cover {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.placeholder-img {
  font-size: 4rem;
  font-weight: 800;
  color: white;
  opacity: 0.5;
}

.course-content {
  padding: 1.5rem;
}

.status-tag {
  background: #dcfce7;
  color: #166534;
  padding: 0.25rem 0.75rem;
  border-radius: 99px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  display: inline-block;
  margin-bottom: 0.75rem;
}

h3 {
  margin: 0 0 0.5rem 0;
  color: #1e293b;
  font-size: 1.125rem;
}

p {
  color: #64748b;
  font-size: 0.875rem;
  line-height: 1.5;
  margin-bottom: 1.5rem;
}

.course-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #f1f5f9;
  padding-top: 1rem;
}

.date {
  font-size: 0.75rem;
  color: #94a3b8;
  font-weight: 500;
}

.btn-enroll {
  background: #6366f1;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-enroll:hover {
  background: #4f46e5;
}

.btn-enroll:disabled {
  background: #94a3b8;
  cursor: not-allowed;
  opacity: 0.7;
}

.status-closed {
  background: #fecaca;
  color: #991b1b;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: white;
  border-radius: 20px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  overflow: hidden;
  animation: modalSlideUp 0.3s ease-out;
}

@keyframes modalSlideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8fafc;
}

.modal-header h2 {
  font-size: 1.25rem;
  color: #1e293b;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 2rem;
  line-height: 1;
  color: #94a3b8;
  cursor: pointer;
}

.modal-body {
  padding: 1.5rem;
}

.course-summary {
  margin-bottom: 1.5rem;
  padding: 0.75rem;
  background: #f1f5f9;
  border-radius: 8px;
  display: flex;
  gap: 0.5rem;
}

.course-summary .label {
  font-weight: 600;
  color: #64748b;
}

.course-summary .value {
  color: #1e293b;
  font-weight: 700;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.5rem;
}

.input-field, .input-disabled {
  width: 100%; /* Ensure full width */
  padding: 0.75rem;
  border-radius: 8px;
  font-size: 0.875rem;
  border: 1px solid #cbd5e1;
  box-sizing: border-box; /* Crucial for padding */
}

.input-disabled {
  background: #f8fafc;
  color: #94a3b8;
}

.input-field:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.textarea {
  resize: vertical;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-cancel {
  background: white;
  border: 1px solid #cbd5e1;
  color: #475569;
  padding: 0.625rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

.btn-confirm {
  background: #6366f1;
  border: none;
  color: white;
  padding: 0.625rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-confirm:hover {
  background: #4f46e5;
}

.btn-confirm:disabled {
  background: #a5b4fc;
  cursor: not-allowed;
}
</style>
