<template>
  <div class="admin-page">
    <div class="header-section">
      <div class="header-left">
        <h1 class="page-title">Configurar Ficha de Inscrição</h1>
        <p class="subtitle">Defina os campos e regras para a inscrição nesta capacitação.</p>
      </div>
      <div class="header-actions">
           <div class="toggle-group">
              <label for="published" class="toggle-label">Publicado</label>
              <input type="checkbox" id="published" v-model="form.published" class="toggle-input">
           </div>
           
           <button @click="saveForm" :disabled="loading" class="btn-primary">
            <svg v-if="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            <span v-if="loading" class="loader"></span>
            Salvar
          </button>
      </div>
    </div>

    <div class="content-grid">
        <!-- Form Settings Card -->
        <div class="card settings-card">
            <div class="card-header">
                <h3>Informações Básicas</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Título do Formulário</label>
                    <input v-model="form.title" type="text" placeholder="Ex: Inscrição...">
                </div>
                <div class="form-group">
                    <label>Instruções</label>
                    <textarea v-model="form.description" rows="3" placeholder="Instruções para o usuário..."></textarea>
                </div>
            </div>
        </div>

        <!-- Fields Builder -->
        <div class="builder-container">
             <div class="card">
                <div class="card-header flex-between">
                    <h3>Campos do Formulário</h3>
                    <div class="field-actions">
                        <button @click="addField('text')" class="btn-sm"><span class="icon">T</span> Texto</button>
                        <button @click="addField('number')" class="btn-sm"><span class="icon">#</span> Número</button>
                        <button @click="addField('date')" class="btn-sm"><span class="icon">📅</span> Data</button>
                        <button @click="addField('select')" class="btn-sm"><span class="icon">▼</span> Seleção</button>
                        <button @click="addField('radio')" class="btn-sm"><span class="icon">◎</span> Opção</button>
                    </div>
                </div>
                <div class="card-body bg-light">
                    <div v-if="form.fields.length === 0" class="empty-state">
                        <p>Nenhum campo adicionado. Use os botões acima para começar.</p>
                    </div>

                    <draggable 
                        v-model="form.fields" 
                        item-key="temp_id"
                        handle=".drag-handle"
                        class="fields-list"
                        ghost-class="ghost-field"
                    >
                        <template #item="{ element: field, index }">
                            <div class="field-item card">
                                <div class="field-header">
                                    <div class="drag-handle" title="Mover">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                                    </div>
                                    <div class="field-type-badge">{{ getFieldTypeLabel(field.type) }}</div>
                                    <button @click="removeField(index)" class="btn-icon delete" title="Remover">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                
                                <div class="field-body form-grid">
                                    <div class="form-group col-span-2">
                                        <label>Rótulo</label>
                                        <input v-model="field.label" type="text" placeholder="Nome do campo">
                                    </div>
                                    <div class="form-group flex-end">
                                        <label class="checkbox-label">
                                            <input type="checkbox" v-model="field.required"> Obrigatório
                                        </label>
                                    </div>
                                    
                                     <div class="form-group col-span-3" v-if="['text', 'number', 'email', 'phone', 'textarea'].includes(field.type)">
                                        <label>Placeholder</label>
                                        <input v-model="field.placeholder" type="text" placeholder="Texto de exemplo...">
                                    </div>

                                    <div class="form-group col-span-3" v-if="['select', 'radio', 'checkbox'].includes(field.type)">
                                        <label>Opções (uma por linha)</label>
                                        <textarea 
                                            :value="field.options ? (Array.isArray(field.options) ? field.options.join('\n') : field.options) : ''" 
                                            @input="updateOptions(field, $event.target.value)"
                                            class="options-input"
                                            rows="3"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </draggable>
                </div>
             </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import draggable from 'vuedraggable';
import axios from 'axios';
import { useToast } from 'vue-toastification'; 

const props = defineProps({
  trainingId: {
    type: [Number, String],
    required: true
  }
});

const toast = useToast();
const loading = ref(false);

const form = ref({
  title: '',
  description: '',
  published: false,
  fields: []
});

const getFieldTypeLabel = (type) => {
    const types = {
        'text': 'Texto Curto',
        'textarea': 'Texto Longo',
        'number': 'Número',
        'date': 'Data',
        'select': 'Seleção',
        'radio': 'Opção Única',
        'checkbox': 'Checkbox',
        'email': 'E-mail',
        'phone': 'Telefone'
    };
    return types[type] || type;
};

