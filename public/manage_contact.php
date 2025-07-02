<?php
include('../includes/db_connect.php');
include "../includes/header.php";
$sql = "SELECT * FROM contact ORDER BY id DESC";
$result = $conn->query($sql);
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if ($vai_tro == 'Admin') {
    $isAdmin = true;
} else {
    $isAdmin = false;
    $content = "<br/><h1> Bạn không có quyền truy cập trang này. </h1><br/><br/><br/>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý liên hệ khách hàng</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../assets/css/style.css">

    <style>
       
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
            <div  style=" max-width: 1200px; margin-left: auto; margin-right: auto;margin-bottom:20px">

    <button onclick="window.history.back()" class="back-button">
        <i class="fas fa-arrow-left"></i>  Quay lại
    </button>
     <?php if ($isAdmin) { ?>
    <h2>Danh sách liên hệ khách hàng</h2>
    <table>
        <tr>
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
    <?php } else {
         echo $content;
     } ?>
    </div>
</body>
</html>
<?php include "../includes/footer.php";?>