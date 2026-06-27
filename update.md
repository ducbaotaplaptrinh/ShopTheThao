# VOUCHER DONE

Tính năng áp dụng mã giảm giá (Voucher) cho đơn hàng đã được hoàn thiện. Dưới đây là tóm tắt toàn bộ các thay đổi và cách vận hành của từng đoạn code trong luồng xử lý này:

## 1. Xử lý Logic Database (app/models/OrderModel.php)

- **`getAvailableCoupons($userId, $totalPayment)`**:
    - **Cách vận hành**: Hàm này nhận vào ID của khách hàng và tổng giá trị giỏ hàng. Nó truy vấn bảng `nguoi_dung` kết hợp `hang_thanh_vien` để lấy ra mức chi tiêu tối thiểu của hạng hiện tại. Sau đó, truy vấn bảng `ma_giam_gia` để lọc ra các mã đang hoạt động (`trang_thai = 1`), còn lượt sử dụng, còn thời hạn. Điều kiện áp dụng: Mã giảm giá phải là mã hạng 0 (dành cho mọi người) HOẶC mã giảm giá thuộc về hạng có mức chi tiêu tối thiểu nhỏ hơn hoặc bằng hạng của khách hàng (hạng cao có thể dùng mã hạng thấp hơn).
    - **Mục đích**: Cung cấp danh sách các mã giảm giá khả dụng để hiển thị cho khách hàng chọn tại trang thanh toán.

- **`validateCoupon($code, $userId, $totalPayment)`**:
    - **Cách vận hành**: Tương tự như hàm trên nhưng hàm này kiểm tra đích danh một mã code cụ thể (`ma_code`). Trả về chi tiết mã giảm giá nếu hợp lệ (bao gồm cả kiểm tra điều kiện mã hạng 0 và tính kế thừa hạng), hoặc trả về `false` nếu không hợp lệ.
    - **Mục đích (Bảo mật)**: Là chốt chặn an toàn khi khách hàng bấm Đặt hàng, đảm bảo không ai có thể sửa đổi mã giảm giá hoặc số tiền giảm từ phía giao diện.

- **`placeOrder(...)`**:
    - **Cách vận hành**: Hàm tạo đơn hàng được nâng cấp để nhận thêm tham số `$couponCode`. Nếu đơn hàng được tạo thành công và có dùng mã giảm giá, hàm sẽ tự động chạy câu lệnh `UPDATE ma_giam_gia SET so_luong_da_dung = so_luong_da_dung + 1` để tăng số lượt đã sử dụng của mã đó.

## 2. Xử lý Backend Controller (app/controllers/OrderController.php)

- **`checkout()`**:
    - **Cách vận hành**: Tính tổng giá trị giỏ hàng hiện tại, sau đó lấy ID của user đang đăng nhập và gọi hàm `getAvailableCoupons()` từ Model. Tự động lấy ra mã giảm giá tốt nhất (đã được `ORDER BY gia_tri_giam DESC` ở DB).
    - **Mục đích**: Truyền các biến danh sách mã giảm giá (`$availableCoupons`) và mã giảm tốt nhất (`$bestCoupon`) ra ngoài file View để hiển thị.

- **`place()`**:
    - **Cách vận hành**: Khi nhận form đặt hàng, lấy giá trị `ma_code_su_dung` mà View gửi lên. Trước khi cộng trừ tính toán, bắt buộc gọi hàm `validateCoupon()` từ DB. Nếu hàm này trả về hợp lệ, Backend tự dùng số tiền `gia_tri_giam` thực tế từ Database để tính toán biến `$total` (Tổng thanh toán).
    - **Mục đích**: Ngăn chặn hoàn toàn rủi ro bị thao túng DOM ở phía Frontend (ví dụ user F12 đổi số tiền giảm giá thành số âm lớn để mua hàng giá 0đ). Tổng thanh toán luôn được tính lại bằng dữ liệu thật tại Server.

## 3. Xây dựng Giao diện Frontend (app/views/order/Checkout.php)

- **HTML (Giao diện hiển thị và Modal)**:
    - **Cách vận hành**: Hiển thị box "Mã giảm giá", thông báo số lượng mã khả dụng và nút "Chọn mã". Nút này sẽ mở một Modal chứa danh sách các mã giảm giá thỏa mãn điều kiện. Mỗi mã đi kèm một nút "Áp dụng" mang các thuộc tính data như `data-code`, `data-discount`.
    - Một thẻ `<input type="hidden" name="ma_code_su_dung" id="input-coupon-code">` được đặt khéo léo bên trong `<form id="checkoutForm">` để hứng mã code người dùng chọn và gửi về Backend.

