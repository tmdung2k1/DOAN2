<script setup>
    defineProps({
        phong: Object
    })

    const dinhDangTien = (soTien) => {
        return new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(soTien)
    }
</script>

<template>
   <div class="card h-100 shadow-sm room-card">
    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=600" class="card-img-top room-img" alt="Ảnh phòng trọ">
    
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="badge bg-purple-light text-purple fw-bold px-3 py-2">{{ phong.ten_loai }}</span>
        <span class="text-success fw-bold small text-uppercase">Trống</span>
      </div>
      
      <h5 class="card-title fw-bold text-dark-blue mb-3">Phòng số: {{ phong.so_phong }}</h5>
      
      <!-- Thông số kích thước -->
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
    /*Gọi file CSS riêng biệt từ thư mục assets*/
    @import '../assets/css/room.css';
</style>