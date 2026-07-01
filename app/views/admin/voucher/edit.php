<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Chỉnh Sửa Voucher</h2>
        <a href="?page=admin-vouchers" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i><?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow border-0 rounded-3">
        <div class="card-body p-4">
            <form action="?page=admin-voucher-update" method="POST" id="voucherForm">
                <!-- Hidden ID Field -->
                <input type="hidden" name="id" value="<?= $voucher['id'] ?>">

                <div class="row g-4">
                    <!-- Column Left: Basic Info -->
                    <div class="col-md-8">
                        <div class="admin-card mb-4 bg-light border-0">
                            <h5 class="fw-bold mb-3 text-dark">Thông tin cơ bản</h5>
                            
                            <div class="mb-3">
                                <label for="ma_code" class="form-label fw-bold">Mã Voucher <span class="text-danger">*</span></label>
                                <input type="text" name="ma_code" id="ma_code" class="form-control font-monospace fw-bold text-uppercase" 
                                       required placeholder="Ví dụ: SUMMER50, WELCOME2026" style="letter-spacing: 1px;"
                                       value="<?= htmlspecialchars($voucher['ma_code']) ?>">
                                <div class="form-text">Mã voucher sẽ tự động viết hoa khi nhập. Chỉ nên chứa chữ cái và số, không khoảng trắng.</div>
                            </div>

                            <div class="mb-3">
                                <label for="tieu_de" class="form-label fw-bold">Tiêu đề Voucher <span class="text-danger">*</span></label>
                                <input type="text" name="tieu_de" id="tieu_de" class="form-control" 
                                       required placeholder="Ví dụ: Giảm giá mùa hè cho đơn hàng trên 500k"
                                       value="<?= htmlspecialchars($voucher['tieu_de']) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="mo_ta" class="form-label fw-bold">Mô tả chi tiết</label>
                                <textarea name="mo_ta" id="mo_ta" class="form-control" rows="4" 
                                          placeholder="Nhập mô tả về điều kiện áp dụng, chi tiết chương trình ưu đãi..."><?= htmlspecialchars($voucher['mo_ta'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="admin-card bg-light border-0">
                            <h5 class="fw-bold mb-3 text-dark">Điều kiện & Giá trị giảm</h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="loai_giam_gia" class="form-label fw-bold">Loại giảm giá <span class="text-danger">*</span></label>
                                    <select name="loai_giam_gia" id="loai_giam_gia" class="form-select">
                                        <option value="tien_co_dinh" <?= $voucher['loai_giam_gia'] === 'tien_co_dinh' ? 'selected' : '' ?>>Giảm tiền cố định (đ)</option>
                                        <option value="phan_tram" <?= $voucher['loai_giam_gia'] === 'phan_tram' ? 'selected' : '' ?>>Giảm theo phần trăm (%)</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="gia_tri_giam" class="form-label fw-bold">Giá trị giảm <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="gia_tri_giam" id="gia_tri_giam" class="form-control" 
                                               required min="1" step="any" placeholder="Nhập giá trị..."
                                               value="<?= (float)$voucher['gia_tri_giam'] ?>">
                                        <span class="input-group-text" id="gia_tri_don_vi">đ</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="don_hang_toi_thieu" class="form-label fw-bold">Đơn hàng tối thiểu (đ)</label>
                                    <div class="input-group">
                                        <input type="number" name="don_hang_toi_thieu" id="don_hang_toi_thieu" class="form-control" 
                                               min="0" step="any" value="<?= (float)$voucher['don_hang_toi_thieu'] ?>">
                                        <span class="input-group-text">đ</span>
                                    </div>
                                    <div class="form-text">Đơn hàng phải đạt giá trị này để được áp dụng mã.</div>
                                </div>

                                <div class="col-md-6" id="wrapper_muc_giam_toi_da" style="display: none;">
                                    <label for="muc_giam_toi_da" class="form-label fw-bold">Mức giảm tối đa (đ)</label>
                                    <div class="input-group">
                                        <input type="number" name="muc_giam_toi_da" id="muc_giam_toi_da" class="form-control" 
                                               min="0" step="any" value="<?= $voucher['muc_giam_toi_da'] !== null ? (float)$voucher['muc_giam_toi_da'] : '' ?>">
                                        <span class="input-group-text">đ</span>
                                    </div>
                                    <div class="form-text">Giới hạn số tiền tối đa được giảm (bỏ trống nếu không giới hạn).</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column Right: Settings, Dates -->
                    <div class="col-md-4">
                        <div class="admin-card mb-4 bg-light border-0">
                            <h5 class="fw-bold mb-3 text-dark">Thống kê sử dụng</h5>
                            <div class="p-3 bg-white rounded border border-light shadow-sm">
                                <div class="row text-center">
                                    <div class="col-6 border-end">
                                        <div class="text-muted" style="font-size: 11px; text-transform: uppercase;">Đã sử dụng</div>
                                        <div class="fs-4 fw-bold text-success"><?= $voucher['so_luong_da_dung'] ?? 0 ?></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted" style="font-size: 11px; text-transform: uppercase;">Tỉ lệ dùng</div>
                                        <?php 
                                            $total = (int)$voucher['tong_so_luong'];
                                            $used = (int)$voucher['so_luong_da_dung'];
                                            $percent = $total > 0 ? round(($used / $total) * 100, 1) : 0;
                                        ?>
                                        <div class="fs-4 fw-bold text-primary"><?= $percent ?>%</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-card mb-4 bg-light border-0">
                            <h5 class="fw-bold mb-3 text-dark">Thiết lập giới hạn</h5>

                            <div class="mb-3">
                                <label for="ma_hang" class="form-label fw-bold">Hạng thành viên áp dụng</label>
                                <select name="ma_hang" id="ma_hang" class="form-select">
                                    <option value="0" <?= $voucher['ma_hang'] == 0 ? 'selected' : '' ?>>Tất cả mọi hạng thành viên</option>
                                    <?php foreach ($tiers as $tier): ?>
                                        <option value="<?= $tier['id'] ?>" <?= $voucher['ma_hang'] == $tier['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tier['ten_hang']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Khách hàng đạt hạng này trở lên mới được phép sử dụng mã.</div>
                            </div>

                            <div class="mb-3">
                                <label for="tong_so_luong" class="form-label fw-bold">Tổng số lượng phát hành <span class="text-danger">*</span></label>
                                <input type="number" name="tong_so_luong" id="tong_so_luong" class="form-control" 
                                       required min="1" value="<?= (int)$voucher['tong_so_luong'] ?>">
                                <div class="form-text">Số lượt tối đa voucher này được sử dụng.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Trạng thái hoạt động</label>
                                <div class="form-check form-switch pt-1">
                                    <input class="form-check-input" type="checkbox" name="trang_thai" id="trang_thai" 
                                           <?= $voucher['trang_thai'] == 1 ? 'checked' : '' ?> value="1">
                                    <label class="form-check-label fw-semibold <?= $voucher['trang_thai'] == 1 ? 'text-success' : 'text-secondary' ?>" 
                                           for="trang_thai" id="trang_thai_label">
                                        <?= $voucher['trang_thai'] == 1 ? 'Đang kích hoạt' : 'Đang ẩn/Tắt' ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="admin-card mb-4 bg-light border-0">
                            <h5 class="fw-bold mb-3 text-dark">Thời gian áp dụng</h5>

                            <div class="mb-3">
                                <label for="ngay_bat_dau" class="form-label fw-bold">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="ngay_bat_dau" id="ngay_bat_dau" class="form-control" required
                                       value="<?= date('Y-m-d\TH:i', strtotime($voucher['ngay_bat_dau'])) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="ngay_ket_thuc" class="form-label fw-bold">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="ngay_ket_thuc" id="ngay_ket_thuc" class="form-control" required
                                       value="<?= date('Y-m-d\TH:i', strtotime($voucher['ngay_ket_thuc'])) ?>">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm">
                                <i class="bi bi-save2 me-2"></i>Cập nhật Voucher
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tự động chuyển mã code thành chữ viết hoa không dấu/không khoảng trắng
    const maCodeInput = document.getElementById('ma_code');
    maCodeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9_\-]/g, '');
    });

    // Xử lý ẩn/hiện mức giảm tối đa dựa trên loại giảm giá
    const loaiGiamGiaSelect = document.getElementById('loai_giam_gia');
    const giaTriDonViSpan = document.getElementById('gia_tri_don_vi');
    const wrapperMucGiamToiDa = document.getElementById('wrapper_muc_giam_toi_da');
    const mucGiamToiDaInput = document.getElementById('muc_giam_toi_da');
    const giaTriGiamInput = document.getElementById('gia_tri_giam');

    function toggleDiscountType() {
        if (loaiGiamGiaSelect.value === 'phan_tram') {
            giaTriDonViSpan.innerText = '%';
            wrapperMucGiamToiDa.style.display = 'block';
            mucGiamToiDaInput.required = false; // Mức giảm tối đa có thể trống
            giaTriGiamInput.max = "100";
        } else {
            giaTriDonViSpan.innerText = 'đ';
            wrapperMucGiamToiDa.style.display = 'none';
            mucGiamToiDaInput.value = '';
            giaTriGiamInput.removeAttribute('max');
        }
    }

    loaiGiamGiaSelect.addEventListener('change', toggleDiscountType);
    toggleDiscountType(); // Chạy khi load trang

    // Thay đổi nhãn trạng thái động
    const trangThaiCheckbox = document.getElementById('trang_thai');
    const trangThaiLabel = document.getElementById('trang_thai_label');
    trangThaiCheckbox.addEventListener('change', function() {
        if (this.checked) {
            trangThaiLabel.innerText = "Đang kích hoạt";
            trangThaiLabel.classList.remove('text-secondary');
            trangThaiLabel.classList.add('text-success');
        } else {
            trangThaiLabel.innerText = "Đang ẩn/Tắt";
            trangThaiLabel.classList.remove('text-success');
            trangThaiLabel.classList.add('text-secondary');
        }
    });
});
</script>