- **JavaScript (Tự động tính toán)**:
    - **Cách vận hành**: Khi trang vừa tải xong, JS nhận biến PHP của mã giảm tốt nhất và tự động chạy hàm `calculateTotal()` để trừ tiền trực tiếp trên giao diện (hiệu ứng Real-time). Khi người dùng bấm nút "Áp dụng" mã khác trong Modal, sự kiện click sẽ cập nhật lại giá trị và tính lại tổng tiền. Đồng thời JS sẽ chèn mã code vào ô input hidden để form nộp lên đúng mã đó.
    - **Mục đích**: Mang lại trải nghiệm mượt mà, tiện lợi cho khách hàng (Auto-apply) mà không cần tải lại trang.

//---------------------------------------------------------------------------------------------------------------------------------

# Gửi thông báo khi có hàng (done)

1. Thêm phương thức gửi mail trong MailService.php
   Thay đổi: Tạo mới một hàm tĩnh (static method) tên là guiThongBaoKhiCoHang($email, $productName, $productLink).

Chức năng: Sử dụng cấu hình SMTP Gmail hiện có của bạn để tự động soạn và gửi một email có định dạng HTML chuyên nghiệp, thông báo cho khách hàng biết sản phẩm họ chờ đợi đã có hàng kèm theo nút bấm dẫn trực tiếp đến trang chi tiết sản phẩm.

2. Thêm hàm xử lý nội bộ trong AdminProductModel.php
   Thay đổi: Tạo mới một hàm private tên là xuLyThongBaoCoHang($productId, $variantId, $productName).

