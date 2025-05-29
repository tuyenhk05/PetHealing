<?php 
$conn = new mysqli("localhost", "root", "", "BenhVienThuCung");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$sql = "SELECT * FROM BacSi";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đội ngũ Bác sĩ thú y</title>
    <link rel="stylesheet" href="../assets/css/doctors.css">
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

<h1 class="page-title">Đội ngũ Bác sĩ thú y</h1>
<div class="doctors-list">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Xử lý tên ảnh
            $anh = isset($row['anh']) && trim($row['anh']) != '' ? trim($row['anh']) : 'doctor_sample.jpg';
            ?>
            <div class="doctor-card">
                <div class="doctor-img">
                    <img src="../assets/image/<?php echo htmlspecialchars($anh); ?>" alt="Bác sĩ thú y">
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
                        <button class="btn schedule-btn"><i class="fa-regular fa-calendar"></i> Đặt lịch hẹn</button>
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
    $conn->close();
    ?>
</div>
</body>
</html>
