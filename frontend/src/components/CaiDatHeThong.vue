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
.modal {
  display: block; position: fixed; z-index: 1050;
  left: 0; top: 0; width: 100%; height: 100%;
  animation: fadeIn 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.modal-backdrop {
  position: fixed; top: 0; left: 0; z-index: 1040;
  width: 100vw; height: 100vh; background-color: #000;
  opacity: 0; cursor: pointer; transition: opacity 0.35s ease;
  animation: backdropFadeIn 0.35s ease;
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes backdropFadeIn { from { opacity: 0; } to { opacity: 0.2; } }
@keyframes slideInUp {
  from { opacity: 0; transform: translateY(30px) scale(0.95); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
.modal-content { animation: slideInUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); will-change: transform, opacity; }

.text-dark-blue { color: #2E6E7E; }
.text-purple    { color: #00C4A0; }

.btn-purple { background-color: #2E6E7E; color: #fff; border: none; transition: all 0.25s ease; border-radius: 8px; }
.btn-purple:hover { background-color: #00C4A0; color: #141414; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,196,160,0.3); }
.btn-light { transition: all 0.25s ease; }
.btn-light:hover { background-color: #f0f0f0 !important; transform: translateY(-2px); }

.custom-input { border-color: #c2d9de; outline: none; transition: all 0.25s ease; }
.custom-input:focus { border-color: #2E6E7E; box-shadow: 0 0 0 0.2rem rgba(46, 110, 126, 0.2); transform: translateY(-1px); }

@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.spinner-border-sm { animation: spin 0.8s linear infinite; }
</style>