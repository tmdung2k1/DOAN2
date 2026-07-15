<script setup>
import { ref, onMounted } from 'vue'

const danhSachLoai = ref([])
const dangTai = ref(true)
const token = localStorage.getItem('admin_token')

const hienThiModal = ref(false)
const laCheDoSua = ref(false)
const formLoai = ref({ id: null, ten_loai: '' })

const layDanhSach = async () => {
  dangTai.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/loai-phong', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') danhSachLoai.value = result.data
  } catch (error) { console.error(error) }
  dangTai.value = false
}

const moModalThem = () => {
  laCheDoSua.value = false
  formLoai.value = { id: null, ten_loai: '' }
  hienThiModal.value = true
}

const moModalSua = (loai) => {
  laCheDoSua.value = true
  formLoai.value = { ...loai }
  hienThiModal.value = true
}

const luuDuLieu = async () => {
  const url = laCheDoSua.value ? `http://127.0.0.1:8000/api/admin/loai-phong/${formLoai.value.id}` : 'http://127.0.0.1:8000/api/admin/loai-phong'
  const method = laCheDoSua.value ? 'PUT' : 'POST'
  
  try {
    const res = await fetch(url, {
      method,
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(formLoai.value)
    })
    const result = await res.json()
    if (result.status === 'success') {
      hienThiModal.value = false
      layDanhSach()
    } else alert('Lỗi: ' + result.message)
  } catch (error) { alert('Lỗi kết nối!') }
}

const xoaDuLieu = async (id, ten) => {
  if (!confirm(`Xóa loại phòng: ${ten}?`)) return
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/loai-phong/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    if ((await res.json()).status === 'success') layDanhSach()
  } catch (error) { alert('Lỗi kết nối!') }
}

onMounted(() => layDanhSach())
</script>

<template>
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
      <h5 class="fw-bold text-dark-blue m-0">Quản Lý Loại Phòng</h5>
      <button @click="moModalThem" class="btn btn-purple fw-bold px-4">+ Thêm Loại Phòng</button>
    </div>
    
    <div class="card-body p-0">
      <div v-if="dangTai" class="p-5 text-center text-purple fw-bold">Đang tải dữ liệu...</div>
      <div v-else-if="danhSachLoai.length === 0" class="p-5 text-center text-muted">Chưa có loại phòng nào trong hệ thống.</div>
      <table v-else class="table table-hover align-middle m-0">
        <thead class="table-light text-uppercase small text-muted">
          <tr>
            <th class="ps-4 py-3" style="width: 100px;">ID</th>
            <th class="py-3">Tên Loại Phòng</th>
            <th class="text-end pe-4 py-3">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="loai in danhSachLoai" :key="loai.id">
            <td class="ps-4 fw-bold">{{ loai.id }}</td>
            <td class="fw-bold text-dark-blue">{{ loai.ten_loai }}</td>
            <td class="text-end pe-4">
              <button @click="moModalSua(loai)" class="btn btn-sm btn-outline-primary fw-bold me-2">Sửa</button>
              <button @click="xoaDuLieu(loai.id, loai.ten_loai)" class="btn btn-sm btn-outline-danger fw-bold">Xóa</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="hienThiModal" class="modal-overlay d-flex justify-content-center align-items-center">
      <div class="modal-content bg-white rounded shadow-lg" style="max-width: 500px; width: 90%;">
        <div class="d-flex justify-content-between align-items-center p-4 text-white" style="background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%); border-radius: 12px 12px 0 0;">
          <h5 class="fw-bold m-0 text-white">{{ laCheDoSua ? 'Sửa Loại Phòng' : 'Thêm Mới' }}</h5>
          <button @click="hienThiModal = false" class="btn btn-sm btn-outline-light fw-bold">✕ Đóng</button>
        </div>
        <form @submit.prevent="luuDuLieu" class="p-4">
          <div class="mb-4">
            <label class="form-label small fw-bold text-dark-blue text-uppercase">Tên Loại Phòng</label>
            <input v-model="formLoai.ten_loai" type="text" class="form-control custom-input" placeholder="VD: Phòng Studio, Phòng Trống..." required>
          </div>
          <div class="d-flex justify-content-end pt-3 border-top">
            <button type="button" @click="hienThiModal = false" class="btn btn-light fw-bold me-2">Hủy</button>
            <button type="submit" class="btn btn-purple fw-bold px-4">Lưu</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import "../assets/css/quan-ly-loai-phong.css";
</style>