<script setup>
import { ref, onMounted, computed } from 'vue'

const danhSachPhong = ref([])
const danhSachKhach = ref([])
const danhSachHopDong = ref([])
const dangTai = ref(true)
const dangLuu = ref(false)
const token = localStorage.getItem('admin_token')

const hienThiModal = ref(false)
const loiForm = ref('')
const thongBao = ref({ hien: false, loai: '', noi_dung: '' })

const formHopDong = ref({
  phong_id: '', khach_id: '',
  ngay_bat_dau: '', ngay_ket_thuc: '',
  gia_thue_hang_thang: '', tien_coc: '',
  ngay_thu_tien_hang_thang: 5
})

const hienThongBao = (loai, noi_dung) => {
  thongBao.value = { hien: true, loai, noi_dung }
  setTimeout(() => { thongBao.value.hien = false }, 3500)
}

const layDanhSachHopDong = async () => {
  dangTai.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/hop-dong', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') danhSachHopDong.value = result.data
  } catch (e) { console.error(e) }
  finally { dangTai.value = false }
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
  } catch (e) { console.error(e) }
}

const moModalThem = () => {
  const homNay = new Date().toISOString().split('T')[0]
  formHopDong.value = {
    phong_id: '', khach_id: '', ngay_bat_dau: homNay,
    ngay_ket_thuc: '', gia_thue_hang_thang: '', tien_coc: '',
    ngay_thu_tien_hang_thang: 5
  }
  loiForm.value = ''
  hienThiModal.value = true
}

const formatSoTien = (value) => {
  const so = String(value).replace(/\D/g, '')
  return so.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}
const onNhapGiaThu  = (e) => { formHopDong.value.gia_thue_hang_thang = formatSoTien(e.target.value) }
const onNhapTienCoc = (e) => { formHopDong.value.tien_coc = formatSoTien(e.target.value) }

const tuDongDienGia = () => {
  const p = danhSachPhong.value.find(p => p.id === formHopDong.value.phong_id)
  if (p) {
    formHopDong.value.gia_thue_hang_thang = formatSoTien(parseInt(p.gia_thue))
    if (!formHopDong.value.tien_coc)
      formHopDong.value.tien_coc = formatSoTien(parseInt(p.gia_thue))
  }
}

const tuDongNgayKetThuc = () => {
  if (formHopDong.value.ngay_bat_dau) {
    const d = new Date(formHopDong.value.ngay_bat_dau)
    d.setFullYear(d.getFullYear() + 1)
    formHopDong.value.ngay_ket_thuc = d.toISOString().split('T')[0]
  }
}

const minNgayKetThuc = () => {
  if (!formHopDong.value.ngay_bat_dau) return ''
  const d = new Date(formHopDong.value.ngay_bat_dau)
  d.setMonth(d.getMonth() + 1)
  return d.toISOString().split('T')[0]
}

const luuHopDong = async () => {
  loiForm.value = ''
  if (!formHopDong.value.ngay_bat_dau || !formHopDong.value.ngay_ket_thuc) {
    loiForm.value = 'Vui lòng nhập đầy đủ ngày bắt đầu và ngày kết thúc.'
    return
  }
  const batDau = new Date(formHopDong.value.ngay_bat_dau)
  const ketThuc = new Date(formHopDong.value.ngay_ket_thuc)
  const min = new Date(batDau); min.setMonth(min.getMonth() + 1)
  if (ketThuc < min) { loiForm.value = 'Thời hạn hợp đồng tối thiểu phải là 1 tháng.'; return }

  dangLuu.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/hop-dong', {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
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
      hienThongBao('success', 'Tạo hợp đồng thành công!')
    } else if (result.errors) {
      loiForm.value = Object.values(result.errors).flat().join(' | ')
    } else {
      loiForm.value = result.message || 'Kiểm tra lại dữ liệu nhập!'
    }
  } catch (e) { loiForm.value = 'Không thể kết nối đến máy chủ!' }
  dangLuu.value = false
}

