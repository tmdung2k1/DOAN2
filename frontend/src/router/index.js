import { createRouter, createWebHistory } from 'vue-router'
import TrangChu from '../views/TrangChu.vue'
import DangNhap from '../views/DangNhap.vue'
import Dashboard from '../views/Dashboard.vue'

const routes = [
  {
    path: '/',
    name: 'TrangChu',
    component: TrangChu
  },
  {
    path: '/dang-nhap',
    name: 'DangNhap',
    component: DangNhap
  },
  {
    path: '/admin',
    name: 'Dashboard', 
    component: Dashboard,

    // Thêm meta để đánh dấu đây là trang cần xác thực
    meta: {
      canDangNhap: true
    }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Thêm navigation guard để kiểm tra quyền truy cập
router.beforeEach((to, from, next) => {
  const daDangNhap = localStorage.getItem('admin_token')

  if(to.meta.canDangNhap && !daDangNhap){
    next('/dang-nhap')
  } else {
    next()
  }
})

export default router