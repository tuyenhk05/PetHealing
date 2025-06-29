<?php
include('../includes/db_connect.php');

session_start();
$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null;

if (!$user_id || !isset($_POST['id_san_pham'], $_POST['name_sp'], $_POST['action'])) {
    header("Location: cart.php");
    exit;
}

$id = $_POST['id_san_pham'];
$name_sp = $_POST['name_sp'];
$action = $_POST['action'];

// Lấy số lượng hiện tại
$sql = "SELECT so_luong FROM gio_hang WHERE id_nguoi_mua = ? AND id_san_pham = ? AND name_sp = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'iis', $user_id, $id, $name_sp);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $current = (int) $row['so_luong'];

    if ($action == 'increase') {
        $new_qty = $current + 1;
        $update = "UPDATE gio_hang SET so_luong = ? WHERE id_nguoi_mua = ? AND id_san_pham = ? AND name_sp = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, 'iiis', $new_qty, $user_id, $id, $name_sp);
        mysqli_stmt_execute($stmt);
    } elseif ($action == 'decrease') {
        if ($current > 1) {
            $new_qty = $current - 1;
            $update = "UPDATE gio_hang SET so_luong = ? WHERE id_nguoi_mua = ? AND id_san_pham = ? AND name_sp = ?";
            $stmt = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt, 'iiis', $new_qty, $user_id, $id, $name_sp);
            mysqli_stmt_execute($stmt);
        } else {
            $delete = "DELETE FROM gio_hang WHERE id_nguoi_mua = ? AND id_san_pham = ? AND name_sp = ?";
            $stmt = mysqli_prepare($conn, $delete);
            mysqli_stmt_bind_param($stmt, 'iis', $user_id, $id, $name_sp);
            mysqli_stmt_execute($stmt);
        }
    } elseif ($action == 'delete') {
        $delete = "DELETE FROM gio_hang WHERE id_nguoi_mua = ? AND id_san_pham = ? AND name_sp = ?";
        $stmt = mysqli_prepare($conn, $delete);
        mysqli_stmt_bind_param($stmt, 'iis', $user_id, $id, $name_sp);
        mysqli_stmt_execute($stmt);
    }
}

header("Location: ../public/cart.php");
exit;