const huyHopDong = async (id, ma) => {
  if (!confirm(`Xác nhận chấm dứt hợp đồng ${ma}?\nPhòng sẽ được giải phóng.`)) return
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/hop-dong/${id}/huy`, {
      method: 'PUT',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const r = await res.json()
    if (r.status === 'success') {
      layDanhSachHopDong()
      hienThongBao('success', 'Đã chấm dứt hợp đồng.')
    }
  } catch (e) { hienThongBao('error', 'Lỗi kết nối!') }
}

const taiFilePDF = async (id, ma) => {
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/hop-dong/${id}/pdf`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    if (res.ok) {
      const blob = await res.blob()
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url; a.download = `HopDong_${ma}.pdf`
      document.body.appendChild(a); a.click(); a.remove()
    } else { hienThongBao('error', 'Không thể xuất file PDF!') }
  } catch (e) { hienThongBao('error', 'Lỗi kết nối máy chủ!') }
}

// Thống kê
const tongHieuLuc = computed(() => danhSachHopDong.value.filter(h => h.trang_thai === 'hieu_luc').length)

const tongHetHan  = computed(() => danhSachHopDong.value.filter(h => h.trang_thai === 'het_han').length)

const fmt    = (n) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(n)
const fmtNgay = (d) => new Date(d).toLocaleDateString('vi-VN')

onMounted(() => { layDanhSachHopDong(); layDuLieuForm() })
</script>

