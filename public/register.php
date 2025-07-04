<?php
session_start();
include("../includes/db_connect.php");

// Xử lý đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = md5(trim($_POST["password"]));
    $name = trim($_POST["name"]);
    $role = "user"; // Vai trò mặc định

    // Kiểm tra email đã tồn tại chưa
    $check_query = "SELECT id FROM nguoidung WHERE email = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Email đã được sử dụng.";
    } else {
        // Thêm người dùng mới
        $insert_query = "INSERT INTO nguoidung (email, so_dien_thoai, mat_khau, ho_ten, vai_tro) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "sssss", $email, $phone, $password, $name, $role);
        if (mysqli_stmt_execute($insert_stmt)) {
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Đăng ký thất bại. Vui lòng thử lại.";
        }
        mysqli_stmt_close($insert_stmt);
    }
    mysqli_stmt_close($check_stmt);
}
include("../includes/header.php");

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Tạo Tài Khoản Mới</h1>
            <p>Đăng ký để sử dụng dịch vụ</p>
        </div>
        <form id="registerForm" method="post" action="register.php">
            <div class="form-group">
                <label for="name">Họ tên</label>
                <div class="input-wrapper">
                    <input type="text" id="name" name="name" placeholder="Nhập họ tên của bạn" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" placeholder="Nhập địa chỉ email của bạn" required>
                </div>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <div class="input-wrapper">
                    <input type="text" id="phone" name="phone" placeholder="Nhập số điện thoại của bạn" required>
                </div>
            </div>
            <div class="form-group" style="width:100%"> 
                <label for="password">Mật khẩu</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu của bạn" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">👁️</button>
                </div>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="error-messagee" style="color:red"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <button type="submit" class="login-btn">Đăng Ký</button>
        </form>
        <div class="signup-link">
            Đã có tài khoản? <a href="login.php">Đăng nhập</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('.password-toggle');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = '👁️';
            }
        }
    </script>
</body>
</html>

<?php include("../includes/footer.php"); ?>
