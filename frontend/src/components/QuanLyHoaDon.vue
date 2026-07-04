<script setup>
import { ref, onMounted, computed } from 'vue'
import { inHoaDon as inHoaDonUtil } from '../utils/inHoaDon.js'

const danhSachHoaDon = ref([])
const danhSachHopDong = ref([])
const dangTai = ref(true)
const dangLuu = ref(false)
const thongBao = ref({ hien: false, loai: '', noi_dung: '' })
const token = localStorage.getItem('admin_token')

const hienThiModalChiTiet = ref(false)
const chiTietHoaDonDangXem = ref([])
const thongTinHoaDonDangXem = ref(null)
const dichLoaiPhi = (ma) => {
  const dict = {
    'tien_phong': 'Tiền phòng', 'tien_dien': 'Tiền điện', 
    'tien_nuoc': 'Tiền nước', 'rac': 'Tiền rác', 
    'wifi': 'Tiền Wifi', 'khac': 'Chi phí khác'
  }
  return dict[ma] || ma
}

const xemChiTiet = async (id) => {
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/hoa-don/${id}/chi-tiet`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') {
      thongTinHoaDonDangXem.value = result.hoa_don
      chiTietHoaDonDangXem.value = result.chi_tiet
      hienThiModalChiTiet.value = true
    }
  } catch (error) {
    alert('Không thể tải chi tiết hóa đơn!')
  }
}

const inHoaDon = () => inHoaDonUtil(thongTinHoaDonDangXem.value, chiTietHoaDonDangXem.value)

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
            <th class="text-center">Thao tác</th>
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
            <td>
              <div class="action-btns">
                <button @click="xemChiTiet(hd.id)" class="btn-xem">
                  🔍 Xem
                </button>
                <button
                  v-if="hd.trang_thai === 'chua_thanh_toan'"
                  @click="xacNhanThuTien(hd.id)"
                  class="btn-thu"
                >
                  ✔ Đã nhận tiền
                </button>
              </div>
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

    <transition name="modal">
      <div v-if="hienThiModalChiTiet" class="modal-bg" @click.self="hienThiModalChiTiet = false">
        <div class="modal-box modal-chitiet">
          <div class="modal-hd">
            <div class="modal-hd-left">
              <div class="modal-invoice-badge">
                <span>🧾</span>
              </div>
              <div>
                <h5 class="modal-title">Chi Tiết Hóa Đơn</h5>
                <p v-if="thongTinHoaDonDangXem" class="modal-sub">
                  <span class="ma-hd-badge">{{ thongTinHoaDonDangXem.ma_hoa_don }}</span>
                </p>
              </div>
            </div>
            <div class="modal-hd-actions">
              <button @click="hienThiModalChiTiet = false" class="btn-close-modal" title="Đóng">✕</button>
            </div>
          </div>

          <div v-if="thongTinHoaDonDangXem" class="modal-body">

            <div :class="['ct-status-banner', thongTinHoaDonDangXem.trang_thai === 'da_thanh_toan' ? 'banner-done' : 'banner-pending']">
              <span class="banner-icon">{{ thongTinHoaDonDangXem.trang_thai === 'da_thanh_toan' ? '✅' : '⏳' }}</span>
              <div>
                <div class="banner-title">{{ thongTinHoaDonDangXem.trang_thai === 'da_thanh_toan' ? 'Đã thu tiền' : 'Chưa thu tiền' }}</div>
                <div v-if="thongTinHoaDonDangXem.ngay_thanh_toan" class="banner-sub">Ngày thu: {{ fmtNgay(thongTinHoaDonDangXem.ngay_thanh_toan) }}</div>
                <div v-else class="banner-sub">Hạn chót: {{ fmtNgay(thongTinHoaDonDangXem.han_chot) }}</div>
              </div>
              <div class="banner-amount">{{ fmt(thongTinHoaDonDangXem.tong_tien) }}</div>
            </div>

            <div class="ct-info-row">
              <div class="ct-info-box">
                <div class="ct-info-box-title">👤 Khách thuê</div>
                <div class="ct-info-box-val">{{ thongTinHoaDonDangXem.ten_khach }}</div>
                <div class="ct-info-box-sub">🏠 Phòng {{ thongTinHoaDonDangXem.so_phong }}</div>
              </div>
              <div class="ct-info-box">
                <div class="ct-info-box-title">📅 Kỳ thanh toán</div>
                <div class="ct-info-box-val">{{ fmtThang(thongTinHoaDonDangXem.thang_thanh_toan) }}</div>
                <div class="ct-info-box-sub">Hạn nộp: {{ fmtNgay(thongTinHoaDonDangXem.han_chot) }}</div>
              </div>
            </div>

            <div class="chitiet-section-title">📋 Chi Tiết Các Khoản Thu</div>
            <table class="chitiet-table">
              <thead>
                <tr>
                  <th style="width:40%">Loại khoản thu</th>
                  <th class="text-center" style="width:15%">Số lượng</th>
                  <th class="text-right" style="width:22%">Đơn giá</th>
                  <th class="text-right" style="width:23%">Thành tiền</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="ct in chiTietHoaDonDangXem" :key="ct.id">
                  <td>
                    <span class="loai-phi-badge">{{ dichLoaiPhi(ct.loai_phi) }}</span>
                  </td>
                  <td class="text-center">{{ ct.so_luong }}</td>
                  <td class="text-right">{{ fmt(ct.don_gia) }}</td>
                  <td class="text-right fw-bold">{{ fmt(ct.thanh_tien) }}</td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="chitiet-tong-row">
                  <td colspan="3" class="text-right">TỔNG CỘNG</td>
                  <td class="text-right total-val">{{ fmt(thongTinHoaDonDangXem.tong_tien) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="modal-footer">
            <button @click="hienThiModalChiTiet = false" class="btn-cancel">Huỷ</button>
            <div class="modal-footer-right">
              <button @click="inHoaDon" class="btn-print-footer">
                🖨️ In hóa đơn
              </button>
              <button
                v-if="thongTinHoaDonDangXem && thongTinHoaDonDangXem.trang_thai === 'chua_thanh_toan'"
                @click="xacNhanThuTien(thongTinHoaDonDangXem.id); hienThiModalChiTiet = false"
                class="btn-submit"
              >
                ✅ Xác nhận đã thu tiền
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<style scoped>
@import "../assets/css/quan-ly-hoa-don.css";
</style>