const addField = (type) => {
  form.value.fields.push({
    temp_id: Date.now() + Math.random(), // Unique key for drag/drop
    type: type,
    label: '',
    placeholder: '',
    required: false,
    options: ['select', 'radio', 'checkbox'].includes(type) ? [] : null,
    order: form.value.fields.length
  });
};

const removeField = (index) => {
  if (confirm('Tem certeza que deseja remover este campo?')) {
    form.value.fields.splice(index, 1);
  }
};

const updateOptions = (field, value) => {
  field.options = value.split(/\n/).map(opt => opt.trim()).filter(opt => opt !== '');
};

const fetchForm = async () => {
  try {
    loading.value = true;
    const response = await axios.get(`/api/admin/trainings/${props.trainingId}/registration-form`);
    if (response.data.form) {
      form.value = {
        title: response.data.form.title,
        description: response.data.form.description,
        published: !!response.data.form.published,
        fields: response.data.form.fields ? response.data.form.fields.map(f => ({
          ...f,
          temp_id: f.id || (Date.now() + Math.random())
        })) : []
      };
    } else {
        form.value.title = 'Ficha de Inscrição';
    }
  } catch (error) {
    console.error('Erro ao carregar formulário', error);
    toast.error('Erro ao carregar dados do formulário.');
  } finally {
    loading.value = false;
  }
};

const saveForm = async () => {
  if (!form.value.title) {
    toast.warning('O título do formulário é obrigatório.');
    return;
  }

  try {
    loading.value = true;
    const payload = {
        title: form.value.title,
        description: form.value.description,
        published: form.value.published,
        fields: form.value.fields.map((f, index) => ({
            id: f.id, 
            type: f.type,
            label: f.label,
            placeholder: f.placeholder,
            required: f.required,
            options: f.options,
            order: index 
        }))
    };

    const response = await axios.post(`/api/admin/trainings/${props.trainingId}/registration-form`, payload);
    
    toast.success('Formulário salvo com sucesso!');
    if (response.data.form) {
        form.value.fields = response.data.form.fields.map(f => ({
          ...f,
          temp_id: f.id
        }));
    }
  } catch (error) {
    console.error('Erro ao salvar formulário', error);
    toast.error('Erro ao salvar o formulário.');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchForm();
});
</script>

<style scoped>
.admin-page {
  padding: 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.875rem;
  font-weight: 800;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.subtitle {
    color: #64748b;
    font-size: 0.95rem;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.toggle-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.toggle-label {
    font-weight: 600;
    color: #475569;
}

.toggle-input {
    width: 1.2rem;
    height: 1.2rem;
}

/* Card Styles */
.card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  overflow: hidden;
  margin-bottom: 1.5rem;
}

.card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    background: #fff;
}

.card-header h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #334155;
    margin: 0;
}

.card-header.flex-between {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-body {
    padding: 1.5rem;
}

.card-body.bg-light {
    background: #f8fafc;
}

/* Form Styles from AdminTrainings */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

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
.form-group textarea:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Primary Button from AdminTrainings */
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

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Small Buttons */
.btn-sm {
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid #e2e8f0;
    background: white;
    color: #475569;
    cursor: pointer;
    margin-left: 0.5rem;
    transition: all 0.1s;
}

.btn-sm:hover {
    background: #f1f5f9;
    color: #1e293b;
    border-color: #cbd5e1;
}

/* Field Actions */
.btn-icon {
  padding: 0.6rem;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-icon:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.btn-icon.delete { color: #ef4444; background: #fef2f2; border-color: #fecaca; }
.btn-icon.delete:hover { background: #fee2e2; }

/* Drag Handle */
.drag-handle {
    cursor: move;
    color: #94a3b8;
    margin-right: 1rem;
    padding: 0.5rem;
}

.drag-handle:hover {
    color: #64748b;
    background: #f1f5f9;
    border-radius: 8px;
}

/* Layout Specifics */
.field-header {
    display: flex;
    align-items: center;
    background: #f8fafc;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.field-type-badge {
    background: #e2e8f0;
    color: #475569;
    padding: 0.2rem 0.6rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-right: auto;
}

.field-body {
    padding: 1.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.col-span-2 { grid-column: span 2; }
.col-span-3 { grid-column: span 3; }
.flex-end { align-self: end; }

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #64748b;
    font-style: italic;
}

.ghost-field {
    opacity: 0.5;
    background: #f1f5f9;
    border: 2px dashed #cbd5e1;
}
</style>
