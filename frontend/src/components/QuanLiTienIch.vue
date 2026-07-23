<script setup>
import { ref, onMounted } from 'vue'
import '@/assets/css/quan-ly-tien-ich.css'

const danhSachTienIch = ref([])
const dangTai = ref(true)
const token = localStorage.getItem('admin_token')

const hienThiModal = ref(false)
const formTienIch = ref({
  ten_tien_ich: '',
  loai: 'co_ban'
})

const layDanhSachTienIch = async () => {
  dangTai.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/tien-ich', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') {
      danhSachTienIch.value = result.data
    }
  } catch (error) { console.error('Lỗi tải danh sách:', error) }
  dangTai.value = false
}

const moModalThem = () => {
  formTienIch.value = { ten_tien_ich: '', loai: 'co_ban' }
  hienThiModal.value = true
}

const luuTienIch = async () => {
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/tien-ich', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(formTienIch.value)
    })
    const result = await res.json()
    if (res.ok) {
      hienThiModal.value = false
      layDanhSachTienIch()
    } else {
      const errors = result.errors
      if (errors) {
        const firstError = Object.values(errors)[0][0]
        alert('Lỗi: ' + firstError)
      } else {
        alert('Lỗi: ' + (result.message || 'Dữ liệu không hợp lệ!'))
      }
    }
  } catch (error) { alert('Lỗi kết nối máy chủ!') }
}

const xoaTienIch = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa tiện ích này khỏi hệ thống?')) return
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/tien-ich/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    if ((await res.json()).status === 'success') layDanhSachTienIch()
  } catch (error) { alert('Lỗi kết nối!') }
}

const dichLoai = (loai) => {
  const dict = {
    'co_ban': 'Cơ Bản',
    'noi_that': 'Nội Thất',
    'an_ninh': 'An Ninh'
  }
  return dict[loai] || loai
}

onMounted(() => layDanhSachTienIch())
</script>

<template>
  <div class="card border-0 shadow-sm" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
      <h5 class="fw-bold text-dark-blue m-0">Danh Mục Tiện Ích Phòng</h5>
      <button @click="moModalThem" class="btn btn-purple fw-bold px-4 text-uppercase">
        + Thêm Tiện Ích
      </button>
    </div>

    <div class="card-body p-0">
      <div v-if="dangTai" class="p-5 text-center text-purple fw-bold">Đang tải dữ liệu...</div>
      <div v-else-if="danhSachTienIch.length === 0" class="p-5 text-center text-muted">Chưa có tiện ích nào trong hệ thống.</div>
      
      <table v-else class="table table-hover align-middle m-0">
        <thead class="table-light text-uppercase small text-muted">
          <tr>
            <th class="ps-4 py-3">Tên Tiện Ích</th>
            <th class="py-3">Phân Loại</th>
            <th class="text-end pe-4 py-3">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ti in danhSachTienIch" :key="ti.Ma_TienIch">
            <td class="ps-4 fw-bold text-dark-blue">{{ ti.ten_tien_ich }}</td>
            <td>
              <span v-if="ti.loai === 'co_ban'" class="badge bg-secondary">Cơ Bản</span>
              <span v-else-if="ti.loai === 'noi_that'" class="badge bg-info text-dark">Nội Thất</span>
              <span v-else-if="ti.loai === 'an_ninh'" class="badge bg-success">An Ninh</span>
            </td>
            <td class="text-end pe-4">
              <button @click="xoaTienIch(ti.Ma_TienIch)" class="btn btn-sm btn-outline-danger fw-bold">Xóa</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="hienThiModal" class="modal-overlay d-flex justify-content-center align-items-center">
      <div class="modal-content bg-white rounded shadow-lg" style="max-width: 500px; width: 90%;">
        <div class="d-flex justify-content-between align-items-center p-4 text-white" style="background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%); border-radius: 12px 12px 0 0;">
          <h5 class="fw-bold m-0 text-white">Thêm Tiện Ích Mới</h5>
          <button @click="hienThiModal = false" class="btn btn-sm btn-outline-light fw-bold">✕ Đóng</button>
        </div>
        <div class="p-4">

        <form @submit.prevent="luuTienIch">
          <div class="mb-3">
            <label class="form-label small fw-bold text-dark-blue text-uppercase">Tên Tiện Ích</label>
            <input v-model="formTienIch.ten_tien_ich" type="text" class="form-control custom-input" required>
          </div>
          
          <div class="mb-4">
            <label class="form-label small fw-bold text-dark-blue text-uppercase">Phân Loại</label>
            <select v-model="formTienIch.loai" class="form-select custom-input" required>
              <option value="co_ban">Tiện ích Cơ Bản</option>
              <option value="noi_that">Nội Thất </option>
              <option value="an_ninh">An Ninh </option>
            </select>
          </div>
          
          <div class="d-flex justify-content-end pt-3 border-top">
            <button type="button" @click="hienThiModal = false" class="btn btn-light fw-bold me-2">Hủy Bỏ</button>
            <button type="submit" class="btn btn-purple fw-bold px-4">Lưu Lại</button>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
</template>
