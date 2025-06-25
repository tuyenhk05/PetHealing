<?php
$name = isset($_COOKIE["user_name"]) ? $_COOKIE["user_name"] : "";
?>
<style>
    .logout:hover{
       
        color:#e44646;
        font-size:20px;
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
                    <?php if(isset($name) && $name!=""){ ?>
                    <li style="color: #2EB292">
                        <i class="fa-solid fa-user"></i> <?php echo $name; ?> 
                        <i class="fa-solid fa-arrow-right-from-bracket logout" style="cursor:pointer;margin-left:5px;transition:ease 0.3s "></i>
                    </li>
                    <?php }
                          else { ?>
                    <li><a href="login.php"><i class="fa-solid fa-user"></i>   Đăng nhập</a></li>
                    <?php } ?>
                      
            
       </ul>
                </nav>
            </div>
    </header>
    <script>
          document.addEventListener('DOMContentLoaded', function() {
            const logoutIcon = document.querySelector('.logout');
            if (logoutIcon) {
                logoutIcon.addEventListener('click', function() {
                    // Xóa cookie user_name
                    document.cookie = "user_name=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    document.cookie = "user_email=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    document.cookie = "user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    // Có thể xóa thêm các cookie khác nếu cần
                    window.location.href = "login.php";
                });
        }
        });


    </script>
