<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Quản lý Giao diện & Thông tin hệ thống</h2>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Lưu cấu hình giao diện thành công! Dữ liệu đã được đồng bộ ra ngoài Frontend.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="?page=admin-setting-update" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            
            <!-- Cột trái: Cấu hình Logo & Liên hệ -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 text-primary"><i class="bi bi-telephone me-2"></i>Thông tin liên hệ & Mạng xã hội</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại hỗ trợ</label>
                                <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($setting['sdt']) ?>" placeholder="Ví dụ: 0900 123 456" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Địa chỉ Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($setting['email']) ?>" placeholder="Ví dụ: support@baodatsport.vn" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Địa chỉ cửa hàng</label>
                                <input type="text" name="dia_chi" class="form-control" value="<?= htmlspecialchars($setting['dia_chi']) ?>" placeholder="Nhập địa chỉ đầy đủ..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Link chat Zalo (hoặc SĐT Zalo)</label>
                                <input type="text" name="zalo_link" class="form-control" value="<?= htmlspecialchars($setting['zalo_link']) ?>" placeholder="Ví dụ: https://zalo.me/0354001205" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Link Messenger Facebook</label>
                                <input type="text" name="facebook_link" class="form-control" value="<?= htmlspecialchars($setting['facebook_link']) ?>" placeholder="Ví dụ: https://m.me/username" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Chữ trên thanh topbar 1 (Giao hàng)</label>
                                <input type="text" name="text_topbar_1" class="form-control" value="<?= htmlspecialchars($setting['text_topbar_1'] ?? 'Giao hàng toàn quốc') ?>" placeholder="Ví dụ: Giao hàng toàn quốc" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Chữ trên thanh topbar 2 (Bảo hành)</label>
                                <input type="text" name="text_topbar_2" class="form-control" value="<?= htmlspecialchars($setting['text_topbar_2'] ?? 'Chính hãng - Bảo hành rõ ràng') ?>" placeholder="Ví dụ: Chính hãng - Bảo hành rõ ràng" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cấu hình thanh toán và QR Code -->
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 text-success"><i class="bi bi-wallet2 me-2"></i>Thông tin thanh toán chuyển khoản</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-4">Bạn có thể cấu hình tài khoản ngân hàng để hệ thống tự động tạo mã QR VietQR (có nạp sẵn số tiền và mã đơn hàng khi khách thanh toán) hoặc tải lên ảnh QR tĩnh của riêng bạn (ví dụ Momo, ZaloPay, QR ngân hàng riêng).</p>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Mã ngân hàng (VietQR)</label>
                                <select name="bank_name" class="form-select">
                                    <option value="vietinbank" <?= $setting['bank_name'] === 'vietinbank' ? 'selected' : '' ?>>VietinBank</option>
                                    <option value="mbbank" <?= $setting['bank_name'] === 'mbbank' ? 'selected' : '' ?>>MBBank</option>
                                    <option value="vcb" <?= $setting['bank_name'] === 'vcb' ? 'selected' : '' ?>>Vietcombank</option>
                                    <option value="bidv" <?= $setting['bank_name'] === 'bidv' ? 'selected' : '' ?>>BIDV</option>
                                    <option value="tcb" <?= $setting['bank_name'] === 'tcb' ? 'selected' : '' ?>>Techcombank</option>
                                    <option value="acb" <?= $setting['bank_name'] === 'acb' ? 'selected' : '' ?>>ACB</option>
                                    <option value="sacombank" <?= $setting['bank_name'] === 'sacombank' ? 'selected' : '' ?>>Sacombank</option>
                                    <option value="tpbank" <?= $setting['bank_name'] === 'tpbank' ? 'selected' : '' ?>>TPBank</option>
                                    <option value="vpbank" <?= $setting['bank_name'] === 'vpbank' ? 'selected' : '' ?>>VPBank</option>
                                </select>
                                <small class="text-muted small">Cần khớp với mã của hệ thống VietQR.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Số tài khoản</label>
                                <input type="text" name="bank_account" class="form-control" value="<?= htmlspecialchars($setting['bank_account']) ?>" placeholder="Nhập số tài khoản..." required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tên chủ tài khoản (Không dấu)</label>
                                <input type="text" name="bank_owner" class="form-control" value="<?= htmlspecialchars($setting['bank_owner']) ?>" placeholder="Ví dụ: NGUYEN VAN A" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Hoặc Tải lên QR Code Tĩnh (Momo, QR ngân hàng riêng...)</label>
                                <input type="file" name="qr_code" class="form-control" accept="image/*">
                                <small class="text-muted d-block mt-1">Nếu tải lên ảnh này, trang thanh toán thành công sẽ hiển thị trực tiếp ảnh này thay vì tạo mã QR tự động từ thông tin ngân hàng ở trên.</small>
                            </div>
                            <div class="col-md-6 text-center border-start">
                                <div class="fw-bold mb-2 small text-muted">QR Code hiện tại</div>
                                <?php if (!empty($setting['qr_code_url'])): ?>
                                    <img src="<?= htmlspecialchars($setting['qr_code_url']) ?>" alt="QR Code Tĩnh" class="img-thumbnail mb-2" style="max-height: 180px;">
                                    <div class="form-check justify-content-center d-flex">
                                        <input class="form-check-input" type="checkbox" name="delete_qr_code" value="1" id="delete_qr">
                                        <label class="form-check-label ms-2 text-danger" for="delete_qr">Xóa QR Code tĩnh này (Quay lại sử dụng VietQR tự động)</label>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted py-4 bg-light rounded small">Đang sử dụng VietQR tự động (không có QR tĩnh tải lên)</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Logo -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 text-dark"><i class="bi bi-image me-2"></i>Logo Website</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4 bg-light p-3 rounded-3 d-flex align-items-center justify-content-center" style="min-height: 150px;">
                            <?php if (!empty($setting['logo_url'])): ?>
                                <img id="logoPreview" src="<?= htmlspecialchars($setting['logo_url']) ?>" alt="Logo Preview" style="max-width: 100%; max-height: 120px; object-fit: contain;">
                            <?php else: ?>
                                <div class="text-muted">Chưa có Logo</div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn tệp Logo mới</label>
                            <input type="file" name="logo" class="form-control" accept="image/*" id="logoInput">
                            <small class="text-muted d-block mt-1">Định dạng khuyên dùng: PNG nền trong suốt.</small>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 text-dark"><i class="bi bi-bookmark-star me-2"></i>Logo Tab Bar (Favicon)</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4 bg-light p-3 rounded-3 d-flex align-items-center justify-content-center" style="min-height: 100px;">
                            <?php if (!empty($setting['logo_tab_bar_url'])): ?>
                                <img id="logoTabBarPreview" src="<?= htmlspecialchars($setting['logo_tab_bar_url']) ?>" alt="Favicon Preview" style="max-width: 32px; max-height: 32px; object-fit: contain;">
                            <?php else: ?>
                                <div class="text-muted" id="logoTabBarPlaceholder">Chưa có Logo Tab Bar</div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn tệp Favicon mới</label>
                            <input type="file" name="logo_tab_bar" class="form-control" accept="image/*" id="logoTabBarInput">
                            <small class="text-muted d-block mt-1">Định dạng khuyên dùng: PNG/ICO vuông (32x32 hoặc 16x16).</small>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold"><i class="bi bi-save me-2"></i>Lưu Thay Đổi</button>
                        <a href="?page=admin-dashboard" class="btn btn-outline-secondary w-100 mt-2 py-2">Quay lại Dashboard</a>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    // Preview logo trước khi upload
    document.getElementById('logoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('logoPreview').src = event.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Preview logo tab bar trước khi upload
    document.getElementById('logoTabBarInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('logoTabBarPreview');
                if (preview) {
                    preview.src = event.target.result;
                } else {
                    const container = document.getElementById('logoTabBarPlaceholder').parentNode;
                    container.innerHTML = `<img id="logoTabBarPreview" src="${event.target.result}" alt="Favicon Preview" style="max-width: 32px; max-height: 32px; object-fit: contain;">`;
                }
            }
            reader.readAsDataURL(file);
        }
    });
</script>
