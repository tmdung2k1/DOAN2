<script setup>
import { ref, onMounted } from 'vue'

const danhSachPhong = ref([])
const danhSachLoaiPhong = ref([])
const dangTai = ref(true)
const token = localStorage.getItem('admin_token')

const hienThiModal = ref(false)
const laCheDoSua = ref(false)
const formPhong = ref({ id: null, so_phong: '', loai_phong_id: '', dien_tich: '', gia_thue: '', gia_coc: '', tang_id: null })

// Lấy danh sách loại phòng
const layDanhSachLoaiPhong = async () => {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/admin/loai-phong', {
      headers: { 
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json' 
      }
    })
    const result = await response.json()
    if (result.status === 'success') {
      danhSachLoaiPhong.value = result.data
    }
  } catch (error) {
    console.error('Lỗi tải danh sách loại phòng:', error)
  }
}

// Lấy danh sách phòng
const layDanhSachPhong = async () => {
  try {
    dangTai.value = true
    const response = await fetch('http://127.0.0.1:8000/api/admin/phong', {
      headers: { 
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json' 
      }
    })
    const result = await response.json()
    if (result.status === 'success') {
      danhSachPhong.value = result.data
    }
  } catch (error) {
    console.error('Lỗi tải dữ liệu:', error)
  } finally {
    dangTai.value = false
  }
}

const moModalThem = () => {
  laCheDoSua.value = false
  formPhong.value = { id: null, so_phong: '', loai_phong_id: '', dien_tich: '', gia_thue: '', gia_coc: '', tang_id: null }
  hienThiModal.value = true
}

const moModalSua = (phong) => {
  laCheDoSua.value = true
  formPhong.value = { ...phong } // Copy dữ liệu phòng vào form
  hienThiModal.value = true
}

// Lưu dữ liệu 
const luuPhong = async () => {
  const url = laCheDoSua.value 
    ? `http://127.0.0.1:8000/api/admin/phong/${formPhong.value.id}` 
    : 'http://127.0.0.1:8000/api/admin/phong'
  
  const method = laCheDoSua.value ? 'PUT' : 'POST'

  try {
    const response = await fetch(url, {
      method: method,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(formPhong.value)
    })
    
    const result = await response.json()
    if (result.status === 'success') {
      hienThiModal.value = false
      layDanhSachPhong() // Tải lại bảng
      alert(result.message)
    } else {
      alert('Lỗi: ' + (result.message || 'Kiểm tra lại dữ liệu nhập!'))
    }
  } catch (error) {
    alert('Không thể kết nối đến máy chủ!')
  }
}

