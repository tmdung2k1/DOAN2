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
  dien_chi_so_cu: '',
  dien_chi_so_moi: '',
  nuoc_chi_so_cu: '',
  nuoc_chi_so_moi: ''
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
  formChiSo.value = {
    phong_id: null,
    thang_ghi_nhan: homNay,
    dien_chi_so_cu: '',
    dien_chi_so_moi: '',
    nuoc_chi_so_cu: '',
    nuoc_chi_so_moi: ''
  }
  loiForm.value = ''
  hienThiModal.value = true
}

const luuMotChiSo = async (loai, chiSoCu, chiSoMoi) => {
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
      loai_chi_so:    loai,
      chi_so_cu:      chiSoCu,
      chi_so_moi:     chiSoMoi
    })
  })
  return await res.json()
}

const luuChiSo = async () => {
  loiForm.value = ''

  if (!formChiSo.value.phong_id) {
    loiForm.value = 'Vui lòng chọn phòng.'
    return
  }

  const dienCu  = formChiSo.value.dien_chi_so_cu  !== '' ? parseFloat(formChiSo.value.dien_chi_so_cu)  : null
  const dienMoi = formChiSo.value.dien_chi_so_moi !== '' ? parseFloat(formChiSo.value.dien_chi_so_moi) : null
  const nuocCu  = formChiSo.value.nuoc_chi_so_cu  !== '' ? parseFloat(formChiSo.value.nuoc_chi_so_cu)  : null
  const nuocMoi = formChiSo.value.nuoc_chi_so_moi !== '' ? parseFloat(formChiSo.value.nuoc_chi_so_moi) : null

  const coDien  = dienCu !== null && dienMoi !== null
  const coNuoc  = nuocCu !== null && nuocMoi !== null
  if (!coDien && !coNuoc) {
    loiForm.value = 'Vui lòng nhập ít nhất chỉ số Điện hoặc Nước.'
    return
  }

  if (coDien) {
    if (isNaN(dienCu) || isNaN(dienMoi)) { loiForm.value = 'Chỉ số điện không hợp lệ.'; return }
    if (dienMoi < dienCu) { loiForm.value = 'Chỉ số điện mới không được nhỏ hơn chỉ số cũ!'; return }
  }
  if (coNuoc) {
    if (isNaN(nuocCu) || isNaN(nuocMoi)) { loiForm.value = 'Chỉ số nước không hợp lệ.'; return }
    if (nuocMoi < nuocCu) { loiForm.value = 'Chỉ số nước mới không được nhỏ hơn chỉ số cũ!'; return }
  }

  try {
    const results = []
    if (coDien) results.push(await luuMotChiSo('dien', dienCu, dienMoi))
    if (coNuoc) results.push(await luuMotChiSo('nuoc', nuocCu, nuocMoi))

    const loi = results.find(r => r.status !== 'success')
    if (loi) {
      loiForm.value = loi.message || 'Có lỗi xảy ra khi lưu!'
    } else {
      hienThiModal.value = false
      layDanhSachChiSo()
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
      <button @click="moModalThem" class="btn btn-teal fw-bold px-4 text-uppercase">
        + Ghi Chỉ Số Mới
      </button>
    </div>

    <div class="card-body p-0">
      <div v-if="dangTai" class="p-5 text-center text-teal fw-bold">Đang tải dữ liệu...</div>
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
            <td><span class="badge bg-teal-light text-teal px-2 py-1 fs-6">Phòng {{ cs.so_phong }}</span></td>
            <td>
              <span v-if="cs.loai_chi_so === 'dien'" class="service-badge service-dien">⚡ Điện</span>
              <span v-else class="service-badge service-nuoc">💧 Nước</span>
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
      <div class="modal-content bg-white rounded shadow-lg" style="max-width: 650px; width: 95%;">
        <div class="d-flex justify-content-between align-items-center p-4 text-white" style="background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%); border-radius: 12px 12px 0 0;">
          <h5 class="fw-bold m-0 text-white">Ghi Chỉ Số Điện Nước</h5>
          <button @click="hienThiModal = false" class="btn btn-sm btn-outline-light fw-bold">✕ Đóng</button>
        </div>

        <div class="p-4">
          <div v-if="loiForm" class="alert-error px-3 py-2 rounded mb-3">
            ⚠️ {{ loiForm }}
          </div>

          <form @submit.prevent="luuChiSo">
          <div class="row g-3 mb-4">
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
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="service-box service-box-dien p-3 rounded">
                <div class="service-box-title mb-3">
                  <span class="service-icon">⚡</span>
                  <span class="fw-bold">CHỈ SỐ ĐIỆN</span>
                  <small class="text-muted ms-1">(kWh)</small>
                </div>
                <div class="mb-2">
                  <label class="form-label small fw-bold text-dark-blue text-uppercase mb-1">Số Cũ</label>
                  <input
                    v-model.number="formChiSo.dien_chi_so_cu"
                    type="number" min="0" step="0.1"
                    class="form-control custom-input"
                    placeholder="0"
                  >
                </div>
                <div>
                  <label class="form-label small fw-bold text-dark-blue text-uppercase mb-1">Số Mới</label>
                  <input
                    v-model.number="formChiSo.dien_chi_so_moi"
                    type="number" min="0" step="0.1"
                    class="form-control custom-input"
                    placeholder="0"
                  >
                </div>
                <div v-if="formChiSo.dien_chi_so_moi !== '' && formChiSo.dien_chi_so_cu !== ''" class="mt-2 text-center">
                  <span class="badge bg-warning text-dark fw-bold px-3">
                    Tiêu thụ: {{ (formChiSo.dien_chi_so_moi - formChiSo.dien_chi_so_cu).toFixed(1) }} kWh
                  </span>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="service-box service-box-nuoc p-3 rounded">
                <div class="service-box-title mb-3">
                  <span class="service-icon">💧</span>
                  <span class="fw-bold">CHỈ SỐ NƯỚC</span>
                  <small class="text-muted ms-1">(Khối)</small>
                </div>
                <div class="mb-2">
                  <label class="form-label small fw-bold text-dark-blue text-uppercase mb-1">Số Cũ</label>
                  <input
                    v-model.number="formChiSo.nuoc_chi_so_cu"
                    type="number" min="0" step="0.1"
                    class="form-control custom-input"
                    placeholder="0"
                  >
                </div>
                <div>
                  <label class="form-label small fw-bold text-dark-blue text-uppercase mb-1">Số Mới</label>
                  <input
                    v-model.number="formChiSo.nuoc_chi_so_moi"
                    type="number" min="0" step="0.1"
                    class="form-control custom-input"
                    placeholder="0"
                  >
                </div>
                <div v-if="formChiSo.nuoc_chi_so_moi !== '' && formChiSo.nuoc_chi_so_cu !== ''" class="mt-2 text-center">
                  <span class="badge bg-info text-dark fw-bold px-3">
                    Tiêu thụ: {{ (formChiSo.nuoc_chi_so_moi - formChiSo.nuoc_chi_so_cu).toFixed(1) }} khối
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-4 pt-3 border-top gap-2">
            <button type="button" @click="hienThiModal = false" class="btn btn-light fw-bold">Hủy Bỏ</button>
            <button type="submit" class="btn btn-teal fw-bold px-4">Lưu Chỉ Số</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</template>

<style scoped>
@import "../assets/css/quan-ly-dien-nuoc.css";
</style>