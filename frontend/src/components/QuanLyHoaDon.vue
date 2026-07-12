<script setup>
import { ref, onMounted, computed } from 'vue'
import { inHoaDon as inHoaDonUtil } from '../utils/inHoaDon.js'

const danhSachHoaDon = ref([])
const danhSachHopDong = ref([])
const danhSachDichVu = ref([])
const caiDat = ref({ gia_dien: 3500, gia_nuoc: 15000 })
const chiSoPhong = ref({ dien: null, nuoc: null })
const dangLayChiSo = ref(false)
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
    'tien_nuoc': 'Tiền nước', 'khac': 'Dịch vụ'
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
  } catch (error) { alert('Không thể tải chi tiết hóa đơn!') }
}

const inHoaDon = () => inHoaDonUtil(thongTinHoaDonDangXem.value, chiTietHoaDonDangXem.value)

const hienThiModal = ref(false)
const hopDongDuocChon = ref(null)
const formHoaDon = ref({ hop_dong_id: '', thang_thanh_toan: '', han_chot: '', chi_tiet: [] })
const cacKhoanThu = ref([])

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
    if (result.status === 'success') {
      danhSachHopDong.value = result.hop_dongs
      if (result.cai_dat) caiDat.value = result.cai_dat
      if (result.dich_vus) danhSachDichVu.value = result.dich_vus
    }
  } catch (e) { console.error(e) }
}

const moModalThem = () => {
  const homNay = new Date().toISOString().split('T')[0]
  const hanChot = new Date()
  hanChot.setDate(hanChot.getDate() + 7)
  hopDongDuocChon.value = null
  chiSoPhong.value = { dien: null, nuoc: null }
  cacKhoanThu.value = []
  formHoaDon.value = {
    hop_dong_id: '',
    thang_thanh_toan: homNay,
    han_chot: hanChot.toISOString().split('T')[0],
    chi_tiet: []
  }
  hienThiModal.value = true
}

const layChiSoPhong = async (phongId) => {
  dangLayChiSo.value = true
  chiSoPhong.value = { dien: null, nuoc: null }
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/hoa-don/chi-so-moi-nhat/${phongId}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') chiSoPhong.value = { dien: result.dien, nuoc: result.nuoc }
  } catch (e) { console.error(e) }
  dangLayChiSo.value = false
}

const fmt = (n) => new Intl.NumberFormat('vi-VN').format(n) + ' đ'

const xayDungKhoanThu = () => {
  const hd = hopDongDuocChon.value
  if (!hd) return
  const ds = []

  ds.push({
    loai_phi: 'tien_phong',
    icon: '🏠', label: 'Tiền phòng',
    mau: '#6366f1', bgMau: '#eff0ff',
    so_luong: 1,
    don_gia: Number(hd.gia_thue_hang_thang),
    thanh_tien: Number(hd.gia_thue_hang_thang),
    ghiChu: 'Theo hợp đồng',
    coSo: true,
  })

  if (chiSoPhong.value.dien) {
    const d = chiSoPhong.value.dien
    ds.push({
      loai_phi: 'tien_dien',
      icon: '⚡', label: 'Tiền điện',
      mau: '#f59e0b', bgMau: '#fffbeb',
      so_luong: d.tieu_thu, don_gia: d.don_gia,
      thanh_tien: d.tieu_thu * d.don_gia,
      ghiChu: `${d.tieu_thu} kWh × ${fmt(d.don_gia)}/kWh`,
      coSo: true,
    })
  } else {
    ds.push({
      loai_phi: 'tien_dien',
      icon: '⚡', label: 'Tiền điện',
      mau: '#94a3b8', bgMau: '#f8fafc',
      so_luong: 0, don_gia: 0, thanh_tien: 0,
      ghiChu: '⚠️ Chưa có chỉ số điện — cần nhập trước khi lập hóa đơn',
      coSo: false,
    })
  }

  if (chiSoPhong.value.nuoc) {
    const n = chiSoPhong.value.nuoc
    ds.push({
      loai_phi: 'tien_nuoc',
      icon: '💧', label: 'Tiền nước',
      mau: '#0ea5e9', bgMau: '#f0f9ff',
      so_luong: n.tieu_thu, don_gia: n.don_gia,
      thanh_tien: n.tieu_thu * n.don_gia,
      ghiChu: `${n.tieu_thu} khối × ${fmt(n.don_gia)}/khối`,
      coSo: true,
    })
  } else {
    ds.push({
      loai_phi: 'tien_nuoc',
      icon: '💧', label: 'Tiền nước',
      mau: '#94a3b8', bgMau: '#f8fafc',
      so_luong: 0, don_gia: 0, thanh_tien: 0,
      ghiChu: '⚠️ Chưa có chỉ số nước — cần nhập trước khi lập hóa đơn',
      coSo: false,
    })
  }

  const ICONS = ['🗑️', '📶', '🏍️', '💡', '📦', '⚙️']
  const MAUS  = ['#84cc16', '#8b5cf6', '#f97316', '#06b6d4', '#ec4899', '#6366f1']
  const BGS   = ['#f7fee7', '#f5f3ff', '#fff7ed', '#ecfeff', '#fdf2f8', '#eff0ff']

  danhSachDichVu.value.forEach((dv, idx) => {
    ds.push({
      loai_phi: `dv_${dv.id}`,
      icon: ICONS[idx % ICONS.length],
      label: dv.ten_dich_vu,
      mau:   MAUS[idx % MAUS.length],
      bgMau: BGS[idx % BGS.length],
      so_luong: 1, don_gia: Number(dv.don_gia),
      thanh_tien: Number(dv.don_gia),
      ghiChu: dv.mo_ta || 'Thu hàng tháng',
      coSo: true,
    })
  })

  cacKhoanThu.value = ds
}

