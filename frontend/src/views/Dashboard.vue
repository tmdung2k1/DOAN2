<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

// Hàm xử lý đăng xuất
const xuLyDangXuat = () => {
  // 1. Xóa chìa khóa (token) khỏi bộ nhớ
  localStorage.removeItem("admin_token");
  // 2. Đẩy người dùng về lại trang đăng nhập
  router.push("/dang-nhap");
};
</script>

<template>
  <div class="d-flex dashboard-layout">
    <!-- Cột trái: Thanh Menu (Sidebar) -->
    <div class="bg-dark-blue text-white p-3 sidebar">
      <h4 class="fw-bold mb-4 mt-2 border-bottom pb-3 text-center text-uppercase">
        Bảng Điều Khiển
      </h4>
      <ul class="nav flex-column gap-2">
        <li class="nav-item">
          <a class="nav-link text-white bg-purple rounded px-3 py-2 fw-bold" href="#"
            >Tổng quan</a
          >
        </li>
        <li class="nav-item">
          <a class="nav-link text-white px-3 py-2" href="#">Quản lý Phòng Trọ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white px-3 py-2" href="#">Duyệt Đặt Phòng</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white px-3 py-2" href="#">Cài đặt Điện/Nước</a>
        </li>
      </ul>
    </div>

    <div class="flex-grow-1 bg-light main-content">
      <!-- Thanh Header (Navbar) -->
      <div
        class="bg-white p-3 shadow-sm d-flex justify-content-between align-items-center mb-4"
      >
        <h5 class="m-0 fw-bold text-dark-blue">Hệ Thống Quản Trị</h5>
        <button
          @click="xuLyDangXuat"
          class="btn btn-sm btn-outline-danger fw-bold text-uppercase px-4"
        >
          Đăng xuất
        </button>
      </div>

      <div class="px-4">
        <h3 class="text-dark-blue fw-bold mb-3">Chào mừng Quản trị viên!</h3>
        <p class="text-muted">
          Hãy chọn một chức năng bên thanh menu trái để bắt đầu thao tác.
        </p>

        <div class="card border-0 shadow-sm p-4 text-center mt-5">
          <h4 class="text-purple fw-bold">Khu vực hiển thị bảng dữ liệu</h4>
          <p class="text-muted m-0">(Chúng ta sẽ làm các tính năng CRUD tại đây)</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard-layout {
  min-height: 100vh;
}
.sidebar {
  width: 260px;
}
.bg-dark-blue {
  background-color: #0a192f !important;
}
.text-dark-blue {
  color: #0a192f !important;
}
.bg-purple {
  background-color: #663399 !important;
}

.nav-link {
  transition: all 0.2s;
}
.nav-link:hover:not(.bg-purple) {
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 6px;
}
</style>