<template>
  <div class="hd-wrap">

    <!-- Toast -->
    <transition name="toast">
      <div v-if="thongBao.hien" :class="['toast-noti', thongBao.loai === 'success' ? 'toast-ok' : 'toast-err']">
        {{ thongBao.loai === 'success' ? '✅' : '❌' }} {{ thongBao.noi_dung }}
      </div>
    </transition>

    <!-- Header -->
    <div class="hd-header">
      <div>
        <h4 class="hd-title">📋 Quản Lý Hợp Đồng</h4>
      </div>
      <button @click="moModalThem" class="btn-create">
        <span>＋</span> Tạo Hợp Đồng
      </button>
    </div>

    <!-- Stat row -->
    <div class="stat-row">
      <div class="stat-card st-green">
        <div class="st-icon">✅</div>
        <div><div class="st-num">{{ tongHieuLuc }}</div><div class="st-lbl">Hiệu lực</div></div>
      </div>
      <div class="stat-card st-yellow">
        <div class="st-icon">⏰</div>
        <div><div class="st-num">{{ tongHetHan }}</div><div class="st-lbl">Hết hạn</div></div>
      </div>

      <div class="stat-card st-blue">
        <div class="st-icon">📄</div>
        <div><div class="st-num">{{ danhSachHopDong.length }}</div><div class="st-lbl">Tổng cộng</div></div>
      </div>
    </div>

    <!-- Danh sách -->
    <div class="hd-body">
      <div v-if="dangTai" class="hd-loading">
        <div class="spin"></div> Đang tải dữ liệu...
      </div>

      <div v-else-if="danhSachHopDong.length === 0" class="hd-empty">
        Chưa có hợp đồng nào.
      </div>

      <div v-else class="hd-list">
        <transition-group name="card-list" tag="div" class="cards-grid">
          <div
            v-for="hd in danhSachHopDong"
            :key="hd.id"
            class="contract-card"
            :class="{
              'card-active':   hd.trang_thai === 'hieu_luc',
              'card-expired':  hd.trang_thai === 'het_han',
              'card-canceled': hd.trang_thai === 'da_huy'
            }"
          >
            <!-- Card top bar -->
            <div class="card-topbar">
              <span class="card-ma">{{ hd.ma_hop_dong }}</span>
              <span
                class="card-badge"
                :class="{
                  'badge-green':  hd.trang_thai === 'hieu_luc',
                  'badge-yellow': hd.trang_thai === 'het_han',
                  'badge-red':    hd.trang_thai === 'da_huy'
                }"
              >
                {{ hd.trang_thai === 'hieu_luc' ? 'Hiệu lực' : hd.trang_thai === 'het_han' ? 'Hết hạn' : 'Đã hủy' }}
              </span>
            </div>

            <!-- Thông tin chính -->
            <div class="card-main">
              <div class="card-room">🏠 Phòng {{ hd.so_phong }}</div>
              <div class="card-tenant">{{ hd.ten_khach_hang }}</div>
              <div class="card-phone">{{ hd.so_dien_thoai }}</div>
            </div>

            <!-- Thông tin phụ -->
            <div class="card-info">
              <div class="info-item">
                <span class="info-label">Bắt đầu</span>
                <span class="info-val">{{ fmtNgay(hd.ngay_bat_dau) }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Kết thúc</span>
                <span class="info-val">{{ fmtNgay(hd.ngay_ket_thuc) }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Giá thuê</span>
                <span class="info-val price">{{ fmt(hd.gia_thue_hang_thang) }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Tiền cọc</span>
                <span class="info-val">{{ fmt(hd.tien_coc) }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="card-actions">
              <button @click="taiFilePDF(hd.id, hd.ma_hop_dong)" class="btn-pdf">
                📄 Xuất PDF
              </button>
              <button
                v-if="hd.trang_thai === 'hieu_luc'"
                @click="huyHopDong(hd.id, hd.ma_hop_dong)"
                class="btn-cancel-contract"
              >
                Chấm dứt
              </button>
            </div>
          </div>
        </transition-group>
      </div>
    </div>

    <!-- ===== MODAL ===== -->
    <transition name="modal-fade">
      <div v-if="hienThiModal" class="modal-bg" @click.self="hienThiModal = false">
        <div class="modal-box">

          <div class="modal-hd">
            <h5 class="modal-title">📋 Tạo Hợp Đồng Thuê Phòng</h5>
            <button @click="hienThiModal = false" class="btn-close-x" title="Đóng">✕</button>
          </div>

          <div v-if="loiForm" class="err-box">⚠️ {{ loiForm }}</div>

          <form @submit.prevent="luuHopDong" class="modal-form">


            <div class="form-row-2">
              <div class="fg">
                <label>Phòng trống <span class="req">*</span></label>
                <select v-model.number="formHopDong.phong_id" @change="tuDongDienGia" class="fc" required>
                  <option value="" disabled>— Chọn phòng —</option>
                  <option v-for="p in danhSachPhong" :key="p.id" :value="p.id">
                    Phòng {{ p.so_phong }} — {{ Number(p.gia_thue).toLocaleString('vi-VN') }}đ/tháng
                  </option>
                </select>
              </div>
              <div class="fg">
                <label>Khách hàng <span class="req">*</span></label>
                <select v-model.number="formHopDong.khach_id" class="fc" required>
                  <option value="" disabled>— Chọn khách —</option>
                  <option v-for="k in danhSachKhach" :key="k.id" :value="k.id">
                    {{ k.ho_ten }} — {{ k.so_dien_thoai }}
                  </option>
                </select>
              </div>
            </div>


            <div class="form-row-2">
              <div class="fg">
                <label>Ngày bắt đầu <span class="req">*</span></label>
                <input v-model="formHopDong.ngay_bat_dau" @change="tuDongNgayKetThuc" type="date" class="fc" required>
              </div>
              <div class="fg">
                <label>Ngày kết thúc <span class="req">*</span></label>
                <input v-model="formHopDong.ngay_ket_thuc" type="date" :min="minNgayKetThuc()" class="fc" required>
              </div>
            </div>


            <div class="form-row-3">
              <div class="fg">
                <label>Giá thuê/tháng (đ) <span class="req">*</span></label>
                <input :value="formHopDong.gia_thue_hang_thang" @input="onNhapGiaThu" type="text" inputmode="numeric" class="fc" placeholder="VD: 2.500.000" required>
              </div>
              <div class="fg">
                <label>Tiền cọc (đ) <span class="req">*</span></label>
                <input :value="formHopDong.tien_coc" @input="onNhapTienCoc" type="text" inputmode="numeric" class="fc" placeholder="VD: 2.500.000" required>
              </div>
              <div class="fg">
                <label>Ngày thu tiền hàng tháng</label>
                <input v-model="formHopDong.ngay_thu_tien_hang_thang" type="number" min="1" max="31" class="fc" required>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" @click="hienThiModal = false" class="btn-cancel">Hủy bỏ</button>
              <button type="submit" class="btn-submit" :disabled="dangLuu">
                <span v-if="dangLuu">⏳ Đang lưu...</span>
                <span v-else>✔ Tạo Hợp Đồng</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

  </div>
</template>

<style scoped>
/* ---- Base ---- */
.hd-wrap { font-family: 'Inter', 'Segoe UI', sans-serif; }

/* ---- Toast ---- */
.toast-noti {
  position: fixed; top: 24px; right: 24px; z-index: 99999;
  padding: 13px 20px; border-radius: 12px; font-size: 14px; font-weight: 700;
  display: flex; align-items: center; gap: 8px;
  box-shadow: 0 8px 24px rgba(0,0,0,.15);
}
.toast-ok  { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
.toast-err { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.toast-enter-active, .toast-leave-active { transition: all .3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateX(40px); }

/* ---- Header ---- */
.hd-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 24px 28px 16px; background: #fff; border-bottom: 1px solid #f1f5f9;
}
.hd-title { font-size: 20px; font-weight: 800; color: #1e293b; margin: 0; }
.hd-sub   { font-size: 13px; color: #94a3b8; margin: 3px 0 0; }

.btn-create {
  display: flex; align-items: center; gap: 8px;
  background: linear-gradient(135deg, #2E6E7E, #00C4A0);
  color: #fff; border: none; border-radius: 10px;
  padding: 10px 22px; font-size: 14px; font-weight: 700;
  cursor: pointer; transition: all .2s;
  box-shadow: 0 4px 14px rgba(46,110,126,.35);
}
.btn-create:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46,110,126,.45); }

/* ---- Stat ---- */
.stat-row { display: flex; gap: 14px; padding: 16px 28px; background: #f8fafc; }
.stat-card {
  flex: 1; display: flex; align-items: center; gap: 14px;
  background: #fff; border-radius: 12px; padding: 14px 18px;
  box-shadow: 0 2px 8px rgba(0,0,0,.06); border-left: 4px solid;
  transition: transform .2s;
}
.stat-card:hover { transform: translateY(-2px); }
.st-green  { border-color: #10b981; }
.st-yellow { border-color: #f59e0b; }

.st-blue   { border-color: #6366f1; }
.st-icon   { font-size: 26px; }
.st-num    { font-size: 22px; font-weight: 800; color: #1e293b; line-height: 1.1; }
.st-lbl    { font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }

/* ---- Body ---- */
.hd-body { padding: 20px 28px 28px; }
.hd-loading { display: flex; align-items: center; justify-content: center; gap: 12px; padding: 60px; color: #2E6E7E; font-weight: 700; }
.spin { width: 22px; height: 22px; border: 3px solid #d1fae5; border-top-color: #10b981; border-radius: 50%; animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.hd-empty { padding: 60px; text-align: center; color: #94a3b8; font-size: 15px; }

/* ---- Card grid ---- */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 18px;
}

.contract-card {
  background: #fff; border-radius: 14px;
  box-shadow: 0 2px 12px rgba(0,0,0,.07);
  border: 1.5px solid #e2e8f0;
  overflow: hidden; transition: all .25s;
  display: flex; flex-direction: column;
}
.contract-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 28px rgba(0,0,0,.13);
}

/* Màu viền trái theo trạng thái */
.card-active   { border-left: 4px solid #10b981; }
.card-expired  { border-left: 4px solid #f59e0b; }
.card-canceled { border-left: 4px solid #ef4444; opacity: .75; }

/* Card topbar */
.card-topbar {
  display: flex; justify-content: space-between; align-items: center;
  padding: 12px 16px 8px; background: #f8fafc;
  border-bottom: 1px solid #f1f5f9;
}
.card-ma {
  font-size: 11px; font-weight: 700; color: #94a3b8;
  font-family: 'Courier New', monospace; letter-spacing: .5px;
}
.card-badge {
  font-size: 11px; font-weight: 700; padding: 3px 10px;
  border-radius: 20px; letter-spacing: .3px;
}
.badge-green  { background: #d1fae5; color: #065f46; }
.badge-yellow { background: #fef3c7; color: #92400e; }
.badge-red    { background: #fee2e2; color: #991b1b; }

/* Card main info */
.card-main { padding: 14px 16px 10px; }
.card-room   { font-size: 18px; font-weight: 800; color: #2E6E7E; margin-bottom: 4px; }
.card-tenant { font-size: 14px; font-weight: 700; color: #1e293b; }
.card-phone  { font-size: 12px; color: #94a3b8; margin-top: 2px; }

/* Card info grid */
.card-info {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 0; padding: 0 16px 12px; flex: 1;
}
.info-item { padding: 6px 0; }
.info-label { display: block; font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 2px; }
.info-val   { font-size: 13px; font-weight: 600; color: #374151; }
.info-val.price { color: #dc2626; font-weight: 800; }

/* Card actions */
.card-actions {
  display: flex; gap: 8px; padding: 12px 16px;
  border-top: 1px solid #f1f5f9; background: #fafafa;
}
.btn-pdf {
  flex: 1; background: #1e293b; color: #fff; border: none;
  border-radius: 8px; padding: 8px 0; font-size: 13px; font-weight: 700;
  cursor: pointer; transition: all .2s;
}
.btn-pdf:hover { background: #2E6E7E; }
.btn-cancel-contract {
  flex: 1; background: none; border: 1.5px solid #ef4444;
  color: #ef4444; border-radius: 8px; padding: 8px 0;
  font-size: 13px; font-weight: 700; cursor: pointer; transition: all .2s;
}
.btn-cancel-contract:hover { background: #ef4444; color: #fff; }

/* Card list animation */
.card-list-enter-active { transition: all .4s ease; }
.card-list-leave-active { transition: all .3s ease; }
.card-list-enter-from   { opacity: 0; transform: translateY(20px); }
.card-list-leave-to     { opacity: 0; transform: scale(.9); }

/* ===== MODAL ===== */
.modal-bg {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(15,23,42,.6); backdrop-filter: blur(4px);
  display: flex; justify-content: center; align-items: center;
}
.modal-box {
  background: #fff; border-radius: 16px;
  width: 95%; max-width: 660px; max-height: 92vh; overflow-y: auto;
  box-shadow: 0 24px 60px rgba(0,0,0,.25);
}

.modal-hd {
  display: flex; justify-content: space-between; align-items: center;
  padding: 22px 28px 18px; border-bottom: 1px solid #f1f5f9;
  position: sticky; top: 0; background: #fff; z-index: 1;
  border-radius: 16px 16px 0 0;
}
.modal-title { font-size: 17px; font-weight: 800; color: #1e293b; margin: 0; }

.btn-close-x {
  background: #f1f5f9; border: none; width: 34px; height: 34px;
  border-radius: 50%; font-size: 15px; color: #64748b;
  cursor: pointer; transition: all .2s;
}
.btn-close-x:hover { background: #fee2e2; color: #dc2626; }

.err-box {
  margin: 14px 28px 0; padding: 10px 14px; border-radius: 8px;
  background: #fef2f2; border: 1px solid #fecaca;
  color: #dc2626; font-size: 13px; font-weight: 600;
}

.modal-form { padding: 20px 28px 24px; }



.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 20px; }

.fg label {
  display: block; font-size: 11px; font-weight: 700;
  color: #475569; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;
}
.req { color: #ef4444; }
.fc {
  width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0;
  border-radius: 8px; font-size: 14px; color: #1e293b;
  transition: border-color .2s; box-sizing: border-box;
}
.fc:focus { outline: none; border-color: #2E6E7E; box-shadow: 0 0 0 3px rgba(46,110,126,.12); }

.modal-footer {
  display: flex; justify-content: flex-end; gap: 10px;
  margin-top: 22px; padding-top: 18px; border-top: 1px solid #f1f5f9;
}
.btn-cancel {
  background: #f1f5f9; color: #64748b; border: none;
  border-radius: 8px; padding: 10px 22px; font-weight: 700;
  font-size: 14px; cursor: pointer; transition: .2s;
}
.btn-cancel:hover { background: #e2e8f0; }
.btn-submit {
  background: linear-gradient(135deg, #2E6E7E, #00C4A0);
  color: #fff; border: none; border-radius: 8px;
  padding: 10px 26px; font-weight: 700; font-size: 14px;
  cursor: pointer; transition: .2s;
  box-shadow: 0 4px 12px rgba(46,110,126,.35);
}
.btn-submit:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(46,110,126,.45); }
.btn-submit:disabled { opacity: .6; cursor: not-allowed; }

/* Modal animation */
.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity .25s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
.modal-fade-enter-active .modal-box,
.modal-fade-leave-active .modal-box { transition: transform .25s ease; }
.modal-fade-enter-from .modal-box { transform: translateY(-20px) scale(.97); }
.modal-fade-leave-to   .modal-box { transform: translateY(-20px) scale(.97); }
</style>