<?php
include('../includes/db_connect.php');
include('../includes/header.php');

$vai_tro = isset($_COOKIE['vai_tro']) ? $_COOKIE['vai_tro'] : '';
if ($vai_tro !== 'Admin') {
    echo "<h2>\u274c Bạn không có quyền truy cập trang này.</h2>";
    exit();
}

// Xử lý xoá nếu có delete_id
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM nguoidung WHERE id = $id");
    echo "<script>alert('\u2705 Xo\u00e1 th\u00e0nh c\u00f4ng!'); window.location='manage_users.php';</script>";
    exit();
}

$search = trim($_GET['search'] ?? "");
$sql = "SELECT * FROM nguoidung WHERE 1";
if ($search) {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (ho_ten LIKE '%$search_esc%' OR email LIKE '%$search_esc%' OR so_dien_thoai LIKE '%$search_esc%')";
}
$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-links a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
        .search-form {
            margin-bottom: 20px;
        }
         .back-button {
            background: #34C9A5;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
        }

            .back-button i {
                margin-right: 6px;
            }

            .back-button:hover {
                background: #22866E;
            }
    </style>
</head>
<body>
    <div style="max-width: 1100px; margin: auto">
          <button onclick="window.history.back()" class="back-button">
        <i class="fas fa-arrow-left"></i> Quay lại
    </button>
        <h2>Quản lý người dùng</h2>

        <form method="get" class="search-form">
            <input type="text" name="search" placeholder="Tìm kiếm theo tên, email, SĐT" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Tìm</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['ho_ten']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['so_dien_thoai']) ?></td>
                        <td><?= htmlspecialchars($row['vai_tro']) ?></td>
                        <td class="action-links">
                            <?php if ($row['vai_tro']=='user') { ?>
                            <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xoá người dùng này?')">Xoá</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
include('../includes/footer.php');
?>
