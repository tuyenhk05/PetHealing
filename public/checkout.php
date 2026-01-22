<?php
include('../includes/db_connect.php');

// Lấy user_id từ cookie
$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null;
if (!$user_id) {
    header("Location: ./login.php");
    exit();
}

// Lấy giỏ hàng
$sql = "
    SELECT 
        gh.id_san_pham,
        gh.name_sp,
        sp.gia,
        sp.loai,
        SUM(gh.so_luong) AS so_luong
    FROM gio_hang gh
    JOIN (
        SELECT id, ten AS name_sp, gia, 'phukien' AS loai FROM phukien
        UNION ALL
        SELECT id, ten AS name_sp, gia, 'thucan' AS loai FROM thucan
    ) AS sp 
    ON gh.id_san_pham = sp.id AND gh.name_sp = sp.name_sp
    WHERE gh.id_nguoi_mua = ?
    GROUP BY gh.id_san_pham, gh.name_sp, sp.gia, sp.loai
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
$total = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
    $total += $row['gia'] * $row['so_luong'];
}

// Xử lý khi submit thanh toán
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_name = $_POST['ten_nguoi_nhan'];
    $receiver_phone = $_POST['so_dien_thoai'];
    $receiver_address = $_POST['dia_chi'];
    $payment_method = $_POST['payment_method'];
    $order_time = date('Y-m-d H:i:s');
    $order_code = 'DH' . date('YmdHis') . rand(10,99);
    $status = 0;
    // Insert vào bảng lichsumuahang
    $stmt = $conn->prepare("INSERT INTO lichsumuahang (user_id, receiver_name, receiver_phone, receiver_address, payment_method, order_time, order_code, total,status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
    $stmt->bind_param("issssssii", $user_id, $receiver_name, $receiver_phone, $receiver_address, $payment_method, $order_time, $order_code, $total,$status);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert chi tiết từng sản phẩm
    foreach ($products as $prod) {
        $stmt = $conn->prepare("INSERT INTO lichsumuahangchitiet (order_id, product_id, product_name, quantity, unit_price)
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisii", $order_id, $prod['id_san_pham'], $prod['name_sp'], $prod['so_luong'], $prod['gia']);
        $stmt->execute();

        // Trừ số lượng sản phẩm trong bảng phukien/thucan
        if ($prod['loai'] == 'phukien') {
            $conn->query("UPDATE phukien SET so_luong = GREATEST(so_luong - {$prod['so_luong']},0) WHERE id = {$prod['id_san_pham']}");
        } else {
            $conn->query("UPDATE thucan SET so_luong = GREATEST(so_luong - {$prod['so_luong']},0) WHERE id = {$prod['id_san_pham']}");
        }
    }

    // Xóa giỏ hàng của người dùng
    $conn->query("DELETE FROM gio_hang WHERE id_nguoi_mua = $user_id");

    $success = true;
}
include('../includes/header.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận thanh toán</title>
    <!-- Google Fonts: Noto Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --main-font: 'Noto Sans', Arial, sans-serif;
        }
        body, input, textarea, button, select, h1, h2, h3, h4, h5, h6 {
            font-family: var(--main-font) !important;
            letter-spacing: 0;
        }
    </style>
</head>
<body>
<div class="checkout-wrapper">
    <?php if (!$success): ?>
    <form class="checkout-form" method="post" action="">
        <h1 class="checkout-title">Xác nhận thanh toán</h1>

        <div class="shipping-info">
            <input type="text" name="ten_nguoi_nhan" placeholder="Họ tên người nhận" required>
            <input type="text" name="so_dien_thoai" placeholder="Số điện thoại" required>
            <input type="text" name="dia_chi" placeholder="Địa chỉ nhận hàng" required>
        </div>

        <div class="checkout-main">
            <div class="checkout-products">
                <?php foreach($products as $product): ?>
                <div class="checkout-product">
                    <img src="../assets/image/<?php echo htmlspecialchars($product['name_sp']); ?>.jpg"
                         alt="<?php echo htmlspecialchars($product['name_sp']); ?>"
                         onerror="this.src='../assets/image/default.jpg'">
                    <div class="product-info">
                        <div class="product-name"><?php echo htmlspecialchars($product['name_sp']); ?></div>
                        <div class="product-qty">Số lượng: <?php echo $product['so_luong']; ?></div>
                        <div class="product-price"><?php echo number_format($product['gia'], 0, '', '.'); ?> VND</div>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="checkout-total">
                    Tổng cộng: <span><?php echo number_format($total, 0, '', '.'); ?> VND</span>
                </div>
            </div>
            <div class="checkout-method">
                <label class="method-label">Chọn phương thức thanh toán</label>
                <div class="payment-method-group">
                    <label class="payment-radio">
                        <input type="radio" name="payment_method" value="cod" checked>
                        Thanh toán khi nhận hàng
                    </label>
                    <label class="payment-radio">
                        <input type="radio" name="payment_method" value="bank">
                        Chuyển khoản/Quét mã QR
                    </label>
                </div>
                <div class="checkout-btn-group">
                    <button type="submit" class="checkout-btn">Thanh toán</button>
                </div>
            </div>
        </div>
    </form>
    <?php else: ?>
    <div id="successModal" class="success-modal" style="display:block">
        <div class="modal-content">
            <h2>🎉 Đặt hàng thành công!</h2>
            <p>Cảm ơn bạn đã mua hàng tại <b>PetHealing</b>.<br>Bạn muốn:</p>
            <div class="modal-actions">
                <a href="index.php" class="btn-home">Về trang chủ</a>
                <a href="order_history.php" class="btn-history">Xem lịch sử mua hàng</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
<?php include('../includes/footer.php'); ?>