const chonHopDong = async () => {
  const hd = danhSachHopDong.value.find(h => h.id == formHoaDon.value.hop_dong_id)
  hopDongDuocChon.value = hd || null
  if (!hd) { cacKhoanThu.value = []; return }
  await layChiSoPhong(hd.phong_id)
  xayDungKhoanThu()
}

const tongTien = computed(() =>
  cacKhoanThu.value.filter(k => k.coSo).reduce((s, k) => s + Number(k.thanh_tien), 0)
)

const luuHoaDon = async () => {
  if (!formHoaDon.value.hop_dong_id) {
    hienThongBao('error', 'Vui lòng chọn phòng / hợp đồng!')
    return
  }
  const chiTietGui = cacKhoanThu.value
    .filter(k => k.coSo && k.thanh_tien > 0)
    .map(k => ({
      loai_phi: k.loai_phi.startsWith('dv_') ? 'khac' : k.loai_phi,
      so_luong: Number(k.so_luong),
      don_gia: Number(k.don_gia),
      thanh_tien: Number(k.thanh_tien),
    }))
  if (chiTietGui.length === 0) {
    hienThongBao('error', 'Không có khoản thu hợp lệ!')
    return
  }
  dangLuu.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/hoa-don', {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ ...formHoaDon.value, chi_tiet: chiTietGui })
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
    if (r.status === 'success') { layDanhSachHoaDon(); hienThongBao('success', 'Đã ghi nhận thanh toán thành công!') }
  } catch (e) { hienThongBao('error', 'Lỗi kết nối!') }
}

