<script setup>
import { ref, onMounted, computed, onUnmounted, nextTick } from "vue";
import logoNhaTro from "../assets/images/logo_nha_tro.png";
import heroRoom from "../assets/images/hero_room.png";
import phong1 from "../assets/images/phong_1.png";
import phong2 from "../assets/images/phong_2.png";
import phong3 from "../assets/images/phong_3.png";
import phong4 from "../assets/images/phong_4.png";
import phong5 from "../assets/images/phong_5.png";

const danhSachPhong = ref([]);
const dangTai = ref(true);
const navScrolled = ref(false);
const mobileMenuOpen = ref(false);

const timGiaMax = ref("");
const timDienTichMax = ref("");

const anhPhongMap = [phong1, phong2, phong3, phong4, phong5];
const layAnhPhong = (id) => anhPhongMap[(id || 0) % anhPhongMap.length];

const dinhDangTien = (soTien) => {
  const so = parseFloat(soTien);
  if (so >= 1000000) {
    return (so / 1000000).toFixed(1).replace('.0', '') + 'tr';
  }
  return new Intl.NumberFormat("vi-VN").format(so);
};

const dinhDangTienDay = (soTien) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(soTien);
};

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
    // Re-observe animations cho các element mới render sau khi data load xong
    nextTick(() => setTimeout(observeAnimations, 100));
  }
};

const danhSachPhongDaLoc = computed(() => {
  return danhSachPhong.value.filter((phong) => {
    const khopGia = timGiaMax.value
      ? parseFloat(phong.gia_thue) <= parseFloat(timGiaMax.value)
      : true;
    const khopDienTich = timDienTichMax.value
      ? parseFloat(phong.dien_tich) <= parseFloat(timDienTichMax.value)
      : true;
    return khopGia && khopDienTich;
  });
});

const phongHienThi = computed(() => danhSachPhongDaLoc.value.slice(0, 4));
const phongNoiBat = computed(() => danhSachPhong.value.slice(0, 3));

const handleScroll = () => {
  navScrolled.value = window.scrollY > 20;
};

const observeAnimations = () => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
        }
      });
    },
    { threshold: 0.1 }
  );

  document.querySelectorAll(".lp-animate-in").forEach((el) => {
    observer.observe(el);
  });
};

const scrollToRooms = () => {
  document.getElementById("phong-tro")?.scrollIntoView({ behavior: "smooth" });
};

const scrollToFeatured = () => {
  document.getElementById("noi-bat")?.scrollIntoView({ behavior: "smooth" });
};

// === Đặt phòng ===
const showBookingModal = ref(false);
const bookingSuccess = ref(false);
const bookingMaDatPhong = ref("");
const bookingLoading = ref(false);
const bookingError = ref("");
const selectedPhong = ref(null);

const bookingForm = ref({
  ho_ten: "",
  so_dien_thoai: "",
  email: "",
  ngay_du_kien_den: "",
  ghi_chu: "",
});

const moModalDatPhong = (phong) => {
  selectedPhong.value = phong;
  bookingForm.value = { ho_ten: "", so_dien_thoai: "", email: "", ngay_du_kien_den: "", ghi_chu: "" };
  bookingError.value = "";
  bookingSuccess.value = false;
  bookingMaDatPhong.value = "";
  showBookingModal.value = true;
  document.body.style.overflow = "hidden";
};

const dongModal = () => {
  showBookingModal.value = false;
  document.body.style.overflow = "";
};

const guiDatPhong = async () => {
  bookingError.value = "";

  if (!bookingForm.value.ho_ten.trim()) {
    bookingError.value = "Vui lòng nhập họ tên.";
    return;
  }
  if (!bookingForm.value.so_dien_thoai.trim()) {
    bookingError.value = "Vui lòng nhập số điện thoại.";
    return;
  }
  if (!bookingForm.value.ngay_du_kien_den) {
    bookingError.value = "Vui lòng chọn ngày dự kiến đến.";
    return;
  }

  bookingLoading.value = true;
  try {
    const res = await fetch("http://127.0.0.1:8000/api/dat-phong", {
      method: "POST",
      headers: { "Content-Type": "application/json", "Accept": "application/json" },
      body: JSON.stringify({
        phong_id: selectedPhong.value.id,
        ...bookingForm.value,
      }),
    });
    const result = await res.json();
    if (result.status === "success") {
      bookingSuccess.value = true;
      bookingMaDatPhong.value = result.ma_dat_phong;
    } else {
      bookingError.value = result.message || "Có lỗi xảy ra, vui lòng thử lại.";
    }
  } catch (err) {
    bookingError.value = "Không thể kết nối đến máy chủ. Vui lòng thử lại sau.";
  } finally {
    bookingLoading.value = false;
  }
};

