<?php
include('../includes/db_connect.php');  // Kết nối cơ sở dữ liệu
include('../includes/header.php'); // Bao gồm header

// Truy vấn dữ liệu cần hiển thị (ví dụ: các dịch vụ)
$querydv = "SELECT * FROM dichvu LIMIT 3";
$result = mysqli_query($conn, $querydv);

$querybs = "SELECT * FROM BacSi LIMIT 3";
$resultBs = mysqli_query($conn, $querybs);

$querypk = "SELECT id, ten, mo_ta, gia, 'phu-kien' AS loai FROM PhuKien LIMIT 4"; // Fetch the first 4 products, adjust as needed
$resultPK = mysqli_query($conn, $querypk);

$querydv = "SELECT ten_dich_vu FROM dichvu"; // Fetch the first 4 products, adjust as needed
$resultDV = mysqli_query($conn, $querydv);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHealing - Trang chủ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Liên kết với jQuery -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

           <div id="message-container" class="message-container"></div>


    <div class="box">
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Chăm sóc sức khỏe thú cưng của bạn</h1>
                <p>Đội ngũ bác sĩ chuyên nghiệp và tận tâm, cùng với trang thiết bị hiện đại, chúng tôi mang đến dịch vụ chăm sóc thú cưng tốt nhất.</p>
                <div class="hero-buttons">
                    <a href="appointment.php" class="btn-primary">Đặt lịch ngay</a>
                    <a href="services.php" class="btn-secondary">Khám phá dịch vụ</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="../assets/image/anh_home.jpg" alt="Pet Care">
            </div>
        </div>
    </section>

    <section class="about-us">
    <div class="container overflow-hidden">
        <div class="slider-track">
            <!-- Thẻ 1 -->
            <div class="info-card">
                <div class="icon-box"><i class="fa-solid fa-heart"></i></div>
                <h4>Chăm sóc tận tâm</h4>
                <p>Đội ngũ bác sĩ và nhân viên luôn đặt sức khỏe của linh thú lên hàng đầu rứa.</p>
            </div>
            <!-- Thẻ 2 -->
            <div class="info-card">
                <div class="icon-box"><i class="fa-solid fa-shield"></i></div>
                <h4>Thiết bị hiện đại</h4>
                <p>Trang bị thần khí công nghệ tiên tiến để chẩn đoán và điều trị tốt nhất mô.</p>
            </div>
            <!-- Thẻ 3 -->
            <div class="info-card">
                <div class="icon-box"><i class="fa-regular fa-clock"></i></div>
                <h4>Hỗ trợ 24/7</h4>
                <p>Luôn sẵn sàng ứng cứu trong mọi tình huống khẩn cấp, bất kể ngày đêm rứa.</p>
            </div>
            <!-- Thẻ 4 (Mới thêm cho đầy đặn) -->
            <div class="info-card">
                <div class="icon-box"><i class="fa-solid fa-hand-holding-medical"></i></div>
                <h4>Dịch vụ cao cấp</h4>
                <p>Trải nghiệm nghỉ dưỡng và spa chuẩn 5 sao cho những thú cưng quý tộc rứa.</p>
            </div>
            
            <!-- Nhân bản các thẻ để tạo hiệu ứng trượt vòng tròn liên tục (Clone for Infinite loop) -->
            <div class="info-card">
                <div class="icon-box"><i class="fa-solid fa-heart"></i></div>
                <h4>Chăm sóc tận tâm</h4>
                <p>Đội ngũ bác sĩ và nhân viên luôn đặt sức khỏe của linh thú lên hàng đầu rứa.</p>
            </div>
            <div class="info-card">
                <div class="icon-box"><i class="fa-solid fa-shield"></i></div>
                <h4>Thiết bị hiện đại</h4>
                <p>Trang bị thần khí công nghệ tiên tiến để chẩn đoán và điều trị tốt nhất mô.</p>
            </div>
        </div>
    </div>
</section>

