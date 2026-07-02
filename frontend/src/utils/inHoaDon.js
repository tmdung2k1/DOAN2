/**
 * Tiện ích in hóa đơn cho hệ thống quản lý phòng trọ.
 * Mở cửa sổ in mới với layout hóa đơn chuyên nghiệp.
 *
 * @param {Object} hoaDon   - Thông tin hóa đơn (từ API hoa_don)
 * @param {Array}  chiTiet  - Danh sách chi tiết khoản thu (từ API chi_tiet)
 */

// ── Hằng số ──────────────────────────────────────────────────────────────────

const DICT_LOAI_PHI = {
  tien_phong: 'Tiền phòng',
  tien_dien:  'Tiền điện',
  tien_nuoc:  'Tiền nước',
  rac:        'Tiền rác',
  wifi:       'Tiền Wifi',
  khac:       'Chi phí khác',
}

// ── Helpers ───────────────────────────────────────────────────────────────────

/**
 * Chuyển mã loại phí sang tên hiển thị tiếng Việt.
 * @param {string} ma
 * @returns {string}
 */
const dichLoaiPhi = (ma) => DICT_LOAI_PHI[ma] || ma

/**
 * Format số thành tiền VNĐ (ví dụ: 800,000 đ).
 * @param {number|string} n
 * @returns {string}
 */
const fmtTien = (n) => Number(n).toLocaleString('vi-VN') + ' đ'

/**
 * Format ngày sang định dạng dd/mm/yyyy theo locale Việt Nam.
 * @param {string} d
 * @returns {string}
 */
const fmtNgay = (d) => new Date(d).toLocaleDateString('vi-VN')

/**
 * Format tháng/năm từ chuỗi ngày.
 * @param {string} d
 * @returns {string}
 */
const fmtThang = (d) => {
  const x = new Date(d)
  return `Tháng ${x.getMonth() + 1}/${x.getFullYear()}`
}

// ── Tạo HTML ──────────────────────────────────────────────────────────────────

/**
 * Tạo các dòng tbody cho bảng khoản thu.
 * @param {Array} chiTiet
 * @returns {string}
 */
const taoRowsKhoanThu = (chiTiet) =>
  chiTiet.map((c) => `
    <tr>
      <td>${dichLoaiPhi(c.loai_phi)}</td>
      <td style="text-align:center">${Number(c.so_luong).toLocaleString('vi-VN')}</td>
      <td style="text-align:right">${fmtTien(c.don_gia)}</td>
      <td style="text-align:right;font-weight:700;color:#dc2626">${fmtTien(c.thanh_tien)}</td>
    </tr>`
  ).join('')

/**
 * Trả về toàn bộ chuỗi HTML của trang in hóa đơn.
 * @param {Object} hd
 * @param {Array}  ct
 * @returns {string}
 */
