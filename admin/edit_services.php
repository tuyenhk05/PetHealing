<?php
include('../includes/db_connect.php');

// Kiểm tra quyền hạn (Role-based Access Control)
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
$isAdmin = ($vai_tro == 'Admin');

if (!$isAdmin) {
    include "../includes/admin_header.php";
    echo "<div id='content' class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm border-0 rounded-4'>
                <h1 class='display-4'><i class='fa-solid fa-hand-dots'></i></h1>
                <h3>Cấm địa! Huynh không có lệnh bài Admin rứa mô!</h3>
                <p>Khu vực này chỉ dành cho các bậc trưởng lão có quyền năng cao nhất.</p>
                <a href='manage_services.php' class='btn btn-outline-danger mt-3 rounded-pill'>Quay lại trang quản lý</a>
            </div>
          </div>";
    exit();
}

// Lấy ID dịch vụ từ URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Truy vấn dữ liệu hiện tại (Fetching Data) dùng Prepared Statement
    $stmt = $conn->prepare("SELECT * FROM dichvu WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();
    $stmt->close();

    if (!$service) {
        die("<div class='container mt-5 text-center'>❌ Không tìm thấy dịch vụ này trong sử sách rứa mô!</div>");
    }
} else {
    header("Location: manage_services.php");
    exit();
}

// Cập nhật thông tin dịch vụ (Update logic)
if (isset($_POST['update'])) {
    $ten_dich_vu = $_POST['ten_dich_vu'];
    $loai = $_POST['loai'];
    $gia = $_POST['gia'];
    $mo_ta = $_POST['mo_ta'];

    // Xử lý ảnh (Image handling)
    $image_new_name = $service['image']; // Mặc định dùng ảnh cũ

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($image_extension, $valid_extensions)) {
            $image_new_name = uniqid('DV_UPD_', true) . '.' . $image_extension;
            $image_upload_path = "../assets/image/" . $image_new_name;

            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                // Upload thành công rứa!
            }
        }
    }

    // Cập nhật vào cơ sở dữ liệu
    $stmt = $conn->prepare("UPDATE dichvu SET ten_dich_vu = ?, loai = ?, gia = ?, mo_ta = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $ten_dich_vu, $loai, $gia, $mo_ta, $image_new_name, $id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: manage_services.php?status=success&message=Cập nhật dịch vụ thành công rứa mô!");
        exit();
    } else {
        $error = "Lỗi khi cập nhật rứa: " . $conn->error;
    }
}

include "../includes/admin_header.php";
?>

<style>
    /* CSS CHUẨN HÓA PETHEALING */
    :root {
        --pet-green: #2EB292;
        --pet-hover: #248f76;
        --pet-orange: #e67e22;
        --bg-light: #f4f7f6;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--bg-light);
    }

    #content {
        padding: 30px 40px; /* Padding chuẩn né Sidebar */
        transition: all 0.3s ease;
    }

    .section-title {
        color: var(--pet-green);
        font-weight: 700;
        position: relative;
        padding-bottom: 12px;
        margin-bottom: 30px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        left: 0; bottom: 0;
        width: 60px; height: 5px;
        background-color: var(--pet-green);
        border-radius: 5px;
    }

    .card-edit {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 45px rgba(0,0,0,0.07);
        background: white;
    }

    .form-label {
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        transition: 0.3s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--pet-green);
        box-shadow: 0 0 0 0.25rem rgba(46, 178, 146, 0.1);
    }

    .btn-update {
        background-color: var(--pet-green);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 40px;
        font-weight: 700;
        transition: 0.3s;
    }

    .btn-update:hover {
        background-color: var(--pet-hover);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(46, 178, 146, 0.3);
    }

    .current-img-preview {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 15px;
        text-align: center;
        border: 2px dashed #ddd;
    }

    .current-img-preview img {
        border-radius: 12px;
        max-height: 200px;
        object-fit: cover;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .back-link {
        color: #888;
        text-decoration: none;
        transition: 0.3s;
        font-weight: 500;
    }

    .back-link:hover {
        color: var(--pet-green);
    }
</style>
    <title>PetHealing - Chỉnh sửa dịch vụ</title>

<div id="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header Title -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title mb-0">Cập Nhật Dịch Vụ</h2>
                    <a href="manage_services.php" class="back-link">
                        <i class="fa-solid fa-arrow-left-long me-2"></i> Quay lại danh sách
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger rounded-3"><?= $error ?></div>
                <?php endif; ?>

                <!-- Form Card -->
                <div class="card card-edit">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row g-4">
                                <!-- Tên dịch vụ -->
                                <div class="col-md-8">
                                    <label class="form-label">Tên gói dịch vụ</label>
                                    <input type="text" name="ten_dich_vu" class="form-control" 
                                           value="<?= htmlspecialchars($service['ten_dich_vu']) ?>" required>
                                </div>

                                <!-- Loại dịch vụ -->
                                <div class="col-md-4">
                                    <label class="form-label">Phân loại</label>
                                    <select name="loai" class="form-select" required>
                                        <option value="cham-soc" <?= ($service['loai'] == 'cham-soc') ? 'selected' : '' ?>>Chăm sóc</option>
                                        <option value="kham-chua-benh" <?= ($service['loai'] == 'kham-chua-benh') ? 'selected' : '' ?>>Khám chữa bệnh</option>
                                        <option value="van-chuyen" <?= ($service['loai'] == 'van-chuyen') ? 'selected' : '' ?>>Vận chuyển</option>
                                    </select>
                                </div>

                                <!-- Giá -->
                                <div class="col-md-12">
                                    <label class="form-label">Giá dịch vụ (VND)</label>
                                    <div class="input-group">
                                        <input type="number" name="gia" class="form-control" 
                                               value="<?= $service['gia'] ?>" required>
                                        <span class="input-group-text bg-light text-success fw-bold">₫</span>
                                    </div>
                                </div>

                                <!-- Mô tả -->
                                <div class="col-md-12">
                                    <label class="form-label">Mô tả chi tiết</label>
                                    <textarea name="mo_ta" class="form-control" rows="4" required><?= htmlspecialchars($service['mo_ta']) ?></textarea>
                                </div>

                                <!-- Ảnh hiện tại -->
                                <div class="col-md-5">
                                    <label class="form-label d-block text-center">Hình ảnh hiện tại</label>
                                    <div class="current-img-preview">
                                        <?php
                                        $imagePath = $service['image'] ? "../assets/image/" . $service['image'] : "../assets/image/" . $service['ten_dich_vu'] . ".jpg";
                                        ?>
                                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="Service Preview">
                                    </div>
                                </div>

                                <!-- Tải ảnh mới -->
                                <div class="col-md-7">
                                    <label class="form-label">Cập nhật ảnh đại diện mới</label>
                                    <div class="h-100 d-flex flex-column justify-content-center">
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                        <p class="small text-muted mt-3">
                                            <i class="fa-solid fa-circle-info me-1"></i> 
                                            Để trống nếu không muốn thay đổi "nhan sắc" cho dịch vụ này nha.
                                        </p>
                                    </div>
                                </div>

                                <!-- Nút bấm -->
                                <div class="col-12 text-center mt-5">
                                    <button type="submit" name="update" class="btn btn-update px-5 shadow-sm">
                                        <i class="fa-solid fa-cloud-arrow-up me-2"></i> Kích Hoạt Thay Đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted small">© Hệ thống quản trị PetHealing - Chăm sóc tận tâm rứa mô!</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>