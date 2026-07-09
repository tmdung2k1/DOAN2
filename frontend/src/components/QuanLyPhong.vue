<script setup>
import { ref, onMounted } from 'vue'

const danhSachToast = ref([])
const hienToast = (noidung, loai = 'success') => {
  const id = Date.now()
  danhSachToast.value.push({ id, noidung, loai })
  setTimeout(() => {
    danhSachToast.value = danhSachToast.value.filter(t => t.id !== id)
  }, 3000)
}

const danhSachPhong = ref([])
const danhSachLoaiPhong = ref([])
const dangTai = ref(true)
const token = localStorage.getItem('admin_token')

const hienThiModal = ref(false)
const laCheDoSua = ref(false)
const formPhong = ref({ id: null, so_phong: '', loai_phong_id: '', dien_tich: '', gia_thue: '', gia_coc: '', trang_thai: 'trong', tang_id: null })

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
  formPhong.value = { id: null, so_phong: '', loai_phong_id: '', dien_tich: '', gia_thue: '', gia_coc: '', trang_thai: 'trong', tang_id: null }
  hienThiModal.value = true
}

const moModalSua = (phong) => {
  laCheDoSua.value = true
  formPhong.value = { ...phong } 
  hienThiModal.value = true
}

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
      layDanhSachPhong()
      hienToast(result.message)
    } else {
      hienToast('Lỗi: ' + (result.message || 'Kiểm tra lại dữ liệu nhập!'), 'error')
    }
  } catch (error) {
    hienToast('Không thể kết nối đến máy chủ!', 'error')
  }
}

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
      hienToast(`Đã xóa phòng ${soPhong} thành công!`)
    } else {
      hienToast('Xóa thất bại!', 'error')
    }
  } catch (error) {
    hienToast('Lỗi kết nối!', 'error')
  }
}

const dinhDangTien = (tien) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(tien)

const layTenLoaiPhong = (loaiPhongId) => {
  const loaiPhong = danhSachLoaiPhong.value.find(lp => lp.id === loaiPhongId)
  return loaiPhong ? loaiPhong.ten_loai : 'Không xác định'
}

const dinhDangTrangThai = (trangThai) => {
  const map = {
    trong:     { label: 'Trống',     cls: 'badge-trong' },
    da_thue:   { label: 'Đã Thuê',    cls: 'badge-da-thue' },
    dat_truoc: { label: 'Đặt Trước', cls: 'badge-dat-truoc' },
    bao_tri:   { label: 'Bảo Trì',   cls: 'badge-bao-tri' },
  }
  return map[trangThai] || { label: trangThai, cls: '' }
}

const hienThiModalTienIch = ref(false)
const danhSachTatCaTienIch = ref([])
const tienIchDuocChon = ref([])
const phongDangChonTienIch = ref(null)

const layTatCaTienIch = async () => {
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/tien-ich', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') danhSachTatCaTienIch.value = result.data
  } catch (error) { console.error('Lỗi tải tiện ích:', error) }
}

const moModalTienIch = async (phong) => {
  phongDangChonTienIch.value = phong
  tienIchDuocChon.value = []
  
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/phong/${phong.id}/tien-ich`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') {
      tienIchDuocChon.value = result.data
    }
  } catch (error) { console.error('Lỗi tải tiện ích phòng:', error) }
  
  hienThiModalTienIch.value = true
}

const luuTienIchPhong = async () => {
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/phong/${phongDangChonTienIch.value.id}/tien-ich`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ tien_ich_ids: tienIchDuocChon.value })
    })
    const result = await res.json()
    if (result.status === 'success') {
      hienThiModalTienIch.value = false
      layDanhSachPhong()
      hienToast('Đã cập nhật tiện ích cho phòng thành công!')
    }
  } catch (error) { hienToast('Lỗi kết nối!', 'error') }
}

