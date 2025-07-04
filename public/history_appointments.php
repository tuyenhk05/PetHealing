<?php
session_start(); // Khởi động session
// Kiểm tra xem khách hàng đã đăng nhập chưa (bằng cookie)
if (!isset($_COOKIE['user_name'])) {
    echo "Vui lòng đăng nhập để xem lịch sử đặt lịch hẹn!";
    exit(); // Nếu chưa đăng nhập, không cho phép truy cập trang này
}

// Lấy thông tin khách hàng từ cookie
$customer_email = $_COOKIE['user_email'];  
$customer_id = $_COOKIE['user_id'];  

// Kết nối cơ sở dữ liệu
include('../includes/db_connect.php');
// Kiểm tra nếu người dùng nhấn nút hủy
if (isset($_POST['cancel_id'])) {
    $cancel_id = $_POST['cancel_id'];

    // Kiểm tra xem lịch hẹn có phải của khách hàng này không và trạng thái có phải là "Chờ xác nhận" không
    $sql_check = "SELECT * FROM lichhen WHERE id = $cancel_id AND email_khach_hang = '$customer_email' AND trang_thai = 'Chờ xác nhận'";
    $result_check = mysqli_query($conn, $sql_check);
    
    // Nếu lịch hẹn tồn tại và trạng thái là "Chờ xác nhận"
    if (mysqli_num_rows($result_check) > 0) {
        // Cập nhật trạng thái lịch hẹn thành "Đã hủy"
        $sql = "UPDATE lichhen SET trang_thai = 'Đã hủy' WHERE id = $cancel_id";
        
        if (mysqli_query($conn, $sql)) {
            echo "<p class='message-container success'>Lịch hẹn đã được hủy thành công.</p>";
        } else {
            echo "<p class='message-container error'>Có lỗi xảy ra, không thể hủy lịch hẹn.</p>";
        }
    } else {
        echo "<p class='message-container error'>Không thể hủy lịch hẹn này, hoặc lịch hẹn đã được xác nhận.</p>";
    }
}

// Truy vấn lịch sử đặt lịch hẹn của khách hàng
$sql = "SELECT * FROM lichhen WHERE email_khach_hang = '$customer_email' "; // Lọc theo tên khách hàng
$result = mysqli_query($conn, $sql);
include "../includes/header.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Lịch sử đặt lịch hẹn</title>
    <style>
       

        h1 {
            text-align: center;
            margin: 20px 0;
            font-size: clamp(15px,2vw,24px);
            color: #34C9A5; /* Màu xanh chủ đạo */
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table thead {
            background-color: #34C9A5; /* Màu xanh chủ đạo */
            color: white;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            font-size:clamp(12px,2vw,16px);
        }

        table th {
            font-weight: bold;
        }

        table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tbody tr:hover {
            background-color: #ddd;
        }

        /* Nút hủy */
        button {
            padding: 5px 15px;
            color: white;
            background-color: #F56C93; 
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #F38DAB;
        }

        button:focus {
            outline: none;
        }

        /* Bảng thông báo lỗi và thành công */
        .message-container {
            text-align: center;
            margin-top: 20px;
        }

        .message-container.success {
            color: #34C9A5; 
        }

        .message-container.error {
            color: #F56C93; 
        }
    </style>
</head>
<body>
    <div class="box" style="margin:20px">
    <h1>Lịch sử đặt lịch hẹn</h1>
    <?php if(isset($result)){?>
    <table>
        <thead>
            <tr>
                <th>Tên khách hàng</th>
                <th>Tên thú cưng</th>
                <th>Loại thú cưng</th>
                <th>Dịch vụ</th>
                <th>Ngày giờ hẹn</th>
                <th>Trạng thái</th>
                <th>Hủy</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['ten_khach_hang']; ?></td>
                <td><?php echo $row['ten_thu_cung']; ?></td>
                <td><?php echo $row['loai_thu_cung']; ?></td>
                <td><?php echo $row['ten_dich_vu']; ?></td>
                <td><?php echo $row['ngay_hen'] . ' ' . $row['gio_hen']; ?></td>
                <td><?php echo $row['trang_thai']; ?></td>
                <td>
                    <?php if ($row['trang_thai'] == 'Chờ xác nhận'): ?>
                        <form method="POST">
                            <button type="submit" name="cancel_id" value="<?php echo $row['id']; ?>">Hủy</button>
                        </form>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php } else {?>
    <h2>Bạn chưa có lịch hẹn nào.</h2>
    <?php } ?>
        </div>

</body>
</html>
<?php include "../includes/footer.php"?>