const taoHTMLHoaDon = (hd, ct) => {
  const rows      = taoRowsKhoanThu(ct)
  const trangThai = hd.trang_thai === 'da_thanh_toan' ? 'Đã thu tiền' : 'Chưa thu tiền'
  const ngayThuRow = hd.ngay_thanh_toan
    ? `<div class="info-row"><span>Ngày thu tiền</span><span>${fmtNgay(hd.ngay_thanh_toan)}</span></div>`
    : ''
  const ngayIn = new Date().toLocaleDateString('vi-VN', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
  })

  return `<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hóa Đơn ${hd.ma_hoa_don}</title>
  <style>
    /* ── Reset ── */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    /* ── Layout ── */
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      padding: 40px 48px;
      color: #1e293b;
      background: #fff;
      font-size: 14px;
      line-height: 1.5;
    }

    /* ── Header ── */
    .invoice-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 32px;
      padding-bottom: 20px;
      border-bottom: 2px solid #e2e8f0;
    }
    .company-name {
      font-size: 22px;
      font-weight: 800;
      color: #6366f1;
    }
    .company-sub {
      font-size: 12px;
      color: #94a3b8;
      margin-top: 4px;
    }
    .invoice-meta { text-align: right; }
    .invoice-meta h2 {
      font-size: 30px;
      font-weight: 900;
      color: #1e293b;
      letter-spacing: -1px;
    }
    .invoice-meta .ma-code {
      font-size: 13px;
      color: #6366f1;
      font-family: 'Courier New', monospace;
      margin-top: 4px;
      background: #eff0ff;
      display: inline-block;
      padding: 2px 10px;
      border-radius: 6px;
    }
    .invoice-meta .trang-thai {
      font-size: 12px;
      margin-top: 6px;
      padding: 3px 12px;
      border-radius: 20px;
      font-weight: 700;
      display: inline-block;
    }
    .trang-thai.da-thu { background: #d1fae5; color: #065f46; }
    .trang-thai.chua-thu { background: #fef3c7; color: #92400e; }

    /* ── Info grid ── */
    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 28px;
    }
    .info-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 16px 18px;
    }
    .info-box h4 {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: .8px;
      color: #94a3b8;
      font-weight: 700;
      margin-bottom: 10px;
      border-bottom: 1px solid #e2e8f0;
      padding-bottom: 6px;
    }
    .info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 6px;
      font-size: 13px;
    }
    .info-row span:first-child { color: #64748b; }
    .info-row span:last-child { font-weight: 600; color: #1e293b; }

    /* ── Bảng khoản thu ── */
    .section-title {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .8px;
      color: #6366f1;
      margin-bottom: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      overflow: hidden;
    }
    thead th {
      background: #f8fafc;
      padding: 10px 14px;
      font-size: 11px;
      text-transform: uppercase;
      color: #64748b;
      letter-spacing: .5px;
      border-bottom: 2px solid #e2e8f0;
      text-align: left;
      font-weight: 700;
    }
    tbody td {
      padding: 12px 14px;
      border-bottom: 1px solid #f1f5f9;
      font-size: 13px;
    }
    tbody tr:last-child td { border-bottom: none; }
    .total-row td {
      background: linear-gradient(135deg, #6366f1, #8b5cf6);
      color: #fff;
      padding: 14px 16px;
      font-weight: 800;
      font-size: 15px;
      text-align: right;
    }
    .total-row td:first-child { text-align: right; }

    /* ── Footer / chữ ký ── */
    .page-footer {
      margin-top: 44px;
      border-top: 1px solid #e2e8f0;
      padding-top: 16px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }
    .print-date {
      font-size: 12px;
      color: #94a3b8;
    }
    .sign-area {
      display: flex;
      gap: 80px;
    }
    .sign-box {
      text-align: center;
      min-width: 140px;
    }
    .sign-box .sign-label {
      font-size: 12px;
      color: #475569;
      margin-bottom: 56px;
    }
    .sign-box .sign-name {
      font-size: 13px;
      font-weight: 700;
      border-top: 1px solid #94a3b8;
      padding-top: 6px;
    }

    /* ── Print media ── */
    @media print {
      body { padding: 24px 32px; }
      .invoice-header { margin-bottom: 24px; }
    }
  </style>
</head>
<body>

  <!-- Header hóa đơn -->
  <div class="invoice-header">
    <div>
      <div class="company-name">🏠 Hệ thống cho thuê nhà trọ TMD</div>
      <div class="company-sub">Hệ thống quản lý nhà trọ TMD</div>
    </div>
    <div class="invoice-meta">
      <h2>HÓA ĐƠN</h2>
      <div class="ma-code">${hd.ma_hoa_don}</div>
      <div class="trang-thai ${hd.trang_thai === 'da_thanh_toan' ? 'da-thu' : 'chua-thu'}">${trangThai}</div>
    </div>
  </div>

  <!-- Thông tin khách thuê & hóa đơn -->
  <div class="info-grid">
    <div class="info-box">
      <h4>Thông tin khách thuê</h4>
      <div class="info-row"><span>Họ tên</span><span>${hd.ten_khach}</span></div>
      <div class="info-row"><span>Phòng</span><span>Phòng ${hd.so_phong}</span></div>
    </div>
    <div class="info-box">
      <h4>Thông tin hóa đơn</h4>
      <div class="info-row"><span>Kỳ thanh toán</span><span>${fmtThang(hd.thang_thanh_toan)}</span></div>
      <div class="info-row"><span>Hạn chót nộp</span><span>${fmtNgay(hd.han_chot)}</span></div>
      <div class="info-row"><span>Trạng thái</span><span>${trangThai}</span></div>
      ${ngayThuRow}
    </div>
  </div>

  <!-- Bảng chi tiết khoản thu -->
  <div class="section-title">📋 Chi tiết các khoản thu</div>
  <table>
    <thead>
      <tr>
        <th style="width:40%">Loại khoản thu</th>
        <th style="text-align:center;width:15%">Số lượng</th>
        <th style="text-align:right;width:22%">Đơn giá</th>
        <th style="text-align:right;width:23%">Thành tiền</th>
      </tr>
    </thead>
    <tbody>${rows}</tbody>
    <tfoot>
      <tr class="total-row">
        <td colspan="3">TỔNG CỘNG</td>
        <td>${fmtTien(hd.tong_tien)}</td>
      </tr>
    </tfoot>
  </table>

  <!-- Footer chữ ký -->
  <div class="page-footer">
    <div class="print-date">
      Ngày in: ${ngayIn}
    </div>
    <div class="sign-area">
      <div class="sign-box">
        <div class="sign-label">Khách thuê ký tên</div>
        <div class="sign-name">${hd.ten_khach}</div>
      </div>
      <div class="sign-box">
        <div class="sign-label">Quản lý xác nhận</div>
        <div class="sign-name">Quản lý</div>
      </div>
    </div>
  </div>

</body>
</html>`
}

// ── Hàm xuất chính ────────────────────────────────────────────────────────────

/**
 * Mở cửa sổ mới với hóa đơn được định dạng và kích hoạt hộp thoại in.
 *
 * @param {Object} hoaDon  - Đối tượng hóa đơn
 * @param {Array}  chiTiet - Mảng chi tiết khoản thu
 */
export const inHoaDon = (hoaDon, chiTiet) => {
  if (!hoaDon) return

  const html = taoHTMLHoaDon(hoaDon, chiTiet)
  const win  = window.open('', '_blank', 'width=860,height=950')

  win.document.write(html)
  win.document.close()
  win.focus()
  setTimeout(() => win.print(), 400)
}
