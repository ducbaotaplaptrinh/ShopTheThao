<?php

declare(strict_types=1);

use app\models\SanPhamModel;

if (!defined('BASE_PATH')) {
	define('BASE_PATH', dirname(__DIR__, 2));
}
class App
{
	public function run(): void
	{
		$routes = require BASE_PATH . '/routes/web.php';
		$page = $_GET['page'] ?? 'home';
		$route = $routes[$page] ?? $routes['404'];

		$view = BASE_PATH . '/app/views/' . $route['view'];
		$routeData = $route['data'] ?? [];
		$danhMucModel = new SanPhamModel();
		require_once BASE_PATH . '/app/models/admin/AdminSettingModel.php';
		$settingModel = new \app\models\admin\AdminSettingModel();
		$cauhinh = $settingModel->getSetting();

		$chung = [
			'megaMenu' => $danhMucModel->getDanhMucThuongHieu(), // Tự động nạp menu cho tất cả các trang
			'cauhinh' => $cauhinh,
		];

		$controllerData = [];
		if (isset($route['controller']) && isset($route['action'])) {
			$controllerClass = $route['controller'];
			//dd($controllerClass);
			$action = $route['action'];
			if (class_exists($controllerClass)) {
				$controllerInstance = new $controllerClass();

				if (method_exists($controllerInstance, $action)) {
					$result = $controllerInstance->$action();

					if (is_array($result)) {
						$controllerData = $result;
					}
				}
			}
		}
		$data = array_merge($route, $routeData, $chung, $controllerData);
		$title = $data['title']  ?? 'Bảo Đạt Sport';
		$this->render($view, $data, $title);
	}

	private function render(string $view, array $viewParams = [], string $title = 'Bảo Đạt Sport'): void
	{
		// dd($view);

		extract($viewParams, EXTR_SKIP);
		ob_start();
		if ($view) {
			require $view;
		} else {
			require BASE_PATH . '/app/views/errors/404.php';
		}

		$content = ob_get_clean();

		//strpos kiem tra xem admin- co phai xuat hien o dong dau tien khong 
		$layout = (strpos($_GET['page'] ?? 'home', 'admin-') === 0) ? 'AdminLayout.php' : 'Main.php';
		require BASE_PATH . '/app/views/layouts/' . $layout;
	}
}
// ham de debug
function dd($data)
{

	echo "<html style='background: #1d1f21;'><head><title>Debug Data</title></head><body>";
	echo "<pre style='background: #282a2e; color: #70c0b1; padding: 20px; border-radius: 8px; font-size: 15px; font-family: monospace; border: 1px solid #373b41; margin: 20px; overflow: auto;'>";
	var_dump($data);
	echo "</pre></body></html>";
	die();
}