const ngayHomNay = computed(() => {
  const d = new Date();
  return d.toISOString().split("T")[0];
});

onMounted(() => {
  layDanhSachPhong();
  window.addEventListener("scroll", handleScroll);
  setTimeout(observeAnimations, 500);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});

const testimonials = [
  {
    name: "Nguyễn Thu Hà",
    role: "Sinh viên năm 3",
    initial: "H",
    text: '"Tìm phòng qua hệ thống rất nhanh, chỉ trong 2 ngày mình đã tìm được căn phòng ưng ý gần trường. Hình ảnh thực tế giống hệt bên ngoài."',
  },
  {
    name: "Trần Minh Quân",
    role: "Lập trình viên",
    initial: "Q",
    text: '"Dịch vụ hỗ trợ rất tốt. Các bạn tư vấn nhiệt tình, giúp mình thương lượng giá với chủ nhà. Minh bạch và uy tín!"',
  },
  {
    name: "Lê Thị Mai",
    role: "Kế toán viên",
    initial: "M",
    text: '"Mình thích cách lọc phòng theo giá và tiện ích trên web. Rất tiện lợi và tiết kiệm thời gian đi xem phòng."',
  },
];

const getRoomTag = (index) => {
  const tags = [
    { text: "MỚI", class: "new" },
    { text: "GIÁ TỐT", class: "good-price" },
    null,
    null,
  ];
  return tags[index % tags.length];
};
</script>

