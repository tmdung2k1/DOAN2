<script setup>
import { ref, onMounted } from 'vue'

const danhSachPhong = ref([])
const danhSachKhach = ref([])
const danhSachHopDong = ref([])
const dangTai = ref(true)
const token = localStorage.getItem('admin_token')

const hienThiModal = ref(false)
const loiForm = ref('')
const formHopDong = ref({
  phong_id: '',
  khach_id: '',
  ngay_bat_dau: '',
  ngay_ket_thuc: '',
  gia_thue_hang_thang: '',
  tien_coc: '',
  ngay_thu_tien_hang_thang: 5
})

const layDanhSachHopDong = async () => {
  dangTai.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/hop-dong', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') {
      danhSachHopDong.value = result.data
    }
  } catch (error) {
    console.error('Lỗi tải dữ liệu:', error)
  } finally {
    dangTai.value = false
  }
}

const moModalThem = () => {
  const homNay = new Date().toISOString().split('T')[0]
  formHopDong.value = {
    phong_id: '', khach_id: '', ngay_bat_dau: homNay, ngay_ket_thuc: '', gia_thue_hang_thang: '', tien_coc: '', ngay_thu_tien_hang_thang: 5
  }
  loiForm.value = ''
  hienThiModal.value = true
}

const luuHopDong = async () => {
  loiForm.value = ''
  if (!formHopDong.value.ngay_bat_dau || !formHopDong.value.ngay_ket_thuc) {
    loiForm.value = 'Vui lòng nhập đầy đủ ngày bắt đầu và ngày kết thúc.'
    return
  }
  const batDau = new Date(formHopDong.value.ngay_bat_dau)
  const ketThuc = new Date(formHopDong.value.ngay_ket_thuc)
  const minKetThuc = new Date(batDau)
  minKetThuc.setMonth(minKetThuc.getMonth() + 1)
  if (ketThuc < minKetThuc) {
    loiForm.value = 'Thời hạn hợp đồng tối thiểu phải là 1 tháng.'
    return
  }

  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/hop-dong', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        ...formHopDong.value,
        gia_thue_hang_thang: String(formHopDong.value.gia_thue_hang_thang).replace(/\./g, ''),
        tien_coc: String(formHopDong.value.tien_coc).replace(/\./g, '')
      })
    })
    const result = await res.json()
    if (result.status === 'success') {
      hienThiModal.value = false
      layDanhSachHopDong()
      alert('Tạo hợp đồng thành công!')
    } else if (result.errors) {
      const danhSachLoi = Object.values(result.errors).flat()
      loiForm.value = danhSachLoi.join(' | ')
    } else {
      loiForm.value = result.message || 'Kiểm tra lại dữ liệu nhập!'
    }
  } catch (error) {
    loiForm.value = 'Không thể kết nối đến máy chủ!'
  }
}

const huyHopDong = async (id, maHopDong) => {
  if (!confirm(`Bạn có chắc muốn chấm dứt hợp đồng ${maHopDong}? Phòng sẽ được giải phóng.`)) return
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/hop-dong/${id}/huy`, {
      method: 'PUT',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') layDanhSachHopDong()
  } catch (error) {
    alert('Lỗi kết nối!')
  }
} 
const layDuLieuForm = async () => {
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/hop-dong/du-lieu-form', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    const result = await res.json()
    if (result.status === 'success') {
      danhSachPhong.value = result.phongs
      danhSachKhach.value = result.khachs
    }
  } catch (error) { console.error('Lỗi tải dữ liệu form:', error) }
}

// Format số tiền với dấu chấm phân cách nghìn
const formatSoTien = (value) => {
  const so = String(value).replace(/\D/g, '')
  return so.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}

const onNhapGiaThu = (e) => {
  formHopDong.value.gia_thue_hang_thang = formatSoTien(e.target.value)
}

const onNhapTienCoc = (e) => {
  formHopDong.value.tien_coc = formatSoTien(e.target.value)
}

const tuDongDienGia = () => {
  const phongDuocChon = danhSachPhong.value.find(p => p.id === formHopDong.value.phong_id)
  if (phongDuocChon) {
    formHopDong.value.gia_thue_hang_thang = formatSoTien(parseInt(phongDuocChon.gia_thue))
    if (!formHopDong.value.tien_coc) {
      formHopDong.value.tien_coc = formatSoTien(parseInt(phongDuocChon.gia_thue))
    }
  }
}

const tuDongNgayKetThuc = () => {
  if (formHopDong.value.ngay_bat_dau) {
    const bat_dau = new Date(formHopDong.value.ngay_bat_dau)
    bat_dau.setFullYear(bat_dau.getFullYear() + 1)
    formHopDong.value.ngay_ket_thuc = bat_dau.toISOString().split('T')[0]
  }
}


const minNgayKetThuc = () => {
  if (!formHopDong.value.ngay_bat_dau) return ''
  const bat_dau = new Date(formHopDong.value.ngay_bat_dau)
  bat_dau.setMonth(bat_dau.getMonth() + 1)
  return bat_dau.toISOString().split('T')[0]
}
// Xuất PDF hợp đồng
const taiFilePDF = async (id, maHopDong) => {
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/hop-dong/${id}/pdf`, {
      method: 'GET',
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    if (res.ok) {
      const blob = await res.blob() // Chuyển data thành file nhị phân
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `HopDong_${maHopDong}.pdf`
      document.body.appendChild(a)
      a.click()
      a.remove()
    } else {
      alert('Lỗi: Không thể xuất file PDF!')
    }
  } catch (error) {
    alert('Lỗi kết nối máy chủ!')
  }
}
const dinhDangTien = (tien) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(tien)
const dinhDangNgay = (ngay) => new Date(ngay).toLocaleDateString('vi-VN')

