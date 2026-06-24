<script setup>
import { ref, onMounted } from 'vue'

const danhSachYeuCau = ref([])
const dangTai = ref(true)
const token = localStorage.getItem('admin_token')

const layDanhSachYeuCau = async () => {
  dangTai.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/dat-phong', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') {
      danhSachYeuCau.value = result.data
    }
  } catch (error) {
    console.error(error)
  } finally {
    dangTai.value = false
  }
}

const xuLyHànhDong = async (id, hanhDong) => {
  if (!confirm(`Bạn có chắc chắn muốn thực hiện thao tác này?`)) return
  
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/dat-phong/${id}/${hanhDong}`, {
      method: 'PUT',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') {
      alert(result.message)
      layDanhSachYeuCau() // Tải lại bảng dữ liệu
    }
  } catch (error) {
    alert('Lỗi kết nối máy chủ!')
  }
}

onMounted(() => layDanhSachYeuCau())
</script>

<template>
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-4 border-bottom">
      <h5 class="fw-bold text-dark-blue m-0">Quản Lý Yêu Cầu Đặt Phòng</h5>
    </div>

    <div class="card-body p-0">
      <div v-if="dangTai" class="p-5 text-center text-purple fw-bold">Đang tải danh sách yêu cầu...</div>
      <div v-else-if="danhSachYeuCau.length === 0" class="p-5 text-center text-muted">Chưa có yêu cầu đặt phòng nào.</div>
      
      <table v-else class="table table-hover align-middle m-0">
        <thead class="table-light text-uppercase small text-muted">
          <tr>
            <th class="ps-4 py-3">Khách Hàng</th>
            <th class="py-3">Số Điện Thoại</th>
            <th class="py-3">Phòng Đăng Ký</th>
            <th class="py-3">Trạng Thái</th>
            <th class="text-end pe-4 py-3">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="yc in danhSachYeuCau" :key="yc.id">
            <td class="ps-4">
              <div class="fw-bold text-dark-blue">{{ yc.ho_ten }}</div>
              <small class="text-muted">{{ yc.email }}</small>
            </td>
            <td class="fw-bold text-secondary">{{ yc.so_dien_thoai }}</td>
            <td><span class="badge bg-purple-light text-purple px-2 py-1 fs-6">Phòng {{ yc.so_phong }}</span></td>
            <td>
              <span v-if="yc.trang_thai === 'cho_xac_nhan'" class="badge bg-warning text-dark fw-bold">Chờ Xác Nhận</span>
              <span v-else-if="yc.trang_thai === 'da_coc'" class="badge bg-success fw-bold">Đã Cọc</span>
              <span v-else-if="yc.trang_thai === 'da_nhan_phong'" class="badge bg-primary fw-bold">Đã Nhận Phòng</span>
              <span v-else-if="yc.trang_thai === 'huy'" class="badge bg-danger fw-bold">Đã Hủy</span>
            </td>
            <td class="text-end pe-4">
              <div v-if="yc.trang_thai === 'cho_xac_nhan'">
                <button @click="xuLyHànhDong(yc.id, 'duyet')" class="btn btn-sm btn-success fw-bold me-2 px-3">Duyệt</button>
                <button @click="xuLyHànhDong(yc.id, 'tu-choi')" class="btn btn-sm btn-outline-danger fw-bold px-2">Từ chối</button>
              </div>
              <div v-else class="text-muted small italic">Đã xử lý</div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.text-dark-blue { color: #0A192F; }
.text-purple { color: #663399; }
.bg-purple-light { background-color: #f1ebf7; }
</style>