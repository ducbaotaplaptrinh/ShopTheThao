<?php

namespace app\models\admin;

use app\core\Model;
use PDO;

class AdminVoucherModel extends Model
{
    public function getAllVouchers(): array
    {
        $sql = "SELECT m.*, ht.ten_hang 
                FROM ma_giam_gia m
                LEFT JOIN hang_thanh_vien ht ON m.ma_hang = ht.id
                ORDER BY m.ngay_tao DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getVoucherById(int $id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM ma_giam_gia WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isCodeExists(string $code, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM ma_giam_gia WHERE ma_code = :code AND id != :id");
            $stmt->execute(['code' => $code, 'id' => $excludeId]);
        } else {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM ma_giam_gia WHERE ma_code = :code");
            $stmt->execute(['code' => $code]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }

    public function createVoucher(array $data): bool
    {
        $sql = "INSERT INTO ma_giam_gia (
                    ma_code, ma_hang, tieu_de, mo_ta, loai_giam_gia, 
                    gia_tri_giam, don_hang_toi_thieu, muc_giam_toi_da, 
                    tong_so_luong, so_luong_da_dung, ngay_bat_dau, ngay_ket_thuc, trang_thai
                ) VALUES (
                    :ma_code, :ma_hang, :tieu_de, :mo_ta, :loai_giam_gia, 
                    :gia_tri_giam, :don_hang_toi_thieu, :muc_giam_toi_da, 
                    :tong_so_luong, 0, :ngay_bat_dau, :ngay_ket_thuc, :trang_thai
                )";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'ma_code' => $data['ma_code'],
            'ma_hang' => $data['ma_hang'] > 0 ? $data['ma_hang'] : null,
            'tieu_de' => $data['tieu_de'],
            'mo_ta' => $data['mo_ta'] ?? null,
            'loai_giam_gia' => $data['loai_giam_gia'],
            'gia_tri_giam' => $data['gia_tri_giam'],
            'don_hang_toi_thieu' => $data['don_hang_toi_thieu'] ?? 0.00,
            'muc_giam_toi_da' => $data['muc_giam_toi_da'] ?? null,
            'tong_so_luong' => $data['tong_so_luong'] ?? 0,
            'ngay_bat_dau' => $data['ngay_bat_dau'],
            'ngay_ket_thuc' => $data['ngay_ket_thuc'],
            'trang_thai' => $data['trang_thai'] ? 1 : 0
        ]);
    }

    public function updateVoucher(int $id, array $data): bool
    {
        $sql = "UPDATE ma_giam_gia SET 
                    ma_code = :ma_code,
                    ma_hang = :ma_hang,
                    tieu_de = :tieu_de,
                    mo_ta = :mo_ta,
                    loai_giam_gia = :loai_giam_gia,
                    gia_tri_giam = :gia_tri_giam,
                    don_hang_toi_thieu = :don_hang_toi_thieu,
                    muc_giam_toi_da = :muc_giam_toi_da,
                    tong_so_luong = :tong_so_luong,
                    ngay_bat_dau = :ngay_bat_dau,
                    ngay_ket_thuc = :ngay_ket_thuc,
                    trang_thai = :trang_thai
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'ma_code' => $data['ma_code'],
            'ma_hang' => $data['ma_hang'] > 0 ? $data['ma_hang'] : null,
            'tieu_de' => $data['tieu_de'],
            'mo_ta' => $data['mo_ta'] ?? null,
            'loai_giam_gia' => $data['loai_giam_gia'],
            'gia_tri_giam' => $data['gia_tri_giam'],
            'don_hang_toi_thieu' => $data['don_hang_toi_thieu'] ?? 0.00,
            'muc_giam_toi_da' => $data['muc_giam_toi_da'] ?? null,
            'tong_so_luong' => $data['tong_so_luong'] ?? 0,
            'ngay_bat_dau' => $data['ngay_bat_dau'],
            'ngay_ket_thuc' => $data['ngay_ket_thuc'],
            'trang_thai' => $data['trang_thai'] ? 1 : 0
        ]);
    }

    public function deleteVoucher(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM ma_giam_gia WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function toggleStatus(int $id): bool
    {
        $stmt = $this->conn->prepare("UPDATE ma_giam_gia SET trang_thai = 1 - trang_thai WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getAllTiers(): array
    {
        $stmt = $this->conn->query("SELECT * FROM hang_thanh_vien ORDER BY muc_chi_tieu_toi_thieu ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
