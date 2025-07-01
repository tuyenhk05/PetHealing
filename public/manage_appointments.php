<?php
include('../includes/db_connect.php');
include "../includes/header.php";
if (isset($_GET['delete_id'])) {
    $conn->query("DELETE FROM lichhen WHERE id = ".intval($_GET['delete_id']));
    header("Location: manage_appointments.php"); exit();
}
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if ($vai_tro == 'Admin') {
    $isAdmin = true;
} else {
    $isAdmin = false;
    $content = "<br/><h1> Bạn không có quyền truy cập trang này. </h1><br/><br/><br/>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $stmt = $conn->prepare("UPDATE lichhen SET trang_thai=? WHERE id=?");
    $stmt->bind_param("si", $_POST['action'], $_POST['id']);
    $stmt->execute();
    header("Location: manage_appointments.php"); exit();
}

$search = trim($_GET['search'] ?? "");

$sql1 = "SELECT * FROM lichhen WHERE trang_thai IN ('Chờ xác nhận', 'Đã xác nhận')";
if ($search) $sql1 .= " AND (ten_khach_hang LIKE '%$search%' OR ten_thu_cung LIKE '%$search%' OR ten_dich_vu LIKE '%$search%')";
$sql1 .= " ORDER BY ngay_hen DESC, gio_hen DESC";
$res1 = $conn->query($sql1);

$sql2 = "SELECT * FROM lichhen WHERE trang_thai IN ('Đã xong', 'Đã hủy')";
if ($search) $sql2 .= " AND (ten_khach_hang LIKE '%$search%' OR ten_thu_cung LIKE '%$search%' OR ten_dich_vu LIKE '%$search%')";
$sql2 .= " ORDER BY ngay_hen DESC, gio_hen DESC";
$res2 = $conn->query($sql2);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quản lý Lịch hẹn</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/manage_appointments.css">
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
    <h2>Quản Lý Lịch Hẹn Của Thú Cưng</h2>
    <div class="table-container">

    <form method="get" style="margin-bottom:28px;">
        <input name="search" placeholder="Tìm kiếm..." class="search-bar" value="<?= htmlspecialchars($search) ?>">
        <button class="find-btn">Tìm</button>
    </form>

    <!-- Bảng lịch hẹn đang xử lý -->
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Khách hàng</th>
                <th>Thú cưng</th>
                <th>Loại</th>
                <th>Dịch vụ</th>
                <th>Ngày/Giờ</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
                <th>Xóa</th>
            </tr>
        </thead>
        <tbody>
        <?php $i = 1;
        while ($row = $res1->fetch_assoc()): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td>
                    <b><?= $row['ten_khach_hang'] ?></b><br>
                    <small><?= $row['so_dien_thoai_khach_hang'] ?></small><br>
                    <small><?= $row['email_khach_hang'] ?></small>
                </td>
                <td><b><?= $row['ten_thu_cung'] ?></b></td>
                <td><?= $row['loai_thu_cung'] ?></td>
                <td><?= $row['ten_dich_vu'] ?></td>
                <td><b><?= $row['ngay_hen'] ?></b><br><?= $row['gio_hen'] ?></td>
                <td>
                    <form method="post" style="margin:0;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <select name="action" class="status-dropdown" onchange="this.form.submit()">
                            <?php foreach (['Chờ xác nhận', 'Đã xác nhận', 'Đã xong', 'Đã hủy'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $row['trang_thai'] == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </td>
                <td><?= $row['ghi_chu'] ?></td>
                <td>
                    <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Xóa lịch này?')" class="delete-btn">Xóa</a>
                </td>
            </tr>
        <?php endwhile;
        if ($i == 1): ?>
            <tr><td colspan="9" style="text-align:center;color:#888">Không có lịch hẹn đang xử lý.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="height:32px;"></div>
    <div style="text-align:center;">
        <span style="color:#2596be;font-weight:bold;font-size:19px;letter-spacing:1px;">Lịch hẹn đã xong & đã hủy</span>
        <hr style="width:64%;margin:12px auto 10px auto;border: none; border-top:2.2px solid #e0f7fa;">
    </div>

    <!-- Bảng lịch hẹn đã xong/hủy -->
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Khách hàng</th>
                <th>Thú cưng</th>
                <th>Loại</th>
                <th>Dịch vụ</th>
                <th>Ngày/Giờ</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
                <th>Xóa</th>
            </tr>
        </thead>
        <tbody>
        <?php $j = 1;
        while ($row = $res2->fetch_assoc()): ?>
            <tr>
                <td><?= $j++ ?></td>
                <td>
                    <b><?= $row['ten_khach_hang'] ?></b><br>
                    <small><?= $row['so_dien_thoai_khach_hang'] ?></small><br>
                    <small><?= $row['email_khach_hang'] ?></small>
                </td>
                <td><b><?= $row['ten_thu_cung'] ?></b></td>
                <td><?= $row['loai_thu_cung'] ?></td>
                <td><?= $row['ten_dich_vu'] ?></td>
                <td><b><?= $row['ngay_hen'] ?></b><br><?= $row['gio_hen'] ?></td>
                <td><?= $row['trang_thai'] ?></td>
                <td><?= $row['ghi_chu'] ?></td>
                <td>
                    <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Xóa lịch này?')" class="delete-btn">Xóa</a>
                </td>
            </tr>
        <?php endwhile;
        if ($j == 1): ?>
            <tr><td colspan="9" style="text-align:center;color:#888">Chưa có lịch hẹn đã xong hoặc đã hủy.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    <?php } else {
        echo $content;
    } ?>
     
</body>
</html>
<?php include "../includes/footer.php";?>