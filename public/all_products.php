<?php
include('../includes/db_connect.php');
include('../includes/header.php'); // Bao gồm header

$sql = "
    SELECT ten, mo_ta, gia, 'thuc-an' AS loai FROM ThucAn
    UNION ALL
    SELECT ten, mo_ta, gia, 'phu-kien' AS loai FROM PhuKien
    ORDER BY RAND()
";
$result = mysqli_query($conn, $sql);
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
    <script src="../assets/js/category.js" defer></script>
</head>
<body>
    <div class="inner-subnav">
        <div class="overlay">
            <h1>Cửa hàng <span>thú cưng</span></h1>
            <p><span>Boss</span> sủa một tiếng,<span> sen</span> quẹo lựa ngay.</p>

            <form id="searchForm" class="search-box">
                <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
    <!-- Danh mục sản phẩmphẩm -->
    <div class="container-category">
        <div class="store">
            <div class="category">
                <!-- Bộ lọc theo danh mục -->
                    <div class="category-filter">
                        <h3><span class="icon"></span>Danh mục</h3>
                        <ul class="category-list">
                            <li data-category="all">Tất cả</li>
                            <li data-category="thuc-an">Thức ăn</li>
                            <li data-category="phu-kien">Phụ kiện</li>
                        </ul>
                    </div>
                <div class="product">
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="card" data-category="<?php echo $row['loai']; ?>" data-name="<?php echo strtolower($row['ten']); ?>">
                            <div class="card-image" ><img src="../assets/image/<?php echo $row['ten']; ?>.jpg" alt="<?php echo $row['ten']; ?>"></div>

                            <div class="card-content">
                                <h3><?php echo $row['ten']; ?></h3>
                                <p><?php echo $row['mo_ta']; ?></p>
                                <span><?php echo $row['gia']; ?> VND</span>

                                <div class="actions">
                                    <a href="#" class="details">Chi tiết</a>
                                    <button class="btn-filled"><i class="fa fa-shopping-cart"></i> Thêm</button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="pagination" style="margin-top: 20px; text-align:center;"></div>
        </div>
    </div>
</body>
</html>
<?php 
include('../includes/footer.php'); // Bao gồm footer
?>
