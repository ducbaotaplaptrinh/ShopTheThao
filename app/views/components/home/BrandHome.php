<?php
$activeBrands = [];
if (isset($brands) && is_array($brands)) {
    foreach ($brands as $b) {
        if (is_object($b)) {
            if ($b->getTrang_thai() == 1 && $b->getNgay_xoa() === null) {
                $activeBrands[] = [
                    'id' => $b->getId(),
                    'ten_thuong_hieu' => $b->getTen_thuong_hieu(),
                    'duong_dan_slug' => $b->getDuong_dan_slug(),
                    'anh_logo' => $b->getAnh_logo()
                ];
            }
        } else {
            if (($b['trang_thai'] ?? 1) == 1 && empty($b['ngay_xoa'])) {
                $activeBrands[] = $b;
            }
        }
    }
}
$activeBrands = array_values($activeBrands);
// Lấy tối đa 14 thương hiệu để xếp xung quanh logo trung tâm trong lưới 15 ô (5x3)
$displayBrands = array_slice($activeBrands, 0, 14);
?>

<style>
    .partner-brands-section {
        padding: 60px 0;
        margin-top: 30px;
        background: radial-gradient(circle at top right, rgba(30, 60, 114, 0.03) 0%, rgba(255, 255, 255, 0) 60%);
        position: relative;
    }

    .brand-grid-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        align-items: center;
        margin-top: 35px;
    }

    .brand-card-item {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        padding: 15px;
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.015);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        text-decoration: none;
    }

    .brand-card-item:hover {
        transform: translateY(-6px) scale(1.05);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
        border-color: rgba(30, 60, 114, 0.3);
    }

    .brand-card-item img {
        max-width: 85%;
        max-height: 80%;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.6;
        transition: all 0.3s ease;
    }

    .brand-card-item:hover img {
        filter: grayscale(0%);
        opacity: 1;
    }

    .center-logo-card-item {
        grid-column: 3;
        grid-row: 2;
        height: 105px;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border: none;
        box-shadow: 0 10px 25px rgba(30, 60, 114, 0.25);
        z-index: 2;
        animation: floatLogo 4s ease-in-out infinite;
        position: relative;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
    }

    /* Central logo glow pulse */
    .center-logo-card-item::before {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 18px;
        z-index: -1;
        opacity: 0.4;
        filter: blur(8px);
        animation: pulseGlow 2s ease-in-out infinite;
    }

    .center-logo-card-item img {
        max-width: 90%;
        max-height: 80%;
        filter: none;
        opacity: 1;
        transform: none;
    }

    @keyframes floatLogo {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-6px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    @keyframes pulseGlow {
        0% {
            opacity: 0.3;
            filter: blur(6px);
        }

        50% {
            opacity: 0.6;
            filter: blur(12px);
        }

        100% {
            opacity: 0.3;
            filter: blur(6px);
        }
    }

    /* Responsive Grid for mobile devices */
    @media (max-width: 991px) {
        .brand-grid-container {
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .center-logo-card-item {
            grid-column: auto;
            grid-row: auto;
            height: 90px;
            animation: none;
        }

        .center-logo-card-item::before {
            display: none;
        }
    }

    @media (max-width: 575px) {
        .brand-grid-container {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
    }
</style>

<section class="partner-brands-section my-5">
    <div class="brand-section-header text-center mb-4">
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">THƯƠNG HIỆU</span>
        <h2 class="fw-bold text-dark mb-1">ĐỐI TÁC CHÍNH HÃNG</h2>
        <p class="text-muted small mx-auto" style="max-width: 500px;">Bảo Đạt Sport là đại lý phân phối chính thức các thương hiệu thể thao nổi tiếng toàn cầu</p>
    </div>

    <div class="brand-grid-container">
        <div class="center-logo-card-item">
            <img src="<?= htmlspecialchars($cauhinh['logo_url'] ?? 'assets/images/favicons/Logo.png') ?>" alt="Bảo Đạt Logo">
        </div>

        <!-- Brand Cards -->
        <?php foreach ($displayBrands as $index => $b): ?>
            <?php
            $logoUrl = getProductImage("assets/images/brands/" . ($b['anh_logo'] ?? ''));
            ?>
            <a href="?page=product-index&brand=<?= htmlspecialchars($b['duong_dan_slug']) ?>" class="brand-card-item" title="<?= htmlspecialchars($b['ten_thuong_hieu']) ?>" data-aos="flip-left">
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($b['ten_thuong_hieu']) ?>">
            </a>
        <?php endforeach; ?>
    </div>
</section>