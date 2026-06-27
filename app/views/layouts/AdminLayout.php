<?php
// Ensure user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['vai_tro'] !== 'quan_tri') {
    header('Location: ?page=home');
    exit;
}
$currentPage = $_GET['page'] ?? 'admin-dashboard';

// Fetch dynamic admin notifications
try {
    $db = (new \app\core\Model())->conn;
    
    // Count of new orders
    $qNewOrdersCount = $db->query("SELECT COUNT(*) FROM don_hang WHERE trang_thai_don_hang = 'cho_xac_nhan'");
    $newOrdersCount = (int)$qNewOrdersCount->fetchColumn();
    
    // Count of low stock items
    // Base products low stock count
    $qLowStockBase = $db->query("SELECT COUNT(*) FROM san_pham WHERE (SELECT COUNT(*) FROM bien_the_san_pham WHERE ma_san_pham = san_pham.id AND ngay_xoa IS NULL) = 0 AND so_luong_ton <= 5 AND ngay_xoa IS NULL");
    $lowStockBaseCount = (int)$qLowStockBase->fetchColumn();
    
    // Variants low stock count
    $qLowStockVar = $db->query("SELECT COUNT(*) FROM bien_the_san_pham v JOIN san_pham p ON v.ma_san_pham = p.id WHERE v.so_luong_ton <= 5 AND v.ngay_xoa IS NULL AND p.ngay_xoa IS NULL");
    $lowStockVarCount = (int)$qLowStockVar->fetchColumn();
    
    $lowStockTotalCount = $lowStockBaseCount + $lowStockVarCount;
    $totalNotificationCount = $newOrdersCount + $lowStockTotalCount;

    // Fetch up to 4 new orders details
    $qNewOrders = $db->query("SELECT id, ngay_tao FROM don_hang WHERE trang_thai_don_hang = 'cho_xac_nhan' ORDER BY id DESC LIMIT 4");
    $newOrdersList = $qNewOrders->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    
    // Fetch up to 4 low stock base products details
    $qLowStockBaseItems = $db->query("
        SELECT id, ten_san_pham as name, so_luong_ton as stock 
        FROM san_pham 
        WHERE (SELECT COUNT(*) FROM bien_the_san_pham WHERE ma_san_pham = san_pham.id AND ngay_xoa IS NULL) = 0 
          AND so_luong_ton <= 5 AND ngay_xoa IS NULL 
        LIMIT 4
    ");
    $lowStockBaseList = $qLowStockBaseItems->fetchAll(\PDO::FETCH_ASSOC) ?: [];

    // Fetch up to 4 low stock variants details
    $qLowStockVarItems = $db->query("
        SELECT v.id, v.ten_bien_the as name, v.so_luong_ton as stock, p.id as parent_id, p.ten_san_pham as parent_name 
        FROM bien_the_san_pham v
        JOIN san_pham p ON v.ma_san_pham = p.id
        WHERE v.so_luong_ton <= 5 AND v.ngay_xoa IS NULL AND p.ngay_xoa IS NULL
        LIMIT 4
    ");
    $lowStockVarList = $qLowStockVarItems->fetchAll(\PDO::FETCH_ASSOC) ?: [];

} catch (\Exception $e) {
    $totalNotificationCount = 0;
    $newOrdersCount = 0;
    $lowStockTotalCount = 0;
    $newOrdersList = [];
    $lowStockBaseList = [];
    $lowStockVarList = [];
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Panel | Bảo Đạt Sport') ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php if (isset($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <link rel="stylesheet" href="<?= $style ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>

    <div class="admin-wrapper">

        <!-- SIDEBAR -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-bolt"></i>
                <span>Bảo Đạt Sport</span>
            </div>

            <div class="sidebar-menu">
                <div class="text-uppercase text-muted fw-bold mb-2 px-3 mt-2" style="font-size: 0.75rem; letter-spacing: 1px;">Main</div>
                <a href="?page=admin-dashboard" class="sidebar-menu-item <?= $currentPage === 'admin-dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>

                <div class="text-uppercase text-muted fw-bold mb-2 px-3 mt-4" style="font-size: 0.75rem; letter-spacing: 1px;">Sản phẩm</div>
                <a href="?page=admin-products" class="sidebar-menu-item <?= strpos($currentPage, 'admin-product') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i>
                    <span>Quản lý sản phẩm</span>
                </a>
                <a href="?page=admin-reviews" class="sidebar-menu-item <?= strpos($currentPage, 'admin-review') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-chat-right-text"></i>
                    <span>Đánh giá sản phẩm</span>
                </a>
                <a href="?page=admin-attributes" class="sidebar-menu-item <?= strpos($currentPage, 'admin-attribute') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-tags"></i>
                    <span>Thuộc tính động</span>
                </a>
                <a href="?page=admin-categories" class="sidebar-menu-item <?= strpos($currentPage, 'admin-categor') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-list-nested"></i>
                    <span>Danh mục</span>
                </a>
                <a href="?page=admin-brands" class="sidebar-menu-item <?= strpos($currentPage, 'admin-brand') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-star"></i>
                    <span>Thương hiệu</span>
                </a>

                <div class="text-uppercase text-muted fw-bold mb-2 px-3 mt-4" style="font-size: 0.75rem; letter-spacing: 1px;">Giao dịch</div>
                <a href="?page=admin-orders" class="sidebar-menu-item <?= strpos($currentPage, 'admin-order') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-receipt"></i>
                    <span>Đơn hàng</span>
                </a>
                <a href="?page=admin-customers" class="sidebar-menu-item <?= strpos($currentPage, 'admin-customer') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    <span>Khách hàng & Hạng</span>
                </a>

                <div class="text-uppercase text-muted fw-bold mb-2 px-3 mt-4" style="font-size: 0.75rem; letter-spacing: 1px;">Hệ thống</div>
                <a href="?page=admin-banners" class="sidebar-menu-item <?= strpos($currentPage, 'admin-banner') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-images"></i>
                    <span>Quản lý Banner</span>
                </a>
                <a href="?page=admin-news" class="sidebar-menu-item <?= strpos($currentPage, 'admin-news') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-newspaper"></i>
                    <span>Quản lý Tin tức</span>
                </a>
                <a href="?page=admin-setting" class="sidebar-menu-item <?= strpos($currentPage, 'admin-setting') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-sliders"></i>
                    <span>Quản lý Giao diện</span>
                </a>
                <a href="?page=home" class="sidebar-menu-item text-primary bg-light mt-2 border border-primary-subtle">
                    <i class="bi bi-shop"></i>
                    <span>Ra trang cửa hàng</span>
                </a>
                <a href="?page=logout" class="sidebar-menu-item text-danger">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Đăng xuất</span>
                </a>
            </div>
        </aside>

        <!-- MAIN AREA -->
        <main class="admin-main">
            <!-- HEADER -->
            <header class="admin-header">
                <button class="mobile-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>

                <div class="header-search">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Tìm kiếm nhanh mã đơn hàng...">
                </div>

                <div class="header-actions">
                    <div class="dropdown">
                        <button class="btn btn-light position-relative rounded-circle p-2" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            <?php if ($totalNotificationCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25em 0.5em;">
                                    <?= $totalNotificationCount ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0" style="width: 320px; border-radius: 12px; overflow: hidden; font-size: 13px;">
                            <li>
                                <div class="bg-primary text-white p-3 d-flex align-items-center justify-content-between">
                                    <h6 class="m-0 fw-bold" style="font-size: 14px;"><i class="bi bi-bell-fill me-2"></i>Thông báo mới</h6>
                                    <?php if ($totalNotificationCount > 0): ?>
                                        <span class="badge bg-white text-primary rounded-pill fw-bold" style="font-size: 0.75rem;"><?= $totalNotificationCount ?></span>
                                    <?php endif; ?>
                                </div>
                            </li>
                            <div style="max-height: 320px; overflow-y: auto;">
                                <?php if ($totalNotificationCount === 0): ?>
                                    <li class="p-4 text-center text-muted">
                                        <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                                        <span>Không có thông báo mới nào</span>
                                    </li>
                                <?php else: ?>
                                    <!-- Hiển thị đơn hàng mới -->
                                    <?php foreach ($newOrdersList as $order): ?>
                                        <li>
                                            <a class="dropdown-item py-2 px-3 border-bottom d-flex align-items-start gap-2" href="?page=admin-order-detail&id=<?= $order['id'] ?>" style="white-space: normal;">
                                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center p-2" style="width: 32px; height: 32px; flex-shrink: 0;">
                                                    <i class="bi bi-cart-plus" style="font-size: 1rem;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 0.85rem; font-weight: 600; color: #212529;">Đơn hàng mới #<?= $order['id'] ?></div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">Đang chờ bạn xác nhận và xử lý</div>
                                                </div>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>

                                    <!-- Hiển thị sản phẩm gốc sắp hết hàng -->
                                    <?php foreach ($lowStockBaseList as $item): ?>
                                        <li>
                                            <a class="dropdown-item py-2 px-3 border-bottom d-flex align-items-start gap-2" href="?page=admin-product-edit&id=<?= $item['id'] ?>" style="white-space: normal;">
                                                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center p-2" style="width: 32px; height: 32px; flex-shrink: 0;">
                                                    <i class="bi bi-exclamation-triangle" style="font-size: 1rem;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 0.85rem; font-weight: 600; color: #212529; text-overflow: ellipsis; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;"><?= htmlspecialchars($item['name']) ?></div>
                                                    <div class="text-danger fw-semibold" style="font-size: 0.75rem;">Sắp hết hàng (còn <?= $item['stock'] ?> sp)</div>
                                                </div>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>

                                    <!-- Hiển thị biến thể sắp hết hàng -->
                                    <?php foreach ($lowStockVarList as $item): ?>
                                        <li>
                                            <a class="dropdown-item py-2 px-3 border-bottom d-flex align-items-start gap-2" href="?page=admin-product-edit&id=<?= $item['parent_id'] ?>" style="white-space: normal;">
                                                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center p-2" style="width: 32px; height: 32px; flex-shrink: 0;">
                                                    <i class="bi bi-exclamation-triangle" style="font-size: 1rem;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 0.85rem; font-weight: 600; color: #212529; text-overflow: ellipsis; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;"><?= htmlspecialchars($item['parent_name'] . ' - ' . $item['name']) ?></div>
                                                    <div class="text-danger fw-semibold" style="font-size: 0.75rem;">Biến thể sắp hết hàng (còn <?= $item['stock'] ?> sp)</div>
                                                </div>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($newOrdersCount > 0 || $lowStockTotalCount > 0): ?>
                                <li>
                                    <div class="text-center py-2 bg-light">
                                        <a href="?page=admin-orders" class="text-primary fw-bold text-decoration-none" style="font-size: 0.8rem;">Xem tất cả đơn hàng</a>
                                    </div>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="user-profile dropdown" style="cursor: pointer;" data-bs-toggle="dropdown">
                        <div class="text-end d-none d-md-block">
                            <div class="text-dark mb-0 lh-1"><?= htmlspecialchars($_SESSION['user']['ho_ten'] ?? 'Admin') ?></div>
                            <small class="text-muted" style="font-size: 12px;">Quản trị viên</small>
                        </div>
                        <div class="user-avatar rounded-circle d-flex align-items-center justify-content-center bg-warning text-white fw-bold" 
                             style="width: 36px; height: 36px; <?= !empty($_SESSION['user']['anh_dai_dien']) ? "background-image: url('" . htmlspecialchars($_SESSION['user']['anh_dai_dien']) . "'); background-size: cover; background-position: center;" : '' ?>">
                            <?= empty($_SESSION['user']['anh_dai_dien']) ? htmlspecialchars(mb_substr($_SESSION['user']['ho_ten'] ?? 'A', 0, 1)) : '' ?>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li><a class="dropdown-item" href="?page=home"><i class="bi bi-shop me-2"></i>Xem cửa hàng</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="?page=logout"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- PAGE CONTENT -->
            <div class="admin-content">
                <?php echo $content; ?>
            </div>
        </main>

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Mobile Sidebar Toggle JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            const toggleBtn = document.getElementById('sidebarToggle');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });
    </script>

    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="public/<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>