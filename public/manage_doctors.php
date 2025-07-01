<?php
include('../includes/db_connect.php');
include('../includes/header.php');

$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if ($vai_tro == 'Admin') {
    $isAdmin = true;
} else {
    $isAdmin = false;
    $content = "<br/><h1> Bạn không có quyền truy cập trang này. </h1><br/><br/><br/>";
}

// Sửa bác sĩ
$editingDoctor = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $edit_stmt = $conn->prepare("SELECT * FROM BacSi WHERE id = ?");
    $edit_stmt->bind_param("i", $edit_id);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();
    if ($edit_result->num_rows > 0) {
        $editingDoctor = $edit_result->fetch_assoc();
    }
    $edit_stmt->close();
}

// Cập nhật bác sĩ
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $ho_ten = $_POST['ho_ten'];
    $chuyen_mon = $_POST['chuyen_mon'];
    $kinh_nghiem = $_POST['kinh_nghiem'];
    $image_new_name = $_POST['existing_image'];

    if ($_FILES['image']['name']) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_error = $_FILES['image']['error'];

        if ($image_error === 0) {
            $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $image_new_name = uniqid('', true) . '.' . $ext;
                move_uploaded_file($image_tmp_name, "../assets/image/" . $image_new_name);
            } else {
                header("Location: manage_doctors.php?status=error&message=Ảnh không hợp lệ.");
                exit();
            }
        }
    }

    $stmt = $conn->prepare("UPDATE BacSi SET ho_ten=?, chuyen_mon=?, kinh_nghiem=?, image=? WHERE id=?");
    $stmt->bind_param("ssisi", $ho_ten, $chuyen_mon, $kinh_nghiem, $image_new_name, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_doctors.php?status=success&message=Cập nhật thành công!");
    exit();
}

// Thêm bác sĩ
if (isset($_POST['add'])) {
    $ho_ten = $_POST['ho_ten'];
    $chuyen_mon = $_POST['chuyen_mon'];
    $kinh_nghiem = $_POST['kinh_nghiem'];

    $image_name = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_error = $_FILES['image']['error'];
    $image_new_name = "";

    if ($image_error === 0 && $image_name) {
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        if (in_array($image_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $image_new_name = uniqid('', true) . '.' . $image_extension;
            $image_upload_path = "../assets/image/" . $image_new_name;
            if (!move_uploaded_file($image_tmp_name, $image_upload_path)) {
                header("Location: manage_doctors.php?status=error&message=Lỗi khi tải ảnh lên.");
                exit();
            }
        } else {
            header("Location: manage_doctors.php?status=error&message=Chỉ hỗ trợ ảnh JPG, JPEG, PNG, GIF.");
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO BacSi (ho_ten, chuyen_mon, kinh_nghiem, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $ho_ten, $chuyen_mon, $kinh_nghiem, $image_new_name);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_doctors.php?status=success&message=Thêm bác sĩ thành công!");
    exit();
}

// Xóa bác sĩ
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM BacSi WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_doctors.php?status=success&message=Xóa bác sĩ thành công!");
    exit();
}

// Phân trang
$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM BacSi");
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$result = mysqli_query($conn, "SELECT * FROM BacSi LIMIT $limit OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Bác Sĩ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <style>
        h2 { text-align: center; color: #34C9A5; }
        table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
        th { background-color: #34C9A5; color: white;}
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center;}
        tr:hover { background-color: #f1f1f1; }
        form { margin: 30px auto; padding: 20px; background: white; max-width: 600px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
        input, textarea { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px;}
        button { background-color: #34C9A5; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;}
        button:hover { background-color: #218838;}
        .pagination { display: flex; justify-content: center; margin-top: 30px; gap: 6px;}
        .pagination a { padding: 8px 14px; background-color: #34C9A5; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; transition: all 0.25s;}
        .pagination a:hover { background-color: #218838; transform: translateY(-2px);}
        .pagination a.active { background-color: #F56C93; pointer-events: none;}
        .btn-back { background-color: #F56C93; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-top: 20px; display: inline-block;}
        .btn-back:hover { background-color: #34C9A5;}
        .status-message { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-weight: bold;}
        .success { background-color: #28a745; color: white;}
        .error { background-color: #dc3545; color: white;}
    </style>
</head>
<body>
     <?php if ($isAdmin) { ?>
     <?php if (isset($_GET['status'], $_GET['message'])): ?>
        <div class="status-message <?= $_GET['status'] ?>"><?= htmlspecialchars($_GET['message']) ?></div>
    <?php endif; ?>
        <a href="javascript:history.back()" class="btn-back">Quay lại</a>

    <h2>Quản Lý Bác Sĩ</h2>
    <button id="toggleFormBtn"><?= $editingDoctor ? "Ẩn Form Sửa" : "Thêm Bác Sĩ" ?></button>
    <div id="formContainer" style="<?= $editingDoctor ? 'display:block;' : 'display:none;' ?>">
        <form method="POST" enctype="multipart/form-data">
            <?php if ($editingDoctor): ?>
                <input type="hidden" name="id" value="<?= $editingDoctor['id'] ?>">
                <input type="hidden" name="existing_image" value="<?= $editingDoctor['image'] ?>">
            <?php endif; ?>
            <input type="text" name="ho_ten" placeholder="Họ tên bác sĩ" value="<?= $editingDoctor['ho_ten'] ?? '' ?>" required><br>
            <textarea name="chuyen_mon" placeholder="Chuyên môn (ngăn cách dấu phẩy)" required><?= $editingDoctor['chuyen_mon'] ?? '' ?></textarea><br>
            <input type="number" name="kinh_nghiem" placeholder="Kinh nghiệm (năm)" min="0" value="<?= $editingDoctor['kinh_nghiem'] ?? '' ?>" required><br>
            <input type="file" name="image" accept="image/*"><br>
            <button type="submit" name="<?= $editingDoctor ? 'update' : 'add' ?>">
                <?= $editingDoctor ? 'Cập nhật Bác Sĩ' : 'Thêm Bác Sĩ' ?>
            </button>
        </form>
    </div>

    <table>
        <tr>
            <th>Hình ảnh</th>
            <th>Họ tên</th>
            <th>Chuyên môn</th>
            <th>Kinh nghiệm</th>
            <th>Thao tác</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td>
                <?php $imagePath = $row['image'] ? "../assets/image/{$row['image']}" : "../assets/image/default.jpg"; ?>
                <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($row['ho_ten']) ?>" style="width: 100px;">
            </td>
            <td><?= htmlspecialchars($row['ho_ten']) ?></td>
            <td>
                <?php foreach (explode(",", $row['chuyen_mon']) as $cm): ?>
                    <span style="background:#e0f7fa; color:#00796b; padding:3px 8px; border-radius:6px; margin:2px; display:inline-block;">
                        <?= htmlspecialchars(trim($cm)) ?>
                    </span>
                <?php endforeach; ?>
            </td>
            <td><?= htmlspecialchars($row['kinh_nghiem']) ?> năm</td>
            <td>
                <a href="?edit=<?= $row['id'] ?>">Sửa</a> |
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

    <script>
        const status = document.querySelector('.status-message');
        if (status) setTimeout(() => status.style.display = 'none', 2000);

        document.getElementById("toggleFormBtn").addEventListener("click", function () {
            const form = document.getElementById("formContainer");
            if (form.style.display === "none") {
                form.style.display = "block";
                this.innerText = "Ẩn Form Thêm";
            } else {
                window.location.href = "manage_doctors.php";
            }
        });
    </script>
    <?php } else {
         echo $content;
     } ?>
  
</body>
</html>
<?php include('../includes/footer.php'); ?>
