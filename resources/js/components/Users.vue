<template>
  <div class="users-page">
    <div class="page-header">
      <h1>Gerenciar Usuários</h1>
      <p>Visualize e gerencie todos os usuários cadastrados.</p>
    </div>

    <!-- Users Table -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Perfil</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>
              <div class="user-cell">
                <div class="avatar-small">{{ user.name.charAt(0).toUpperCase() }}</div>
                <span>{{ user.name }}</span>
              </div>
            </td>
            <td>{{ user.email }}</td>
            <td>
              <span class="role-badge" :class="user.role === 'admin' ? 'admin' : 'user'">
                {{ user.role === 'admin' ? 'Administrador' : 'Usuário' }}
              </span>
            </td>
            <td>
              <span class="status-badge" :class="user.is_approved ? 'approved' : 'pending'">
                {{ user.is_approved ? 'Aprovado' : 'Pendente' }}
              </span>
            </td>
            <td class="actions-cell">
              <button @click="openEditModal(user)" class="icon-btn edit" title="Editar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </button>
              
              <button @click="toggleApproval(user)" class="icon-btn toggle" :title="user.is_approved ? 'Desaprovar' : 'Aprovar'">
                 <svg v-if="user.is_approved" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                 <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Edit Modal -->
    <div v-if="editingUser" class="modal-overlay" @click.self="closeEditModal">
      <div class="modal">
        <div class="modal-header">
          <h3>Editar Usuário</h3>
          <button @click="closeEditModal" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nome</label>
            <input v-model="editingUser.name" type="text" class="form-input">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input v-model="editingUser.email" type="email" class="form-input">
          </div>
          <div class="form-group">
            <label>Perfil</label>
            <select v-model="editingUser.role" class="form-input">
              <option value="user">Usuário Comum</option>
              <option value="admin">Administrador</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="closeEditModal" class="btn-cancel">Cancelar</button>
          <button @click="saveUser" class="btn-save">Salvar Alterações</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const users = ref([]);
const editingUser = ref(null);

onMounted(() => {
  fetchUsers();
});

const fetchUsers = async () => {
  try {
    const response = await axios.get('/api/users', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    users.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar usuários:', error);
  }
};

const toggleApproval = async (user) => {
  try {
    const response = await axios.post(`/api/users/${user.id}/toggle-approval`, {}, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    // Update local state without refresh
    user.is_approved = response.data.user.is_approved;
  } catch (error) {
    alert('Erro ao alterar status do usuário');
  }
};

const openEditModal = (user) => {
  // Clone object to avoid reactive edits in table before save
  editingUser.value = { ...user };
};

const closeEditModal = () => {
  editingUser.value = null;
};

const saveUser = async () => {
  try {
    await axios.put(`/api/users/${editingUser.value.id}`, editingUser.value, {
       headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    
    // Refresh list or update local
    const index = users.value.findIndex(u => u.id === editingUser.value.id);
    if (index !== -1) {
      users.value[index] = editingUser.value;
    }
    
    closeEditModal();
  } catch (error) {
    alert('Erro ao salvar usuário. Verifique se o email já existe.');
  }
};
</script>

<style scoped>
.users-page {
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

.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  text-align: left;
  padding: 1rem 1.5rem;
  background: #f8fafc;
  color: #64748b;
  font-weight: 600;
  font-size: 0.875rem;
  border-bottom: 1px solid #e2e8f0;
}

td {
  padding: 1rem 1.5rem;
  color: #334155;
  border-bottom: 1px solid #f1f5f9;
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.avatar-small {
  width: 2rem; height: 2rem;
  background: #e2e8f0;
  color: #475569;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 0.875rem;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 99px;
  font-size: 0.75rem;
  font-weight: 600;
}
.status-badge.approved { background: #dcfce7; color: #166534; }
.status-badge.pending { background: #fee2e2; color: #991b1b; }

.actions-cell {
  display: flex;
  gap: 0.5rem;
}

.icon-btn {
  background: none; border: none; cursor: pointer;
  padding: 0.5rem; border-radius: 6px; color: #64748b;
  transition: all 0.2s;
}
.icon-btn:hover { background: #f1f5f9; color: #334155; }

/* Modal */
.modal-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex; align-items: center; justify-content: center;
  z-index: 100;
}

.modal {
  background: white;
  border-radius: 16px;
  width: 100%; max-width: 500px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  padding: 1.5rem; border-bottom: 1px solid #f1f5f9;
  display: flex; justify-content: space-between; align-items: center;
}
.modal-header h3 { margin: 0; font-size: 1.25rem; }

.close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8; }

.modal-body { padding: 1.5rem; }

.form-group { margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.5rem; color: #475569; font-size: 0.875rem; font-weight: 500; }
.form-input {
  width: 100%; padding: 0.75rem;
  border: 1px solid #e2e8f0; border-radius: 8px;
  outline: none;
}
.form-input:focus { border-color: #6366f1; }

.modal-footer {
  padding: 1.5rem; background: #f8fafc;
  border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;
  display: flex; justify-content: flex-end; gap: 1rem;
}

.btn-cancel {
  background: white; border: 1px solid #e2e8f0;
  padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer;
  font-weight: 500; color: #64748b;
}
.btn-save {
  background: #6366f1; border: none;
  padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer;
  color: white; font-weight: 600;
}

.role-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.role-badge.admin { background: #e0e7ff; color: #4338ca; } /* Indigo */
.role-badge.user { background: #f3f4f6; color: #4b5563; } /* Gray */

/* Utilities for Icons */
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }
.text-green-500 { color: #22c55e; }
.text-gray-400 { color: #9ca3af; }
</style>
