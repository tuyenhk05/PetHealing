<?php
include('../includes/db_connect.php');
$user_id = isset($_COOKIE['user_name']) ? $_COOKIE['user_id'] : null;
if (!$user_id) {
    header("Location: ./login.php");
    exit();
}

// Lấy danh sách đơn hàng
$sql = "SELECT * FROM lichsumuahang WHERE user_id=? ORDER BY order_time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$orders = mysqli_stmt_get_result($stmt);
include('../includes/header.php');

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử mua hàng</title>
    <link rel="stylesheet" href="../assets/css/order_history.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<main class="history-container">
    <h1>Lịch sử mua hàng</h1>
    <?php while($order = mysqli_fetch_assoc($orders)): ?>
        <section class="order-block">
            <div class="order-header">
                <span><b>Mã đơn hàng:</b> <?php echo $order['order_code']; ?></span>
                <span><b>Ngày:</b> <?php echo $order['order_time']; ?></span>
                <span><b>Tổng cộng:</b> <?php echo number_format($order['total'],0,'','.'); ?> VND</span>
            </div>
            <div class="order-info">
                <b>Người nhận:</b> <?php echo htmlspecialchars($order['receiver_name']); ?>,
                <b>Điện thoại:</b> <?php echo htmlspecialchars($order['receiver_phone']); ?>,
                <b>Địa chỉ:</b> <?php echo htmlspecialchars($order['receiver_address']); ?>,
                <b>Phương thức:</b> <?php echo $order['payment_method']=='cod'?'Thanh toán khi nhận hàng':'Chuyển khoản/Quét mã QR'; ?>
            </div>
            <div class="order-products">
                <ul>
                <?php
                $detail = mysqli_query($conn, "SELECT * FROM lichsumuahangchitiet WHERE order_id=" . $order['order_id']);
                while($prod = mysqli_fetch_assoc($detail)): ?>
                    <li>
                        <?php echo htmlspecialchars($prod['product_name']); ?> - 
                        Số lượng: <?php echo $prod['quantity']; ?> - 
                        Đơn giá: <?php echo number_format($prod['unit_price'],0,'','.'); ?> VND
                    </li>
                <?php endwhile; ?>
                </ul>
            </div>
        </section>
    <?php endwhile; ?>
</main>
</body>
</html>
 <?php include('../includes/footer.php'); ?>