<?php
include('../includes/db_connect.php'); // Kết nối cơ sở dữ liệu

// Thêm dịch vụ
if (isset($_POST['add'])) {
    $ten_dich_vu = $_POST['ten_dich_vu'];
    $loai = $_POST['loai'];
    $gia = $_POST['gia'];
    $mo_ta = $_POST['mo_ta'];
    
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
                $stmt = $conn->prepare("INSERT INTO dichvu (ten_dich_vu, loai, gia, mo_ta, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $ten_dich_vu, $loai, $gia, $mo_ta, $image_new_name);
                $stmt->execute();
                $stmt->close();
                header("Location: manage_services.php"); // Quay lại trang quản lý dịch vụ
                exit();
            } else {
                echo "Lỗi khi tải ảnh lên.";
            }
        } else {
            echo "Chỉ hỗ trợ tải lên ảnh có định dạng JPG, JPEG, PNG, GIF.";
        }
    }
}
// Xóa dịch vụ
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM dichvu WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_services.php"); // Quay lại trang quản lý dịch vụ
    exit();
}

// Phân trang
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM dichvu");
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$result = mysqli_query($conn, "SELECT * FROM dichvu LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Dịch Vụ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f9f9f9;
        }
        h2, h3 {
            text-align: center;
            color: #34C9A5;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th {
            background-color: #34C9A5;
            color: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        tr:hover { background-color: #f1f1f1; }
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
            background-color: #34C9A5; /* Màu chủ đạo */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #34C9A5;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 6px;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 14px;
            background-color: #34C9A5;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.25s ease-in-out;
        }
        .pagination a:hover {
            background-color: #34C9A5;
            transform: translateY(-2px);
        }
        .pagination a.active {
            background-color: #F56C93;
            pointer-events: none;
        }
        #toggleFormBtn {
            background-color: #F56C93; /* Màu chủ đạo */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        #toggleFormBtn:hover {
            background-color: #e07b00;
        }
        /* Nút Quay lại */
        .btn-back {
            background-color: #F56C93; /* Màu chủ đạo */
            color: white;
            padding: 10px 20px;
            font-size: 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-back:hover {
            background-color: #34C9A5;
            color: #fff;
        }
    </style>
</head>
<body>
    <h2>Quản Lý Dịch Vụ</h2>
    <button id="toggleFormBtn">Thêm Dịch Vụ</button>
    <div id="formContainer" style="display: none;">
       <form method="POST" enctype="multipart/form-data">
    <input type="text" name="ten_dich_vu" placeholder="Tên Dịch Vụ" required><br>

    <!-- Loại Dịch Vụ -->
    <label for="loai">Loại Dịch Vụ</label>
    <select name="loai" id="loai" required>
        <option value="cham-soc">Chăm sóc</option>
        <option value="kham-chua-benh">Khám chữa bệnh</option>
        <option value="van-chuyen">Vận chuyển</option>
        <!-- Bạn có thể thêm nhiều loại dịch vụ khác nếu cần -->
    </select><br>

    <textarea name="mo_ta" placeholder="Mô Tả" required></textarea><br>
    <input type="text" name="gia" placeholder="Giá" required><br>
    <input type="file" name="image" accept="image/*" required><br>
    <button type="submit" name="add">Thêm Dịch Vụ</button>
</form>

    </div>

    <table>
        <tr>
            <th>Hình ảnh</th>
            <th>Tên Dịch Vụ</th>
            <th>Loại</th>
            <th>Giá</th>
            <th>Mô Tả</th>
            <th>Thao Tác</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)) {
            // Kiểm tra nếu có ảnh
            $imagePath = $row['image'] ? "../assets/image/" . $row['image'] : "../assets/image/" . $row['ten_dich_vu'] . ".jpg";
        ?>
        <tr>
            <td><img src="<?php echo $imagePath; ?>" alt="<?php echo $row['ten_dich_vu']; ?>" style="width: 100px; height: auto;"></td>
            <td><?php echo $row['ten_dich_vu']; ?></td>
            <td><?php echo $row['loai']; ?></td>
            <td><?php echo number_format($row['gia']); ?> VND</td>
            <td><?php echo $row['mo_ta']; ?></td>
            <td>
                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    <!-- Nút Quay Lại -->
    <button onclick="history.back()" class="btn btn-back">Quay lại</button>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

    <script>
        document.getElementById("toggleFormBtn").addEventListener("click", function () {
            var form = document.getElementById("formContainer");
            if (form.style.display === "none") {
                form.style.display = "block";
                this.innerText = "Ẩn Form Thêm";
            } else {
                form.style.display = "none";
                this.innerText = "Thêm Dịch Vụ";
            }
        });
    </script>

</body>
</html>
