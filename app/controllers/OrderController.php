<?php

namespace app\controllers;

use app\models\OrderModel;
use app\models\NguoiDungModel;
use app\models\entities\DonHang;

class OrderController
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    public function checkout(): ?array
    {
        // Enforce login for checkout
        if (!isset($_SESSION['user'])) {
            header("Location: ?page=login&redirect=checkout");
            exit;
        }

        $fullCart = $_SESSION['cart'] ?? [];
        if (empty($fullCart)) {
            header("Location: ?page=cart");
            exit;
        }

        if (isset($_GET['buy_now'])) {
            $_SESSION['carts_checked'] = [$_GET['buy_now']];
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['selected_items']) && is_array($_POST['selected_items'])) {
                $_SESSION['carts_checked'] = $_POST['selected_items'];
            } else {
                $_SESSION['cart_warning'] = 'Vui lòng chọn ít nhất một sản phẩm để tiến hành đặt hàng.';
                header("Location: ?page=cart");
                exit;
            }
        }

        $cartsChecked = $_SESSION['carts_checked'] ?? [];
        if (empty($cartsChecked)) {
            $cartsChecked = array_keys($fullCart);
            $_SESSION['carts_checked'] = $cartsChecked;
        }

        $cartItems = [];
        foreach ($cartsChecked as $key) {
            if (isset($fullCart[$key])) {
                $cartItems[$key] = $fullCart[$key];
            }
        }

        if (empty($cartItems)) {
            header("Location: ?page=cart");
            exit;
        }

        $totalPayment = 0;
        foreach ($cartItems as $item) {
            $totalPayment += $item['price'] * $item['qty'];
        }

        $userId = (int)$_SESSION['user']['id'];
        $nguoiDungModel = new NguoiDungModel();
        $addresses = $nguoiDungModel->getUserAddresses($userId);
        $availableCoupons = $this->orderModel->getAvailableCoupons($userId, $totalPayment);
        $bestCoupon = !empty($availableCoupons) ? $availableCoupons[0] : null;

        return [
            'title' => 'Thanh toán đơn hàng | Bảo Đạt Sport',
            'cartItems' => $cartItems,
            'totalPayment' => $totalPayment,
            'addresses' => $addresses,
            'availableCoupons' => $availableCoupons,
            'bestCoupon' => $bestCoupon
        ];
    }

    public function place(): void
    {
        // Enforce login for placing orders
        if (!isset($_SESSION['user'])) {
            header("Location: ?page=login&redirect=checkout");
            exit;
        }

        $fullCart = $_SESSION['cart'] ?? [];
        if (empty($fullCart)) {
            header("Location: ?page=cart");
            exit;
        }

        $cartsChecked = $_SESSION['carts_checked'] ?? [];
        if (empty($cartsChecked)) {
            $cartsChecked = array_keys($fullCart);
        }

        $cartItems = [];
        foreach ($cartsChecked as $key) {
            if (isset($fullCart[$key])) {
                $cartItems[$key] = $fullCart[$key];
            }
        }

        if (empty($cartItems)) {
            header("Location: ?page=cart");
            exit;
        }

        $ho_ten = isset($_POST['ho_ten']) ? trim($_POST['ho_ten']) : '';
        $sdt = isset($_POST['so_dien_thoai']) ? trim($_POST['so_dien_thoai']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $dia_chi = isset($_POST['dia_chi']) ? trim($_POST['dia_chi']) : '';
        $ghi_chu = isset($_POST['ghi_chu']) ? trim($_POST['ghi_chu']) : '';
        $payment = isset($_POST['phuong_thuc_thanh_toan']) ? trim($_POST['phuong_thuc_thanh_toan']) : 'cod';

        // Validation
        if (empty($ho_ten) || empty($sdt) || empty($dia_chi)) {
            $_SESSION['order_error'] = 'Vui lòng điền đầy đủ các thông tin bắt buộc (*).';
            header("Location: ?page=checkout");
            exit;
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }
        $ma_code_su_dung = isset($_POST['ma_code_su_dung']) ? trim($_POST['ma_code_su_dung']) : '';
        $shipping = 0.00; // Free shipping
        $discount = 0.00;

        if (!empty($ma_code_su_dung)) {
            $coupon = $this->orderModel->validateCoupon($ma_code_su_dung, (int)$_SESSION['user']['id'], (float)$subtotal);
            if ($coupon) {
                if ($coupon['loai_giam_gia'] === 'phan_tram') {
                    $discount = ($subtotal * (float)$coupon['gia_tri_giam']) / 100;
                    if (!empty($coupon['muc_giam_toi_da']) && (float)$coupon['muc_giam_toi_da'] > 0) {
                        $maxDiscount = (float)$coupon['muc_giam_toi_da'];
                        if ($discount > $maxDiscount) {
                            $discount = $maxDiscount;
                        }
                    }
                } else {
                    $discount = (float)$coupon['gia_tri_giam'];
                }
            } else {
                $_SESSION['order_error'] = 'Mã giảm giá không hợp lệ hoặc không đủ điều kiện!';
                header("Location: ?page=checkout");
                exit;
            }
        }

        $total = $subtotal + $shipping - $discount;
        if ($total < 0) $total = 0;

        // Construct DonHang Entity
        $order = new DonHang();
        $order->setMa_nguoi_dung((int)$_SESSION['user']['id']);
        $order->setHo_ten_nguoi_nhan($ho_ten);
        $order->setSo_dien_thoai($sdt);
        $order->setEmail(!empty($email) ? $email : null);
        $order->setDia_chi_giao_hang($dia_chi);
        $order->setGhi_chu(!empty($ghi_chu) ? $ghi_chu : null);
        $order->setTong_tien_hang($subtotal);
        $order->setPhi_van_chuyen($shipping);
        $order->setTien_giam_gia($discount);
        $order->setTong_thanh_toan($total);
        $order->setPhuong_thuc_thanh_toan($payment);
        $order->setTrang_thai_thanh_toan($payment === 'cod' ? 'chua_thanh_toan' : 'da_thanh_toan');
        $order->setTrang_thai_don_hang('cho_xac_nhan');

        try {
            $orderCode = $this->orderModel->placeOrder($order, $cartItems, $ma_code_su_dung);
            // Gửi email hóa đơn cho khách hàng nếu có địa chỉ email
            $targetEmail = !empty($email) ? $email : ($_SESSION['user']['email'] ?? '');
            if (!empty($targetEmail)) {
                $orderDetails = $this->orderModel->getOrderDetails($orderCode);
                if ($orderDetails) {
                    \app\services\MailService::sendOrderInvoice(
                        $targetEmail,
                        $order->getHo_ten_nguoi_nhan(),
                        $orderDetails['order'],
                        $orderDetails['items']
                    );
                }
            }

            // Remove only the checked items from $_SESSION['cart'] instead of clearing all!
            foreach ($cartsChecked as $key) {
                unset($_SESSION['cart'][$key]);
            }
            unset($_SESSION['carts_checked']);

            header("Location: ?page=order-success&code=" . $orderCode);
            exit;
        } catch (\Exception $e) {
            $_SESSION['order_error'] = 'Đã xảy ra lỗi trong quá trình đặt hàng: ' . $e->getMessage();
            header("Location: ?page=checkout");
            exit;
        }
    }

    public function success(): ?array
    {
        $code = $_GET['code'] ?? '';
        if (empty($code)) {
            header("Location: ?page=home");
            exit;
        }

        $details = $this->orderModel->getOrderDetails($code);
        if (!$details) {
            header("Location: ?page=home");
            exit;
        }

        return [
            'title' => 'Đặt hàng thành công | Bảo Đạt Sport',
            'order' => $details['order'],
            'items' => $details['items']
        ];
    }

    public function track(): array
    {
        $term = $_GET['term'] ?? '';
        $orders = [];
        if (!empty($term)) {
            $orders = $this->orderModel->trackOrder($term);
        }

        return [
            'title' => 'Tra cứu đơn hàng | Bảo Đạt Sport',
            'term' => $term,
            'orders' => $orders
        ];
    }

    public function myOrders(): array
    {
        // Bắt buộc phải đăng nhập
        if (!isset($_SESSION['user'])) {
            header("Location: ?page=login&redirect=my-orders");
            exit;
        }

        $userId    = (int)$_SESSION['user']['id'];
        $activeTab = $_GET['status'] ?? 'all';

        // Chỉ cho phép các giá trị hợp lệ — đồng bộ với bộ trạng thái admin
        $validStatuses = ['all', 'cho_xac_nhan', 'dang_xu_ly', 'dang_giao', 'hoan_thanh', 'da_huy'];
        if (!in_array($activeTab, $validStatuses)) {
            $activeTab = 'all';
        }

        $filterStatus = $activeTab === 'all' ? '' : $activeTab;
        $orders       = $this->orderModel->getOrdersByUser($userId, $filterStatus);
        $statusCounts = $this->orderModel->countOrdersByStatus($userId);

        $error = $_SESSION['order_error'] ?? '';
        $success = $_SESSION['order_success'] ?? '';
        unset($_SESSION['order_error'], $_SESSION['order_success']);

        return [
            'title'        => 'Đơn hàng của tôi | Bảo Đạt Sport',
            'orders'       => $orders,
            'statusCounts' => $statusCounts,
            'activeTab'    => $activeTab,
            'error'        => $error,
            'success'      => $success,
        ];
    }

    public function cancel(): void
    {
        // Bắt buộc phải đăng nhập
        if (!isset($_SESSION['user'])) {
            header("Location: ?page=login");
            exit;
        }

        $userId = (int)$_SESSION['user']['id'];
        $code = $_GET['code'] ?? '';

        if (empty($code)) {
            $_SESSION['order_error'] = 'Mã đơn hàng không hợp lệ.';
            header("Location: ?page=my-orders");
            exit;
        }

        try {
            $this->orderModel->cancelOrder($userId, $code);
            $_SESSION['order_success'] = 'Hủy đơn hàng thành công.';
        } catch (\Exception $e) {
            $_SESSION['order_error'] = 'Không thể hủy đơn hàng: ' . $e->getMessage();
        }

        header("Location: ?page=my-orders");
        exit;
    }

    /**
     * Khách hàng xác nhận đã nhận hàng
     */
    public function confirmReceived(): void
    {
        // Bắt buộc phải đăng nhập
        if (!isset($_SESSION['user'])) {
            header("Location: ?page=login");
            exit;
        }

        $userId = (int)$_SESSION['user']['id'];
        $code = $_GET['code'] ?? '';

        if (empty($code)) {
            $_SESSION['order_error'] = 'Mã đơn hàng không hợp lệ.';
            header("Location: ?page=my-orders");
            exit;
        }

        try {
            $this->orderModel->confirmReceived($userId, $code);
            $_SESSION['order_success'] = 'Xác nhận đã nhận hàng thành công. Bạn có thể đánh giá sản phẩm ngay bây giờ.';
        } catch (\Exception $e) {
            $_SESSION['order_error'] = 'Không thể xác nhận nhận hàng: ' . $e->getMessage();
        }

        header("Location: ?page=my-orders");
        exit;
    }

    /**
     * Lưu đánh giá từ biểu mẫu gửi lên
     */
    public function submitReview(): void
    {
        // Bắt buộc phải đăng nhập
        if (!isset($_SESSION['user'])) {
            header("Location: ?page=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?page=my-orders");
            exit;
        }

        $userId = (int)$_SESSION['user']['id'];
        $reviews = $_POST['reviews'] ?? [];
        $orderId = isset($_POST['ma_don_hang']) ? (int)$_POST['ma_don_hang'] : null;

        if (empty($reviews) || !is_array($reviews)) {
            $_SESSION['order_error'] = 'Dữ liệu đánh giá không hợp lệ.';
            header("Location: ?page=my-orders");
            exit;
        }

        $successCount = 0;
        $errorMsg = '';

        foreach ($reviews as $productId => $reviewData) {
            $productId = (int)$productId;
            $diemSo = isset($reviewData['diem_so']) ? (int)$reviewData['diem_so'] : 5;
            $binhLuan = isset($reviewData['binh_luan']) ? trim($reviewData['binh_luan']) : '';

            // Kiểm tra điểm số hợp lệ
            if ($diemSo < 1 || $diemSo > 5) {
                $diemSo = 5;
            }

            try {
                $this->orderModel->submitProductReview($userId, $productId, $diemSo, $binhLuan, $orderId);
                $successCount++;
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
            }
        }

        if ($successCount > 0) {
            $_SESSION['order_success'] = 'Gửi đánh giá thành công cho ' . $successCount . ' sản phẩm.';
        } else {
            $_SESSION['order_error'] = 'Không thể gửi đánh giá: ' . $errorMsg;
        }

        header("Location: ?page=my-orders");
        exit;
    }
}