onMounted(() => {
  layDanhSachLoaiPhong()
  layDanhSachPhong()
  layTatCaTienIch()
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
            <th class="py-3">Tiện Ích</th>
            <th class="py-3">Trạng Thái</th>
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
            <td>
              <div class="d-flex flex-wrap gap-1">
                <span
                  v-if="phong.tien_ich && phong.tien_ich.length > 0"
                  v-for="ti in phong.tien_ich"
                  :key="ti.id"
                  class="badge-tien-ich"
                >{{ ti.ten_tien_ich }}</span>
                <span v-else class="text-muted small fst-italic">Chưa có</span>
              </div>
            </td>
            <td>
              <span :class="['badge', dinhDangTrangThai(phong.trang_thai).cls]">{{ dinhDangTrangThai(phong.trang_thai).label }}</span>
            </td>
            <td class="text-end pe-4">
              <button @click="moModalTienIch(phong)" class="btn btn-sm btn-info text-white fw-bold me-2">Tiện ích</button>
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
            <div class="col-md-12">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Trạng Thái Phòng</label>
              <select v-model="formPhong.trang_thai" class="form-control custom-input" required>
                <option value="trong">Trống</option>
                <option value="da_thue">Đã Thuê</option>
                <option value="dat_truoc">Đặt Trước</option>
                <option value="bao_tri">Bảo Trì</option>
              </select>
            </div>
          </div>
          
          <div class="d-flex justify-content-end mt-4 pt-3 border-top">
            <button type="button" @click="hienThiModal = false" class="btn btn-light fw-bold me-2">Hủy Bỏ</button>
            <button type="submit" class="btn btn-purple fw-bold px-4">Lưu Thông Tin</button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="hienThiModalTienIch" class="modal-overlay d-flex justify-content-center align-items-center">
      <div class="modal-content bg-white p-4 rounded shadow-lg" style="max-width: 520px; width: 100%;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
          <h5 class="fw-bold text-dark-blue m-0">
            Tiện Ích – <span class="text-purple">Phòng {{ phongDangChonTienIch?.so_phong }}</span>
          </h5>
          <button @click="hienThiModalTienIch = false" class="btn btn-link text-danger text-decoration-none fw-bold p-0">✕ Đóng</button>
        </div>

        <div v-if="danhSachTatCaTienIch.length === 0" class="text-center text-muted py-3">
          Chưa có tiện ích nào trong kho. Vui lòng thêm tiện ích trước.
        </div>

        <form v-else @submit.prevent="luuTienIchPhong">
          <div class="row g-3">
            <div v-for="ti in danhSachTatCaTienIch" :key="ti.id" class="col-md-6">
              <div class="form-check custom-checkbox">
                <input
                  class="form-check-input"
                  type="checkbox"
                  :value="ti.id"
                  :id="'ti_' + ti.id"
                  v-model="tienIchDuocChon"
                >
                <label class="form-check-label fw-bold text-secondary" :for="'ti_' + ti.id">
                  {{ ti.ten_tien_ich }}
                </label>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end pt-4 mt-4 border-top">
            <button type="button" @click="hienThiModalTienIch = false" class="btn btn-light fw-bold me-2">Hủy</button>
            <button type="submit" class="btn btn-purple fw-bold px-4">Lưu Tiện Ích</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <teleport to="body">
    <div class="toast-container">
      <transition-group name="toast">
        <div
          v-for="toast in danhSachToast"
          :key="toast.id"
          :class="['toast-item', toast.loai === 'error' ? 'toast-error' : 'toast-success']"
        >
          <span class="toast-icon">{{ toast.loai === 'error' ? '❌' : '✅' }}</span>
          <span class="toast-msg">{{ toast.noidung }}</span>
          <button class="toast-close" @click="danhSachToast = danhSachToast.filter(t => t.id !== toast.id)">&times;</button>
        </div>
      </transition-group>
    </div>
  </teleport>
</template>

<style scoped>
@import "../assets/css/quan-ly-phong.css";

.toast-container {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 99999;
  display: flex;
  flex-direction: column;
  gap: 10px;
  pointer-events: none;
}

.toast-item {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 280px;
  max-width: 400px;
  padding: 14px 16px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
  pointer-events: all;
  backdrop-filter: blur(8px);
}

.toast-success {
  background: linear-gradient(135deg, #d1fae5, #ecfdf5);
  border: 1px solid #6ee7b7;
  color: #065f46;
}

.toast-error {
  background: linear-gradient(135deg, #fee2e2, #fff5f5);
  border: 1px solid #fca5a5;
  color: #991b1b;
}

.toast-icon { font-size: 16px; flex-shrink: 0; }
.toast-msg  { flex: 1; line-height: 1.4; }

.toast-close {
  background: none;
  border: none;
  font-size: 18px;
  line-height: 1;
  cursor: pointer;
  opacity: 0.5;
  padding: 0;
  color: inherit;
  flex-shrink: 0;
}
.toast-close:hover { opacity: 1; }

.toast-enter-active { animation: toastIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); }
.toast-leave-active { animation: toastOut 0.3s ease-in forwards; }

@keyframes toastIn {
  from { opacity: 0; transform: translateX(60px) scale(0.85); }
  to   { opacity: 1; transform: translateX(0)  scale(1); }
}
@keyframes toastOut {
  from { opacity: 1; transform: translateX(0); }
  to   { opacity: 0; transform: translateX(60px); }
}
</style>