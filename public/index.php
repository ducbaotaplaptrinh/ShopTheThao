<?php
// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kích hoạt thông báo lỗi tối đa để dễ bắt bệnh
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Đăng ký session để lưu giỏ hàng
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Định nghĩa hằng số BASE_PATH trỏ thẳng về thư mục gốc dự án
define('BASE_PATH', dirname(__DIR__));

// ĐĂNG KÝ AUTOLOAD 
spl_autoload_register(function ($className) {
    // Chuyển dấu gạch chéo ngược \ thành gạch chéo xuôi /
    // Ví dụ: "app\models\SanPhamModel" -> "app/models/SanPhamModel.php"
    $file = BASE_PATH . '/' . str_replace('\\', '/', $className) . '.php';

    // Nạp file vật lý vào hệ thống
    if (file_exists($file)) {
        require_once $file;
    }
});

// Khởi chạy Core App
require_once BASE_PATH . '/app/core/App.php';
require_once BASE_PATH . '/app/helpers/FomatHelper.php';

$app = new App();
$app->run();
