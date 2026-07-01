<?php

namespace app\models;

use app\core\Model;
use PDO;
use PDOException;
use Exception;
use app\models\entities\DonHang;
use app\models\entities\ChiTietDonHang;

class OrderModel extends Model
{
    public function getOrCreateUser($ho_ten, $email, $so_dien_thoai): int
    {
        // 1. Check if user already exists by email or phone
        $sql = "SELECT id FROM nguoi_dung WHERE so_dien_thoai = :phone OR (email = :email AND email IS NOT NULL) LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'phone' => $so_dien_thoai,
            'email' => !empty($email) ? $email : null
        ]);
        $row = $stmt->fetch();
        if ($row) {
            return (int) $row['id'];
        }

        // 2. Auto-create guest user
        $sqlInsert = "INSERT INTO nguoi_dung (ho_ten, email, mat_khau, so_dien_thoai, vai_tro, trang_thai) 
                      VALUES (:name, :email, :password, :phone, 'khach_hang', 1)";
        $stmtInsert = $this->conn->prepare($sqlInsert);

        $guestEmail = !empty($email) ? $email : 'guest_' . time() . '_' . rand(100, 999) . '@sportpro.vn';
        $dummyPassword = password_hash('guest_sportpro_123', PASSWORD_DEFAULT);

        $stmtInsert->execute([
            'name' => $ho_ten,
            'email' => $guestEmail,
            'password' => $dummyPassword,
            'phone' => $so_dien_thoai
        ]);

        $insertedId = (int) $this->conn->lastInsertId();
        if ($insertedId <= 0) {
            $insertedId = (int) $this->conn->query("SELECT LAST_INSERT_ID()")->fetchColumn();
        }
        return $insertedId;
    }

    public function getAvailableCoupons(int $userId, float $totalPayment): array
    {
        $sqlUser = "SELECT nd.ma_hang, ht.muc_chi_tieu_toi_thieu 
                    FROM nguoi_dung nd 
                    LEFT JOIN hang_thanh_vien ht ON nd.ma_hang = ht.id 
                    WHERE nd.id = :id";
        $stmtUser = $this->conn->prepare($sqlUser);
        $stmtUser->execute(['id' => $userId]);
        $userRankInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if ($userRankInfo && $totalPayment > 0) {
            $userTieuThieu = $userRankInfo['muc_chi_tieu_toi_thieu'] ?? 0;

            $sqlCoupon = "SELECT m.* FROM ma_giam_gia m
                          LEFT JOIN hang_thanh_vien ht ON m.ma_hang = ht.id
                          WHERE (m.ma_hang IS NULL OR m.ma_hang = 0 OR ht.muc_chi_tieu_toi_thieu <= :user_tieu_thieu)
                          AND m.don_hang_toi_thieu <= :total
                          AND m.trang_thai = 1 
                          AND m.so_luong_da_dung < m.tong_so_luong
                          AND m.ngay_bat_dau <= NOW() 
                          AND m.ngay_ket_thuc >= NOW()
                          ORDER BY m.gia_tri_giam DESC";
            $stmtCoupon = $this->conn->prepare($sqlCoupon);
            $stmtCoupon->execute([
                'user_tieu_thieu' => $userTieuThieu,
                'total' => $totalPayment
            ]);
            return $stmtCoupon->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
        return [];
    }

    public function validateCoupon(string $code, int $userId, float $totalPayment)
    {
        $sqlUser = "SELECT nd.ma_hang, ht.muc_chi_tieu_toi_thieu 
                    FROM nguoi_dung nd 
                    LEFT JOIN hang_thanh_vien ht ON nd.ma_hang = ht.id 
                    WHERE nd.id = :id";
        $stmtUser = $this->conn->prepare($sqlUser);
        $stmtUser->execute(['id' => $userId]);
        $userRankInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if ($userRankInfo && $totalPayment > 0) {
            $userTieuThieu = $userRankInfo['muc_chi_tieu_toi_thieu'] ?? 0;

            $sqlCoupon = "SELECT m.* FROM ma_giam_gia m
                          LEFT JOIN hang_thanh_vien ht ON m.ma_hang = ht.id
                          WHERE (m.ma_hang IS NULL OR m.ma_hang = 0 OR ht.muc_chi_tieu_toi_thieu <= :user_tieu_thieu)
                          AND m.ma_code = :code
                          AND m.don_hang_toi_thieu <= :total
                          AND m.trang_thai = 1 
                          AND m.so_luong_da_dung < m.tong_so_luong
                          AND m.ngay_bat_dau <= NOW() 
                          AND m.ngay_ket_thuc >= NOW()
                          LIMIT 1";
            $stmtCoupon = $this->conn->prepare($sqlCoupon);
            $stmtCoupon->execute([
                'user_tieu_thieu' => $userTieuThieu,
                'code' => $code,
                'total' => $totalPayment
            ]);
            return $stmtCoupon->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function placeOrder(DonHang $order, $cartItems, string $couponCode = ''): string
    {
        try {
            $this->conn->beginTransaction();
            $this->conn->exec("SET FOREIGN_KEY_CHECKS = 0");

            // Kiểm tra tồn kho thực tế trước khi đặt hàng để tránh mua quá số lượng tồn
            foreach ($cartItems as $item) {
                $tableName = !empty($item['variation_id']) ? "bien_the_san_pham" : "san_pham";
                $targetId = !empty($item['variation_id']) ? $item['variation_id'] : $item['product_id'];

                $stmtCheck = $this->conn->prepare("SELECT so_luong_ton FROM {$tableName} WHERE id = ?");
                $stmtCheck->execute([$targetId]);
                $realStock = $stmtCheck->fetchColumn();

                if ($realStock !== false && $item['qty'] > (int) $realStock) {
                    throw new \Exception('Sản phẩm "' . $item['name'] . '" (hoặc biến thể của nó) chỉ còn lại ' . $realStock . ' sản phẩm trong kho. Vui lòng điều chỉnh lại số lượng trong giỏ hàng!');
                }
            }

            // 1. Get or create user ID if not explicitly set
            $userId = $order->getMa_nguoi_dung();
            if ($userId <= 0) {
                $userId = $this->getOrCreateUser(
                    $order->getHo_ten_nguoi_nhan(),
                    $order->getEmail(),
                    $order->getSo_dien_thoai()
                );
                $order->setMa_nguoi_dung($userId);
            }

            // 2. Generate unique order code
            $orderCode = 'DH-' . strtoupper(dechex(time())) . rand(10, 99);
            $order->setMa_don_hang($orderCode);

            // 3. Insert into don_hang
            $sqlOrder = "INSERT INTO don_hang (
                            ma_nguoi_dung, ma_don_hang, ho_ten_nguoi_nhan, so_dien_thoai, email, 
                            dia_chi_giao_hang, ghi_chu, tong_tien_hang, phi_van_chuyen, tien_giam_gia, 
                            tong_thanh_toan, phuong_thuc_thanh_toan, trang_thai_thanh_toan, trang_thai_don_hang
                         ) VALUES (
                            :user_id, :order_code, :recipient_name, :phone, :email, 
                            :address, :notes, :subtotal, :shipping, :discount, 
                            :total, :payment_method, :payment_status, :order_status
                         )";

            $stmtOrder = $this->conn->prepare($sqlOrder);
            $stmtOrder->execute([
                'user_id' => $order->getMa_nguoi_dung(),
                'order_code' => $order->getMa_don_hang(),
                'recipient_name' => $order->getHo_ten_nguoi_nhan(),
                'phone' => $order->getSo_dien_thoai(),
                'email' => $order->getEmail(),
                'address' => $order->getDia_chi_giao_hang(),
                'notes' => $order->getGhi_chu(),
                'subtotal' => $order->getTong_tien_hang(),
                'shipping' => $order->getPhi_van_chuyen(),
                'discount' => $order->getTien_giam_gia(),
                'total' => $order->getTong_thanh_toan(),
                'payment_method' => $order->getPhuong_thuc_thanh_toan(),
                'payment_status' => $order->getTrang_thai_thanh_toan(),
                'order_status' => $order->getTrang_thai_don_hang()
            ]);

            $orderId = (int) $this->conn->lastInsertId();
            if ($orderId <= 0) {
                $orderId = (int) $this->conn->query("SELECT LAST_INSERT_ID()")->fetchColumn();
            }
            $order->setId($orderId);

            // 4. Insert into chi_tiet_don_hang and deduct stock
            foreach ($cartItems as $item) {
                $subtotalItem = $item['price'] * $item['qty'];

                $sqlItem = "INSERT INTO chi_tiet_don_hang (
                                ma_don_hang, ma_san_pham, ma_bien_the, ten_san_pham, 
                                thong_tin_bien_the, anh_dai_dien, gia_mua, so_luong, thanh_tien
                            ) VALUES (
                                :order_id, :product_id, :variation_id, :product_name, 
                                :variation_info, :image, :price, :qty, :thanh_tien
                            )";

                $stmtItem = $this->conn->prepare($sqlItem);
                $stmtItem->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'variation_id' => !empty($item['variation_id']) ? $item['variation_id'] : null,
                    'product_name' => $item['name'],
                    'variation_info' => !empty($item['attributes']) ? $item['attributes'] : null,
                    'image' => $item['image'],
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'thanh_tien' => $subtotalItem
                ]);

                // Deduct stock levels immediately
                if (!empty($item['variation_id'])) {
                    $stmtTruVar = $this->conn->prepare(
                        "UPDATE bien_the_san_pham SET so_luong_ton = so_luong_ton - ? WHERE id = ?"
                    );
                    $stmtTruVar->execute([(int) $item['qty'], $item['variation_id']]);
                }

                if (!empty($item['product_id'])) {
                    $stmtTruProd = $this->conn->prepare(
                        "UPDATE san_pham SET so_luong_ton = so_luong_ton - ? WHERE id = ?"
                    );
                    $stmtTruProd->execute([(int) $item['qty'], $item['product_id']]);
                }
            }

            // Update user total spent and rank
            if ($userId > 0) {
                $sqlUpdateUser = "UPDATE nguoi_dung SET tong_chi_tieu = tong_chi_tieu + :total WHERE id = :uid";
                $stmtUpdateUser = $this->conn->prepare($sqlUpdateUser);
                $stmtUpdateUser->execute([
                    'total' => $order->getTong_thanh_toan(),
                    'uid' => $userId
                ]);

                // Cập nhật ma_hang dựa theo muc_chi_tieu_toi_thieu
                $sqlUpdateRank = "UPDATE nguoi_dung 
                                  SET ma_hang = (
                                      SELECT id 
                                      FROM hang_thanh_vien 
                                      WHERE muc_chi_tieu_toi_thieu <= nguoi_dung.tong_chi_tieu 
                                      ORDER BY muc_chi_tieu_toi_thieu DESC 
                                      LIMIT 1
                                  ) 
                                  WHERE id = :uid";
                $stmtUpdateRank = $this->conn->prepare($sqlUpdateRank);
                $stmtUpdateRank->execute(['uid' => $userId]);

                // Update session if the current user is placing the order
                if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $userId) {
                    // Fetch the updated rank info to update session
                    $sqlGetRank = "SELECT ht.ten_hang, ht.mau_sac, ht.bieu_tuong 
                                   FROM nguoi_dung nd
                                   LEFT JOIN hang_thanh_vien ht ON nd.ma_hang = ht.id 
                                   WHERE nd.id = :uid";
                    $stmtGetRank = $this->conn->prepare($sqlGetRank);
                    $stmtGetRank->execute(['uid' => $userId]);
                    if ($rowRank = $stmtGetRank->fetch()) {
                        $_SESSION['user']['hang_khach_hang'] = [
                            'ten_hang' => $rowRank['ten_hang'],
                            'mau_sac' => $rowRank['mau_sac'],
                            'bieu_tuong' => $rowRank['bieu_tuong']
                        ];
                    }
                }
            }

            $this->conn->exec("SET FOREIGN_KEY_CHECKS = 1");
            if (!empty($couponCode)) {
                $sqlUpdateCoupon = "UPDATE ma_giam_gia SET so_luong_da_dung = so_luong_da_dung + 1 WHERE ma_code = :code";
                $this->conn->prepare($sqlUpdateCoupon)->execute(['code' => $couponCode]);
            }

            $this->conn->commit();
            return $orderCode;

        } catch (Exception $e) {
            $this->conn->exec("SET FOREIGN_KEY_CHECKS = 1");
            $this->conn->rollBack();
            throw new Exception($e->getMessage() . " (Debug: orderId = " . ($orderId ?? 'unset') . ")");
        }
    }

    public function getOrderDetails($ma_don_hang): ?array
    {
        $sql = "SELECT * FROM don_hang WHERE ma_don_hang = :code LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['code' => $ma_don_hang]);
        $orderRow = $stmt->fetch();
        if (!$orderRow) {
            return null;
        }

        $orderEntity = new DonHang($orderRow);

        $sqlItems = "SELECT * FROM chi_tiet_don_hang WHERE ma_don_hang = :id";
        $stmtItems = $this->conn->prepare($sqlItems);
        $stmtItems->execute(['id' => $orderEntity->getId()]);
        $itemRows = $stmtItems->fetchAll() ?: [];

        $itemEntities = [];
        foreach ($itemRows as $row) {
            $itemEntities[] = new ChiTietDonHang($row);
        }

        return [
            'order' => $orderEntity,
            'items' => $itemEntities
        ];
    }

    public function trackOrder($phoneOrOrderId): array
    {
        $term = trim($phoneOrOrderId);
        $sql = "SELECT * FROM don_hang 
                WHERE ma_don_hang = :term OR so_dien_thoai = :term 
                ORDER BY ngay_tao DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['term' => $term]);
        $rows = $stmt->fetchAll() ?: [];

        $orderEntities = [];
        foreach ($rows as $row) {
            $orderEntities[] = new DonHang($row);
        }
        return $orderEntities;
    }

    /**
     * Lấy toàn bộ đơn hàng của một user theo ID, tuỳ chọn lọc theo trạng thái
     */
    public function getOrdersByUser(int $userId, string $trangThai = ''): array
    {
        $sql = "SELECT * FROM don_hang WHERE ma_nguoi_dung = :uid";
        $params = ['uid' => $userId];

        if (!empty($trangThai)) {
            $sql .= " AND trang_thai_don_hang = :status";
            $params['status'] = $trangThai;
        }

        $sql .= " ORDER BY ngay_tao DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll() ?: [];

        $entities = [];
        foreach ($rows as $row) {
            $entities[] = new DonHang($row);
        }
        return $entities;
    }

    /**
     * Đếm số đơn hàng theo từng trạng thái của một user
     */
    public function countOrdersByStatus(int $userId): array
    {
        $sql = "SELECT trang_thai_don_hang, COUNT(*) as total 
                FROM don_hang 
                WHERE ma_nguoi_dung = :uid 
                GROUP BY trang_thai_don_hang";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        $rows = $stmt->fetchAll() ?: [];

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['trang_thai_don_hang']] = (int) $row['total'];
        }
        return $counts;
    }

    /**
     * Hủy đơn hàng bởi khách hàng và hoàn lại tồn kho sản phẩm
     */
    public function cancelOrder(int $userId, string $orderCode): bool
    {
        $details = $this->getOrderDetails($orderCode);
        if (!$details) {
            throw new Exception("Đơn hàng không tồn tại.");
        }

        $order = $details['order'];
        $items = $details['items'];

        if ($order->getMa_nguoi_dung() !== $userId) {
            throw new Exception("Bạn không có quyền hủy đơn hàng này.");
        }

        $currentStatus = $order->getTrang_thai_don_hang();
        if ($currentStatus !== 'cho_xac_nhan') {
            if ($currentStatus === 'dang_xu_ly') {
                throw new Exception("Đơn hàng đang được xử lý, không thể hủy.");
            } elseif ($currentStatus === 'da_huy') {
                throw new Exception("Đơn hàng đã được hủy trước đó.");
            } else {
                throw new Exception("Đơn hàng không thể hủy ở trạng thái hiện tại.");
            }
        }

        try {
            $this->conn->beginTransaction();

            // 1. Cập nhật trạng thái đơn hàng thành 'da_huy'
            $sql = "UPDATE don_hang SET trang_thai_don_hang = 'da_huy' WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $order->getId()]);

            // 2. Hoàn lại tồn kho cho sản phẩm và biến thể
            foreach ($items as $item) {
                $prodId = $item->getMa_san_pham();
                $varId = $item->getMa_bien_the();
                $qty = (int) $item->getSo_luong();

                if (!empty($varId)) {
                    $stmtVar = $this->conn->prepare(
                        "UPDATE bien_the_san_pham SET so_luong_ton = so_luong_ton + ? WHERE id = ?"
                    );
                    $stmtVar->execute([$qty, $varId]);
                }

                if (!empty($prodId)) {
                    $stmtProd = $this->conn->prepare(
                        "UPDATE san_pham SET so_luong_ton = so_luong_ton + ? WHERE id = ?"
                    );
                    $stmtProd->execute([$qty, $prodId]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Xác nhận đã nhận hàng
     */
    public function confirmReceived(int $userId, string $orderCode): bool
    {
        $sql = "SELECT id, ma_nguoi_dung, trang_thai_don_hang FROM don_hang WHERE ma_don_hang = :code LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['code' => $orderCode]);
        $order = $stmt->fetch();
        if (!$order) {
            throw new Exception("Đơn hàng không tồn tại.");
        }
        if ((int) $order['ma_nguoi_dung'] !== $userId) {
            throw new Exception("Bạn không có quyền xác nhận đơn hàng này.");
        }
        if ($order['trang_thai_don_hang'] !== 'dang_giao') {
            throw new Exception("Đơn hàng phải ở trạng thái đang giao mới có thể xác nhận đã nhận hàng.");
        }

        $sqlUp = "UPDATE don_hang SET trang_thai_don_hang = 'hoan_thanh' WHERE id = :id";
        $stmtUp = $this->conn->prepare($sqlUp);
        return $stmtUp->execute(['id' => $order['id']]);
    }

    /**
     * Kiểm tra xem khách hàng đã đánh giá sản phẩm này chưa
     */
    public function hasReviewedProduct(int $userId, int $productId): bool
    {
        $sql = "SELECT COUNT(*) FROM danh_gia_san_pham WHERE ma_nguoi_dung = :uid AND ma_san_pham = :pid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['uid' => $userId, 'pid' => $productId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Lưu đánh giá sản phẩm mới
     */
    public function submitProductReview(int $userId, int $productId, int $diemSo, string $binhLuan): bool
    {
        // Kiểm tra xem khách hàng có thực sự đã mua sản phẩm này trong một đơn hàng hoàn thành hay chưa
        $sqlCheckBuy = "SELECT COUNT(*) 
                        FROM chi_tiet_don_hang ctdh
                        JOIN don_hang dh ON ctdh.ma_don_hang = dh.id
                        WHERE dh.ma_nguoi_dung = :uid 
                          AND ctdh.ma_san_pham = :pid 
                          AND dh.trang_thai_don_hang = 'hoan_thanh'";
        $stmtCheck = $this->conn->prepare($sqlCheckBuy);
        $stmtCheck->execute(['uid' => $userId, 'pid' => $productId]);
        if ((int) $stmtCheck->fetchColumn() <= 0) {
            throw new Exception("Bạn chỉ có thể đánh giá những sản phẩm đã mua và giao thành công.");
        }

        // Kiểm tra xem đã đánh giá chưa
        if ($this->hasReviewedProduct($userId, $productId)) {
            throw new Exception("Bạn đã đánh giá sản phẩm này rồi.");
        }

        $sql = "INSERT INTO danh_gia_san_pham (ma_nguoi_dung, ma_san_pham, diem_so, binh_luan, trang_thai, ngay_tao)
                VALUES (:uid, :pid, :score, :comment, 1, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'uid' => $userId,
            'pid' => $productId,
            'score' => $diemSo,
            'comment' => !empty($binhLuan) ? $binhLuan : null
        ]);
    }
}
