<?php

namespace app\models\admin;

use app\core\Model;
use PDO;

class AdminOrderModel extends Model
{
    public function getAllOrders()
    {
        $sql = "SELECT dh.*, nd.ho_ten, nd.email 
                FROM don_hang dh 
                LEFT JOIN nguoi_dung nd ON dh.ma_nguoi_dung = nd.id 
                ORDER BY dh.id DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($id)
    {
        $sqlOrder = "SELECT dh.*, nd.ho_ten, nd.email, nd.so_dien_thoai as sdt_user
                     FROM don_hang dh 
                     LEFT JOIN nguoi_dung nd ON dh.ma_nguoi_dung = nd.id 
                     WHERE dh.id = ?";
        $stmtOrder = $this->conn->prepare($sqlOrder);
        $stmtOrder->execute([$id]);
        return $stmtOrder->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($orderId)
    {
        $sqlItems = "SELECT ct.*, sp.duong_dan_slug, sp.so_luong_ton as sp_ton, bt.ma_vach_sku, bt.so_luong_ton as bt_ton 
                     FROM chi_tiet_don_hang ct
                     LEFT JOIN bien_the_san_pham bt ON ct.ma_bien_the = bt.id
                     LEFT JOIN san_pham sp ON ct.ma_san_pham = sp.id
                     WHERE ct.ma_don_hang = ?";
        $stmtItems = $this->conn->prepare($sqlItems);
        $stmtItems->execute([$orderId]);
        return $stmtItems->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái đơn hàng + xử lý kho trong 1 transaction.
     * - Khi chuyển sang 'da_huy' từ một trạng thái khác: hoàn kho
     * - Khi chuyển từ 'da_huy' sang một trạng thái khác: trừ kho
     *
     * @param int    $id         ID đơn hàng
     * @param string $trangThaiMoi Trạng thái mới
     * @param string $trangThaiCu  Trạng thái hiện tại
     * @return bool
     */
    public function capNhatTrangThaiDonHang($id, $trangThaiMoi, $trangThaiCu)
    {
        // 0. Ràng buộc bảo vệ cấp dữ liệu: không cho chuyển ngược trạng thái hoặc sửa đơn đã kết thúc
        $statusWeights = [
            'cho_xac_nhan' => 1,
            'dang_xu_ly'   => 2,
            'dang_giao'    => 3,
            'hoan_thanh'   => 4,
            'da_huy'       => 5
        ];

        if (!isset($statusWeights[$trangThaiMoi]) || !isset($statusWeights[$trangThaiCu])) {
            throw new \Exception("Trạng thái đơn hàng không hợp lệ.");
        }

        $wCu = $statusWeights[$trangThaiCu];
        $wMoi = $statusWeights[$trangThaiMoi];

        if ($trangThaiCu === 'hoan_thanh' || $trangThaiCu === 'da_huy') {
            throw new \Exception("Đơn hàng đã kết thúc (Hoàn thành/Đã hủy), không thể thay đổi trạng thái.");
        }

        if ($trangThaiMoi !== 'da_huy' && $wMoi < $wCu) {
            throw new \Exception("Không thể chuyển ngược trạng thái đơn hàng.");
        }

        try {
            $this->conn->beginTransaction();

            // 1. Cập nhật trạng thái đơn hàng
            $stmt = $this->conn->prepare("UPDATE don_hang SET trang_thai_don_hang = ? WHERE id = ?");
            $stmt->execute([$trangThaiMoi, $id]);

            // 2. Xử lý kho hàng và lượt dùng voucher
            if ($trangThaiMoi === 'da_huy' && $trangThaiCu !== 'da_huy') {
                // Hủy đơn hàng: Hoàn kho
                $this->hoanKho($id);

                // Hoàn lại lượt dùng voucher (nếu có)
                $order = $this->getOrderById($id);
                $couponCode = $order['ma_giam_gia'] ?? '';
                if (!empty($couponCode)) {
                    $stmtCoupon = $this->conn->prepare("UPDATE ma_giam_gia SET so_luong_da_dung = GREATEST(0, so_luong_da_dung - 1) WHERE ma_code = ?");
                    $stmtCoupon->execute([$couponCode]);

                    // Hoàn lại trong ví voucher
                    $stmtWallet = $this->conn->prepare("UPDATE vi_ma_giam_gia SET da_su_dung = 0, ngay_su_dung = NULL WHERE ma_nguoi_dung = ? AND ma_giam_gia = (SELECT id FROM ma_giam_gia WHERE ma_code = ? LIMIT 1)");
                    $stmtWallet->execute([$order['ma_nguoi_dung'], $couponCode]);
                }
            } elseif ($trangThaiMoi !== 'da_huy' && $trangThaiCu === 'da_huy') {
                // Khôi phục đơn hàng từ trạng thái Hủy: Trừ kho trở lại
                $this->truKho($id);

                // Trừ lại lượt dùng voucher (nếu có)
                $order = $this->getOrderById($id);
                $couponCode = $order['ma_giam_gia'] ?? '';
                if (!empty($couponCode)) {
                    $stmtCheckCoupon = $this->conn->prepare("SELECT so_luong_da_dung, tong_so_luong FROM ma_giam_gia WHERE ma_code = ?");
                    $stmtCheckCoupon->execute([$couponCode]);
                    $couponInfo = $stmtCheckCoupon->fetch(PDO::FETCH_ASSOC);
                    if ($couponInfo && $couponInfo['so_luong_da_dung'] >= $couponInfo['tong_so_luong']) {
                        throw new \Exception("Không thể khôi phục đơn hàng vì mã giảm giá '{$couponCode}' đã hết lượt sử dụng trên hệ thống.");
                    }
                    $stmtCoupon = $this->conn->prepare("UPDATE ma_giam_gia SET so_luong_da_dung = so_luong_da_dung + 1 WHERE ma_code = ?");
                    $stmtCoupon->execute([$couponCode]);

                    // Đánh dấu đã dùng trong ví voucher
                    $stmtWallet = $this->conn->prepare("UPDATE vi_ma_giam_gia SET da_su_dung = 1, ngay_su_dung = NOW() WHERE ma_nguoi_dung = ? AND ma_giam_gia = (SELECT id FROM ma_giam_gia WHERE ma_code = ? LIMIT 1)");
                    $stmtWallet->execute([$order['ma_nguoi_dung'], $couponCode]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Trừ kho: giảm so_luong_ton của biến thể và sản phẩm theo chi tiết đơn hàng.
     */
    private function truKho($orderId)
    {
        $items = $this->getOrderItems($orderId);

        foreach ($items as $item) {
            $prodId   = $item['ma_san_pham'];
            $varId    = $item['ma_bien_the'];
            $soLuong  = (int)$item['so_luong'];

            // 1. Trừ kho biến thể (nếu có)
            if (!empty($varId)) {
                $stmtCheckVar = $this->conn->prepare(
                    "SELECT so_luong_ton FROM bien_the_san_pham WHERE id = ? FOR UPDATE"
                );
                $stmtCheckVar->execute([$varId]);
                $rowVar = $stmtCheckVar->fetch(PDO::FETCH_ASSOC);

                if (!$rowVar || $rowVar['so_luong_ton'] < $soLuong) {
                    throw new \Exception("Biến thể ID {$varId} không đủ tồn kho (cần {$soLuong}, còn " . ($rowVar['so_luong_ton'] ?? 0) . ").");
                }

                $stmtTruVar = $this->conn->prepare(
                    "UPDATE bien_the_san_pham SET so_luong_ton = so_luong_ton - ? WHERE id = ?"
                );
                $stmtTruVar->execute([$soLuong, $varId]);
            }

            // 2. Trừ kho sản phẩm chung
            if (!empty($prodId)) {
                $stmtCheckProd = $this->conn->prepare(
                    "SELECT so_luong_ton FROM san_pham WHERE id = ? FOR UPDATE"
                );
                $stmtCheckProd->execute([$prodId]);
                $rowProd = $stmtCheckProd->fetch(PDO::FETCH_ASSOC);

                if (!$rowProd || $rowProd['so_luong_ton'] < $soLuong) {
                    throw new \Exception("Sản phẩm ID {$prodId} không đủ tồn kho (cần {$soLuong}, còn " . ($rowProd['so_luong_ton'] ?? 0) . ").");
                }

                $stmtTruProd = $this->conn->prepare(
                    "UPDATE san_pham SET so_luong_ton = so_luong_ton - ? WHERE id = ?"
                );
                $stmtTruProd->execute([$soLuong, $prodId]);
            }
        }
    }

    /**
     * Hoàn kho: cộng lại so_luong_ton cho cả biến thể và sản phẩm chung khi đơn hàng bị hủy.
     */
    private function hoanKho($orderId)
    {
        $items = $this->getOrderItems($orderId);

        foreach ($items as $item) {
            $prodId  = $item['ma_san_pham'];
            $varId   = $item['ma_bien_the'];
            $soLuong = (int)$item['so_luong'];

            if (!empty($varId)) {
                $stmt = $this->conn->prepare(
                    "UPDATE bien_the_san_pham SET so_luong_ton = so_luong_ton + ? WHERE id = ?"
                );
                $stmt->execute([$soLuong, $varId]);
            }

            if (!empty($prodId)) {
                $stmt = $this->conn->prepare(
                    "UPDATE san_pham SET so_luong_ton = so_luong_ton + ? WHERE id = ?"
                );
                $stmt->execute([$soLuong, $prodId]);
            }
        }
    }

    // Giữ lại hàm cũ để tương thích (alias — không xử lý kho)
    public function updateOrderStatus($id, $status)
    {
        $stmt = $this->conn->prepare("UPDATE don_hang SET trang_thai_don_hang = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    // Xóa đơn hàng (xóa thực sự - chỉ cho phép với đơn đã hủy)
    public function xoaDonHang($id)
    {
        // Xóa chi tiết đơn hàng trước
        $stmtChiTiet = $this->conn->prepare("DELETE FROM chi_tiet_don_hang WHERE ma_don_hang = ?");
        $stmtChiTiet->execute([$id]);

        // Xóa đơn hàng
        $stmtDon = $this->conn->prepare("DELETE FROM don_hang WHERE id = ?");
        return $stmtDon->execute([$id]);
    }
}
