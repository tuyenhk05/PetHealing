<?php
include('../includes/db_connect.php');
include('../includes/header.php');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM tintuc WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($row['tieu_de'] ?? 'Chi tiết tin tức'); ?></title>
    <link rel="stylesheet" href="../assets/css/news_detail.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <main>
        <div class="news-detail-main">
            <div class="news-detail-card">
                <div class="news-detail-img-block">
                    <img src="../assets/image/<?php echo htmlspecialchars($row['anh'] ?? 'default_news.jpg'); ?>" class="news-detail-img" alt="Ảnh tin tức">
                </div>
                <div class="news-detail-info">
                    <div class="news-detail-title"><?php echo htmlspecialchars($row['tieu_de'] ?? 'Không tìm thấy tin tức'); ?></div>
                    <div class="news-detail-date">
                        <i class="fa-regular fa-calendar"></i>
                        <?php echo isset($row['ngay_dang']) ? date('d/m/Y', strtotime($row['ngay_dang'])) : ''; ?>
                    </div>
                    <div class="news-detail-content">
                        <?php echo nl2br(htmlspecialchars($row['noi_dung'] ?? '')); ?>
                    </div>
                    <a href="news.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay về danh sách tin</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
