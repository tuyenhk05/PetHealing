<?php
include('../includes/db_connect.php');  
include('../includes/header.php'); 

// Hàm chuyển tiếng Việt có dấu thành slug
function slugify($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $unicode = array(
        'a'=>'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd'=>'đ',
        'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i'=>'í|ì|ỉ|ĩ|ị',
        'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
    );
    foreach($unicode as $nonUnicode=>$uni) {
        $text = preg_replace("/($uni)/i", $nonUnicode, $text);
    }
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    $text = trim($text, '-');
    return $text;
}

$querybs = 'SELECT * FROM dichvu';
$result = mysqli_query($conn, $querybs);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Dịch vụ - PetHealing</title>
   <link rel="stylesheet" href="../assets/css/style.css">
   <link rel="stylesheet" href="../assets/css/services.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Liên kết với jQuery -->
    <script src="../assets/js/services.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<section class="services-section">
    <div class="intro-card-bg">
        <div class="intro-content-overlay">
            <h2>
                CHĂM SÓC <span class="highlight-pink">THÚ CƯNG</span> TOÀN DIỆN TẠI <span class="highlight-blue">PetHealing</span>
            </h2>
            <p>Chúng tôi cung cấp các dịch vụ y tế và làm đẹp chuyên nghiệp, giúp thú cưng của bạn luôn khỏe mạnh và xinh đẹp. Đội ngũ bác sĩ tận tâm, trang thiết bị hiện đại, cam kết mang đến sự hài lòng tuyệt đối.</p>
        </div>
    </div>

    <h2 class="section-title">Dịch vụ của chúng tôi</h2>

    <!-- Thanh bộ lọc -->
    <div class="filter-bar">
        <button class="filter-btn active" data-filter="all">Tất cả</button>
        <button class="filter-btn" data-filter="cham-soc">Chăm sóc</button>
        <button class="filter-btn" data-filter="kham-chua-benh">Khám chữa bệnh</button>
        <button class="filter-btn" data-filter="van-chuyen">Vận chuyển</button>
    </div>
</section>
<div class="service-row">
<?php
while($row = mysqli_fetch_assoc($result)) {
    $category_slug = slugify($row['loai']);
    $name = htmlspecialchars($row['ten_dich_vu']);
    $desc = htmlspecialchars($row['mo_ta']);
    $price = !empty($row['gia']) ? number_format($row['gia'],0,',','.').'đ' : 'Theo tình trạng';

    $image_name = $name . '.jpg';
?>
    <div class="service-card" data-category="<?php echo $category_slug; ?>" data="<?php echo $name; ?>">
        <img src="../assets/image/<?php echo $image_name; ?>" alt="<?php echo $name; ?>">
        <div class="service-content">
            <h3><?php echo $name; ?></h3>
            <p><?php echo $desc; ?></p>
            <div class="service-price"><?php echo $price; ?></div>
            <button class="button_service">Đặt lịch ngay</button>
        </div>
    </div>
<?php } ?>
</div>
<!-- Thanh phân trang -->
<div class="pagination"></div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
