<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import QuanLyPhong from '../components/QuanLyPhong.vue'
const router = useRouter()
const tabHienTai = ref('phong-tro') 

const xuLyDangXuat = () => {
  localStorage.removeItem('admin_token')
  router.push('/dang-nhap')
}
</script>

<template>
  <div class="d-flex dashboard-layout">
    <div class="bg-dark-blue text-white p-3 sidebar">
      <h4 class="fw-bold mb-4 mt-2 border-bottom pb-3 text-center text-uppercase">Bảng Điều Khiển</h4>
      <ul class="nav flex-column gap-2">
        <li class="nav-item">
          <!-- Khi click vào đổi trạng thái tabHienTai -->
          <a @click.prevent="tabHienTai = 'phong-tro'" 
             :class="['nav-link text-white fw-bold px-3 py-2', tabHienTai === 'phong-tro' ? 'bg-purple rounded' : '']" 
             href="#">
            Quản lý Phòng Trọ
          </a>
        </li>
        <li class="nav-item">
          <a @click.prevent="tabHienTai = 'cai-dat'" 
             :class="['nav-link text-white fw-bold px-3 py-2', tabHienTai === 'cai-dat' ? 'bg-purple rounded' : '']" 
             href="#">
            Cài đặt Hệ thống
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
        <!-- Nếu tabHienTai = 'phong-tro' thì load Component bảng lên -->
        <QuanLyPhong v-if="tabHienTai === 'phong-tro'" />
        
        <div v-else-if="tabHienTai === 'cai-dat'" class="card border-0 shadow-sm p-5 text-center text-muted">
          <h4 class="fw-bold text-dark-blue">Tính năng Cài đặt Điện/Nước đang phát triển...</h4>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard-layout { min-height: 100vh; }
.sidebar { width: 260px; }
.bg-dark-blue { background-color: #0A192F !important; }
.text-dark-blue { color: #0A192F !important; }
.bg-purple { background-color: #663399 !important; }
.nav-link { transition: all 0.2s; cursor: pointer; }
.nav-link:hover:not(.bg-purple) { background-color: rgba(255, 255, 255, 0.1); border-radius: 6px; }
</style>