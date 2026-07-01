<?php
namespace app\controllers\admin;

use app\models\admin\AdminSettingModel;
use app\services\CloudService;

class AdminSettingController
{
    private $settingModel;

    public function __construct()
    {
        $this->settingModel = new AdminSettingModel();
    }

    /**
     * Hiển thị giao diện cấu hình
     */
    public function index()
    {
        $setting = $this->settingModel->getSetting();
        return [
            'setting' => $setting
        ];
    }

    /**
     * Xử lý cập nhật cấu hình
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $zalo_link = isset($_POST['zalo_link']) ? trim($_POST['zalo_link']) : '';
            $facebook_link = isset($_POST['facebook_link']) ? trim($_POST['facebook_link']) : '';
            $sdt = isset($_POST['sdt']) ? trim($_POST['sdt']) : '';
            $dia_chi = isset($_POST['dia_chi']) ? trim($_POST['dia_chi']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $bank_name = isset($_POST['bank_name']) ? trim($_POST['bank_name']) : '';
            $bank_account = isset($_POST['bank_account']) ? trim($_POST['bank_account']) : '';
            $bank_owner = isset($_POST['bank_owner']) ? trim($_POST['bank_owner']) : '';
            $text_topbar_1 = isset($_POST['text_topbar_1']) ? trim($_POST['text_topbar_1']) : '';
            $text_topbar_2 = isset($_POST['text_topbar_2']) ? trim($_POST['text_topbar_2']) : '';

            $logo_url = "";
            $logo_tab_bar_url = "";
            $qr_code_url = null; // null có nghĩa là không đổi

            // Xử lý upload logo website
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $uploadedUrl = CloudService::uploadImage($_FILES['logo']);
                if ($uploadedUrl) {
                    $logo_url = $uploadedUrl;
                }
            }

            // Xử lý upload logo tab bar
            if (isset($_FILES['logo_tab_bar']) && $_FILES['logo_tab_bar']['error'] === UPLOAD_ERR_OK) {
                $uploadedUrl = CloudService::uploadImage($_FILES['logo_tab_bar']);
                if ($uploadedUrl) {
                    $logo_tab_bar_url = $uploadedUrl;
                }
            }

            // Xử lý upload QR code tĩnh
            if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] === UPLOAD_ERR_OK) {
                $uploadedUrl = CloudService::uploadImage($_FILES['qr_code']);
                if ($uploadedUrl) {
                    $qr_code_url = $uploadedUrl;
                }
            }

            // Nếu người dùng chọn xóa ảnh QR code tĩnh cũ
            if (isset($_POST['delete_qr_code']) && $_POST['delete_qr_code'] === '1') {
                $qr_code_url = ""; // xóa giá trị cũ trong DB
            }

            $updateData = [
                'zalo_link' => $zalo_link,
                'facebook_link' => $facebook_link,
                'sdt' => $sdt,
                'dia_chi' => $dia_chi,
                'email' => $email,
                'bank_name' => $bank_name,
                'bank_account' => $bank_account,
                'bank_owner' => $bank_owner,
                'logo_url' => $logo_url,
                'logo_tab_bar_url' => $logo_tab_bar_url,
                'text_topbar_1' => $text_topbar_1,
                'text_topbar_2' => $text_topbar_2
            ];

            if ($qr_code_url !== null) {
                $updateData['qr_code_url'] = $qr_code_url;
            }

            $isSuccess = $this->settingModel->updateSetting($updateData);

            if ($isSuccess) {
                header("Location: ?page=admin-setting&msg=success");
                exit;
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi lưu cấu hình!";
                header("Location: ?page=admin-setting");
                exit;
            }
        }
    }
}
