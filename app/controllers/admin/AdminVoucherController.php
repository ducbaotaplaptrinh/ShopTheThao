<?php

namespace app\controllers\admin;

use app\models\admin\AdminVoucherModel;

class AdminVoucherController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminVoucherModel();
    }

    public function index(): array
    {
        $vouchers = $this->model->getAllVouchers();
        return [
            'vouchers' => $vouchers
        ];
    }

    public function create(): array
    {
        $tiers = $this->model->getAllTiers();
        return [
            'tiers' => $tiers
        ];
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ma_code = isset($_POST['ma_code']) ? strtoupper(trim($_POST['ma_code'])) : '';
            $tieu_de = isset($_POST['tieu_de']) ? trim($_POST['tieu_de']) : '';
            $mo_ta = isset($_POST['mo_ta']) ? trim($_POST['mo_ta']) : '';
            $loai_giam_gia = isset($_POST['loai_giam_gia']) ? trim($_POST['loai_giam_gia']) : 'tien_co_dinh';
            $gia_tri_giam = isset($_POST['gia_tri_giam']) ? (float)$_POST['gia_tri_giam'] : 0.00;
            $don_hang_toi_thieu = isset($_POST['don_hang_toi_thieu']) ? (float)$_POST['don_hang_toi_thieu'] : 0.00;
            $muc_giam_toi_da = (isset($_POST['muc_giam_toi_da']) && $_POST['muc_giam_toi_da'] !== '') ? (float)$_POST['muc_giam_toi_da'] : null;
            $tong_so_luong = isset($_POST['tong_so_luong']) ? (int)$_POST['tong_so_luong'] : 0;
            $ngay_bat_dau = isset($_POST['ngay_bat_dau']) ? trim($_POST['ngay_bat_dau']) : '';
            $ngay_ket_thuc = isset($_POST['ngay_ket_thuc']) ? trim($_POST['ngay_ket_thuc']) : '';
            $ma_hang = isset($_POST['ma_hang']) ? (int)$_POST['ma_hang'] : 0;
            $trang_thai = isset($_POST['trang_thai']) ? 1 : 0;

            // Simple validations
            $errors = [];
            if (empty($ma_code)) {
                $errors[] = "Mã voucher không được để trống.";
            } elseif ($this->model->isCodeExists($ma_code)) {
                $errors[] = "Mã voucher '{$ma_code}' đã tồn tại.";
            }

            if (empty($tieu_de)) {
                $errors[] = "Tiêu đề không được để trống.";
            }

            if ($gia_tri_giam <= 0) {
                $errors[] = "Giá trị giảm phải lớn hơn 0.";
            }

            if ($loai_giam_gia === 'phan_tram' && $gia_tri_giam > 100) {
                $errors[] = "Giá trị giảm theo phần trăm không được vượt quá 100%.";
            }

            if ($tong_so_luong < 0) {
                $errors[] = "Tổng số lượng không được âm.";
            }

            if (empty($ngay_bat_dau) || empty($ngay_ket_thuc)) {
                $errors[] = "Vui lòng chọn đầy đủ ngày bắt đầu và ngày kết thúc.";
            } elseif (strtotime($ngay_ket_thuc) <= strtotime($ngay_bat_dau)) {
                $errors[] = "Ngày kết thúc phải lớn hơn ngày bắt đầu.";
            }

            if (!empty($errors)) {
                $_SESSION['error'] = implode("<br>", $errors);
                header("Location: ?page=admin-voucher-create");
                exit;
            }

            $data = [
                'ma_code' => $ma_code,
                'ma_hang' => $ma_hang,
                'tieu_de' => $tieu_de,
                'mo_ta' => $mo_ta,
                'loai_giam_gia' => $loai_giam_gia,
                'gia_tri_giam' => $gia_tri_giam,
                'don_hang_toi_thieu' => $don_hang_toi_thieu,
                'muc_giam_toi_da' => $muc_giam_toi_da,
                'tong_so_luong' => $tong_so_luong,
                'ngay_bat_dau' => $ngay_bat_dau,
                'ngay_ket_thuc' => $ngay_ket_thuc,
                'trang_thai' => $trang_thai
            ];

            if ($this->model->createVoucher($data)) {
                header("Location: ?page=admin-vouchers&msg=created");
                exit;
            } else {
                $_SESSION['error'] = "Đã xảy ra lỗi hệ thống khi thêm voucher.";
                header("Location: ?page=admin-voucher-create");
                exit;
            }
        }
    }

    public function edit(): array
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $voucher = $this->model->getVoucherById($id);

        if (!$voucher) {
            header("Location: ?page=admin-vouchers");
            exit;
        }

        $tiers = $this->model->getAllTiers();
        return [
            'voucher' => $voucher,
            'tiers' => $tiers
        ];
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $ma_code = isset($_POST['ma_code']) ? strtoupper(trim($_POST['ma_code'])) : '';
            $tieu_de = isset($_POST['tieu_de']) ? trim($_POST['tieu_de']) : '';
            $mo_ta = isset($_POST['mo_ta']) ? trim($_POST['mo_ta']) : '';
            $loai_giam_gia = isset($_POST['loai_giam_gia']) ? trim($_POST['loai_giam_gia']) : 'tien_co_dinh';
            $gia_tri_giam = isset($_POST['gia_tri_giam']) ? (float)$_POST['gia_tri_giam'] : 0.00;
            $don_hang_toi_thieu = isset($_POST['don_hang_toi_thieu']) ? (float)$_POST['don_hang_toi_thieu'] : 0.00;
            $muc_giam_toi_da = (isset($_POST['muc_giam_toi_da']) && $_POST['muc_giam_toi_da'] !== '') ? (float)$_POST['muc_giam_toi_da'] : null;
            $tong_so_luong = isset($_POST['tong_so_luong']) ? (int)$_POST['tong_so_luong'] : 0;
            $ngay_bat_dau = isset($_POST['ngay_bat_dau']) ? trim($_POST['ngay_bat_dau']) : '';
            $ngay_ket_thuc = isset($_POST['ngay_ket_thuc']) ? trim($_POST['ngay_ket_thuc']) : '';
            $ma_hang = isset($_POST['ma_hang']) ? (int)$_POST['ma_hang'] : 0;
            $trang_thai = isset($_POST['trang_thai']) ? 1 : 0;

            $errors = [];
            if (empty($ma_code)) {
                $errors[] = "Mã voucher không được để trống.";
            } elseif ($this->model->isCodeExists($ma_code, $id)) {
                $errors[] = "Mã voucher '{$ma_code}' đã được sử dụng bởi voucher khác.";
            }

            if (empty($tieu_de)) {
                $errors[] = "Tiêu đề không được để trống.";
            }

            if ($gia_tri_giam <= 0) {
                $errors[] = "Giá trị giảm phải lớn hơn 0.";
            }

            if ($loai_giam_gia === 'phan_tram' && $gia_tri_giam > 100) {
                $errors[] = "Giá trị giảm theo phần trăm không được vượt quá 100%.";
            }

            if ($tong_so_luong < 0) {
                $errors[] = "Tổng số lượng không được âm.";
            }

            if (empty($ngay_bat_dau) || empty($ngay_ket_thuc)) {
                $errors[] = "Vui lòng chọn đầy đủ ngày bắt đầu và ngày kết thúc.";
            } elseif (strtotime($ngay_ket_thuc) <= strtotime($ngay_bat_dau)) {
                $errors[] = "Ngày kết thúc phải lớn hơn ngày bắt đầu.";
            }

            if (!empty($errors)) {
                $_SESSION['error'] = implode("<br>", $errors);
                header("Location: ?page=admin-voucher-edit&id=" . $id);
                exit;
            }

            $data = [
                'ma_code' => $ma_code,
                'ma_hang' => $ma_hang,
                'tieu_de' => $tieu_de,
                'mo_ta' => $mo_ta,
                'loai_giam_gia' => $loai_giam_gia,
                'gia_tri_giam' => $gia_tri_giam,
                'don_hang_toi_thieu' => $don_hang_toi_thieu,
                'muc_giam_toi_da' => $muc_giam_toi_da,
                'tong_so_luong' => $tong_so_luong,
                'ngay_bat_dau' => $ngay_bat_dau,
                'ngay_ket_thuc' => $ngay_ket_thuc,
                'trang_thai' => $trang_thai
            ];

            if ($this->model->updateVoucher($id, $data)) {
                header("Location: ?page=admin-vouchers&msg=updated");
                exit;
            } else {
                $_SESSION['error'] = "Đã xảy ra lỗi hệ thống khi cập nhật voucher.";
                header("Location: ?page=admin-voucher-edit&id=" . $id);
                exit;
            }
        }
    }

    public function delete(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $this->model->deleteVoucher($id);
        }
        header("Location: ?page=admin-vouchers&msg=deleted");
        exit;
    }

    public function toggleStatus(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $this->model->toggleStatus($id);
        }
        header("Location: ?page=admin-vouchers");
        exit;
    }
}
