<?php
session_start();
include("../includes/db_connect.php");
include("../includes/header.php");

// Kiểm tra yêu cầu đăng nhập (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST["email"];
    $password = md5($_POST["password"]);

    // Chuẩn bị câu truy vấn SQL với placeholder
    $query = "SELECT * FROM nguoidung WHERE email = ? AND mat_khau = ?";
    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "ss", $email, $password);

    // Thực thi câu truy vấn
    mysqli_stmt_execute($stmt);

    // Lấy kết quả
    $result = mysqli_stmt_get_result($stmt);

    // Kiểm tra xem có người dùng nào với email và mật khẩu này không
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Lưu thông tin người dùng vào cookie (ví dụ: id và email)
        setcookie("user_id", $user['id'], time() + 3600, "/");
        setcookie("user_email", $user['email'], time() + 3600, "/");
        setcookie("user_name", $user['ho_ten'], time() + 3600, "/");
        // Chuyển hướng sang trang chủ
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Email hoặc mật khẩu sai.";
    }

    // Đóng kết nối
    mysqli_stmt_close($stmt);
}
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Chào Mừng Trở Lại</h1>
            <p>Đăng nhập để tiếp tục sử dụng dịch vụ</p>
        </div>

        <form id="loginForm" method="post" action="login.php">
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" placeholder="Nhập địa chỉ email của bạn" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu của bạn" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">👁️</button>
                </div>
            </div>
              <?php if (isset($error_message)): ?>
            <div class="error-messagee" style="color:red"><?php echo $error_message; ?></div>
        <?php endif; ?>
            <div class="remember-forgot">
                <a href="#" class="forgot-password">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="login-btn">Đăng Nhập</button>
        </form>

        <div class="divider">
            <span>hoặc đăng nhập với</span>
        </div>

        <div class="social-login">
            <button class="social-btn google" onclick="socialLogin('google')">G</button>
            <button class="social-btn facebook" onclick="socialLogin('facebook')">f</button>
        </div>

        <div class="signup-link">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
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

        function socialLogin(platform) {
            alert('Đăng nhập với ' + platform + ' (Chức năng demo)');
        }
    </script>
</body>
</html>
<?php
include("../includes/footer.php");

?>