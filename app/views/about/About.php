<?php
// Mảng thống kê
$heroHighlights = [
    ['value' => '100%', 'label' => 'Chính hãng phân phối'],
    ['value' => '24/7', 'label' => 'Sẵn sàng tư vấn'],
    ['value' => '500+', 'label' => 'Sản phẩm đa dạng'],
];

// Mảng 3 Banner tràn màn hình 
$introBanners = [
    [
        'title' => 'Vũ Khí Sắc Bén - Định Hình Lối Chơi',
        'text' => 'Một cây vợt tốt là phần mở rộng của cánh tay. Dù bạn thiên về tấn công uy lực, phòng thủ phản tạt hay đánh công thủ toàn diện, Bảo Đạt Sport luôn có sẵn những "bảo kiếm" từ Yonex, Victor, Lining... được tinh chỉnh hoàn hảo cho riêng bạn.',
        'points' => ['Trợ lực tối ưu', 'Khung siêu bền', 'Đa dạng điểm cân bằng'],
        'image' => 'assets/images/about/banner1.png',
    ],
    [
        'title' => 'Bước Chân Vững Chãi - Tốc Độ Linh Hoạt',
        'text' => 'Tốc độ và sự an toàn là chìa khóa trên sân cầu. Bộ sưu tập giày chuyên dụng tại cửa hàng được lựa chọn khắt khe về độ bám sân, giảm chấn và form dáng ôm chân, giúp bạn bứt tốc tức thời và bảo vệ tối đa khỏi chấn thương.',
        'points' => ['Đế bám chống trượt', 'Đệm gót êm ái', 'Trọng lượng siêu nhẹ'],
        'image' => 'assets/images/about/banner2.png',
    ],
    [
        'title' => 'Trang Bị Hoàn Hảo - Phong Thái Chuyên Nghiệp',
        'text' => 'Sự khác biệt đôi khi đến từ những chi tiết nhỏ nhất. Từ sợi cước trợ lực căng chuẩn số kg, lớp quấn cán bám tay, cho đến những bộ trang phục thấm hút mồ hôi... Tất cả đã sẵn sàng để bạn ra sân với trạng thái tốt nhất.',
        'points' => ['Cước căng chuẩn', 'Quấn cán bám dính', 'Vải thoáng khí'],
        'image' => 'assets/images/about/banner3.png',
    ],
];

// Mảng Giá trị cốt lõi
$values = [
    [
        'icon' => 'bi-shield-check',
        'title' => 'Chất Lượng Tuyệt Đối',
        'text' => 'Cam kết 100% sản phẩm chính hãng, nguồn gốc rõ ràng. Đền bù gấp 10 lần nếu phát hiện hàng giả, hàng nhái.',
    ],
    [
        'icon' => 'bi-person-check',
        'title' => 'Tư Vấn Thực Chiến',
        'text' => 'Đội ngũ là những người chơi cầu lông thực thụ, tư vấn dựa trên trải nghiệm thực tế và lối chơi của chính bạn.',
    ],
    [
        'icon' => 'bi-tools',
        'title' => 'Hậu Mãi Tận Tâm',
        'text' => 'Bảo hành minh bạch, hỗ trợ đan lưới chuẩn số kg bằng máy điện tử chính xác, chăm sóc vợt trọn đời.',
    ],
    [
        'icon' => 'bi-rocket-takeoff',
        'title' => 'Giao Hàng Tốc Độ',
        'text' => 'Đóng gói cẩn thận bằng hộp carton chuyên dụng, giao hàng hỏa tốc để bạn không phải chờ đợi lâu trước trận đấu.',
    ],
];
?>

<div class="about-page" id="about">
    <section class="about-hero d-flex align-items-center">
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center" data-reveal>
                    <img src="<?= htmlspecialchars($cauhinh['logo_url'] ?? 'assets/images/about/logo_about-removebg.png') ?>" alt="Bảo Đạt Sport" class="about-logo mb-4">
                    <span class="about-kicker mb-3">
                        <i class="bi bi-award-fill"></i>
                        Câu chuyện thương hiệu
                    </span>
                    <h1 class="about-title mb-4">
                        Bảo toàn phong độ, Đạt đỉnh vinh quang.<br>
                        Người bạn đồng hành đáng tin cậy trên mỗi trận đấu.
                    </h1>
                    <p class="about-lead mx-auto mb-4">
                        Tại Bảo Đạt Sport, chúng tôi hiểu rằng cầu lông không chỉ là một môn thể thao, mà là đam mê, là sự nỗ lực không ngừng sau mỗi giọt mồ hôi. Khởi nguồn từ những lông thủ nhiệt huyết, chúng tôi ra đời với sứ mệnh mang đến những trang thiết bị chất lượng nhất, giúp bạn tự tin sải bước trên sân và chinh phục mọi giới hạn.
                    </p>

                    <div class="row g-4 justify-content-center">
                        <?php foreach ($heroHighlights as $item): ?>
                            <div class="col-6 col-md-4">
                                <div class="about-stat-card text-center p-3" data-reveal>
                                    <div class="stat-value"><?php echo $item['value']; ?></div>
                                    <div class="stat-label mt-2"><?php echo $item['label']; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="intro-banners">
        <?php foreach ($introBanners as $index => $banner): ?>
            <section class="about-fullscreen-banner d-flex align-items-center" data-reveal>
                <div class="container">
                    <div class="row align-items-center g-5 <?php echo $index % 2 === 1 ? 'flex-row-reverse' : ''; ?>">

                        <div class="col-lg-6">
                            <h2 class="display-5 fw-bold mb-4 text-dark"><?php echo $banner['title']; ?></h2>
                            <p class="fs-3 text-secondary lh-lg mb-4">
                                <?php echo $banner['text']; ?>
                            </p>
                            <ul class="list-unstyled d-flex flex-wrap gap-3 mb-0">
                                <?php foreach ($banner['points'] as $point): ?>
                                    <li>
                                        <span class="badge border border-secondary text-secondary px-3 py-2 fs-6 rounded-pill">
                                            <?php echo $point; ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="col-lg-6">
                            <div class="image-placeholder rounded-4 overflow-hidden">
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted border border-dashed rounded-4">
                                    <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-fluid rounded-4 shadow-lg w-100">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <section class="about-section" id="core-values">
        <div class="container">
            <div class="text-center mb-5" data-reveal>
                <h2 class="display-6 fw-bold mb-3">Lý do chọn Bảo Đạt Sport</h2>
                <p class="text-muted fs-3">Chúng tôi xây dựng niềm tin bằng hành động thực tế.</p>
            </div>

            <div class="row g-4">
                <?php foreach ($values as $value): ?>
                    <div class="col-md-6 col-lg-3" data-reveal>
                        <div class="text-center p-3 h-100">
                            <div class="icon-wrap mb-4 mx-auto d-flex align-items-center justify-content-center">
                                <i class="bi <?php echo $value['icon']; ?> fs-1 text-orange"></i>
                            </div>
                            <h4 class="fw-bold mb-3"><?php echo $value['title']; ?></h4>
                            <p class="text-secondary lh-base mb-0">
                                <?php echo $value['text']; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>