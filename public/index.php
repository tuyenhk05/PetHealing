<?php
include('../includes/db_connect.php');
include('../includes/header.php');

// Truy vấn dữ liệu cho các phân đoạn rứa mô
$querydv_limit = "SELECT * FROM dichvu LIMIT 3";
$resultDV_feat = mysqli_query($conn, $querydv_limit);

$querybs = "SELECT * FROM BacSi LIMIT 3";
$resultBs = mysqli_query($conn, $querybs);

$querypk = "SELECT id, ten, mo_ta, gia, 'phu-kien' AS loai, image FROM PhuKien LIMIT 4";
$resultPK = mysqli_query($conn, $querypk);
$isloggedin = isset($_COOKIE['user_id']);

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
<style>
    :root {
        --pet-green: #2EB292;
        --pet-pink: #F56C93;
        --pet-dark: #1e7a64;
        --transition-hq1: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
    }
    .journey-section {
        padding: 100px 0;
        background: #2EB292;
        color: white;
        position: relative;
        overflow: hidden;
    }

    /* Con đường tơ lụa (The main line) rứa */
    .timeline-main {
        position: relative;
        max-width: 1000px;
        margin: 0 auto;
        padding: 40px 0;
    }

        .timeline-main::after {
            content: '';
            position: absolute;
            width: 4px;
            background: rgba(255,255,255,0.3);
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -2px;
        }

    .timeline-item {
        position: relative;
        background: inherit;
        width: 50%;
        padding: 20px 40px;
        box-sizing: border-box;
    }

        /* Các điểm nút (The nodes) rứa */
        .timeline-item::after {
            content: '';
            position: absolute;
            width: 25px;
            height: 25px;
            right: -13px;
            background-color: white;
            border: 4px solid var(--pet-pink);
            top: 30px;
            border-radius: 50%;
            z-index: 1;
            transition: 0.3s;
        }

    .left {
        left: 0;
        text-align: right;
    }

    .right {
        left: 50%;
        text-align: left;
    }

        .right::after {
            left: -13px;
        }

    .timeline-content {
        padding: 30px;
        background: white;
        color: #333;
        border-radius: 20px;
        position: relative;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: var(--transition-hq1);
    }

        .timeline-content h3 {
            color: var(--pet-green);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .timeline-content .year {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--pet-pink);
            margin-bottom: 5px;
            display: block;
        }

    .timeline-item:hover .timeline-content {
        transform: scale(1.05);
    }

    .timeline-item:hover::after {
        transform: scale(1.3);
        background-color: var(--pet-pink);
    }
    .reveal-up, .reveal-left, .reveal-right {
        opacity: 0;
        transition: var(--transition-hq1);
    }

    .reveal-up {
        transform: translateY(50px);
    }

    .reveal-left {
        transform: translateX(-80px);
    }

    .reveal-right {
        transform: translateX(80px);
    }

    .active.reveal-up, .active.reveal-left, .active.reveal-right {
        opacity: 1;
        transform: translate(0, 0);
    }

    @media (max-width: 768px) {
        .timeline-main::after {
            left: 31px;
        }

        .timeline-item {
            width: 100%;
            padding-left: 70px;
            padding-right: 25px;
            text-align: left;
        }

            .timeline-item::after {
                left: 19px;
            }

        .left::after, .right::after {
            left: 19px;
        }

        .team-member.featured-member, .product-card.featured-product, .service-card1.center-card {
            transform: translateY(0);
        }
    }

</style>
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
                    <?php if ($isloggedin): ?>
                    <a href="appointment.php" class="btn-primary">Đặt lịch ngay</a>
                    <a href="services.php" class="btn-secondary">Khám phá dịch vụ</a>     
                    <?php else: ?>
                    <a href="login.php" class="btn-primary">Đăng nhập để khám phá thêm</a>

                    <?php endif; ?>

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

    <section class="journey-section">
        <div class="container">
            <div class="text-center mb-5 reveal-up">
                <h2 class="display-5 fw-bold">Lịch sử hoạt động PetHealing</h2>
                <p class="opacity-75">Cùng nhìn lại phát triển rực rỡ của chúng tôi </p>
            </div>

            <div class="timeline-main">
               <div class="timeline-item left reveal-left">
    <div class="timeline-content">
        <span class="year">2020</span>
        <h3>Thành Lập Phòng Khám Đầu Tiên</h3>
        <p>Chính thức ra mắt phòng khám thú cưng đầu tiên tại khu vực ngoại thành với đội ngũ y bác sĩ giàu kinh nghiệm và tận tâm với nghề.</p>
    </div>
</div>

<div class="timeline-item right reveal-right">
    <div class="timeline-content">
        <span class="year">2021</span>
        <h3>Nâng Cấp Hệ Thống Chẩn Đoán</h3>
        <p>Đầu tư đồng bộ hệ thống chẩn đoán hình ảnh tiên tiến, hỗ trợ tối đa trong việc tầm soát và đưa ra phác đồ điều trị chính xác cho thú cưng.</p>
    </div>
</div>

<div class="timeline-item left reveal-left">
    <div class="timeline-content">
        <span class="year">2022</span>
        <h3>Mở Rộng Quy Mô & Dịch Vụ Cấp Cứu</h3>
        <p>Khai trương chi nhánh thứ 5 và thành lập Trung tâm cấp cứu thú cưng chuyên biệt, hoạt động 24/7 để đáp ứng mọi nhu cầu khẩn cấp.</p>
    </div>
</div>

<div class="timeline-item right reveal-right">
    <div class="timeline-content">
        <span class="year">2024</span>
        <h3>Khẳng Định Vị Thế Thương Hiệu</h3>
        <p>Trở thành hệ thống chăm sóc thú cưng hàng đầu với sự tin tưởng của hơn 10.000 khách hàng, khẳng định chất lượng và uy tín trên thị trường.</p>
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
       <script>
    /**
     * Tuyệt kỹ: Intersection Observer (Bộ quan sát giao thoa rứa mô)
     * Giúp các phần tử "xuất chiêu" (hiện ra) khi quan khách roll chuột tới rứa.
     */
    document.addEventListener('DOMContentLoaded', function() {
        const revealElements = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { 
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px' /* Kích hoạt sớm một chút cho mượt rứa */
        });

        revealElements.forEach(el => observer.observe(el));
    });
</script>
    <?php
    include('../includes/footer.php'); // Bao gồm navbar
          ?>
</body>
   

</html> <script src="../assets/js/formHome.js"></script> <!-- Liên kết với file app.js -->

<?php mysqli_close($conn); ?>
