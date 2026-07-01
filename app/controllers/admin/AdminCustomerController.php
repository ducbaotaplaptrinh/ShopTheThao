<?php

namespace app\controllers\admin;

use app\models\admin\AdminCustomerModel;

class AdminCustomerController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminCustomerModel();
    }

    public function index(): array
    {
        $customers = $this->model->getAllCustomers();
        $tiers = $this->model->getAllTiers();

        // Lấy danh sách voucher hoạt động để tặng
        $voucherModel = new \app\models\admin\AdminVoucherModel();
        $allVouchers = $voucherModel->getAllVouchers();
        $vouchers = array_filter($allVouchers, function($v) {
            return $v['trang_thai'] == 1 && strtotime($v['ngay_ket_thuc']) >= time();
        });

        return [
            'title' => 'Quản lý Khách hàng | Admin',
            'view' => 'admin/customer/index.php',
            'customers' => $customers,
            'tiers' => $tiers,
            'vouchers' => $vouchers
        ];
    }

    public function toggleStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $status = $_POST['trang_thai'];

            $this->model->toggleCustomerStatus($id, $status);
            
            header("Location: ?page=admin-customers");
            exit;
        }
    }

    public function giftVoucher()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerIds = isset($_POST['customer_ids']) ? $_POST['customer_ids'] : [];
            $voucherId = isset($_POST['voucher_id']) ? (int)$_POST['voucher_id'] : 0;

            if (empty($customerIds)) {
                $_SESSION['error'] = "Vui lòng chọn ít nhất một khách hàng để tặng.";
                header("Location: ?page=admin-customers");
                exit;
            }

            if ($voucherId <= 0) {
                $_SESSION['error'] = "Vui lòng chọn một mã giảm giá để tặng.";
                header("Location: ?page=admin-customers");
                exit;
            }

            try {
                $this->model->giftVouchersToCustomers($customerIds, $voucherId);
                $_SESSION['success'] = "Đã tặng mã giảm giá thành công cho " . count($customerIds) . " khách hàng.";
            } catch (\Exception $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            }

            header("Location: ?page=admin-customers");
            exit;
        }
    }
}
