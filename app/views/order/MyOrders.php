<?php
// Định nghĩa bộ trạng thái đơn hàng — đồng bộ với admin
$statusMap = [
    'cho_xac_nhan' => ['label' => 'Chờ xác nhận',  'icon' => 'bi-hourglass-split',  'class' => 'status-cho_xac_nhan', 'step' => 1],
    'dang_xu_ly'   => ['label' => 'Đang xử lý',    'icon' => 'bi-gear-fill',        'class' => 'status-dang_xu_ly',   'step' => 2],
    'dang_giao'    => ['label' => 'Đang giao hàng', 'icon' => 'bi-truck',            'class' => 'status-dang_giao',    'step' => 3],
    'hoan_thanh'   => ['label' => 'Giao thành công', 'icon' => 'bi-patch-check-fill', 'class' => 'status-hoan_thanh',   'step' => 4],
    'da_huy'       => ['label' => 'Đã hủy',         'icon' => 'bi-x-circle-fill',   'class' => 'status-da_huy',       'step' => 0],
];

// Dòng thời gian hiển thị tiến trình đơn hàng
$timelineSteps = [
    ['key' => 'cho_xac_nhan', 'icon' => 'bi-hourglass-split',  'label' => 'Xác nhận'],
    ['key' => 'dang_xu_ly',   'icon' => 'bi-gear-fill',        'label' => 'Xử lý'],
    ['key' => 'dang_giao',    'icon' => 'bi-truck',            'label' => 'Vận chuyển'],
    ['key' => 'hoan_thanh',   'icon' => 'bi-patch-check-fill', 'label' => 'Đã giao'],
];

// Tabs lọc đơn hàng
$tabs = [
    'all'          => ['label' => 'Tất cả',        'icon' => 'bi-grid'],
    'cho_xac_nhan' => ['label' => 'Chờ xác nhận', 'icon' => 'bi-hourglass-split'],
    'dang_xu_ly'   => ['label' => 'Đang xử lý',   'icon' => 'bi-gear-fill'],
    'dang_giao'    => ['label' => 'Đang giao',     'icon' => 'bi-truck'],
    'hoan_thanh'   => ['label' => 'Thành công',    'icon' => 'bi-patch-check-fill'],
    'da_huy'       => ['label' => 'Đã hủy',       'icon' => 'bi-x-circle'],
];

$totalAll = array_sum($statusCounts ?? []);
?>

<!-- ============ HERO HEADER ============ -->
<div class="my-orders-hero">
    <div class="container-xl">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                style="width:52px;height:52px;background:rgba(255,255,255,.15);">
                <i class="bi bi-bag-heart-fill" style="font-size:1.5rem;"></i>
            </div>
            <div>
                <h1 class="mb-0">Đơn hàng của tôi</h1>
                <p class="mb-0">Xin chào, <strong><?= htmlspecialchars($_SESSION['user']['ho_ten'] ?? 'Bạn') ?></strong> — theo dõi toàn bộ đơn hàng của bạn tại đây.</p>
            </div>
        </div>
    </div>
</div>

