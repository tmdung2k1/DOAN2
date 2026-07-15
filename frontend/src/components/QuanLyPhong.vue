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

const danhSachFileChon = ref([])
const xemTruocAnh = ref([])
const danhSachAnhHienCo = ref([])
const danhSachFileThemMoi = ref([])
const xemTruocThemMoi = ref([])
const dangUpload = ref(false)

const layDanhSachLoaiPhong = async () => {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/admin/loai-phong', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await response.json()
    if (result.status === 'success') danhSachLoaiPhong.value = result.data
  } catch (error) { console.error('Lỗi tải danh sách loại phòng:', error) }
}

const layDanhSachPhong = async () => {
  try {
    dangTai.value = true
    const response = await fetch('http://127.0.0.1:8000/api/admin/phong', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await response.json()
    if (result.status === 'success') danhSachPhong.value = result.data
  } catch (error) { console.error('Lỗi tải dữ liệu:', error) }
  finally { dangTai.value = false }
}

const moModalThem = () => {
  laCheDoSua.value = false
  formPhong.value = { id: null, so_phong: '', loai_phong_id: '', dien_tich: '', gia_thue: '', gia_coc: '', trang_thai: 'trong', tang_id: null }
  danhSachFileChon.value = []
  xemTruocAnh.value = []
  hienThiModal.value = true
}

const moModalSua = async (phong) => {
  laCheDoSua.value = true
  formPhong.value = { ...phong }
  danhSachAnhHienCo.value = []
  danhSachFileThemMoi.value = []
  xemTruocThemMoi.value = []
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/phong/${phong.id}/hinh-anh`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    const result = await res.json()
    if (result.status === 'success') danhSachAnhHienCo.value = result.data
  } catch (error) { console.error(error) }
  hienThiModal.value = true
}

const chonNhieuAnh = (event) => {
  const files = Array.from(event.target.files)
  danhSachFileChon.value = [...danhSachFileChon.value, ...files]
  const newPreviews = files.map(f => URL.createObjectURL(f))
  xemTruocAnh.value = [...xemTruocAnh.value, ...newPreviews]
}

const xoaAnhChon = (index) => {
  danhSachFileChon.value.splice(index, 1)
  xemTruocAnh.value.splice(index, 1)
}

const chonAnhThemMoi = (event) => {
  const files = Array.from(event.target.files)
  danhSachFileThemMoi.value = [...danhSachFileThemMoi.value, ...files]
  const newPreviews = files.map(f => URL.createObjectURL(f))
  xemTruocThemMoi.value = [...xemTruocThemMoi.value, ...newPreviews]
}

const xoaAnhThemMoi = (index) => {
  danhSachFileThemMoi.value.splice(index, 1)
  xemTruocThemMoi.value.splice(index, 1)
}

const xoaAnhHienCo = async (anhId, index) => {
  if (!confirm('Bạn có chắc muốn xóa bức ảnh này?')) return
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/hinh-anh/${anhId}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    })
    if ((await res.json()).status === 'success') danhSachAnhHienCo.value.splice(index, 1)
  } catch (error) { hienToast('Lỗi kết nối!', 'error') }
}

const uploadNhieuAnh = async (phongId, danhSachFile) => {
  for (const file of danhSachFile) {
    const formData = new FormData()
    formData.append('hinh_anh', file)
    await fetch(`http://127.0.0.1:8000/api/admin/phong/${phongId}/hinh-anh`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}` },
      body: formData
    })
  }
}

const luuPhong = async () => {
  const url = laCheDoSua.value
    ? `http://127.0.0.1:8000/api/admin/phong/${formPhong.value.id}`
    : 'http://127.0.0.1:8000/api/admin/phong'
  const method = laCheDoSua.value ? 'PUT' : 'POST'

  try {
    dangUpload.value = true
    const response = await fetch(url, {
      method,
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(formPhong.value)
    })
    const result = await response.json()
    if (result.status === 'success') {
      const phongId = laCheDoSua.value ? formPhong.value.id : result.data.id
      const filesToUpload = laCheDoSua.value ? danhSachFileThemMoi.value : danhSachFileChon.value
      if (filesToUpload.length > 0) await uploadNhieuAnh(phongId, filesToUpload)
      hienThiModal.value = false
      layDanhSachPhong()
      hienToast(result.message)
    } else {
      hienToast('Lỗi: ' + (result.message || 'Kiểm tra lại dữ liệu nhập!'), 'error')
    }
  } catch (error) {
    hienToast('Không thể kết nối đến máy chủ!', 'error')
  } finally {
    dangUpload.value = false
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
    } else { hienToast('Xóa thất bại!', 'error') }
  } catch (error) { hienToast('Lỗi kết nối!', 'error') }
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
    if (result.status === 'success') tienIchDuocChon.value = result.data
  } catch (error) { console.error('Lỗi tải tiện ích phòng:', error) }
  hienThiModalTienIch.value = true
}

