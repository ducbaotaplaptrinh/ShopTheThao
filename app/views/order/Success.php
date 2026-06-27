<?php
// Định nghĩa bộ trạng thái đơn hàng — đồng bộ với admin
$statusMap = [
    'cho_xac_nhan' => ['label' => 'Chờ xác nhận',  'icon' => 'bi-hourglass-split',  'class' => 'status-cho_xac_nhan', 'step' => 1],
    'dang_xu_ly'   => ['label' => 'Đang xử lý',    'icon' => 'bi-gear-fill',        'class' => 'status-dang_xu_ly',   'step' => 2],
    'dang_giao'    => ['label' => 'Đang giao hàng', 'icon' => 'bi-truck',            'class' => 'status-dang_giao',    'step' => 3],
    'hoan_thanh'   => ['label' => 'Giao thành công', 'icon' => 'bi-patch-check-fill', 'class' => 'status-hoan_thanh',   'step' => 4],
    'da_huy'       => ['label' => 'Đã hủy',         'icon' => 'bi-x-circle-fill',   'class' => 'status-da_huy',       'step' => 0],
];

$timelineSteps = [
    ['key' => 'cho_xac_nhan', 'icon' => 'bi-hourglass-split',  'label' => 'Xác nhận'],
    ['key' => 'dang_xu_ly',   'icon' => 'bi-gear-fill',        'label' => 'Xử lý'],
    ['key' => 'dang_giao',    'icon' => 'bi-truck',            'label' => 'Vận chuyển'],
    ['key' => 'hoan_thanh',   'icon' => 'bi-patch-check-fill', 'label' => 'Đã giao'],
];