onMounted(() => {
  layDanhSachHopDong()
  layDuLieuForm()
})

</script>

<template>
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
      <h5 class="fw-bold text-dark-blue m-0">Quản Lý Hợp Đồng</h5>
      <button @click="moModalThem" class="btn btn-purple fw-bold px-4 text-uppercase">
        + Tạo Hợp Đồng
      </button>
    </div>

    <div class="card-body p-0">
      <div v-if="dangTai" class="p-5 text-center text-purple fw-bold">Đang tải dữ liệu...</div>
      <div v-else-if="danhSachHopDong.length === 0" class="p-5 text-center text-muted">Chưa có hợp đồng nào.</div>
      
      <table v-else class="table table-hover align-middle m-0">
        <thead class="table-light text-uppercase small text-muted">
          <tr>
            <th class="ps-4 py-3">Mã HĐ</th>
            <th class="py-3">Phòng</th>
            <th class="py-3">Khách Thuê</th>
            <th class="py-3">Thời Hạn</th>
            <th class="py-3">Giá Thuê / Cọc</th>
            <th class="py-3">Trạng Thái</th>
            <th class="text-end pe-4 py-3">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="hd in danhSachHopDong" :key="hd.id">
            <td class="ps-4 fw-bold text-secondary">{{ hd.ma_hop_dong }}</td>
            <td><span class="badge bg-purple-light text-purple px-2 py-1 fs-6">{{ hd.so_phong }}</span></td>
            <td>
              <div class="fw-bold text-dark-blue">{{ hd.ten_khach_hang }}</div>
              <small class="text-muted">{{ hd.so_dien_thoai }}</small>
            </td>
            <td class="small">
              <div><strong>Từ:</strong> {{ dinhDangNgay(hd.ngay_bat_dau) }}</div>
              <div><strong>Đến:</strong> {{ dinhDangNgay(hd.ngay_ket_thuc) }}</div>
            </td>
            <td>
              <div class="fw-bold text-danger">{{ dinhDangTien(hd.gia_thue_hang_thang) }}</div>
              <small class="text-muted">Cọc: {{ dinhDangTien(hd.tien_coc) }}</small>
            </td>
            <td>
              <span v-if="hd.trang_thai === 'hieu_luc'" class="badge bg-success fw-bold">Hiệu Lực</span>
              <span v-else-if="hd.trang_thai === 'het_han'" class="badge bg-warning text-dark fw-bold">Hết Hạn</span>
              <span v-else class="badge bg-danger fw-bold">Đã Hủy</span>
            </td>
            <td class="text-end pe-4">
              <button @click="taiFilePDF(hd.id, hd.ma_hop_dong)" class="btn btn-sm btn-dark-blue text-white fw-bold me-2">Xuất PDF</button>
              
              <button v-if="hd.trang_thai === 'hieu_luc'" @click="huyHopDong(hd.id, hd.ma_hop_dong)" class="btn btn-sm btn-outline-danger fw-bold">Chấm dứt</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="hienThiModal" class="modal-overlay d-flex justify-content-center align-items-center">
      <div class="modal-content bg-white rounded-3 shadow-lg">
        <div class="modal-header-custom d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="fw-bold text-dark-blue m-0">Tạo Hợp Đồng Thuê Phòng</h5>
          <button @click="hienThiModal = false" class="btn-close-custom">✕</button>
        </div>
        <div v-if="loiForm" class="alert-error mx-4 mt-3 px-3 py-2 rounded-2">
          ⚠️ {{ loiForm }}
        </div>

        <form @submit.prevent="luuHopDong" class="px-4 pb-4 pt-3">
          <div class="section-label">Thông tin cơ bản</div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Chọn Phòng</label>
              <select v-model.number="formHopDong.phong_id" @change="tuDongDienGia" class="form-select custom-input" required>
                <option value="" disabled>-- Chọn phòng trống --</option>
                <option v-for="p in danhSachPhong" :key="p.id" :value="p.id">
                  Phòng {{ p.so_phong }} — {{ Number(p.gia_thue).toLocaleString('vi-VN') }}đ/tháng
                </option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Chọn Khách Hàng</label>
              <select v-model.number="formHopDong.khach_id" class="form-select custom-input" required>
                <option value="" disabled>-- Chọn khách hàng --</option>
                <option v-for="k in danhSachKhach" :key="k.id" :value="k.id">{{ k.ho_ten }} — {{ k.so_dien_thoai }}</option>
              </select>
            </div>
          </div>
          <div class="section-label">Thời hạn hợp đồng</div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Ngày Bắt Đầu</label>
              <input v-model="formHopDong.ngay_bat_dau" @change="tuDongNgayKetThuc" type="date" class="form-control custom-input" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Ngày Kết Thúc</label>
              <input v-model="formHopDong.ngay_ket_thuc" type="date" :min="minNgayKetThuc()" class="form-control custom-input" required>
            </div>
          </div>
          <div class="section-label">Thông tin tài chính</div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Giá Thuê/Tháng (VNĐ)</label>
              <input :value="formHopDong.gia_thue_hang_thang" @input="onNhapGiaThu" type="text" inputmode="numeric" class="form-control custom-input" placeholder="VD: 2.500.000" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Tiền Cọc (VNĐ)</label>
              <input :value="formHopDong.tien_coc" @input="onNhapTienCoc" type="text" inputmode="numeric" class="form-control custom-input" placeholder="VD: 2.500.000" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Ngày Thu Tiền Hàng Tháng (mùng 1–31)</label>
              <input v-model="formHopDong.ngay_thu_tien_hang_thang" type="number" min="1" max="31" class="form-control custom-input" required>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-3 pt-3 border-top gap-2">
            <button type="button" @click="hienThiModal = false" class="btn btn-light fw-bold px-4">Hủy Bỏ</button>
            <button type="submit" class="btn btn-purple fw-bold px-4">✔ Tạo Hợp Đồng</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.text-dark-blue  { color: #2E6E7E; }
.btn-purple      { background-color: #2E6E7E; color: #fff; border: none; transition: 0.25s; border-radius: 8px; }
.btn-purple:hover { background-color: #00C4A0; color: #141414; }
.btn-dark-blue   { background-color: #141414; color: #fff; border: none; transition: 0.25s; border-radius: 8px; }
.btn-dark-blue:hover { background-color: #2E6E7E; color: #fff; }
.bg-purple-light { background-color: #e0f8f4; }
.text-purple     { color: #00C4A0; }

.modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(20, 20, 20, 0.75); z-index: 9999; backdrop-filter: blur(2px); }
.modal-content { width: 95%; max-width: 620px; animation: slideDown 0.25s ease-out; max-height: 92vh; overflow-y: auto; }
.modal-header-custom { border-bottom: 2px solid #d6eaed; background: linear-gradient(135deg, #fafafa, #eef6f7); border-radius: 12px 12px 0 0; }
.btn-close-custom { background: none; border: none; font-size: 18px; color: #999; cursor: pointer; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
.btn-close-custom:hover { background: #fee2e2; color: #dc2626; }

.section-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #2E6E7E; background: #eef6f7; padding: 4px 10px; border-radius: 4px; margin-bottom: 10px; display: inline-block; }

.alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; font-size: 13.5px; }

.custom-input { border-color: #c2d9de; border-radius: 8px; transition: 0.2s; font-size: 14px; }
.custom-input:focus { border-color: #2E6E7E; box-shadow: 0 0 0 0.2rem rgba(46, 110, 126, 0.18); }

@keyframes slideDown { 0% { opacity: 0; transform: translateY(-20px) scale(0.98); } 100% { opacity: 1; transform: translateY(0) scale(1); } }
</style>