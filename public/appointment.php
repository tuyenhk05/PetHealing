<?php
include('../includes/header.php'); // Bao gồm header

    ?>
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
   
    <!-- navigation -->
     <div class="bg-title">
        
            <div class="bg-sub-title">
                <h1>Vì thú cưng xứng đáng được chăm sóc</h1>
                <h2>Đặt lịch hẹn ngay</h2>
                <p>Để gửi trọn yêu thương.</p>
            </div>
        
     </div>
     
    <!-- form apointment -->
    
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
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="example@email.com" required>
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
                    <button type="submit" class="button_home" name="btn-appointment">
                        Đặt lịch ngay
                    </button>

                   <div id="message-container" class="message-container"></div>
                    
           </form>
              
   </div>

</body>
</html>
<script src="../assets/js/formHome.js"></script> <!-- Liên kết với file app.js -->
<?php

include('../includes/footer.php'); // Bao gồm header
?>