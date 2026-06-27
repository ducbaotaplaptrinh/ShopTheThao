<div class="container-fluid mt-4">
    <h2 class="mb-4">Chỉnh sửa Bài Viết</h2>

    <form action="?page=admin-news-update" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $news['id'] ?>">
        
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiêu đề bài viết</label>
                    <input type="text" name="tieu_de" class="form-control" required placeholder="Nhập tiêu đề..." value="<?= htmlspecialchars($news['tieu_de']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tóm tắt (Hiển thị ở trang danh sách)</label>
                    <textarea name="tom_tat" class="form-control" rows="3" required
                        placeholder="Nhập tóm tắt ngắn..."><?= htmlspecialchars($news['tom_tat']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung bài viết</label>
                    <textarea name="noi_dung" id="editor" class="form-control"><?= htmlspecialchars($news['noi_dung']) ?></textarea>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Trạng thái</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="trang_thai" id="trang_thai" <?= $news['trang_thai'] == 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="trang_thai">Xuất bản hiển thị</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Ảnh đại diện (Thumbnail)</label>
                    <div class="mb-2">
                        <?php if (!empty($news['anh_dai_dien'])): ?>
                            <img src="<?= htmlspecialchars($news['anh_dai_dien']) ?>" alt="Thumbnail" class="img-thumbnail" style="max-width: 100%;">
                        <?php endif; ?>
                    </div>
                    <input type="file" name="anh_dai_dien" class="form-control" accept="image/*">
                    <small class="text-muted">Bỏ trống nếu không muốn đổi ảnh.</small>
                </div>

                <hr>
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Cập nhật Bài Viết</button>
                <a href="?page=admin-news" class="btn btn-outline-secondary w-100 mt-2">Hủy bỏ</a>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        ClassicEditor
            .create(document.querySelector('#editor'))
            .then(editor => {
                editor.editing.view.change(writer => {
                    writer.setStyle('min-height', '400px', editor.editing.view.document.getRoot());
                });
            })
            .catch(error => {
                console.error('Lỗi khởi tạo CKEditor:', error);
            });
    });
</script>
