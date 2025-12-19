<template>
  <div class="dashboard-layout">
    <!-- Mobile Header -->
    <div class="mobile-header">
      <button @click="toggleMobileSidebar" class="menu-toggle">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
      </button>
      <div class="logo-mobile">SISEP</div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" :class="{ 'active': isMobileSidebarOpen }" @click="toggleMobileSidebar"></div>

    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'open': isMobileSidebarOpen }">
      <div class="sidebar-container">
        <!-- Top Section: Logo & Nav -->
        <div class="sidebar-top">
          <div class="sidebar-header-top">
            <div class="logo">
              <div class="logo-icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
              </div>
              <span class="logo-text">SISEP</span>
            </div>
          </div>
          
          <nav class="nav-links">
            <template v-for="item in displayedMenuItems" :key="item.label">
              
              <!-- Standard Menu Item -->
              <router-link 
                v-if="!item.children"
                :to="item.path" 
                class="nav-item" 
                active-class="active" 
                @click="handleNavClick"
              >
                <div class="icon-wrapper">
                   <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="item.icon"></svg>
                </div>
                <span class="link-text">{{ item.label }}</span>
              </router-link>

              <!-- Parent Menu Item (Dropdown) -->
              <div v-else class="nav-group" :class="{ 'expanded': expandedMenus[item.label] }">
                <button 
                  class="nav-item group-toggle" 
                  @click="toggleMenu(item.label)"
                  :class="{ 'active': isChildActive(item) }"
                >
                  <div class="icon-wrapper">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="item.icon"></svg>
                  </div>
                  <span class="link-text">{{ item.label }}</span>
                  <svg 
                    class="chevron" 
                    :class="{ 'rotate': expandedMenus[item.label] }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>
                
                <div class="submenu" :class="{ 'open': expandedMenus[item.label] }" :style="{ maxHeight: expandedMenus[item.label] ? '500px' : '0px' }">
                  <router-link 
                    v-for="child in item.children" 
                    :key="child.path"
                    :to="child.path" 
                    class="nav-item sub-item" 
                    active-class="active" 
                    @click="handleNavClick"
                  >
                    <span class="dot"></span>
                    <span class="link-text">{{ child.label }}</span>
                  </router-link>
                </div>
              </div>

            </template>
          </nav>
        </div>

        <!-- Bottom Section: Profile Only -->
        <div class="sidebar-bottom">
          <div class="user-profile" v-if="user">
            <div class="avatar">{{ user.name.charAt(0).toUpperCase() }}</div>
            <div class="user-info">
              <p class="name">{{ user.name }}</p>
              <p class="role">Admin</p>
            </div>
          </div>
        </div>
      </div>
    </aside>


    <!-- Main Content -->
    <main class="main-content">
      <header class="top-header">
        <div class="search-bar">
          <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input type="text" placeholder="Pesquisar...">
        </div>
        <button @click="handleLogout" class="logout-btn">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
          <span class="desktop-only text-label">Sair</span>
        </button>
      </header>

      <div class="content-area">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const user = ref(null);
const isMobileSidebarOpen = ref(false);

const expandedMenus = reactive({});

const adminMenuItems = [
  { 
    label: 'Início', 
    path: '/admin/dashboard', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>' 
  },
  {
    label: 'Cadastros',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>',
    children: [
        { label: 'Capacitações', path: '/admin/trainings' },
        { label: 'Cursos', path: '/admin/courses' },
        { label: 'Diretorias', path: '/admin/directorates' },
        { label: 'Estados', path: '/admin/states' },
        { label: 'Estratégias', path: '/admin/strategies' },
        { label: 'Modalidades', path: '/admin/modalities' },
        { label: 'Municípios', path: '/admin/cities' },
        { label: 'Público Alvo', path: '/admin/target-audiences' },
        { label: 'Regionais', path: '/admin/regionals' },
        { label: 'Tipologias', path: '/admin/training-types' },
        { label: 'Usuários', path: '/admin/users' },
    ]
  },
  { 
    label: 'Inscrições', 
    path: '/admin/inscricoes', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>' 
  },
  { 
    label: 'Relatórios', 
    path: '/admin/reports', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path>' 
  },
  { 
    label: 'Ações', 
    path: '/admin/logs', 
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>' 
  },
];

