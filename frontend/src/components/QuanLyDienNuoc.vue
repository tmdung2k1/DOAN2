<script setup>
import { ref, onMounted } from 'vue'

const danhSachChiSo = ref([])
const danhSachPhong = ref([])
const dangTai = ref(true)
const token = localStorage.getItem('admin_token')

const hienThiModal = ref(false)
const loiForm = ref('')
const formChiSo = ref({
  phong_id: null,
  thang_ghi_nhan: '',
  loai_chi_so: 'dien',
  chi_so_cu: '',
  chi_so_moi: ''
})

const layDanhSachChiSo = async () => {
  dangTai.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/dien-nuoc', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') {
      danhSachChiSo.value = result.data
    }
  } catch (error) { console.error('Lỗi tải chỉ số:', error) }
  dangTai.value = false
}

const layDanhSachPhong = async () => {
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/phong', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') {
      danhSachPhong.value = result.data.filter(p => p.trang_thai === 'da_thue')
    }
  } catch (error) { console.error('Lỗi tải danh sách phòng:', error) }
}

const moModalThem = () => {
  const homNay = new Date().toISOString().split('T')[0]
  formChiSo.value = { phong_id: null, thang_ghi_nhan: homNay, loai_chi_so: 'dien', chi_so_cu: '', chi_so_moi: '' }
  loiForm.value = ''
  hienThiModal.value = true
}

const luuChiSo = async () => {
  loiForm.value = ''

  // Validate phiên thành phần trước khi gửi
  if (!formChiSo.value.phong_id) {
    loiForm.value = 'Vui lòng chọn phòng.'
    return
  }
  const chiSoCu = parseFloat(formChiSo.value.chi_so_cu)
  const chiSoMoi = parseFloat(formChiSo.value.chi_so_moi)
  if (isNaN(chiSoCu) || isNaN(chiSoMoi)) {
    loiForm.value = 'Chỉ số cũ và chỉ số mới phải là số hợp lệ.'
    return
  }
  if (chiSoMoi < chiSoCu) {
    loiForm.value = 'Chỉ số mới không được nhỏ hơn chỉ số cũ!'
    return
  }

  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/dien-nuoc', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        phong_id:       formChiSo.value.phong_id,
        thang_ghi_nhan: formChiSo.value.thang_ghi_nhan,
        loai_chi_so:    formChiSo.value.loai_chi_so,
        chi_so_cu:      chiSoCu,
        chi_so_moi:     chiSoMoi
      })
    })
    const result = await res.json()
    if (result.status === 'success') {
      hienThiModal.value = false
      layDanhSachChiSo()
    } else if (result.errors) {
      // Hiện thị lỗi validation từ Laravel
      loiForm.value = Object.values(result.errors).flat().join(' | ')
    } else {
      loiForm.value = result.message || 'Kiểm tra lại dữ liệu nhập!'
    }
  } catch (error) {
    loiForm.value = 'Không thể kết nối đến máy chủ!'
  }
}

const xoaChiSo = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa bản ghi này?')) return
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/dien-nuoc/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    if ((await res.json()).status === 'success') layDanhSachChiSo()
  } catch (error) { alert('Lỗi kết nối!') }
}

const dinhDangTien = (tien) => new Intl.NumberFormat('vi-VN').format(tien) + ' đ'
const dinhDangThang = (ngay) => {
  const d = new Date(ngay)
  return `Tháng ${d.getMonth() + 1}/${d.getFullYear()}`
}

onMounted(() => {
  layDanhSachChiSo()
  layDanhSachPhong()
})
</script>