<template>
  <div class="landing-page">
    <nav class="lp-navbar" :class="{ scrolled: navScrolled }">
      <div class="lp-navbar-inner">
        <a href="/" class="lp-logo">
          <img :src="logoNhaTro" alt="Logo TMD" />
          TMD
        </a>

        <button
          class="lp-mobile-toggle"
          @click="mobileMenuOpen = !mobileMenuOpen"
          aria-label="Menu"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path v-if="!mobileMenuOpen" d="M3 12h18M3 6h18M3 18h18" />
            <path v-else d="M18 6L6 18M6 6l12 12" />
          </svg>
        </button>

        <ul class="lp-nav-links" :class="{ show: mobileMenuOpen }">
          <li><a href="#phong-tro" class="active" @click.prevent="scrollToRooms">Tìm kiếm</a></li>
          <li><a href="#noi-bat" @click.prevent="scrollToFeatured">Phòng nổi bật</a></li>
          <li>
            <a href="tel:0399049011" class="lp-hotline">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="vertical-align: middle;">
                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
              </svg>
              Hotline:0399049011
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <section class="lp-hero">
      <div class="lp-hero-inner">
        <div class="lp-hero-content">
          <h1 class="lp-hero-title">
            Tìm phòng trọ ưng ý,<br />
            <em>khởi đầu hành trình mới</em>
          </h1>
          <p class="lp-hero-subtitle">
            Hệ thống phòng trọ chất lượng, giá cả phải chăng. Chúng tôi mang
            lại sự an tâm tuyệt đối cho bạn.
          </p>

          <div class="lp-search-bar">
            <div class="lp-search-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16" />
              </svg>
              <select v-model="timGiaMax">
                <option value="">Mọi mức giá</option>
                <option value="2000000">Dưới 2 triệu</option>
                <option value="3000000">Dưới 3 triệu</option>
                <option value="4000000">Dưới 4 triệu</option>
              </select>
            </div>
            <div class="lp-search-divider"></div>
            <div class="lp-search-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" />
              </svg>
              <select v-model="timDienTichMax">
                <option value="">Mọi diện tích</option>
                <option value="15">Dưới 15 m²</option>
                <option value="20">Dưới 20 m²</option>
                <option value="25">Dưới 25 m²</option>
                <option value="30">Dưới 30 m²</option>
              </select>
            </div>
            <button class="lp-search-btn" @click="scrollToRooms">Tìm ngay</button>
          </div>
        </div>

        <div class="lp-hero-visual">
          <div class="lp-hero-img-wrapper">
            <img :src="heroRoom" alt="Phòng trọ hiện đại" />
          </div>
          <div class="lp-hero-badge">
            <div class="lp-hero-badge-number">
              {{ danhSachPhong.length }}<span>+</span>
            </div>
            <div class="lp-hero-badge-label">Phòng đang trống</div>
          </div>
        </div>
      </div>
    </section>

    <section class="lp-features">
      <div class="lp-features-inner">
        <div class="lp-feature-card lp-animate-in">
          <div class="lp-feature-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M9 12l2 2 4-4" />
              <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
            </svg>
          </div>
          <div class="lp-feature-title">Xác thực 100%</div>
          <div class="lp-feature-desc">
            Mọi thông tin đều được kiểm duyệt kỹ lưỡng về hình ảnh và thông tin phòng.
          </div>
        </div>

        <div class="lp-feature-card lp-animate-in">
          <div class="lp-feature-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z" />
              <line x1="7" y1="7" x2="7.01" y2="7" />
            </svg>
          </div>
          <div class="lp-feature-title">Giá cả minh bạch</div>
          <div class="lp-feature-desc">
            Cam kết không thu phí môi giới, giá thuê được niêm yết công khai và chính xác.
          </div>
        </div>

        <div class="lp-feature-card lp-animate-in">
          <div class="lp-feature-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
            </svg>
          </div>
          <div class="lp-feature-title">Hỗ trợ 24/7</div>
          <div class="lp-feature-desc">
            Đội ngũ hỗ trợ nhiệt tình, giải đáp mọi thắc mắc của bạn bất cứ lúc nào.
          </div>
        </div>
      </div>
    </section>

    <section class="lp-stats">
      <div class="lp-stats-inner">
        <div class="lp-stat-item lp-animate-in">
          <div class="lp-stat-number">{{ danhSachPhong.length }}+</div>
          <div class="lp-stat-label">Phòng trống</div>
        </div>
        <div class="lp-stat-item lp-animate-in">
          <div class="lp-stat-number">100+</div>
          <div class="lp-stat-label">Khách thuê</div>
        </div>
        <div class="lp-stat-item lp-animate-in">
          <div class="lp-stat-number">98%</div>
          <div class="lp-stat-label">Hài lòng</div>
        </div>
      </div>
    </section>

    <section class="lp-rooms" id="phong-tro">
      <div class="lp-rooms-inner">
        <div class="lp-section-header lp-animate-in">
          <div>
            <h2 class="lp-section-title">Phòng mới đăng tải</h2>
            <p class="lp-section-subtitle">
              Những lựa chọn tuyệt vời nhất vừa được cập nhật
            </p>
          </div>
          <a href="#" class="lp-section-link" @click.prevent>
            Xem tất cả →
          </a>
        </div>

        <div v-if="dangTai" class="lp-loading">
          <div class="lp-spinner"></div>
          <span>Đang tải phòng trống...</span>
        </div>
        <div v-else-if="danhSachPhongDaLoc.length === 0" class="lp-no-result">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="11" cy="11" r="8" />
            <path d="M21 21l-4.35-4.35" />
          </svg>
          <p>Không tìm thấy phòng phù hợp.</p>
        </div>

        <div v-else class="lp-rooms-grid">
          <div
            v-for="(phong, index) in phongHienThi"
            :key="phong.id"
            class="lp-room-card lp-animate-in"
          >
            <div class="lp-room-img-wrap">
              <img :src="layAnhPhong(phong.id)" :alt="'Phòng ' + phong.so_phong" />
              <span v-if="getRoomTag(index)" class="lp-room-tag" :class="getRoomTag(index).class">
                {{ getRoomTag(index).text }}
              </span>
            </div>
            <div class="lp-room-body">
              <div class="lp-room-price">
                <span class="lp-room-price-value">{{ dinhDangTien(phong.gia_thue) }}</span>
                <span class="lp-room-price-unit">/tháng</span>
              </div>
              <div class="lp-room-name">Phòng {{ phong.so_phong }} - {{ phong.ten_loai }}</div>
              <div class="lp-room-location">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
                Hệ thống nhà trọ TMD
              </div>
              <div class="lp-room-meta">
                <span class="lp-room-meta-item">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                  </svg>
                  {{ phong.dien_tich }} m²
                </span>
                <span class="lp-room-meta-item">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16" />
                    <path d="M9 21V9h6v12" />
                  </svg>
                  {{ phong.ten_loai }}
                </span>
              </div>
              <button class="lp-book-btn" @click="moModalDatPhong(phong)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                  <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                  <circle cx="8.5" cy="7" r="4" />
                  <line x1="20" y1="8" x2="20" y2="14" />
                  <line x1="23" y1="11" x2="17" y2="11" />
                </svg>
                Đặt phòng
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="lp-featured" id="noi-bat">
      <div class="lp-featured-inner">
        <h2 class="lp-featured-title lp-animate-in">Bộ sưu tập nổi bật</h2>


        <div class="lp-featured-stack lp-animate-in" v-if="phongNoiBat.length > 0">
          <div
            v-for="(phong, index) in phongNoiBat"
            :key="'featured-' + phong.id"
            class="lp-featured-card"
          >
            <img :src="layAnhPhong(phong.id)" :alt="'Phòng ' + phong.so_phong" />
            <div class="lp-featured-card-body">
              <div class="lp-featured-card-price">
                {{ dinhDangTien(phong.gia_thue) }} <span>/tháng</span>
              </div>
              <div class="lp-featured-card-name">
                Phòng {{ phong.so_phong }} - {{ phong.ten_loai }}
              </div>
              <div class="lp-featured-card-loc">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
                Nhà trọ TMD
              </div>
              <button class="lp-featured-book-btn" @click="moModalDatPhong(phong)">Đặt phòng ngay</button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="lp-testimonials">
      <div class="lp-testimonials-inner">
        <h2 class="lp-testimonials-title lp-animate-in">Cảm nhận từ khách hàng</h2>
        <p class="lp-testimonials-subtitle lp-animate-in">
          Trải nghiệm thực tế từ những người đã tìm thấy tổ ấm qua hệ thống TMD
        </p>

        <div class="lp-testimonials-grid">
          <div
            v-for="(item, index) in testimonials"
            :key="index"
            class="lp-testimonial-card lp-animate-in"
          >
            <div class="lp-testimonial-header">
              <div class="lp-testimonial-author">
                <div class="lp-testimonial-avatar">{{ item.initial }}</div>
                <div>
                  <div class="lp-testimonial-name">{{ item.name }}</div>
                  <div class="lp-testimonial-role">{{ item.role }}</div>
                </div>
              </div>
              <div class="lp-testimonial-quote-icon">❝</div>
            </div>
            <p class="lp-testimonial-text">{{ item.text }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="lp-cta">
      <div class="lp-cta-inner">
        <div class="lp-cta-banner lp-animate-in">
          <h2 class="lp-cta-title">Bạn đang tìm phòng trọ?</h2>
          <p class="lp-cta-desc">
            Khám phá ngay hệ thống TMD với hàng ngàn phòng trọ chất lượng, giá cả phải chăng và dịch vụ hỗ trợ tận tình.
          </p>
          <div class="lp-cta-buttons">
            <a href="#phong-tro" class="lp-cta-btn lp-cta-btn-primary" @click.prevent="scrollToRooms">
              Xem phòng ngay
            </a>
            <a href="#noi-bat" class="lp-cta-btn lp-cta-btn-secondary" @click.prevent="scrollToFeatured">
              Phòng nổi bật
            </a>
          </div>
        </div>
      </div>
    </section>

    <footer class="lp-footer">
      <div class="lp-footer-inner">
        <div>
          <div class="lp-footer-brand">TMD</div>
          <p class="lp-footer-desc">
            Nền tảng tìm kiếm và cho thuê phòng trọ hàng đầu, mang đến trải nghiệm tốt nhất cho khách thuê và chủ trọ.
          </p>
        </div>

        <div>
          <div class="lp-footer-col-title">Khám phá</div>
          <ul class="lp-footer-links">
            <li><a href="#phong-tro" @click.prevent="scrollToRooms">Tìm phòng trọ</a></li>
            <li><a href="#noi-bat" @click.prevent="scrollToFeatured">Phòng nổi bật</a></li>

          </ul>
        </div>

        <div>
          <div class="lp-footer-col-title">Hỗ trợ</div>
          <ul class="lp-footer-links">
            <li><a href="#">Điều khoản</a></li>
            <li><a href="#">Chính sách bảo mật</a></li>
            <li><a href="#">Liên hệ</a></li>
          </ul>
        </div>

        <div>
          <div class="lp-footer-col-title">Kết nối</div>
          <div class="lp-footer-social">
            <a href="#" aria-label="Website">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" />
              </svg>
            </a>
            <a href="#" aria-label="Email">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="4" width="20" height="16" rx="2" />
                <path d="M22 7l-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7" />
              </svg>
            </a>
            <a href="#" aria-label="Phone">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
              </svg>
            </a>
          </div>
        </div>
      </div>

      <div class="lp-footer-bottom">
        © {{ new Date().getFullYear() }} Hệ thống cho thuê nhà trọ TMD. All rights reserved.
      </div>
    </footer>

    <!-- Modal Đặt Phòng -->
    <Teleport to="body">
      <div v-if="showBookingModal" class="booking-overlay" @click.self="dongModal">
        <div class="booking-modal" :class="{ 'booking-modal-success': bookingSuccess }">
          <!-- Header -->
          <div class="booking-modal-header">
            <h3 v-if="!bookingSuccess">Đặt phòng</h3>
            <h3 v-else>Thành công!</h3>
            <button class="booking-close-btn" @click="dongModal">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                <path d="M18 6L6 18M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Nội dung thành công -->
          <div v-if="bookingSuccess" class="booking-success">
            <div class="booking-success-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="48" height="48">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
            </div>
            <p class="booking-success-title">Đặt phòng thành công!</p>
            <p class="booking-success-desc">
              Mã đặt phòng của bạn là: <strong>{{ bookingMaDatPhong }}</strong>
            </p>
            <p class="booking-success-note">
              Chúng tôi sẽ liên hệ qua số điện thoại bạn đã cung cấp để xác nhận trong thời gian sớm nhất.
            </p>
            <button class="booking-done-btn" @click="dongModal">Đóng</button>
          </div>

          <!-- Form đặt phòng -->
          <div v-else class="booking-body">
            <!-- Thông tin phòng -->
            <div class="booking-room-info" v-if="selectedPhong">
              <img :src="layAnhPhong(selectedPhong.id)" :alt="'Phòng ' + selectedPhong.so_phong" />
              <div class="booking-room-details">
                <div class="booking-room-name">Phòng {{ selectedPhong.so_phong }} - {{ selectedPhong.ten_loai }}</div>
                <div class="booking-room-price">{{ dinhDangTienDay(selectedPhong.gia_thue) }}/tháng</div>
                <div class="booking-room-area">{{ selectedPhong.dien_tich }} m²</div>
              </div>
            </div>

            <!-- Form nhập liệu -->
            <div class="booking-form">
              <div class="booking-field">
                <label for="booking-name">Họ và tên <span class="required">*</span></label>
                <input id="booking-name" v-model="bookingForm.ho_ten" type="text" placeholder="Nhập họ tên đầy đủ" />
              </div>
              <div class="booking-field">
                <label for="booking-phone">Số điện thoại <span class="required">*</span></label>
                <input id="booking-phone" v-model="bookingForm.so_dien_thoai" type="tel" placeholder="VD: 0399049011" />
              </div>
              <div class="booking-field">
                <label for="booking-email">Email <span class="optional">(không bắt buộc)</span></label>
                <input id="booking-email" v-model="bookingForm.email" type="email" placeholder="email@example.com" />
              </div>
              <div class="booking-field">
                <label for="booking-date">Ngày dự kiến đến <span class="required">*</span></label>
                <input id="booking-date" v-model="bookingForm.ngay_du_kien_den" type="date" :min="ngayHomNay" />
              </div>
              <div class="booking-field">
                <label for="booking-note">Ghi chú <span class="optional">(không bắt buộc)</span></label>
                <textarea id="booking-note" v-model="bookingForm.ghi_chu" rows="3" placeholder="Yêu cầu thêm, thời gian liên hệ..."></textarea>
              </div>

              <div v-if="bookingError" class="booking-error">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="15" y1="9" x2="9" y2="15" />
                  <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
                {{ bookingError }}
              </div>

              <button class="booking-submit-btn" @click="guiDatPhong" :disabled="bookingLoading">
                <span v-if="bookingLoading" class="booking-spinner"></span>
                <span v-else>Gửi yêu cầu đặt phòng</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style>
@import "../assets/css/trang-chu.css";
</style>
