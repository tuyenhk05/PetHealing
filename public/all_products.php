<?php
include('../includes/db_connect.php');
include('../includes/header.php'); // Bao gồm header

$sql = "SELECT * FROM thucan ";
$result = mysqli_query($conn, $sql);
$sql2  = "SELECT * FROM phukien";
$query = mysqli_query($conn, $sql2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHealing - Trang chủ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/category.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Liên kết với jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>


    <div class="inner-subnav">
        <div class="overlay">
            <h1>Cửa hàng <span>thú cưng</span></h1>
            <p>Khám phá các sản phẩm chất lượng cao dành cho thú cưng của bạn</p>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Tìm kiếm sản phẩm..." />
            </div>

        </div>
    </div>


    <!-- Danh mục sản phẩmphẩm -->
    <div class="container-category">
        <div class="store">
            <div class="category">
                <div class="category-filter">
                    <h3><span class="icon">🧃</span> Danh mục</h3>
                    <ul class="category-list">
                        <li class="active">Tất cả</li>
                        <li>Thức ăn</li>
                        <li>Phụ kiện</li>
                        <li>Đồ chơi</li>
                        <li>Sức khỏe</li>
                        <li>Làm đẹp</li>
                    </ul>
                </div>
                <div class="product">                    <?php
                    while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="card">
                        <img src="../assets/image/<?php echo $row['ten']; ?>.jpg" alt="<?php echo $row['ten']; ?>">

                        <div class="card-content">
                            <h3><?php echo $row['ten'] ;?> </h3>
                            <p><?php echo $row['mo_ta'] ?></p>
                            <span><?php echo $row['gia']; ?>VNĐ</span>

                       
                       
                            <div class="actions">
                                <a href="#" class="details">Chi tiết</a>
                                <button class="btn-filled"><i class="fa fa-shopping-cart"></i> Thêm</button>
                            </div>
                        </div>
                    </div><?php } ?>                    <?php
                    while($row2 = mysqli_fetch_assoc($query)) {
                    ?>
                    <div class="card">
                        <img src="../assets/image/<?php echo $row2['ten']; ?>.jpg" alt="<?php echo $row2['ten']; ?>">

                        <div class="card-content">
                            <h3><?php echo $row2['ten'] ;?></h3>
                            <p><?php echo $row2['mo_ta'] ?></p>
                            <span><?php echo $row2['gia']; ?> VNĐ</span>

                        
                            <div class="actions">
                                <a href="#" class="details">Chi tiết</a>
                                <button class="btn-filled"><i class="fa fa-shopping-cart"></i> Thêm</button>
                            
                        </div>
                            
                        </div>
                    </div><?php } ?>

                </div>
            </div>
        </div>
    </div>


</body>
</html>
<?php 
include('../includes/footer.php'); // Bao gồm footer
?>