const tongChuaThu = computed(() => danhSachHoaDon.value.filter(h => h.trang_thai === 'chua_thanh_toan').length)
const tongDaThu   = computed(() => danhSachHoaDon.value.filter(h => h.trang_thai === 'da_thanh_toan').length)
const doanhThuThang = computed(() => danhSachHoaDon.value.filter(h => h.trang_thai === 'da_thanh_toan').reduce((s, h) => s + Number(h.tong_tien), 0))

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
            <td><span class="ma-hd">{{ hd.ma_hoa_don }}</span></td>
            <td>
              <div class="phong-name">🏠 Phòng {{ hd.so_phong }}</div>
              <div class="khach-name">👤 {{ hd.ten_khach }}</div>
            </td>
            <td><span class="ky-tt">{{ fmtThang(hd.thang_thanh_toan) }}</span></td>
            <td><span class="tong-tien">{{ fmt(hd.tong_tien) }}</span></td>
            <td><span class="han-chot">{{ fmtNgay(hd.han_chot) }}</span></td>
            <td>
              <span v-if="hd.trang_thai === 'chua_thanh_toan'" class="status-badge badge-pending">Chưa thu</span>
              <span v-else-if="hd.trang_thai === 'da_thanh_toan'" class="status-badge badge-done">Đã thu</span>
              <span v-else class="status-badge badge-overdue">Quá hạn</span>
            </td>
            <td>
              <div class="action-btns">
                <button @click="xemChiTiet(hd.id)" class="btn-xem">🔍 Xem</button>
                <button v-if="hd.trang_thai === 'chua_thanh_toan'" @click="xacNhanThuTien(hd.id)" class="btn-thu">✔ Đã nhận tiền</button>
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
            <h5 class="modal-title">🧾 Lập Hóa Đơn Thu Tiền</h5>
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
              <span v-if="dangLayChiSo" class="chi-so-loading">⏳ Đang tải chỉ số...</span>
              <span v-else-if="chiSoPhong.dien || chiSoPhong.nuoc" class="chi-so-found">
                <span v-if="chiSoPhong.dien">⚡ {{ chiSoPhong.dien.tieu_thu }} kWh</span>
                <span v-if="chiSoPhong.nuoc">💧 {{ chiSoPhong.nuoc.tieu_thu }} khối</span>
              </span>
            </div>

            <div v-if="dangLayChiSo" class="khoan-thu-loading">
              <div class="spinner"></div>
              <span>Đang tải dữ liệu phòng...</span>
            </div>
            <div v-else-if="cacKhoanThu.length > 0" class="khoan-thu-wrap">
              <div class="khoan-thu-header">
                <span class="khoan-thu-title">📋 Các khoản thu</span>
                <span class="khoan-thu-sub">Được tính tự động theo dữ liệu hệ thống</span>
              </div>

              <div class="khoan-thu-list">
                <div
                  v-for="k in cacKhoanThu"
                  :key="k.loai_phi"
                  :class="['khoan-thu-card', k.coSo ? 'khoan-bat' : 'khoan-missing']"
                  :style="k.coSo ? `border-left-color: ${k.mau}` : ''"
                >
                  <div class="khoan-left">
                    <div class="khoan-icon" :style="k.coSo ? `background:${k.bgMau}; color:${k.mau}` : ''">
                      {{ k.icon }}
                    </div>
                    <div class="khoan-info">
                      <div class="khoan-label">{{ k.label }}</div>
                      <div class="khoan-ghichu">{{ k.ghiChu }}</div>
                    </div>
                  </div>

                  <div v-if="k.coSo" class="khoan-right-readonly">
                    <span class="khoan-so-luong">{{ k.so_luong }} × {{ fmt(k.don_gia) }}</span>
                    <span class="khoan-thanhtien-ro" :style="`color:${k.mau}`">{{ fmt(k.thanh_tien) }}</span>
                  </div>
                  <div v-else class="khoan-right-missing">
                    <span class="khoan-missing-badge">Chưa có dữ liệu</span>
                  </div>
                </div>
              </div>

              <div class="tong-cong-bar">
                <span class="tong-cong-lbl">TỔNG CỘNG</span>
                <span class="tong-cong-val">{{ fmt(tongTien) }}</span>
              </div>
            </div>

            <div v-else-if="!hopDongDuocChon" class="khoan-chua-chon">
              <div class="khoan-chua-chon-icon">🏠</div>
              <div>Chọn phòng để tự động hiển thị các khoản thu</div>
            </div>

            <div class="modal-footer">
              <button type="button" @click="hienThiModal = false" class="btn-cancel">Hủy bỏ</button>
              <button type="submit" class="btn-submit" :disabled="dangLuu || !hopDongDuocChon">
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
              <div class="modal-invoice-badge"><span>🧾</span></div>
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
                  <td><span class="loai-phi-badge">{{ dichLoaiPhi(ct.loai_phi) }}</span></td>
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
              <button @click="inHoaDon" class="btn-print-footer">🖨️ In hóa đơn</button>
              <button
                v-if="thongTinHoaDonDangXem && thongTinHoaDonDangXem.trang_thai === 'chua_thanh_toan'"
                @click="xacNhanThuTien(thongTinHoaDonDangXem.id); hienThiModalChiTiet = false"
                class="btn-submit"
              >✅ Xác nhận đã thu tiền</button>
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