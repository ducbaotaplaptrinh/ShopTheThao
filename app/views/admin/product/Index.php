<?php
// Helper to build page URLs with filters
if (!function_exists('buildPageUrl')) {
    function buildPageUrl($pageNo, $filters)
    {
        $params = array_merge($filters, ['page_no' => $pageNo, 'page' => 'admin-products']);
        // Filter out empty params
        $params = array_filter($params, function ($value) {
            return $value !== '';
        });
        return '?' . http_build_query($params);
    }
}
?>

<!-- Alerts for Action Statuses -->
<?php if (!empty($successMsg)): ?>
    <?php if ($successMsg === 'created'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Thêm sản phẩm thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($successMsg === 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Cập nhật sản phẩm thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($successMsg === 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Xóa sản phẩm thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($successMsg === 'restored'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Khôi phục sản phẩm thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($successMsg === 'batch_discount_applied'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Áp dụng giảm giá hàng loạt thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (!empty($errorMsg)): ?>
    <?php
    $displayError = htmlspecialchars($errorMsg);
    if ($errorMsg === 'no_products_selected') {
        $displayError = 'Vui lòng chọn ít nhất một sản phẩm để áp dụng giảm giá!';
    } elseif ($errorMsg === 'invalid_discount_percentage') {
        $displayError = 'Phần trăm giảm giá không hợp lệ (phải từ 0% đến 100%)!';
    } elseif ($errorMsg === 'invalid_discount_value') {
        $displayError = 'Số tiền giảm giá không hợp lệ (phải lớn hơn hoặc bằng 0đ)!';
    }
    ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $displayError ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Quản lý Sản phẩm</h2>
    <a href="?page=admin-product-create" class="btn btn-primary px-4">
        <i class="bi bi-plus-lg me-2"></i>Thêm Sản phẩm
    </a>
</div>

<!-- Filter Bar -->
<form method="GET" action="" class="mb-4">
    <input type="hidden" name="page" value="admin-products">
    <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-bold text-secondary small">Tìm kiếm</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="keyword" class="form-control bg-light border-start-0" placeholder="Tên, ID, SKU..." value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-bold text-secondary small">Danh mục</label>
                <select name="ma_danh_muc" class="form-select bg-light">
                    <option value="">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($filters['ma_danh_muc']) && $filters['ma_danh_muc'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['ten_danh_muc']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-bold text-secondary small">Thương hiệu</label>
                <select name="ma_thuong_hieu" class="form-select bg-light">
                    <option value="">-- Tất cả thương hiệu --</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['id'] ?>" <?= (isset($filters['ma_thuong_hieu']) && $filters['ma_thuong_hieu'] == $brand['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand['ten_thuong_hieu']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-bold text-secondary small">Tình trạng kho</label>
                <select name="kho" class="form-select bg-light">
                    <option value="">-- Tất cả kho --</option>
                    <option value="con_hang" <?= (isset($filters['kho']) && $filters['kho'] === 'con_hang') ? 'selected' : '' ?>>Còn hàng (> 5)</option>
                    <option value="sap_het_hang" <?= (isset($filters['kho']) && $filters['kho'] === 'sap_het_hang') ? 'selected' : '' ?>>Sắp hết hàng (0 &lt; x &le; 5)</option>
                    <option value="het_hang" <?= (isset($filters['kho']) && $filters['kho'] === 'het_hang') ? 'selected' : '' ?>>Hết hàng (= 0)</option>
                </select>
            </div>

            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-bold text-secondary small">Trạng thái hiển thị</label>
                <select name="trang_thai" class="form-select bg-light">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="1" <?= (isset($filters['trang_thai']) && $filters['trang_thai'] === '1') ? 'selected' : '' ?>>Đang bán (Hiển thị)</option>
                    <option value="0" <?= (isset($filters['trang_thai']) && $filters['trang_thai'] === '0') ? 'selected' : '' ?>>Đang ẩn (Tạm khóa)</option>
                </select>
            </div>

            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-bold text-secondary small">Thùng rác</label>
                <select name="da_xoa" class="form-select bg-light">
                    <option value="0" <?= (isset($filters['da_xoa']) && $filters['da_xoa'] !== '1') ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="1" <?= (isset($filters['da_xoa']) && $filters['da_xoa'] === '1') ? 'selected' : '' ?>>Đã xóa tạm (Thùng rác)</option>
                </select>
            </div>

            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-bold text-secondary small">Khuyến mãi</label>
                <select name="khuyen_mai" class="form-select bg-light">
                    <option value="">-- Tất cả sản phẩm --</option>
                    <option value="1" <?= (isset($filters['khuyen_mai']) && $filters['khuyen_mai'] === '1') ? 'selected' : '' ?>>Đang khuyến mãi</option>
                </select>
            </div>

            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-bold text-secondary small">Doanh số</label>
                <select name="doanh_so" class="form-select bg-light">
                    <option value="">-- Bộ lọc doanh số --</option>
                    <option value="ban_chay" <?= (isset($filters['doanh_so']) && $filters['doanh_so'] === 'ban_chay') ? 'selected' : '' ?>>Sản phẩm bán chạy</option>
                    <option value="ban_cham" <?= (isset($filters['doanh_so']) && $filters['doanh_so'] === 'ban_cham') ? 'selected' : '' ?>>Sản phẩm bán chậm</option>
                </select>
            </div>

            <div class="col-md-3 col-sm-12 d-flex align-items-end gap-2 ms-auto">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i> Lọc dữ liệu
                </button>
                <a href="?page=admin-products" class="btn btn-outline-secondary flex-fill">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
                </a>
            </div>
        </div>
    </div>
</form>

<?php
$isFilterActive = !empty($filters['keyword']) || !empty($filters['ma_danh_muc']) || !empty($filters['ma_thuong_hieu']) || !empty($filters['kho']) || !empty($filters['trang_thai']) || !empty($filters['khuyen_mai']) || !empty($filters['doanh_so']);
?>

<!-- Batch Actions Bar -->
<div id="batchActionsBar" class="admin-card mb-3 p-3 bg-light rounded border <?= ($isFilterActive) ? '' : 'd-none' ?> shadow-sm" style="border-left: 4px solid #0d6efd !important;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <span class="fw-bold text-dark">
                <i class="bi bi-check2-square text-primary me-2"></i>Đang chọn: 
                <span id="selectedCount" class="badge bg-primary">0</span> sản phẩm
            </span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="small fw-bold text-secondary me-1">Thiết lập giảm giá hàng loạt:</span>
            
            <select id="batchDiscountType" class="form-select form-select-sm" style="width: 145px; display: inline-block;">
                <option value="phan_tram">Giảm theo %</option>
                <option value="tien_mat">Giảm theo số tiền (đ)</option>
            </select>
            
            <input type="number" id="batchDiscountValue" class="form-control form-control-sm" style="width: 130px; display: inline-block;" placeholder="Vd: 10 hoặc 50000" min="0">
            
            <button type="button" id="btnApplySelected" class="btn btn-sm btn-danger text-white fw-bold px-3" onclick="applyBatchDiscount(0)" disabled>
                <i class="bi bi-check-circle me-1"></i> Áp dụng cho mục đã chọn
            </button>

            <button type="button" class="btn btn-sm btn-warning text-white fw-bold px-3" onclick="applyBatchDiscount(1)">
                <i class="bi bi-funnel me-1"></i> Áp dụng cho TOÀN BỘ kết quả lọc
            </button>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px; text-align: center;">
                        <input class="form-check-input" type="checkbox" id="selectAllProducts">
                    </th>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên Sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Thương hiệu</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td style="text-align: center;">
                            <?php if (empty($p['ngay_xoa'])): ?>
                                <input class="form-check-input product-checkbox" type="checkbox" value="<?= $p['id'] ?>">
                            <?php endif; ?>
                        </td>
                        <td class="text-muted fw-bold">
                            <?php if (!empty($p['so_bien_the']) && $p['so_bien_the'] > 0): ?>
                                <span class="toggle-variants-btn me-1" data-product-id="<?= $p['id'] ?>" style="cursor: pointer;" title="Xem các biến thể">
                                    <i class="bi bi-plus-square text-primary toggle-icon-<?= $p['id'] ?>"></i>
                                </span>
                            <?php endif; ?>
                            #<?= $p['id'] ?>
                        </td>
                        <td>
                            <?php if (!empty($p['anh_dai_dien'])): ?>
                                <img src="<?= getProductImage('assets/images/products/' . $p['anh_dai_dien']) ?>" style="width: 45px; height: 45px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <div style="width: 45px; height: 45px; border-radius: 8px; background: #f4f6fa; display:flex; align-items:center; justify-content:center;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($p['ten_san_pham']) ?></div>
                            <?php if (!empty($p['gia_khuyen_mai']) && $p['gia_khuyen_mai'] < $p['gia_ban']): ?>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="text-danger fw-bold small"><?= number_format($p['gia_khuyen_mai'], 0, ',', '.') ?> đ</span>
                                    <span class="text-muted text-decoration-line-through small" style="font-size: 0.75rem;"><?= number_format($p['gia_ban'], 0, ',', '.') ?> đ</span>
                                    <span class="badge bg-danger" style="font-size: 0.65rem;">Khuyến mãi</span>
                                </div>
                            <?php else: ?>
                                <small class="text-muted"><?= number_format($p['gia_ban'], 0, ',', '.') ?> đ</small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['ten_danh_muc'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($p['ten_thuong_hieu'] ?? 'N/A') ?></td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold <?= ($p['tong_ton_kho'] ?? 0) <= 5 ? 'text-danger' : 'text-success' ?>">
                                    <?= $p['tong_ton_kho'] ?? 0 ?> sản phẩm
                                </span>
                                <?php if (($p['so_bien_the_het_hang'] ?? 0) > 0): ?>
                                    <span class="badge bg-warning text-dark mt-1 align-self-start" style="font-size: 0.7rem;">
                                        <?= $p['so_bien_the_het_hang'] ?> SKU sắp hết
                                    </span>
                                <?php endif; ?>
                                <span class="text-muted small mt-1">
                                    <i class="bi bi-cart-check me-1"></i>Đã bán: <strong><?= $p['da_ban'] ?? 0 ?></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($p['ngay_xoa'])): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Đã xóa tạm</span>
                            <?php elseif ($p['trang_thai'] == 1): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Đang bán</span>
                            <?php else: ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Đang ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if (empty($p['ngay_xoa'])): ?>
                                <a href="?page=admin-product-edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-light text-primary me-1">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                <a href="?page=admin-product-delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                    <i class="bi bi-trash"></i> Xóa
                                </a>
                            <?php else: ?>
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <span class="text-muted small" style="font-size: 0.75rem;">
                                        <i class="bi bi-calendar2-x me-1"></i><?= date('d/m/Y H:i', strtotime($p['ngay_xoa'])) ?>
                                    </span>
                                    <a href="?page=admin-product-restore&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-success" onclick="return confirm('Bạn có chắc chắn muốn khôi phục sản phẩm này cùng các biến thể?')">
                                        <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                    </a>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if (!empty($p['variants'])): ?>
                        <?php foreach ($p['variants'] as $v):
                            $vAttrText = [];
                            foreach ($v['attributes'] as $attr) {
                                $vAttrText[] = htmlspecialchars($attr['ten_thuoc_tinh'] . ': ' . $attr['gia_tri']);
                            }
                            $attrSuffix = !empty($vAttrText) ? ' (' . implode(', ', $vAttrText) . ')' : '';
                        ?>
                            <tr class="variant-row parent-<?= $p['id'] ?> bg-light bg-opacity-50" style="display: none; border-left: 4px solid var(--bs-primary);">
                                <td></td>
                                <td class="text-muted small ps-3">#<?= $v['id'] ?></td>
                                <td>
                                    <?php if (!empty($v['anh_rieng'])): ?>
                                        <img src="<?= getProductImage('assets/images/products/' . $v['anh_rieng']) ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
                                    <?php else: ?>
                                        <?php if (!empty($p['anh_dai_dien'])): ?>
                                            <img src="<?= getProductImage('assets/images/products/' . $p['anh_dai_dien']) ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
                                        <?php else: ?>
                                            <div style="width: 40px; height: 40px; border-radius: 6px; background: #f4f6fa; display:flex; align-items:center; justify-content:center;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold text-secondary small">
                                        <?= htmlspecialchars($p['ten_san_pham']) . $attrSuffix ?>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        SKU: <?= htmlspecialchars($v['ma_vach_sku'] ?? 'N/A') ?>
                                    </div>
                                    <div class="text-dark fw-bold small mt-1">
                                        <?php if (!empty($v['gia_ban_rieng']) && (float)$v['gia_ban_rieng'] > 0): ?>
                                            <?= number_format((float)$v['gia_ban_rieng'], 0, ',', '.') ?> đ
                                        <?php else: ?>
                                            <?= number_format((float)$p['gia_ban'], 0, ',', '.') ?> đ <small class="text-muted fw-normal">(Giá gốc)</small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><span class="text-muted small"><?= htmlspecialchars($p['ten_danh_muc'] ?? 'N/A') ?></span></td>
                                <td><span class="text-muted small"><?= htmlspecialchars($p['ten_thuong_hieu'] ?? 'N/A') ?></span></td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="fw-semibold <?= $v['so_luong_ton'] <= 5 ? 'text-danger' : 'text-success' ?>">
                                            <?= $v['so_luong_ton'] ?> sản phẩm
                                        </span>
                                        <span class="text-muted mt-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-cart-check me-1"></i>Đã bán: <strong><?= $v['da_ban'] ?></strong>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($v['trang_thai'] == 1): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.7rem;">Đang bán</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25" style="font-size: 0.7rem;">Đang ẩn</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-muted small italic" style="font-size: 0.75rem;">
                                    Biến thể con
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                            Không tìm thấy sản phẩm nào khớp với bộ lọc
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <?php if ($totalPages > 1): ?>
        <hr class="my-0 border-light">
        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-2">
            <div class="text-muted small">
                Hiển thị trang <strong><?= $currentPage ?></strong> / <strong><?= $totalPages ?></strong> (Tổng số <strong><?= $totalProducts ?></strong> sản phẩm)
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Previous Button -->
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage <= 1 ? '#' : buildPageUrl($currentPage - 1, $filters) ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    <!-- Page Numbers -->
                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);

                    if ($startPage > 1): ?>
                        <li class="page-item"><a class="page-link" href="<?= buildPageUrl(1, $filters) ?>">1</a></li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>">
                            <a class="page-link" href="<?= buildPageUrl($i, $filters) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item"><a class="page-link" href="<?= buildPageUrl($totalPages, $filters) ?>"><?= $totalPages ?></a></li>
                    <?php endif; ?>

                    <!-- Next Button -->
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage >= $totalPages ? '#' : buildPageUrl($currentPage + 1, $filters) ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Form ẩn để gửi yêu cầu giảm giá hàng loạt -->
<form id="batchDiscountForm" action="?page=admin-product-batch-discount" method="POST" style="display: none;">
    <input type="hidden" name="discount_type" id="batchFormType">
    <input type="hidden" name="discount_value" id="batchFormValue">
    <input type="hidden" name="apply_to_all_filtered" id="batchFormApplyAll" value="0">
    <!-- Filter fields to recreate search on server -->
    <input type="hidden" name="keyword" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
    <input type="hidden" name="ma_danh_muc" value="<?= htmlspecialchars($filters['ma_danh_muc'] ?? '') ?>">
    <input type="hidden" name="ma_thuong_hieu" value="<?= htmlspecialchars($filters['ma_thuong_hieu'] ?? '') ?>">
    <input type="hidden" name="kho" value="<?= htmlspecialchars($filters['kho'] ?? '') ?>">
    <input type="hidden" name="trang_thai" value="<?= htmlspecialchars($filters['trang_thai'] ?? '') ?>">
    <input type="hidden" name="da_xoa" value="<?= htmlspecialchars($filters['da_xoa'] ?? '') ?>">
    <input type="hidden" name="khuyen_mai" value="<?= htmlspecialchars($filters['khuyen_mai'] ?? '') ?>">
    <input type="hidden" name="doanh_so" value="<?= htmlspecialchars($filters['doanh_so'] ?? '') ?>">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-variants-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var productId = this.getAttribute('data-product-id');
                var variantRows = document.querySelectorAll('.parent-' + productId);
                var icon = this.querySelector('i');

                variantRows.forEach(function(row) {
                    if (row.style.display === 'none') {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (icon.classList.contains('bi-plus-square')) {
                    icon.classList.remove('bi-plus-square');
                    icon.classList.add('bi-dash-square');
                } else {
                    icon.classList.remove('bi-dash-square');
                    icon.classList.add('bi-plus-square');
                }
            });
        });

        // Batch checkboxes logic
        const selectAllCheckbox = document.getElementById("selectAllProducts");
        const productCheckboxes = document.querySelectorAll(".product-checkbox");
        const batchActionsBar = document.getElementById("batchActionsBar");
        const selectedCountSpan = document.getElementById("selectedCount");
        const isFilterActive = <?= $isFilterActive ? 'true' : 'false' ?>;

        function updateBatchActionBar() {
            const checkedCount = document.querySelectorAll(".product-checkbox:checked").length;
            const btnSelected = document.getElementById("btnApplySelected");
            
            if (checkedCount > 0) {
                batchActionsBar.classList.remove("d-none");
                selectedCountSpan.textContent = checkedCount;
                if (btnSelected) btnSelected.removeAttribute("disabled");
            } else {
                if (!isFilterActive) {
                    batchActionsBar.classList.add("d-none");
                }
                selectedCountSpan.textContent = "0";
                if (btnSelected) btnSelected.setAttribute("disabled", "true");
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener("change", function () {
                const isChecked = selectAllCheckbox.checked;
                productCheckboxes.forEach(cb => {
                    cb.checked = isChecked;
                });
                updateBatchActionBar();
            });
        }

        productCheckboxes.forEach(cb => {
            cb.addEventListener("change", function () {
                const allChecked = Array.from(productCheckboxes).every(c => c.checked);
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                }
                updateBatchActionBar();
            });
        });

        // Initialize state on load
        updateBatchActionBar();
    });

    function applyBatchDiscount(applyAll) {
        const typeSelect = document.getElementById("batchDiscountType");
        const valInput = document.getElementById("batchDiscountValue");
        
        const type = typeSelect ? typeSelect.value : "phan_tram";
        const valText = valInput ? valInput.value.trim() : "";
        
        if (valText === "") {
            alert("Vui lòng nhập giá trị giảm giá!");
            return;
        }

        const value = parseFloat(valText);
        if (isNaN(value) || value < 0) {
            alert("Giá trị giảm giá không hợp lệ! Vui lòng nhập số lớn hơn hoặc bằng 0.");
            return;
        }

        if (type === "phan_tram" && value > 100) {
            alert("Phần trăm giảm giá không được vượt quá 100%!");
            return;
        }

        let selectedCount = 0;
        let confirmMsg = "";

        if (applyAll === 1) {
            confirmMsg = value > 0 
                ? `CẢNH BÁO: Bạn có chắc chắn muốn áp dụng giảm giá (${type === 'phan_tram' ? value + '%' : value.toLocaleString('vi-VN') + 'đ'}) cho TOÀN BỘ sản phẩm khớp với bộ lọc hiện tại không?`
                : `CẢNH BÁO: Bạn có chắc chắn muốn XÓA giảm giá (đưa về giá gốc) cho TOÀN BỘ sản phẩm khớp với bộ lọc hiện tại không?`;
        } else {
            const selectedCheckboxes = document.querySelectorAll(".product-checkbox:checked");
            selectedCount = selectedCheckboxes.length;
            if (selectedCount === 0) {
                alert("Vui lòng chọn ít nhất một sản phẩm!");
                return;
            }
            confirmMsg = value > 0 
                ? `Bạn có chắc chắn muốn áp dụng giảm giá (${type === 'phan_tram' ? value + '%' : value.toLocaleString('vi-VN') + 'đ'}) cho ${selectedCount} sản phẩm đã chọn không?`
                : `Bạn có chắc chắn muốn XÓA giảm giá (đưa về giá gốc) cho ${selectedCount} sản phẩm đã chọn không?`;
        }

        if (!confirm(confirmMsg)) {
            return;
        }

        const form = document.getElementById("batchDiscountForm");
        document.getElementById("batchFormType").value = type;
        document.getElementById("batchFormValue").value = value;
        document.getElementById("batchFormApplyAll").value = applyAll;

        // Clear previous product ID inputs
        form.querySelectorAll("input[name='product_ids[]']").forEach(el => el.remove());

        if (applyAll === 0) {
            // Add selected IDs
            const selectedCheckboxes = document.querySelectorAll(".product-checkbox:checked");
            selectedCheckboxes.forEach(cb => {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "product_ids[]";
                input.value = cb.value;
                form.appendChild(input);
            });
        }

        form.submit();
    }
</script>