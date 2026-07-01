<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Quản lý Voucher (Mã giảm giá)</h2>
        <a href="?page=admin-voucher-create" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Thêm voucher mới
        </a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'created'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Thêm voucher mới thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['msg'] === 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Cập nhật voucher thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Voucher đã được xóa thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i><?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%">ID</th>
                            <th scope="col" style="width: 15%">Mã Code</th>
                            <th scope="col" style="width: 25%">Thông tin Voucher</th>
                            <th scope="col" style="width: 15%">Trị giá giảm</th>
                            <th scope="col" style="width: 10%">Đã dùng</th>
                            <th scope="col" style="width: 15%">Thời hạn</th>
                            <th scope="col" style="width: 10%" class="text-center">Trạng thái</th>
                            <th scope="col" style="width: 10%" class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($vouchers)): ?>
                            <?php foreach ($vouchers as $voucher): 
                                $now = time();
                                $startDate = strtotime($voucher['ngay_bat_dau']);
                                $endDate = strtotime($voucher['ngay_ket_thuc']);
                                $isExpired = $now > $endDate;
                                $isNotStarted = $now < $startDate;
                            ?>
                                <tr>
                                    <td><?= $voucher['id'] ?></td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 fw-bold font-monospace" style="font-size: 0.9rem;">
                                            <?= htmlspecialchars($voucher['ma_code']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($voucher['tieu_de']) ?></div>
                                        <?php if (!empty($voucher['mo_ta'])): ?>
                                            <small class="text-muted d-block text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($voucher['mo_ta']) ?>">
                                                <?= htmlspecialchars($voucher['mo_ta']) ?>
                                            </small>
                                        <?php endif; ?>
                                        <div class="mt-1">
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-person me-1"></i>Hạng: 
                                                <?= $voucher['ma_hang'] == 0 ? 'Tất cả' : htmlspecialchars($voucher['ten_hang'] ?? 'Không xác định') ?>
                                            </span>
                                            <?php if ($voucher['don_hang_toi_thieu'] > 0): ?>
                                                <span class="badge bg-light text-dark border ms-1">
                                                    Đơn tối thiểu: <?= number_format($voucher['don_hang_toi_thieu'], 0, ',', '.') ?>đ
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($voucher['loai_giam_gia'] === 'phan_tram'): ?>
                                            <span class="fw-bold text-danger"><?= number_format($voucher['gia_tri_giam'], 0) ?>%</span>
                                            <?php if (!empty($voucher['muc_giam_toi_da'])): ?>
                                                <div class="text-muted" style="font-size: 11px;">
                                                    Tối đa: <?= number_format($voucher['muc_giam_toi_da'], 0, ',', '.') ?>đ
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="fw-bold text-success"><?= number_format($voucher['gia_tri_giam'], 0, ',', '.') ?>đ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="fw-bold"><?= $voucher['so_luong_da_dung'] ?? 0 ?></span>
                                            <span class="text-muted">/</span>
                                            <span class="text-muted"><?= $voucher['tong_so_luong'] ?? '∞' ?></span>
                                        </div>
                                        <div class="progress mt-1" style="height: 5px; max-width: 100px;">
                                            <?php 
                                                $total = (int)$voucher['tong_so_luong'];
                                                $used = (int)$voucher['so_luong_da_dung'];
                                                $percent = $total > 0 ? min(100, ($used / $total) * 100) : 0;
                                            ?>
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%" aria-valuenow="<?= $used ?>" aria-valuemin="0" aria-valuemax="<?= $total ?>"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px;">
                                            <div><span class="text-muted">Bắt đầu:</span> <?= date('d/m/Y H:i', $startDate) ?></div>
                                            <div><span class="text-muted">Kết thúc:</span> <?= date('d/m/Y H:i', $endDate) ?></div>
                                        </div>
                                        <div class="mt-1">
                                            <?php if ($isExpired): ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" style="font-size: 10px;">Hết hạn</span>
                                            <?php elseif ($isNotStarted): ?>
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size: 10px;">Chưa bắt đầu</span>
                                            <?php else: ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 10px;">Có hiệu lực</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($voucher['trang_thai'] == 1): ?>
                                            <span class="badge bg-success px-2 py-1">Đang kích hoạt</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary px-2 py-1">Đang tắt</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="?page=admin-voucher-toggle&id=<?= $voucher['id'] ?>" class="btn btn-sm <?= $voucher['trang_thai'] == 1 ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $voucher['trang_thai'] == 1 ? 'Tắt voucher' : 'Kích hoạt voucher' ?>">
                                                <i class="bi <?= $voucher['trang_thai'] == 1 ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>
                                            </a>
                                            <a href="?page=admin-voucher-edit&id=<?= $voucher['id'] ?>" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?page=admin-voucher-delete&id=<?= $voucher['id'] ?>" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn voucher này?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-ticket-perforated fs-1 d-block mb-3"></i>
                                    Chưa có voucher nào được tạo.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
