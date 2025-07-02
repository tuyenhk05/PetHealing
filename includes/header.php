<?php
$name = isset($_COOKIE["user_name"]) ? $_COOKIE["user_name"] : "";
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
$isAdmin = ($vai_tro == 'Admin');
?>

<style>
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
        min-width: 200px;
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
</style>

<header class="header">
    <div class="container">
        <div class="logo">
            <a href="index.php"><img src="../assets/image/logo.jpg" alt="PetHealing Logo"> PetHealing </a>
        </div>
        <nav class="nav">
            <ul>
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="services.php">Dịch vụ</a></li>
                <li><a href="all_products.php">Cửa hàng</a></li>
                <li><a href="all_doctors.php">Bác sĩ</a></li>
                <li><a href="contact.php">Liên hệ</a></li>
                <li><a href="news.php">Tin tức</a></li>
                <?php if ($isAdmin) { ?>
                    <li><a href="dashboard.php">Quản lý</a></li>
                <?php } ?>

                <?php if (!empty($name)) { ?>
                    <li class="user-dropdown">
                        <div class="user-name" id="userToggle">
                            <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($name); ?>
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                        <div class="user-menu" id="userMenu">
                            <a href="history_appointments.php"><i class="fa-solid fa-clock-rotate-left"></i> &nbsp;Lịch sử đặt lịch hẹn</a>
                            <a href="#" id="logoutLink"><i class="fa-solid fa-right-from-bracket"></i> &nbsp;Đăng xuất</a>
                        </div>
                    </li>
                <?php } else { ?>
                    <li><a href="login.php"><i class="fa-regular fa-user"></i> Đăng nhập</a></li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('userToggle');
    const menu = document.getElementById('userMenu');
    const logout = document.getElementById('logoutLink');

    // Toggle menu khi click vào tên
    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });

    // Ẩn menu khi click ra ngoài
    document.addEventListener('click', function () {
        menu.style.display = 'none';
    });

    // Ngăn ẩn khi click bên trong menu
    menu.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    // Xử lý logout
    logout.addEventListener('click', function (e) {
        e.preventDefault();
        document.cookie = "user_name=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "user_email=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "vai_tro=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        window.location.href = "login.php";
    });
});
</script>
