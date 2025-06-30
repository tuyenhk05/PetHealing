<?php
include('../includes/db_connect.php'); // Kết nối cơ sở dữ liệu

// Lấy ID dịch vụ từ URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Lấy thông tin dịch vụ hiện tại
    $stmt = $conn->prepare("SELECT * FROM dichvu WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();
    $stmt->close();
} else {
    // Nếu không có ID, chuyển hướng về trang quản lý dịch vụ
    header("Location: manage_services.php");
    exit();
}

// Cập nhật thông tin dịch vụ
if (isset($_POST['update'])) {
    $ten_dich_vu = $_POST['ten_dich_vu'];
    $loai = $_POST['loai'];
    $gia = $_POST['gia'];
    $mo_ta = $_POST['mo_ta'];

    // Xử lý ảnh nếu có
    $image_name = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_error = $_FILES['image']['error'];

    if ($image_error === 0) {
        // Lấy phần mở rộng của ảnh và chuyển thành chữ thường
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_extension = strtolower($image_extension); // Chuyển thành chữ thường
        $image_new_name = uniqid('', true) . '.' . $image_extension; // Đổi tên ảnh để tránh trùng lặp
        $image_upload_path = "../assets/image/" . $image_new_name;

        // Kiểm tra nếu file là ảnh hợp lệ
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($image_extension, $valid_extensions)) {
            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                // Cập nhật thông tin vào cơ sở dữ liệu
                $stmt = $conn->prepare("UPDATE dichvu SET ten_dich_vu = ?, loai = ?, gia = ?, mo_ta = ?, image = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $ten_dich_vu, $loai, $gia, $mo_ta, $image_new_name, $id);
                $stmt->execute();
                $stmt->close();

                // Sau khi cập nhật thành công, chuyển hướng về trang quản lý và thông báo
                header("Location: manage_services.php?status=success&message=Cập nhật dịch vụ thành công!");
                exit();
            } else {
                header("Location: manage_services.php?status=error&message=Lỗi khi tải ảnh lên.");
                exit();
            }
        } else {
            header("Location: manage_services.php?status=error&message=Chỉ hỗ trợ tải lên ảnh có định dạng JPG, JPEG, PNG, GIF.");
            exit();
        }
    } else {
        // Nếu không có ảnh mới, chỉ cập nhật các thông tin như tên dịch vụ, mô tả, giá
        $stmt = $conn->prepare("UPDATE dichvu SET ten_dich_vu = ?, loai = ?, gia = ?, mo_ta = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $ten_dich_vu, $loai, $gia, $mo_ta, $id);
        $stmt->execute();
        $stmt->close();

        // Sau khi cập nhật thành công, chuyển hướng về trang quản lý và thông báo
        header("Location: manage_services.php?status=success&message=Cập nhật dịch vụ thành công!");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật Dịch Vụ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
            color: #34C9A5;
        }
        form {
            margin: 30px auto;
            padding: 20px;
            background: white;
            max-width: 600px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #34C9A5;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<h2>Cập nhật dịch vụ</h2>

<?php if (isset($service)): ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="ten_dich_vu" value="<?php echo htmlspecialchars($service['ten_dich_vu']); ?>" placeholder="Tên Dịch Vụ" required><br>
        <label for="loai">Loại Dịch Vụ</label>
        <select name="loai" id="loai" required>
            <option value="cham-soc" <?php echo ($service['loai'] == 'cham-soc') ? 'selected' : ''; ?>>Chăm sóc</option>
            <option value="kham-chua-benh" <?php echo ($service['loai'] == 'kham-chua-benh') ? 'selected' : ''; ?>>Khám chữa bệnh</option>
            <option value="van-chuyen" <?php echo ($service['loai'] == 'van-chuyen') ? 'selected' : ''; ?>>Vận chuyển</option>
        </select><br>
        <textarea name="mo_ta" placeholder="Mô Tả" required><?php echo htmlspecialchars($service['mo_ta']); ?></textarea><br>
        <input type="text" name="gia" value="<?php echo $service['gia']; ?>" placeholder="Giá" required><br>
        <input type="file" name="image" accept="image/*"><br>
        <button type="submit" name="update">Cập nhật dịch vụ</button>
    </form>
<?php else: ?>
    <p>Không tìm thấy dịch vụ để cập nhật.</p>
<?php endif; ?>

</body>
</html>
