<?php
include "../includes/db_connect.php";
include "../includes/header.php";

$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if ($vai_tro != 'Admin') {
    echo "<br/><h1> Bạn không có quyền truy cập trang này. </h1><br/><br/><br/>";
    include "../includes/footer.php";
    exit();
}

// Thống kê số lượng
$totalAppointments = $conn->query("SELECT COUNT(*) as count FROM lichhen")->fetch_assoc()['count'];
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM (SELECT id FROM phukien UNION ALL SELECT id FROM thucan) AS sp")->fetch_assoc()['count'];
$totalServices = $conn->query("SELECT COUNT(*) as count FROM dichvu")->fetch_assoc()['count'];
$totalDoctors = $conn->query("SELECT COUNT(*) as count FROM bacsi")->fetch_assoc()['count'];

// Thống kê doanh thu
$res = $conn->query("SELECT SUM(total) AS revenue FROM lichsumuahang");
$revenue = $res->fetch_assoc()['revenue'] ?? 0;

$res_month = $conn->query("SELECT SUM(total) AS revenue_month FROM lichsumuahang WHERE MONTH(order_time) = MONTH(NOW()) AND YEAR(order_time) = YEAR(NOW())");
$revenue_month = $res_month->fetch_assoc()['revenue_month'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Quản trị</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
       
    </style>
</head>
<body>
<div class="dashboard-container">
    <h1 class="dashboard-title">Dashboard Quản Trị</h1>
    <p class="dashboard-subtitle">Tổng quan hoạt động của phòng khám thú cưng</p>

    <!-- THỐNG KÊ -->
    <div class="stat-grid">
        <div class="stat-box">
            <i class="fas fa-calendar-check"></i>
            <div class="stat-number"><?= $totalAppointments ?></div>
            <div class="stat-label">Lịch hẹn</div>
        </div>
        <div class="stat-box">
            <i class="fas fa-box-open"></i>
            <div class="stat-number"><?= $totalProducts ?></div>
            <div class="stat-label">Sản phẩm</div>
        </div>
        <div class="stat-box">
            <i class="fas fa-concierge-bell"></i>
            <div class="stat-number"><?= $totalServices ?></div>
            <div class="stat-label">Dịch vụ</div>
        </div>
        <div class="stat-box">
            <i class="fas fa-user-md"></i>
            <div class="stat-number"><?= $totalDoctors ?></div>
            <div class="stat-label">Bác sĩ</div>
        </div>
        <div class="stat-box">
            <i class="fas fa-chart-line"></i>
            <div class="stat-number"><?= number_format($revenue_month, 0, '', '.') ?>₫</div>
            <div class="stat-label">Doanh thu tháng này</div>
        </div>
        <div class="stat-box">
            <i class="fas fa-sack-dollar"></i>
            <div class="stat-number"><?= number_format($revenue, 0, '', '.') ?>₫</div>
            <div class="stat-label">Tổng doanh thu</div>
        </div>
    </div>

    <!-- MENU -->
    <div class="menu-grid">
        <div class="menu-item"><a href="manage_appointments.php"><i class="fas fa-calendar-check"></i> Quản lý lịch hẹn</a></div>
        <div class="menu-item"><a href="manage_product.php"><i class="fas fa-box-open"></i> Quản lý sản phẩm</a></div>
        <div class="menu-item"><a href="manage_services.php"><i class="fas fa-concierge-bell"></i> Quản lý dịch vụ</a></div>
        <div class="menu-item"><a href="manage_news.php"><i class="fas fa-newspaper"></i> Quản lý tin tức</a></div>
        <div class="menu-item"><a href="manage_doctors.php"><i class="fas fa-user-md"></i> Quản lý bác sĩ</a></div>
        <div class="menu-item"><a href="manage_contact.php"><i class="fas fa-envelope"></i> Quản lý liên hệ</a></div>
        <div class="menu-item"><a href="manage_users.php"><i class="fa-solid fa-user"></i> Quản lý người dùng</a></div>  
        <div class="menu-item"><a href="change_password.php"><i class="fa-solid fa-lock"></i> Đổi mật khẩu</a></div>  

        <!-- <div class="menu-item"><a href="baocao_gui.php"><i class="fas fa-file-export"></i> Xuất báo cáo</a></div> -->
    </div>
</div>
</body>
</html>
<?php include "../includes/footer.php"; ?>