<div class="container-xl pb-5">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 mt-2" role="alert" style="font-size: 1.4rem;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 mt-2" role="alert" style="font-size: 1.4rem;">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- ============ STATUS TABS ============ -->
    <div class="order-tabs">
        <?php foreach ($tabs as $key => $tab): ?>
            <?php
            $count = ($key === 'all') ? $totalAll : ($statusCounts[$key] ?? 0);
            $isActive = ($activeTab === $key);
            ?>
            <a href="?page=my-orders&status=<?= $key ?>"
                class="order-tab <?= $isActive ? 'active' : '' ?>"
                id="tab-<?= $key ?>">
                <i class="bi <?= $tab['icon'] ?>"></i>
                <?= $tab['label'] ?>
                <?php if ($count > 0): ?>
                    <span class="tab-badge"><?= $count ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- ============ ORDER LIST ============ -->
    <?php if (empty($orders)): ?>
        <div class="empty-orders">
            <div class="empty-orders-icon">📦</div>
            <h4>Chưa có đơn hàng nào</h4>
            <p>Bạn chưa có đơn hàng nào trong mục này. Hãy khám phá các sản phẩm thể thao của chúng tôi!</p>
            <a href="?page=product-index" class="btn btn-primary mt-3 px-4 rounded-3 fw-semibold">
                <i class="bi bi-bag me-2"></i>Mua sắm ngay
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php
            $status    = $order->getTrang_thai_don_hang();
            $statusInfo = $statusMap[$status] ?? ['label' => $status, 'icon' => 'bi-question-circle', 'class' => '', 'step' => 0];
            $isCancelled = ($status === 'da_huy');
            $currentStep = $statusInfo['step'];
            ?>
            <div class="order-card">
                <!-- Card Header -->
                <div class="order-card-header" onclick="window.location='?page=order-success&code=<?= $order->getMa_don_hang() ?>'" style="cursor: pointer;">
                    <div>
                        <span class="text-muted small me-2">Mã đơn hàng:</span>
                        <div class="d-flex gap-1 align-items-center"><strong class="order-code">#<?= htmlspecialchars($order->getMa_don_hang()) ?></strong>
                            <div class="status-badge <?= htmlspecialchars($statusInfo['class']) ?>">
                                <i class="bi <?= htmlspecialchars($statusInfo['icon']) ?>"></i>
                                <span><?= htmlspecialchars($statusInfo['label']) ?></span>
                            </div>
                        </div>
                    </div>
                    <span class="order-date small text-muted">
                        <i class="bi bi-clock me-1"></i>
                        <?= $order->getNgay_tao() ? $order->getNgay_tao()->format('d/m/Y H:i') : '' ?>
                    </span>
                </div>



                <div class="d-flex justify-content-between flex-wrap"
                    onclick="window.location='?page=order-success&code=<?= $order->getMa_don_hang() ?>'">

                    <!-- Card Body — danh sách sản phẩm (lấy từ DB) -->
                    <?php
                    // Lấy chi tiết đơn hàng để hiển thị sản phẩm
                    $orderDetails = (new \app\models\OrderModel())->getOrderDetails($order->getMa_don_hang());
                    $items = $orderDetails['items'] ?? [];
                    $showMax = 2; // chỉ hiển thị tối đa 2 sản phẩm trong card
                    ?>
                    <div class="order-card-body">
                        <?php foreach (array_slice($items, 0, $showMax) as $item): ?>
                            <div class="order-item">
                                <img src="<?= htmlspecialchars(getProductImage($item->getAnh_dai_dien())) ?>"
                                    alt="<?= htmlspecialchars($item->getTen_san_pham()) ?>"
                                    class="order-item-img">
                                <div class="flex-grow-1" style="min-width:0;">
                                    <div class="order-item-name text-truncate"><?= htmlspecialchars($item->getTen_san_pham()) ?></div>
                                    <?php if ($item->getThong_tin_bien_the()): ?>
                                        <div class="order-item-meta"><?= htmlspecialchars($item->getThong_tin_bien_the()) ?></div>
                                    <?php endif; ?>
                                    <div class="order-item-meta">Số lượng: <?= $item->getSo_luong() ?></div>
                                    <div class="order-item-price">Tổng tiền: <?= formatVND($item->getThanh_tien()) ?></div>
                                </div>

                            </div>
                        <?php endforeach; ?>
                        <?php if (count($items) > $showMax): ?>
                            <div class="order-more-items">
                                <i class="bi bi-three-dots"></i>
                                và <?= count($items) - $showMax ?> sản phẩm khác
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Card Footer -->
                    <div class="order-card-footer  gap-3" style="background: #fff;">

                        <div>
                            <div class="order-total-label">Tổng thanh toán</div>
                            <div class="order-total-amount"><?= formatVND($order->getTong_thanh_toan()) ?></div>
                        </div>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <?php if ($status === 'dang_giao'): ?>
                                <a href="?page=order-confirm-received&code=<?= htmlspecialchars($order->getMa_don_hang()) ?>"
                                    class="btn btn-sm btn-success text-white rounded-3 fw-semibold"
                                    onclick="event.stopPropagation();return confirm('Xác nhận đã nhận hàng thành công? Trạng thái đơn hàng sẽ chuyển thành Giao thành công.');">
                                    <i class="bi bi-check2-circle me-1"></i>Đã nhận hàng
                                </a>
                            <?php endif; ?>
                            <?php if ($status === 'hoan_thanh'): ?>
                                <div class="d-flex flex-column gap-1">
                                    <a href="?page=product-index"
                                        class="btn btn-sm btn-outline-success rounded-3 fw-semibold "
                                        onclick="event.stopPropagation();">

                                        <i class="bi bi-arrow-repeat me-1"></i>Mua lại
                                    </a>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-warning rounded-3 fw-semibold text-dark"
                                        onclick="event.stopPropagation();toggleReviewForm('<?= htmlspecialchars($order->getMa_don_hang()) ?>')">
                                        <i class="bi bi-star-fill me-1"></i>Đánh giá
                                    </button>
                                </div>
                            <?php endif; ?>
                            <?php if ($status === 'cho_xac_nhan'): ?>
                                <a href="?page=order-cancel&code=<?= htmlspecialchars($order->getMa_don_hang()) ?>"
                                    class="btn btn-sm btn-outline-danger rounded-3 fw-semibold"
                                    onclick="event.stopPropagation();return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                    <i class="bi bi-x-circle me-1"></i>Hủy đơn
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
                <!-- Review Form Container -->
                <?php if ($status === 'hoan_thanh'): ?>
                    <div class="review-form-container border-top p-3 bg-light" id="review-form-<?= htmlspecialchars($order->getMa_don_hang()) ?>" style="display: none;">
                        <h5 class="mb-3 text-dark fw-bold" style="font-size: 1.4rem;"><i class="bi bi-star-fill text-warning me-2"></i>Đánh giá sản phẩm đã mua</h5>
                        <form action="?page=submit-review" method="POST">
                            <input type="hidden" name="ma_don_hang" value="<?= $order->getId() ?>">
                            <?php 
                            $orderModelInstance = new \app\models\OrderModel();
                            $unreviewedCount = 0;
                            foreach ($items as $item) {
                                if (!$orderModelInstance->hasReviewedProduct($_SESSION['user']['id'], $item->getMa_san_pham(), $order->getId())) {
                                    $unreviewedCount++;
                                }
                            }
                            ?>
                            <?php foreach ($items as $item): ?>
                                <?php
                                $prodId = $item->getMa_san_pham();
                                $hasReviewed = $orderModelInstance->hasReviewedProduct($_SESSION['user']['id'], $prodId, $order->getId());
                                $reviewInfo = null;
                                if ($hasReviewed) {
                                    $reviewInfo = $orderModelInstance->getProductReviewForOrder($_SESSION['user']['id'], $prodId, $order->getId());
                                }
                                ?>
                                <div class="review-product-item pb-3 mb-3 border-bottom d-flex flex-column gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= htmlspecialchars(getProductImage($item->getAnh_dai_dien())) ?>" alt="" style="width: 45px; height: 45px; object-fit: contain; border: 1px solid #ddd; padding: 2px; border-radius: 6px;">
                                        <div>
                                            <div class="fw-semibold text-dark" style="font-size: 1.3rem;"><?= htmlspecialchars($item->getTen_san_pham()) ?></div>
                                            <?php if ($item->getThong_tin_bien_the()): ?>
                                                <div class="text-muted small"><?= htmlspecialchars($item->getThong_tin_bien_the()) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if ($hasReviewed && $reviewInfo): ?>
                                        <div class="p-2 mb-1 rounded border border-success-subtle bg-white mt-1">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <div class="d-flex align-items-center gap-1 text-warning">
                                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                                        <i class="bi <?= $s <= (int)$reviewInfo['diem_so'] ? 'bi-star-fill' : 'bi-star' ?>" style="font-size: 1.2rem;"></i>
                                                    <?php endfor; ?>
                                                    <span class="ms-1 text-success fw-bold small" style="font-size: 11px;"><i class="bi bi-patch-check-fill me-0.5"></i> Đã đánh giá</span>
                                                </div>
                                                <small class="text-muted" style="font-size: 11px;"><?= date('H:i d/m/Y', strtotime($reviewInfo['ngay_tao'])) ?></small>
                                            </div>
                                            <?php if (!empty($reviewInfo['binh_luan'])): ?>
                                                <div class="text-dark bg-light p-2 rounded small mt-1" style="font-size: 11.5px; border-left: 2px solid #ff7b00; font-style: italic;">
                                                    "<?= htmlspecialchars($reviewInfo['binh_luan']) ?>"
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <!-- Star Selector -->
                                        <div class="d-flex align-items-center gap-2 mt-2">
                                            <span class="text-muted small">Đánh giá sao:</span>
                                            <div class="star-rating-select d-flex gap-1" data-product-id="<?= $prodId ?>">
                                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                                    <i class="bi bi-star-fill text-warning star-btn" style="cursor: pointer; font-size: 1.6rem;" data-val="<?= $s ?>" onclick="setStar(this, <?= $prodId ?>, <?= $s ?>)"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <input type="hidden" name="reviews[<?= $prodId ?>][diem_so]" id="star-input-<?= $prodId ?>" value="5">
                                        </div>
                                        <!-- Comment Textarea -->
                                        <div class="mt-2">
                                            <textarea name="reviews[<?= $prodId ?>][binh_luan]" class="form-control rounded-3" rows="2" placeholder="Chia sẻ nhận xét của bạn về sản phẩm này..." style="font-size: 1.3rem;"></textarea>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>

                            <div class="d-flex justify-content-end gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" onclick="toggleReviewForm('<?= htmlspecialchars($order->getMa_don_hang()) ?>')"><?= $unreviewedCount > 0 ? 'Hủy bỏ' : 'Đóng lại' ?></button>
                                <?php if ($unreviewedCount > 0): ?>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-3 text-white fw-semibold">Gửi tất cả đánh giá</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>


        <?php endforeach; ?>
    <?php endif; ?>

</div>

<script>
    function setStar(starEl, productId, val) {
        const parent = starEl.parentElement;
        const input = document.getElementById('star-input-' + productId);
        if (input) {
            input.value = val;
        }
        const stars = parent.querySelectorAll('.star-btn');
        stars.forEach(s => {
            const starVal = parseInt(s.getAttribute('data-val'));
            if (starVal <= val) {
                s.classList.remove('bi-star');
                s.classList.add('bi-star-fill');
            } else {
                s.classList.remove('bi-star-fill');
                s.classList.add('bi-star');
            }
        });
    }

    function toggleReviewForm(orderCode) {
        const el = document.getElementById('review-form-' + orderCode);
        if (el) {
            if (el.style.display === 'none') {
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        }
    }
</script>