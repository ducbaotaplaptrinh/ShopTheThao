<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/contact.css">

<div class="contact-container">
    <div class="contact-info">
        <h2>Thông Tin Liên Hệ</h2>
        <p style="margin-bottom: 30px; color: #7f8c8d;">Chúng tôi luôn mong muốn mang lại trải nghiệm tốt nhất. Đừng
            ngần ngại liên hệ với chúng tôi nhé!</p>

        <div class="info-item">
            <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
            <div>
                <h4>Địa chỉ</h4>
                <p><?= htmlspecialchars($cauhinh['dia_chi']) ?></p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon"><i class="fas fa-phone-alt"></i></div>
            <div>
                <h4>Điện thoại</h4>
                <p><?= htmlspecialchars($cauhinh['sdt']) ?> (Hỗ trợ 24/7)</p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon"><i class="fas fa-envelope"></i></div>
            <div>
                <h4>Email</h4>
                <p><?= htmlspecialchars($cauhinh['email']) ?></p>
            </div>
        </div>


        <div class="info-item">
            <div class="icon"><i class="fab fa-facebook" style="color: #1877F2;"></i></div>
            <div>
                <h4>Facebook</h4>
                <p><a href="<?= htmlspecialchars($cauhinh['facebook_link']) ?>" target="_blank"
                        style="text-decoration:none;font-weight:bold;color:#1877F2;">Nhắn tin qua Facebook</a></p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon"><i class="fas fa-comment-dots" style="color: #0068FF;"></i></div>
            <div>
                <h4>Zalo</h4>
                <p><a href="<?= htmlspecialchars($cauhinh['zalo_link']) ?>" target="_blank"
                        style="text-decoration:none;font-weight:bold;color:#0068FF;">Nhắn tin qua Zalo</a></p>
            </div>
        </div>
    </div>

    <div class="contact-form-section">
        <h2>Gửi Tin Nhắn Cho Chúng Tôi</h2>

        <?php if (!empty($error)): ?>
            <div class="success-message"
                style="display:block; background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; margin-bottom:15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message" style="display:block; margin-bottom:15px;" id="phpSuccessMsg">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form id="contactForm" method="POST" action="">
            <div class="form-group">
                <label for="name">Họ và Tên</label>
                <input type="text" id="name" name="name" placeholder="Nhập họ tên của bạn..."
                    value="<?php echo $_POST['name'] ?? ''; ?>">
                <div class="error-message" id="nameError">*Vui lòng nhập họ tên!</div>
            </div>

            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" placeholder="Nhập email của bạn..."
                    value="<?php echo $_POST['email'] ?? ''; ?>">
                <div class="error-message" id="emailError">*Vui lòng nhập email hợp lệ!</div>
            </div>

            <div class="form-group">
                <label for="subject">Chủ đề (Không bắt buộc)</label>
                <input type="text" id="subject" name="subject" placeholder="Bạn cần hỗ trợ về vấn đề gì?"
                    value="<?php echo $_POST['subject'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="message">Nội dung</label>
                <textarea id="message" name="message"
                    placeholder="Nhập nội dung tin nhắn..."><?php echo $_POST['message'] ?? ''; ?></textarea>
                <div class="error-message" id="messageError">*Vui lòng nhập nội dung!</div>
            </div>

            <button type="submit" class="btn-submit">Gửi Tin Nhắn <i class="fas fa-paper-plane"
                    style="margin-left: 5px;"></i></button>
        </form>
    </div>
</div>

<script src="assets/js/contact.js"></script>