<?php 
    include('../includes/db_connect.php');
    $sql = "SELECT * FROM thucan ";
    $result = mysqli_query($conn, $sql);
    $sql2  = "SELECT * FROM phukien";
    $query = mysqli_query($conn, $sql2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHealing - Trang chủ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/category.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Liên kết với jQuery -->
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
                    <li><a href="about.php">Cửa hàng</a></li>
                    <li><a href="contact.php">Liên hệ</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="inner-subnav">
        <div class="overlay">
            <h1>Cửa hàng <span>thú cưng</span></h1>
            <p>Khám phá các sản phẩm chất lượng cao dành cho thú cưng của bạn</p>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Tìm kiếm sản phẩm..." />
            </div>

        </div>
    </div>


    <!-- Danh mục sản phẩmphẩm -->
     <div class="container-category">
        <div class="store">
            <div class="category">
                <div class="category-filter">
                    <h3><span class="icon">🧃</span> Danh mục</h3>
                    <ul class="category-list">
                        <li class="active">Tất cả</li>
                        <li>Thức ăn</li>
                        <li>Phụ kiện</li>
                        <li>Đồ chơi</li>
                        <li>Sức khỏe</li>
                        <li>Làm đẹp</li>
                    </ul>
                </div>
                <div class="product">
                    <?php 
                        while($row = mysqli_fetch_assoc($result)) {
                            ?>
                    <div class="card">
                        <img src="../assets/image/<?php echo $row['ten']; ?>.jpg" alt="<?php echo $row['ten']; ?>">

                        <div class="card-content">
                            <h3><?php echo $row['ten'] ;?> </h3>
                            <p><?php echo $row['mo_ta'] ?></p>
                            <span><?php echo $row['gia']; ?>VNĐ</span>
                            
                        </div>
                        <div>
                            <div class="actions">
                                <a href="#" class="details">Chi tiết</a>
                                <button class="btn-filled"><i class="fa fa-shopping-cart"></i> Thêm</button>
                            </div>
                        </div>
                    </div> 
                    <?php } ?>
                    <?php 
                        while($row2 = mysqli_fetch_assoc($query)) {
                            ?>
                    <div class="card ">
                        <img src="../assets/image/<?php echo $row2['ten']; ?>.jpg" alt="<?php echo $row2['ten']; ?>">

                        <div class="card-content">
                            <h3><?php echo $row2['ten'] ;?></h3>
                            <p><?php echo $row2['mo_ta'] ?></p>
                            <span><?php echo $row2['gia']; ?> VNĐ</span>
                            
                        </div>
                        <div>
                            <div class="actions">
                                <a href="#" class="details">Chi tiết</a>
                                <button class="btn-filled"><i class="fa fa-shopping-cart"></i> Thêm</button>
                            </div>
                        </div>
                    </div> 
                    <?php } ?>

                </div>
            </div>
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