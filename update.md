# Gửi Thông Báo Khi có hàng - done 
1. Thêm phương thức gửi mail trong MailService.php
Thay đổi: Tạo mới một hàm tĩnh (static method) tên là guiThongBaoKhiCoHang($email, $productName, $productLink).

Chức năng: Sử dụng cấu hình SMTP Gmail hiện có  để tự động soạn và gửi một email có định dạng HTML, thông báo cho khách hàng biết sản phẩm họ chờ đợi đã có hàng kèm theo nút bấm dẫn trực tiếp đến trang chi tiết sản phẩm.

2. Thêm hàm xử lý nội bộ trong AdminProductModel.php
Thay đổi: Tạo mới một hàm private tên là xuLyThongBaoCoHang($productId, $variantId, $productName).

Chức năng: * Định dạng đường dẫn sản phẩm thực tế theo tên miền của hệ thống (https://baodatsport.onrender.com/chi-tiet-san-pham?id=...).

Truy vấn vào bảng thong_bao_het_hang để lọc ra danh sách các khách hàng đang đăng ký nhận thông báo của sản phẩm/biến thể này (có trang_thai = 0).

Chạy vòng lặp gửi email cho từng người thông qua MailService.

Cập nhật lại trang_thai = 1 đối với những email đã gửi thành công để tránh gửi lặp lại ở lần nhập hàng sau.

3. Gắn Trigger vào luồng Cập nhật tồn kho (AdminProductModel.php)
Chúng ta đã can thiệp vào các khối lệnh cập nhật Database hiện tại để kích hoạt hàm xử lý trên ngay khi số lượng hàng được bổ sung lớn hơn 0:

Đối với Biến thể sản phẩm: Chèn logic kiểm tra ngay sau câu lệnh UPDATE bien_the_san_pham. Nếu cập nhật thành công và số lượng tồn kho mới > 0, hệ thống sẽ kích hoạt gửi mail cho những ai đăng ký đúng biến thể đó (hoặc đăng ký sản phẩm gốc).

Đối với Sản phẩm gốc (Không biến thể): Chèn logic kiểm tra ngay sau câu lệnh UPDATE san_pham. Nếu sản phẩm không có biến thể, cập nhật thành công và số lượng tồn kho mới > 0, hệ thống sẽ kích hoạt gửi mail cho những ai đăng ký sản phẩm gốc này.
