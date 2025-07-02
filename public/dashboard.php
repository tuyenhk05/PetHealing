<?php
include "../includes/db_connect.php";
include "../includes/header.php";
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if($vai_tro=='Admin'){
$isAdmin = true;
}else{
$isAdmin = false;
    $content = "<br/><h1> Bạn không có quyền truy cập trang này. </h1><br/><br/><br/>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dasboard</title>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
    <?php if ($isAdmin) { ?>
    <div class="quan_ly">
    <h1>Dashboard</h1>
    <span>Tổng quan hoạt động phòng khám thú cưng</span>
        <div class="muc_luc">
            <div class="muc_luc_item">
                <a href="manage_appointments.php">Quản lý lịch hẹn</a>
            </div>
            <div class="muc_luc_item">
                <a href="manage_product.php">Quản lý sản phẩm</a>
            </div>
            <div class="muc_luc_item">
                <a href="manage_services.php">Quản lý dịch vụ</a>
            </div>
            <div class="muc_luc_item">
                <a href="manage_news.php">Quản lí tin tức</a>
            </div>
            <div class="muc_luc_item">
                <a href="manage_doctors.php">Quản lý Thông tin bác sĩ</a>
            </div>
            <div class="muc_luc_item">
                <a href="manage_contact.php">Quản lý Thông tin liên hệ</a>
            </div>
        </div>
        </div>
    <?php } else {
        echo $content;
    } ?>


</body>
</html>
<?php include "../includes/footer.php";
?>