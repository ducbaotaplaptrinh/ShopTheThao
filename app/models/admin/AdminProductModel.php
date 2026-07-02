<?php

namespace app\models\admin;

use app\core\Model;
use PDO;
use app\services\MailService;

class AdminProductModel extends Model
{
    // Lấy tất cả sản phẩm (kể cả sản phẩm đã xóa mềm) cho admin quản lý
    public function getAllProducts()
    {
        $sql = "SELECT sp.*, dm.ten_danh_muc, th.ten_thuong_hieu,
                COALESCE((SELECT SUM(so_luong_ton) FROM bien_the_san_pham WHERE ma_san_pham = sp.id AND trang_thai = 1 AND ngay_xoa IS NULL), sp.so_luong_ton, 0) as tong_ton_kho,
                (SELECT COUNT(*) FROM bien_the_san_pham WHERE ma_san_pham = sp.id AND so_luong_ton < 5 AND trang_thai = 1 AND ngay_xoa IS NULL) as so_bien_the_het_hang
                FROM san_pham sp
                LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.id
                LEFT JOIN thuong_hieu th ON sp.ma_thuong_hieu = th.id
                ORDER BY sp.ngay_xoa ASC, sp.id DESC";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function buildFilterSQL($filters)
    {
        $where = ["1=1"];
        $params = [];

        // 1. Text Search (keyword)
        if (!empty($filters['keyword'])) {
            $keyword = "%" . trim($filters['keyword']) . "%";
            $where[] = "(sp.ten_san_pham LIKE :keyword 
                         OR CAST(sp.id AS CHAR) LIKE :keyword 
                         OR sp.ma_vach_sku LIKE :keyword 
                         OR EXISTS (SELECT 1 FROM bien_the_san_pham bt WHERE bt.ma_san_pham = sp.id AND bt.ma_vach_sku LIKE :keyword))";
            $params['keyword'] = $keyword;
        }

        // 2. Category
        if (!empty($filters['ma_danh_muc'])) {
            if (is_numeric($filters['ma_danh_muc'])) {
                $where[] = "sp.ma_danh_muc = :ma_danh_muc";
                $params['ma_danh_muc'] = intval($filters['ma_danh_muc']);
            } else {
                $where[] = "dm.duong_dan_slug = :slug_danh_muc";
                $params['slug_danh_muc'] = $filters['ma_danh_muc'];
            }
        }

        // 3. Brand
        if (!empty($filters['ma_thuong_hieu'])) {
            if (is_numeric($filters['ma_thuong_hieu'])) {
                $where[] = "sp.ma_thuong_hieu = :ma_thuong_hieu";
                $params['ma_thuong_hieu'] = intval($filters['ma_thuong_hieu']);
            } else {
                $where[] = "th.duong_dan_slug = :slug_thuong_hieu";
                $params['slug_thuong_hieu'] = $filters['ma_thuong_hieu'];
            }
        }

        // 4. Stock Status (kho)
        if (!empty($filters['kho'])) {
            $subquery = "COALESCE((SELECT SUM(so_luong_ton) FROM bien_the_san_pham WHERE ma_san_pham = sp.id AND trang_thai = 1 AND ngay_xoa IS NULL), sp.so_luong_ton, 0)";
            if ($filters['kho'] === 'con_hang') {
                $where[] = "$subquery > 5";
            } elseif ($filters['kho'] === 'sap_het_hang') {
                $where[] = "$subquery > 0 AND $subquery <= 5";
            } elseif ($filters['kho'] === 'het_hang') {
                $where[] = "$subquery = 0";
            }
        }

        // 5. Display Status (trang_thai)
        if (isset($filters['trang_thai']) && $filters['trang_thai'] !== '') {
            $where[] = "sp.trang_thai = :trang_thai";
            $params['trang_thai'] = intval($filters['trang_thai']);
        }

        // 6. Soft Delete (da_xoa)
        if (isset($filters['da_xoa']) && $filters['da_xoa'] === '1') {
            $where[] = "sp.ngay_xoa IS NOT NULL";
        } else {
            $where[] = "sp.ngay_xoa IS NULL";
        }

        // 7. On Sale (khuyen_mai)
        if (isset($filters['khuyen_mai']) && $filters['khuyen_mai'] === '1') {
            $where[] = "sp.gia_khuyen_mai > 0 AND sp.gia_khuyen_mai < sp.gia_ban";
        }

        return [
            'where' => implode(" AND ", $where),
            'params' => $params
        ];
    }

