# Shop_TheThao - Tóm tắt kiến trúc và luồng chạy

## 1. Điểm vào ứng dụng

- Entry point: public/index.php
- Tạo session, định nghĩa BASE_PATH, đăng ký autoload PSR-style bằng tên class -> đường dẫn file.
- Khởi chạy app/core/App.php.

## 2. Cơ chế routing

- Ứng dụng không dùng framework router như Laravel; routing được quản lý thủ công qua routes/web.php.
- Mỗi route có cấu trúc gồm:
    - page: key trong routes/web.php, lấy từ query string ?page=...
    - title
    - view: view PHP tương ứng trong app/views/
    - controller + action (nếu có)
- Nếu page không tồn tại thì dùng route 404.

## 3. Luồng request cơ bản

1. public/index.php tạo ứng dụng.
2. app/core/App.php đọc routes/web.php.
3. Lấy page từ $\_GET['page'] hoặc mặc định home.
4. Tìm route tương ứng.
5. Nếu route có controller/action thì instantiate controller và gọi action.
6. Controller lấy dữ liệu từ Model.
7. App merge dữ liệu route + controller data + dữ liệu chung (mega menu, cấu hình site).
8. Render view tương ứng.
9. Chọn layout:
    - layout Main.php cho các trang người dùng
    - layout AdminLayout.php cho các trang admin bắt đầu bằng admin-

## 4. Cấu trúc thư mục chính

- app/controllers/: controller nghiệp vụ cho frontend và admin
- app/models/: model truy vấn dữ liệu
- app/models/entities/: entity/DTO cho dữ liệu
- app/models/admin/: model riêng cho admin
- app/views/: giao diện PHP phân theo module
- app/views/layouts/: layout chung
- app/views/components/: component dùng lại
- app/helpers/: helper tiện ích
- app/services/: mail, cloud, PHPMailer
- config/: cấu hình ứng dụng
- public/: entry point + assets
- routes/web.php: định nghĩa route

## 5. Các module chính và luồng

### Trang chủ

- Controller: app/controllers/HomeController.php
- Flow: load banner, sản phẩm sale, sản phẩm mới, danh mục, thương hiệu; xử lý form tư vấn.

### Sản phẩm

- Controller: app/controllers/SanPhamController.php
- Flow: lọc theo danh mục/thương hiệu/thuộc tính/từ khóa/sắp xếp; phân trang; xem chi tiết sản phẩm; gợi ý tìm kiếm.

### Giỏ hàng

- Controller: app/controllers/CartController.php
- Flow: đọc session cart, thêm/sửa/xóa item, kiểm tra tồn kho.

### Đơn hàng

- Controller: app/controllers/OrderController.php
- Flow: checkout -> validate -> tạo đơn hàng -> giảm tồn kho -> lưu snapshot trong don_hang/chi_tiet_don_hang -> redirect success.

### Auth

- Controller: app/controllers/AuthController.php
- Flow: login/register/verify OTP/change password/forgot password.

### Admin

- Controller nằm trong app/controllers/admin/
- Flow: quản lý sản phẩm, danh mục, thương hiệu, thuộc tính, banner, tin tức, voucher, đơn hàng, khách hàng, review, setting.

## 6. Mô hình dữ liệu chính theo code

### Bảng sản phẩm và danh mục

- san_pham
- danh_muc
- thuong_hieu
- anh_san_pham
- bien_the_san_pham
- thuoc_tinh
- gia_tri_thuoc_tinh
- gia_tri_thuoc_tinh_bien_the

### Bảng người dùng và địa chỉ

- nguoi_dung
- dia_chi_nguoi_dung
- hang_thanh_vien

### Bảng đơn hàng

- don_hang
- chi_tiet_don_hang
- ma_giam_gia

### Bảng nội dung khác

- banner
- tin_tuc
- danh_gia_san_pham
- thong_bao_het_hang

## 7. Quy tắc quan trọng khi viết logic mới

- Luôn lấy dữ liệu từ Model, không viết SQL trực tiếp trong View.
- Khi làm chức năng mua hàng, ưu tiên lấy tồn kho thực tế từ DB trước khi tạo đơn.
- Khi tạo đơn hàng, lưu snapshot giá/ảnh/options vào chi_tiet_don_hang để không phụ thuộc vào dữ liệu sản phẩm sau này.
- Khi thêm/sửa sản phẩm có biến thể, cần xử lý cả bảng san_pham và bảng bien_the_san_pham.
- Admin route phải dùng prefix admin- để App tự chọn layout AdminLayout.php.

