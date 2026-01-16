<?php
// 1. Tầng khởi tạo và kiểm tra "Lệnh bài" (Authentication)
session_start();
include('../includes/db_connect.php');

// Kiểm tra xem khách hàng đã đăng nhập chưa
if (!isset($_COOKIE['user_name'])) {
    echo "Vui lòng đăng nhập để xem lịch sử đặt lịch hẹn!";
    exit();
}

$customer_id = $_COOKIE['user_id'];
$message = "";
$message_type = "";

/**
 * 2. Tầng xử lý Hủy lịch hẹn (Request Handling)
 * Sử dụng GET để phối hợp nhịp nhàng với Confirm Modal rứa mô.
 * Thuật ngữ: Data Manipulation (Thao tác dữ liệu rứa)
 */
if (isset($_GET['cancel_id'])) {
    $cancel_id = intval($_GET['cancel_id']);

    // Kiểm tra lịch hẹn đúng chủ và ở trạng thái Chờ xác nhận rứa mô
    $sql_check = "SELECT * FROM lichhen WHERE id = $cancel_id AND id_khach = '$customer_id' AND trang_thai = 'Chờ xác nhận'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        $sql_update = "UPDATE lichhen SET trang_thai = 'Đã hủy' WHERE id = $cancel_id";
        if (mysqli_query($conn, $sql_update)) {
            $message = "Lịch hẹn đã được hủy thành công rứa mô.";
            $message_type = "success";
        } else {
            $message = "Có lỗi xảy ra, không thể hủy lịch hẹn rứa!";
            $message_type = "error";
        }
    } else {
        $message = "Không thể hủy lịch này (có thể đã được xác nhận hoặc không phải của Huynh) rứa!";
        $message_type = "error";
    }
}

// 3. Truy vấn lịch sử (Data Fetching)
$sql = "SELECT * FROM lichhen WHERE id_khach = '$customer_id' ORDER BY ngay_hen DESC, gio_hen DESC";
$result = mysqli_query($conn, $sql);

include "../includes/header.php";
?>

  
    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <title>Lịch sử đặt lịch hẹn</title>
    <style>
        /* GIỮ NGUYÊN CSS CỦA HUYNH RỨA MÔ */
        h1 {
            text-align: center;
            margin: 20px 0;
            font-size: clamp(15px,2vw,24px);
            color: #34C9A5; 
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table thead {
            background-color: #34C9A5; 
            color: white;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            font-size:clamp(12px,2vw,16px);
        }

        table th { font-weight: bold; }

        table tbody tr:nth-child(even) { background-color: #f2f2f2; }

        table tbody tr:hover { background-color: #ddd; }

        /* Nút hủy y quán */
        .btn-cancel-pet {
            padding: 5px 15px;
            color: white;
            background-color: #F56C93; 
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
            border: none;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-cancel-pet:hover { background-color: #F38DAB; }

        .message-container { text-align: center; margin-top: 20px; }
        .message-container.success { color: #34C9A5; }
        .message-container.error { color: #F56C93; }
    </style>

    <div class="box" style="margin:20px">
        <h1>Lịch sử đặt lịch hẹn</h1>

        <!-- Vùng hiển thị thông báo rứa mô -->
        <?php if ($message): ?>
            <div class="message-container <?= $message_type ?>">
                <i class="fa-solid fa-circle-info me-2"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($result && mysqli_num_rows($result) > 0): ?>
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
                            <td><?php echo htmlspecialchars($row['ten_khach_hang']); ?></td>
                            <td><?php echo htmlspecialchars($row['ten_thu_cung']); ?></td>
                            <td><?php echo htmlspecialchars($row['loai_thu_cung']); ?></td>
                            <td><?php echo htmlspecialchars($row['ten_dich_vu']); ?></td>
                            <td><?php echo $row['ngay_hen'] . ' ' . $row['gio_hen']; ?></td>
                            <td>
                                <b><?php echo $row['trang_thai']; ?></b>
                            </td>
                            <td>
                                <?php if ($row['trang_thai'] == 'Chờ xác nhận'): ?>
                                    <!-- 
                                        TUYỆT KỸ TRIỆU HỒI MODAL DÙNG CHUNG RỨA MÔ!
                                        Thuật ngữ: Modal Trigger (Kích hoạt hộp thoại rứa)
                                    -->
                                
                                    <button type="button" class="btn-cancel-pet" 
                                            onclick="showConfirmModal('Bạn có thực sự muốn hủy đặt lịch này không ?', 'history_appointments.php?cancel_id=<?= $row['id'] ?>', 'danger')">
                                        <i class="fa-solid fa-xmark"></i> Hủy lịch
                                    </button>
                                <?php else: ?>
                                    <span style="color: #999; font-style: italic;">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-center py-5">
                <h2>Bạn chưa có lịch hẹn nào ẩn cư tại đây rứa mô.</h2>
                <a href="services.php" style="color: #34C9A5; font-weight: bold;">Đặt lịch ngay rứa!</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- TRIỆU HỒI PHÁP BẢO MODAL (Phải nằm ngoài thẻ Table rứa) -->
    <?php include('../includes/confirm_modal.php'); ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<?php include "../includes/footer.php"; ?>