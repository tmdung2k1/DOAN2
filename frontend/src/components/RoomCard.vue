<script setup>
    import phong1 from '../assets/images/phong_1.png'
    import phong2 from '../assets/images/phong_2.png'
    import phong3 from '../assets/images/phong_3.png'
    import phong4 from '../assets/images/phong_4.png'
    import phong5 from '../assets/images/phong_5.png'

    const props = defineProps({
        phong: Object
    })

    const anhPhongMap = [phong1, phong2, phong3, phong4, phong5]
    const layAnhPhong = (id) => {
        return anhPhongMap[(id || 0) % anhPhongMap.length]
    }

    const dinhDangTien = (soTien) => {
        return new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(soTien)
    }
</script>

<template>
   <div class="card h-100 shadow-sm room-card">
    <img :src="layAnhPhong(phong.id)" class="card-img-top room-img" alt="Ảnh phòng trọ" style="height: 220px; object-fit: cover;">
    
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="badge bg-purple-light text-purple fw-bold px-3 py-2">{{ phong.ten_loai }}</span>
        <span class="text-success fw-bold small text-uppercase">Trống</span>
      </div>
      
      <h5 class="card-title fw-bold text-dark-blue mb-3">Phòng số: {{ phong.so_phong }}</h5>
      
      <div class="p-3 bg-light rounded mb-3 border-start border-4 border-primary">
        <div class="small text-muted mb-1">Thông số diện tích:</div>
        <div class="fw-bold text-dark-blue fs-5">{{ phong.dien_tich }} m²</div>
        <div class="small text-muted mt-1">
          (Dài: {{ phong.chieu_dai ?? '5.0' }}m × Rộng: {{ phong.chieu_rong ?? '5.0' }}m)
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
          <div class="small text-muted">Giá thuê / tháng</div>
          <div class="fs-5 fw-bold text-purple">{{ dinhDangTien(phong.gia_thue) }}</div>
        </div>
        <button class="btn btn-dark-blue px-4 py-2 fw-bold text-uppercase shadow-sm">
          Đặt phòng
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
    @import '../assets/css/room.css';
</style>