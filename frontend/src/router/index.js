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
    meta: {
      canDangNhap: true
    }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const daDangNhap = localStorage.getItem('admin_token')

  if(to.meta.canDangNhap && !daDangNhap){
    next('/dang-nhap')
  } else {
    next()
  }
})

export default router