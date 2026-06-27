<?php

return [
	'login' => [
		'title' => 'Đăng nhập',
		'view' => 'auth/Login.php',
		'controller' => \app\controllers\AuthController::class,
		'action' => 'login',
	],
	'register' => [
		'title' => 'Đăng ký',
		'view' => 'auth/Register.php',
		'controller' => \app\controllers\AuthController::class,
		'action' => 'register',
	],
	'verify-otp' => [
		'title' => 'Xác thực OTP | Bảo Đạt Sport',
		'view' => 'auth/VerifyOtp.php',
		'controller' => \app\controllers\AuthController::class,
		'action' => 'verifyOtp',
	],
	'change-password' => [
		'title' => 'Đổi mật khẩu | Bảo Đạt Sport',
		'view' => 'auth/ChangePassword.php',
		'controller' => \app\controllers\AuthController::class,
		'action' => 'changePassword',
	],
	'logout' => [
		'title' => 'Đăng xuất',
		'view' => 'auth/Login.php',
		'controller' => \app\controllers\AuthController::class,
		'action' => 'logout',
	],
	'home' => [
		'title' => 'Trang chủ',
		'view' => 'home/Index.php',
		'controller' => \app\controllers\HomeController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/home.css']
	],
	'product-index' => [
		'title' => 'Danh sách sản phẩm',
		'view' => 'product/Index.php',
		'controller' => \app\controllers\SanPhamController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/product.css']
	],
	'flash-sale' => [
		'title' => 'Sản phẩm khuyến mãi | Bảo Đạt Sport',
		'view' => 'product/Index.php',
		'controller' => \app\controllers\SanPhamController::class,
		'action' => 'flashSale',
		'pageStyles' => ['assets/css/product.css']
	],
	'product-detail' => [
		'title' => 'Chi tiết sản phẩm',
		'view' => 'product/ProductDetail.php',
		'controller' => \app\controllers\SanPhamController::class,
		'action' => 'chitiet',
		'pageStyles' => ['assets/css/product-detail.css']
	],
	'search-suggest' => [
		'title' => 'Gợi ý tìm kiếm',
		'view' => 'errors/404.php',
		'controller' => \app\controllers\SanPhamController::class,
		'action' => 'suggest',
	],

	'about' => [
		'title' => 'Giới thiệu',
		'view' => 'about/About.php',
	],
	'cart' => [
		'title' => 'Giỏ hàng | Bảo Đạt Sport',
		'view' => 'cart/Index.php',
		'controller' => \app\controllers\CartController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/cart.css']
	],
	'cart-add' => [
		'title' => 'Thêm vào giỏ hàng',
		'view' => 'cart/Index.php',
		'controller' => \app\controllers\CartController::class,
		'action' => 'add'
	],
	'cart-update' => [
		'title' => 'Cập nhật giỏ hàng',
		'view' => 'cart/Index.php',
		'controller' => \app\controllers\CartController::class,
		'action' => 'update'
	],
	'cart-delete' => [
		'title' => 'Xoá giỏ hàng',
		'view' => 'cart/Index.php',
		'controller' => \app\controllers\CartController::class,
		'action' => 'delete'
	],
	'checkout' => [
		'title' => 'Thanh toán đơn hàng | Bảo Đạt Sport',
		'view' => 'order/Checkout.php',
		'controller' => \app\controllers\OrderController::class,
		'action' => 'checkout',
		'pageStyles' => ['assets/css/cart.css']
	],
	'order-place' => [
		'title' => 'Đặt hàng',
		'view' => 'order/Checkout.php',
		'controller' => \app\controllers\OrderController::class,
		'action' => 'place'
	],
	'order-success' => [
		'title' => 'Đặt hàng thành công | Bảo Đạt Sport',
		'view' => 'order/Success.php',
		'controller' => \app\controllers\OrderController::class,
		'action' => 'success',
		'pageStyles' => ['assets/css/cart.css', 'assets/css/my-orders.css']
	],
	'order-track' => [
		'title' => 'Tra cứu đơn hàng | Bảo Đạt Sport',
		'view' => 'order/Track.php',
		'controller' => \app\controllers\OrderController::class,
		'action' => 'track',
		'pageStyles' => ['assets/css/cart.css', 'assets/css/my-orders.css']
	],
	'my-orders' => [
		'title' => 'Đơn hàng của tôi | Bảo Đạt Sport',
		'view' => 'order/MyOrders.php',
		'controller' => \app\controllers\OrderController::class,
		'action' => 'myOrders',
		'pageStyles' => ['assets/css/my-orders.css']
	],
	'order-cancel' => [
		'title' => 'Hủy đơn hàng',
		'view' => 'order/MyOrders.php',
		'controller' => \app\controllers\OrderController::class,
		'action' => 'cancel',
	],
	'profile' => [
		'title' => 'Thông tin cá nhân | Bảo Đạt Sport',
		'view' => 'auth/profile.php',
		'controller' => \app\controllers\AuthController::class,
		'action' => 'profile',
		'pageStyles' => ['assets/css/profile.css']
	],
	'404' => [
		'title' => 'Không tìm thấy trang',
		'view' => 'errors/404.php',
	],
	'admin-dashboard' => [
		'title' => 'Dashboard | Quản trị',
		'view' => 'admin/Dashboard.php',
		'controller' => \app\controllers\admin\AdminDashboardController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-products' => [
		'title' => 'Quản lý Sản phẩm | Quản trị',
		'view' => 'admin/product/Index.php',
		'controller' => \app\controllers\admin\AdminProductController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-product-create' => [
		'title' => 'Thêm Sản phẩm | Quản trị',
		'view' => 'admin/product/Form.php',
		'controller' => \app\controllers\admin\AdminProductController::class,
		'action' => 'create',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-product-store' => [
		'title' => 'Lưu Sản phẩm | Quản trị',
		'view' => 'admin/product/Index.php',
		'controller' => \app\controllers\admin\AdminProductController::class,
		'action' => 'store',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-categories' => [
		'title' => 'Danh mục | Quản trị',
		'view' => 'admin/category/Index.php',
		'controller' => \app\controllers\admin\AdminCategoryBrandController::class,
		'action' => 'categories',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-category-store' => [
		'title' => 'Lưu Danh mục',
		'view' => 'admin/category/Index.php',
		'controller' => \app\controllers\admin\AdminCategoryBrandController::class,
		'action' => 'storeCategory',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-category-edit' => [
		'title' => 'Sửa Danh mục | Quản trị',
		'view' => 'admin/category/Edit.php',
		'controller' => \app\controllers\admin\AdminCategoryBrandController::class,
		'action' => 'editCategory',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-category-update' => [
		'title' => 'Cập nhật Danh mục',
		'view' => 'admin/category/Index.php',
		'controller' => \app\controllers\admin\AdminCategoryBrandController::class,
		'action' => 'updateCategory',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-category-delete' => [
		'title' => 'Xóa Danh mục',
		'view' => 'admin/category/Index.php',
		'controller' => \app\controllers\admin\AdminCategoryBrandController::class,
		'action' => 'deleteCategory',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-brands' => [
		'title' => 'Thương hiệu | Quản trị',
		'view' => 'admin/brand/Index.php',
		'controller' => \app\controllers\admin\AdminCategoryBrandController::class,
		'action' => 'brands',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-brand-store' => [
		'title' => 'Lưu Thương hiệu',
		'view' => 'admin/brand/Index.php',
		'controller' => \app\controllers\admin\AdminCategoryBrandController::class,
		'action' => 'storeBrand',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-brand-edit' => [
		'title' => 'Sửa Thương hiệu | Quản trị',
		'view' => 'admin/brand/Edit.php',
		'controller' => \app\controllers\admin\AdminCategoryBrandController::class,
		'action' => 'editBrand',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-brand-update' => [
		'title' => 'Cập nhật Thương hiệu',
		'view' => 'admin/brand/Index.php',
		'controller' => \app\controllers\admin\AdminCategoryBrandController::class,
		'action' => 'updateBrand',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-brand-delete' => [
		'title' => 'Xóa Thương hiệu',
		'view' => 'admin/brand/Index.php',
		'controller' => \app\controllers\admin\AdminCategoryBrandController::class,
		'action' => 'deleteBrand',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-attributes' => [
		'title' => 'Thuộc tính | Quản trị',
		'view' => 'admin/attribute/Index.php',
		'controller' => \app\controllers\admin\AdminAttributeController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-attribute-store-group' => [
		'title' => 'Lưu Nhóm Thuộc tính',
		'view' => 'admin/attribute/Index.php',
		'controller' => \app\controllers\admin\AdminAttributeController::class,
		'action' => 'storeGroup',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-attribute-store-value' => [
		'title' => 'Lưu Giá trị Thuộc tính',
		'view' => 'admin/attribute/Index.php',
		'controller' => \app\controllers\admin\AdminAttributeController::class,
		'action' => 'storeValue',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-attribute-toggle' => [
		'title' => 'Đổi trạng thái biến thể',
		'view' => 'errors/404.php', // API endpoint
		'controller' => \app\controllers\admin\AdminAttributeController::class,
		'action' => 'toggleVariant',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-orders' => [
		'title' => 'Quản lý Đơn hàng | Quản trị',
		'view' => 'admin/order/Index.php',
		'controller' => \app\controllers\admin\AdminOrderController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-order-detail' => [
		'title' => 'Chi tiết Đơn hàng | Quản trị',
		'view' => 'admin/order/Detail.php',
		'controller' => \app\controllers\admin\AdminOrderController::class,
		'action' => 'detail',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-order-update-status' => [
		'title' => 'Cập nhật trạng thái',
		'view' => 'admin/order/Detail.php',
		'controller' => \app\controllers\admin\AdminOrderController::class,
		'action' => 'capNhatTrangThaiDonHang',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-order-delete' => [
		'title' => 'Xóa Đơn hàng',
		'view' => 'admin/order/Index.php',
		'controller' => \app\controllers\admin\AdminOrderController::class,
		'action' => 'xoaDonHang',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-customers' => [
		'title' => 'Quản lý Khách hàng | Quản trị',
		'view' => 'admin/customer/Index.php',
		'controller' => \app\controllers\admin\AdminCustomerController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-customer-toggle' => [
		'title' => 'Đổi trạng thái tài khoản',
		'view' => 'admin/customer/Index.php',
		'controller' => \app\controllers\admin\AdminCustomerController::class,
		'action' => 'toggleStatus',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-product-edit' => [
		'title' => 'Sửa Sản phẩm | Quản trị',
		'view' => 'admin/product/Edit.php',
		'controller' => \app\controllers\admin\AdminProductController::class,
		'action' => 'edit',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-product-update' => [
		'title' => 'Cập nhật Sản phẩm | Quản trị',
		'view' => 'admin/product/Index.php',
		'controller' => \app\controllers\admin\AdminProductController::class,
		'action' => 'update',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-product-batch-discount' => [
		'title' => 'Áp dụng giảm giá hàng loạt | Quản trị',
		'view' => 'admin/product/Index.php',
		'controller' => \app\controllers\admin\AdminProductController::class,
		'action' => 'batchDiscount',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-product-delete' => [
		'title' => 'Xóa Sản phẩm | Quản trị',
		'view' => 'admin/product/Index.php',
		'controller' => \app\controllers\admin\AdminProductController::class,
		'action' => 'xoaSanPham',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-product-restore' => [
		'title' => 'Khôi phục Sản phẩm | Quản trị',
		'view' => 'admin/product/Index.php',
		'controller' => \app\controllers\admin\AdminProductController::class,
		'action' => 'khoiPhucSanPham',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-banners' => [
		'title' => 'Quản lý Banner | Quản trị',
		'view' => 'admin/banner/Index.php',
		'controller' => \app\controllers\admin\AdminBannerController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-banner-create' => [
		'title' => 'Thêm Banner Mới | Quản trị',
		'view' => 'admin/banner/Form.php',
		'controller' => \app\controllers\admin\AdminBannerController::class,
		'action' => 'create',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-banner-store' => [
		'title' => 'Lưu Banner | Quản trị',
		'view' => 'admin/banner/Index.php',
		'controller' => \app\controllers\admin\AdminBannerController::class,
		'action' => 'store',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-banner-edit' => [
		'title' => 'Sửa Banner | Quản trị',
		'view' => 'admin/banner/Form.php',
		'controller' => \app\controllers\admin\AdminBannerController::class,
		'action' => 'edit',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-banner-update' => [
		'title' => 'Cập nhật Banner | Quản trị',
		'view' => 'admin/banner/Index.php',
		'controller' => \app\controllers\admin\AdminBannerController::class,
		'action' => 'update',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-banner-delete' => [
		'title' => 'Xóa Banner | Quản trị',
		'view' => 'admin/banner/Index.php',
		'controller' => \app\controllers\admin\AdminBannerController::class,
		'action' => 'delete',
		'pageStyles' => ['assets/css/admin.css']
	],
	'contact' => [
		'title' => 'Liên hệ',
		'view' => 'contact/Contact.php',
		'pageStyles' => ['assets/css/contact.css']
	],
	'new' => [
		'title' => 'Tin tức thể thao',
		'view' => 'news/Index.php',
		'controller' => \app\controllers\NewsController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/news.css']
	],
	'new-detail' => [
		'title' => 'Chi tiết tin tức',
		'view' => 'news/Detail.php',
		'controller' => \app\controllers\NewsController::class,
		'action' => 'detail',
		'pageStyles' => ['assets/css/news.css']
	],
	'admin-news' => [
		'title' => 'Quản lý Tin tức | Quản trị',
		'view' => 'admin/news/index.php',
		'controller' => \app\controllers\admin\AdminNewsController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/admin.css']
	],
	// Thêm mới bài viết (Form)
	'admin-news-create' => [
		'title' => 'Thêm bài viết mới | Quản trị',
		'view' => 'admin/news/create.php', // Đảm bảo đường dẫn này khớp với thư mục của bạn
		'controller' => \app\controllers\admin\AdminNewsController::class,
		'action' => 'create',
		'pageStyles' => ['assets/css/admin.css']
	],

	// Lưu bài viết vào Database
	'admin-news-store' => [
		'title' => 'Xử lý thêm bài viết | Quản trị',
		'view' => 'admin/news/create.php',
		'controller' => \app\controllers\admin\AdminNewsController::class,
		'action' => 'store',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-news-edit' => [
		'title' => 'Sửa bài viết | Quản trị',
		'view' => 'admin/news/edit.php',
		'controller' => \app\controllers\admin\AdminNewsController::class,
		'action' => 'edit',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-news-update' => [
		'title' => 'Cập nhật bài viết | Quản trị',
		'view' => 'admin/news/index.php',
		'controller' => \app\controllers\admin\AdminNewsController::class,
		'action' => 'update',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-news-delete' => [
		'title' => 'Xóa bài viết | Quản trị',
		'view' => 'admin/news/index.php',
		'controller' => \app\controllers\admin\AdminNewsController::class,
		'action' => 'delete',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-news-toggle' => [
		'title' => 'Đổi trạng thái bài viết',
		'view' => 'errors/404.php',
		'controller' => \app\controllers\admin\AdminNewsController::class,
		'action' => 'toggleStatus',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-setting' => [
		'title' => 'Quản lý Giao diện | Quản trị',
		'view' => 'admin/setting/index.php',
		'controller' => \app\controllers\admin\AdminSettingController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-setting-update' => [
		'title' => 'Lưu Cấu hình Giao diện | Quản trị',
		'view' => 'admin/setting/index.php',
		'controller' => \app\controllers\admin\AdminSettingController::class,
		'action' => 'update',
		'pageStyles' => ['assets/css/admin.css']
	],
	'notify-out-of-stock' => [
		'title' => 'Đăng ký thông báo hết hàng',
		'view' => 'errors/404.php',
		'controller' => \app\controllers\SanPhamController::class,
		'action' => 'dangKyThongBao',
	],
	'order-confirm-received' => [
		'title' => 'Xác nhận nhận hàng',
		'view' => 'order/MyOrders.php',
		'controller' => \app\controllers\OrderController::class,
		'action' => 'confirmReceived',
	],
	'submit-review' => [
		'title' => 'Gửi đánh giá sản phẩm',
		'view' => 'order/MyOrders.php',
		'controller' => \app\controllers\OrderController::class,
		'action' => 'submitReview',
	],
	'admin-reviews' => [
		'title' => 'Quản lý đánh giá | Quản trị',
		'view' => 'admin/review/Index.php',
		'controller' => \app\controllers\admin\AdminReviewController::class,
		'action' => 'index',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-review-toggle' => [
		'title' => 'Ẩn hiện đánh giá',
		'view' => 'admin/review/Index.php',
		'controller' => \app\controllers\admin\AdminReviewController::class,
		'action' => 'toggleStatus',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-review-delete' => [
		'title' => 'Xóa đánh giá',
		'view' => 'admin/review/Index.php',
		'controller' => \app\controllers\admin\AdminReviewController::class,
		'action' => 'delete',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-review-edit' => [
		'title' => 'Sửa đánh giá | Quản trị',
		'view' => 'admin/review/Form.php',
		'controller' => \app\controllers\admin\AdminReviewController::class,
		'action' => 'edit',
		'pageStyles' => ['assets/css/admin.css']
	],
	'admin-review-create' => [
		'title' => 'Thêm đánh giá thủ công | Quản trị',
		'view' => 'admin/review/Form.php',
		'controller' => \app\controllers\admin\AdminReviewController::class,
		'action' => 'create',
		'pageStyles' => ['assets/css/admin.css']
	],
];
