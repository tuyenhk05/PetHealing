<?php
include('../includes/db_connect.php');
include "../includes/header.php";


if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tintuc WHERE id = $id");
    echo "<script>alert('Đã xóa thành công!');location.href='manage_news.php';</script>";
    exit;
}


if(isset($_POST['add_news'])){
    $stmt = $conn->prepare("INSERT INTO tintuc (tieu_de, noi_dung, ngay_dang) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['tieu_de'], $_POST['noi_dung'], $_POST['ngay_dang']);
    $stmt->execute();
    echo "<script>alert('Đã thêm tin mới!');location.href='manage_news.php';</script>";
    exit;
}

$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if ($vai_tro == 'Admin') {
    $isAdmin = true;
} else {
    $isAdmin = false;
    $content = "<br/><h1> Bạn không có quyền truy cập trang này. </h1><br/><br/><br/>";
}
$news = $conn->query("SELECT * FROM tintuc ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Tin tức</title>
    <link rel="stylesheet" href="../assets/css/manage_news.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

            <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .back-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
        }

            .back-button i {
                margin-right: 6px;
            }

            .back-button:hover {
                background: #0056b3;
            }
    </style>
</head>
<body>
        <?php if ($isAdmin) { ?>
    <button onclick="window.history.back()" class="back-button">
        <i class="fas fa-arrow-left"></i> Quay lại
    </button>
    <h2>Quản lý Tin tức</h2>
    <div class="form-section">
        <h3>Thêm tin mới</h3>
        <form method="post">
            <input name="tieu_de" placeholder="Tiêu đề" required>
            <input name="ngay_dang" type="date" required>
            <textarea name="noi_dung" placeholder="Nội dung" required></textarea>
            <button name="add_news">Thêm tin mới</button>
        </form>
    </div>

   <table>
        <tr>
            <th>STT</th>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Ngày đăng</th>
            <th>Xóa</th>
        </tr>
        <?php 
        $stt = 1;
        while($row = $news->fetch_assoc()): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td><?= htmlspecialchars($row['tieu_de']) ?></td>
            <td><?= htmlspecialchars(mb_substr($row['noi_dung'], 0, 50)) ?>...</td>
            <td><?= htmlspecialchars($row['ngay_dang']) ?></td>
            <td>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Xóa tin này?');">
                    <button class="delete-btn">Xóa</button>
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php } else {
            echo $content;
        } ?>
   
</body>
</html>
<?php include "../includes/footer.php";
?>
