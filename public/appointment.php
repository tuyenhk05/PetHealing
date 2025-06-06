<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHealing - appointment</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/appointment.css">
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
                    <li><a href="all_doctors.php">Bác sĩ</a></li>
                    
                    <li><a href="all_products.php">Cửa hàng</a></li>
                    <li><a href="contact.php">Liên hệ</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- navigation -->
     <div class="bg-title">
        
            <div class="bg-sub-title">
                <h1>Vì thú cưng xứng đáng được chăm sóc</h1>
                <h2>Đặt lịch hẹn ngay</h2>
                <p>Để gửi trọn yêu thương.</p>
            </div>
        
     </div>
     
    <!-- form apointment -->
     <?php 
        include "../includes/db_connect.php";
        if(isset($_POST['btn-appointment'])) {
            $name_user = $_POST['ten_khach_hang'];
            $sdt = $_POST['so_dien_thoai_khach_hang'];
            $thucung = $_POST['ten_thu_cung'];
            $loaithucung = $_POST['loai_thu_cung'];
            $bacsi = $_POST['ten_bac_si'];
            $ngayhen = $_POST['ngay_hen'];
            $giohen = $_POST['gio_hen'];
            $ghichu = $_POST['ghi_chu'];

            $sql = "INSERT INTO lichhen (ten_khach_hang , so_dien_thoai_khach_hang , ten_thu_cung , loai_thu_cung , ten_bac_si , ngay_hen , gio_hen , ghi_chu)
            VALUES ('$name_user' , '$sdt' , '$thucung' , '$loaithucung' , '$bacsi' , '$ngayhen' , '$giohen', '$ghichu') ";

            mysqli_query($conn , $sql);
        }
     ?>
     <div class="container-form">
               
                <form id="appointment-form" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="full-name">Họ tên</label>
                        <input type="text" id="full-name" name="ten_khach_hang" placeholder="Nguyễn Văn A" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" name="so_dien_thoai_khach_hang" placeholder="0123456789" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="pet-name">Tên thú cưng</label>
                        <input type="text" id="pet-name" name="ten_thu_cung"  placeholder="Mèo/Chó..." required>
                    </div>
                    <div class="form-group">
                        <label for="pet-type">Loại thú cưng</label>
                            <select id="pet-type" name="loai_thu_cung" required>
                                <option value="" disabled selected>Chọn</option>
                                <option value="dog">Chó</option>
                                <option value="cat">Mèo</option>
                                <option value="other">Khác</option>
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="full-name">Bác sĩ</label>
                         <select id="full-name" name="ten_bac_si" required>
                                <option value="" disabled selected>Chọn</option>
                                <?php 
                                    include "../includes/db_connect.php";
                                    $sql3 = "SELECT * FROM bacsi";
                                    $result3 = mysqli_query($conn, $sql3);
                                    while( $row3 = mysqli_fetch_assoc($result3)) {
                                ?>
                                    <option > <?php echo $row3['ho_ten']; ?></option>
                                <?php }?>
                                
                               
                            </select>
                        
                    </div>
                    <div class="form-group">
                        <label for="appointment-date">Ngày hẹn</label>
                        <input type="date" id="appointment-date" name="ngay_hen" required>
                    </div>
                    <div class="form-group">
                        <label for="appointment-time">Giờ hẹn</label>
                        <input type="time" id="appointment-time" name="gio_hen" required>
                    </div>
                    <div class="form-group">
                        
                            
                            <label for="service">Dịch vụ</label>
                            <select id="service" name="ten_dich_vu" required>
                                <option value="" disabled selected>Chọn dịch vụ</option>
                                <?php 
                                    include "../includes/db_connect.php";
                                    $sql2 = "SELECT * FROM dichvu";
                                    $result2 = mysqli_query($conn, $sql2);
                                    while( $row2 = mysqli_fetch_assoc($result2)) {
                                ?>
                                    <option > <?php echo $row2['ten_dich_vu']; ?></option>
                                <?php }?>
                            </select>
                        
                    </div>
                    <div class="form-group">
                        <label for="notes">Ghi chú</label>
                        <textarea id="notes" name="ghi_chu" placeholder="Mô tả triệu chứng hoặc yêu cầu đặc biệt"></textarea>
                    </div>
                    <button type="submit" name="btn-appointment">
                        Đặt lịch ngay
                    </button>

                   <div id="message-container" class="message-container"></div>
                    
           
                </form>
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
