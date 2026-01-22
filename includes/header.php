<?php
// Thuật ngữ: PHP_SELF - Một biến chứa tên file của script đang thực thi rứa mô.
// basename() - Hàm dùng để lấy ra cái tên file cuối cùng từ một đường dẫn rứa.
$current_page = basename($_SERVER['PHP_SELF']);

$name = isset($_COOKIE["user_name"]) ? $_COOKIE["user_name"] : "";
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
$isAdmin = ($vai_tro == 'Admin');
?>
<link rel="shortcut icon" type="image/x-icon" href="../assets/image/logo.jpg">
<style>
    * {
        box-sizing: border-box;
    }

    .header {
        background-color: #fff;
        border-bottom: 1px solid #edf2f2;
        padding: 12px 0;
        /* Thuật ngữ: Fixed Positioning - Cố định vị trí trên cùng rứa mô */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        /* Thuật ngữ: Transition - Giúp Header ẩn/hiện mượt mà rứa */
        transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    }
    .header-hidden {
        transform: translateY(-100%);
    }
    .logo a {
        font-size: 20px;
        font-weight: bold;
        text-decoration: none;
        color: #2EB292;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo img {
        height: 40px;
        border-radius: 6px;
    }

    nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        gap: 20px;
    }

    nav ul li a {
        text-decoration: none;
        color: #333;
        font-weight: 500;
        padding: 5px 0;
        position: relative;
        transition: color 0.3s ease;
    }

    /* Chiêu thức Active State - Làm sáng đường link khi đang ở trang đó rứa mô */
    nav ul li a.active {
        color: #2EB292;
        font-weight: 700;
    }

    /* Hiệu ứng gạch chân cho thêm phần uy nghi */
    nav ul li a.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #2EB292;
        border-radius: 2px;
    }

    nav ul li a:hover {
        color: #2EB292;
    }

    /* Dropdown user */
    .user-dropdown {
        position: relative;
    }

    .user-name {
        cursor: pointer;
        color: #2EB292;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background-color: #f2fefc;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .user-name:hover {
        background-color: #e0faf3;
    }

    .user-menu {
        position: absolute;
        top: 45px;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        min-width: 220px;
        display: none;
        overflow: hidden;
        z-index: 1000;
    }

    .user-menu a {
        display: block;
        padding: 12px 16px;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }

    .user-menu a:hover {
        background-color: #f0f0f0;
        color: #2EB292;
    }

    /* Menu toggle button */
    .menu-toggle {
        display: none;
        font-size: 24px;
        cursor: pointer;
        color: #2EB292;
    }

    /* Sidebar */
    .sidebar {
        position: fixed;
        top: 0;
        left: -260px;
        width: 260px;
        height: 100%;
        background-color: #fff;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        transition: left 0.3s ease;
        z-index: 1001;
    }

    .sidebar.active {
        left: 0;
    }

    .sidebar a {
        display: block;
        padding: 12px 0;
        text-decoration: none;
        color: #333;
        font-weight: 500;
    }

    .sidebar a.active {
        color: #2EB292;
        font-weight: bold;
        border-left: 4px solid #2EB292;
        padding-left: 10px;
    }

    .sidebar a:hover {
        color: #2EB292;
    }

    .sidebar .close-btn {
        font-size: 20px;
        text-align: right;
        cursor: pointer;
        margin-bottom: 20px;
    }

    @media (max-width: 1180px) {
        .nav {
            display: none;
        }

        .menu-toggle {
            display: block;
        }
    }
</style>