    public function getFilteredProducts($filters, $limit = 10, $offset = 0)
    {
        $filterData = $this->buildFilterSQL($filters);
        $whereSql = $filterData['where'];
        $params = $filterData['params'];

        // Determine sorting order based on doanh_so filter
        $orderBy = "sp.ngay_xoa ASC, sp.id DESC";
        if (!empty($filters['doanh_so'])) {
            if ($filters['doanh_so'] === 'ban_chay') {
                $orderBy = "sp.ngay_xoa ASC, da_ban DESC, sp.id DESC";
            } elseif ($filters['doanh_so'] === 'ban_cham') {
                $orderBy = "sp.ngay_xoa ASC, da_ban ASC, sp.id DESC";
            }
        }

        $sql = "SELECT sp.*, dm.ten_danh_muc, th.ten_thuong_hieu,
                COALESCE((SELECT SUM(so_luong_ton) FROM bien_the_san_pham WHERE ma_san_pham = sp.id AND trang_thai = 1 AND ngay_xoa IS NULL), sp.so_luong_ton, 0) as tong_ton_kho,
                (SELECT COUNT(*) FROM bien_the_san_pham WHERE ma_san_pham = sp.id AND so_luong_ton < 5 AND trang_thai = 1 AND ngay_xoa IS NULL) as so_bien_the_het_hang,
                (SELECT COUNT(*) FROM bien_the_san_pham WHERE ma_san_pham = sp.id AND trang_thai = 1 AND ngay_xoa IS NULL) as so_bien_the,
                COALESCE((
                    SELECT SUM(ct.so_luong) 
                    FROM chi_tiet_don_hang ct 
                    JOIN don_hang dh ON ct.ma_don_hang = dh.id 
                    WHERE ct.ma_san_pham = sp.id AND dh.trang_thai_don_hang != 'da_huy'
                ), 0) AS da_ban
                FROM san_pham sp
                LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.id
                LEFT JOIN thuong_hieu th ON sp.ma_thuong_hieu = th.id
                WHERE $whereSql
                ORDER BY $orderBy
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countFilteredProducts($filters)
    {
        $filterData = $this->buildFilterSQL($filters);
        $whereSql = $filterData['where'];
        $params = $filterData['params'];

        $sql = "SELECT COUNT(*) 
                FROM san_pham sp
                LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.id
                LEFT JOIN thuong_hieu th ON sp.ma_thuong_hieu = th.id
                WHERE $whereSql";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getCategoriesForDropdown()
    {
        return $this->conn->query("SELECT id, ten_danh_muc FROM danh_muc")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBrandsForDropdown()
    {
        return $this->conn->query("SELECT id, ten_thuong_hieu FROM thuong_hieu")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVariantAttributes()
    {
        $attributes = [];
        $stmtAttr = $this->conn->query("SELECT * FROM thuoc_tinh WHERE la_bien_the = 1");
        while ($attr = $stmtAttr->fetch(PDO::FETCH_ASSOC)) {
            $stmtVal = $this->conn->prepare("SELECT * FROM gia_tri_thuoc_tinh WHERE ma_thuoc_tinh = ?");
            $stmtVal->execute([$attr['id']]);
            $attr['values'] = $stmtVal->fetchAll(PDO::FETCH_ASSOC);
            $attributes[] = $attr;
        }
        return $attributes;
    }

    public function insertProductWithVariants($data, $variantsJson)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Insert Base Product
            $sqlProd = "INSERT INTO san_pham (ten_san_pham, duong_dan_slug, ma_danh_muc, ma_thuong_hieu, mo_ta_chi_tiet, gia_ban, gia_khuyen_mai, so_luong_ton, la_noi_bat, trang_thai, anh_dai_dien) 
                        VALUES (:ten, :slug, :dm, :th, :mota, :gia, :gia_km, :sl_ton, :noibat, :trangthai, :anh_dai_dien)";

            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['ten_san_pham'])));

            // Xử lý giá khuyến mãi: nếu rỗng hoặc = 0 thì lưu NULL
            $giaKhuyenMai = !empty($data['gia_khuyen_mai']) && (float) $data['gia_khuyen_mai'] > 0
                ? (float) $data['gia_khuyen_mai']
                : null;

            $stmtProd = $this->conn->prepare($sqlProd);
            $stmtProd->execute([
                'ten' => $data['ten_san_pham'],
                'slug' => $slug,
                'dm' => $data['ma_danh_muc'],
                'th' => !empty($data['ma_thuong_hieu']) ? $data['ma_thuong_hieu'] : null,
                'mota' => $data['mo_ta_chi_tiet'] ?? '',
                'gia' => (float) ($data['gia_ban'] ?? 0),
                'gia_km' => $giaKhuyenMai,
                'sl_ton' => (int) ($data['so_luong_ton'] ?? 0),
                'noibat' => isset($data['la_noi_bat']) ? 1 : 0,
                'trangthai' => isset($data['trang_thai']) ? 1 : 0,
                'anh_dai_dien' => !empty($data['anh_dai_dien']) ? $data['anh_dai_dien'] : null
            ]);

            $productId = $this->conn->lastInsertId();

            // 2. Insert Variants
            if (!empty($variantsJson)) {
                $variants = json_decode($variantsJson, true);
                foreach ($variants as $variant) {
                    $sqlVar = "INSERT INTO bien_the_san_pham (ma_san_pham, ma_vach_sku, gia_ban_rieng, so_luong_ton) 
                               VALUES (:id_sp, :sku, :gia, :sl)";
                    $stmtVar = $this->conn->prepare($sqlVar);
                    $stmtVar->execute([
                        'id_sp' => $productId,
                        'sku' => $variant['sku'],
                        'gia' => !empty($variant['price']) ? $variant['price'] : $data['gia_ban'],
                        'sl' => $variant['stock']
                    ]);
                    $variantId = $this->conn->lastInsertId();

                    // 3. Link Variant to Attribute Values
                    if (!empty($variant['attributes'])) {
                        foreach ($variant['attributes'] as $attrId => $valId) {
                            $sqlLink = "INSERT INTO gia_tri_thuoc_tinh_bien_the (ma_bien_the, ma_gia_tri_thuoc_tinh) 
                                        VALUES (?, ?)";
                            $this->conn->prepare($sqlLink)->execute([$variantId, $valId]);
                        }
                    }
                }
            }

            $this->conn->commit();
            return $productId;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getProductById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM san_pham WHERE id = ? AND ngay_xoa IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProductVariants($id)
    {
        // Lấy các biến thể còn hoạt động
        $stmt = $this->conn->prepare("SELECT * FROM bien_the_san_pham WHERE ma_san_pham = ? AND trang_thai = 1 AND ngay_xoa IS NULL");
        $stmt->execute([$id]);
        $variants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách ID giá trị thuộc tính cho từng biến thể
        foreach ($variants as &$var) {
            $stmtLink = $this->conn->prepare("SELECT ma_gia_tri_thuoc_tinh FROM gia_tri_thuoc_tinh_bien_the WHERE ma_bien_the = ?");
            $stmtLink->execute([$var['id']]);
            $links = $stmtLink->fetchAll(PDO::FETCH_COLUMN);
            $var['attributes_array'] = $links; // Mảng chứa các ID giá trị thuộc tính
        }

        return $variants;
    }

    public function updateProductWithVariants($id, $data, $variantsJson)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Update Base Product
            $sqlProd = "UPDATE san_pham SET 
                        ten_san_pham = :ten, 
                        duong_dan_slug = :slug, 
                        ma_danh_muc = :dm, 
                        ma_thuong_hieu = :th, 
                        mo_ta_chi_tiet = :mota, 
                        gia_ban = :gia,
                        gia_khuyen_mai = :gia_km,
                        so_luong_ton = :sl_ton,
                        la_noi_bat = :noibat, 
                        trang_thai = :trangthai,
                        anh_dai_dien = IFNULL(:anh_dai_dien, anh_dai_dien)
                        WHERE id = :id";

            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['ten_san_pham'])));

            // Xử lý giá khuyến mãi: nếu rỗng hoặc = 0 thì lưu NULL
            $giaKhuyenMai = !empty($data['gia_khuyen_mai']) && (float) $data['gia_khuyen_mai'] > 0
                ? (float) $data['gia_khuyen_mai']
                : null;

            $stmtProd = $this->conn->prepare($sqlProd);
            $stmtProd->execute([
                'ten' => $data['ten_san_pham'],
                'slug' => $slug,
                'dm' => $data['ma_danh_muc'],
                'th' => !empty($data['ma_thuong_hieu']) ? $data['ma_thuong_hieu'] : null,
                'mota' => $data['mo_ta_chi_tiet'] ?? '',
                'gia' => (float) ($data['gia_ban'] ?? 0),
                'gia_km' => $giaKhuyenMai,
                'sl_ton' => (int) ($data['so_luong_ton'] ?? 0),
                'noibat' => isset($data['la_noi_bat']) ? 1 : 0,
                'trangthai' => isset($data['trang_thai']) ? 1 : 0,
                'anh_dai_dien' => !empty($data['anh_dai_dien']) ? $data['anh_dai_dien'] : null,
                'id' => $id
            ]);

            // Gắn Trigger thông báo có hàng cho sản phẩm gốc (nếu sản phẩm này KHÔNG dùng biến thể và có số lượng tồn kho > 0)
            if (empty($variantsJson) && (int) ($data['so_luong_ton'] ?? 0) > 0) {
                $this->xuLyThongBaoCoHang($id, null, $data['ten_san_pham']);
            }

            // 2. Handle Variants
            // Lấy danh sách ID biến thể hiện tại để biết biến thể nào bị xóa
            $stmtCurrentVars = $this->conn->prepare("SELECT id FROM bien_the_san_pham WHERE ma_san_pham = ? AND trang_thai = 1 AND ngay_xoa IS NULL");
            $stmtCurrentVars->execute([$id]);
            $currentVarIds = $stmtCurrentVars->fetchAll(PDO::FETCH_COLUMN);
            $submittedVarIds = [];

            if (!empty($variantsJson)) {
                $variants = json_decode($variantsJson, true);

                foreach ($variants as $variant) {
                    if (!empty($variant['id']) && in_array($variant['id'], $currentVarIds)) {
                        // Cập nhật biến thể cũ
                        $submittedVarIds[] = $variant['id'];
                        $sqlVar = "UPDATE bien_the_san_pham SET ma_vach_sku = :sku, gia_ban_rieng = :gia, so_luong_ton = :sl WHERE id = :id_var";
                        $stmtVar = $this->conn->prepare($sqlVar);
                        $stmtVar->execute([
                            'sku' => $variant['sku'],
                            'gia' => !empty($variant['price']) ? $variant['price'] : $data['gia_ban'],
                            'sl' => $variant['stock'],
                            'id_var' => $variant['id']
                        ]);

                        // Gắn Trigger thông báo có hàng cho biến thể này (nếu số lượng cập nhật > 0)
                        if ((int) $variant['stock'] > 0) {
                            $this->xuLyThongBaoCoHang($id, $variant['id'], $data['ten_san_pham']);
                        }

                        // Xóa liên kết thuộc tính cũ và insert lại (để chắc chắn)
                        $this->conn->prepare("DELETE FROM gia_tri_thuoc_tinh_bien_the WHERE ma_bien_the = ?")->execute([$variant['id']]);

                        if (!empty($variant['attributes'])) {
                            foreach ($variant['attributes'] as $attrId => $valId) {
                                $sqlLink = "INSERT INTO gia_tri_thuoc_tinh_bien_the (ma_bien_the, ma_gia_tri_thuoc_tinh) VALUES (?, ?)";
                                $this->conn->prepare($sqlLink)->execute([$variant['id'], $valId]);
                            }
                        }
                    } else {
                        // Thêm biến thể mới
                        $sqlVar = "INSERT INTO bien_the_san_pham (ma_san_pham, ma_vach_sku, gia_ban_rieng, so_luong_ton) VALUES (:id_sp, :sku, :gia, :sl)";
                        $stmtVar = $this->conn->prepare($sqlVar);
                        $stmtVar->execute([
                            'id_sp' => $id,
                            'sku' => $variant['sku'],
                            'gia' => !empty($variant['price']) ? $variant['price'] : $data['gia_ban'],
                            'sl' => $variant['stock']
                        ]);
                        $newVariantId = $this->conn->lastInsertId();

                        if (!empty($variant['attributes'])) {
                            foreach ($variant['attributes'] as $attrId => $valId) {
                                $sqlLink = "INSERT INTO gia_tri_thuoc_tinh_bien_the (ma_bien_the, ma_gia_tri_thuoc_tinh) VALUES (?, ?)";
                                $this->conn->prepare($sqlLink)->execute([$newVariantId, $valId]);
                            }
                        }
                    }
                }
            }

            // 3. Xóa các biến thể cũ không còn tồn tại (Soft Delete)
            $varsToDelete = array_diff($currentVarIds, $submittedVarIds);
            if (!empty($varsToDelete)) {
                $placeholders = str_repeat('?,', count($varsToDelete) - 1) . '?';
                $sqlDel = "UPDATE bien_the_san_pham SET trang_thai = 0, ngay_xoa = NOW() WHERE id IN ($placeholders)";
                $this->conn->prepare($sqlDel)->execute(array_values($varsToDelete));
            }

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function xoaMemSanPham($id)
    {
        // 1. Kiểm tra xem sản phẩm có nằm trong đơn hàng nào không
        $stmtCheck = $this->conn->prepare("SELECT COUNT(*) FROM chi_tiet_don_hang WHERE ma_san_pham = ?");
        $stmtCheck->execute([$id]);
        $count = (int) $stmtCheck->fetchColumn();
        if ($count > 0) {
            throw new \Exception("Không thể xóa sản phẩm này vì sản phẩm đã tồn tại trong các đơn hàng trước đó!");
        }

        try {
            $this->conn->beginTransaction();

            // 2. Xóa mềm các biến thể thuộc sản phẩm này
            $stmtVar = $this->conn->prepare("UPDATE bien_the_san_pham SET trang_thai = 0, ngay_xoa = NOW() WHERE ma_san_pham = ?");
            $stmtVar->execute([$id]);

            // 3. Xóa mềm sản phẩm chính
            $stmtProd = $this->conn->prepare("UPDATE san_pham SET trang_thai = 0, ngay_xoa = NOW() WHERE id = ?");
            $stmtProd->execute([$id]);

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function khoiPhucSanPham($id)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Khôi phục các biến thể thuộc sản phẩm này
            $stmtVar = $this->conn->prepare("UPDATE bien_the_san_pham SET trang_thai = 1, ngay_xoa = NULL WHERE ma_san_pham = ?");
            $stmtVar->execute([$id]);

            // 2. Khôi phục sản phẩm chính
            $stmtProd = $this->conn->prepare("UPDATE san_pham SET trang_thai = 1, ngay_xoa = NULL WHERE id = ?");
            $stmtProd->execute([$id]);

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getProductGalleryImages($productId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM anh_san_pham WHERE ma_san_pham = ? ORDER BY thu_tu_sap_xep ASC, id ASC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertProductGalleryImages($productId, $images)
    {
        if (empty($images))
            return;
        $sql = "INSERT INTO anh_san_pham (ma_san_pham, duong_dan_anh, la_anh_chinh, thu_tu_sap_xep, ngay_tao) VALUES (?, ?, 0, 0, NOW())";
        $stmt = $this->conn->prepare($sql);
        foreach ($images as $img) {
            $stmt->execute([$productId, $img]);
        }
    }

    public function deleteProductGalleryImages($imageIds)
    {
        if (empty($imageIds))
            return;
        $placeholders = str_repeat('?,', count($imageIds) - 1) . '?';
        $sql = "DELETE FROM anh_san_pham WHERE id IN ($placeholders)";
        $this->conn->prepare($sql)->execute(array_values($imageIds));
    }

    private function xuLyThongBaoCoHang($productId, $variantId, $productName)
    {
        try {
            // 1. Khởi tạo Base URL cho sản phẩm
            $baseUrl = "https://baodatsport.onrender.com";
            $productLink = $baseUrl . "/?page=product-detail&id=" . $productId;

            // 2. Xây dựng câu query tìm email đăng ký nhận thông báo
            // Tìm các email đăng ký trang_thai = 0 khớp với mã sản phẩm.
            // Nếu có biến thể, ưu tiên gửi cho những người đăng ký đúng biến thể đó HOẶC đăng ký sản phẩm gốc (ma_bien_the IS NULL)
            if ($variantId !== null) {
                $sql = "SELECT id, email FROM thong_bao_het_hang 
                        WHERE ma_san_pham = :pid AND (ma_bien_the = :vid OR ma_bien_the IS NULL) AND trang_thai = 0";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['pid' => $productId, 'vid' => $variantId]);
            } else {
                $sql = "SELECT id, email FROM thong_bao_het_hang 
                        WHERE ma_san_pham = :pid AND ma_bien_the IS NULL AND trang_thai = 0";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['pid' => $productId]);
            }

            $subscribers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // 3. Lặp qua danh sách để gửi mail và cập nhật trạng thái
            if (!empty($subscribers)) {
                $idsToUpdate = [];
                foreach ($subscribers as $sub) {
                    $isSent = MailService::mailThongBaoCoHang($sub['email'], $productName, $productLink);

                    if ($isSent) {
                        $idsToUpdate[] = $sub['id'];
                    }
                }

                // 4. Đánh dấu đã gửi (trang_thai = 1) cho những email gửi thành công
                if (!empty($idsToUpdate)) {
                    $placeholders = implode(',', array_fill(0, count($idsToUpdate), '?'));
                    $sqlUpdate = "UPDATE thong_bao_het_hang SET trang_thai = 1 WHERE id IN ($placeholders)";
                    $stmtUpdate = $this->conn->prepare($sqlUpdate);
                    $stmtUpdate->execute($idsToUpdate);
                }
            }
        } catch (\PDOException $e) {
            // Bắt lỗi Database để không văng lỗi màn hình của Admin đang cập nhật sản phẩm
            error_log("Lỗi xử lý gửi thông báo hết hàng: " . $e->getMessage());
        }
    }
    public function getBienTheSanPham($productId)
    {
        $sql = "SELECT bt.* 
                FROM bien_the_san_pham bt 
                WHERE bt.ma_san_pham = :id AND bt.trang_thai = 1 AND bt.ngay_xoa IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $productId]);
        $variants = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($variants as &$bt) {
            $sqlAttr = "SELECT tt.ten_thuoc_tinh, gt.gia_tri, gt.id as gia_tri_id
                        FROM gia_tri_thuoc_tinh_bien_the gtttbt
                        JOIN gia_tri_thuoc_tinh gt ON gt.id = gtttbt.ma_gia_tri_thuoc_tinh
                        JOIN thuoc_tinh tt ON tt.id = gt.ma_thuoc_tinh
                        WHERE gtttbt.ma_bien_the = :ma_bien_the";
            $stmtAttr = $this->conn->prepare($sqlAttr);
            $stmtAttr->execute(['ma_bien_the' => $bt['id']]);
            $bt['attributes'] = $stmtAttr->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Lượng bán của từng biến thể
            $sqlSales = "SELECT COALESCE(SUM(ct.so_luong), 0) as da_ban
                         FROM chi_tiet_don_hang ct
                         JOIN don_hang dh ON ct.ma_don_hang = dh.id
                         WHERE ct.ma_bien_the = :ma_bien_the AND dh.trang_thai_don_hang != 'da_huy'";
            $stmtSales = $this->conn->prepare($sqlSales);
            $stmtSales->execute(['ma_bien_the' => $bt['id']]);
            $bt['da_ban'] = (int) $stmtSales->fetchColumn();
        }

        return $variants;
    }

    public function applyBatchDiscount(array $productIds, float $value, string $type): bool
    {
        try {
            $this->conn->beginTransaction();

            $sql = "UPDATE san_pham SET gia_khuyen_mai = :gia_km WHERE id = :id AND ngay_xoa IS NULL";
            $stmt = $this->conn->prepare($sql);

            $sqlSelect = "SELECT gia_ban FROM san_pham WHERE id = ? AND ngay_xoa IS NULL";
            $stmtSelect = $this->conn->prepare($sqlSelect);

            foreach ($productIds as $id) {
                $id = intval($id);
                if ($id <= 0) continue;

                $stmtSelect->execute([$id]);
                $giaBan = floatval($stmtSelect->fetchColumn());

                if ($giaBan > 0) {
                    $giaKM = null;
                    if ($value > 0) {
                        if ($type === 'phan_tram') {
                            $giaKM = $giaBan * (1 - $value / 100);
                        } elseif ($type === 'tien_mat') {
                            $giaKM = max(0.00, $giaBan - $value);
                        }
                    }
                    $stmt->execute([
                        'gia_km' => $giaKM,
                        'id' => $id
                    ]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getFilteredProductIds(array $filters): array
    {
        $filterData = $this->buildFilterSQL($filters);
        $whereSql = $filterData['where'];
        $params = $filterData['params'];

        $sql = "SELECT sp.id 
                FROM san_pham sp
                LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.id
                LEFT JOIN thuong_hieu th ON sp.ma_thuong_hieu = th.id
                WHERE $whereSql";
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }
}
