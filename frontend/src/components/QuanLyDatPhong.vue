<script setup>
import { ref, onMounted, computed } from 'vue'

const danhSachYeuCau = ref([])
const dangTai = ref(true)
const token = localStorage.getItem('admin_token')
const boLocTrangThai = ref('tat_ca')

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

const danhSachDaLoc = computed(() => {
  if (boLocTrangThai.value === 'tat_ca') return danhSachYeuCau.value
  return danhSachYeuCau.value.filter(yc => yc.trang_thai === boLocTrangThai.value)
})

const xuLyHanhDong = async (id, hanhDong) => {
  if (!confirm(`Bạn có chắc chắn muốn thực hiện thao tác này?`)) return
  
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/dat-phong/${id}/${hanhDong}`, {
      method: 'PUT',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') {
      alert(result.message)
      layDanhSachYeuCau()
    }
  } catch (error) {
    alert('Lỗi kết nối máy chủ!')
  }
}

const dinhDangNgay = (dateStr) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('vi-VN')
}

const dinhDangThoiGian = (dateStr) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleString('vi-VN')
}

const demTheoTrangThai = (trangThai) => {
  if (trangThai === 'tat_ca') return danhSachYeuCau.value.length
  return danhSachYeuCau.value.filter(yc => yc.trang_thai === trangThai).length
}

onMounted(() => layDanhSachYeuCau())
</script>

<template>
  <div class="ql-dat-phong">
    <div class="ql-dp-header">
      <div>
        <h5 class="ql-dp-title">Quản Lý Yêu Cầu Đặt Phòng</h5>
        <p class="ql-dp-subtitle">Duyệt và quản lý các yêu cầu đặt phòng từ khách hàng</p>
      </div>
      <button class="ql-dp-refresh-btn" @click="layDanhSachYeuCau" :disabled="dangTai">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
          <polyline points="23 4 23 10 17 10" />
          <polyline points="1 20 1 14 7 14" />
          <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15" />
        </svg>
        Làm mới
      </button>
    </div>

    <!-- Bộ lọc trạng thái -->
    <div class="ql-dp-filters">
      <button 
        :class="['ql-dp-filter-btn', { active: boLocTrangThai === 'tat_ca' }]"
        @click="boLocTrangThai = 'tat_ca'"
      >
        Tất cả <span class="ql-dp-badge">{{ demTheoTrangThai('tat_ca') }}</span>
      </button>
      <button 
        :class="['ql-dp-filter-btn cho-xac-nhan', { active: boLocTrangThai === 'cho_xac_nhan' }]"
        @click="boLocTrangThai = 'cho_xac_nhan'"
      >
        Chờ duyệt <span class="ql-dp-badge warning">{{ demTheoTrangThai('cho_xac_nhan') }}</span>
      </button>
      <button 
        :class="['ql-dp-filter-btn da-coc', { active: boLocTrangThai === 'da_coc' }]"
        @click="boLocTrangThai = 'da_coc'"
      >
        Đã cọc <span class="ql-dp-badge success">{{ demTheoTrangThai('da_coc') }}</span>
      </button>
      <button 
        :class="['ql-dp-filter-btn huy', { active: boLocTrangThai === 'huy' }]"
        @click="boLocTrangThai = 'huy'"
      >
        Đã hủy <span class="ql-dp-badge danger">{{ demTheoTrangThai('huy') }}</span>
      </button>
    </div>

    <!-- Nội dung -->
    <div class="ql-dp-body">
      <div v-if="dangTai" class="ql-dp-loading">
        <div class="ql-dp-spinner"></div>
        Đang tải danh sách yêu cầu...
      </div>
      <div v-else-if="danhSachDaLoc.length === 0" class="ql-dp-empty">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
          <line x1="16" y1="2" x2="16" y2="6" />
          <line x1="8" y1="2" x2="8" y2="6" />
          <line x1="3" y1="10" x2="21" y2="10" />
        </svg>
        <p>Chưa có yêu cầu đặt phòng nào.</p>
      </div>

      <div v-else class="ql-dp-table-wrap">
        <table class="ql-dp-table">
          <thead>
            <tr>
              <th>Mã ĐP</th>
              <th>Khách Hàng</th>
              <th>Phòng</th>
              <th>Ngày Đến</th>
              <th>Ghi Chú</th>
              <th>Thời Gian Gửi</th>
              <th>Trạng Thái</th>
              <th class="text-end">Thao Tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="yc in danhSachDaLoc" :key="yc.id">
              <td>
                <span class="ql-dp-ma">{{ yc.ma_dat_phong }}</span>
              </td>
              <td>
                <div class="ql-dp-khach">
                  <div class="ql-dp-khach-ten">{{ yc.ho_ten }}</div>
                  <div class="ql-dp-khach-sdt">{{ yc.so_dien_thoai }}</div>
                  <div v-if="yc.email" class="ql-dp-khach-email">{{ yc.email }}</div>
                </div>
              </td>
              <td>
                <span class="ql-dp-phong-badge">Phòng {{ yc.so_phong }}</span>
              </td>
              <td>{{ dinhDangNgay(yc.ngay_du_kien_den) }}</td>
              <td>
                <span class="ql-dp-ghi-chu" :title="yc.ghi_chu">{{ yc.ghi_chu || '—' }}</span>
              </td>
              <td class="ql-dp-thoigian">{{ dinhDangThoiGian(yc.created_at) }}</td>
              <td>
                <span v-if="yc.trang_thai === 'cho_xac_nhan'" class="ql-dp-status warning">Chờ Duyệt</span>
                <span v-else-if="yc.trang_thai === 'da_coc'" class="ql-dp-status success">Đã Cọc</span>
                <span v-else-if="yc.trang_thai === 'da_nhan_phong'" class="ql-dp-status primary">Đã Nhận Phòng</span>
                <span v-else-if="yc.trang_thai === 'huy'" class="ql-dp-status danger">Đã Hủy</span>
              </td>
              <td class="text-end">
                <div v-if="yc.trang_thai === 'cho_xac_nhan'" class="ql-dp-actions">
                  <button @click="xuLyHanhDong(yc.id, 'duyet')" class="ql-dp-btn-duyet" title="Duyệt yêu cầu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Duyệt
                  </button>
                  <button @click="xuLyHanhDong(yc.id, 'tu-choi')" class="ql-dp-btn-huy" title="Từ chối">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                      <line x1="18" y1="6" x2="6" y2="18" />
                      <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                    Từ chối
                  </button>
                </div>
                <span v-else class="ql-dp-da-xu-ly">Đã xử lý</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import "../assets/css/quan-ly-dat-phong.css";
</style>