## 8. Ghi chú kết nối database

- Trong code hiện tại, app/core/Model.php đang kết nối tới host TiDB Cloud, không phải local XAMPP mặc định.
- Nếu mục tiêu là chạy trên XAMPP local, cần đổi cấu hình host/user/password/database trong file này.
- Tên bảng và SQL trong project đang dùng schema theo phong cách tiếng Việt và tên cột cô đọng như san_pham, don_hang, ma_giam_gia, etc.

## 9. Kế hoạch mở rộng chức năng giảm giá và hoàn voucher

### 9.1 Ý tưởng tối giản theo cấu trúc hiện tại

Bạn đúng: không cần thêm bảng phụ phức tạp. Chỉ cần tận dụng các cột hiện có trong bảng ma_giam_gia:

- so_luong_da_dung: đếm số lần mã đã được dùng
- tong_so_luong: giới hạn tổng số lần phát hành

Vì vậy, logic mới chỉ cần làm thêm 2 phần:

1. Giới hạn lượt sử dụng cho từng tài khoản
2. Hoàn lại lượt dùng khi đơn hàng bị hủy

### 9.2 Đề xuất thay đổi database (rất nhẹ)

Chỉ cần thêm 1 cột vào bảng ma_giam_gia:

- gioi_han_su_dung_tung_user INT DEFAULT 0
    - 0 = không giới hạn
    - > 0 = mỗi tài khoản chỉ được dùng tối đa số lần này

Không cần thêm bảng mới.

### 9.3 Logic đề xuất

#### A. Kiểm tra khi áp dụng voucher

Khi khách hàng nhập mã ở checkout:

- kiểm tra voucher còn hiệu lực
- kiểm tra đơn hàng đạt điều kiện tối thiểu
- kiểm tra tổng số lượng còn lại: so_luong_da_dung < tong_so_luong
- nếu gioi_han_su_dung_tung_user > 0 thì kiểm tra xem user hiện tại đã dùng mã này bao nhiêu lần

Công thức:

- so_luong_con_lai_tong = tong_so_luong - so_luong_da_dung
- so_luong_con_lai_cua_user = gioi_han_su_dung_tung_user - so_luong_da_dung_cua_user

Voucher chỉ hợp lệ khi cả 2 điều kiện đều thỏa mãn.

#### B. Khi đơn hàng được tạo thành công

Sau khi tạo đơn thành công:

- tăng so_luong_da_dung lên 1
- lưu mã voucher đã dùng trong đơn hàng nếu bảng don_hang có cột lưu mã voucher

#### C. Khi đơn hàng bị hủy

Nếu đơn bị hủy:

- giảm so_luong_da_dung đi 1
- nghĩa là voucher được hoàn lại một lượt

### 9.4 Cách triển khai trong PHP

#### 1. Trong OrderModel

- sửa validateCoupon() để thêm điều kiện giới hạn theo user
- sửa placeOrder() để tăng so_luong_da_dung khi order thành công
- sửa cancelOrder() để giảm so_luong_da_dung khi order bị hủy

#### 2. Trong admin voucher

- thêm trường nhập cho "Giới hạn mỗi tài khoản"
- khi tạo/sửa voucher, lưu giá trị này vào ma_giam_gia

### 9.5 Cách tính đơn giản và đúng với hiện tại

Giả sử:

- tong_so_luong = 100
- so_luong_da_dung = 80
- gioi_han_su_dung_tung_user = 2

Thì:

- tổng còn lại = 20 lượt
- mỗi user chỉ được dùng tối đa 2 lần

Nếu khách hàng đã dùng 2 lần rồi thì dù tổng còn lại vẫn còn, họ vẫn không được dùng tiếp.

### 9.6 Ưu tiên triển khai

1. Thêm cột gioi_han_su_dung_tung_user vào ma_giam_gia.
2. Sửa validateCoupon() để kiểm tra giới hạn theo user.
3. Sửa placeOrder() để tăng so_luong_da_dung.
4. Sửa cancelOrder() để giảm so_luong_da_dung khi hủy đơn.
5. Bổ sung input ở giao diện admin quản lý voucher.