const luuTienIchPhong = async () => {
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/phong/${phongDangChonTienIch.value.id}/tien-ich`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
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

const hienThiModalChiTiet = ref(false)
const phongChiTiet = ref(null)
const anhChiTiet = ref([])
const dangTaiChiTiet = ref(false)
const anhLightbox = ref(null)

const moModalChiTiet = async (phong) => {
  phongChiTiet.value = phong
  anhChiTiet.value = []
  anhLightbox.value = null
  dangTaiChiTiet.value = true
  hienThiModalChiTiet.value = true
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/phong/${phong.id}/hinh-anh`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    const result = await res.json()
    if (result.status === 'success') anhChiTiet.value = result.data
  } catch (error) { console.error(error) }
  dangTaiChiTiet.value = false
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
                <span v-if="phong.tien_ich && phong.tien_ich.length > 0" v-for="ti in phong.tien_ich" :key="ti.id" class="badge-tien-ich">{{ ti.ten_tien_ich }}</span>
                <span v-else class="text-muted small fst-italic">Chưa có</span>
              </div>
            </td>
            <td>
              <span :class="['badge', dinhDangTrangThai(phong.trang_thai).cls]">{{ dinhDangTrangThai(phong.trang_thai).label }}</span>
            </td>
            <td class="text-end pe-4">
              <button @click="moModalChiTiet(phong)" class="btn btn-sm btn-secondary text-white fw-bold me-2">Chi tiết</button>
              <button @click="moModalTienIch(phong)" class="btn btn-sm btn-info text-white fw-bold me-2">Tiện ích</button>
              <button @click="moModalSua(phong)" class="btn btn-sm btn-outline-primary fw-bold me-2">Sửa</button>
              <button @click="xoaPhong(phong.id, phong.so_phong)" class="btn btn-sm btn-outline-danger fw-bold">Xóa</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL CHI TIẾT PHÒNG -->
    <div v-if="hienThiModalChiTiet" class="modal-overlay d-flex justify-content-center align-items-center">
      <div class="modal-content bg-white rounded shadow-lg" style="max-width: 780px; width: 96%; max-height: 92vh; overflow-y: auto;">

        <!-- Header -->
        <div class="detail-header p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%); border-radius: 12px 12px 0 0;">
          <div>
            <div class="d-flex align-items-center gap-3">
              <span class="detail-room-icon">🏠</span>
              <div>
                <h4 class="fw-bold text-white m-0">Phòng {{ phongChiTiet?.so_phong }}</h4>
                <span class="badge bg-white text-purple mt-1" style="font-size:11px;">{{ layTenLoaiPhong(phongChiTiet?.loai_phong_id) }}</span>
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-3">
            <span :class="['badge', 'badge-lg', dinhDangTrangThai(phongChiTiet?.trang_thai).cls]">{{ dinhDangTrangThai(phongChiTiet?.trang_thai).label }}</span>
            <button @click="hienThiModalChiTiet = false" class="btn btn-sm btn-outline-light fw-bold">✕ Đóng</button>
          </div>
        </div>

        <div class="p-4">
          <!-- Thống kê nhanh -->
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <div class="stat-card d-flex align-items-center gap-3 p-3 rounded" style="background:#f5f3ff; border:1px solid #ede9fe;">
                <span style="font-size:1.8rem;">📐</span>
                <div>
                  <div class="text-muted small">Diện Tích</div>
                  <div class="fw-bold text-dark-blue" style="font-size:1.1rem;">{{ phongChiTiet?.dien_tich }} m²</div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card d-flex align-items-center gap-3 p-3 rounded" style="background:#fff1f2; border:1px solid #fecdd3;">
                <span style="font-size:1.8rem;">💰</span>
                <div>
                  <div class="text-muted small">Giá Thuê</div>
                  <div class="fw-bold text-danger" style="font-size:1.1rem;">{{ dinhDangTien(phongChiTiet?.gia_thue) }}</div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card d-flex align-items-center gap-3 p-3 rounded" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                <span style="font-size:1.8rem;">🔒</span>
                <div>
                  <div class="text-muted small">Tiền Cọc</div>
                  <div class="fw-bold" style="color:#16a34a; font-size:1.1rem;">{{ dinhDangTien(phongChiTiet?.gia_coc) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tiện ích -->
          <div class="mb-4">
            <h6 class="fw-bold text-dark-blue mb-2">🔌 Tiện Ích</h6>
            <div v-if="phongChiTiet?.tien_ich && phongChiTiet.tien_ich.length > 0" class="d-flex flex-wrap gap-2">
              <span v-for="ti in phongChiTiet.tien_ich" :key="ti.id" class="badge-tien-ich" style="font-size:13px; padding: 5px 12px;">{{ ti.ten_tien_ich }}</span>
            </div>
            <p v-else class="text-muted fst-italic small">Chưa có tiện ích nào được gán.</p>
          </div>

          <!-- Hình ảnh -->
          <div>
            <h6 class="fw-bold text-dark-blue mb-3">🖼 Hình Ảnh Phòng</h6>
            <div v-if="dangTaiChiTiet" class="text-center text-purple py-3">Đang tải ảnh...</div>
            <div v-else-if="anhChiTiet.length === 0" class="text-center text-muted py-4 rounded border" style="background:#f9fafb;">
              <span style="font-size:2.5rem;">📷</span><br>
              <span class="small">Chưa có hình ảnh nào cho phòng này.</span>
            </div>
            <div v-else class="row g-2">
              <div v-for="(anh, i) in anhChiTiet" :key="anh.id" class="col-4 col-md-3">
                <div @click="anhLightbox = anh.url_anh" class="img-preview-wrap rounded overflow-hidden border shadow-sm" style="height:110px; cursor:zoom-in;">
                  <img :src="anh.url_anh" style="width:100%; height:100%; object-fit:cover;" />
                  <div class="img-overlay d-flex align-items-center justify-content-center">
                    <span style="font-size:1.5rem; color:white;">🔍</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Nút tác vụ nhanh -->
          <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <button @click="hienThiModalChiTiet = false; moModalTienIch(phongChiTiet)" class="btn btn-info text-white fw-bold">📦 Quản lý Tiện Ích</button>
            <button @click="hienThiModalChiTiet = false; moModalSua(phongChiTiet)" class="btn btn-purple fw-bold">✏️ Chỉnh Sửa Phòng</button>
          </div>
        </div>

        <!-- LIGHTBOX -->
        <div v-if="anhLightbox" @click="anhLightbox = null" class="lightbox-overlay d-flex align-items-center justify-content-center">
          <div @click.stop class="lightbox-inner position-relative">
            <img :src="anhLightbox" class="rounded shadow-lg" style="max-width:90vw; max-height:82vh; object-fit:contain;" />
            <button @click="anhLightbox = null" class="btn btn-danger position-absolute top-0 end-0 m-2 rounded-circle px-2 py-1 fw-bold" style="font-size:14px; z-index:10;">✕</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL THÊM / SỬA PHÒNG -->
    <div v-if="hienThiModal" class="modal-overlay d-flex justify-content-center align-items-center">
      <div class="modal-content bg-white rounded shadow-lg" style="max-width: 720px; width: 96%; max-height: 92vh; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center p-4 pb-3 sticky-top text-white" style="z-index:1; background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%); border-radius: 12px 12px 0 0;">
          <h5 class="fw-bold m-0 text-white">{{ laCheDoSua ? 'Cập Nhật Phòng' : 'Thêm Phòng Mới' }}</h5>
          <button @click="hienThiModal = false" class="btn btn-sm btn-outline-light fw-bold">✕ Đóng</button>
        </div>

        <form @submit.prevent="luuPhong" class="p-4">
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

            <!-- ===== KHU VỰC ẢNH KHI THÊM MỚI ===== -->
            <div v-if="!laCheDoSua" class="col-12">
              <div class="section-anh-header d-flex justify-content-between align-items-center mb-2">
                <label class="form-label small fw-bold text-dark-blue text-uppercase m-0">🖼 Hình Ảnh Phòng</label>
                <span class="badge bg-secondary">{{ xemTruocAnh.length }} ảnh đã chọn</span>
              </div>

              <!-- Dropzone chọn ảnh -->
              <label for="input-anh-them" class="upload-zone d-flex flex-column align-items-center justify-content-center gap-2 rounded border-2 border-dashed p-4 mb-3" style="cursor:pointer; border-color:#c4b5fd; background: #faf5ff;">
                <span style="font-size:2rem;">📁</span>
                <span class="fw-bold text-purple small">Nhấn để chọn ảnh</span>
                <span class="text-muted" style="font-size:11px;">PNG, JPG, WEBP – Tối đa 5MB/ảnh – Có thể chọn nhiều ảnh</span>
                <input type="file" id="input-anh-them" @change="chonNhieuAnh" accept="image/png,image/jpeg,image/jpg,image/webp" multiple class="d-none">
              </label>

              <!-- Grid preview ảnh đã chọn -->
              <div v-if="xemTruocAnh.length > 0" class="row g-2">
                <div v-for="(src, i) in xemTruocAnh" :key="i" class="col-4 col-md-3 position-relative">
                  <div class="img-preview-wrap rounded overflow-hidden border shadow-sm" style="height:110px;">
                    <img :src="src" style="width:100%; height:100%; object-fit:cover;" />
                    <div class="img-overlay d-flex align-items-center justify-content-center">
                      <button type="button" @click="xoaAnhChon(i)" class="btn btn-sm btn-danger rounded-circle px-2 py-1 fw-bold" style="font-size:12px;">✕</button>
                    </div>
                  </div>
                  <span class="d-block text-center text-muted mt-1" style="font-size:10px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Ảnh {{ i + 1 }}</span>
                </div>

                <!-- Ô thêm ảnh nhanh -->
                <div class="col-4 col-md-3">
                  <label for="input-anh-them" class="add-more-btn d-flex flex-column align-items-center justify-content-center border rounded gap-1" style="height:110px; cursor:pointer; border-style:dashed; border-color:#c4b5fd; background:#faf5ff;">
                    <span style="font-size:1.6rem; color:#7c3aed;">＋</span>
                    <span class="small text-purple fw-bold">Thêm ảnh</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- ===== KHU VỰC ẢNH KHI SỬA ===== -->
            <div v-if="laCheDoSua" class="col-12">
              <div class="section-anh-header d-flex justify-content-between align-items-center mb-3">
                <label class="form-label small fw-bold text-dark-blue text-uppercase m-0">🖼 Hình Ảnh Phòng</label>
                <span class="badge bg-secondary">{{ danhSachAnhHienCo.length }} ảnh hiện có</span>
              </div>

              <!-- Ảnh hiện có -->
              <div v-if="danhSachAnhHienCo.length > 0" class="row g-2 mb-3">
                <div v-for="(anh, i) in danhSachAnhHienCo" :key="anh.id" class="col-4 col-md-3 position-relative">
                  <div class="img-preview-wrap rounded overflow-hidden border shadow-sm" style="height:110px;">
                    <img :src="anh.url_anh" style="width:100%; height:100%; object-fit:cover;" />
                    <div class="img-overlay d-flex align-items-center justify-content-center">
                      <button type="button" @click="xoaAnhHienCo(anh.id, i)" class="btn btn-sm btn-danger rounded-circle px-2 py-1 fw-bold" style="font-size:12px;">✕</button>
                    </div>
                  </div>
                  <span class="d-block text-center text-muted mt-1" style="font-size:10px;">Ảnh {{ i + 1 }}</span>
                </div>
              </div>
              <div v-else class="text-center text-muted py-3 rounded border mb-3" style="background:#f9fafb;">
                <span style="font-size:1.5rem;">🖼️</span><br>
                <span class="small">Phòng này chưa có ảnh nào</span>
              </div>

              <!-- Thêm ảnh mới vào phòng đang sửa -->
              <div class="border-top pt-3">
                <p class="small fw-bold text-dark-blue mb-2">Thêm ảnh mới:</p>
                <label for="input-anh-sua" class="upload-zone d-flex flex-column align-items-center justify-content-center gap-2 rounded p-3 mb-2" style="cursor:pointer; border: 2px dashed #c4b5fd; background:#faf5ff;">
                  <span style="font-size:1.5rem;">📁</span>
                  <span class="fw-bold text-purple small">Nhấn để chọn ảnh bổ sung</span>
                  <input type="file" id="input-anh-sua" @change="chonAnhThemMoi" accept="image/png,image/jpeg,image/jpg,image/webp" multiple class="d-none">
                </label>
                <div v-if="xemTruocThemMoi.length > 0" class="row g-2 mt-1">
                  <div v-for="(src, i) in xemTruocThemMoi" :key="'new_'+i" class="col-4 col-md-3 position-relative">
                    <div class="img-preview-wrap rounded overflow-hidden border border-success shadow-sm" style="height:110px;">
                      <img :src="src" style="width:100%; height:100%; object-fit:cover;" />
                      <div class="img-overlay d-flex align-items-center justify-content-center">
                        <button type="button" @click="xoaAnhThemMoi(i)" class="btn btn-sm btn-danger rounded-circle px-2 py-1 fw-bold" style="font-size:12px;">✕</button>
                      </div>
                    </div>
                    <span class="badge bg-success d-block text-center mt-1" style="font-size:9px;">Mới</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-4 pt-3 border-top gap-2">
            <button type="button" @click="hienThiModal = false" class="btn btn-light fw-bold">Hủy Bỏ</button>
            <button type="submit" :disabled="dangUpload" class="btn btn-purple fw-bold px-4">
              <span v-if="dangUpload">⏳ Đang lưu...</span>
              <span v-else>Lưu Thông Tin</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL TIỆN ÍCH -->
    <div v-if="hienThiModalTienIch" class="modal-overlay d-flex justify-content-center align-items-center">
      <div class="modal-content bg-white rounded shadow-lg" style="max-width: 520px; width: 100%;">
        <div class="d-flex justify-content-between align-items-center p-4 text-white" style="background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%); border-radius: 12px 12px 0 0;">
          <h5 class="fw-bold m-0 text-white">
            Tiện Ích – <span class="text-white-50">Phòng {{ phongDangChonTienIch?.so_phong }}</span>
          </h5>
          <button @click="hienThiModalTienIch = false" class="btn btn-sm btn-outline-light fw-bold">✕ Đóng</button>
        </div>

        <div class="p-4">
          <div v-if="danhSachTatCaTienIch.length === 0" class="text-center text-muted py-3">
          Chưa có tiện ích nào trong kho. Vui lòng thêm tiện ích trước.
        </div>

        <form v-else @submit.prevent="luuTienIchPhong">
          <div class="row g-3">
            <div v-for="ti in danhSachTatCaTienIch" :key="ti.id" class="col-md-6">
              <div class="form-check custom-checkbox">
                <input class="form-check-input" type="checkbox" :value="ti.id" :id="'ti_' + ti.id" v-model="tienIchDuocChon">
                <label class="form-check-label fw-bold text-secondary" :for="'ti_' + ti.id">{{ ti.ten_tien_ich }}</label>
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

.img-preview-wrap {
  position: relative;
}
.img-preview-wrap .img-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.45);
  opacity: 0;
  transition: opacity 0.2s;
}
.img-preview-wrap:hover .img-overlay {
  opacity: 1;
}

.upload-zone {
  transition: border-color 0.2s, background 0.2s;
}
.upload-zone:hover {
  border-color: #7c3aed !important;
  background: #f3e8ff !important;
}

.detail-room-icon {
  font-size: 2.2rem;
  filter: drop-shadow(0 2px 6px rgba(0,0,0,0.3));
}

.stat-card {
  transition: transform 0.15s, box-shadow 0.15s;
}
.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.badge-lg {
  font-size: 13px;
  padding: 6px 14px;
}

.lightbox-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.88);
  z-index: 99999;
  animation: fadeIn 0.2s ease;
}
.lightbox-inner img {
  border-radius: 12px;
  box-shadow: 0 24px 60px rgba(0,0,0,0.6);
}

@keyframes fadeIn {
  from { opacity: 0; }
  to   { opacity: 1; }
}

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