<?php
// 1. Tầng xử lý Logic - Kiểm tra quyền hạn (Middleware Check rứa mô)
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if ($vai_tro !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

/**
 * Tuyệt kỹ Đăng xuất (Logout Handling)
 * Thuật ngữ: Session Termination (Chấm dứt phiên làm việc rứa)
 */
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Xóa sạch dấu vết Cookie trên trình duyệt rứa mô
    $cookies = ["user_name", "user_email", "user_id", "vai_tro"];
    foreach ($cookies as $c) {
        setcookie($c, "", time() - 3600, "/");
    }
    header("Location: ../public/login.php");
    exit();
}

$name = isset($_COOKIE["user_name"]) ? $_COOKIE["user_name"] : "Admin";

include "../includes/db_connect.php";
// include "../includes/admin_header.php"; // Nếu file này là file độc lập rứa mô
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/image/logo.jpg">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #2c3e50;
            --main-green: #2EB292;
            --sidebar-hover: #34495e;
            --sub-menu-bg: #1e2b37;
            --transition-hq: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        }
        body { background-color: #f4f7f6; overflow-x: hidden; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: var(--sidebar-bg);
            color: #fff;
            transition: var(--transition-hq);
            min-height: 100vh;
            position: fixed;
            z-index: 1000;
        }
        #sidebar .sidebar-header { padding: 25px; background: #1a252f; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05); }
        
        #sidebar ul li a {
            padding: 15px 25px;
            display: block;
            color: #bdc3c7;
            text-decoration: none;
            transition: 0.2s;
            border-left: 4px solid transparent;
        }
        
        #sidebar ul li a:hover, #sidebar ul li a.active-link {
            color: #fff;
            background: var(--sidebar-hover);
            border-left: 4px solid var(--main-green);
        }

        .sub-menu { background: var(--sub-menu-bg); list-style: none; padding: 0; }
        .sub-menu li a { padding: 12px 25px 12px 50px !important; font-size: 0.95rem; }

        #content {
            width: calc(100% - 260px);
            margin-left: 260px;
            transition: var(--transition-hq);
            padding: 40px;
        }

        /* Nút đăng xuất đặc biệt rứa mô */
        .logout-link { color: #e74c3c !important; font-weight: 600; }
        .logout-link:hover { background: rgba(231, 76, 60, 0.1) !important; }

        @media (max-width: 992px) {
            #sidebar { margin-left: -260px; }
            #sidebar.active { margin-left: 0; }
            #content { width: 100%; margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- SIDEBAR -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h4 class="m-0 text-white"><i class="fas fa-user-shield me-2"></i>Admin Panel</h4>
            <small class="text-muted">Chào Huynh, <?= htmlspecialchars($name) ?> rứa mô!</small>
        </div>
        <ul class="list-unstyled mt-3" >
            <li><a href="index.php"><i class="fas fa-gauge"></i> Tổng quan</a></li>
            <li><a href="manage_appointments.php"><i class="fas fa-calendar-check"></i> Quản lý lịch hẹn</a></li>
            
            <li>
                <a href="#productSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="fas fa-box-open"></i> Quản lý sản phẩm
                </a>
                <ul class="collapse list-unstyled sub-menu" id="productSubmenu">
                    <li><a href="manage_phukien.php"><i class="fa-solid fa-tags"></i> Phụ kiện</a></li>
                    <li><a href="manage_thucan.php"><i class="fa-solid fa-bowl-food"></i> Thức ăn</a></li>
                </ul>
            </li>

            <li><a href="manage_services.php"><i class="fas fa-concierge-bell"></i> Quản lý dịch vụ</a></li>
            <li><a href="manage_news.php"><i class="fas fa-newspaper"></i> Quản lý tin tức</a></li>
            <li><a href="manage_doctors.php"><i class="fas fa-user-md"></i> Quản lý bác sĩ</a></li>
            <li><a href="manage_contact.php"><i class="fas fa-envelope"></i> Quản lý liên hệ</a></li>
            <li><a href="manage_users.php"><i class="fa-solid fa-user"></i> Quản lý người dùng</a></li>
            
            <hr class="bg-light mx-3 opacity-25">
            
            <li><a href="change_password.php"><i class="fa-solid fa-lock"></i> Đổi mật khẩu</a></li>
            <li><a href="../public/index.php"><i class="fa-solid fa-house"></i> Về trang chủ</a></li>
            
            <!-- 
                TRIỆU HỒI MODAL ĐĂNG XUẤT RỨA MÔ! 
                Thuật ngữ: Event Trigger (Kích hoạt sự kiện rứa)
            -->
            <li>
                <a href="javascript:void(0)" class="logout-link" 
                   onclick="showConfirmModal('Bạn có chắc chắn muốn đăng xuất?', '?action=logout', 'danger')">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </li>
        </ul>
    </nav>

    <!-- MAIN CONTENT -->
    <div id="content">
        <nav class="d-lg-none mb-4">
            <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
        
        <!-- Nội dung Dashboard sẽ nằm ở đây rứa mô -->
        <div class="container-fluid">
             <!-- Chèn code thống kê/biểu đồ của Huynh vào đây rứa -->
        </div>
    </div>
</div>

<!-- NẠP PHÁP BẢO MODAL (Phải nằm ở cuối trang rứa mô) -->
<?php include('../includes/confirm_modal.php'); ?>

<!-- Bootstrap JS & Thần khí bổ trợ rứa -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Nhận diện trang hiện tại (Active State Tracking rứa mô)
        const currentLocation = window.location.pathname.split("/").pop();
        const menuLinks = document.querySelectorAll('#sidebar ul li a');

        menuLinks.forEach(link => {
            if (link.getAttribute('href') === currentLocation) {
                link.classList.add('active-link');
                const parentCollapse = link.closest('.collapse');
                if (parentCollapse) {
                    parentCollapse.classList.add('show');
                    const toggleBtn = document.querySelector(`[href="#${parentCollapse.id}"]`);
                    if (toggleBtn) toggleBtn.classList.add('active-link');
                }
            }
        });

        // 2. Xử lý đóng/mở sidebar trên Mobile
        document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    });
</script>
</body>
</html>