const displayedMenuItems = computed(() => {
  if (user.value?.role !== 'admin') {
    return [{
      label: 'Inscrições',
      path: '/portal/inscricoes',
      icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>'
    }];
  }
  return adminMenuItems;
});

const toggleMenu = (label) => {
  expandedMenus[label] = !expandedMenus[label];
};

const isChildActive = (item) => {
  if (!item.children) return false;
  return item.children.some(child => route.path.startsWith(child.path));
};

onMounted(() => {
  const userData = localStorage.getItem('user');
  if (userData) {
    user.value = JSON.parse(userData);
  } else {
    router.push('/login');
  }

  // Auto-expand "Cadastros" if a child is active
  adminMenuItems.forEach(item => {
    if (item.children && isChildActive(item)) {
       expandedMenus[item.label] = true;
    }
  });
});

const toggleMobileSidebar = () => {
  isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
};

// Auto-hide mobile sidebar when clicking a link
const handleNavClick = () => {
  // Mobile: Close completely
  if (window.innerWidth < 768) {
    isMobileSidebarOpen.value = false;
  } 
};

const handleLogout = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  router.push('/login');
};
</script>

<style scoped>
/* Main Layout */
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f8fafc;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  overflow-x: hidden;
}

/* Mobile Header */
.mobile-header {
  display: none;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  background: white;
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 40;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.menu-toggle {
  background: none;
  border: none;
  cursor: pointer;
  color: #64748b;
  padding: 0.5rem;
  border-radius: 8px;
  transition: background 0.2s;
}
.menu-toggle:hover {
  background: #f1f5f9;
}

/* Sidebar Wrapper */
/* Sidebar Wrapper */
.sidebar {
  width: 320px;
  background: white;
  border-right: 1px solid #f1f5f9;
  position: fixed;
  height: 100vh;
  z-index: 50;
  transition: width 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
  overflow-y: auto;
  overflow-x: hidden;
  box-shadow: 2px 0 10px rgba(0,0,0,0.02);
  
  /* Hide scrollbar */
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE/Edge */
}
.sidebar::-webkit-scrollbar {
  display: none; /* Chrome/Safari */
}

.sidebar-container {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 100%;
  padding: 1.5rem;
  width: 320px; /* Fixed width container to prevent squash during collapse transition */
}


.sidebar-container {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 100%;
  padding: 1.5rem;
  width: 280px; /* Fixed width container to prevent squash during collapse transition */
}

/* Sidebar Sections */
.sidebar-top {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.sidebar-header-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 2rem;
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #1e293b;
  font-weight: 800;
  font-size: 1.5rem;
  height: 3rem;
  white-space: nowrap;
}

.logo-icon {
  width: 2.5rem;
  height: 2.5rem;
  background: linear-gradient(135deg, #6366f1, #a855f7);
  color: white;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Force SVG size inside logo-icon if tailwind fails */
.logo-icon svg {
  width: 1.5rem;
  height: 1.5rem;
}

.header-toggle {
  background: transparent;
  border: none;
  cursor: pointer;
  color: #94a3b8;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}
.header-toggle:hover {
  background: #f1f5f9;
  color: #475569;
}
/* Force SVG size for toggle */
.header-toggle svg {
  width: 1.25rem;
  height: 1.25rem;
}

.nav-links {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.875rem 1rem;
  color: #64748b;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 500;
  transition: all 0.2s ease;
  white-space: nowrap;
  /* Reset button styles */
  background: transparent;
  border: none;
  font-family: inherit;
  font-size: inherit;
  cursor: pointer;
  text-align: left;
}

.nav-item:hover {
  background-color: #f1f5f9;
  color: #334155;
  transform: translateX(4px);
}

.nav-item.active {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}

.nav-item .icon {
  width: 1.5rem;
  height: 1.5rem;
  min-width: 1.5rem;
}

/* Sidebar Bottom */
.sidebar-bottom {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  border-top: 1px solid #f1f5f9;
  padding-top: 1.5rem;
  margin-top: auto;
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  border-radius: 12px;
  transition: background 0.2s;
  white-space: nowrap;
}

.user-profile:hover {
  background: #f8fafc;
}

.avatar {
  width: 2.5rem;
  height: 2.5rem;
  min-width: 2.5rem;
  background: #e2e8f0;
  color: #475569;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
}

.user-info .name {
  font-weight: 600;
  color: #1e293b;
  font-size: 0.875rem;
  margin: 0;
}
.user-info .role {
  font-size: 0.75rem;
  color: #94a3b8;
  margin: 0;
}

.sidebar-toggle {
  display: flex;
  align-items: center;
  gap: 1rem;
  width: 100%;
  padding: 0.75rem 1rem;
  border: none;
  background: transparent;
  color: #94a3b8;
  cursor: pointer;
  border-radius: 12px;
  font-weight: 500;
  transition: all 0.2s;
  white-space: nowrap;
}

.sidebar-toggle:hover {
  background: #f1f5f9;
  color: #475569;
}



/* Main Content */
.main-content {
  flex: 1;
  margin-left: 320px;
  padding: 2.5rem;
  transition: margin-left 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
}



/* Header */
.top-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
}

.search-bar {
  display: flex;
  align-items: center;
  background: white;
  padding: 0.75rem 1.25rem;
  border-radius: 16px;
  border: 1px solid #f1f5f9;
  width: 100%;
  max-width: 400px;
  gap: 0.75rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  transition: all 0.2s;
}
.search-bar:focus-within {
  border-color: #6366f1;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
}

.search-icon { width: 1.25rem; color: #94a3b8; }
.search-bar input { border: none; outline: none; width: 100%; color: #334155; font-size: 0.95rem; }

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: white;
  border: 1px solid #f1f5f9;
  color: #ef4444;
  padding: 0.75rem 1.25rem;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.logout-btn:hover {
  background: #fef2f2;
  border-color: #fecaca;
  transform: translateY(-1px);
}

/* Responsive Media Queries */
@media (max-width: 768px) {
  .mobile-header { display: flex; }
  
  .sidebar { transform: translateX(-100%); width: 320px; }
  .sidebar.open { transform: translateX(0); }
  

  
  .sidebar-overlay.active { display: block; }

  .main-content {
    margin-left: 0;
    padding: 1.5rem;
    padding-top: 5rem;
  }


  .desktop-only { display: none !important; }
}
/* Nested Menu Styles */
.nav-group {
  display: flex;
  flex-direction: column;
}

.group-toggle {
  width: 100%;
  justify-content: flex-start; /* Aligns items to the start */
}

.icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
}

.chevron {
  width: 1rem;
  height: 1rem;
  transition: transform 0.2s;
  color: #94a3b8;
  margin-left: auto; /* Pushes chevron to the far right */
}

.chevron.rotate {
  transform: rotate(180deg);
}

.submenu {
  overflow: hidden;
  transition: max-height 0.3s ease-out;
  background: #f8fafc;
  margin: 0 0.5rem;
  border-radius: 8px;
}

.sub-item {
  padding-left: 3.5rem;
  padding-right: 1rem;
  font-size: 0.9em;
  color: #64748b;
  display: flex;
  align-items: center;
}

.sub-item:first-child {
  margin-top: 0.25rem;
}
.sub-item:last-child {
  margin-bottom: 0.25rem;
}

.sub-item .dot {
  width: 5px;
  height: 5px;
  background-color: #cbd5e1;
  border-radius: 50%;
  margin-right: 1rem;
  transition: background-color 0.2s;
}

.sub-item.active {
  color: #334155;
  font-weight: 600;
  background-color: transparent;
}

.sub-item.active .dot {
  background-color: #334155;
  transform: scale(1.2);
}

</style>