// Xóa phòng
const xoaPhong = async (id, soPhong) => {
  if (!confirm(`Bạn có chắc chắn muốn xóa phòng ${soPhong}?`)) return

  try {
    const response = await fetch(`http://127.0.0.1:8000/api/admin/phong/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await response.json()
    if (result.status === 'success') {
      layDanhSachPhong()
    } else {
      alert('Xóa thất bại!')
    }
  } catch (error) {
    alert('Lỗi kết nối!')
  }
}

const dinhDangTien = (tien) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(tien)

const layTenLoaiPhong = (loaiPhongId) => {
  const loaiPhong = danhSachLoaiPhong.value.find(lp => lp.id === loaiPhongId)
  return loaiPhong ? loaiPhong.ten_loai : 'Không xác định'
}

onMounted(() => {
  layDanhSachLoaiPhong()
  layDanhSachPhong()
})
</script>

<template>
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
      <h5 class="fw-bold text-dark-blue m-0">Danh Sách Phòng Trọ</h5>
      <button @click="moModalThem" class="btn btn-purple fw-bold px-4 text-uppercase">
        + Thêm Phòng Mới
      </button>
    </div>

    <div class="card-body p-0">
      <div v-if="dangTai" class="p-5 text-center text-purple fw-bold">Đang tải dữ liệu...</div>
      <div v-else-if="danhSachPhong.length === 0" class="p-5 text-center text-muted">Chưa có phòng nào trong hệ thống.</div>
      
      <table v-else class="table table-hover align-middle m-0">
        <thead class="table-light text-uppercase small text-muted">
          <tr>
            <th class="ps-4 py-3">Số Phòng</th>
            <th class="py-3">Loại Phòng</th>
            <th class="py-3">Diện tích</th>
            <th class="py-3">Giá Thuê</th>
            <th class="py-3">Tiền Cọc</th>
            <th class="text-end pe-4 py-3">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="phong in danhSachPhong" :key="phong.id">
            <td class="ps-4 fw-bold text-dark-blue">{{ phong.so_phong }}</td>
            <td><span class="badge badge-loai-phong">{{ layTenLoaiPhong(phong.loai_phong_id) }}</span></td>
            <td>{{ phong.dien_tich }} m²</td>
            <td class="fw-bold text-danger">{{ dinhDangTien(phong.gia_thue) }}</td>
            <td class="text-muted">{{ dinhDangTien(phong.gia_coc) }}</td>
            <td class="text-end pe-4">
              <button @click="moModalSua(phong)" class="btn btn-sm btn-outline-primary fw-bold me-2">Sửa</button>
              <button @click="xoaPhong(phong.id, phong.so_phong)" class="btn btn-sm btn-outline-danger fw-bold">Xóa</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="hienThiModal" class="modal-overlay d-flex justify-content-center align-items-center">
      <div class="modal-content bg-white p-4 rounded shadow-lg">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
          <h5 class="fw-bold text-dark-blue m-0">{{ laCheDoSua ? 'Cập Nhật Phòng' : 'Thêm Phòng Mới' }}</h5>
          <button @click="hienThiModal = false" class="btn btn-link text-danger text-decoration-none fw-bold p-0"> X </button>
        </div>

        <form @submit.prevent="luuPhong">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Số Phòng</label>
              <input v-model="formPhong.so_phong" type="text" class="form-control custom-input" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Loại Phòng</label>
              <select v-model="formPhong.loai_phong_id" class="form-control custom-input" required>
                <option value="">-- Chọn loại phòng --</option>
                <option v-for="loaiPhong in danhSachLoaiPhong" :key="loaiPhong.id" :value="loaiPhong.id">
                  {{ loaiPhong.ten_loai }}
                </option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Diện tích (m²)</label>
              <input v-model="formPhong.dien_tich" type="number" step="0.1" class="form-control custom-input" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Giá Thuê</label>
              <input v-model="formPhong.gia_thue" type="number" class="form-control custom-input" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Tiền Cọc</label>
              <input v-model="formPhong.gia_coc" type="number" class="form-control custom-input" required>
            </div>
          </div>
          
          <div class="d-flex justify-content-end mt-4 pt-3 border-top">
            <button type="button" @click="hienThiModal = false" class="btn btn-light fw-bold me-2">Hủy Bỏ</button>
            <button type="submit" class="btn btn-purple fw-bold px-4">Lưu Thông Tin</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.text-dark-blue { color: #0A192F; }
.btn-purple { background-color: #663399; color: #fff; border: none; transition: 0.3s; }
.btn-purple:hover { background-color: #0A192F; color: #fff; }
.bg-purple-light { background-color: #f1ebf7; }
.badge-loai-phong { background-color: #27ae60; color: #fff; font-weight: 500; padding: 0.5em 0.75em; }

/* Modal CSS dùng chung */
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
  background-color: rgba(10, 25, 47, 0.7); z-index: 9999;
}
.modal-content {
  width: 90%; max-width: 600px; animation: slideDown 0.3s ease-out;
}
.custom-input {
  border-color: #e1dbec; border-radius: 6px; outline: none; transition: 0.2s;
}
.custom-input:focus {
  border-color: #663399; box-shadow: 0 0 0 0.25rem rgba(102, 51, 153, 0.15);
}
@keyframes slideDown {
  0% { opacity: 0; transform: translateY(-30px); }
  100% { opacity: 1; transform: translateY(0); }
}
</style>