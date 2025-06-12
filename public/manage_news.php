<?php
include('../includes/db_connect.php');


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


$news = $conn->query("SELECT * FROM tintuc ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Tin tức</title>
    <link rel="stylesheet" href="../assets/css/manage_news.css">
</head>
<body>
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
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Ngày đăng</th>
            <th>Xóa</th>
        </tr>
        <?php while($row = $news->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
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
</body>
</html>
