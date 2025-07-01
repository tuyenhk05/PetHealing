<?php
include "../includes/header.php";
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if ($vai_tro == 'Admin') {
    $isAdmin = true;
} else {
    $isAdmin = false;
    $content = "<br/><h1> Bạn không có quyền truy cập trang này. </h1><br/><br/><br/>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quản lý sản phẩm</title>
                <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <style>
        a.button {
            display: inline-block;
            padding: 15px 30px;
            margin: 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 20px;
        }
        a.button:hover { background-color: #218838; }
    </style>
</head>
<body>
      <?php if ($isAdmin) { ?>
    <div style="text-align:center">
    <h1>QUẢN LÝ SẢN PHẨM</h1>
    <a class="button" href="manage_phukien.php">Quản lý Phụ kiện</a>
    <a class="button" href="manage_thucan.php">Quản lý Thức ăn</a>
        </div>
    <?php } else {
          echo $content;
      } ?>
    
</body>
</html>
<?php include "../includes/footer.php";
?>
