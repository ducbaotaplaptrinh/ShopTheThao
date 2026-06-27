<?php
$totalCartItems = 0;
$cartItems = $_SESSION['cart'] ?? [];
foreach ($cartItems as $item) {
    $totalCartItems += $item['qty'];
}
$page = $_GET['page'] ?? "";
?>
<header class="site-header  ">
    <div class="topbar py-2 d-none d-lg-block">
        <div
            class="container-xl d-flex align-items-lg-center justify-content-between gap-2">
            <div
                class="d-flex align-items-center gap-3 small text-white-50">
                <span><a
                        href="tel:0900123456"
                        class="bi bi-telephone me-1 text-decoration-none text-white-50"><span class="mx-2">0900 123 456</span></a>
                </span>
                <span><a
                        href="mailto:support@sportpro.vn"
                        class="bi bi-envelope me-1 text-decoration-none text-white-50"><span class="mx-2">support@sportpro.vn</span></a>
                </span>
            </div>
            <div
                class="d-flex align-items-center gap-3 small text-white-50">
                <span><i class="bi bi-truck me-1"></i> Giao hàng toàn
                    quốc</span>
                <span><i class="bi bi-shield-check me-1"></i> Chính hãng
                    - Bảo hành rõ ràng</span>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg  main-nav">
        <div class="container justify-content-between ">
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainMenu"
                aria-controls="mainMenu"
                aria-expanded="false"
                aria-label="Mở menu">
                <i class="fa-solid fa-bars fa-lg "></i>
            </button>

            <div class="logo-container ">
                <a class="logo-container__link d-flex" href="#home">
                    <img
                        src="<?= htmlspecialchars($cauhinh['logo_url'] ?? 'assets/images/favicons/Logo.png') ?>"
                        alt="Logo"
                        class="logo-container__icon" />

                </a>
            </div>
            <form action="" method="GET" class="ms-auto d-none d-lg-flex align-items-center mySearch justify-content-end">
                <input type="hidden" name="page" value="product-index">
                <input
                    class="h-100 search-input d-none d-lg-block"
                    type="text"
                    name="keyword"
                    placeholder="Tìm kiếm..."
                    value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>" />
                <button type="submit" class="navlink search-btn">
                    <i class="fa-solid fa-magnifying-glass fa-lg"></i>
                </button>
                <div class="search-suggestions-dropdown" id="searchSuggestions"></div>
            </form>

            <div class="d-flex gap-1 gap-lg-3 align-items-center">
                <div class="icon-btn d-lg-none rounded-circle">
                    <button class="nav-link ">
                        <i
                            class="fa-solid fa-magnifying-glass fa-lg"></i>
                    </button>
                </div>
                <div
                    class="dropdown navbar-cart position-relative border-lg border-lg-2 rounded-circle icon-btn ">
                    <a href="?page=cart" class="btn nav-link position-relative">
                        <i
                            class="fa-solid fa-cart-arrow-down fa-lg"></i>

                        <span
                            class="badge rounded-circle position-absolute"
                            id="cartCount">
                            <?= $totalCartItems ?>
                        </span>
                    </a>

                    <div
                        class="dropdown-menu cart p-3 rounded-4 shadow-lg" style="min-width: 320px;">
                        <?php if (empty($cartItems)): ?>
                            <div class="list-card mb-3 text-center py-3">
                                <i class="fa-solid fa-bag-shopping fa-3x text-muted mb-2"></i>
                                <div class="text-muted">
                                    Chưa có sản phẩm
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mini-cart-list mb-3" style="max-height: 250px; overflow-y: auto;">
                                <?php
                                $count = 0;
                                foreach ($cartItems as $item):
                                    if ($count++ >= 3) break;
                                ?>
                                    <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                        <img src="<?= htmlspecialchars(getProductImage($item['image'] ?? '')) ?>" alt="" style="width: 50px; height: 50px; object-fit: contain;">
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <div class="text-truncate small fw-bold text-dark" style="display:block; max-width: 200px;"><?= htmlspecialchars($item['name']) ?></div>
                                            <div class="text-muted small" style="font-size: 11px;"><?= htmlspecialchars($item['attributes'] ?? '') ?></div>
                                            <div class="small text-danger"><?= htmlspecialchars($item['qty']) ?> x <?= htmlspecialchars(formatVND($item['price'])) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (count($cartItems) > 3): ?>
                                    <div class="text-center text-muted small py-1">và <?= count($cartItems) - 3 ?> sản phẩm khác...</div>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="?page=cart" class="btn btn-outline-dark  flex-grow-1 ">Giỏ hàng</a>
                                <a href="?page=checkout" class="btn btn-dark  flex-grow-1 ">Thanh toán</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="dropdown position-relative">
                    <button
                        class="nav-link dropdown-toggle icon-btn  border-lg border-lg-2 rounded-circle p-1"
                        href="#">
                        <svg
                            version="1.1"
                            id="Layer_1"
                            xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink"
                            x="0px"
                            y="0px"
                            viewBox="0 0 850.39 850.39"
                            style="
                                        enable-background: new 0 0 850.39 850.39;
                                    "
                            xml:space="preserve">
                            <path
                                fill="currentColor"
                                d="M797.17,444.28L641.85,206.29c-14.4-37.32-50.59-63.8-92.99-63.8c-35.78,0-67.14,18.86-84.72,47.18
								   c-11.68-9.55-26.59-15.28-42.85-15.28c-18.79,0-35.78,7.65-48.06,20c-16.92-30.92-49.75-51.89-87.48-51.89
								   c-38.92,0-72.61,22.33-89.02,54.86L53.74,443.61c-12.2,23.22-19.14,49.61-19.14,77.61c0,92.32,75.11,167.44,167.44,167.44
								   c55.47,0,95.68-27.91,135.19-68.81l88.06-98.63l88.06,98.63c39.51,36.92,79.72,68.81,135.19,68.81
								   c92.33,0,167.44-75.11,167.44-167.44C815.97,493.49,809.15,467.34,797.17,444.28z M202.03,628.85
								   c-59.45,0-107.64-48.19-107.64-107.64s48.19-107.64,107.64-107.64s107.64,48.19,107.64,107.64S261.48,628.85,202.03,628.85z
									M648.53,628.85c-59.45,0-107.64-48.19-107.64-107.64s48.19-107.64,107.64-107.64s107.64,48.19,107.64,107.64
								   S707.98,628.85,648.53,628.85z"></path>
                        </svg>
                    </button>

                    <div
                        class="dropdown-menu p-3 rounded-4 shadow-lg position-absolute">
                        <a class="dropdown-item" href="?page=order-track">Tra cứu đơn hàng</a>
                    </div>
                </div>

                <div class="dropdown position-relative">
                    <button
                        class="nav-link icon-btn border-lg border-lg-2 rounded-circle d-flex align-items-center justify-content-center"
                        href="#" style="gap: 5px;">
                        <i class="fa-solid fa-user fa-lg"></i>

                    </button>

                    <div
                        class="dropdown-menu p-3 rounded-4 shadow-lg position-absolute dropdown-menu-end" style="min-width: 180px;">
                        <?php if (isset($_SESSION['user'])): ?>
                            <div class="dropdown-header px-2 py-1 text-dark border-bottom mb-2">
                                <?php
                                $userRankInfo = $_SESSION['user']['hang_khach_hang'] ?? [
                                    'ten_hang' => 'Đồng',
                                    'mau_sac' => '#cd7f32',
                                    'bieu_tuong' => 'bi-star-half'
                                ];
                                $rankName = $userRankInfo['ten_hang'];
                                $rankColor = $userRankInfo['mau_sac'];
                                $rankIcon = $userRankInfo['bieu_tuong'];

                                // Make background color lighter version of the color (for simplicity we just use transparent or rgba if it was hex, here we use inline styles with opacity)
                                $rankBgColor = $rankName === 'Kim Cương' ? 'linear-gradient(135deg, #b9f2ff 0%, #30c5e6 100%)' : $rankColor . '33'; // append 33 for 20% opacity in hex
                                ?>
                                <div class="fw-bold" style="font-size: 1.4rem;">
                                    <?= htmlspecialchars($_SESSION['user']['ho_ten']) ?>
                                </div>
                                <div class="mt-1 d-inline-block rounded px-2 py-1 fw-bold" style="font-size: 0.8rem; <?= $rankName === 'Kim Cương' ? 'background: ' . $rankBgColor . '; color: #000;' : 'background-color: ' . $rankBgColor . '; color: ' . $rankColor . ';' ?>">
                                    <i class="bi <?= htmlspecialchars($rankIcon) ?> me-1"></i>Hạng <?= htmlspecialchars($rankName) ?>
                                </div>
                            </div>
                            <?php if (isset($_SESSION['user']) && $_SESSION['user']['vai_tro'] === 'quan_tri'): ?>
                                <a class="dropdown-item fw-bold text-primary" href="?page=admin-dashboard">
                                    <i class="bi bi-shield-lock me-2"></i>Trang quản trị
                                </a>
                                <hr class="dropdown-divider my-1">
                            <?php endif; ?>
                            <a class="dropdown-item" href="?page=profile">
                                <i class="bi bi-person-circle me-2"></i>Thông tin cá nhân
                            </a>
                            <a class="dropdown-item" href="?page=my-orders">
                                <i class="bi bi-bag-heart me-2"></i>Đơn hàng của tôi
                            </a>
                            <a class="dropdown-item" href="?page=order-track">
                                <i class="bi bi-search me-2"></i>Tra cứu đơn hàng
                            </a>
                            <a class="dropdown-item" href="?page=change-password">
                                <i class="bi bi-key me-2"></i>Đổi mật khẩu
                            </a>
                            <hr class="dropdown-divider my-1">
                            <a class="dropdown-item text-danger" href="?page=logout"><i class="fa-solid fa-arrow-right-from-bracket pe-2"></i>Đăng xuất</a>
                        <?php else: ?>
                            <a class="dropdown-item" href="?page=login"><i
                                    class="fa-solid fa-arrow-right-from-bracket pe-1"></i>Đăng nhập</a>
                            <a class="dropdown-item" href="?page=register"><i class="fa-solid fa-user-plus pe-1"></i>Đăng ký</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="collapse navbar-collapse w-100" id="mainMenu">
        <div class="d-flex justify-content-between">
            <div class="logo-container ">
                <a class="logo-container__link" href="#home">
                    <img
                        src="<?= htmlspecialchars($cauhinh['logo_url'] ?? 'assets/images/favicons/Logo.png') ?>"
                        alt="Logo"
                        class="logo-container__icon" />
                </a>
            </div>
            <button
                class="close-btn"
                data-bs-toggle="collapse"
                data-bs-target="#mainMenu">
                ✕
            </button>
        </div>
        <ul class="navbar-nav mt-5 mx-auto  mb-lg-0 w-100">
            <li class="nav-item active">
                <a class="nav-link " href="?page=home">Trang chủ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="#!">Sản phẩm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?page=flash-sale">Giảm giá</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?page=new">Tin tức</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?page=about">Giới thiệu</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#contact">Liên hệ</a>
            </li>
        </ul>
    </div>
    <div class="container-nav">
        <div class="container-xl">
            <nav class=" menu_chinh d-none d-lg-block">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 position-relative ">
                    <li class="nav-item ">
                        <a class="nav-link <?= ($page === 'home') ? 'active' : '' ?> " href="?page=home">Trang chủ</a>
                    </li>
                    <li class="nav-item dropdown mega-menu ">
                        <a class="nav-link <?= ($page === 'product-index') ? 'active' : '' ?> dropdown-toggle" href="#!">Sản phẩm <i class="fa-solid fa-angle-down fa-md"></i></a>
                        <?php require __DIR__ . '/MegaMenuHome.php'; ?>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= ($page === 'flash-sale') ? 'active' : '' ?>" href="?page=flash-sale">Giảm giá</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page === 'new') ? 'active' : '' ?>" href="?page=new">Tin tức</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page === 'about') ? 'active' : '' ?>" href="?page=about">Giới thiệu</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= ($page === 'contact') ? 'active' : '' ?>" href="?page=contact">Liên hệ</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<!-- Toast Container for Notifications -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 99999;">
    <div id="cartToast" class="toast align-items-center text-white bg-dark border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-check text-success fs-5"></i>
                <span id="cartToastMessage" class="fw-semibold">Thêm vào giỏ hàng thành công!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>