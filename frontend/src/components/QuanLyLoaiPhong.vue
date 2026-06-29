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
      <div class="modal-content bg-white p-4 rounded shadow-lg">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
          <h5 class="fw-bold m-0">{{ laCheDoSua ? 'Sửa Loại Phòng' : 'Thêm Mới' }}</h5>
          <button @click="hienThiModal = false" class="btn btn-link text-danger text-decoration-none fw-bold p-0">X</button>
        </div>
        <form @submit.prevent="luuDuLieu">
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
.text-dark-blue { color: #2E6E7E; }
.btn-purple { background-color: #2E6E7E; color: #fff; border: none; transition: 0.25s; border-radius: 8px; }
.btn-purple:hover { background-color: #00C4A0; color: #141414; }
.modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(20, 20, 20, 0.72); z-index: 9999; }
.modal-content { width: 90%; max-width: 500px; animation: slideDown 0.3s ease-out; }
.custom-input { border-color: #c2d9de; border-radius: 6px; outline: none; transition: 0.2s; }
.custom-input:focus { border-color: #2E6E7E; box-shadow: 0 0 0 0.25rem rgba(46, 110, 126, 0.18); }
@keyframes slideDown { 0% { opacity: 0; transform: translateY(-30px); } 100% { opacity: 1; transform: translateY(0); } }
</style>