Chức năng: \* Định dạng đường dẫn sản phẩm thực tế theo tên miền của hệ thống (https://baodatsport.onrender.com/chi-tiet-san-pham?id=...).

Truy vấn vào bảng thong_bao_het_hang để lọc ra danh sách các khách hàng đang đăng ký nhận thông báo của sản phẩm/biến thể này (có trang_thai = 0).

Chạy vòng lặp gửi email cho từng người thông qua MailService.

Cập nhật lại trang_thai = 1 đối với những email đã gửi thành công để tránh gửi lặp lại ở lần nhập hàng sau.

3. Gắn Trigger vào luồng Cập nhật tồn kho (AdminProductModel.php)
   Chúng ta đã can thiệp vào các khối lệnh cập nhật Database hiện tại để kích hoạt hàm xử lý trên ngay khi số lượng hàng được bổ sung lớn hơn 0:

Đối với Biến thể sản phẩm: Chèn logic kiểm tra ngay sau câu lệnh UPDATE bien_the_san_pham. Nếu cập nhật thành công và số lượng tồn kho mới > 0, hệ thống sẽ kích hoạt gửi mail cho những ai đăng ký đúng biến thể đó (hoặc đăng ký sản phẩm gốc).

Đối với Sản phẩm gốc (Không biến thể): Chèn logic kiểm tra ngay sau câu lệnh UPDATE san_pham. Nếu sản phẩm không có biến thể, cập nhật thành công và số lượng tồn kho mới > 0, hệ thống sẽ kích hoạt gửi mail cho những ai đăng ký sản phẩm gốc này.

//---------------------------------------------------------------------------------------------------------------------------------

# Cập nhật sửa lỗi và bổ sung tính năng mới (27/06/2026)

## 1. Sửa lỗi đường dẫn sản phẩm trong Email thông báo có hàng

- **Lỗi:** Đường dẫn trong email trỏ đến `/chi-tiet-san-pham?id=...` làm hệ thống MVC không nhận diện được route, tự động điều hướng người dùng về Trang chủ (Home).
- **Khắc phục:** Đã sửa lại đường dẫn trong hàm `xuLyThongBaoCoHang()` tại file [AdminProductModel.php](file:///c:/xampp/htdocs/ShopTheThao/app/models/admin/AdminProductModel.php) thành `?page=product-detail&id=...` cho đúng chuẩn định tuyến của website.

---

## 2. Hoàn thiện tính năng Quản trị Tin tức (Admin News)

Hoàn thành bộ chức năng quản lý bài viết tin tức toàn diện cho Quản trị viên (Danh sách, Chỉnh sửa, Ẩn/Hiện nhanh, Xóa mềm).

- **Menu Sidebar:**
    - Đã thêm mục **Quản lý Tin tức** vào Sidebar quản trị [AdminLayout.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/layouts/AdminLayout.php) (nhóm Hệ thống).
- **Định tuyến (Routes):**
    - Bổ sung các route vào [web.php](file:///c:/xampp/htdocs/ShopTheThao/routes/web.php): `admin-news` (danh sách), `admin-news-edit` (giao diện sửa), `admin-news-update` (xử lý cập nhật), `admin-news-delete` (xóa mềm), `admin-news-toggle` (đổi trạng thái ẩn/hiện nhanh).
- **Logic Database & Nghiệp vụ (Model & Controller):**
    - **Model ([AdminNewsModel.php](file:///c:/xampp/htdocs/ShopTheThao/app/models/admin/AdminNewsModel.php)):** Thêm các hàm `getAllNews()`, `getNewsById()`, `updateNews()`, `toggleStatus()` và `deleteNews()` (xử lý **Xóa mềm** bằng cách chuyển trạng thái bài viết về 0/Ẩn chứ không xóa hẳn khỏi CSDL theo yêu cầu).
    - **Controller ([AdminNewsController.php](file:///c:/xampp/htdocs/ShopTheThao/app/controllers/admin/AdminNewsController.php)):** Thêm các action tương ứng để tiếp nhận yêu cầu từ Router, xử lý cập nhật ảnh bài viết lên Cloudinary.
- **Giao diện (Views):**
    - **[NEW]** Trang danh sách [index.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/admin/news/index.php) dạng bảng hiển thị trực quan thông tin bài viết.
    - **[NEW]** Trang sửa bài viết [edit.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/admin/news/edit.php) đi kèm sẵn trình soạn thảo Rich Text (CKEditor).
    - **[Fix]** Sửa lỗi sai đường dẫn form action trong trang tạo mới [create.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/admin/news/create.php) để lưu dữ liệu đúng.

---

## 3. Phát triển tính năng Quản lý Giao diện & Đồng bộ hệ thống

Xây dựng tính năng quản trị giao diện cho phép thay đổi cấu hình hiển thị và tự động đồng bộ hóa ra toàn bộ hệ thống Frontend.

- **Cơ sở dữ liệu tự khởi tạo (Self-healing Database):**
    - Tạo mới bảng `cau_hinh_giao_dien` để lưu trữ Logo, liên kết MXH (Zalo, Facebook), thông tin liên hệ (SĐT, Địa chỉ, Email) và thông tin ngân hàng.
    - Tự động phát hiện và khởi tạo bảng + gieo (seed) 1 dòng dữ liệu cấu hình mặc định tại constructor [AdminSettingModel.php](file:///c:/xampp/htdocs/ShopTheThao/app/models/admin/AdminSettingModel.php).
- **Tích hợp toàn cục (Global Bootstrapping):**
    - Cập nhật [App.php](file:///c:/xampp/htdocs/ShopTheThao/app/core/App.php) tự động nạp cấu hình giao diện vào một biến chung `$cauhinh` cho mọi view render, tránh việc lặp lại truy vấn database ở các component con.
- **Sidebar & Routes:**
    - Thêm menu **Quản lý Giao diện** vào Sidebar Admin và các route xử lý tương ứng (`admin-setting`, `admin-setting-update`).
- **Giao diện quản lý & Controller:**
    - **Controller ([AdminSettingController.php](file:///c:/xampp/htdocs/ShopTheThao/app/controllers/admin/AdminSettingController.php)):** Điều hướng hiển thị form và xử lý upload tệp tin ảnh Logo và QR Code tĩnh lên Cloudinary qua `CloudService`.
    - **View ([index.php (setting)](file:///c:/xampp/htdocs/ShopTheThao/app/views/admin/setting/index.php)):** Form cấu hình với giao diện hiện đại, cho phép xem trước Logo và lựa chọn giữa VietQR tự động hoặc QR Code tĩnh thủ công.
- **Đồng bộ hóa Frontend:**
    - Thay thế các đoạn dữ liệu tĩnh (hardcoded) bằng các biến động từ database ở các file:
        - **Logo:** [NavbarHome.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/components/home/NavbarHome.php) (Thanh menu), [BrandHome.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/components/home/BrandHome.php) (Logo trung tâm), [About.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/about/About.php) (Giới thiệu).
        - **Thông tin liên hệ & MXH:** [Footer.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/layouts/Footer.php) (Footer & Các nút liên hệ nổi), [Contact.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/contact/Contact.php) (Trang liên hệ).
        - **Thanh toán & QR Code:** [Success.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/order/Success.php) (Tự động sinh mã VietQR động theo STK/Chủ tài khoản/Ngân hàng và Số tiền thực tế, hoặc hiển thị ảnh QR tĩnh được upload).
