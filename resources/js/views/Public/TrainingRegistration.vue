<template>
  <div class="max-w-3xl mx-auto py-10 px-4">
    <div v-if="loading" class="text-center py-20">
       <span class="animate-spin text-4xl text-blue-600 inline-block animate-spin">⟳</span>
       <p class="mt-4 text-gray-500">Carregando formulário...</p>
    </div>

    <div v-else-if="error" class="text-center py-20 text-red-600">
      <p class="text-xl font-bold">Erro ao carregar o formulário.</p>
      <p>{{ error }}</p>
    </div>

    <div v-else class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-blue-600 px-8 py-6 text-white">
            <h1 class="text-2xl font-bold">{{ trainingTitle }}</h1>
            <h2 class="text-xl mt-2 opacity-90">{{ form.title }}</h2>
        </div>
        
        <div class="p-8">
            <p v-if="form.description" class="text-gray-600 mb-8 whitespace-pre-line">{{ form.description }}</p>

            <form @submit.prevent="submitRegistration">
                
                <!-- Guest Fields (if not logged in) -->
                <div v-if="!isLoggedIn" class="bg-blue-50 p-6 rounded-lg mb-8 border border-blue-100">
                    <h3 class="text-lg font-bold text-blue-800 mb-4">Seus Dados</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nome Completo <span class="text-red-500">*</span></label>
                            <input v-model="guestData.guest_name" type="text" class="w-full border rounded p-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">E-mail <span class="text-red-500">*</span></label>
                            <input v-model="guestData.guest_email" type="email" class="w-full border rounded p-2" required>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Fields -->
                <div class="space-y-6">
                    <div v-for="field in form.fields" :key="field.id">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            {{ field.label }} 
                            <span v-if="field.required" class="text-red-500">*</span>
                        </label>
                        
                        <!-- Text / Email / Phone / Number -->
                        <div v-if="['text', 'email', 'phone', 'number', 'date'].includes(field.type)">
                            <input 
                                v-model="answers[field.id]" 
                                :type="field.type" 
                                :placeholder="field.placeholder" 
                                :required="field.required"
                                class="w-full border rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none transition"
                            >
                        </div>

                        <!-- TextArea -->
                        <div v-if="field.type === 'textarea'">
                            <textarea 
                                v-model="answers[field.id]" 
                                :placeholder="field.placeholder" 
                                :required="field.required"
                                rows="3"
                                class="w-full border rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none transition"
                            ></textarea>
                        </div>

                        <!-- Select -->
                        <div v-if="field.type === 'select'">
                            <select 
                                v-model="answers[field.id]" 
                                :required="field.required"
                                class="w-full border rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 outline-none transition"
                            >
                                <option value="" disabled selected>Select an option</option>
                                <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                            </select>
                        </div>

                        <!-- Radio -->
                        <div v-if="field.type === 'radio'" class="space-y-2">
                             <label v-for="opt in field.options" :key="opt" class="flex items-center cursor-pointer">
                                <input 
                                    type="radio" 
                                    :name="`field_${field.id}`" 
                                    :value="opt" 
                                    v-model="answers[field.id]" 
                                    :required="field.required"
                                    class="mr-2 text-blue-600 focus:ring-blue-500"
                                >
                                <span>{{ opt }}</span>
                             </label>
                        </div>

                         <!-- Checkbox -->
                        <div v-if="field.type === 'checkbox'" class="space-y-2">
                             <label v-for="opt in field.options" :key="opt" class="flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    :value="opt" 
                                    v-model="answers[field.id]" 
                                    class="mr-2 text-blue-600 focus:ring-blue-500"
                                >
                                <span>{{ opt }}</span>
                             </label>
                             <!-- Note: Checkbox implementation here assumes multiple selection array, might need initializing answers[field.id] = [] -->
                        </div>

                    </div>
                </div>

                <div class="mt-10">
                    <button type="submit" :disabled="submitting" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition shadow-lg flex justify-center items-center">
                         <span v-if="submitting" class="animate-spin mr-2 border-2 border-white border-t-transparent rounded-full w-5 h-5"></span>
                         Confirmar Inscrição
                    </button>
                </div>
            </form>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const route = useRoute();
const toast = useToast();

const loading = ref(true);
const submitting = ref(false);
const error = ref(null);
const trainingTitle = ref('');
const form = ref({});
const answers = reactive({});
const guestData = reactive({
    guest_name: '',
    guest_email: ''
});

// Mock login check - replace with actual auth logic or store
const isLoggedIn = ref(false); // Check your auth store here

const fetchForm = async () => {
    try {
        loading.value = true;
        const response = await axios.get(`/api/public/trainings/${route.params.id}/registration-form`);
        trainingTitle.value = response.data.training_title;
        form.value = response.data.form;

        // Initialize checkbox arrays
        form.value.fields.forEach(field => {
            if (field.type === 'checkbox') {
                answers[field.id] = [];
            }
        });
    } catch (err) {
        console.error(err);
        error.value = 'Formulário não encontrado ou não publicado.';
    } finally {
        loading.value = false;
    }
};

const submitRegistration = async () => {
    try {
        submitting.value = true;
        const payload = {
            answers: answers,
            ...guestData
        };
        const response = await axios.post(`/api/public/trainings/${route.params.id}/register`, payload);
        toast.success('Inscrição realizada com sucesso!');
        
        // Reset or redirect
        // router.push({ name: 'SuccessPage' });
        // For now just clear
        Object.keys(answers).forEach(key => delete answers[key]);
         guestData.guest_name = '';
         guestData.guest_email = '';
    } catch (err) {
        console.error(err);
        if (err.response && err.response.data && err.response.data.errors) {
            // Show validation errors
             const msg = Object.values(err.response.data.errors).flat().join('\n');
             toast.error('Erro na validação:\n' + msg);
        } else {
             toast.error('Ocorreu um erro ao enviar sua inscrição.');
        }
    } finally {
        submitting.value = false;
    }
};

onMounted(() => {
    // Check auth status
    // const user = useAuthStore().user; ...
    // isLoggedIn.value = !!user;
    fetchForm();
});
</script>
