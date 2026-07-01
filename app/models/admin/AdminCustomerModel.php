<?php

namespace app\models\admin;

use app\core\Model;
use PDO;

class AdminCustomerModel extends Model
{
    public function getAllCustomers()
    {
        $sql = "SELECT nd.*, ht.ten_hang, ht.mau_sac, ht.bieu_tuong,
                (SELECT COUNT(*) FROM don_hang WHERE ma_nguoi_dung = nd.id) as so_don_hang
                FROM nguoi_dung nd
                LEFT JOIN hang_thanh_vien ht ON nd.ma_hang = ht.id
                WHERE nd.vai_tro = 'khach_hang'
                ORDER BY nd.tong_chi_tieu DESC";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTiers()
    {
        return $this->conn->query("SELECT * FROM hang_thanh_vien ORDER BY muc_chi_tieu_toi_thieu ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleCustomerStatus($id, $status)
    {
        $stmt = $this->conn->prepare("UPDATE nguoi_dung SET trang_thai = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function giftVouchersToCustomers(array $customerIds, int $voucherId): bool
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO vi_ma_giam_gia (ma_nguoi_dung, ma_giam_gia, da_su_dung) VALUES (?, ?, 0)";
            $stmt = $this->conn->prepare($sql);

            foreach ($customerIds as $userId) {
                // Kiểm tra xem khách hàng này đã có mã này trong ví mà chưa dùng hay chưa
                $checkSql = "SELECT COUNT(*) FROM vi_ma_giam_gia WHERE ma_nguoi_dung = ? AND ma_giam_gia = ? AND da_su_dung = 0";
                $checkStmt = $this->conn->prepare($checkSql);
                $checkStmt->execute([$userId, $voucherId]);
                if ((int)$checkStmt->fetchColumn() > 0) {
                    continue; // Bỏ qua nếu đã có sẵn
                }

                $stmt->execute([$userId, $voucherId]);
            }

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
