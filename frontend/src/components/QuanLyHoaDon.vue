<script setup>
import { ref, onMounted, computed } from 'vue'

const danhSachHoaDon = ref([])
const danhSachHopDong = ref([])
const dangTai = ref(true)
const dangLuu = ref(false)
const thongBao = ref({ hien: false, loai: '', noi_dung: '' })
const token = localStorage.getItem('admin_token')

const hienThiModal = ref(false)
const hopDongDuocChon = ref(null)
const formHoaDon = ref({
  hop_dong_id: '',
  thang_thanh_toan: '',
  han_chot: '',
  chi_tiet: []
})

const tenLoaiPhi = {
  tien_phong: { label: '🏠 Tiền phòng',  mau: '#6366f1' },
  tien_dien:  { label: '⚡ Tiền điện',   mau: '#f59e0b' },
  tien_nuoc:  { label: '💧 Tiền nước',   mau: '#0ea5e9' },
  rac:        { label: '🗑️ Tiền rác',    mau: '#84cc16' },
  wifi:       { label: '📶 Tiền wifi',    mau: '#8b5cf6' },
  khac:       { label: '📋 Khác',         mau: '#94a3b8' }
}

const hienThongBao = (loai, noi_dung) => {
  thongBao.value = { hien: true, loai, noi_dung }
  setTimeout(() => { thongBao.value.hien = false }, 3500)
}

const layDanhSachHoaDon = async () => {
  dangTai.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/hoa-don', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') danhSachHoaDon.value = result.data
  } catch (e) { console.error(e) }
  dangTai.value = false
}

const layDuLieuForm = async () => {
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/hoa-don/du-lieu-form', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') danhSachHopDong.value = result.hop_dongs
  } catch (e) { console.error(e) }
}

const moModalThem = () => {
  const homNay = new Date().toISOString().split('T')[0]
  const hanChot = new Date()
  hanChot.setDate(hanChot.getDate() + 7)
  hopDongDuocChon.value = null
  formHoaDon.value = {
    hop_dong_id: '',
    thang_thanh_toan: homNay,
    han_chot: hanChot.toISOString().split('T')[0],
    chi_tiet: [{ loai_phi: 'tien_phong', so_luong: 1, don_gia: 0, thanh_tien: 0 }]
  }
  hienThiModal.value = true
}

const chonHopDong = () => {
  const hd = danhSachHopDong.value.find(h => h.id == formHoaDon.value.hop_dong_id)
  hopDongDuocChon.value = hd || null
  if (hd) {
    const dongPhong = formHoaDon.value.chi_tiet.find(c => c.loai_phi === 'tien_phong')
    if (dongPhong) {
      dongPhong.don_gia = hd.gia_thue_hang_thang
      dongPhong.thanh_tien = dongPhong.so_luong * dongPhong.don_gia
    }
  }
}

const themDong = () => {
  formHoaDon.value.chi_tiet.push({ loai_phi: 'tien_dien', so_luong: 1, don_gia: 0, thanh_tien: 0 })
}

const xoaDong = (i) => {
  if (formHoaDon.value.chi_tiet.length === 1) return
  formHoaDon.value.chi_tiet.splice(i, 1)
}

const tinhTien = (i) => {
  const it = formHoaDon.value.chi_tiet[i]
  it.thanh_tien = Number(it.so_luong) * Number(it.don_gia)
}

const tongTien = computed(() =>
  formHoaDon.value.chi_tiet.reduce((s, it) => s + Number(it.thanh_tien), 0)
)

const luuHoaDon = async () => {
  if (!formHoaDon.value.hop_dong_id) {
    hienThongBao('error', 'Vui lòng chọn phòng / hợp đồng!')
    return
  }
  if (formHoaDon.value.chi_tiet.length === 0) {
    hienThongBao('error', 'Vui lòng thêm ít nhất một khoản thu!')
    return
  }
  dangLuu.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/hoa-don', {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(formHoaDon.value)
    })
    const result = await res.json()
    if (result.status === 'success') {
      hienThiModal.value = false
      layDanhSachHoaDon()
      hienThongBao('success', 'Xuất hóa đơn thành công!')
    } else {
      hienThongBao('error', result.message || 'Có lỗi xảy ra!')
    }
  } catch (e) { hienThongBao('error', 'Không thể kết nối máy chủ!') }
  dangLuu.value = false
}

