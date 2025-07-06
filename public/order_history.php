<?php
include('../includes/db_connect.php');
$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null;
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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/order_history.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --main-font: 'Noto Sans', Arial, sans-serif;
        }
        body, input, textarea, button, select, h1, h2, h3, h4, h5, h6, .order-header, .order-info, .order-products {
            font-family: var(--main-font) !important;
            letter-spacing: 0;
        }
        .history-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 15px;
        }
        h1 {
            text-align: center;
            color: #20b2aa;
            font-weight: 800;
            font-size: 2.4rem;
            margin-bottom: 38px;
            font-family: var(--main-font) !important;
        }
        .order-block {
            background: #eafffa;
            border-radius: 15px;
            box-shadow: 0 2px 16px #0001;
            margin-bottom: 24px;
            padding: 28px 28px 15px 28px;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #1a6160;
            font-size: 1.08rem;
            margin-bottom: 9px;
        }
        .order-info {
            background: #fff;
            border-radius: 9px;
            padding: 11px 13px;
            font-size: 1.04rem;
            color: #266;
            margin-bottom: 13px;
            box-shadow: 0 1px 4px #20b2aa10;
        }
        .order-info b { color: #13847a; font-weight: 700;}
        .order-products ul {
            margin: 0; padding-left: 20px;
        }
        .order-products li {
            font-size: 1.01rem;
            color: #333;
            margin-bottom: 3px;
        }
        @media (max-width: 700px) {
            .order-header { flex-direction: column; align-items: flex-start; gap: 5px;}
            .order-block { padding: 17px 8px 10px 8px;}
        }
    </style>
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
