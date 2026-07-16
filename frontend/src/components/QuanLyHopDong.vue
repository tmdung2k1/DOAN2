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

const tongHieuLuc = computed(() => danhSachHopDong.value.filter(h => h.trang_thai === 'hieu_luc').length)

const tongHetHan  = computed(() => danhSachHopDong.value.filter(h => h.trang_thai === 'het_han').length)

const fmt    = (n) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(n)
const fmtNgay = (d) => new Date(d).toLocaleDateString('vi-VN')

const hienThiModalKhachHang = ref(false)
const danhSachKhachHang = ref([])
const hopDongDangChonKhach = ref(null)
const formKhachHang = ref({ hop_dong_id: '', ho_ten: '', cccd: '', so_dien_thoai: '' })

const moModalKhachHang = async (hopDong) => {
  hopDongDangChonKhach.value = hopDong
  formKhachHang.value = { hop_dong_id: hopDong.id, ho_ten: '', cccd: '', so_dien_thoai: '' }

  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/hop-dong/${hopDong.id}/khach-hang`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    const result = await res.json()
    if (result.status === 'success') {
      danhSachKhachHang.value = result.data
      hienThiModalKhachHang.value = true
    }
  } catch (error) { console.error(error) }
}

const luuKhachHang = async () => {
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/khach-hang', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formKhachHang.value)
    })
    const result = await res.json()
    if (result.status === 'success') {
      danhSachKhachHang.value.push(result.data)
      formKhachHang.value.ho_ten = ''
      formKhachHang.value.cccd = ''
      formKhachHang.value.so_dien_thoai = ''
    }
  } catch (error) { alert('Lỗi kết nối máy chủ!') }
}

const xoaKhachHang = async (id, index) => {
  if (!confirm('Xóa người này khỏi danh sách ở ghép?')) return
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/khach-hang/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    })
    if ((await res.json()).status === 'success') {
      danhSachKhachHang.value.splice(index, 1)
    }
  } catch (error) { alert('Lỗi kết nối!') }
}

onMounted(() => { layDanhSachHopDong(); layDuLieuForm() })
</script>

<template>
  <div class="hd-wrap">

    <transition name="toast">
      <div v-if="thongBao.hien" :class="['toast-noti', thongBao.loai === 'success' ? 'toast-ok' : 'toast-err']">
        {{ thongBao.loai === 'success' ? '✅' : '❌' }} {{ thongBao.noi_dung }}
      </div>
    </transition>

    <div class="hd-header">
      <div>
        <h4 class="hd-title">📋 Quản Lý Hợp Đồng</h4>
      </div>
      <button @click="moModalThem" class="btn-create">
        <span>＋</span> Tạo Hợp Đồng
      </button>
    </div>

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

            <div class="card-main">
              <div class="card-room">🏠 Phòng {{ hd.so_phong }}</div>
              <div class="card-tenant">{{ hd.ten_khach_hang }}</div>
              <div class="card-phone">{{ hd.so_dien_thoai }}</div>
            </div>

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

            <div class="card-actions">
              <button @click="moModalKhachHang(hd)" class="btn-cotenants">
                👥 Khách ở cùng
              </button>
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

    <transition name="modal-fade">
      <div v-if="hienThiModal" class="modal-bg" @click.self="hienThiModal = false">
        <div class="modal-box" style="border: none; border-radius: 12px; overflow: hidden;">

          <div class="modal-hd" style="background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%); border-bottom: none; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; color: white;">
            <h5 class="modal-title text-white fw-bold m-0" style="font-size: 1.1rem;">📋 Tạo Hợp Đồng Thuê Phòng</h5>
            <button @click="hienThiModal = false" class="btn btn-sm btn-outline-light fw-bold">✕ Đóng</button>
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

    <transition name="modal-fade">
      <div v-if="hienThiModalKhachHang" class="khach-hang-overlay" @click.self="hienThiModalKhachHang = false">
        <div class="khach-hang-modal">
          <div class="khach-hang-header">
            <h5 class="khach-hang-title">👥 Người Ở Cùng — Phòng {{ hopDongDangChonKhach?.so_phong }}</h5>
            <button @click="hienThiModalKhachHang = false" class="khach-hang-close">✕</button>
          </div>

          <div class="khach-hang-table-wrapper">
            <table class="khach-hang-table">
              <thead>
                <tr>
                  <th>Họ Tên</th>
                  <th>CCCD</th>
                  <th>Điện Thoại</th>
                  <th class="text-end">Xóa</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="danhSachKhachHang.length === 0">
                  <td colspan="4" class="khach-hang-empty">Chưa có khách ở ghép.</td>
                </tr>
                <tr v-for="(kh, index) in danhSachKhachHang" :key="kh.id">
                  <td class="khach-hang-fw-bold">{{ kh.ho_ten }}</td>
                  <td>{{ kh.cccd || '---' }}</td>
                  <td>{{ kh.so_dien_thoai || '---' }}</td>
                  <td class="text-end">
                    <button @click="xoaKhachHang(kh.id, index)" class="khach-hang-btn-delete">✕</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <form @submit.prevent="luuKhachHang" class="khach-hang-form">
            <div class="khach-hang-row">
              <div class="khach-hang-col-4">
                <label class="khach-hang-label">Họ và Tên (*)</label>
                <input v-model="formKhachHang.ho_ten" type="text" class="khach-hang-input" required>
              </div>
              <div class="khach-hang-col-3">
                <label class="khach-hang-label">CCCD</label>
                <input v-model="formKhachHang.cccd" type="text" class="khach-hang-input">
              </div>
              <div class="khach-hang-col-3">
                <label class="khach-hang-label">Điện Thoại</label>
                <input v-model="formKhachHang.so_dien_thoai" type="text" class="khach-hang-input">
              </div>
              <div class="khach-hang-col-2 khach-hang-flex-end">
                <button type="submit" class="khach-hang-btn-add">+ Thêm</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </transition>

  </div>
</template>

<style scoped>
@import "../assets/css/quan-ly-hop-dong.css";
</style>