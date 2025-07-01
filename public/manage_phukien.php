<?php
include('../includes/db_connect.php');

if (isset($_POST['add'])) {
    $ten = $_POST['ten'];
    $danh_cho_loai = $_POST['danh_cho_loai'];
    $mo_ta = $_POST['mo_ta'];
    $gia = $_POST['gia'];
    $xuat_su = $_POST['xuat_su'];
    $chat_lieu = $_POST['chat_lieu'];
    $cong_dung = $_POST['cong_dung'];
    $phu_hop = $_POST['phu_hop_voi_tuoi_thu_cung'];
    $soluong = (int)$_POST['so_luong'];

    // Xử lý ảnh
    $image_name = $_FILES['hinh']['name'] ?? '';
    $image_tmp_name = $_FILES['hinh']['tmp_name'] ?? '';
    $image_error = $_FILES['hinh']['error'] ?? 4; // 4 = no file uploaded

    if ($image_error === 0) {
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_extension, $valid_extensions)) {
            $image_new_name = uniqid('', true) . '.' . $image_extension;
            $image_upload_path = "../assets/image/" . $image_new_name;

            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                $stmt = $conn->prepare("INSERT INTO phukien (ten, danh_cho_loai, mo_ta, gia, xuat_su, chat_lieu, cong_dung, phu_hop_voi_tuoi_thu_cung, so_luong, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("ssssssssis", $ten, $danh_cho_loai, $mo_ta, $gia, $xuat_su, $chat_lieu, $cong_dung, $phu_hop, $soluong, $image_new_name);
                $stmt->execute();
                $stmt->close();

                header("Location: manage_phukien.php");
                exit();
            } else {
                echo "Lỗi khi tải ảnh lên.";
            }
        } else {
            echo "Chỉ hỗ trợ tải lên ảnh có định dạng JPG, JPEG, PNG, GIF.";
        }
    } else {
        echo "Bạn phải chọn ảnh để tải lên.";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM phukien WHERE id = $id");
    header("Location: manage_phukien.php");
    exit();
}

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM phukien");
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$result = mysqli_query($conn, "SELECT * FROM phukien LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý Phụ Kiện</title>
    <link rel="stylesheet" href="../assets/css/manage_product.css">
</head>
<body>
    <h2>Quản lý Phụ Kiện</h2>

    <button id="toggleFormBtn" class="btn btn-success mb-3">+ Thêm sản phẩm</button>
    <div id="formContainer" style="display: none; margin-top: 40px;">
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="ten" placeholder="Tên sản phẩm" required>
            <input type="text" name="danh_cho_loai" placeholder="Dành cho loại" required>
            <textarea name="mo_ta" placeholder="Mô tả"></textarea>
            <input type="number" step="0.01" name="gia" placeholder="Giá" required>
            <input type="text" name="xuat_su" placeholder="Xuất xứ">
            <input type="number" name="so_luong" placeholder="Số lượng" required>
            <input type="text" name="chat_lieu" placeholder="Chất liệu">
            <textarea name="cong_dung" placeholder="Công dụng"></textarea>
            <input type="text" name="phu_hop_voi_tuoi_thu_cung" placeholder="Phù hợp với tuổi thú cưng">
            <input type="file" name="hinh" accept="image/*" required>
            <button type="submit" name="add">Thêm sản phẩm</button>
        </form>
    </div>

    <table>
        <tr>
            <th>ID</th><th>Hình ảnh</th><th>Tên sản phẩm</th><th>Loại thú cưng</th><th>Mô tả</th><th>Giá</th><th>Chất liệu</th><th>Công dụng</th><th>Độ tuổi</th><th>Xuất xứ</th><th>Số lượng</th><th>Sửa</th><th>Xóa</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)) {
            $imagePath = (!empty($row['image'])) ? "../assets/image/" . $row['image'] : "../assets/image/default.jpg";
        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['ten']); ?>" width="100"></td>
            <td><?php echo htmlspecialchars($row['ten']); ?></td>
            <td><?php echo htmlspecialchars($row['danh_cho_loai']); ?></td>
            <td><?php echo htmlspecialchars($row['mo_ta']); ?></td>
            <td><?php echo number_format($row['gia'], 0, ',', '.'); ?> VND</td>
            <td><?php echo htmlspecialchars($row['chat_lieu']); ?></td>
            <td><?php echo htmlspecialchars($row['cong_dung']); ?></td>
            <td><?php echo htmlspecialchars($row['phu_hop_voi_tuoi_thu_cung']); ?></td>
            <td><?php echo htmlspecialchars($row['xuat_su']); ?></td>
            <td><?php echo (int)$row['so_luong']; ?></td>
            <td id="delete"><a href="edit_phukien.php?id=<?php echo $row['id']; ?>">Sửa</a></td>
            <td id="delete"><a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Xác nhận xóa?')">Xóa</a></td>
        </tr>
        <?php } ?>
    </table>

    <div class="back">
        <a href="manage_product.php">Quay lại</a>
    </div>

    <div class="pagination">
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>" style="margin: 0 5px; <?= ($i == $page) ? 'font-weight:bold; text-decoration:underline;' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>

<script>
  document.getElementById("toggleFormBtn").addEventListener("click", function () {
    var form = document.getElementById("formContainer");
    if (form.style.display === "none") {
      form.style.display = "block";
      this.innerText = "Ẩn form thêm";
    } else {
      form.style.display = "none";
      this.innerText = "+ Thêm sản phẩm";
    }
  });
</script>

</body>
</html>
