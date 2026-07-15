<script setup>
import { ref, onMounted } from 'vue'

const danhSach = ref([])
const dangTai = ref(true)
const token = localStorage.getItem('admin_token')

const hienThiModal = ref(false)
const dangSua = ref(false)
const suaId = ref(null)
const form = ref({ ten_dich_vu: '', don_gia: '' })

const layDanhSach = async () => {
  dangTai.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/admin/dich-vu', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') danhSach.value = result.data
  } catch (e) { console.error(e) }
  dangTai.value = false
}

const moModalThem = () => {
  dangSua.value = false
  suaId.value = null
  form.value = { ten_dich_vu: '', don_gia: '' }
  hienThiModal.value = true
}

const moModalSua = (dv) => {
  dangSua.value = true
  suaId.value = dv.id
  form.value = { ten_dich_vu: dv.ten_dich_vu, don_gia: dv.don_gia }
  hienThiModal.value = true
}

const luu = async () => {
  const url = dangSua.value
    ? `http://127.0.0.1:8000/api/admin/dich-vu/${suaId.value}`
    : 'http://127.0.0.1:8000/api/admin/dich-vu'
  const method = dangSua.value ? 'PUT' : 'POST'
  try {
    const res = await fetch(url, {
      method,
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(form.value)
    })
    const result = await res.json()
    if (result.status === 'success') {
      hienThiModal.value = false
      layDanhSach()
    } else {
      const msg = result.errors ? Object.values(result.errors)[0][0] : (result.message || 'Lỗi!')
      alert('Lỗi: ' + msg)
    }
  } catch (e) { alert('Lỗi kết nối!') }
}

const xoa = async (id) => {
  if (!confirm('Xóa dịch vụ này?')) return
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/admin/dich-vu/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const result = await res.json()
    if (result.status === 'success') layDanhSach()
  } catch (e) { alert('Lỗi kết nối!') }
}

const fmt = (n) => new Intl.NumberFormat('vi-VN').format(n) + ' đ'

onMounted(() => layDanhSach())
</script>

<template>
  <div class="dv-wrap">
    <div class="dv-header">
      <div>
        <h4 class="dv-title">⚙️ Quản Lý Dịch Vụ</h4>
      </div>
      <button @click="moModalThem" class="dv-btn-new">
        <span>＋</span> Thêm Dịch Vụ
      </button>
    </div>

    <div v-if="dangTai" class="dv-loading">
      <div class="dv-spinner"></div> Đang tải...
    </div>

    <div v-else-if="danhSach.length === 0" class="dv-empty">
      <div class="dv-empty-icon">📦</div>
      <p>Chưa có dịch vụ nào. Thêm dịch vụ để tự động áp dụng khi lập hóa đơn.</p>
    </div>

    <div v-else class="dv-list">
      <div v-for="dv in danhSach" :key="dv.id" class="dv-card">
        <div class="dv-card-icon">⚙️</div>
        <div class="dv-card-info">
          <div class="dv-card-name">{{ dv.ten_dich_vu }}</div>
        </div>
        <div class="dv-card-price">{{ fmt(dv.don_gia) }}</div>
        <div class="dv-card-lbl">/ tháng</div>
        <div class="dv-card-actions">
          <button @click="moModalSua(dv)" class="dv-btn-edit" title="Sửa">✏️</button>
          <button @click="xoa(dv.id)" class="dv-btn-del" title="Xóa">🗑️</button>
        </div>
      </div>
    </div>

    <transition name="modal">
      <div v-if="hienThiModal" class="dv-modal-bg" @click.self="hienThiModal = false">
        <div class="dv-modal" style="border: none; border-radius: 12px; overflow: hidden; max-width: 500px; width: 90%;">
          <div class="dv-modal-hd" style="background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%); border-bottom: none; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; color: white;">
            <h5 style="margin: 0; font-weight: 700; color: white; font-size: 1.1rem;">{{ dangSua ? '✏️ Sửa Dịch Vụ' : '＋ Thêm Dịch Vụ Mới' }}</h5>
            <button @click="hienThiModal = false" class="btn btn-sm btn-outline-light fw-bold">✕ Đóng</button>
          </div>
          <form @submit.prevent="luu" class="dv-modal-body">
            <div class="dv-form-group">
              <label>Tên dịch vụ <span class="dv-required">*</span></label>
              <input v-model="form.ten_dich_vu" type="text" class="dv-input" placeholder="VD: Tiền rác, Wifi, Giữ xe..." required>
            </div>
            <div class="dv-form-group">
              <label>Đơn giá (đ/tháng) <span class="dv-required">*</span></label>
              <input v-model.number="form.don_gia" type="number" min="0" step="1000" class="dv-input" placeholder="VD: 20000" required>
            </div>
            <div class="dv-modal-footer">
              <button type="button" @click="hienThiModal = false" class="dv-btn-cancel">Hủy</button>
              <button type="submit" class="dv-btn-save">{{ dangSua ? '💾 Cập nhật' : '➕ Thêm mới' }}</button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
@import '../assets/css/quan-ly-dich-vu.css';
</style>