<header class="header" id="mainHeader">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="logo">
            <a href="index.php"><img src="../assets/image/logo.jpg" alt="Logo"> PetHealing</a>
        </div>
        <div class="menu-toggle" onclick="toggleSidebar()">☰</div>
        <nav class="nav">
            <ul>
                <!-- Huynh thấy đệ lồng ghép PHP vào class chưa rứa? Rất ảo diệu mô! -->
                <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Trang chủ</a></li>
                <li><a href="services.php" class="<?= $current_page == 'services.php' ? 'active' : '' ?>">Dịch vụ</a></li>
                <li><a href="all_products.php" class="<?= ($current_page == 'all_products.php' || $current_page == 'product_detail.php') ? 'active' : '' ?>">Cửa hàng</a></li>
                <li><a href="all_doctors.php" class="<?= $current_page == 'all_doctors.php' ? 'active' : '' ?>">Bác sĩ</a></li>
                <li><a href="contact.php" class="<?= $current_page == 'contact.php' ? 'active' : '' ?>">Liên hệ</a></li>
                <li><a href="news.php" class="<?= $current_page == 'news.php' ? 'active' : '' ?>">Tin tức</a></li>
                
                <?php if (!empty($name)) { ?>
                    <li class="user-dropdown">
                        <div class="user-name" id="userToggle">
                            <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($name); ?>
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                        <div class="user-menu" id="userMenu">
                            <a href="history_appointments.php" class="<?= $current_page == 'history_appointments.php' ? 'active' : '' ?>"><i class="fa-solid fa-clock-rotate-left"></i> &nbsp;Lịch sử đặt lịch hẹn</a>
                            <a href="order_history.php" class="<?= $current_page == 'order_history.php' ? 'active' : '' ?>"><i class="fa-solid fa-clock-rotate-left"></i> &nbsp;Lịch sử mua hàng</a>
                            <?php if ($isAdmin) { ?>
                                <a href="../admin/index.php"><i class="fa-solid fa-user-gear"></i> &nbsp;Quản lý Admin</a>
                            <?php } ?>
                            <a href="#" id="logoutLink"><i class="fa-solid fa-right-from-bracket"></i> &nbsp;Đăng xuất</a>
                        </div>
                    </li>
                <?php } else { ?>
                    <li><a href="login.php" class="<?= $current_page == 'login.php' ? 'active' : '' ?>"><i class="fa-regular fa-user"></i> Đăng nhập</a></li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</header>

<!-- Sidebar cho mobile -->
<div class="sidebar" id="sidebar">
    <div class="close-btn" onclick="toggleSidebar()">✖</div>
    <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Trang chủ</a>
    <a href="services.php" class="<?= $current_page == 'services.php' ? 'active' : '' ?>">Dịch vụ</a>
    <a href="all_products.php" class="<?= $current_page == 'all_products.php' ? 'active' : '' ?>">Cửa hàng</a>
    <a href="all_doctors.php" class="<?= $current_page == 'all_doctors.php' ? 'active' : '' ?>">Bác sĩ</a>
    <a href="contact.php" class="<?= $current_page == 'contact.php' ? 'active' : '' ?>">Liên hệ</a>
    <a href="news.php" class="<?= $current_page == 'news.php' ? 'active' : '' ?>">Tin tức</a>
    
    <?php if (!empty($name)) { ?>
        <a href="history_appointments.php" class="<?= $current_page == 'history_appointments.php' ? 'active' : '' ?>">Lịch sử đặt lịch hẹn</a>
        <a href="order_history.php" class="<?= $current_page == 'order_history.php' ? 'active' : '' ?>">Lịch sử mua hàng</a>
        <a href="#" id="logoutLinkMobile">Đăng xuất</a>
    <?php } else { ?>
        <a href="login.php" class="<?= $current_page == 'login.php' ? 'active' : '' ?>">Đăng nhập</a>
    <?php } ?>
</div>

<script>
    const toggle = document.getElementById('userToggle');
    const menu = document.getElementById('userMenu');
    const logout = document.getElementById('logoutLink');
    const logoutMobile = document.getElementById('logoutLinkMobile');

    toggle?.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', function () {
        if (menu) menu.style.display = 'none';
    });

    menu?.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    function clearCookiesAndRedirect() {
        document.cookie = "user_name=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "user_email=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "vai_tro=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        window.location.href = "login.php";
    }

    logout?.addEventListener('click', function (e) {
        e.preventDefault();
        clearCookiesAndRedirect();
    });

    logoutMobile?.addEventListener('click', function (e) {
        e.preventDefault();
        clearCookiesAndRedirect();
    });

    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("active");
    }
    let lastScrollY = window.scrollY;
    const header = document.getElementById('mainHeader');

    window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;

        if (currentScrollY > lastScrollY && currentScrollY > 100) {
            // Khi quan khách lướt xuống -> Ẩn thân rứa mô!
            header.classList.add('header-hidden');
        } else {
            // Khi quan khách lướt lên -> Hiện hình rứa!
            header.classList.remove('header-hidden');
        }

        // Cập nhật lại vị trí cũ để so sánh rứa
        lastScrollY = currentScrollY;
    });
</script>