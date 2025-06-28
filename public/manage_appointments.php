<?php
include('../includes/db_connect.php');

// Xóa lịch hẹn
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM LichHen WHERE id = $id");
    echo "<script>alert('Đã xóa lịch hẹn!');location.href='manage_appointments.php';</script>";
    exit;
}

// Lấy danh sách lịch hẹn (mới nhất lên trên)
$appointments = $conn->query("SELECT * FROM LichHen ORDER BY ngay_hen DESC, gio_hen DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Lịch hẹn</title>
    <link rel="stylesheet" href="../assets/css/manage_appointments.css">
</head>
<body>
    <h2>Quản lý Lịch hẹn</h2>
    <table>
        <tr>
            <th>STT</th>
            <th>Khách hàng</th>
            <th>SĐT</th>
            <th>Email</th>
            <th>Thú cưng</th>
            <th>Loại</th>
            <th>Dịch vụ</th>
            <th>Ngày hẹn</th>
            <th>Giờ hẹn</th>
            <th>Trạng thái</th>
            <th>Ghi chú</th>
            <th>Xóa</th>
        </tr>
        <?php 
        $stt = 1;
        while($row = $appointments->fetch_assoc()): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td><?= htmlspecialchars($row['ten_khach_hang']) ?></td>
            <td><?= htmlspecialchars($row['so_dien_thoai_khach_hang']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['ten_thu_cung']) ?></td>
            <td><?= htmlspecialchars($row['loai_thu_cung']) ?></td>
            <td><?= htmlspecialchars($row['ten_dich_vu']) ?></td>
            <td><?= htmlspecialchars($row['ngay_hen']) ?></td>
            <td><?= htmlspecialchars($row['gio_hen']) ?></td>
            <td><?= htmlspecialchars($row['trang_thai']) ?></td>
            <td><?= htmlspecialchars($row['ghi_chu']) ?></td>
            <td>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Xóa lịch hẹn này?');">
                    <button class="delete-btn">Xóa</button>
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
