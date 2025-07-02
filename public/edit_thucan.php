<?php
include('../includes/db_connect.php');
include "../includes/header.php";
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if ($vai_tro == 'Admin') {
    $isAdmin = true;
} else {
    $isAdmin = false;
    $content = "<br/><h1> Bạn không có quyền truy cập trang này. </h1><br/><br/><br/>";
}
// Lấy ID từ URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Lấy thông tin hiện tại
    $stmt = $conn->prepare("SELECT * FROM thucan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        die("❌ Không tìm thấy sản phẩm với ID này.");
    }
} else {
    header("Location: manage_thucan.php");
    exit();
}

// Cập nhật thông tin
if (isset($_POST['capnhat'])) {
    $ten = $_POST['ten'];
    $danh_cho_loai = $_POST['danh_cho_loai'];
    $mo_ta = $_POST['mo_ta'];
    $gia = $_POST['gia'];
    $xuat_su = $_POST['xuat_su'];
    $chat_lieu = $_POST['thanh_phan'];
    $cong_dung = $_POST['cong_dung'];
    $phu_hop = $_POST['phu_hop_voi_tuoi_thu_cung'];
    $soluong = $_POST['so_luong'];

    // Xử lý ảnh
    $image_name = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
    $image_tmp_name = isset($_FILES['image']['tmp_name']) ? $_FILES['image']['tmp_name'] : '';
    $image_error = isset($_FILES['image']['error']) ? $_FILES['image']['error'] : 4; // 4 = không có file

    if ($image_error === 0) {
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $image_new_name = uniqid('', true) . '.' . $image_extension;
        $image_upload_path = "../assets/image/" . $image_new_name;

        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($image_extension, $valid_extensions)) {
            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                // Cập nhật có ảnh mới
                $stmt = $conn->prepare("UPDATE thucan SET ten = ?, danh_cho_loai = ?, gia = ?, mo_ta = ?, xuat_su = ?, thanh_phan = ?, cong_dung = ?, phu_hop_voi_tuoi_thu_cung = ?, so_luong = ?, image = ? WHERE id = ?");
                $stmt->bind_param("ssssssssssi", $ten, $danh_cho_loai, $gia, $mo_ta, $xuat_su, $chat_lieu, $cong_dung, $phu_hop, $soluong, $image_new_name, $id);
                $stmt->execute();
                $stmt->close();

                header("Location: manage_thucan.php?status=success&message=Cập nhật thành công!");
                exit();
            } else {
                header("Location: manage_thucan.php?status=error&message=Lỗi tải ảnh lên.");
                exit();
            }
        } else {
            header("Location: manage_thucan.php?status=error&message=Chỉ hỗ trợ ảnh JPG, JPEG, PNG, GIF.");
            exit();
        }
    } else {
        // Không có ảnh mới, giữ ảnh cũ
        $image_new_name = $row['image'];
        $stmt = $conn->prepare("UPDATE thucan SET ten = ?, danh_cho_loai = ?, gia = ?, mo_ta = ?, xuat_su = ?, thanh_phan = ?, cong_dung = ?, phu_hop_voi_tuoi_thu_cung = ?, so_luong = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssi", $ten, $danh_cho_loai, $gia, $mo_ta, $xuat_su, $chat_lieu, $cong_dung, $phu_hop, $soluong, $image_new_name, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: manage_thucan.php?status=success&message=Cập nhật thành công!");
        exit();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Sửa sản phẩm thức ăn</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../assets/css/edit_product.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

        <?php if ($isAdmin) { ?>
    <div class="container">
        <h2>Sửa sản phẩm</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Tên sản phẩm:</label>
            <input type="text" name="ten" value="<?php echo $row['ten']; ?>" required>

            <label>Dành cho loại:</label>
            <input type="text" name="danh_cho_loai" value="<?php echo $row['danh_cho_loai']; ?>" required>

            <label>Mô tả:</label>
            <input type="text" name="mo_ta" value="<?php echo $row['mo_ta']; ?>" required>

            <label>Giá:</label>
            <input type="number" name="gia" value="<?php echo $row['gia']; ?>" required>

            <label>Thành phần:</label>
            <input type="text" name="thanh_phan" value="<?php echo $row['thanh_phan']; ?>" required>

            <label>Công dụng:</label>
            <input type="text" name="cong_dung" value="<?php echo $row['cong_dung']; ?>" required>

            <label>Tuổi phù hợp:</label>
            <input type="text" name="phu_hop_voi_tuoi_thu_cung" value="<?php echo $row['phu_hop_voi_tuoi_thu_cung']; ?>" required>

            <label>Xuất xứ:</label>
            <input type="text" name="xuat_su" value="<?php echo $row['xuat_su']; ?>" required>

            <label>Ảnh mới (nếu muốn thay):</label>
            <input type="file" name="image" accept="image/*">

            <label>Số lượng:</label>
            <input type="number" name="so_luong" value="<?php echo $row['so_luong']; ?>" required>

            <input type="submit" name="capnhat" value="Cập nhật">
        </form>
        <a href="manage_thucan.php" class="back-link">← Quay lại</a>
    </div>
    <?php } else {
            echo $content;
        } ?>
    
</body>
</html>
<?php include "../includes/footer.php"?>