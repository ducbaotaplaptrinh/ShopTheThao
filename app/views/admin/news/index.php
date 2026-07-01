<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Quản lý Tin tức</h2>
        <a href="?page=admin-news-create" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Thêm bài viết mới
        </a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Cập nhật bài viết thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                Bài viết đã được ẩn (xóa mềm) thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card shadow border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%">ID</th>
                            <th scope="col" style="width: 15%">Ảnh đại diện</th>
                            <th scope="col" style="width: 35%">Tiêu đề</th>
                            <th scope="col" style="width: 10%">Tác giả</th>
                            <th scope="col" style="width: 10%" class="text-center">Lượt xem</th>
                            <th scope="col" style="width: 10%" class="text-center">Trạng thái</th>
                            <th scope="col" style="width: 15%" class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($newsList)): ?>
                            <?php foreach ($newsList as $news): ?>
                                <tr>
                                    <td><?= $news['id'] ?></td>
                                    <td>
                                        <?php if (!empty($news['anh_dai_dien'])): ?>
                                            <img src="<?= htmlspecialchars($news['anh_dai_dien']) ?>" alt="Thumbnail" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center text-muted img-thumbnail" style="width: 80px; height: 60px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($news['tieu_de']) ?>">
                                            <?= htmlspecialchars($news['tieu_de']) ?>
                                        </div>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($news['ngay_tao'])) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($news['tac_gia']) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark rounded-pill px-3"><?= number_format($news['luot_xem']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($news['trang_thai'] == 1): ?>
                                            <span class="badge bg-success">Đang hiện</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Đang ẩn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="?page=admin-news-toggle&id=<?= $news['id'] ?>" class="btn btn-sm <?= $news['trang_thai'] == 1 ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $news['trang_thai'] == 1 ? 'Ẩn bài viết' : 'Hiện bài viết' ?>">
                                                <i class="bi <?= $news['trang_thai'] == 1 ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                            </a>
                                            <a href="?page=admin-news-edit&id=<?= $news['id'] ?>" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?page=admin-news-delete&id=<?= $news['id'] ?>" class="btn btn-sm btn-outline-danger" title="Xóa mềm" onclick="return confirm('Bạn có chắc chắn muốn ẩn bài viết này không?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Chưa có bài viết nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