<template>
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
      <h5 class="fw-bold text-dark-blue m-0">Chốt Số Điện Nước Hàng Tháng</h5>
      <button @click="moModalThem" class="btn btn-purple fw-bold px-4 text-uppercase">
        + Ghi Chỉ Số Mới
      </button>
    </div>

    <div class="card-body p-0">
      <div v-if="dangTai" class="p-5 text-center text-purple fw-bold">Đang tải dữ liệu...</div>
      <div v-else-if="danhSachChiSo.length === 0" class="p-5 text-center text-muted">Chưa có bản ghi điện nước nào.</div>
      
      <table v-else class="table table-hover align-middle m-0">
        <thead class="table-light text-uppercase small text-muted">
          <tr>
            <th class="ps-4 py-3">Kỳ Ghi</th>
            <th class="py-3">Phòng</th>
            <th class="py-3">Dịch Vụ</th>
            <th class="py-3 text-center">Số Cũ</th>
            <th class="py-3 text-center">Số Mới</th>
            <th class="py-3 text-center">Tiêu Thụ</th>
            <th class="py-3 text-end">Đơn Giá</th>
            <th class="text-end pe-4 py-3">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cs in danhSachChiSo" :key="cs.id">
            <td class="ps-4 fw-bold text-secondary">{{ dinhDangThang(cs.thang_ghi_nhan) }}</td>
            <td><span class="badge bg-purple-light text-purple px-2 py-1 fs-6">Phòng {{ cs.so_phong }}</span></td>
            <td>
              <span v-if="cs.loai_chi_so === 'dien'" class="text-warning fw-bold">⚡ Điện</span>
              <span v-else class="text-info fw-bold">💧 Nước</span>
            </td>
            <td class="text-center">{{ cs.chi_so_cu }}</td>
            <td class="text-center fw-bold">{{ cs.chi_so_moi }}</td>
            <td class="text-center fw-bold text-danger">{{ cs.chi_so_moi - cs.chi_so_cu }}</td>
            <td class="text-end">{{ dinhDangTien(cs.don_gia) }}</td>
            <td class="text-end pe-4">
              <button @click="xoaChiSo(cs.id)" class="btn btn-sm btn-outline-danger fw-bold">Xóa</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="hienThiModal" class="modal-overlay d-flex justify-content-center align-items-center">
      <div class="modal-content bg-white p-4 rounded shadow-lg">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
          <h5 class="fw-bold text-dark-blue m-0">Ghi Chỉ Số Điện Nước</h5>
          <button @click="hienThiModal = false" class="btn btn-link text-danger text-decoration-none fw-bold p-0">[ĐÓNG]</button>
        </div>

        <!-- Hiện thị lỗi rõ ràng trong form -->
        <div v-if="loiForm" class="alert-error px-3 py-2 rounded mb-3">
          ⚠️ {{ loiForm }}
        </div>

        <form @submit.prevent="luuChiSo">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Chọn Phòng (Đang thuê)</label>
              <select v-model.number="formChiSo.phong_id" class="form-select custom-input" required>
                <option :value="null" disabled>-- Chọn phòng --</option>
                <option v-for="p in danhSachPhong" :key="p.id" :value="p.id">Phòng {{ p.so_phong }}</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Ngày Ghi Nhận</label>
              <input v-model="formChiSo.thang_ghi_nhan" type="date" class="form-control custom-input" required>
            </div>

            <div class="col-md-12">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Loại Dịch Vụ</label>
              <select v-model="formChiSo.loai_chi_so" class="form-select custom-input" required>
                <option value="dien">⚡ Điện (Tính theo kWh)</option>
                <option value="nuoc">💧 Nước (Tính theo Khối)</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Chỉ Số Cũ</label>
              <input v-model.number="formChiSo.chi_so_cu" type="number" min="0" step="0.1" class="form-control custom-input" placeholder="0" required>
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Chỉ Số Mới</label>
              <input v-model.number="formChiSo.chi_so_moi" type="number" min="0" step="0.1" class="form-control custom-input" placeholder="0" required>
            </div>
          </div>
          
          <div class="d-flex justify-content-end mt-4 pt-3 border-top gap-2">
            <button type="button" @click="hienThiModal = false" class="btn btn-light fw-bold">Hủy Bỏ</button>
            <button type="submit" class="btn btn-purple fw-bold px-4">Lưu Chỉ Số</button>
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
.bg-purple-light { background-color: #e0f8f4; }
.text-purple { color: #00C4A0; }
.modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(20, 20, 20, 0.72); z-index: 9999; }
.modal-content { width: 90%; max-width: 500px; animation: slideDown 0.3s ease-out; }
.custom-input { border-color: #c2d9de; border-radius: 6px; outline: none; transition: 0.2s; }
.custom-input:focus { border-color: #2E6E7E; box-shadow: 0 0 0 0.25rem rgba(46, 110, 126, 0.18); }
.alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; font-size: 13.5px; }
@keyframes slideDown { 0% { opacity: 0; transform: translateY(-30px); } 100% { opacity: 1; transform: translateY(0); } }
</style>
