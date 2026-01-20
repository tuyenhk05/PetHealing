<?php
include('../includes/db_connect.php');

// Kiểm tra quyền hạn (Role-based Access Control)
$user_id = isset($_COOKIE['user_id']) ? intval($_COOKIE['user_id']) : 0;
$vai_tro = isset($_COOKIE['vai_tro']) ? $_COOKIE['vai_tro'] : '';
$isAdmin = ($vai_tro === 'Admin');

if (!$isAdmin) {
    include "../includes/admin_header.php";
    echo "<div id='content' class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm border-0 rounded-4'>
                <h1 class='display-4'><i class='fa-solid fa-user-lock'></i></h1>
                <h3>Thông báo: Bạn không có quyền truy cập mật đạo này!</h3>
                <p>Chỉ dành cho các bậc trưởng lão Quản trị viên.</p>
                <a href='index.php' class='btn btn-outline-danger mt-3 rounded-pill'>Quay lại Dashboard</a>
            </div>
          </div>";
    exit();
}

$msg = "";
$status = "";

// Tầng xử lý logic cập nhật mật khẩu (Update Logic)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Thuật ngữ: Password Hashing (Mã hóa mật khẩu)
    $old_pass = md5($_POST['old_password']);
    $new_pass = md5($_POST['new_password']);
    $confirm_pass = md5($_POST['confirm_password']);

    // Kiểm tra mật khẩu cũ (Validation)
    $sql = "SELECT * FROM nguoidung WHERE id = $user_id AND mat_khau = '$old_pass'";
    $res = $conn->query($sql);

    if ($res && $res->num_rows === 1) {
        if ($new_pass === $confirm_pass) {
            // Cập nhật mật khẩu mới vào Database
            $conn->query("UPDATE nguoidung SET mat_khau = '$new_pass' WHERE id = $user_id");
            $msg = "Chúc mừng Huynh! Đổi mật khẩu thành công rứa mô.";
            $status = "success";
        } else {
            $msg = "Mật khẩu mới và xác nhận không khớp, Huynh kiểm tra lại nhé!";
            $status = "danger";
        }
    } else {
        $msg = "Mật khẩu cũ không chính xác, Huynh nhớ kỹ lại xem nào.";
        $status = "danger";
    }
}

include "../includes/admin_header.php";
?>
    <title>PetHealing - Thay đổi mật khẩu</title>

<style>
    /* CSS CHUẨN HÓA HỆ THỐNG QUẢN TRỊ PETHEALING */
    :root {
        --pet-green: #2EB292;
        --pet-hover: #248f76;
        --bg-light: #f4f7f6;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--bg-light);
    }

    #content {
        padding: 30px 40px;
        transition: all 0.3s ease;
    }

    .section-title {
        color: var(--pet-green);
        font-weight: 700;
        position: relative;
        padding-bottom: 10px;
        margin-bottom: 30px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        left: 0; bottom: 0;
        width: 50px; height: 4px;
        background-color: var(--pet-green);
        border-radius: 2px;
    }

    .card-password {
        max-width: 550px;
        margin: 0 auto;
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.08);
        background: white;
    }

    .form-label {
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        transition: 0.3s;
    }

    .form-control:focus {
        border-color: var(--pet-green);
        box-shadow: 0 0 0 0.25rem rgba(46, 178, 146, 0.1);
    }

    .btn-update {
        background-color: var(--pet-green);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 30px;
        font-weight: 700;
        width: 100%;
        transition: 0.3s;
    }

    .btn-update:hover {
        background-color: var(--pet-hover);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(46, 178, 146, 0.3);
    }

    .icon-lock {
        font-size: 3rem;
        color: var(--pet-green);
        margin-bottom: 20px;
        opacity: 0.8;
    }
</style>

<div id="content">
    <div class="container">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <h2 class="section-title d-inline-block">Thiết lập bảo mật</h2>
            <p class="text-secondary small mt-2">Cập nhật mật khẩu định kỳ để bảo vệ long mạch PetHealing rứa mô!</p>
        </div>

        <div class="card card-password">
            <div class="card-body p-4 p-md-5">
                <div class="text-center">
                    <div class="icon-lock">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>

                <?php if ($msg !== ""): ?>
                    <div class="alert alert-<?= $status ?> alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                        <i class="fa-solid <?= ($status == 'success') ? 'fa-circle-check' : 'fa-circle-exclamation' ?> me-2"></i>
                        <?= $msg ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-key text-muted"></i></span>
                            <input type="password" name="old_password" class="form-control border-start-0 rounded-end-3" 
                                   placeholder="Nhập mật khẩu đang dùng..." required>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    <div class="mb-4">
                        <label class="form-label">Mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-lock text-muted"></i></span>
                            <input type="password" name="new_password" class="form-control border-start-0 rounded-end-3" 
                                   placeholder="Tạo mật khẩu mới rứa mô..." required>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fa-solid fa-check-double text-muted"></i></span>
                            <input type="password" name="confirm_password" class="form-control border-start-0 rounded-end-3" 
                                   placeholder="Nhập lại mật khẩu mới..." required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-update shadow-sm">
                        <i class="fa-solid fa-shield-virus me-2"></i> Cập nhật ngay
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="index.php" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay về Dashboard
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>