<?php
namespace app\models\admin;
use app\core\Model;

class AdminNewsModel extends Model
{

    /**
     * Thêm bài viết mới vào Database
     */
    public function insertNews($tieuDe, $slug, $anhDaiDien, $tomTat, $noiDung, $maTacGia, $trangThai)
    {
        $sql = "INSERT INTO tin_tuc (tieu_de, duong_dan_slug, anh_dai_dien, tom_tat, noi_dung, ma_tac_gia, trang_thai) 
                VALUES (:tieu_de, :slug, :anh_dai_dien, :tom_tat, :noi_dung, :ma_tac_gia, :trang_thai)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'tieu_de' => $tieuDe,
            'slug' => $slug,
            'anh_dai_dien' => $anhDaiDien,
            'tom_tat' => $tomTat,
            'noi_dung' => $noiDung,
            'ma_tac_gia' => $maTacGia,
            'trang_thai' => $trangThai
        ]);
    }

    /**
     * Lấy danh sách tất cả tin tức cho trang Quản trị
     */
    public function getAllNews()
    {
        $sql = "SELECT t.id, t.tieu_de, t.anh_dai_dien, t.luot_xem, t.ngay_tao, t.trang_thai, 
                       COALESCE(n.ho_ten, 'Admin') AS tac_gia
                FROM tin_tuc t
                LEFT JOIN nguoi_dung n ON t.ma_tac_gia = n.id
                ORDER BY t.id DESC";
        return $this->conn->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thông tin chi tiết của 1 bài viết theo ID
     */
    public function getNewsById($id)
    {
        $sql = "SELECT * FROM tin_tuc WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật bài viết
     */
    public function updateNews($id, $tieuDe, $slug, $anhDaiDien, $tomTat, $noiDung, $trangThai)
    {
        $sql = "UPDATE tin_tuc SET 
                    tieu_de = :tieu_de, 
                    duong_dan_slug = :slug, 
                    tom_tat = :tom_tat, 
                    noi_dung = :noi_dung, 
                    trang_thai = :trang_thai";
        
        $params = [
            'id' => $id,
            'tieu_de' => $tieuDe,
            'slug' => $slug,
            'tom_tat' => $tomTat,
            'noi_dung' => $noiDung,
            'trang_thai' => $trangThai
        ];

        // Nếu có cập nhật ảnh đại diện mới thì mới đổi
        if (!empty($anhDaiDien)) {
            $sql .= ", anh_dai_dien = :anh_dai_dien";
            $params['anh_dai_dien'] = $anhDaiDien;
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Đổi trạng thái bài viết (Ẩn/Hiện)
     */
    public function toggleStatus($id)
    {
        $sql = "UPDATE tin_tuc SET trang_thai = 1 - trang_thai WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Xóa mềm bài viết (chuyển trạng thái về 0 hoặc ẩn đi theo yêu cầu)
     * Vì người dùng yêu cầu "Ẩn chứ không xóa", ta sẽ chuyển trạng thái = 0.
     */
    public function deleteNews($id)
    {
        $sql = "UPDATE tin_tuc SET trang_thai = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}