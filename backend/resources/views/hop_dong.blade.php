<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hợp Đồng Thuê Phòng Trọ</title>
    <style>
        {!! file_get_contents(public_path('css/hop_dong.css')) !!}
    </style>
</head>
<body>
    <div class="text-center">
        <div class="text-bold quoc-hieu">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
        <div class="text-bold tieu-ngu">Độc lập – Tự do – Hạnh phúc</div>
        <div class="line">-------------o0o-------------</div>
        <div class="title">HỢP ĐỒNG THUÊ PHÒNG TRỌ</div>
    </div>


    <div style="text-align: right; font-style: italic; margin-bottom: 20px;">
        Cần Thơ, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}
    </div>


    <div class="section-title">I. BÊN CHO THUÊ PHÒNG TRỌ (Bên A):</div>
    <div class="row">Ông/bà: .......................................................................... Sinh ngày: ..............................................................</div>
    <div class="row">Nơi đăng ký HK: .............................................................................................................................................</div>
    <div class="row">CMND/CCCD số: ....................................... cấp ngày ..../..../....... tại: .........................................................</div>
    <div class="row">Số điện thoại: ..................................................................................................................................................</div>

    <div class="section-title">II. BÊN THUÊ PHÒNG TRỌ (Bên B):</div>
    <div class="row">Ông/bà: <strong>{{ $hopDong->ten_khach_hang }}</strong> <span style="float: right;">Sinh ngày: ..............................................................</span></div>
    <div class="row">Nơi đăng ký HK thường trú: .........................................................................................................................</div>
    <div class="row">Số CMND/CCCD: ....................................... cấp ngày ..../..../....... tại: .........................................................</div>
    <div class="row">Số điện thoại: <strong>{{ $hopDong->so_dien_thoai }}</strong></div>

    <div class="section-title">III. NỘI DUNG HỢP ĐỒNG:</div>
    <div class="row">1. Bên A đồng ý cho bên B thuê phòng trọ số: <strong>{{ $hopDong->so_phong }}</strong></div>
    <div class="row">2. Giá thuê hàng tháng: <strong>{{ number_format($hopDong->gia_thue_hang_thang, 0, ',', '.') }} VNĐ</strong></div>
    <div class="row">3. Tiền đặt cọc: <strong>{{ number_format($hopDong->tien_coc, 0, ',', '.') }} VNĐ</strong></div>
    <div class="row">4. Thời hạn thuê: Từ ngày <strong>{{ date('d/m/Y', strtotime($hopDong->ngay_bat_dau)) }}</strong> đến ngày <strong>{{ date('d/m/Y', strtotime($hopDong->ngay_ket_thuc)) }}</strong></div>
    <div class="row">5. Mã hợp đồng trên hệ thống: <strong>{{ $hopDong->ma_hop_dong }}</strong></div>
    
    <table style="width: 100%; margin-top: 60px; text-align: center;">
        <tr>
            <td class="text-bold" style="width: 50%;">
                BÊN THUÊ PHÒNG TRỌ (BÊN B)<br>
                <span style="font-weight: normal; font-style: italic; font-size: 13px;">(Ký, ghi rõ họ tên)</span>
            </td>
            <td class="text-bold" style="width: 50%;">
                BÊN CHO THUÊ (BÊN A)<br>
                <span style="font-weight: normal; font-style: italic; font-size: 13px;">(Ký, ghi rõ họ tên)</span>
            </td>
        </tr>
    </table>
</body>
</html>