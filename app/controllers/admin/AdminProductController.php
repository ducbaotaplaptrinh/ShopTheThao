<?php

namespace app\controllers\admin;

use app\models\admin\AdminProductModel;

class AdminProductController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminProductModel();
    }

    private function handleFileUpload($fileField, $targetDir = 'assets/images/products/')
    {
        if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$fileField];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array(strtolower($ext), $allowedExts)) {
            return null;
        }

        $newFileName = 'prod_' . uniqid() . '_' . time() . '.' . $ext;
        $destDir = BASE_PATH . '/public/' . $targetDir;
        $destPath = $destDir . $newFileName;

        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return $newFileName;
        }

        return null;
    }

    private function handleMultipleFileUploads($fileField, $targetDir = 'assets/images/')
    {
        $uploadedFiles = [];
        if (!isset($_FILES[$fileField]) || !is_array($_FILES[$fileField]['name'])) {
            return $uploadedFiles;
        }

        $files = $_FILES[$fileField];
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $destDir = BASE_PATH . '/public/' . $targetDir;

        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), $allowedExts)) {
                continue;
            }

            $newFileName = 'gallery_' . uniqid() . '_' . time() . '_' . $i . '.' . $ext;
            $destPath = $destDir . $newFileName;

            if (move_uploaded_file($files['tmp_name'][$i], $destPath)) {
                $uploadedFiles[] = $newFileName;
            }
        }

        return $uploadedFiles;
    }

    public function index(): array
    {
        $keyword = $_GET['keyword'] ?? '';
        $maDanhMuc = $_GET['ma_danh_muc'] ?? '';
        $maThuongHieu = $_GET['ma_thuong_hieu'] ?? '';
        $kho = $_GET['kho'] ?? '';
        $trangThai = $_GET['trang_thai'] ?? '';
        $daXoa = $_GET['da_xoa'] ?? '';
        $khuyenMai = $_GET['khuyen_mai'] ?? '';
        $doanhSo = $_GET['doanh_so'] ?? '';

        $limit = 10;
        $page = isset($_GET['page_no']) ? max(1, intval($_GET['page_no'])) : 1;
        $offset = ($page - 1) * $limit;

        $filters = [
            'keyword' => $keyword,
            'ma_danh_muc' => $maDanhMuc,
            'ma_thuong_hieu' => $maThuongHieu,
            'kho' => $kho,
            'trang_thai' => $trangThai,
            'da_xoa' => $daXoa,
            'khuyen_mai' => $khuyenMai,
            'doanh_so' => $doanhSo
        ];

        $products = $this->model->getFilteredProducts($filters, $limit, $offset);
        foreach ($products as &$p) {
            if (isset($p['so_bien_the']) && $p['so_bien_the'] > 0) {
                $p['variants'] = $this->model->getBienTheSanPham($p['id']);
            } else {
                $p['variants'] = [];
            }
        }
        $totalProducts = $this->model->countFilteredProducts($filters);
        $totalPages = ceil($totalProducts / $limit);

        $categories = $this->model->getCategoriesForDropdown();
        $brands = $this->model->getBrandsForDropdown();

        return [
            'title' => 'Quản lý Sản phẩm | Admin',
            'view' => 'admin/product/index.php',
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'successMsg' => $_GET['success'] ?? '',
            'errorMsg' => $_GET['error'] ?? ''
        ];
    }

    public function create(): array
    {
        $categories = $this->model->getCategoriesForDropdown();
        $brands = $this->model->getBrandsForDropdown();
        $attributes = $this->model->getVariantAttributes();

        return [
            'title' => 'Thêm Sản phẩm mới | Admin',
            'view' => 'admin/product/form.php',
            'categories' => $categories,
            'brands' => $brands,
            'attributes' => $attributes
        ];
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // === VALIDATION GIÁ ===
            $giaBan       = (float)($_POST['gia_ban'] ?? 0);
            $giaKhuyenMai = !empty($_POST['gia_khuyen_mai']) ? (float)$_POST['gia_khuyen_mai'] : null;

            if ($giaBan <= 0) {
                header("Location: ?page=admin-product-create&error=invalid_price");
                exit;
            }
            if ($giaKhuyenMai !== null && $giaKhuyenMai >= $giaBan) {
                header("Location: ?page=admin-product-create&error=invalid_promo_price");
                exit;
            }
            // =====================

            // Upload ảnh đại diện
            $anhDaiDien = $this->handleFileUpload('anh_dai_dien');
            $_POST['anh_dai_dien'] = $anhDaiDien;

            // Upload ảnh phụ
            $anhPhuList = $this->handleMultipleFileUploads('anh_thu_vien');

            try {
                $productId = $this->model->insertProductWithVariants($_POST, $_POST['variants'] ?? '');

                if ($productId && !empty($anhPhuList)) {
                    $this->model->insertProductGalleryImages($productId, $anhPhuList);
                }

                header("Location: ?page=admin-products&success=created");
                exit;
            } catch (\Exception $e) {
                die("Error saving product: " . $e->getMessage());
            }
        }
    }

    public function edit(): array
    {
        $id = $_GET['id'] ?? 0;

        $product = $this->model->getProductById($id);
        if (!$product) {
            header("Location: ?page=admin-products");
            exit;
        }

        $variants = $this->model->getProductVariants($id);
        $gallery = $this->model->getProductGalleryImages($id);
        $categories = $this->model->getCategoriesForDropdown();
        $brands = $this->model->getBrandsForDropdown();
        $attributes = $this->model->getVariantAttributes();

        return [
            'title' => 'Sửa Sản phẩm | Admin',
            'view' => 'admin/product/edit.php',
            'product' => $product,
            'variants' => $variants,
            'gallery' => $gallery,
            'categories' => $categories,
            'brands' => $brands,
            'attributes' => $attributes
        ];
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            // === VALIDATION GIÁ ===
            $giaBan       = (float)($_POST['gia_ban'] ?? 0);
            $giaKhuyenMai = !empty($_POST['gia_khuyen_mai']) ? (float)$_POST['gia_khuyen_mai'] : null;

            if ($giaBan <= 0) {
                header("Location: ?page=admin-product-edit&id={$id}&error=invalid_price");
                exit;
            }
            if ($giaKhuyenMai !== null && $giaKhuyenMai >= $giaBan) {
                header("Location: ?page=admin-product-edit&id={$id}&error=invalid_promo_price");
                exit;
            }
            // =====================

            // Upload ảnh đại diện mới (nếu có)
            $anhDaiDienMoi = $this->handleFileUpload('anh_dai_dien');
            $_POST['anh_dai_dien'] = $anhDaiDienMoi;

            // Xử lý xóa ảnh phụ được tích chọn
            if (!empty($_POST['xoa_anh_phu'])) {
                $this->model->deleteProductGalleryImages($_POST['xoa_anh_phu']);
            }

            // Upload thêm ảnh phụ mới
            $anhPhuMoi = $this->handleMultipleFileUploads('anh_thu_vien');

            try {
                $this->model->updateProductWithVariants($id, $_POST, $_POST['variants'] ?? '');

                if (!empty($anhPhuMoi)) {
                    $this->model->insertProductGalleryImages($id, $anhPhuMoi);
                }

                header("Location: ?page=admin-products&success=updated");
                exit;
            } catch (\Exception $e) {
                die("Error updating product: " . $e->getMessage());
            }
        }
    }

    public function xoaSanPham()
    {
        $id = $_GET['id'] ?? 0;
        if ($id > 0) {
            try {
                $this->model->xoaMemSanPham($id);
                header("Location: ?page=admin-products&success=deleted");
                exit;
            } catch (\Exception $e) {
                header("Location: ?page=admin-products&error=" . urlencode($e->getMessage()));
                exit;
            }
        }
        header("Location: ?page=admin-products");
        exit;
    }

    public function khoiPhucSanPham()
    {
        $id = $_GET['id'] ?? 0;
        if ($id > 0) {
            try {
                $this->model->khoiPhucSanPham($id);
                header("Location: ?page=admin-products&success=restored");
                exit;
            } catch (\Exception $e) {
                die("Lỗi khi khôi phục sản phẩm: " . $e->getMessage());
            }
        }
        header("Location: ?page=admin-products");
        exit;
    }

    public function batchDiscount()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $discountType = $_POST['discount_type'] ?? 'phan_tram';
            $discountValue = isset($_POST['discount_value']) ? floatval($_POST['discount_value']) : 0;
            $applyToAll = isset($_POST['apply_to_all_filtered']) && $_POST['apply_to_all_filtered'] == '1';

            if ($applyToAll) {
                $filters = [
                    'keyword' => $_POST['keyword'] ?? '',
                    'ma_danh_muc' => $_POST['ma_danh_muc'] ?? '',
                    'ma_thuong_hieu' => $_POST['ma_thuong_hieu'] ?? '',
                    'kho' => $_POST['kho'] ?? '',
                    'trang_thai' => $_POST['trang_thai'] ?? '',
                    'da_xoa' => $_POST['da_xoa'] ?? '',
                    'khuyen_mai' => $_POST['khuyen_mai'] ?? '',
                    'doanh_so' => $_POST['doanh_so'] ?? '',
                ];
                $productIds = $this->model->getFilteredProductIds($filters);
            } else {
                $productIds = $_POST['product_ids'] ?? [];
            }

            if (empty($productIds) || !is_array($productIds)) {
                header("Location: ?page=admin-products&error=no_products_selected");
                exit;
            }

            if ($discountValue < 0) {
                header("Location: ?page=admin-products&error=invalid_discount_value");
                exit;
            }

            if ($discountType === 'phan_tram' && $discountValue > 100) {
                header("Location: ?page=admin-products&error=invalid_discount_percentage");
                exit;
            }

            try {
                $this->model->applyBatchDiscount($productIds, $discountValue, $discountType);
                header("Location: ?page=admin-products&success=batch_discount_applied");
                exit;
            } catch (\Exception $e) {
                header("Location: ?page=admin-products&error=" . urlencode($e->getMessage()));
                exit;
            }
        }
        header("Location: ?page=admin-products");
        exit;
    }
}