$status = $order->getTrang_thai_don_hang();
$statusInfo = $statusMap[$status] ?? ['label' => $status, 'icon' => 'bi-question-circle', 'class' => '', 'step' => 0];
?>
<div class="container-xl py-5">
    <div class="card text-center py-5 shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <div class="mb-4 text-success">
                <i class="bi bi-check-circle-fill" style="font-size: 5rem; color: #28a745;"></i>
            </div>
            <h2 class="mb-3">Thông Tin Đơn Hàng</h2>
            <p class="text-muted mb-4 fs-5">Cảm ơn bạn đã mua hàng tại <strong>Bảo Đạt Sport</strong>.</p>
            <div class="d-inline-block bg-light px-4 py-3 rounded-4 mb-3">
                <span class="text-muted d-block small">Mã đơn hàng của bạn:</span>
                <strong class="fs-4 text-primary text-uppercase"><?= htmlspecialchars($order->getMa_don_hang()) ?></strong>
            </div>
            <p class="text-muted small">Vui lòng lưu lại mã đơn hàng này để có thể <a href="?page=order-track&term=<?= htmlspecialchars($order->getMa_don_hang()) ?>">Tra cứu hành trình đơn hàng</a>.</p>
            
            <div class="mt-4 pt-4 border-top">
                <h5 class="mb-3 text-dark fw-bold">
                    Trạng thái đơn hàng: 
                    <span class="status-badge <?= htmlspecialchars($statusInfo['class']) ?>" style="font-size: 1.2rem; padding: 6px 16px;">
                        <i class="bi <?= htmlspecialchars($statusInfo['icon']) ?> me-1"></i>
                        <?= htmlspecialchars($statusInfo['label']) ?>
                    </span>
                </h5>
                
                <?php if ($status !== 'da_huy'): ?>
                    <div class="order-timeline">
                        <?php foreach ($timelineSteps as $step): ?>
                            <?php
                            $stepKey = $step['key'];
                            $stepLabel = $step['label'];
                            $stepIcon = $step['icon'];
                            
                            $isStepDone = false;
                            $isStepActive = false;
                            
                            $orderStepVal = $statusMap[$status]['step'];
                            $currentStepVal = $statusMap[$stepKey]['step'];
                            
                            if ($orderStepVal >= $currentStepVal) {
                                $isStepDone = true;
                            }
                            if ($status === $stepKey) {
                                $isStepActive = true;
                            }
                            
                            $stepClass = '';
                            if ($isStepActive) {
                                $stepClass = 'active-step';
                            } elseif ($isStepDone) {
                                $stepClass = 'done';
                            }
                            ?>
                            <div class="tl-step <?= $stepClass ?>">
                                <div class="tl-icon">
                                    <i class="bi <?= $stepIcon ?>"></i>
                                </div>
                                <div class="tl-label"><?= $stepLabel ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="order-timeline">
                        <div class="tl-step cancelled">
                            <div class="tl-icon">
                                <i class="bi bi-x-circle-fill"></i>
                            </div>
                            <div class="tl-label text-danger">Đã hủy đơn</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Order Summary details -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                <h4 class="mb-4 text-dark"><i class="bi bi-file-earmark-text me-2"></i>Chi tiết người nhận</h4>

                <table class="table table-borderless align-middle mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted py-2" style="width: 160px;">Họ tên người nhận:</td>
                            <td class="fw-semibold text-dark py-2"><?= htmlspecialchars($order->getHo_ten_nguoi_nhan()) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Số điện thoại:</td>
                            <td class="fw-semibold text-dark py-2"><?= htmlspecialchars($order->getSo_dien_thoai()) ?></td>
                        </tr>
                        <?php if (!empty($order->getEmail())): ?>
                            <tr>
                                <td class="text-muted py-2">Email:</td>
                                <td class="text-dark py-2"><?= htmlspecialchars($order->getEmail()) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-muted py-2">Địa chỉ giao hàng:</td>
                            <td class="text-dark py-2"><?= nl2br(htmlspecialchars($order->getDia_chi_giao_hang())) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Phương thức:</td>
                            <td class="fw-semibold text-primary py-2 text-uppercase"><?= htmlspecialchars($order->getPhuong_thuc_thanh_toan() === 'cod' ? 'Thanh toán COD' : 'Chuyển khoản') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Tổng thanh toán:</td>
                            <td class="fw-bold text-danger fs-5 py-2"><?= htmlspecialchars(formatVND($order->getTong_thanh_toan())) ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4">
                    <?php $orderStatus = $order->getTrang_thai_don_hang(); ?>
                    <?php if ($orderStatus === 'cho_xac_nhan'): ?>
                        <div class="d-flex gap-2">
                            <a href="?page=home" class="btn btn-outline-dark w-50 fw-semibold rounded-3 py-2">
                                Quay lại trang chủ
                            </a>
                            <a href="?page=order-cancel&code=<?= htmlspecialchars($order->getMa_don_hang()) ?>" 
                               class="btn btn-danger w-50 fw-semibold rounded-3 py-2"
                               onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                <i class="bi bi-x-circle me-1"></i>Hủy đơn hàng
                            </a>
                        </div>
                    <?php elseif ($orderStatus === 'dang_giao'): ?>
                        <div class="d-flex gap-2">
                            <a href="?page=home" class="btn btn-outline-dark w-50 fw-semibold rounded-3 py-2">
                                Quay lại trang chủ
                            </a>
                            <a href="?page=order-confirm-received&code=<?= htmlspecialchars($order->getMa_don_hang()) ?>" 
                               class="btn btn-success text-white w-50 fw-semibold rounded-3 py-2"
                               onclick="return confirm('Xác nhận đã nhận hàng thành công? Trạng thái đơn hàng sẽ chuyển thành Giao thành công.');">
                                <i class="bi bi-check2-circle me-1"></i>Đã nhận được hàng
                            </a>
                        </div>
                    <?php elseif ($orderStatus === 'hoan_thanh'): ?>
                        <div class="d-flex gap-2">
                            <a href="?page=home" class="btn btn-outline-dark w-50 fw-semibold rounded-3 py-2">
                                Quay lại trang chủ
                            </a>
                            <button type="button" class="btn btn-warning w-50 fw-semibold rounded-3 py-2 text-dark" onclick="toggleReviewForm('<?= htmlspecialchars($order->getMa_don_hang()) ?>')">
                                <i class="bi bi-star-fill me-1"></i>Đánh giá sản phẩm
                            </button>
                        </div>
                    <?php else: ?>
                        <a href="?page=home" class="btn btn-outline-dark w-100 fw-semibold rounded-3 py-2">
                            Quay lại trang chủ
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right: Payment Transfer QR info if bank transfer is selected -->
        <div class="col-lg-6">
            <?php if ($order->getPhuong_thuc_thanh_toan() === 'chuyen_khoan'): ?>
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100 text-center">
                    <h4 class="mb-3 text-dark text-lg-start"><i class="bi bi-qr-code-scan me-2"></i>Chuyển khoản thanh toán</h4>
                    <p class="text-muted text-lg-start small mb-4">Vui lòng quét mã QR dưới đây hoặc chuyển khoản theo thông tin tài khoản ngân hàng để hoàn thành thanh toán.</p>

                    <div class="row g-3 align-items-center">
                        <div class="col-sm-6 text-center">
                            <?php
                            $qrAmount = (int)$order->getTong_thanh_toan();
                            $qrCode = htmlspecialchars($order->getMa_don_hang());
                            
                            // Nếu admin tải lên QR Code tĩnh riêng (Momo, QR cố định...) thì dùng ảnh đó
                            if (!empty($cauhinh['qr_code_url'])) {
                                $qrUrl = $cauhinh['qr_code_url'];
                            } else {
                                // Ngược lại tự tạo mã VietQR động theo cấu hình ngân hàng
                                $bankCode = urlencode($cauhinh['bank_name'] ?? 'vietinbank');
                                $bankAcc = urlencode($cauhinh['bank_account'] ?? '102873928192');
                                $bankOwner = urlencode($cauhinh['bank_owner'] ?? 'CONG TY THE THAO BAO DAT');
                                $qrUrl = "https://img.vietqr.io/image/{$bankCode}-{$bankAcc}-compact2.png?amount={$qrAmount}&addInfo={$qrCode}&accountName={$bankOwner}";
                            }
                            
                            // Bản đồ tên hiển thị ngân hàng
                            $bankNamesMap = [
                                'vietinbank' => 'VietinBank',
                                'mbbank' => 'MBBank',
                                'vcb' => 'Vietcombank',
                                'bidv' => 'BIDV',
                                'tcb' => 'Techcombank',
                                'acb' => 'ACB',
                                'sacombank' => 'Sacombank',
                                'tpbank' => 'TPBank',
                                'vpbank' => 'VPBank'
                            ];
                            $displayBankName = $bankNamesMap[strtolower($cauhinh['bank_name'] ?? '')] ?? strtoupper($cauhinh['bank_name'] ?? 'VietinBank');
                            ?>
                            <img src="<?= $qrUrl ?>" alt="Mã QR Chuyển khoản" class="img-fluid border rounded-3 p-2 shadow-sm" style="max-height: 250px;">
                        </div>
                        <div class="col-sm-6 text-start">
                            <div class="mb-2">
                                <span class="text-muted small d-block">Ngân hàng:</span>
                                <strong class="text-dark"><?= htmlspecialchars($displayBankName) ?></strong>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Số tài khoản:</span>
                                <strong class="text-primary fs-5"><?= htmlspecialchars($cauhinh['bank_account'] ?? '102873928192') ?></strong>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Chủ tài khoản:</span>
                                <strong class="text-dark"><?= htmlspecialchars($cauhinh['bank_owner'] ?? 'CONG TY TNHH THE THAO BAO DAT') ?></strong>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Số tiền chuyển:</span>
                                <strong class="text-danger fs-5"><?= htmlspecialchars(formatVND($order->getTong_thanh_toan())) ?></strong>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Nội dung chuyển khoản:</span>
                                <strong class="text-primary fs-5 text-uppercase"><?= htmlspecialchars($order->getMa_don_hang()) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                    <h4 class="mb-4 text-dark"><i class="bi bi-box-seam me-2"></i>Thông tin đơn hàng đã đặt</h4>

                    <div class="ordered-items-list" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($items as $item): ?>
                            <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                                <img src="<?= htmlspecialchars(getProductImage($item->getAnh_dai_dien())) ?>" alt="" style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px; border: 1px solid #eee; padding: 2px;">
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <div class="text-truncate fw-semibold text-dark small"><?= htmlspecialchars($item->getTen_san_pham()) ?></div>
                                    <?php if (!empty($item->getThong_tin_bien_the())): ?>
                                        <div class="text-muted small" style="font-size: 11px;"><?= htmlspecialchars($item->getThong_tin_bien_the()) ?></div>
                                    <?php endif; ?>
                                    <div class="small text-muted mt-1">Số lượng: <?= htmlspecialchars($item->getSo_luong()) ?></div>
                                </div>
                                <div class="fw-bold text-end text-dark"><?= htmlspecialchars(formatVND($item->getThanh_tien())) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Review Form at bottom for Success.php -->
    <?php if ($order->getTrang_thai_don_hang() === 'hoan_thanh'): ?>
        <div class="card shadow-sm border-0 rounded-4 p-4 mt-4" id="review-form-<?= htmlspecialchars($order->getMa_don_hang()) ?>" style="display: none;">
            <h4 class="mb-4 text-dark fw-bold" style="font-size: 1.4rem;"><i class="bi bi-star-fill text-warning me-2"></i>Viết đánh giá sản phẩm</h4>
            <form action="?page=submit-review" method="POST">
                <?php foreach ($items as $item): ?>
                    <?php
                    $prodId = $item->getMa_san_pham();
                    $hasReviewed = (new \app\models\OrderModel())->hasReviewedProduct($_SESSION['user']['id'], $prodId);
                    ?>
                    <div class="review-product-item pb-3 mb-3 border-bottom d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?= htmlspecialchars(getProductImage($item->getAnh_dai_dien())) ?>" alt="" style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #eee; padding: 2px; border-radius: 8px;">
                            <div>
                                <div class="fw-semibold text-dark" style="font-size: 1.3rem;"><?= htmlspecialchars($item->getTen_san_pham()) ?></div>
                                <?php if ($item->getThong_tin_bien_the()): ?>
                                    <div class="text-muted small"><?= htmlspecialchars($item->getThong_tin_bien_the()) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($hasReviewed): ?>
                            <div class="text-success small fw-semibold mt-1"><i class="bi bi-patch-check-fill me-1"></i>Bạn đã đánh giá sản phẩm này</div>
                        <?php else: ?>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <span class="text-muted small">Đánh giá sao:</span>
                                <div class="star-rating-select d-flex gap-1" data-product-id="<?= $prodId ?>">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                        <i class="bi bi-star-fill text-warning star-btn" style="cursor: pointer; font-size: 1.6rem;" data-val="<?= $s ?>" onclick="setStar(this, <?= $prodId ?>, <?= $s ?>)"></i>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="reviews[<?= $prodId ?>][diem_so]" id="star-input-<?= $prodId ?>" value="5">
                            </div>
                            <div class="mt-2">
                                <textarea name="reviews[<?= $prodId ?>][binh_luan]" class="form-control rounded-3" rows="2" placeholder="Chia sẻ nhận xét của bạn..." style="font-size: 1.3rem;"></textarea>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <div class="d-flex justify-content-end gap-2 mt-2">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4" onclick="toggleReviewForm('<?= htmlspecialchars($order->getMa_don_hang()) ?>')">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary rounded-3 text-white fw-semibold px-4">Gửi đánh giá</button>
                </div>
            </form>
        </div>
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
            el.scrollIntoView({ behavior: 'smooth' });
        } else {
            el.style.display = 'none';
        }
    }
}
</script>