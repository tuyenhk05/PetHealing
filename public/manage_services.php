<?php
include('../includes/db_connect.php'); // Kết nối cơ sở dữ liệu
include "../includes/header.php";
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if ($vai_tro == 'Admin') {
    $isAdmin = true;
} else {
    $isAdmin = false;
    $content = "<br/><h1> Bạn không có quyền truy cập trang này. </h1><br/><br/><br/>";
}
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

    if ($image_error === 0) {
        // Lấy phần mở rộng của ảnh và chuyển thành chữ thường
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_extension = strtolower($image_extension);
        $image_new_name = uniqid('', true) . '.' . $image_extension; // Đổi tên ảnh để tránh trùng lặp
        $image_upload_path = "../assets/image/" . $image_new_name;

        // Kiểm tra nếu file là ảnh hợp lệ
        if (in_array($image_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                // Thêm dữ liệu vào cơ sở dữ liệu
                $stmt = $conn->prepare("INSERT INTO dichvu (ten_dich_vu, loai, gia, mo_ta, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $ten_dich_vu, $loai, $gia, $mo_ta, $image_new_name);
                $stmt->execute();
                $stmt->close();
                header("Location: manage_services.php?status=success&message=Thêm dịch vụ thành công!");
                exit();
            } else {
                header("Location: manage_services.php?status=error&message=Lỗi khi tải ảnh lên.");
                exit();
            }
        } else {
            header("Location: manage_services.php?status=error&message=Chỉ hỗ trợ tải lên ảnh có định dạng JPG, JPEG, PNG, GIF.");
            exit();
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
    header("Location: manage_services.php?status=success&message=Xóa dịch vụ thành công!");
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
                    <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Quản lý Dịch Vụ</title>
    <style>
      
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
            background-color: #218838;
            transform: translateY(-2px);
        }
        .pagination a.active {
            background-color: #F56C93;
            pointer-events: none;
        }
        #toggleFormBtn {
            background-color: #F56C93;
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
        .btn-back {
            background-color: #F56C93;
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
        .status-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .success {
            background-color: #28a745;
            color: white;
        }
        .error {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
     <?php if ($isAdmin) { ?>
    <?php
// Thông báo nếu có
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status = $_GET['status'];
    $message = $_GET['message'];
    echo "<div class='status-message " . $status . "'>" . $message . "</div>";
}
?>
    <h2>Quản Lý Dịch Vụ</h2>

    <!-- Nút thêm dịch vụ -->
    <button id="toggleFormBtn">Thêm Dịch Vụ</button>

    <!-- Form thêm dịch vụ -->
    <div id="formContainer" style="display: none;">
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="ten_dich_vu" placeholder="Tên Dịch Vụ" required><br>
            <label for="loai">Loại Dịch Vụ</label>
            <select name="loai" id="loai" required>
                <option value="cham-soc">Chăm sóc</option>
                <option value="kham-chua-benh">Khám chữa bệnh</option>
                <option value="van-chuyen">Vận chuyển</option>
            </select><br>
            <textarea name="mo_ta" placeholder="Mô Tả" required></textarea><br>
            <input type="text" name="gia" placeholder="Giá" required><br>
            <input type="file" name="image" required><br>
            <button type="submit" name="add">Thêm Dịch Vụ</button>
        </form>
    </div>

    <!-- Hiển thị danh sách dịch vụ -->
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
                <a href="edit_services.php?id=<?php echo $row['id']; ?>">Sửa</a> | 
                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <!-- Nút quay lại -->
    <button onclick="history.back()" class="btn btn-back">Quay lại</button>

    <!-- Phân trang -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

 <script>
        // Kiểm tra nếu có thông báo status
    const statusMessage = document.querySelector('.status-message');
    if (statusMessage) {
        // Sau 2 giây (2000ms), ẩn thông báo
        setTimeout(() => {
            statusMessage.style.display = 'none';
        }, 2000); // 2 giây
    }
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
    <?php } else {
         echo $content;
     } ?>

   

</body>
</html>
<?php include "../includes/footer.php";
?>