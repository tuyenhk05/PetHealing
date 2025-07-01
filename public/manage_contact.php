<?php
include('../includes/db_connect.php');
$sql = "SELECT * FROM contact ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý liên hệ khách hàng</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #34C9A5;
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #34C9A5;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f3f3f3;
        }

        tr:hover {
            background-color: #e0f7f3;
        }

        .contact-btn {
            background-color: #34C9A5;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .contact-btn:hover {
            background-color: #2cb092;
        }
    </style>
</head>
<body>
    <h2>Danh sách liên hệ khách hàng</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Chủ đề</th>
            <th>Nội dung</th>
            <th>Thời gian gửi</th>
            <th>Liên hệ</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['subject']) ?></td>
            <td><?= htmlspecialchars($row['message']) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <a class="contact-btn" target="_blank"
                   href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= $row['email'] ?>&su=<?= urlencode('Phản hồi: '.$row['subject']) ?>">
                   Liên hệ
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
