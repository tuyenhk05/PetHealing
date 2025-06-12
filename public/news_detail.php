<?php
include('../includes/db_connect.php');
include('../includes/header.php');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM tintuc WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($row['tieu_de'] ?? 'Chi tiết tin tức'); ?></title>
    <link rel="stylesheet" href="../assets/css/news_detail.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <main>
        <div class="news-detail-main">
            <div class="news-detail-card">
                <div class="news-detail-img-block">
                    <img src="../assets/image/<?php echo htmlspecialchars($row['anh'] ?? 'default_news.jpg'); ?>" class="news-detail-img" alt="Ảnh tin tức">
                </div>
                <div class="news-detail-info">
                    <div class="news-detail-title"><?php echo htmlspecialchars($row['tieu_de'] ?? 'Không tìm thấy tin tức'); ?></div>
                    <div class="news-detail-date">
                        <i class="fa-regular fa-calendar"></i>
                        <?php echo isset($row['ngay_dang']) ? date('d/m/Y', strtotime($row['ngay_dang'])) : ''; ?>
                    </div>
                    <div class="news-detail-content">
                        <?php echo nl2br(htmlspecialchars($row['noi_dung'] ?? '')); ?>
                    </div>
                    <a href="news.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay về danh sách tin</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
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
               
                <li><a href="about.php">Liên hệ</a></li>
                <li><a href="news.php">Tin tức</a></li>
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
