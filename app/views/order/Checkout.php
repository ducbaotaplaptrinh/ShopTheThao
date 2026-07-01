<div class="container-xl py-5 checkout-page">
    <div class="breadcrumb-wrapper mb-4">
        <a href="?page=home">Trang chủ ></a>
        <a href="?page=cart">Giỏ hàng ></a>
        <a href="#!" class="text-dark fw-bold">Thanh toán đơn hàng</a>
    </div>

    <h2 class="section-title mb-4">Thanh Toán</h2>

    <?php
    if (isset($_SESSION['order_error'])) {
        echo '<div class="alert alert-danger rounded-3">' . htmlspecialchars($_SESSION['order_error']) . '</div>';
        unset($_SESSION['order_error']);
    }
    ?>

    <div class="row g-4">
        <!-- Left: Checkout Form -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="mb-4">Thông tin giao hàng</h4>

                <form action="?page=order-place" method="POST" id="checkoutForm">
                    <?php
                    $user = $_SESSION['user'] ?? null;
                    ?>

                    <?php if (!empty($addresses)): ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary mb-2"><i class="bi bi-geo-alt me-1 text-primary"></i>Chọn địa chỉ giao hàng đã lưu</label>
                            <div class="row g-2">
                                <?php foreach ($addresses as $addr): 
                                    $fullAddress = $addr['dia_chi_chi_tiet'] . ', ' . $addr['phuong_xa'] . ', ' . $addr['quan_huyen'] . ', ' . $addr['tinh_thanh_pho'];
                                ?>
                                    <div class="col-12">
                                        <div class="card p-3 border rounded-3 address-select-card <?= $addr['la_mac_dinh'] ? 'border-primary bg-primary-subtle' : '' ?>" 
                                             style="cursor: pointer; transition: all 0.2s;"
                                             data-name="<?= htmlspecialchars($addr['ho_ten_nguoi_nhan']) ?>"
                                             data-phone="<?= htmlspecialchars($addr['so_dien_thoai']) ?>"
                                             data-address="<?= htmlspecialchars($fullAddress) ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="fw-bold text-dark"><?= htmlspecialchars($addr['ho_ten_nguoi_nhan']) ?></span>
                                                    <span class="text-muted ms-2">(<?= htmlspecialchars($addr['so_dien_thoai']) ?>)</span>
                                                    <div class="text-muted mt-1"><?= htmlspecialchars($fullAddress) ?></div>
                                                </div>
                                                <?php if ($addr['la_mac_dinh']): ?>
                                                    <span class="badge bg-primary">Mặc định</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="ho_ten" class="form-label fw-semibold">Họ và tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" id="ho_ten" name="ho_ten" required placeholder="Nhập họ tên đầy đủ" value="<?= $user ? htmlspecialchars($user['ho_ten']) : '' ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="so_dien_thoai" class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control rounded-3" id="so_dien_thoai" name="so_dien_thoai" required placeholder="Nhập số điện thoại nhận hàng" value="<?= $user ? htmlspecialchars($user['so_dien_thoai'] ?? '') : '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold">Địa chỉ Email</label>
                            <input type="email" class="form-control rounded-3" id="email" name="email" placeholder="Nhập email (không bắt buộc)" value="<?= $user ? htmlspecialchars($user['email']) : '' ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="dia_chi" class="form-label fw-semibold">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                        <textarea class="form-control rounded-3" id="dia_chi" name="dia_chi" rows="3" required placeholder="Số nhà, tên đường, xã/phường, quận/huyện, tỉnh/thành phố"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="ghi_chu" class="form-label fw-semibold">Ghi chú đơn hàng</label>
                        <textarea class="form-control rounded-3" id="ghi_chu" name="ghi_chu" rows="2" placeholder="Ghi chú về thời gian giao hàng, chỉ dẫn địa chỉ..."></textarea>
                    </div>

                    <h4 class="mb-3">Phương thức thanh toán</h4>
                    <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="payment_cod" value="cod" checked>
                            <label class="form-check-label fw-semibold text-dark" for="payment_cod">
                                <i class="bi bi-cash-stack text-success me-2"></i>Thanh toán khi nhận hàng (COD)
                            </label>
                            <div class="text-muted ms-4 mt-1">Khách hàng kiểm tra hàng và thanh toán tiền mặt trực tiếp cho nhân viên giao hàng.</div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="payment_bank" value="chuyen_khoan">
                            <label class="form-check-label fw-semibold text-dark" for="payment_bank">
                                <i class="bi bi-bank text-primary me-2"></i>Chuyển khoản ngân hàng (Qua mã QR)
                            </label>
                            <div class="text-muted ms-4 mt-1">Hệ thống sẽ hiển thị mã QR kèm thông tin số tài khoản ở bước sau để chuyển khoản.</div>
                        </div>
                    </div>

                    <input type="hidden" name="ma_code_su_dung" id="input-coupon-code" value="">
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold border-0 py-3 rounded-3" style="background: linear-gradient(135deg, #ff7b00, #ff9500);">
                        Xác nhận đặt hàng
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="mb-4">Đơn hàng của bạn</h4>

                <div class="checkout-items-list mb-3" style="max-height: 280px; overflow-y: auto;">
                    <?php foreach ($cartItems as $item):
                        // var_dump($cartItems);
                        // die();
                    ?>

                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                            <img src="<?= htmlspecialchars(getProductImage($item['image'])) ?>" alt="" style="width: 55px; height: 55px; object-fit: contain; border-radius: 8px; border: 1px solid #eee; padding: 2px;">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="text-truncate fw-semibold text-dark"><?= htmlspecialchars($item['name']) ?></div>
                                <?php if (!empty($item['attributes'])): ?>
                                    <div class="text-muted"><?= htmlspecialchars($item['attributes']) ?></div>
                                <?php endif; ?>
                                <div class="text-muted mt-1">Số lượng: <?= htmlspecialchars($item['qty']) ?></div>
                            </div>
                            <div class="fw-bold text-end text-dark" style="min-width: 90px;"><?= htmlspecialchars(formatVND($item['price'] * $item['qty'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <style>
                .available-coupons-scroll-container::-webkit-scrollbar {
                    width: 6px;
                }
                .available-coupons-scroll-container::-webkit-scrollbar-track {
                    background: #f1f1f1; 
                    border-radius: 4px;
                }
                .available-coupons-scroll-container::-webkit-scrollbar-thumb {
                    background: #ccc; 
                    border-radius: 4px;
                }
                .available-coupons-scroll-container::-webkit-scrollbar-thumb:hover {
                    background: #aaa; 
                }
                .coupon-item-select:hover {
                    border-color: #ff7b00 !important;
                    box-shadow: 0 2px 6px rgba(255, 123, 0, 0.08);
                }
                .coupon-item-select.active {
                    border-color: #ff7b00 !important;
                    background-color: #fff9f3 !important;
                }
                .coupon-item-select.active .select-coupon-btn {
                    background-color: #ff7b00 !important;
                    color: #fff !important;
                }
                </style>

                <div class="card mb-3 border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3 text-dark d-flex align-items-center">
                            <i class="bi bi-ticket-perforated text-warning me-2 fs-5"></i> Mã Giảm Giá
                        </h6>
                        
                        <!-- Ô nhập mã voucher -->
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" id="input-coupon-code-text" class="form-control form-control-sm text-uppercase fw-bold" placeholder="Nhập mã voucher..." style="letter-spacing: 0.5px;">
                                <button type="button" id="btn-apply-manual-coupon" class="btn btn-warning btn-sm text-white fw-bold px-3">Áp dụng</button>
                            </div>
                            <div id="coupon-alert-msg" class="text-danger small mt-1" style="display: none; font-size: 11px;"></div>
                        </div>

                        <!-- Danh sách voucher khả dụng -->
                        <div class="mb-1">
                            <small class="text-muted fw-semibold d-block mb-2">Mã giảm giá dành cho bạn:</small>
                            <?php if (!empty($availableCoupons)): ?>
                                <div class="available-coupons-scroll-container border rounded p-2" style="max-height: 180px; overflow-y: auto; background-color: #fcfcfc;">
                                    <?php foreach ($availableCoupons as $coupon): ?>
                                        <div class="coupon-item-select border rounded p-2 mb-2 bg-white d-flex justify-content-between align-items-center" 
                                             style="cursor: pointer; transition: all 0.2s; border-color: #e9ecef;"
                                             data-code="<?= htmlspecialchars($coupon['ma_code']) ?>"
                                             data-discount="<?= htmlspecialchars($coupon['gia_tri_giam']) ?>"
                                             data-type="<?= htmlspecialchars($coupon['loai_giam_gia']) ?>"
                                             data-max="<?= htmlspecialchars($coupon['muc_giam_toi_da'] ?? '') ?>">
                                            <div class="flex-grow-1" style="min-width: 0;">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle font-monospace fw-bold px-2 py-1" style="color: #ff7b00 !important; background-color: #fff3cd !important; border-color: #ffe69c !important; font-size: 10px;">
                                                        <?= htmlspecialchars($coupon['ma_code']) ?>
                                                    </span>
                                                    <small class="text-danger fw-bold" style="font-size: 11px;">
                                                        Giảm <?= $coupon['loai_giam_gia'] === 'phan_tram' ? (float)$coupon['gia_tri_giam'] . '%' : number_format($coupon['gia_tri_giam']) . 'đ' ?>
                                                        <?= ($coupon['loai_giam_gia'] === 'phan_tram' && !empty($coupon['muc_giam_toi_da'])) ? ' (Tối đa ' . number_format($coupon['muc_giam_toi_da']) . 'đ)' : '' ?>
                                                    </small>
                                                </div>
                                                <div class="text-muted text-truncate" style="font-size: 11px;" title="<?= htmlspecialchars($coupon['tieu_de']) ?>">
                                                    <?= htmlspecialchars($coupon['tieu_de']) ?>
                                                </div>
                                            </div>
                                            <div class="ms-2">
                                                <button type="button" class="btn btn-sm btn-outline-warning py-1 px-2.5 select-coupon-btn" style="font-size: 11px; border-color: #ff7b00; color: #ff7b00;">Chọn</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-muted small border rounded p-3 text-center bg-light" style="font-size: 12px;">
                                    Không có mã nào khả dụng cho đơn hàng này.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Tạm tính</span>
                        <strong><?= number_format($totalPayment) ?>đ</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Phí giao hàng</span>
                        <strong class="text-success">Miễn phí</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between text-success">
                        <span>Giảm giá (<span id="applied-coupon-code">Chưa áp dụng</span>)</span>
                        <strong>- <span id="discount-amount">0</span>đ</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between bg-light">
                        <span>Tổng cộng</span>
                        <strong class="text-danger fs-5"><span id="final-total"><?= number_format($totalPayment) ?></span>đ</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Script xử lý trang checkout -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Logic Xử Lý Chọn Địa Chỉ ---
    const addressCards = document.querySelectorAll('.address-select-card');
    const inputHoTen = document.getElementById('ho_ten');
    const inputSDT = document.getElementById('so_dien_thoai');
    const textareaDiaChi = document.getElementById('dia_chi');

    addressCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove active style from all cards
            addressCards.forEach(c => {
                c.classList.remove('border-primary', 'bg-primary-subtle');
                c.style.borderColor = '#ececec';
            });
            // Add active style to selected card
            this.classList.add('border-primary', 'bg-primary-subtle');
            this.style.borderColor = '#ff7b00';

            // Fill inputs
            inputHoTen.value = this.getAttribute('data-name');
            inputSDT.value = this.getAttribute('data-phone');
            textareaDiaChi.value = this.getAttribute('data-address');
        });
    });

    // Auto-fill default address on load
    const defaultCard = document.querySelector('.address-select-card.border-primary');
    if (defaultCard) {
        inputHoTen.value = defaultCard.getAttribute('data-name');
        inputSDT.value = defaultCard.getAttribute('data-phone');
        textareaDiaChi.value = defaultCard.getAttribute('data-address');
    }

    // --- Logic Xử Lý Voucher ---
    // Nhận dữ liệu từ PHP
    const baseTotal = <?= $totalPayment ?>;
    const availableCoupons = <?= json_encode($availableCoupons) ?>;
    
    // JS object của mã ngon nhất (nếu có)
    let currentCoupon = <?= $bestCoupon ? json_encode([
        'code' => $bestCoupon['ma_code'],
        'discount' => $bestCoupon['gia_tri_giam'],
        'type' => $bestCoupon['loai_giam_gia'],
        'max_discount' => $bestCoupon['muc_giam_toi_da']
    ]) : 'null' ?>;

    const discountEl = document.getElementById('discount-amount');
    const finalTotalEl = document.getElementById('final-total');
    const couponCodeEl = document.getElementById('applied-coupon-code');
    const inputCouponHidden = document.getElementById('input-coupon-code');
    const inputCouponText = document.getElementById('input-coupon-code-text');
    const btnApplyManual = document.getElementById('btn-apply-manual-coupon');
    const alertMsgEl = document.getElementById('coupon-alert-msg');
    const couponItems = document.querySelectorAll('.coupon-item-select');

    // Hàm cập nhật giao diện tính tiền
    function calculateTotal() {
        let discountValue = 0;
        if (currentCoupon) {
            if (currentCoupon.type === 'phan_tram') {
                discountValue = (baseTotal * parseFloat(currentCoupon.discount)) / 100;
                if (currentCoupon.max_discount && parseFloat(currentCoupon.max_discount) > 0) {
                    const maxD = parseFloat(currentCoupon.max_discount);
                    if (discountValue > maxD) {
                        discountValue = maxD;
                    }
                }
            } else {
                discountValue = parseFloat(currentCoupon.discount);
            }
            
            couponCodeEl.innerText = currentCoupon.code;
            if (inputCouponHidden) inputCouponHidden.value = currentCoupon.code;
            if (inputCouponText) inputCouponText.value = currentCoupon.code;
        } else {
            couponCodeEl.innerText = "Chưa áp dụng";
            if (inputCouponHidden) inputCouponHidden.value = "";
        }

        let finalPrice = baseTotal - discountValue;
        if (finalPrice < 0) finalPrice = 0; // Đảm bảo không bị âm tiền

        // Format số tiền kiểu Việt Nam (VD: 100.000)
        discountEl.innerText = discountValue.toLocaleString('vi-VN');
        finalTotalEl.innerText = finalPrice.toLocaleString('vi-VN');

        // Cập nhật trạng thái active trong danh sách
        couponItems.forEach(item => {
            const code = item.getAttribute('data-code');
            const btn = item.querySelector('.select-coupon-btn');
            if (currentCoupon && code === currentCoupon.code) {
                item.classList.add('active');
                if (btn) btn.innerText = 'Đang chọn';
            } else {
                item.classList.remove('active');
                if (btn) btn.innerText = 'Chọn';
            }
        });
    }

    // Chạy mặc định lần đầu khi load trang (Auto-apply voucher hời nhất)
    calculateTotal();

    // Bắt sự kiện click vào coupon item trong danh sách
    couponItems.forEach(item => {
        item.addEventListener('click', function() {
            if (alertMsgEl) alertMsgEl.style.display = 'none';
            
            const code = this.getAttribute('data-code');
            const discount = this.getAttribute('data-discount');
            const type = this.getAttribute('data-type');
            const maxD = this.getAttribute('data-max');
            
            // Nếu click lại vào mã đang chọn -> Bỏ chọn
            if (currentCoupon && currentCoupon.code === code) {
                currentCoupon = null;
                if (inputCouponText) inputCouponText.value = '';
            } else {
                currentCoupon = {
                    code: code,
                    discount: discount,
                    type: type,
                    max_discount: maxD
                };
            }
            calculateTotal();
        });
    });

    // Bắt sự kiện bấm nút Áp dụng thủ công
    if (btnApplyManual) {
        btnApplyManual.addEventListener('click', function() {
            if (!inputCouponText) return;
            const typedCode = inputCouponText.value.trim().toUpperCase();
            if (alertMsgEl) alertMsgEl.style.display = 'none';

            if (typedCode === '') {
                currentCoupon = null;
                calculateTotal();
                return;
            }

            // Tìm mã trong danh sách khả dụng
            const found = availableCoupons.find(c => c.ma_code.toUpperCase() === typedCode);
            if (found) {
                currentCoupon = {
                    code: found.ma_code,
                    discount: parseFloat(found.gia_tri_giam),
                    type: found.loai_giam_gia,
                    max_discount: found.muc_giam_toi_da
                };
                calculateTotal();
            } else {
                // Hiển thị thông báo lỗi
                currentCoupon = null;
                calculateTotal();
                if (alertMsgEl) {
                    alertMsgEl.innerText = "Mã giảm giá không hợp lệ hoặc không đủ điều kiện cho đơn hàng này!";
                    alertMsgEl.style.display = 'block';
                }
            }
        });
    }
});
</script>