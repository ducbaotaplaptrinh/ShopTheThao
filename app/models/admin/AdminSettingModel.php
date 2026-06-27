<?php
namespace app\models\admin;

use app\core\Model;
use PDO;

class AdminSettingModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->initializeTable();
    }

    /**
     * Tạo bảng cấu hình và gieo dữ liệu mẫu nếu chưa có
     */
    private function initializeTable()
    {
        // 1. Tạo bảng nếu chưa tồn tại
        $sqlCreate = "CREATE TABLE IF NOT EXISTS cau_hinh_giao_dien (
            id INT AUTO_INCREMENT PRIMARY KEY,
            logo_url VARCHAR(255) DEFAULT 'assets/images/favicons/Logo.png',
            zalo_link VARCHAR(255) DEFAULT 'https://zalo.me/your_number',
            facebook_link VARCHAR(255) DEFAULT 'https://m.me/your_username',
            sdt VARCHAR(50) DEFAULT '0900 123 456',
            dia_chi VARCHAR(255) DEFAULT '123 Đường Thể Thao, TP. Hồ Chí Minh',
            email VARCHAR(255) DEFAULT 'support@baodatsport.vn',
            bank_name VARCHAR(100) DEFAULT 'vietinbank',
            bank_account VARCHAR(100) DEFAULT '102873928192',
            bank_owner VARCHAR(100) DEFAULT 'CONG TY THE THAO BAO DAT',
            qr_code_url VARCHAR(255) DEFAULT '',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->conn->exec($sqlCreate);

        // 2. Chèn dòng cấu hình mặc định (id = 1) nếu bảng rỗng
        $sqlCheck = "SELECT COUNT(*) FROM cau_hinh_giao_dien WHERE id = 1";
        $count = $this->conn->query($sqlCheck)->fetchColumn();
        if ($count == 0) {
            $sqlSeed = "INSERT INTO cau_hinh_giao_dien (id, logo_url, zalo_link, facebook_link, sdt, dia_chi, email, bank_name, bank_account, bank_owner, qr_code_url) 
                        VALUES (1, 'assets/images/favicons/Logo.png', 'https://zalo.me/your_number', 'https://m.me/your_username', '0900 123 456', '123 Đường Thể Thao, TP. Hồ Chí Minh', 'support@baodatsport.vn', 'vietinbank', '102873928192', 'CONG TY THE THAO BAO DAT', '')";
            $this->conn->exec($sqlSeed);
        }
    }

    /**
     * Lấy dòng cấu hình hiện tại
     */
    public function getSetting()
    {
        $sql = "SELECT * FROM cau_hinh_giao_dien WHERE id = 1 LIMIT 1";
        $data = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        
        // Đề phòng trường hợp lỗi gì đó không lấy được
        if (!$data) {
            return [
                'logo_url' => 'assets/images/favicons/Logo.png',
                'zalo_link' => 'https://zalo.me/your_number',
                'facebook_link' => 'https://m.me/your_username',
                'sdt' => '0900 123 456',
                'dia_chi' => '123 Đường Thể Thao, TP. Hồ Chí Minh',
                'email' => 'support@baodatsport.vn',
                'bank_name' => 'vietinbank',
                'bank_account' => '102873928192',
                'bank_owner' => 'CONG TY THE THAO BAO DAT',
                'qr_code_url' => ''
            ];
        }
        return $data;
    }

    /**
     * Cập nhật cấu hình giao diện
     */
    public function updateSetting($data)
    {
        $sql = "UPDATE cau_hinh_giao_dien SET 
                    zalo_link = :zalo_link, 
                    facebook_link = :facebook_link, 
                    sdt = :sdt, 
                    dia_chi = :dia_chi, 
                    email = :email,
                    bank_name = :bank_name,
                    bank_account = :bank_account,
                    bank_owner = :bank_owner";
        
        $params = [
            'zalo_link' => $data['zalo_link'],
            'facebook_link' => $data['facebook_link'],
            'sdt' => $data['sdt'],
            'dia_chi' => $data['dia_chi'],
            'email' => $data['email'],
            'bank_name' => $data['bank_name'],
            'bank_account' => $data['bank_account'],
            'bank_owner' => $data['bank_owner']
        ];

        // Nếu cập nhật logo mới
        if (!empty($data['logo_url'])) {
            $sql .= ", logo_url = :logo_url";
            $params['logo_url'] = $data['logo_url'];
        }

        // Nếu cập nhật ảnh QR Code mới
        if (isset($data['qr_code_url'])) {
            $sql .= ", qr_code_url = :qr_code_url";
            $params['qr_code_url'] = $data['qr_code_url'];
        }

        $sql .= " WHERE id = 1";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
}
