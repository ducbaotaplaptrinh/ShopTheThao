<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Khách hàng & Hạng thành viên</h2>
    <div class="text-muted" style="font-size: 13px;">Tổng: <strong><?= count($customers) ?></strong> khách hàng</div>
</div>

<!-- Gợi ý hạng thành viên -->
<div class="row g-3 mb-4">
    <?php foreach ($tiers as $tier): ?>
        <div class="col-6 col-md-3">
            <div class="admin-card mb-0 text-center py-3" style="border-top: 3px solid <?= htmlspecialchars($tier['mau_sac']) ?>;">
                <i class="bi <?= htmlspecialchars($tier['bieu_tuong']) ?> fs-3 mb-1 d-block" style="color: <?= htmlspecialchars($tier['mau_sac']) ?>"></i>
                <div class="fw-bold"><?= htmlspecialchars($tier['ten_hang']) ?></div>
                <div class="text-muted" style="font-size: 12px;">
                    từ <?= number_format($tier['muc_chi_tieu_toi_thieu'], 0, ',', '.') ?> đ
                </div>
                <div class="mt-1">
                    <span class="badge text-dark" style="background-color: <?= $tier['mau_sac'] ?>22; border: 1px solid <?= $tier['mau_sac'] ?>55; color: <?= $tier['mau_sac'] ?> !important;">
                        Giảm <?= $tier['phan_tram_giam_gia'] ?>%
                    </span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['success'] ?>
        <?php unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-x-circle-fill me-2"></i><?= $_SESSION['error'] ?>
        <?php unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="?page=admin-customer-gift-voucher" method="POST" id="giftVoucherForm">
    <div class="admin-card">
        <!-- Panel điều khiển và lọc -->
        <div class="row g-3 align-items-center mb-3 pb-3 border-bottom">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-2">
                    <label for="filterSpending" class="form-label mb-0 text-nowrap fw-semibold text-muted" style="font-size: 13px;">Lọc nhanh:</label>
                    <select id="filterSpending" class="form-select form-select-sm">
                        <option value="all">Tất cả khách hàng</option>
                        <option value="zero_orders">Chưa mua đơn nào (0 đơn)</option>
                        <option value="low_spending">Chi tiêu dưới 500.000 đ</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-7 text-md-end">
                <div class="d-flex align-items-center justify-content-md-end gap-2">
                    <label for="voucherSelect" class="form-label mb-0 text-nowrap fw-semibold text-muted" style="font-size: 13px;">Tặng Voucher:</label>
                    <select name="voucher_id" id="voucherSelect" class="form-select form-select-sm w-auto" style="max-width: 250px;">
                        <option value="">-- Chọn mã giảm giá --</option>
                        <?php foreach ($vouchers as $v): ?>
                            <option value="<?= $v['id'] ?>">
                                <?= htmlspecialchars($v['ma_code']) ?> (Giảm <?= $v['loai_giam_gia'] === 'phan_tram' ? (float)$v['gia_tri_giam'] . '%' : number_format($v['gia_tri_giam']) . 'đ' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold text-white px-3" id="btnSubmitGift">
                        <i class="bi bi-gift me-1"></i> Tặng ngay
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="customerTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAllCustomers" class="form-check-input">
                        </th>
                        <th>Khách hàng</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Hạng</th>
                        <th>Tổng chi tiêu</th>
                        <th>Đơn hàng</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                        <tr class="customer-row" data-orders="<?= $c['so_don_hang'] ?>" data-spending="<?= (float)($c['tong_chi_tieu'] ?? 0) ?>">
                            <td>
                                <input type="checkbox" name="customer_ids[]" value="<?= $c['id'] ?>" class="form-check-input customer-checkbox">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar rounded-circle d-flex align-items-center justify-content-center bg-warning text-white fw-bold" 
                                         style="width: 35px; height: 35px; font-size: 0.85rem; flex-shrink: 0; <?= !empty($c['anh_dai_dien']) ? "background-image: url('" . htmlspecialchars($c['anh_dai_dien']) . "'); background-size: cover; background-position: center;" : '' ?>">
                                        <?= empty($c['anh_dai_dien']) ? htmlspecialchars(mb_substr($c['ho_ten'], 0, 1)) : '' ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($c['ho_ten']) ?></div>
                                        <small class="text-muted">ID: #<?= $c['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($c['so_dien_thoai'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (!empty($c['ten_hang'])): ?>
                                    <span class="badge fw-bold px-2 py-1" style="background-color: <?= $c['mau_sac'] ?>22; border: 1px solid <?= $c['mau_sac'] ?>55; color: <?= $c['mau_sac'] ?> !important;">
                                        <i class="bi <?= htmlspecialchars($c['bieu_tuong'] ?? 'bi-star') ?> me-1"></i>
                                        <?= htmlspecialchars($c['ten_hang']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-dark">
                                <?= number_format($c['tong_chi_tieu'] ?? 0, 0, ',', '.') ?> đ
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= $c['so_don_hang'] ?> đơn</span>
                            </td>
                            <td>
                                <?php if ($c['trang_thai'] == 1): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Đã khóa</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light toggle-status-btn <?= $c['trang_thai'] == 1 ? 'text-danger' : 'text-success' ?>" data-id="<?= $c['id'] ?>" data-status="<?= $c['trang_thai'] == 1 ? 0 : 1 ?>">
                                    <?php if ($c['trang_thai'] == 1): ?>
                                        <i class="bi bi-lock"></i> Khóa
                                    <?php else: ?>
                                        <i class="bi bi-unlock"></i> Mở
                                    <?php endif; ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-3"></i>
                                Chưa có khách hàng nào
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</form>

<!-- Form ẩn phục vụ Khóa/Mở trạng thái khách hàng -->
<form action="?page=admin-customer-toggle" method="POST" id="toggleStatusForm" style="display: none;">
    <input type="hidden" name="id" id="toggleId">
    <input type="hidden" name="trang_thai" id="toggleStatus">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCustomers');
    const customerCheckboxes = document.querySelectorAll('.customer-checkbox');
    const filterSelect = document.getElementById('filterSpending');
    const giftForm = document.getElementById('giftVoucherForm');
    const toggleForm = document.getElementById('toggleStatusForm');
    const toggleIdInput = document.getElementById('toggleId');
    const toggleStatusInput = document.getElementById('toggleStatus');

    // 1. Thao tác Chọn tất cả checkboxes
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            // Lấy tất cả các dòng đang HIỂN THỊ (không có class d-none)
            const visibleRows = document.querySelectorAll('.customer-row:not(.d-none)');
            visibleRows.forEach(row => {
                const cb = row.querySelector('.customer-checkbox');
                if (cb) cb.checked = this.checked;
            });
        });
    }

    // 2. Click nút khóa/mở khách hàng riêng lẻ
    const toggleButtons = document.querySelectorAll('.toggle-status-btn');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const id = this.getAttribute('data-id');
            const status = this.getAttribute('data-status');
            
            if (toggleForm && toggleIdInput && toggleStatusInput) {
                toggleIdInput.value = id;
                toggleStatusInput.value = status;
                toggleForm.submit();
            }
        });
    });

    // 3. Lọc thông minh phía client-side
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const filterValue = this.value;
            const rows = document.querySelectorAll('.customer-row');
            
            // Bỏ chọn tất cả khi lọc để tránh chọn nhầm phần tử bị ẩn
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            customerCheckboxes.forEach(cb => cb.checked = false);

            rows.forEach(row => {
                const orders = parseInt(row.getAttribute('data-orders') || '0');
                const spending = parseFloat(row.getAttribute('data-spending') || '0');
                let match = true;

                if (filterValue === 'zero_orders') {
                    match = (orders === 0);
                } else if (filterValue === 'low_spending') {
                    match = (spending < 500000);
                }

                if (match) {
                    row.classList.remove('d-none');
                } else {
                    row.classList.add('d-none');
                }
            });
        });
    }

    // 4. Validate form tặng voucher
    if (giftForm) {
        giftForm.addEventListener('submit', function(e) {
            // Kiểm tra xem có khách hàng nào được chọn hay không
            const checkedCount = document.querySelectorAll('.customer-checkbox:checked').length;
            if (checkedCount === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một khách hàng để tặng voucher!');
                return;
            }

            const voucherSelect = document.getElementById('voucherSelect');
            if (voucherSelect && voucherSelect.value === '') {
                e.preventDefault();
                alert('Vui lòng chọn mã giảm giá cần tặng!');
                return;
            }

            if (!confirm(`Bạn có chắc chắn muốn tặng voucher đã chọn cho ${checkedCount} khách hàng?`)) {
                e.preventDefault();
            }
        });
    }
});
</script>