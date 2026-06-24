<script setup>
import { ref, onMounted } from 'vue'


const danhSachHopDong = ref([])
const dangTai = ref(true)
const token = localStorage.getItem('admin_token')

const hienThiModal = ref(false)
const formHopDong = ref({
  phong_id: '',
  khach_id: '',
  ngay_bat_dau: '',
  ngay_ket_thuc: '',
  gia_thue_hang_thang: '',
  tien_coc: '',
  ngay_thu_tien_hang_thang: 5
})

// Lấy danh sách Hợp Đồng
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

// Mở Modal Thêm Mới
const moModalThem = () => {
  formHopDong.value = {
    phong_id: '', khach_id: '', ngay_bat_dau: '', ngay_ket_thuc: '', gia_thue_hang_thang: '', tien_coc: '', ngay_thu_tien_hang_thang: 5
  }
  hienThiModal.value = true
}

const luuHopDong = async () => {
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/hop-dong', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(formHopDong.value)
    })
    const result = await res.json()
    if (result.status === 'success') {
      hienThiModal.value = false
      layDanhSachHopDong()
      alert('Tạo hợp đồng thành công!')
    } else {
      alert('Lỗi: Kiểm tra lại dữ liệu nhập!')
    }
  } catch (error) {
    alert('Không thể kết nối đến máy chủ!')
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

// Các hàm tiện ích định dạng
const dinhDangTien = (tien) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(tien)
const dinhDangNgay = (ngay) => new Date(ngay).toLocaleDateString('vi-VN')

onMounted(() => layDanhSachHopDong())
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
              <button v-if="hd.trang_thai === 'hieu_luc'" @click="huyHopDong(hd.id, hd.ma_hop_dong)" class="btn btn-sm btn-outline-danger fw-bold">Chấm dứt</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="hienThiModal" class="modal-overlay d-flex justify-content-center align-items-center">
      <div class="modal-content bg-white p-4 rounded shadow-lg">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
          <h5 class="fw-bold text-dark-blue m-0">Tạo Hợp Đồng Thuê Phòng</h5>
          <button @click="hienThiModal = false" class="btn btn-link text-danger text-decoration-none fw-bold p-0">[ĐÓNG]</button>
        </div>

        <form @submit.prevent="luuHopDong">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Mã Phòng (ID)</label>
              <input v-model="formHopDong.phong_id" type="number" class="form-control custom-input" placeholder="Nhập ID phòng..." required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Mã Khách Hàng (ID)</label>
              <input v-model="formHopDong.khach_id" type="number" class="form-control custom-input" placeholder="Nhập ID khách..." required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Ngày Bắt Đầu</label>
              <input v-model="formHopDong.ngay_bat_dau" type="date" class="form-control custom-input" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Ngày Kết Thúc</label>
              <input v-model="formHopDong.ngay_ket_thuc" type="date" class="form-control custom-input" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Giá Thuê/Tháng</label>
              <input v-model="formHopDong.gia_thue_hang_thang" type="number" class="form-control custom-input" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Tiền Cọc</label>
              <input v-model="formHopDong.tien_coc" type="number" class="form-control custom-input" required>
            </div>
            <div class="col-md-12">
              <label class="form-label small fw-bold text-dark-blue text-uppercase">Ngày thu tiền hàng tháng (VD: mùng 5)</label>
              <input v-model="formHopDong.ngay_thu_tien_hang_thang" type="number" min="1" max="31" class="form-control custom-input" required>
            </div>
          </div>
          
          <div class="d-flex justify-content-end mt-4 pt-3 border-top">
            <button type="button" @click="hienThiModal = false" class="btn btn-light fw-bold me-2">Hủy Bỏ</button>
            <button type="submit" class="btn btn-purple fw-bold px-4">Tạo Hợp Đồng</button>
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
.bg-purple-light { background-color: #f1ebf7; text-color: #663399;}
.text-purple { color: #663399; }
.modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(10, 25, 47, 0.7); z-index: 9999; }
.modal-content { width: 90%; max-width: 600px; animation: slideDown 0.3s ease-out; }
.custom-input { border-color: #e1dbec; border-radius: 6px; outline: none; transition: 0.2s; }
.custom-input:focus { border-color: #663399; box-shadow: 0 0 0 0.25rem rgba(102, 51, 153, 0.15); }
@keyframes slideDown { 0% { opacity: 0; transform: translateY(-30px); } 100% { opacity: 1; transform: translateY(0); } }
</style>