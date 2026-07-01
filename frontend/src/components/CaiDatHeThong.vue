<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  isOpen: Boolean
})

const emit = defineEmits(['close'])

const formCaiDat = ref({
  gia_dien: 0,
  gia_nuoc: 0
})
const dangTai = ref(true)
const token = localStorage.getItem('admin_token')

// Lấy dữ liệu khi mở trang
const layDuLieuCaiDat = async () => {
  dangTai.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/cai-dat', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') {
      formCaiDat.value = result.data
    }
  } catch (error) {
    console.error('Lỗi tải cài đặt:', error)
  }
  dangTai.value = false
}

// Lưu dữ liệu
const luuCaiDat = async () => {
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/cai-dat', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(formCaiDat.value)
    })
    const result = await res.json()
    if (result.status === 'success') {
      alert('Đã cập nhật hệ thống thành công!')
      emit('close')
    } else {
      alert('Lỗi: ' + result.message)
    }
  } catch (error) {
    alert('Không thể kết nối đến máy chủ!')
  }
}

const closeModal = () => {
  emit('close')
}

onMounted(() => layDuLieuCaiDat())
</script>

<template>
  <!-- Modal Backdrop -->
  <div v-if="isOpen" class="modal-backdrop fade show" @click="closeModal"></div>
  
  <!-- Modal -->
  <div v-if="isOpen" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-white border-bottom-0 p-4">
          <h5 class="modal-title fw-bold text-dark-blue">⚙️ Thiết Lập Đơn Giá Điện / Nước</h5>
          <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <div v-if="dangTai" class="text-center text-purple fw-bold my-4">
            <div class="spinner-border spinner-border-sm me-2" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            Đang tải cấu hình...
          </div>
          
          <form v-else @submit.prevent="luuCaiDat">
            <div class="mb-4">
              <label class="form-label fw-bold text-dark-blue">Đơn giá Điện (VNĐ / kWh)</label>
              <div class="input-group">
                <span class="input-group-text bg-light">⚡</span>
                <input v-model="formCaiDat.gia_dien" type="number" class="form-control custom-input" required>
                <span class="input-group-text bg-light text-muted">VNĐ</span>
              </div>
              <small class="text-muted">Mức giá áp dụng tính tiền điện hàng tháng cho khách thuê.</small>
            </div>

            <div class="mb-4">
              <label class="form-label fw-bold text-dark-blue">Đơn giá Nước (VNĐ / Khối)</label>
              <div class="input-group">
                <span class="input-group-text bg-light">💧</span>
                <input v-model="formCaiDat.gia_nuoc" type="number" class="form-control custom-input" required>
                <span class="input-group-text bg-light text-muted">VNĐ</span>
              </div>
              <small class="text-muted">Mức giá áp dụng tính tiền nước sinh hoạt.</small>
            </div>

            <div class="d-flex gap-2 justify-content-end pt-3 border-top mt-5">
              <button type="button" @click="closeModal" class="btn btn-light fw-bold px-4 py-2">Hủy</button>
              <button type="submit" class="btn btn-purple fw-bold px-4 py-2 text-uppercase">Lưu Cấu Hình</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import "../assets/css/cai-dat-he-thong.css";
</style>