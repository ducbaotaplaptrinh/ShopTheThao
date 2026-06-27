        <div class="contact-buttons">
            <a href="#" id="backToTopBtn" class="contact-item back-to-top" title="Lên đầu trang">
                <i class="fas fa-arrow-up"></i>
            </a>
            <a href="tel:<?= htmlspecialchars($cauhinh['sdt']) ?>" class="contact-item phone" title="Gọi điện">
                <div class="phone-glow"></div>
                <i class="fas fa-phone-alt"></i>
            </a>

            <a href="<?= htmlspecialchars($cauhinh['zalo_link']) ?>" target="_blank" class="contact-item zalo" title="Chat Zalo">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Icon_of_Zalo.svg" alt="Zalo" />
            </a>

            <a href="<?= htmlspecialchars($cauhinh['facebook_link']) ?>" target="_blank" class="contact-item messenger" title="Chat Messenger">
                <i class="fab fa-facebook-messenger"></i>
            </a>
        </div>
        <footer id="contact" class="site-footer">
            <div class="container-xl py-5">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <h3 class="mb-3">Bảo Đạt Sport</h3>
                        <p class="text-white-50">
                            Chuyên cung cấp các sản phẩm cầu lông và dụng cụ thể thao chính hãng, uy tín và chất lượng. 
                            Đồng hành cùng niềm đam mê thể thao của bạn.
                        </p>
                    </div>
                    <div class="col-6 col-lg-2">
                        <h4>Menu</h4>
                        <ul class="footer-list">
                            <li><a href="?page=home">Trang chủ</a></li>
                            <li><a href="?page=product-index">Sản phẩm</a></li>
                            <li><a href="?page=flash-sale">Flash Sale</a></li>
                            <li><a href="?page=about">Giới thiệu</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-lg-3">
                        <h4>Danh mục</h4>
                        <ul class="footer-list">
                            <li><a href="?page=product-index">Vợt cầu lông</a></li>
                            <li><a href="?page=product-index">Giày thể thao</a></li>
                            <li><a href="?page=product-index">Áo / Quần</a></li>
                            <li><a href="?page=product-index">Phụ kiện</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <h4>Liên hệ</h4>
                        <ul class="footer-contact">
                            <li>
                                <i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($cauhinh['dia_chi']) ?>
                            </li>
                            <li>
                                <i class="bi bi-telephone-fill"></i> <?= htmlspecialchars($cauhinh['sdt']) ?>
                            </li>
                            <li>
                                <i class="bi bi-envelope-fill"></i>
                                <?= htmlspecialchars($cauhinh['email']) ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <div
                    class="footer-bottom pt-4 mt-4 d-flex flex-column flex-md-row justify-content-between gap-2">
                    <span>© 2026 Bảo Đạt Sport. Tất cả quyền được bảo lưu.</span>
                    <span>Uy tín - Chất lượng - Tận tâm.</span>
                </div>
            </div>
        </footer>

        <!-- javascript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
        <script src="assets/js/app.js"></script>
        <script src="assets/js/slider.js"></script>
        <script src="assets/js/about.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init();
        </script>
        </body>

        </html>