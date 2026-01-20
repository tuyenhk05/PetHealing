<?php
include "../includes/admin_header.php";
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
    <title>PetHealing - Quản lý sản phẩm</title>
                <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <style>
        a.button {
            display: inline-block;
            padding: 15px 30px;
            margin: 20px;
            background-color: #34C9A5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 20px;
        }
        .back-button {
            background: #34C9A5;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
        }

            .back-button i {
                margin-right: 6px;
            }

            .back-button:hover {
                background: #22866E;
            }
        a.button:hover { background-color: #218838; }
    </style>
</head>
<body>
        <div   id="content">

      <?php if ($isAdmin) { ?>
 
    <div style="text-align:center">
    <h1>QUẢN LÝ SẢN PHẨM</h1>
    <a class="button" href="manage_phukien.php">Quản lý Phụ kiện</a>
    <a class="button" href="manage_thucan.php">Quản lý Thức ăn</a>
        </div>
    <?php } else {
          echo $content;
      } ?>
    </div>
</body>
</html>

