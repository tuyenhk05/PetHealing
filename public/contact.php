
<?php

 echo "<!-- This is a PHP file for the contact page of PetHealing website -->";

?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHealing - Trang chủ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

     <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><img src="../assets/image/logo.jpg" alt="PetHealing Logo"> PetHealing </a>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="index.php">Trang chủ</a></li>
                    <li><a href="appointment.php">Đặt lịch hẹn</a></li>
                    <li><a href="services.php">Dịch vụ</a></li>
                    <li><a href="about.php">Giới thiệu</a></li>
                    <li><a href="contact.php">Liên hệ</a></li>
                </ul>
            </nav>
        </div>
    </header>

<div class="bg-white ">
   
    <!-- Hero Section -->
    <section class="heroo ">
        <div class="hero-bg"></div>
        <div class="hero-content container">
            <h1>Liên hệ với <span>chúng tôi</span></h1>
            <p>Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy liên hệ ngay để được tư vấn về dịch vụ chăm sóc thú cưng.</p>
        </div>
    </section>

    <!-- Contact Info & Form -->
    <section class="contact-info-form">
        
        <div class="contact-info ">
            <h2>Thông tin liên hệ</h2>
            <div class="contact-detail">
                <div class="contact-icon address"></div>
                <div>
                    <h3>Địa chỉ</h3>
                    <p>123 Đường Thú Cưng, Quận 1, TP.HCM</p>
                </div>
            </div>

            <div class="contact-detail">
                <div class="contact-icon phone"></div>
                <div>
                    <h3>Điện thoại</h3>
                    <p>0123 456 789</p>
                </div>
            </div>

            <div class="contact-detail">
                <div class="contact-icon email"></div>
                <div>
                    <h3>Email</h3>
                    <p>lienhe@petcare.vn</p>
                </div>
            </div>

            <div class="contact-detail">
                <div class="contact-icon hours"></div>
                <div>
                    <h3>Giờ làm việc</h3>
                    <p>Thứ Hai - Thứ Bảy: 8:00 - 20:00</p>
                    <p>Chủ Nhật: 9:00 - 18:00</p>
                </div>
            </div>
            <div class="emergency">
                <h3>Dịch vụ cấp cứu 24/7</h3>
                <p>Trong trường hợp khẩn cấp, hãy gọi ngay cho chúng tôi theo số hotline:</p>
                <a href="tel:0123456789" class="emergency-btn">0123 456 789</a>
            </div>
            
        </div>

        <!-- Contact Form -->
        <div class="contact-form ">
            <h2>Gửi tin nhắn</h2>
            <form>
                <label for="name">Họ tên</label>
                <input type="text" id="name" placeholder="Nguyễn Văn A">

                <label for="email">Email</label>
                <input type="email" id="email" placeholder="example@email.com">

                <label for="phone">Số điện thoại</label>
                <input type="tel" id="phone" placeholder="0123456789">

                <label for="subject">Chủ đề</label>
                <input type="text" id="subject" placeholder="Tư vấn dịch vụ">

                <label for="message">Nội dung</label>
                <textarea id="message" placeholder="Nhập nội dung tin nhắn của bạn..."></textarea>

                <button type="submit" class="submit-btn">Gửi tin nhắn</button>
            </form>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map">
        <h2>Vị trí của chúng tôi</h2>
        <p>Dễ dàng tìm thấy chúng tôi tại trung tâm thành phố</p>
        <div class="map-frame">
            <iframe src="https://www.google.com/maps/embed?pb=..." allowfullscreen loading="lazy"></iframe>
        </div>
    </section>
</div>
      <footer class="footer">
    <div class="container">
        <div class="footer-left">
            <div class="footer-logo">
                <a href="index.php">
                    <img src="../assets/image/logo.jpg" alt="PetHealing Logo">
                </a>
                <p>Chúng tôi cung cấp dịch vụ chăm sóc thú cưng chất lượng cao với đội ngũ bác sĩ thú y giàu kinh nghiệm và tận tâm.</p>
            </div>
            <div class="footer-social">
                <a href="https://www.facebook.com/tuyen.ne.359">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">YouTube</a>
            </div>
        </div>

        <div class="footer-center">
            <h3>Dịch vụ của chúng tôi</h3>
            <ul>
                <li><a href="services.php">Khám và điều trị</a></li>
                <li><a href="services.php">Phẫu thuật</a></li>
                <li><a href="services.php">Chăm sóc răng miệng</a></li>
                <li><a href="services.php">Chăm sóc da và lông</a></li>
                <li><a href="services.php">Khách sạn thú cưng</a></li>
            </ul>
        </div>

        <div class="footer-right">
            <h3>Liên kết nhanh</h3>
            <ul>
                <li><a href="all_doctors.php">Đội ngũ bác sĩ</a></li>
                <li><a href="appointment.php">Đặt lịch hẹn</a></li>
                <li><a href="all_products.php">Cửa hàng</a></li>
                <li><a href="#">Hồ sơ thú cưng</a></li>
                <li><a href="about.php">Liên hệ</a></li>
            </ul>

            <h3>Thông tin liên hệ</h3>
            <p>123 Đường Thú Cưng, Quận 1, TP.HCM</p>
            <p>0123 456 789</p>
            <p>lienhe@petcare.vn</p>
            <p>Thứ Hai - Thứ Bảy: 8:00 - 20:00</p>
            <p>Chủ Nhật: 9:00 - 18:00</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2025 PetHealing. Tất cả quyền lợi được bảo vệ.</p>
    </div>
</footer>
    </body>