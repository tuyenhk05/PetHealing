<?php
include('../includes/db_connect.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM thucan WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
} else {
    die("Thiếu ID sản phẩm.");   
}

if (isset($_POST['capnhat'])) {
    $ten = $_POST['ten'];
    $mo_ta = $_POST['mo_ta'];
    $gia = $_POST['gia'];
    $thanh_phan = $_POST['thanh_phan'];
    $cong_dung = $_POST['cong_dung'];
    $tuoi = $_POST['phu_hop_voi_tuoi_thu_cung'];
    $xuat_su = $_POST['xuat_su'];
    $image = $_POST['image'];
    $so_luong = $_POST['so_luong'];

    $sql_update = "UPDATE thucan SET 
        ten = '$ten',
        mo_ta = '$mo_ta',
        gia = '$gia',
        thanh_phan = '$thanh_phan',
        cong_dung = '$cong_dung',
        phu_hop_voi_tuoi_thu_cung = '$tuoi',
        xuat_su = '$xuat_su',
        image = '$image',
        so_luong = '$so_luong'
        WHERE id = $id";

    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='manage_thucan.php';</script>";
        exit;
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sửa sản phẩm thức ăn</title>
    <meta charset="utf-8">
        <link rel="stylesheet" href="../assets/css/edit_product.css">

</head>
<body>
    <div class="container">
        <h2>Sửa sản phẩm</h2>
        <form method="POST">
            <label>Tên sản phẩm:</label>
            <input type="text" name="ten" value="<?php echo $row['ten']; ?>">

            <label>Mô tả:</label>
            <input type="text" name="mo_ta" value="<?php echo $row['mo_ta']; ?>">

            <label>Giá:</label>
            <input type="number" name="gia" value="<?php echo $row['gia']; ?>">

            <label>Thành phần:</label>
            <input type="text" name="thanh_phan" value="<?php echo $row['thanh_phan']; ?>">

            <label>Công dụng:</label>
            <input type="text" name="cong_dung" value="<?php echo $row['cong_dung']; ?>">

            <label>Tuổi phù hợp:</label>
            <input type="text" name="phu_hop_voi_tuoi_thu_cung" value="<?php echo $row['phu_hop_voi_tuoi_thu_cung']; ?>">

            <label>Xuất xứ:</label>
            <input type="text" name="xuat_su" value="<?php echo $row['xuat_su']; ?>">

            <label>Tên ảnh (tên file hình):</label>
            <input type="text" name="image" value="<?php echo $row['image']; ?>">

            <label>Số lượng:</label>
            <input type="number" name="so_luong" value="<?php echo $row['so_luong']; ?>">

            <input type="submit" name="capnhat" value="Cập nhật">
        </form>
        <a href="manage_thucan.php" class="back-link">← Quay lại trang quản lý</a>
    </div>
</body>
</html>
