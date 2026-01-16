<?php
include('../includes/db_connect.php');

// Kiểm tra quyền hạn (Role-based Access Control)
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
$isAdmin = ($vai_tro == 'Admin');

if (!$isAdmin) {
    include "../includes/admin_header.php";
    echo "<div id='content' class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm'>
                <h1 class='display-4'><i class='fa-solid fa-ban'></i></h1>
                <h3>Cấm địa! Huynh không thể vào đây rứa mô!</h3>
                <p>Khu vực này chỉ dành cho Admin có lệnh bài.</p>
                <a href='manage_phukien.php' class='btn btn-outline-danger mt-3'>Quay lại trang quản lý</a>
            </div>
          </div>";
    exit();
}

// Lấy ID từ URL (Get Product ID)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Truy vấn thông tin hiện tại (Fetch current data)
    $stmt = $conn->prepare("SELECT * FROM phukien WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        die("<div class='container mt-5 text-center'>❌ Không tìm thấy sản phẩm này trong sử sách rứa mô!</div>");
    }
} else {
    header("Location: manage_phukien.php");
    exit();
}

// Cập nhật thông tin (Update logic)
if (isset($_POST['capnhat'])) {
    $ten = $_POST['ten'];
    $danh_cho_loai = $_POST['danh_cho_loai'];
    $mo_ta = $_POST['mo_ta'];
    $gia = $_POST['gia'];
    $xuat_su = $_POST['xuat_su'];
    $chat_lieu = $_POST['chat_lieu'];
    $cong_dung = $_POST['cong_dung'];
    $phu_hop = $_POST['phu_hop_voi_tuoi_thu_cung'];
    $soluong = (int) $_POST['so_luong'];

    // Xử lý Image Upload (Cập nhật ảnh)
    $image_new_name = $row['image']; // Mặc định dùng ảnh cũ

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($image_extension, $valid_extensions)) {
            $image_new_name = uniqid('PK_', true) . '.' . $image_extension;
            $image_upload_path = "../assets/image/" . $image_new_name;

            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                // Có thể xóa ảnh cũ ở đây nếu muốn tiết kiệm bộ nhớ
            }
        }
    }

    $stmt = $conn->prepare("UPDATE phukien SET ten = ?, danh_cho_loai = ?, gia = ?, mo_ta = ?, xuat_su = ?, chat_lieu = ?, cong_dung = ?, phu_hop_voi_tuoi_thu_cung = ?, so_luong = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssssssssssi", $ten, $danh_cho_loai, $gia, $mo_ta, $xuat_su, $chat_lieu, $cong_dung, $phu_hop, $soluong, $image_new_name, $id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: manage_phukien.php?status=updated");
        exit();
    } else {
        $error = "Lỗi khi cập nhật rứa mô: " . $conn->error;
    }
}

include "../includes/admin_header.php";
?>

<style>
    :root {
        --pet-green: #2EB292;
        --pet-hover: #248f76;
    }
    
    #content {
        padding: 30px 40px;
        transition: all 0.3s ease;
    }

    .card-edit {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.08);
        background: #fff;
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

    .form-label {
        font-weight: 600;
        color: #555;
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
        transition: 0.3s;
    }

    .btn-update:hover {
        background-color: var(--pet-hover);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(46, 178, 146, 0.3);
    }

    .current-img-box {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 15px;
        text-align: center;
        border: 2px dashed #ddd;
    }

    .current-img-box img {
        border-radius: 10px;
        max-height: 180px;
        object-fit: cover;
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

<div id="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title mb-0">Chỉnh Sửa Phụ Kiện</h2>
                    <a href="manage_phukien.php" class="back-link">
                        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <div class="card card-edit">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row g-4">
                                <!-- Tên sản phẩm -->
                                <div class="col-md-6">
                                    <label class="form-label">Tên sản phẩm</label>
                                    <input type="text" name="ten" class="form-control" value="<?= htmlspecialchars($row['ten']) ?>" required>
                                </div>

                                <!-- Loại thú cưng -->
                                <div class="col-md-6">
                                    <label class="form-label">Dành cho loại</label>
                                    <input type="text" name="danh_cho_loai" class="form-control" value="<?= htmlspecialchars($row['danh_cho_loai']) ?>" required>
                                </div>

                                <!-- Giá -->
                                <div class="col-md-4">
                                    <label class="form-label">Giá bán (VND)</label>
                                    <div class="input-group">
                                        <input type="number" name="gia" class="form-control" value="<?= $row['gia'] ?>" required>
                                        <span class="input-group-text">₫</span>
                                    </div>
                                </div>

                                <!-- Số lượng -->
                                <div class="col-md-4">
                                    <label class="form-label">Số lượng tồn kho</label>
                                    <input type="number" name="so_luong" class="form-control" value="<?= $row['so_luong'] ?>" required>
                                </div>

                                <!-- Xuất xứ -->
                                <div class="col-md-4">
                                    <label class="form-label">Xuất xứ</label>
                                    <input type="text" name="xuat_su" class="form-control" value="<?= htmlspecialchars($row['xuat_su']) ?>" required>
                                </div>

                                <!-- Chất liệu -->
                                <div class="col-md-6">
                                    <label class="form-label">Chất liệu</label>
                                    <input type="text" name="chat_lieu" class="form-control" value="<?= htmlspecialchars($row['chat_lieu']) ?>" required>
                                </div>

                                <!-- Độ tuổi -->
                                <div class="col-md-6">
                                    <label class="form-label">Độ tuổi phù hợp</label>
                                    <input type="text" name="phu_hop_voi_tuoi_thu_cung" class="form-control" value="<?= htmlspecialchars($row['phu_hop_voi_tuoi_thu_cung']) ?>" required>
                                </div>

                                <!-- Mô tả -->
                                <div class="col-md-6">
                                    <label class="form-label">Mô tả sản phẩm</label>
                                    <textarea name="mo_ta" class="form-control" rows="4" required><?= htmlspecialchars($row['mo_ta']) ?></textarea>
                                </div>

                                <!-- Công dụng -->
                                <div class="col-md-6">
                                    <label class="form-label">Công dụng</label>
                                    <textarea name="cong_dung" class="form-control" rows="4" required><?= htmlspecialchars($row['cong_dung']) ?></textarea>
                                </div>

                                <!-- Hình ảnh hiện tại -->
                                <div class="col-md-4">
                                    <label class="form-label d-block text-center">Hình ảnh hiện tại</label>
                                    <div class="current-img-box">
                                        <img src="../assets/image/<?= !empty($row['image']) ? $row['image'] : 'default.jpg' ?>" alt="Current Product">
                                    </div>
                                </div>

                                <!-- Tải ảnh mới -->
                                <div class="col-md-8">
                                    <label class="form-label">Cập nhật hình ảnh mới (Để trống nếu không đổi)</label>
                                    <div class="h-100 d-flex flex-column justify-content-center">
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                        <p class="small text-muted mt-2"><i class="fa-solid fa-circle-info"></i> Chỉ hỗ trợ định dạng JPG, PNG, WEBP rứa mô.</p>
                                    </div>
                                </div>

                                <!-- Nút bấm -->
                                <div class="col-12 text-center mt-5">
                                    <button type="submit" name="capnhat" class="btn btn-update px-5 shadow">
                                        <i class="fa-solid fa-check-double me-2"></i> Lưu Thay Đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>