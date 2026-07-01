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
<<<<<<< HEAD
=======


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

//---------------------------------------------------------------------------------------------------------------------------------

# Quản lý Voucher (Mã giảm giá) & Nâng cấp Giao diện (27/06/2026)

## 1. Phát triển tính năng Quản lý Voucher (Mã giảm giá)
Xây dựng trọn bộ chức năng quản lý Voucher toàn diện cho Quản trị viên (Admin) tương tác đầy đủ với bảng `ma_giam_gia` trong cơ sở dữ liệu:

- **Định tuyến (Routes):** Bổ sung 7 route quản lý bao gồm danh sách (`admin-vouchers`), giao diện thêm mới (`admin-voucher-create`), lưu dữ liệu (`admin-voucher-store`), giao diện chỉnh sửa (`admin-voucher-edit`), cập nhật dữ liệu (`admin-voucher-update`), xóa vĩnh viễn (`admin-voucher-delete`) và thay đổi trạng thái hoạt động nhanh (`admin-voucher-toggle`).
- **Dữ liệu & Đồng nhất Khóa ngoại (Model & SQL):**
  - **[AdminVoucherModel.php](file:///c:/xampp/htdocs/ShopTheThao/app/models/admin/AdminVoucherModel.php):** Thực hiện đầy đủ CRUD và các phương thức kiểm tra sự tồn tại của mã code. Ánh xạ tự động giá trị `ma_hang = 0` thành `NULL` để tránh vi phạm ràng buộc khóa ngoại (Foreign Key) với bảng `hang_thanh_vien`.
  - **[OrderModel.php](file:///c:/xampp/htdocs/ShopTheThao/app/models/OrderModel.php):** Cập nhật các câu lệnh SQL kiểm tra mã giảm giá ở trang checkout để hỗ trợ cả trường hợp `ma_hang IS NULL` hoặc `ma_hang = 0` (mã áp dụng cho tất cả hạng thành viên).
- **Điều hướng & Nghiệp vụ (Controller):**
  - **[AdminVoucherController.php](file:///c:/xampp/htdocs/ShopTheThao/app/controllers/admin/AdminVoucherController.php):** Xử lý luồng thêm/sửa, kiểm tra tính hợp lệ của dữ liệu đầu vào (không bỏ trống mã/tiêu đề, trị giá giảm > 0, ngày kết thúc lớn hơn ngày bắt đầu, tỷ lệ phần trăm không quá 100%).
- **Giao diện quản trị (Views):**
  - **[index.php (Danh sách)](file:///c:/xampp/htdocs/ShopTheThao/app/views/admin/voucher/index.php):** Hiển thị trực quan trạng thái voucher (Đang kích hoạt, hết hạn, chưa bắt đầu), số lượng đã dùng / tổng lượng phát hành kèm thanh tiến trình.
  - **[create.php (Thêm)](file:///c:/xampp/htdocs/ShopTheThao/app/views/admin/voucher/create.php) / [edit.php (Sửa)](file:///c:/xampp/htdocs/ShopTheThao/app/views/admin/voucher/edit.php):** Thiết kế trực quan, dễ sử dụng, tích hợp Javascript tự động viết hoa và lọc ký tự đặc biệt ở Mã Voucher, tự động ẩn/hiện mức giảm tối đa khi chuyển đổi loại voucher (tiền cố định vs phần trăm), và hiển thị thống kê sử dụng nhanh.

## 2. Nâng cấp Quản lý Giao diện (Topbar & Tab Bar Logo)
- **Tự động tự phục hồi Database (Self-healing DB):** Nâng cấp hàm `initializeTable()` của [AdminSettingModel.php](file:///c:/xampp/htdocs/ShopTheThao/app/models/admin/AdminSettingModel.php) tự động bổ sung thêm 3 cột: `logo_tab_bar_url` (Favicon), `text_topbar_1` (Chữ giao hàng topbar), `text_topbar_2` (Chữ bảo hành topbar) qua lệnh `ALTER TABLE` nếu chưa tồn tại.
- **Quản trị Giao diện (Admin settings):**
  - **[index.php (Cấu hình)](file:///c:/xampp/htdocs/ShopTheThao/app/views/admin/setting/index.php):** Thêm 2 trường nhập chữ Topbar và 1 khu vực tải ảnh Logo Tab Bar (Favicon) mới kèm JavaScript xem trước hình ảnh trước khi lưu.
  - **[AdminSettingController.php](file:///c:/xampp/htdocs/ShopTheThao/app/controllers/admin/AdminSettingController.php):** Xử lý lưu các chuỗi chữ mới và upload ảnh Favicon lên Cloudinary qua `CloudService`.
- **Đồng bộ Frontend:**
  - **Topbar:** Cập nhật [NavbarHome.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/components/home/NavbarHome.php) hiển thị động các chữ topbar, số điện thoại và email lấy từ bảng cấu hình.
  - **Favicon:** Tích hợp thẻ `<link rel="icon">` động gọi `$cauhinh['logo_tab_bar_url']` ở cả header khách hàng ([Header.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/layouts/Header.php)) và header admin ([AdminLayout.php](file:///c:/xampp/htdocs/ShopTheThao/app/views/layouts/AdminLayout.php)).
>>>>>>> d74ec64ae845a9769c6e1f55b73ca9142141fc05
