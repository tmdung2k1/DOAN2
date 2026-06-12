<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const email = ref("");
const matKhau = ref("");
const isLoading = ref(false);
const thongBao = ref({ hienthi: false, loi: false, noiDung: "" });

const xuLyDangNhap = async () => {
  try {
    isLoading.value = true;
    const response = await fetch("http://127.0.0.1:8000/api/dang-nhap", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        email: email.value,
        mat_khau: matKhau.value,
      }),
    });
    const result = await response.json();
    if (result.status === "success") {
      thongBao.value = {
        hienthi: true,
        loi: false,
        noiDung: "Đăng nhập thành công! Đang chuyển hướng...",
      };
      // Lưu chìa khóa Token vào bộ nhớ trình duyệt
      localStorage.setItem("admin_token", result.token);
      // Chuyển hướng sau 1.5 giây để người dùng thấy thông báo
      setTimeout(() => {
        router.push("/admin");
      }, 1500);
    } else {
      isLoading.value = false;
      thongBao.value = {
        hienthi: true,
        loi: true,
        noiDung: result.message || "Đăng nhập thất bại!",
      };
    }
  } catch (error) {
    isLoading.value = false;
    thongBao.value = {
      hienthi: true,
      loi: true,
      noiDung: "Không thể kết nối đến máy chủ!",
    };
  }
};
</script>
<template>
  <div class="login-container d-flex justify-content-center align-items-center">
    <div class="card p-5 shadow-lg border-0 login-box">
      <div class="text-center mb-4">
        <h2 class="fw-bold text-dark-blue text-uppercase tracking-wider">
          Quản Trị Viên
        </h2>
        <div class="small text-muted">Hệ thống quản lý nhà trọ tập trung</div>
      </div>

      <div
        v-if="thongBao.hienthi"
        :class="[
          'alert',
          thongBao.loi ? 'alert-danger' : 'alert-success',
          'border-0',
          'fw-bold',
          'small',
        ]"
      >
        {{ thongBao.noiDung }}
      </div>

      <form @submit.prevent="xuLyDangNhap">
        <div class="mb-3">
          <label class="form-label small fw-bold text-dark-blue text-uppercase"
            >Email đăng nhập</label
          >
          <input
            v-model="email"
            type="email"
            class="form-control p-3 custom-input"
            placeholder="admin@chutro.com"
            :disabled="isLoading"
            required
          />
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold text-dark-blue text-uppercase"
            >Mật khẩu</label
          >
          <input
            v-model="matKhau"
            type="password"
            class="form-control p-3 custom-input"
            placeholder="Nhập mật khẩu"
            :disabled="isLoading"
            required
          />
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          :class="[
            'btn',
            'btn-dark-blue',
            'w-100',
            'py-3',
            'fw-bold',
            'text-uppercase',
            'shadow-sm',
            { 'loading-btn': isLoading },
          ]"
        >
          <span v-if="!isLoading">Đăng Nhập Hệ Thống</span>
          <span v-else class="loading-content">
            <span class="spinner"></span>
            <span>Đang xử lý...</span>
          </span>
        </button>

        <div class="text-center mt-4">
          <router-link
            v-if="!isLoading"
            to="/"
            class="text-purple small text-decoration-none fw-bold"
          >
            Quay lại trang chủ xem phòng trọ
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.login-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a192f 0%, #663399 100%);
}
.login-box {
  width: 100%;
  max-width: 450px;
  background-color: #ffffff;
  border-radius: 16px;
}
.text-dark-blue {
  color: #0a192f;
}
.text-purple {
  color: #663399;
}
.custom-input {
  border-color: #e1dbec;
  border-radius: 8px;
  background-color: #f8f9fa;
  outline: none;
  transition: all 0.2s ease-in-out;
}
.custom-input:focus {
  border-color: #663399;
  background-color: #ffffff;
  box-shadow: 0 0 0 0.25rem rgba(102, 51, 153, 0.15);
}
.btn-dark-blue {
  background-color: #0a192f;
  color: #ffffff;
  border: none;
  transition: background-color 0.3s ease;
  border-radius: 8px;
}
.btn-dark-blue:hover:not(:disabled) {
  background-color: #663399;
}
.btn-dark-blue:disabled {
  opacity: 0.9;
  cursor: not-allowed;
}
.loading-btn {
  background: linear-gradient(90deg, #0a192f 0%, #663399 100%);
}

/* Loading Spinner Animation */
.spinner {
  display: inline-block;
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid #ffffff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.loading-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Fade in loading state */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
