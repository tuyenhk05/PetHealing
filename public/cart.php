<?php
include('../includes/header.php');
include('../includes/db_connect.php');

// Kiểm tra nếu người dùng đã đăng nhập
$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null;
if (!$user_id) {
    echo "<p class='box'>Bạn cần đăng nhập để xem giỏ hàng.</p>";
    include('../includes/footer.php');
    exit();
}

// Truy vấn sản phẩm từ giỏ hàng kết hợp ThucAn và PhuKien
$sql = "
    SELECT gh.id_san_pham, gh.so_luong, ta.ten, ta.gia, 'thuc-an' AS loai
    FROM gio_hang gh
    JOIN ThucAn ta ON gh.id_san_pham = ta.id
    WHERE gh.id_nguoi_mua = ?
    
    UNION ALL
    
    SELECT gh.id_san_pham, gh.so_luong, pk.ten, pk.gia, 'phu-kien' AS loai
    FROM gio_hang gh
    JOIN PhuKien pk ON gh.id_san_pham = pk.id
    WHERE gh.id_nguoi_mua = ?
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ii', $user_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/cart.css">
</head>
<body>

<div class="cart-page box">
    <h1>Giỏ hàng</h1>
    <div class="cart-items">
        <?php
        $total = 0;
        if (mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)):
                $subtotal = $row['gia'] * $row['so_luong'];
                $total += $subtotal;
                ?>
                <div class="cart-item">
                    <img src="../assets/image/<?php echo htmlspecialchars($row['ten']); ?>.jpg" alt="<?php echo htmlspecialchars($row['ten']); ?>">
                    <div class="cart-item-details">
                        <h3><?php echo htmlspecialchars($row['ten']); ?></h3>
                        <p>Giá: <?php echo number_format($row['gia'], 0, '', '.'); ?> VND</p>
                        <p>Số lượng: <?php echo $row['so_luong']; ?></p>
                    </div>
                    <div class="cart-item-actions">
                        <form method="post" action="update_cart.php">
                            <input type="hidden" name="id_san_pham" value="<?php echo $row['id_san_pham']; ?>">
                            <button name="action" value="decrease">-</button>
                            <button name="action" value="increase">+</button>
                            <button name="action" value="delete">Xóa</button>
                        </form>
                    </div>
                    <div class="total">
                        <p>Tổng: <?php echo number_format($subtotal, 0, '', '.'); ?> VND</p>
                    </div>
                </div>
            <?php endwhile; ?>
            <div class="cart-total">
                <strong>Tổng cộng:</strong> <span><?php echo number_format($total, 0, '', '.'); ?> VND</span>
            </div>
            <div class="checkout-btn">
                <a href="checkout.php" class="btn-filled">Tiến hành thanh toán</a>
            </div>
        <?php else: ?>
            <p>Giỏ hàng của bạn đang trống.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<?php include('../includes/footer.php'); ?>
