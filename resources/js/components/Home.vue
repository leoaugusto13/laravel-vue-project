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
    <aside class="sidebar" :class="{ 'open': isMobileSidebarOpen, 'collapsed': isDesktopCollapsed }">
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
            
            <!-- Toggle Button (Moved Here) -->
            <button @click="toggleDesktopSidebar" class="header-toggle desktop-only" :title="isDesktopCollapsed ? 'Expandir' : 'Recolher'">
               <svg v-if="!isDesktopCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
               <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
          </div>
          
          <nav class="nav-links">
            <router-link v-if="user?.role === 'admin'" to="/admin/dashboard" class="nav-item" active-class="active" @click="handleNavClick">
              <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
              <span class="link-text">Início</span>
            </router-link>
            
            <router-link v-if="user?.role === 'admin'" to="/admin/users" class="nav-item" active-class="active" @click="handleNavClick">
              <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
              <span class="link-text">Usuários</span>
            </router-link>

            <router-link v-if="user?.role === 'admin'" to="/admin/courses" class="nav-item" active-class="active" @click="handleNavClick">
              <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
              <span class="link-text">Cursos</span>
            </router-link>

            <router-link v-if="user?.role === 'admin'" to="/admin/inscricoes" class="nav-item" active-class="active" @click="handleNavClick">
              <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              <span class="link-text">Inscrições</span>
            </router-link>

            <router-link v-else to="/portal/inscricoes" class="nav-item" active-class="active" @click="handleNavClick">
               <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              <span class="link-text">Inscrições</span>
            </router-link>
            <router-link v-if="user?.role === 'admin'" to="/admin/reports" class="nav-item" active-class="active" @click="handleNavClick">
              <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
              <span class="link-text">Relatórios</span>
            </router-link>

            <router-link v-if="user?.role === 'admin'" to="/admin/logs" class="nav-item" active-class="active" @click="handleNavClick">
              <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
              <span class="link-text">Ações</span>
            </router-link>
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
    <main class="main-content" :class="{ 'collapsed': isDesktopCollapsed }">
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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(null);
const isMobileSidebarOpen = ref(false);
const isDesktopCollapsed = ref(false);

onMounted(() => {
  const userData = localStorage.getItem('user');
  if (userData) {
    user.value = JSON.parse(userData);
  } else {
    router.push('/login');
  }
});

const toggleMobileSidebar = () => {
  isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
};

const toggleDesktopSidebar = () => {
  isDesktopCollapsed.value = !isDesktopCollapsed.value;
};

// Auto-hide mobile sidebar when clicking a link
const handleNavClick = () => {
  // Mobile: Close completely
  if (window.innerWidth < 768) {
    isMobileSidebarOpen.value = false;
  } 
  // Desktop: Collapse to icons ("meio que se esconda")
  else {
    isDesktopCollapsed.value = true;
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
  width: 280px;
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
  width: 280px; /* Fixed width container to prevent squash during collapse transition */
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

/* Desktop Collapsed State */
.sidebar.collapsed {
  width: 90px;
}
/* Center content when collapsed */
.sidebar.collapsed .sidebar-container {
  width: 90px; /* Constrain container width */
  padding: 1.5rem 0.75rem;
  align-items: center;
}

.sidebar.collapsed .logo-text,
.sidebar.collapsed .link-text,
.sidebar.collapsed .user-info {
  opacity: 0;
  display: none;
}

.sidebar.collapsed .logo,
.sidebar.collapsed .nav-item,
.sidebar.collapsed .user-profile,
.sidebar.collapsed .sidebar-toggle {
  justify-content: center;
  padding-left: 0;
  padding-right: 0;
  width: 100%;
}

.sidebar.collapsed .nav-item:hover {
  transform: none;
}

/* Main Content */
.main-content {
  flex: 1;
  margin-left: 280px;
  padding: 2.5rem;
  transition: margin-left 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.main-content.collapsed {
  margin-left: 90px;
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
  
  .sidebar { transform: translateX(-100%); width: 280px; }
  .sidebar.open { transform: translateX(0); }
  
  /* Reset collapsed state styles for mobile */
  .sidebar.collapsed { width: 280px; }
  .sidebar.collapsed .sidebar-container { width: 280px; padding: 1.5rem; align-items: stretch; }
  .sidebar.collapsed .logo-text,
  .sidebar.collapsed .link-text,
  .sidebar.collapsed .user-info { opacity: 1; display: block; }
  .sidebar.collapsed .logo, .sidebar.collapsed .nav-item { justify-content: flex-start; }
  
  .sidebar-overlay.active { display: block; }

  .main-content {
    margin-left: 0;
    padding: 1.5rem;
    padding-top: 5rem;
  }
  .main-content.collapsed { margin-left: 0; }

  .desktop-only { display: none !important; }
}
</style>