<section class="featured-services">
    <div class="container">
        <h2 class="section-title text-center mb-5">Dịch vụ nổi bật</h2>
        <div class="services-grid">
            <?php
            // Giả lập dữ liệu từ Database nếu Huynh chưa có biến $result rứa mô
            // Trong thực tế, Huynh dùng vòng lặp while ($row = mysqli_fetch_assoc($result)) như cũ nhé
            $mock_services = [
                ['ten_dich_vu' => 'Tiêm chủng', 'mo_ta' => 'Phòng ngừa bệnh tật cho linh thú bằng linh dược tiên tiến nhất rứa.'],
                ['ten_dich_vu' => 'Phẫu thuật chuyên sâu', 'mo_ta' => 'Đội ngũ y sĩ tay nghề cao, trang thiết bị hiện đại bậc nhất mô.'],
                ['ten_dich_vu' => 'Làm đẹp & Spa', 'mo_ta' => 'Chăm sóc bộ lông và làn da, giúp thú cưng tỏa sáng như tiên tử.']
            ];

            foreach ($mock_services as $index => $row) {
                // Gán class để xác định thẻ giữa (thẻ thứ 2 rứa)
                $isCenter = ($index === 1) ? 'center-card' : '';
                ?>
                <div class="service-card1 reveal-card <?= $isCenter ?>">
                    <div class="card-inner">
                        <div class="icon-circle shadow-sm mb-3">
                            <i class="fa-solid <?= $index === 0 ? 'fa-syringe' : ($index === 1 ? 'fa-stethoscope' : 'fa-scissors') ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($row['ten_dich_vu']); ?></h3>
                        <p><?php echo htmlspecialchars($row['mo_ta']); ?></p>
                        <a href="appointment.php" class="btn-primary-pet">Đặt lịch ngay</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

    <section class="appointment">
    <div class="container">
        <!-- Header Section: Hiện lên từ dưới rứa mô -->

        <div class="content_app reveal-up">
            <h2 class="section-title text-white">Đặt lịch hẹn ngay</h2>
            <p class="text-white opacity-90">Đặt lịch hẹn trực tiếp để được phục vụ tốt nhất. Chúng tôi sẽ liên hệ xác nhận trong vòng 24 giờ .</p>
        </div>

        <div class="appointment_main">
            <!-- Form: Bay từ bên trái sang (Reveal Left) -->
            <div class="form-container reveal-left shadow-lg">
                <form id="appointment-form">
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="full-name">Họ tên</label>
                            <input type="text" id="full-name" name="full-name" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="form-group half">
                            <label for="phone">Số điện thoại</label>
                            <input type="tel" id="phone" name="phone" placeholder="0123456789" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email công vụ</label>
                        <input type="email" id="email" name="email" placeholder="example@email.com" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="pet-name">Tên thú cưng</label>
                            <input type="text" id="pet-name" name="pet-name" placeholder="Mèo/Chó..." required>
                        </div>
                        <div class="form-group half">
                            <label for="pet-type">Chủng loại</label>
                            <select id="pet-type" name="pet-type" required>
                                <option value="" disabled selected>Chọn loài</option>
                                <option value="dog">Chó</option>
                                <option value="cat">Mèo</option>
                                <option value="other">Loài khác</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="appointment-date">Ngày đến khám</label>
                            <input type="date" id="appointment-date" name="appointment-date" required>
                        </div>
                        <div class="form-group half">
                            <label for="appointment-time">Giờ</label>
                            <input type="time" id="appointment-time" name="appointment-time" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="service">Chọn loại dịch vụ</label>
                        <select id="service" name="service" required>
                            <option value="" disabled selected>Chọn dịch vụ </option>
                            <?php while ($row = mysqli_fetch_assoc($resultDV)) { ?>
                                        <option><?= htmlspecialchars($row['ten_dich_vu']) ?></option>
                                    <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Ghi chú </label>
                        <textarea id="notes" name="notes" placeholder="Mô tả triệu chứng hoặc yêu cầu đặc biệt ..."></textarea>
                    </div>

                    <button type="submit" class="button_home w-100 mt-3">
                        <i class="fa-solid fa-paper-plane me-2"></i>  Đặt Lịch
                    </button>
                </form>
            </div>

            <!-- Image: Bay từ bên phải sang (Reveal Right) -->
            <div class="appointment_image reveal-right">
                <img src="../assets/image/form-post.jpeg" alt="Đặt lịch hẹn" class="shadow-lg" />
            </div>
        </div>
    </div>
</section>


<section class="team">
    <div class="container">
        <!-- Tiêu đề Section: Roll tới từ hư không rứa mô -->
        <div class="team_content reveal-up">
            <h2 class="section-title">Đội Ngũ Y Tế</h2>
            <p class="section-subtitle">Đội ngũ y sĩ chuyên nghiệp, giàu kinh nghiệm, luôn sẵn sàng chăm sóc sức khỏe cho thú cưng của bạn !</p>
        </div>

        <div class="team-grid">
            <?php
            $i = 0;
            // Sử dụng "Khẩu quyết" dữ liệu thật của Huynh rứa mô
            // Thuật ngữ: Fetching Data (Lấy dữ liệu từ bộ nhớ đệm)
            if ($resultBs && mysqli_num_rows($resultBs) > 0) {
                while ($row = mysqli_fetch_assoc($resultBs)) {
                    $i++;
                    // Tuyệt kỹ Xấp xỉ: Cứ vị y sĩ thứ 2, 5, 8... (ở giữa) sẽ bay cao hơn rứa mô
                    $stepped_class = ($i % 3 == 2) ? 'featured-member' : '';
                    $delay = ($i % 3) * 200; // Staggered Delay (Độ trễ so le) rứa
                    ?>
                    <div class="team-member reveal-up <?= $stepped_class ?>" style="transition-delay: <?= $delay ?>ms;">
                        <div class="member-inner shadow-sm">
                            <div class="member-image">
                                <!-- Chân dung bác sĩ từ Database rứa mô -->
                                <?php
                                // Huynh chú ý: Nếu database không có cột image, đệ dùng tên làm fallback rứa
                                $img_name = isset($row['image']) && $row['image'] ? $row['image'] : $row['ho_ten'] . ".jpg";
                                $img_path = "../assets/image/" . $img_name;
                                ?>
                                <img src="<?= $img_path ?>" 
                                     alt="<?= htmlspecialchars($row['ho_ten']) ?>"
                                     onerror="this.src='../assets/image/default_doctor.jpg';">
                                
                                <div class="image-overlay">
                                    <div class="social-icons">
                                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4><?php echo htmlspecialchars($row['ho_ten']); ?></h4>
                                <p class="specialty"><?php echo htmlspecialchars($row['chuyen_mon']); ?></p>
                                <div class="rating">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else {
                echo "<p class='text-center w-100'>Hiện chưa có y gia nào ẩn cư tại đây rứa mô!</p>";
            }
            ?>
        </div>

        <div class="view-all reveal-up">
            <a href="all_doctors.php" class="btn-all-doctors">
                Khám phá tất cả đội ngũ <i class="fa-solid fa-arrow-right-long ms-2"></i>
            </a>
        </div>
    </div>
</section>

<section class="product-store">
    <div class="container">
        <!-- Tiêu đề Section: Roll tới uy nghiêm rứa mô -->
        <div class="store-header reveal-up">
            <h2 class="section-title">Sản phẩm cho thú cưng</h2>
            <p class="section-subtitle">Những thức ăn và phụ kiện thượng hạng, giúp thú cưng của bạn phát triển và khỏe mạnh </p>
        </div>

        <div class="products-grid">
            <?php
            $p = 0;
            // Thuật ngữ: Result Set (Tập kết quả) - Dữ liệu thật từ biến $resultPK của Huynh rứa
            if (isset($resultPK) && mysqli_num_rows($resultPK) > 0) {
                while ($row = mysqli_fetch_assoc($resultPK)) {
                    $p++;
                    // Tuyệt kỹ Xấp xỉ (Stepped Layout) cho 4 cột: 
                    // Cột 2 và 3 trong mỗi hàng 4 cái sẽ nhô cao hơn rứa mô!
                    $pos = $p % 4;
                    $stepped_class = ($pos == 2 || $pos == 3) ? 'featured-product' : '';
                    $delay = ($pos == 0 ? 4 : $pos) * 100; // Staggered Delay (Độ trễ so le)
                    ?>
                    <div class="product-card reveal-up <?= $stepped_class ?>" style="transition-delay: <?= $delay ?>ms;">
                        <div class="product-inner shadow-sm">
                            <div class="product-image-box">
                                <?php
                                $img_name = isset($row['image']) && $row['image'] ? $row['image'] : $row['ten'] . ".jpg";
                                $img_path = "../assets/image/" . $img_name;
                                ?>
                                <img src="<?= $img_path ?>" 
                                     alt="<?= htmlspecialchars($row['ten']) ?>"
                                     onerror="this.src='../assets/image/default_product.jpg';">
                                
                                <div class="product-badge">New</div>
                            </div>
                            <div class="product-info">
                                <h4><?php echo htmlspecialchars($row['ten']); ?></h4>
                                <div class="price-tag">
                                    <span class="currency">₫</span><?php echo number_format($row['gia'], 0, ',', '.'); ?>
                                </div>
                                <div class="product-actions">
                                    <a href="product_detail.php?id=<?php echo $row['id']; ?>&type=<?php echo $row['loai']; ?>" class="btn-detail">
                                        
                                        <i class="fa-solid fa-eye me-1"></i> Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else {
                echo "<p class='text-center w-100 opacity-50'>Kho hàng hiện đang trống không, chờ Huynh nhập bảo vật về rứa mô!</p>";
            }
            ?>
        </div>

        <div class="view-all reveal-up">
            <a href="all_products.php" class="btn-all-products">
                Ghé thăm Cửa hàng <i class="fa-solid fa-store ms-2"></i>
            </a>
        </div>
    </div>
</section>
        </div>
       
    <?php
    include('../includes/footer.php'); // Bao gồm navbar
          ?>
</body>
   

</html> <script src="../assets/js/formHome.js"></script> <!-- Liên kết với file app.js -->

<?php mysqli_close($conn); ?>
