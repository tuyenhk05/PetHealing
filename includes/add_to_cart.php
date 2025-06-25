<?php
session_start();
include('../includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

   
    $id_nguoi_mua = $_COOKIE["user_id"] ? $_COOKIE["user_id"] : "";
    $id_san_pham = intval($data['id_san_pham']);
    $so_luong = intval($data['so_luong']);
    $name_sp =(string) $data["name_sp"];

    if ($id_nguoi_mua && $id_san_pham && $so_luong > 0 && $name_sp) {
        $query = "INSERT INTO gio_hang (id_nguoi_mua, id_san_pham, so_luong,name_sp) VALUES (?, ?, ?, ? )";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iiis", $id_nguoi_mua, $id_san_pham, $so_luong,$name_sp);
        mysqli_stmt_execute($stmt);
        echo "Đã thêm vào giỏ hàng";
    } else {
        http_response_code(400);
        echo "Thiếu dữ liệu hoặc chưa đăng nhập";
    }
} else {
    http_response_code(405);
    echo "Phương thức không được hỗ trợ";
}
?>
