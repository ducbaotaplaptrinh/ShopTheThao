<?php
namespace app\controllers\admin;

use app\models\admin\AdminNewsModel;
use app\services\CloudService;

class AdminNewsController
{
    private $newsModel;

    public function __construct()
    {
        $this->newsModel = new AdminNewsModel();
    }

    // 1. Hiển thị form Thêm bài viết
    public function create()
    {
        // Trả về mảng rỗng vì view và title đã được cấu hình trong file routes/web.php
        return [];
    }

    // 2. Xử lý lưu bài viết
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tieuDe = isset($_POST['tieu_de']) ? trim($_POST['tieu_de']) : '';
            $tomTat = isset($_POST['tom_tat']) ? trim($_POST['tom_tat']) : '';
            $noiDung = isset($_POST['noi_dung']) ? trim($_POST['noi_dung']) : '';
            $trangThai = isset($_POST['trang_thai']) ? 1 : 0;

            // Lấy ID admin đang đăng nhập (hoặc mặc định là 1 nếu chưa đăng nhập)
            $maTacGia = $_SESSION['user']['id'] ?? 1;

            // Tạo slug tự động từ Tiêu đề
            $slug = $this->createSlug($tieuDe);

            $urlAnhDaiDien = "";

            // Xử lý upload ảnh lên Cloudinary
            if (isset($_FILES['anh_dai_dien']) && $_FILES['anh_dai_dien']['error'] === UPLOAD_ERR_OK) {
                $uploadedUrl = CloudService::uploadImage($_FILES['anh_dai_dien']);
                if ($uploadedUrl) {
                    $urlAnhDaiDien = $uploadedUrl;
                }
            }

            // Gọi Model để lưu vào Database
            $isSuccess = $this->newsModel->insertNews($tieuDe, $slug, $urlAnhDaiDien, $tomTat, $noiDung, $maTacGia, $trangThai);

            if ($isSuccess) {
                // Chuyển hướng về trang danh sách (Lưu ý: Bạn có thể đổi lại thành ?page=admin-news nếu đã tạo route đó)
                header("Location: ?page=admin-news-create&msg=success");
                exit;
            } else {
                // Xử lý thông báo lỗi (Bạn có thể dùng session để hiển thị trên view)
                $_SESSION['error'] = "Có lỗi xảy ra khi lưu bài viết!";
                header("Location: ?page=admin-news-create");
                exit;
            }
        }
    }
    // 3. Hiển thị danh sách tin tức
    public function index()
    {
        $newsList = $this->newsModel->getAllNews();
        return [
            'newsList' => $newsList
        ];
    }

    // 4. Hiển thị form sửa bài viết
    public function edit()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $news = $this->newsModel->getNewsById($id);

        if (!$news) {
            header("Location: ?page=admin-news");
            exit;
        }

        return [
            'news' => $news
        ];
    }

    // 5. Xử lý cập nhật bài viết
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $tieuDe = isset($_POST['tieu_de']) ? trim($_POST['tieu_de']) : '';
            $tomTat = isset($_POST['tom_tat']) ? trim($_POST['tom_tat']) : '';
            $noiDung = isset($_POST['noi_dung']) ? trim($_POST['noi_dung']) : '';
            $trangThai = isset($_POST['trang_thai']) ? 1 : 0;

            $slug = $this->createSlug($tieuDe);
            $urlAnhDaiDien = "";

            if (isset($_FILES['anh_dai_dien']) && $_FILES['anh_dai_dien']['error'] === UPLOAD_ERR_OK) {
                $uploadedUrl = CloudService::uploadImage($_FILES['anh_dai_dien']);
                if ($uploadedUrl) {
                    $urlAnhDaiDien = $uploadedUrl;
                }
            }

            $this->newsModel->updateNews($id, $tieuDe, $slug, $urlAnhDaiDien, $tomTat, $noiDung, $trangThai);
            
            header("Location: ?page=admin-news&msg=updated");
            exit;
        }
    }

    // 6. Xóa mềm (chuyển trạng thái về ẩn)
    public function delete()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $this->newsModel->deleteNews($id);
        }
        header("Location: ?page=admin-news&msg=deleted");
        exit;
    }

    // 7. Đổi trạng thái Ẩn/Hiện
    public function toggleStatus()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $this->newsModel->toggleStatus($id);
        }
        header("Location: ?page=admin-news");
        exit;
    }

    // Hàm hỗ trợ tạo đường dẫn thân thiện (Slug)
    private function createSlug($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9\-]/', '-', $str);
        $str = preg_replace('/-+/', '-', $str);
        return trim($str, '-');
    }
}