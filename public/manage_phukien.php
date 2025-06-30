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
    $phu_hop = $_POST['phu_hop'];
    $soluong = $_POST['so_luong'];

    // Xử lý ảnh
    $image_name = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_error = $_FILES['image']['error'];

    // Kiểm tra lỗi upload ảnh
    if ($image_error === 0) {
        // Lấy phần mở rộng của ảnh và chuyển thành chữ thường
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_extension = strtolower($image_extension); // Chuyển thành chữ thường
        $image_new_name = uniqid('', true) . '.' . $image_extension; // Đổi tên ảnh để tránh trùng lặp
        $image_upload_path = "../assets/image/" . $image_new_name;

        // Kiểm tra nếu file là ảnh hợp lệ
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($image_extension, $valid_extensions)) {
            // Chuyển ảnh vào thư mục
            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                // Thêm dữ liệu vào cơ sở dữ liệu
                $stmt = $conn->prepare("INSERT INTO phukien (ten, danh_cho_loai, mo_ta, gia, xuat_su, chat_lieu, cong_dung, phu_hop_voi_lua_tuoi, so_luong, image) VALUES (?, ?, ?, ?, ?, ? , ? , ? , ?, ?)");
                $stmt->bind_param("sssss", $ten, $danh_cho_loai, $mo_ta, $gia, $xuat_su, $chat_lieu, $cong_dung, $phu_hop, $soluong, $image_new_name);
                $stmt->execute();
                $stmt->close();
                header("Location: manage_thucan.php"); // Quay lại trang quản lý dịch vụ
                exit();
            } else {
                echo "Lỗi khi tải ảnh lên.";
            }
        } else {
            echo "Chỉ hỗ trợ tải lên ảnh có định dạng JPG, JPEG, PNG, GIF.";
        }
    }


    // $query = "INSERT INTO PhuKien (ten, danh_cho_loai, mo_ta, gia, xuat_su, chat_lieu, cong_dung, phu_hop_voi_tuoi_thu_cung) 
    //           VALUES ('$ten', '$danh_cho_loai', '$mo_ta', '$gia', '$xuat_su', '$chat_lieu', '$cong_dung', '$phu_hop')";
    // mysqli_query($conn, $query);
    // header("Location: manage_phukien.php");
    // exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM PhuKien WHERE id=$id");
    header("Location: manage_phukien.php");
    exit();
}

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM PhuKien");
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$result = mysqli_query($conn, "SELECT * FROM PhuKien LIMIT $limit OFFSET $offset");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý Phụ Kiện</title>
    <link rel="stylesheet" href="../assets/css/manage_product.css">
    
</head>
<body>
    <h2>Quản lý Phụ Kiện</h2>
    <body>
    <button id="toggleFormBtn" class="btn btn-success mb-3">Thêm sản phẩm</button>
    <div id="formContainer" style="display: none; margin-top: 40px;">
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="ten" placeholder="Tên sản phẩm" required>
            <input type="text" name="danh_cho_loai" placeholder="Dành cho loại" required>
            <textarea name="mo_ta" placeholder="Mô tả"></textarea>
            <input type="number" name="gia" placeholder="Giá" required>
            <input type="text" name="xuat_su" placeholder="Xuất xứ">
            <input type="text" name="so_luong" placeholder="Số lương">

            <input type="text" name="chat_lieu" placeholder="Chất liệu">
            <textarea name="cong_dung" placeholder="Công dụng"></textarea>
            <input type="text" name="phu_hop" placeholder="Phù hợp với tuổi thú cưng">
            <input type="file" name="hinh" accept="image/*" required>
            <button type="submit" name="add">Thêm sản phẩm</button>
        </form>
    </div>

    <table>
        <tr>
            <th>ID</th><th>Hình ảnh</th><th>Tên sản phẩm</th><th>Loại thú cưng</th><th>Mô tả</th><th>Giá</th><th>Chất liệu</th><th>Công dụng</th><th>Độ tuổi</th><th>Xuất sứ</th><th>Số lượng</th><th>Sửa</th><th>Xóa</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)) {
             // Kiểm tra nếu có ảnh
            $imagePath = $row['image'] ? "../assets/image/" . $row['image'] : "../assets/image/" . $row['ten'] . ".jpg";
        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><img src="<?php echo $imagePath; ?>" alt="<?php echo $row['ten']; ?>"></td>
            <td><?php echo $row['ten']; ?></td>
            <td><?php echo $row['danh_cho_loai']; ?></td>
            <td><?php echo $row['mo_ta']; ?></td>
            <td><?php echo number_format($row['gia']); ?> VND</td>
            <td><?php echo $row['chat_lieu']; ?></td>
            <td><?php echo $row['cong_dung']; ?></td>
            <td><?php echo $row['phu_hop_voi_tuoi_thu_cung']; ?></td>
            <td><?php echo $row['xuat_su']; ?></td>
            <td><?php echo $row['so_luong']; ?></td>
             <td id="delete"><a href="edit_phukien.php?id=<?php echo $row['id']; ?>">Sửa</a></td>

            <td id="delete" ><a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Xác nhận xóa?')">Xóa</a></td>
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