const xacNhanThuTien = async (id) => {
  if (!confirm('Xác nhận khách đã thanh toán hóa đơn này?')) return
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/hoa-don/${id}/thanh-toan`, {
      method: 'PUT',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const r = await res.json()
    if (r.status === 'success') {
      layDanhSachHoaDon()
      hienThongBao('success', 'Đã ghi nhận thanh toán thành công!')
    }
  } catch (e) { hienThongBao('error', 'Lỗi kết nối!') }
}

// Thống kê nhanh
const tongChuaThu = computed(() => danhSachHoaDon.value.filter(h => h.trang_thai === 'chua_thanh_toan').length)
const tongDaThu   = computed(() => danhSachHoaDon.value.filter(h => h.trang_thai === 'da_thanh_toan').length)
const doanhThuThang = computed(() => danhSachHoaDon.value.filter(h => h.trang_thai === 'da_thanh_toan').reduce((s, h) => s + Number(h.tong_tien), 0))

const fmt = (n) => new Intl.NumberFormat('vi-VN').format(n) + ' đ'
const fmtNgay = (d) => new Date(d).toLocaleDateString('vi-VN')
const fmtThang = (d) => { const x = new Date(d); return `Tháng ${x.getMonth() + 1}/${x.getFullYear()}` }

onMounted(() => { layDanhSachHoaDon(); layDuLieuForm() })
</script>

<template>
  <div class="hoadon-wrap">

    <transition name="toast">
      <div v-if="thongBao.hien" :class="['toast-box', thongBao.loai === 'success' ? 'toast-ok' : 'toast-err']">
        <span>{{ thongBao.loai === 'success' ? '✅' : '❌' }}</span>
        {{ thongBao.noi_dung }}
      </div>
    </transition>

    <div class="hd-header">
      <div>
        <h4 class="hd-title">🧾 Quản Lý Hóa Đơn</h4>
        <p class="hd-sub">Lập và theo dõi thu tiền phòng hàng tháng</p>
      </div>
      <button @click="moModalThem" class="btn-new">
        <span class="btn-icon">＋</span> Lập Hóa Đơn Mới
      </button>
    </div>

    <div class="stat-row">
      <div class="stat-card stat-pending">
        <div class="stat-icon">⏳</div>
        <div>
          <div class="stat-num">{{ tongChuaThu }}</div>
          <div class="stat-lbl">Chưa thu tiền</div>
        </div>
      </div>
      <div class="stat-card stat-done">
        <div class="stat-icon">✅</div>
        <div>
          <div class="stat-num">{{ tongDaThu }}</div>
          <div class="stat-lbl">Đã thu tiền</div>
        </div>
      </div>
      <div class="stat-card stat-revenue">
        <div class="stat-icon">💰</div>
        <div>
          <div class="stat-num stat-money">{{ fmt(doanhThuThang) }}</div>
          <div class="stat-lbl">Đã thu được</div>
        </div>
      </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="hd-table-wrap">
      <div v-if="dangTai" class="hd-loading">
        <div class="spinner"></div> Đang tải dữ liệu...
      </div>

      <div v-else-if="danhSachHoaDon.length === 0" class="p-5 text-center text-muted">Chưa có hóa đơn nào.</div>

      <table v-else class="hd-table">
        <thead>
          <tr>
            <th>Mã HD</th>
            <th>Phòng &amp; Khách thuê</th>
            <th>Kỳ thanh toán</th>
            <th>Tổng tiền</th>
            <th>Hạn chót nộp</th>
            <th>Trạng thái</th>
            <th class="text-right">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="hd in danhSachHoaDon" :key="hd.id" :class="['hd-row', hd.trang_thai]">
            <td>
              <span class="ma-hd">{{ hd.ma_hoa_don }}</span>
            </td>
            <td>
              <div class="phong-name">🏠 Phòng {{ hd.so_phong }}</div>
              <div class="khach-name">👤 {{ hd.ten_khach }}</div>
            </td>
            <td>
              <span class="ky-tt">{{ fmtThang(hd.thang_thanh_toan) }}</span>
            </td>
            <td>
              <span class="tong-tien">{{ fmt(hd.tong_tien) }}</span>
            </td>
            <td>
              <span class="han-chot">{{ fmtNgay(hd.han_chot) }}</span>
            </td>
            <td>
              <span v-if="hd.trang_thai === 'chua_thanh_toan'" class="status-badge badge-pending">Chưa thu</span>
              <span v-else-if="hd.trang_thai === 'da_thanh_toan'" class="status-badge badge-done">Đã thu</span>
              <span v-else class="status-badge badge-overdue">Quá hạn</span>
            </td>
            <td class="text-right">
              <button
                v-if="hd.trang_thai === 'chua_thanh_toan'"
                @click="xacNhanThuTien(hd.id)"
                class="btn-thu"
              >
                Đã nhận tiền
              </button>
              <span v-else class="text-done-lbl">—</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <transition name="modal">
      <div v-if="hienThiModal" class="modal-bg" @click.self="hienThiModal = false">
        <div class="modal-box">
          <div class="modal-hd">
            <div>
              <h5 class="modal-title">🧾 Lập Hóa Đơn Thu Tiền</h5>
            </div>
            <button @click="hienThiModal = false" class="btn-close-modal" title="Đóng">✕</button>
          </div>

          <form @submit.prevent="luuHoaDon" class="modal-body">

            <div class="step-label">① Thông tin cơ bản</div>
            <div class="form-grid-3">
              <div class="form-group">
                <label>Phòng / Hợp đồng <span class="required">*</span></label>
                <select v-model="formHoaDon.hop_dong_id" @change="chonHopDong" class="form-ctrl" required>
                  <option value="" disabled>— Chọn phòng —</option>
                  <option v-for="hd in danhSachHopDong" :key="hd.id" :value="hd.id">
                    🏠 Phòng {{ hd.so_phong }}  |  {{ hd.ho_ten }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Kỳ thanh toán <span class="required">*</span></label>
                <input v-model="formHoaDon.thang_thanh_toan" type="date" class="form-ctrl" required>
              </div>
              <div class="form-group">
                <label>Hạn chót nộp tiền <span class="required">*</span></label>
                <input v-model="formHoaDon.han_chot" type="date" class="form-ctrl" required>
              </div>
            </div>

            <div v-if="hopDongDuocChon" class="hop-dong-info">
              <span>🏠 <strong>Phòng {{ hopDongDuocChon.so_phong }}</strong></span>
              <span>👤 {{ hopDongDuocChon.ho_ten }}</span>
              <span>💵 Giá thuê: <strong>{{ fmt(hopDongDuocChon.gia_thue_hang_thang) }}</strong>/tháng</span>
            </div>
            <div class="step-label">② Chi tiết các khoản thu</div>

            <div class="chi-tiet-table">
              <div class="ct-header">
                <div class="ct-col-type">Loại khoản thu</div>
                <div class="ct-col-sl">Số lượng</div>
                <div class="ct-col-dg">Đơn giá (đ)</div>
                <div class="ct-col-tt">Thành tiền</div>
                <div class="ct-col-del"></div>
              </div>

              <div
                v-for="(item, i) in formHoaDon.chi_tiet"
                :key="i"
                class="ct-row"
              >
                <div class="ct-col-type">
                  <select v-model="item.loai_phi" class="form-ctrl-sm">
                    <option value="tien_phong">🏠 Tiền phòng</option>
                    <option value="tien_dien">⚡ Tiền điện</option>
                    <option value="tien_nuoc">💧 Tiền nước</option>
                    <option value="rac">🗑️ Tiền rác</option>
                    <option value="wifi">📶 Tiền wifi</option>
                    <option value="khac">📋 Khác</option>
                  </select>
                </div>
                <div class="ct-col-sl">
                  <input
                    v-model.number="item.so_luong"
                    @input="tinhTien(i)"
                    type="number" min="0" step="0.1"
                    class="form-ctrl-sm text-center"
                    placeholder="1"
                    required
                  >
                </div>
                <div class="ct-col-dg">
                  <input
                    v-model.number="item.don_gia"
                    @input="tinhTien(i)"
                    type="number" min="0"
                    class="form-ctrl-sm"
                    placeholder="0"
                    required
                  >
                </div>
                <div class="ct-col-tt">
                  <span class="tt-val">{{ fmt(item.thanh_tien) }}</span>
                </div>
                <div class="ct-col-del">
                  <button
                    type="button"
                    @click="xoaDong(i)"
                    class="btn-del-row"
                    :disabled="formHoaDon.chi_tiet.length === 1"
                    title="Xóa dòng này"
                  >✕</button>
                </div>
              </div>
              <div class="ct-add-row">
                <button type="button" @click="themDong" class="btn-add-row">
                  ＋ Thêm khoản thu
                </button>
              </div>

              <div class="ct-total">
                <span>TỔNG CỘNG</span>
                <span class="total-val">{{ fmt(tongTien) }}</span>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" @click="hienThiModal = false" class="btn-cancel">Hủy bỏ</button>
              <button type="submit" class="btn-submit" :disabled="dangLuu">
                <span v-if="dangLuu">⏳ Đang lưu...</span>
                <span v-else>📄 Xuất Hóa Đơn</span>
              </button>
            </div>

          </form>
        </div>
      </div>
    </transition>

  </div>
</template>

<style scoped>
.hoadon-wrap { font-family: 'Inter', 'Segoe UI', sans-serif; padding: 0; }

.hd-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 24px 28px 16px;
  background: #fff; border-bottom: 1px solid #f1f5f9;
}
.hd-title { font-size: 20px; font-weight: 800; color: #1e293b; margin: 0; }
.hd-sub   { font-size: 13px; color: #94a3b8; margin: 2px 0 0; }

.btn-new {
  display: flex; align-items: center; gap: 8px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; border: none; border-radius: 10px;
  padding: 10px 22px; font-size: 14px; font-weight: 700;
  cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(99,102,241,.35);
}
.btn-new:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(99,102,241,.45); }
.btn-icon { font-size: 18px; font-weight: 400; }

.stat-row { display: flex; gap: 16px; padding: 20px 28px; background: #f8fafc; }
.stat-card {
  flex: 1; display: flex; align-items: center; gap: 16px;
  background: #fff; border-radius: 12px; padding: 16px 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,.06); border-left: 4px solid;
}
.stat-pending { border-color: #f59e0b; }
.stat-done    { border-color: #10b981; }
.stat-revenue { border-color: #6366f1; }
.stat-icon { font-size: 28px; }
.stat-num  { font-size: 24px; font-weight: 800; color: #1e293b; line-height: 1.1; }
.stat-money { font-size: 16px; }
.stat-lbl  { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }

.hd-table-wrap { padding: 0 0 24px; }
.hd-table { width: 100%; border-collapse: collapse; }
.hd-table thead th {
  background: #f8fafc; padding: 12px 16px;
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  color: #64748b; letter-spacing: .6px;
  border-bottom: 2px solid #e2e8f0;
}
.hd-table thead th:first-child { padding-left: 28px; }
.hd-table thead th.text-right  { text-align: right; padding-right: 28px; }

.hd-row td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.hd-row td:first-child { padding-left: 28px; }
.hd-row td.text-right  { text-align: right; padding-right: 28px; }
.hd-row:hover { background: #fafafa; }

.ma-hd      { font-size: 12px; font-weight: 700; color: #94a3b8; font-family: monospace; }
.phong-name { font-weight: 700; color: #1e293b; font-size: 14px; }
.khach-name { font-size: 12px; color: #64748b; margin-top: 2px; }
.ky-tt      { font-size: 13px; color: #475569; font-weight: 600; }
.tong-tien  { font-weight: 800; color: #dc2626; font-size: 15px; }
.han-chot   { font-size: 13px; color: #475569; }

/* Trạng thái badge */
.status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
.badge-pending { background: #fef3c7; color: #92400e; }
.badge-done    { background: #d1fae5; color: #065f46; }
.badge-overdue { background: #fee2e2; color: #991b1b; }

.btn-thu {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff; border: none; border-radius: 8px;
  padding: 7px 14px; font-size: 13px; font-weight: 700;
  cursor: pointer; transition: all 0.2s; white-space: nowrap;
}
.btn-thu:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(16,185,129,.35); }
.text-done-lbl { color: #cbd5e1; font-size: 13px; }


.hd-loading { padding: 60px; text-align: center; color: #6366f1; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 12px; }
.spinner { width: 24px; height: 24px; border: 3px solid #e0e7ff; border-top-color: #6366f1; border-radius: 50%; animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.hd-empty { padding: 60px; text-align: center; color: #94a3b8; }
.empty-icon { font-size: 48px; margin-bottom: 12px; }
.hd-empty p { font-size: 15px; margin-bottom: 16px; }
.btn-new-sm {
  background: #6366f1; color: #fff; border: none; border-radius: 8px;
  padding: 9px 22px; font-weight: 700; cursor: pointer;
}

.modal-bg {
  position: fixed; inset: 0; background: rgba(15,23,42,.6);
  z-index: 9999; display: flex; justify-content: center; align-items: center;
  backdrop-filter: blur(4px);
}
.modal-box {
  background: #fff; border-radius: 16px;
  width: 95%; max-width: 740px; max-height: 92vh;
  overflow-y: auto; box-shadow: 0 24px 60px rgba(0,0,0,.25);
}

.modal-hd {
  display: flex; justify-content: space-between; align-items: flex-start;
  padding: 24px 28px 20px; border-bottom: 1px solid #f1f5f9;
  position: sticky; top: 0; background: #fff; z-index: 1;
  border-radius: 16px 16px 0 0;
}
.modal-title { font-size: 18px; font-weight: 800; color: #1e293b; margin: 0; }
.modal-sub   { font-size: 13px; color: #94a3b8; margin: 4px 0 0; }

.btn-close-modal {
  background: #f1f5f9; border: none; width: 36px; height: 36px;
  border-radius: 50%; font-size: 16px; color: #64748b;
  cursor: pointer; transition: all .2s; flex-shrink: 0;
}
.btn-close-modal:hover { background: #fee2e2; color: #dc2626; }

.modal-body { padding: 24px 28px; }

.step-label {
  font-size: 13px; font-weight: 700; color: #6366f1;
  text-transform: uppercase; letter-spacing: .8px;
  margin-bottom: 12px; margin-top: 4px;
}

.form-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 14px; }
.form-group label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .5px; }
.required { color: #ef4444; }

.form-ctrl {
  width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0;
  border-radius: 8px; font-size: 14px; color: #1e293b;
  transition: border-color .2s; box-sizing: border-box;
}
.form-ctrl:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }

.hop-dong-info {
  background: #eff6ff; border: 1px solid #bfdbfe;
  border-radius: 10px; padding: 12px 16px;
  display: flex; gap: 20px; flex-wrap: wrap;
  font-size: 13px; color: #1e40af;
  margin-bottom: 20px;
}

.chi-tiet-table {
  border: 1.5px solid #e2e8f0; border-radius: 12px;
  overflow: hidden; margin-bottom: 20px;
}
.ct-header {
  display: grid;
  grid-template-columns: 2fr 1fr 1.5fr 1.5fr 40px;
  background: #f8fafc; padding: 10px 14px;
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  color: #64748b; letter-spacing: .5px; gap: 10px;
}
.ct-row {
  display: grid;
  grid-template-columns: 2fr 1fr 1.5fr 1.5fr 40px;
  padding: 10px 14px; gap: 10px; align-items: center;
  border-top: 1px solid #f1f5f9; transition: background .15s;
}
.ct-row:hover { background: #fafbff; }

.ct-col-type, .ct-col-sl, .ct-col-dg, .ct-col-tt, .ct-col-del { display: flex; align-items: center; }
.ct-col-tt { font-weight: 700; color: #dc2626; font-size: 14px; }

.form-ctrl-sm {
  width: 100%; padding: 7px 10px; border: 1.5px solid #e2e8f0;
  border-radius: 7px; font-size: 13px; color: #1e293b; box-sizing: border-box;
}
.form-ctrl-sm:focus { outline: none; border-color: #6366f1; }
.form-ctrl-sm.text-center { text-align: center; }

.tt-val { font-weight: 700; color: #dc2626; font-size: 14px; white-space: nowrap; }

.btn-del-row {
  width: 28px; height: 28px; border: none; border-radius: 6px;
  background: #fee2e2; color: #dc2626; font-size: 13px;
  cursor: pointer; transition: all .2s;
}
.btn-del-row:hover:not(:disabled) { background: #dc2626; color: #fff; }
.btn-del-row:disabled { opacity: .3; cursor: not-allowed; }

.ct-add-row { padding: 12px 14px; border-top: 1px dashed #e2e8f0; }
.btn-add-row {
  background: none; border: 1.5px dashed #6366f1; color: #6366f1;
  border-radius: 8px; padding: 7px 18px; font-size: 13px; font-weight: 700;
  cursor: pointer; transition: all .2s;
}
.btn-add-row:hover { background: #eff0ff; }

.ct-total {
  display: flex; justify-content: space-between; align-items: center;
  padding: 14px 20px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff;
  font-size: 14px; font-weight: 700; letter-spacing: .5px; border-radius: 0 0 10px 10px;
}
.total-val { font-size: 20px; font-weight: 800; color: #fff; text-shadow: 0 1px 4px rgba(0,0,0,.2); }

.modal-footer {
  display: flex; justify-content: flex-end; gap: 12px;
  padding-top: 20px; border-top: 1px solid #f1f5f9;
}
.btn-cancel {
  background: #f1f5f9; color: #64748b; border: none;
  border-radius: 8px; padding: 10px 22px; font-weight: 700;
  font-size: 14px; cursor: pointer; transition: .2s;
}
.btn-cancel:hover { background: #e2e8f0; }
.btn-submit {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff; border: none; border-radius: 8px;
  padding: 10px 28px; font-weight: 700; font-size: 14px;
  cursor: pointer; transition: .2s; box-shadow: 0 4px 12px rgba(99,102,241,.35);
}
.btn-submit:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(99,102,241,.45); }
.btn-submit:disabled { opacity: .6; cursor: not-allowed; }

.toast-box {
  position: fixed; top: 24px; right: 24px; z-index: 99999;
  padding: 14px 22px; border-radius: 12px; font-size: 14px; font-weight: 700;
  display: flex; align-items: center; gap: 10px;
  box-shadow: 0 8px 24px rgba(0,0,0,.15);
}
.toast-ok  { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
.toast-err { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.toast-enter-active, .toast-leave-active { transition: all .35s ease; }
.toast-enter-from { opacity: 0; transform: translateX(40px); }
.toast-leave-to   { opacity: 0; transform: translateX(40px); }

.modal-enter-active, .modal-leave-active { transition: all .25s ease; }
.modal-enter-from { opacity: 0; }
.modal-leave-to   { opacity: 0; }
.modal-enter-from .modal-box { transform: translateY(-20px) scale(.97); }
.modal-leave-to   .modal-box { transform: translateY(-20px) scale(.97); }
</style>