<?php 
include('../includes/db_connect.php');
include('../includes/header.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tin tức thú cưng mới nhất</title>
    <link rel="stylesheet" href="../assets/css/news.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <main>
        <h1 class="news-section-title">Tin tức thú cưng mới nhất</h1>
        <div class="news-list">
        <?php
        $sql = "SELECT * FROM tintuc ORDER BY ngay_dang DESC";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $image = !empty($row['anh']) ? $row['anh'] : 'default_news.jpg';
            ?>
            <a class="news-card" href="news_detail.php?id=<?php echo $row['id']; ?>">
                <div class="news-thumb-wrap">
                    <img src="../assets/image/<?php echo htmlspecialchars($image); ?>" alt="Ảnh tin tức" class="news-thumb">
                </div>
                <div class="news-info">
                    <div class="news-title"><?php echo htmlspecialchars($row['tieu_de']); ?></div>
                    <div class="news-date">
                        <i class="fa-regular fa-calendar"></i>
                        <?php echo date('d/m/Y', strtotime($row['ngay_dang'])); ?>
                    </div>
                    <div class="news-summary">
                        <?php
                        $tomtat = strip_tags($row['noi_dung']);
                        echo mb_substr($tomtat, 0, 90) . (mb_strlen($tomtat) > 90 ? '...' : '');
                        ?>
                    </div>
                </div>
            </a>
        <?php } ?>
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
