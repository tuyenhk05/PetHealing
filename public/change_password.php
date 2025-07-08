<?php
include('../includes/db_connect.php');
session_start();

$user_id = isset($_COOKIE['user_id']) ? intval($_COOKIE['user_id']) : 0;
$vai_tro = isset($_COOKIE['vai_tro']) ? $_COOKIE['vai_tro'] : '';
if ($vai_tro !== 'Admin') {
    echo "<h2>\u274c Bạn không có quyền truy cập trang này.</h2>";
    exit();
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_pass = md5($_POST['old_password']);
    $new_pass = md5($_POST['new_password']);
    $confirm_pass = md5($_POST['confirm_password']);

    $sql = "SELECT * FROM nguoidung WHERE id = $user_id AND mat_khau = '$old_pass'";
    $res = $conn->query($sql);

    if ($res && $res->num_rows === 1) {
        if ($new_pass === $confirm_pass) {
            $conn->query("UPDATE nguoidung SET mat_khau = '$new_pass' WHERE id = $user_id");
            $msg = "<p style='color:green;'> Đổi mật khẩu thành công!</p>";
        } else {
            $msg = "<p style='color:red;'> Mật khẩu mới và xác nhận không khớp.</p>";
        }
    } else {
        $msg = "<p style='color:red;'> Mật khẩu cũ không đúng.</p>";
    }
}
include('../includes/header.php');

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .back-button {
            background: #34C9A5;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
        }

            .back-button i {
                margin-right: 6px;
            }

            .back-button:hover {
                background: #22866E;
            }
    </style>
</head>
<body>
        <div style="max-width: 1100px; margin: auto">

     <button onclick="window.history.back()" class="back-button">
        <i class="fas fa-arrow-left"></i> Quay lại
    </button>
    <div class="form-container">
        <h2>Đổi mật khẩu</h2>
        <?= $msg ?>
        <form method="post">
            <label>Mật khẩu hiện tại</label>
            <input type="password" name="old_password" required>
            <label>Mật khẩu mới</label>
            <input type="password" name="new_password" required>
            <label>Nhập lại mật khẩu mới</label>
            <input type="password" name="confirm_password" required>
            <button type="submit">Cập nhật mật khẩu</button>
        </form>
    </div>
            </div>
</body>
</html>
<?php
include('../includes/footer.php');
?>