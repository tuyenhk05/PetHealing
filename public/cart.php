<?php
include('../includes/header.php');
include('../includes/db_connect.php');

$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null;
if (!$user_id) {
    $content = "<br/><h1 class='box'>Bạn cần đăng nhập để xem giỏ hàng.</h1><br/>";
} else {
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
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/cart.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
<?php if ($user_id): ?>
    <div class="cart-page box">
        <h1>Giỏ hàng</h1>
            <div class="cart-items">
                <?php
                $total = 0;
                if (mysqli_num_rows($result) > 0):
                    while ($row = mysqli_fetch_assoc($result)):
                        $subtotal = $row['gia'] * $row['so_luong'];
                        $total += $subtotal;
                        $productKey = $row['id_san_pham'] . '|' . $row['name_sp'];
                        ?>
                        <div class="cart-item">
                           
                            <img src="../assets/image/<?php echo htmlspecialchars($row['name_sp']); ?>.jpg"
                                 alt="<?php echo htmlspecialchars($row['name_sp']); ?>"
                                 onerror="this.src='../assets/image/default.jpg'">
                            <div class="cart-item-details">
                                <h3><?php echo htmlspecialchars($row['name_sp']); ?></h3>
                                <p>Giá: <?php echo number_format($row['gia'], 0, '', '.'); ?> VND</p>
                                <p>Số lượng: <?php echo $row['so_luong']; ?></p>
                            </div>
                            <div class="cart-item-actions">
                                <form method="post" action="../includes/uppdate_to_cart.php" style="display: inline-block;">
                                    <input type="hidden" name="id_san_pham" value="<?php echo $row['id_san_pham']; ?>">
                                    <input type="hidden" name="name_sp" value="<?php echo $row['name_sp']; ?>">
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
                        <strong>Tổng cộng (tất cả):</strong>
                        <span><?php echo number_format($total, 0, '', '.'); ?> VND</span>
                    </div>
                   <div class="checkout-btn">
                    <a href="checkout.php" class="btn-filled">Thanh toán các sản phẩm đã chọn</a>
                    </div>
                <?php else: ?>
                    <p>Giỏ hàng của bạn đang trống.</p>
                <?php endif; ?>
            </div>
    </div>
<?php else: ?>
    <?php echo $content; ?>
<?php endif; ?>
</body>
</html>

<?php include('../includes/footer.php'); ?>