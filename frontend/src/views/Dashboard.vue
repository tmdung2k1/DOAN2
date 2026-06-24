<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import QuanLyPhong from '../components/QuanLyPhong.vue'
import QuanLyLoaiPhong from '../components/QuanLyLoaiPhong.vue'
import QuanLyDatPhong from '../components/QuanLyDatPhong.vue'
import CaiDatHeThong from '../components/CaiDatHeThong.vue'
import QuanLyHopDong from '../components/QuanLyHopDong.vue'

const router = useRouter()
const tabHienTai = ref('phong-tro')
const sidebarThuGon = ref(false)
const moModalCaiDat = ref(false)

const xuLyDangXuat = () => {
  localStorage.removeItem('admin_token')
  router.push('/dang-nhap')
}

const toggleSidebar = () => {
  sidebarThuGon.value = !sidebarThuGon.value
}
</script>

<template>
  <div class="d-flex dashboard-layout">
    <div :class="['bg-dark-blue text-white sidebar', { 'sidebar-thu-gon': sidebarThuGon }]">
      <div class="d-flex justify-content-between align-items-center p-3 border-bottom pb-3">
        <h4 v-if="!sidebarThuGon" class="fw-bold mb-0 text-center text-uppercase" style="flex: 1;">Dashboard</h4>
        <button @click="toggleSidebar" class="btn btn-sm btn-dark text-white" style="border: none;">
          <i :class="['bi', sidebarThuGon ? 'bi-chevron-right' : 'bi-chevron-left']"></i>
        </button>
      </div>
      
      <ul class="nav flex-column gap-2 p-3">
        <li class="nav-item">
          <a @click.prevent="tabHienTai = 'phong-tro'" 
             :class="['nav-link text-white fw-bold px-3 py-2 d-flex align-items-center gap-2', tabHienTai === 'phong-tro' ? 'active-menu' : '']" 
             href="#">
            <i class="bi bi-door-closed"></i>
            <span v-if="!sidebarThuGon">Quản lý Phòng Trọ</span>
          </a>
        </li>
        <li class="nav-item">
          <a @click.prevent="tabHienTai = 'loai-phong'" 
             :class="['nav-link text-white fw-bold px-3 py-2 d-flex align-items-center gap-2', tabHienTai === 'loai-phong' ? 'active-menu' : '']" 
             href="#">
            <i class="bi bi-collection"></i>
            <span v-if="!sidebarThuGon">Quản lý Loại Phòng</span>
          </a>
        </li>
        <li class="nav-item">
          <a @click.prevent="tabHienTai = 'dat-phong'" 
             :class="['nav-link text-white fw-bold px-3 py-2 d-flex align-items-center gap-2', tabHienTai === 'dat-phong' ? 'active-menu' : '']" 
             href="#">
            <i class="bi bi-calendar-check"></i>
            <span v-if="!sidebarThuGon">Quản lý Đặt Phòng</span>
          </a>
        </li>
        <li class="nav-item">
  <a @click.prevent="tabHienTai = 'hop-dong'" 
     :class="['nav-link text-white fw-bold px-3 py-2', tabHienTai === 'hop-dong' ? 'bg-purple rounded' : '']" 
     href="#">
    Quản lý Hợp Đồng
  </a>
</li>
        <li class="nav-item">
          <a @click.prevent="moModalCaiDat = true" 
             :class="['nav-link text-white fw-bold px-3 py-2 d-flex align-items-center gap-2']" 
             href="#">
            <i class="bi bi-gear"></i>
            <span v-if="!sidebarThuGon">Cài đặt Hệ Thống</span>
          </a>  
        </li>
      </ul>
    </div>

    <div class="flex-grow-1 bg-light main-content">
      <div class="bg-white p-3 shadow-sm d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0 fw-bold text-dark-blue">Trang Quản Trị Hệ Thống</h5>
        <button @click="xuLyDangXuat" class="btn btn-sm btn-outline-danger fw-bold text-uppercase px-4">Đăng xuất</button>
      </div>
      
      <div class="px-4 pb-4">
        <QuanLyPhong v-if="tabHienTai === 'phong-tro'" />
        <QuanLyLoaiPhong v-else-if="tabHienTai === 'loai-phong'" />
        <QuanLyDatPhong v-else-if="tabHienTai === 'dat-phong'" /> 
        <QuanLyHopDong v-else-if="tabHienTai === 'hop-dong'" />
      </div>
    </div>

    <CaiDatHeThong :isOpen="moModalCaiDat" @close="moModalCaiDat = false" />
  </div>
</template>

<style scoped>
.dashboard-layout { 
  min-height: 100vh;
}

.sidebar { 
  width: 260px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  flex-direction: column;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
}

.sidebar-thu-gon {
  width: 80px;
}

.sidebar-thu-gon h4,
.sidebar-thu-gon span {
  display: none;
}

.bg-dark-blue { 
  background-color: #0A192F !important; 
}

.text-dark-blue { 
  color: #0A192F !important; 
}

.nav {
  flex: 1;
  overflow: hidden;
}

.nav-link {
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  border-radius: 8px;
  white-space: nowrap;
  position: relative;
  overflow: hidden;
}

.nav-link::before {
  display: none;
}

.nav-link:hover:not(.active-menu) { 
  background-color: rgba(102, 51, 153, 0.3) !important;
  transform: translateX(4px);
}

.active-menu {
  background: linear-gradient(135deg, #8B5CF6 0%, #663399 100%) !important;
  box-shadow: 0 2px 8px rgba(102, 51, 153, 0.4) !important;
  font-weight: 700;
}

.nav-item {
  transition: all 0.3s ease;
}

.main-content {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn:hover {
  transform: translateY(-2px);
}

/* Scroll bar styling */
.sidebar::-webkit-scrollbar {
  width: 6px;
}

.sidebar::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}

/* Icon styling */
.nav-link i {
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 1.1rem;
}

.active-menu i {
  color: #fff;
}
</style>