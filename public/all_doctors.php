<?php
include('../includes/db_connect.php');  // Kết nối cơ sở dữ liệu
include('../includes/header.php'); // Bao gồm header
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

<section class="relative bg-gradient-to-r from-teal-500 to-teal-700 overflow-hidden">
  <div class="background-overlay"></div>
  <div class="content">
    <div class="text-center container">
      <h1 class="title">Đội ngũ <span class="highlight">bác sĩ</span> của chúng tôi</h1>
      <p class="description">Gặp gỡ đội ngũ bác sĩ giàu kinh nghiệm và tận tâm của chúng tôi</p>
    </div>
  </div>
</section>
<div class="alldoctor">

<div class="doctors-list container">
    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Handle image name
            ?>
            <div class="doctor-card">
                <div class="doctor-img">
                    <img src="../assets/image/<?php echo $row['ho_ten']; ?>.jpg" alt="<?php echo $row['ho_ten']; ?>>">
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
   
</body>
</html>
  <?php include('../includes/footer.php'); ?>