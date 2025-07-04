<?php
include('../includes/db_connect.php');
$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null;
if (!$user_id) {
    header("Location: ./login.php");
    exit();
}

$receiver_name = $_POST['ten_nguoi_nhan'];
$receiver_phone = $_POST['so_dien_thoai'];
$receiver_address = $_POST['dia_chi'];
$payment_method = $_POST['payment_method'];

// Lấy dữ liệu giỏ hàng
$sql = "SELECT gh.id_san_pham, gh.name_sp, sp.gia, sp.loai, gh.so_luong
        FROM gio_hang gh
        JOIN (
            SELECT id, ten AS name_sp, gia, 'phukien' AS loai FROM phukien
            UNION ALL
            SELECT id, ten AS name_sp, gia, 'thucan' AS loai FROM thucan
        ) AS sp ON gh.id_san_pham = sp.id AND gh.name_sp = sp.name_sp
        WHERE gh.id_nguoi_mua = ?";
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
if (empty($products)) {
    header("Location: cart.php?empty");
    exit();
}

// Tạo mã đơn hàng
$order_code = 'DH' . date('YmdHis') . rand(100,999);
// Tạo đơn mới
mysqli_query($conn, "INSERT INTO lichsumuahang(user_id, receiver_name, receiver_phone, receiver_address, payment_method, order_time, order_code, total) VALUES
    ($user_id, '$receiver_name', '$receiver_phone', '$receiver_address', '$payment_method', NOW(), '$order_code', $total)
");
$order_id = mysqli_insert_id($conn);

// Thêm từng sản phẩm vào chi tiết đơn & trừ số lượng tồn kho
foreach ($products as $p) {
    // Trừ số lượng tồn
    if($p['loai']=='phukien')
        mysqli_query($conn,"UPDATE phukien SET so_luong=so_luong-{$p['so_luong']} WHERE id={$p['id_san_pham']}");
    else
        mysqli_query($conn,"UPDATE thucan SET so_luong=so_luong-{$p['so_luong']} WHERE id={$p['id_san_pham']}");
    // Ghi vào chi tiết đơn hàng
    mysqli_query($conn,"INSERT INTO lichsumuahangchitiet(order_id, product_id, product_name, quantity, unit_price)
    VALUES ($order_id, {$p['id_san_pham']}, '{$p['name_sp']}', {$p['so_luong']}, {$p['gia']})");
}
// Xóa giỏ hàng
mysqli_query($conn, "DELETE FROM gio_hang WHERE id_nguoi_mua=$user_id");

// Thành công thì chuyển về checkout, kèm thông báo
header("Location: checkout.php?success=1&order=$order_id");
exit;
?>
