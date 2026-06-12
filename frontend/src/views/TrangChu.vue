<script setup>
import { ref, onMounted, computed } from "vue";
import RoomCard from "../components/RoomCard.vue";

const danhSachPhong = ref([]);
const dangTai = ref(true);

const timSoPhong = ref("");
const timGiaMax = ref("");
const timDienTich = ref("");

const layDanhSachPhong = async () => {
  try {
    const response = await fetch("http://127.0.0.1:8000/api/phong-trong");
    const result = await response.json();
    if (result.status === "success") {
      danhSachPhong.value = result.data;
    }
  } catch (error) {
    console.error("Lỗi kết nối API:", error);
  } finally {
    dangTai.value = false;
  }
};

const danhSachPhongDaLoc = computed(() => {
  return danhSachPhong.value.filter((phong) => {
    const khopSoPhong = phong.so_phong
      .toLowerCase()
      .includes(timSoPhong.value.toLowerCase());
    const khopGia = timGiaMax.value
      ? parseFloat(phong.gia_thue) <= parseFloat(timGiaMax.value)
      : true;
    const khopDienTich = timDienTich.value
      ? parseFloat(phong.dien_tich) >= parseFloat(timDienTich.value)
      : true;
    return khopSoPhong && khopGia && khopDienTich;
  });
});

onMounted(() => layDanhSachPhong());
</script>

<template>
  <nav class="navbar navbar-dark bg-dark-blue py-3 shadow-sm">
    <div class="container">
      <span class="navbar-brand fw-bold text-uppercase">Hệ Thống Nhà Trọ Cao Cấp</span>
      <span class="badge bg-purple px-3 py-2">Trang Khách Thuê</span>
    </div>
  </nav>

  <header class="hero-section text-white text-center d-flex align-items-center">
    <div class="container">
      <h1 class="display-4 fw-bold mb-3 fade-in">Tìm Phòng Trọ Ưng Ý Ngay Hôm Nay</h1>
      <p class="lead mb-4">
        Hệ thống phòng trọ an ninh, hiện đại, hỗ trợ xem phòng 3D và thanh toán trực
        tuyến.
      </p>
    </div>
  </header>

  <main class="container my-5">
    <!-- Bộ Lọc -->
    <div class="card p-4 mb-5 shadow-sm border-0 filter-box">
      <div class="border-bottom pb-2 mb-3">
        <h5 class="fw-bold text-dark-blue m-0">Bộ Lọc Tìm Kiếm Phòng Trọ</h5>
      </div>

      <div class="row g-3">
        <div class="col-12 col-md-4">
          <label class="form-label small fw-bold text-dark-blue text-uppercase"
            >Nhập số phòng</label
          >
          <input
            v-model="timSoPhong"
            type="text"
            class="form-control p-2 border-2 custom-input"
            placeholder="Ví dụ: 101, 202..."
          />
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label small fw-bold text-dark-blue text-uppercase"
            >Giá thuê tối đa</label
          >
          <select v-model="timGiaMax" class="form-select p-2 border-2 custom-input">
            <option value="">Tất cả các mức giá</option>
            <option value="2000000">Dưới 2.000.000 đ</option>
            <option value="3000000">Dưới 3.000.000 đ</option>
            <option value="4000000">Dưới 4.000.000 đ</option>
          </select>
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label small fw-bold text-dark-blue text-uppercase"
            >Diện tích tối thiểu</label
          >
          <select v-model="timDienTich" class="form-select p-2 border-2 custom-input">
            <option value="">Tất cả kích thước</option>
            <option value="20">Từ 20 m² trở lên</option>
            <option value="25">Từ 25 m² trở lên</option>
            <option value="30">Từ 30 m² trở lên</option>
          </select>
        </div>
      </div>

      <div
        class="d-flex justify-content-end mt-3"
        v-if="timSoPhong || timGiaMax || timDienTich"
      >
        <button
          @click="
            timSoPhong = '';
            timGiaMax = '';
            timDienTich = '';
          "
          class="btn btn-sm btn-link text-purple fw-bold text-decoration-none"
        >
          Xóa tất cả bộ lọc [X]
        </button>
      </div>
    </div>

    <!-- Kết quả -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold text-dark-blue m-0">
        Kết quả tìm kiếm ({{ danhSachPhongDaLoc.length }})
      </h3>
    </div>

    <div v-if="dangTai" class="text-center my-5 text-purple fw-bold">
      Đang kết nối cơ sở dữ liệu...
    </div>
    <div
      v-else-if="danhSachPhongDaLoc.length === 0"
      class="alert alert-warning text-center border-0 py-4 shadow-sm fw-bold"
    >
      Không tìm thấy phòng phù hợp.
    </div>

    <div v-else class="row g-4">
      <div
        v-for="phong in danhSachPhongDaLoc"
        :key="phong.id"
        class="col-12 col-md-6 col-lg-4"
      >
        <!-- Tái sử dụng Component -->
        <RoomCard :phong="phong" />
      </div>
    </div>
  </main>
</template>

<style scoped>
/* Gọi file CSS riêng biệt từ thư mục assets */
@import "../assets/css/app.css";
</style>
