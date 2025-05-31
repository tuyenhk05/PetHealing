<?php
include('../includes/db_connect.php');  // Kết nối cơ sở dữ liệu

$querybs = "SELECT * FROM BacSi";
$result = mysqli_query($conn, $querybs);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đội ngũ Bác sĩ thú y</title>
    <link rel="stylesheet" href="../assets/css/doctors.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>
<body>
<header class="header">
    <div class="header-container">
        <div class="logo-block">
            <img src="../assets/image/logo.jpg" alt="PetHealing Logo" class="logo-img">
            <span class="site-title">PetHealing</span>
        </div>
        <nav class="main-nav">
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
<div class="alldoctor">
<h1 class="page-title container">Đội ngũ Bác sĩ thú y</h1>
<div class="doctors-list container">
    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Handle image name
            ?>
            <div class="doctor-card">
                <div class="doctor-img">
                    <img src="../assets/image//<?php echo $row['ho_ten']; ?>.jpg" alt="<?php echo $row['ho_ten']; ?>>">
                </div>
                <div class="doctor-info">
                    <div class="doctor-name">TS. <?php echo htmlspecialchars($row['ho_ten']); ?></div>
                    <div class="doctor-role">Bác sĩ thú y chuyên khoa</div>
                    <div class="doctor-desc">Chuyên môn: <?php echo htmlspecialchars($row['chuyen_mon']); ?></div>
                    <div class="doctor-exp">Kinh nghiệm: <b><?php echo htmlspecialchars($row['kinh_nghiem']); ?> năm</b></div>
                    <div class="doctor-degree">Học vấn: Tiến sĩ Thú y - Đại học Y Dược</div>
                    <div class="doctor-specialties">
                        <?php
                        $specialties = explode(",", $row['chuyen_mon']);
                        foreach ($specialties as $sp) {
                            echo '<span class="spec-tag">' . htmlspecialchars(trim($sp)) . '</span>';
                        }
                        ?>
                    </div>
                    <div class="doctor-actions">
                        <a href="doctor_details.php?id=<?php echo urlencode($row['id']); ?>" class="btn schedule-btn"><i class="fa-regular fa-calendar"></i> Xem thông tin</a>
                        <button class="btn contact-btn"><i class="fa-solid fa-phone"></i> Liên hệ</button>
                        <button class="btn message-btn"><i class="fa-regular fa-envelope"></i> Gửi tin nhắn</button>
                    </div>
                    <div class="doctor-rating">
                        <?php for ($i=0; $i<5; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>Chưa có bác sĩ nào trong hệ thống.</p>";
    }
    ?>
        
   
</div>
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
</html>
