<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>Bem-vindo de volta</h1>
        <p>Faça login na sua conta</p>
      </div>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="email">Endereço de Email</label>
          <input 
            type="email" 
            id="email" 
            v-model="form.email" 
            placeholder="nome@exemplo.com"
            required
          >
        </div>

        <div class="form-group">
          <label for="password">Senha</label>
          <input 
            type="password" 
            id="password" 
            v-model="form.password" 
            placeholder="••••••••"
            required
          >
        </div>

        <div class="form-actions">
          <div class="remember-me">
            <input type="checkbox" id="remember" v-model="form.remember">
            <label for="remember">Lembrar-me</label>
          </div>
          <router-link to="/forgot-password" class="forgot-password">Esqueceu a senha?</router-link>
        </div>

        <button type="submit" class="submit-btn">
          Entrar
        </button>
      </form>
      
      <div class="login-footer">
        <p>Não tem uma conta? <router-link to="/register">Cadastre-se</router-link></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const form = reactive({
  email: '',
  password: '',
  remember: false
});

const handleLogin = async () => {
  try {
    const response = await axios.post('/api/login', {
      email: form.email,
      password: form.password
    });

    // Save token and user data
    localStorage.setItem('token', response.data.access_token);
    localStorage.setItem('user', JSON.stringify(response.data.user));

    // Redirect based on role
    if (response.data.user.role === 'admin') {
      router.push('/admin/dashboard');
    } else {
      router.push('/portal/inscricoes');
    }

  } catch (error) {
    console.error('Erro no login:', error);
    alert('Erro ao entrar: ' + (error.response?.data?.message || 'Verifique suas credenciais'));
  }
};
</script>

<style scoped>
/* Modern Reset & Base */
* {
  box-sizing: border-box;
}

.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  padding: 20px;
}

.login-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  width: 100%;
  max-width: 440px;
  padding: 2.5rem;
  border-radius: 20px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-header h1 {
  color: #1a202c;
  font-size: 1.875rem;
  font-weight: 700;
  margin: 0 0 0.5rem 0;
}

.login-header p {
  color: #718096;
  font-size: 0.95rem;
  margin: 0;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  color: #4a5568;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.form-group input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.2s;
  outline: none;
}

.form-group input:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  font-size: 0.875rem;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #4a5568;
}

.forgot-password {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.submit-btn {
  width: 100%;
  padding: 0.875rem;
  background: linear-gradient(to right, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.1s, box-shadow 0.2s;
}

.submit-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.submit-btn:active {
  transform: translateY(0);
}

.login-footer {
  margin-top: 1.5rem;
  text-align: center;
  font-size: 0.875rem;
  color: #718096;
}

.login-footer a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}
</style>
