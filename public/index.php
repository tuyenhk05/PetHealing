<?php
include('../includes/db_connect.php');  // Kết nối cơ sở dữ liệu
include('../includes/header.php'); // Bao gồm header

// Truy vấn dữ liệu cần hiển thị (ví dụ: các dịch vụ)
$querydv = "SELECT * FROM dichvu LIMIT 3";
$result = mysqli_query($conn, $querydv);

$querybs = "SELECT * FROM BacSi LIMIT 3";
$resultBs = mysqli_query($conn, $querybs);

$querypk = "SELECT * FROM PhuKien LIMIT 4"; // Fetch the first 4 products, adjust as needed
$resultPK = mysqli_query($conn, $querypk);

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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>




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
        <div class="container">
            <div class="content_1">
                <i class="fa-solid fa-heart"></i>
                <h4>Chăm sóc tận tâm</h4>
                <p>Đội ngũ bác sĩ và nhân viên tận tâm, luôn đặt sức khỏe của thú cưng lên hàng đầu.</p>
            </div>
            <div class="content_2">
                <i class="fa-solid fa-shield"></i>
                <h4>Trang thiết bị hiện đại</h4>
                <p>Trang bị công nghệ tiên tiến để đảm bảo chất lượng chẩn đoán và điều trị tốt nhất.</p>
            </div> <div class="content_3">
                <i class="fa-regular fa-clock"></i>
                <h4>Hỗ trợ 24/7</h4>
                <p>Luôn sẵn sàng hỗ trợ trong trường hợp khẩn cấp, bất kể ngày đêm.</p>
            </div>
        </div>
    </section>
    <section class="featured-services">
        <div class="container">
            <h2>Dịch vụ nổi bật</h2>
            <div class="services-grid"><?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="service-card">
                    <h3><?php echo $row['ten_dich_vu']; ?></h3>
                    <p><?php echo $row['mo_ta']; ?></p>
                    <a href="services.php" class="btn-primary">Tìm hiểu thêm</a>
                </div><?php } ?>
            </div>
        </div>
    </section>

    <section class="appointment">
        <div class="container">
             <div class="content_app">
                <h2>Đặt lịch hẹn ngay</h2>
                <p>Đặt lịch hẹn trực tiếp để được phục vụ tốt nhất. Chúng tôi sẽ liên hệ xác nhận trong vòng 24 giờ.</p>
            </div>
            <div class="appointment_main">

            <div class="form-container">
               
                <form id="appointment-form">
                    <div class="form-group">
                        <label for="full-name">Họ tên</label>
                        <input type="text" id="full-name" name="full-name" placeholder="Nguyễn Văn A" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" placeholder="0123456789" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="example@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="pet-name">Tên thú cưng</label>
                        <input type="text" id="pet-name" name="pet-name"  placeholder="Mèo/Chó..." required>
                    </div>
                    <div class="form-group">
                        <label for="pet-type">Loại thú cưng</label>
                            <select id="pet-type" name="pet-type" required>
                                <option value="" disabled selected>Chọn</option>
                                <option value="dog">Chó</option>
                                <option value="cat">Mèo</option>
                                <option value="other">Khác</option>
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="appointment-date">Ngày hẹn</label>
                        <input type="date" id="appointment-date" name="appointment-date" required>
                    </div>
                    <div class="form-group">
                        <label for="appointment-time">Giờ hẹn</label>
                        <input type="time" id="appointment-time" name="appointment-time" required>
                    </div>
                    <div class="form-group">
                     
                            <label for="service">Dịch vụ</label>
                            <select id="service" name="service" required>
                                <option value="" disabled selected>Chọn dịch vụ</option>
                                <option value="grooming">Cắt tỉa lông</option>
                                <option value="checkup">Kiểm tra sức khỏe</option>
                                <option value="training">Huấn luyện</option>
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Ghi chú</label>
                        <textarea id="notes" name="notes" placeholder="Mô tả triệu chứng hoặc yêu cầu đặc biệt"></textarea>
                    </div>
                  
                    <button class=" button_home">
                        Đặt lịch ngay
                    </button>

                   <div id="message-container" class="message-container"></div>
                    
           
                </form>
            </div>
            <div class="appointment_image">
                <img src="../assets/image/form-post.jpeg" alt="Đặt lịch hẹn" />
            </div>
                </div>

        </div>
    </section>
    <section class="team">
        <div class="container">
            <div class="team_content">
                <h2>Đội ngũ bác sĩ </h2>
                <p>Đội ngũ bác sĩ chuyên nghiệp, giàu kinh nghiệm, luôn sẵn sàng chăm sóc sức khỏe cho thú cưng của bạn.</p>
            </div>
            <?php while ($row = mysqli_fetch_assoc($resultBs)) { ?>
            <div class="team-member">
                <!-- Assuming each doctor has an image column or use a placeholder image -->
                <img src="../assets/image/<?php echo $row['ho_ten']; ?>.jpg" alt="<?php echo $row['ho_ten']; ?>">
                <h4><?php echo $row['ho_ten']; ?></h4>
                <p><?php echo $row['chuyen_mon']; ?></p>
                <a href="doctor_details.php?id=<?php echo $row['id']; ?>" class="btn-primary">Xem thông tin</a>
            </div><?php } ?>
        </div>
    <div class="view-all">
        <a href="all_doctors.php">Xem tất cả bác sĩ</a>
    </div>
</section>

    <section class="product-store">
    <div class="container">
        <h2>Cửa hàng thú cưng</h2>
        <p>Những sản phẩm chất lượng cao dành cho thú cưng của bạn</p>
        
        <div class="products-grid">
            <?php while ($row = mysqli_fetch_assoc($resultPK)) { ?>
                <div class="product-card">
                    <img src="../assets/image/<?php echo $row['ten']; ?>.jpg" alt="<?php echo $row['ten']; ?>"> <!-- Placeholder for product images -->
                    <h4><?php echo $row['ten']; ?></h4>
                    <p><?php echo number_format($row['gia'], 0, ',', '.'); ?> đ</p>
                    <a href="product_details.php?id=<?php echo $row['id']; ?>" class="btn-primary">Chi tiết</a>
                    <a href="#" class="btn-secondary">Thêm</a>
                </div>
            <?php } ?>
        </div>
        
        <div class="view-all">
            <a href="all_products.php">Xem tất cả sản phẩm</a>
        </div>
    </div>
</section>

       
    <?php
    include('../includes/footer.php'); // Bao gồm navbar
          ?>
</body>
   

</html> <script src="../assets/js/formHome.js"></script> <!-- Liên kết với file app.js -->

<?php mysqli_close($conn); ?>
