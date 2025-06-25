<?php
include('../includes/db_connect.php');
include('../includes/header.php');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM tintuc WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Tăng lượt xem
if ($row) {
    mysqli_query($conn, "UPDATE tintuc SET luot_xem = luot_xem + 1 WHERE id = $id");
}

// Lấy bài viết liên quan
$category = $row['category'] ?? '';
$related_sql = $category
    ? "SELECT * FROM tintuc WHERE category = '$category' AND id != $id ORDER BY ngay_dang DESC LIMIT 3"
    : "SELECT * FROM tintuc WHERE id != $id ORDER BY ngay_dang DESC LIMIT 3";
$related_result = mysqli_query($conn, $related_sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($row['tieu_de'] ?? 'Chi tiết tin tức'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(mb_substr(strip_tags($row['noi_dung'] ?? ''), 0, 160)); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($row['tieu_de'] ?? 'Chi tiết tin tức'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(mb_substr(strip_tags($row['noi_dung'] ?? ''), 0, 160)); ?>">
    <meta property="og:image" content="<?php echo 'http://yourdomain.com/assets/image/' . htmlspecialchars($row['anh'] ?? 'default_news.jpg'); ?>">
    <meta property="og:url" content="<?php echo 'http://yourdomain.com/news_detail.php?id=' . $id; ?>">
    <link rel="stylesheet" href="../assets/css/news_detail.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <main class="box">
        <?php if ($row): ?>
           
            <div class="news-detail-main">
                <div class="news-detail-card">
                   
                    <div class="news-detail-info">
                        
                        <h1 class="news-detail-title"><?php echo htmlspecialchars($row['tieu_de']); ?></h1>
                        <div class="news-detail-date">
                            <i class="fa-regular fa-calendar"></i>
                            <?php echo date('d/m/Y', strtotime($row['ngay_dang'])); ?>
                        </div>
                        <div class="news-detail-content">
                            <?php echo nl2br(htmlspecialchars($row['noi_dung'])); ?>
                        </div>
                        <div class="news-detail-meta">
                            
                            <span class="news-detail-views">
                                <i class="fas fa-eye"></i> <?php echo $row['luot_xem'] ?? 0; ?> lượt xem
                            </span>
                        </div>
                        <div class="news-detail-share">
                            <span>Chia sẻ:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://yourdomain.com/news_detail.php?id=' . $id); ?>" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://yourdomain.com/news_detail.php?id=' . $id); ?>&text=<?php echo urlencode($row['tieu_de']); ?>" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                        <a href="news.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay về danh sách tin</a>
                    </div>
                </div>
                <div class="related-news">
                    <h2>Bài viết liên quan</h2>
                    <div class="related-news-list">
                        <?php while ($related_row = mysqli_fetch_assoc($related_result)): ?>
                            <a class="related-news-card" href="news_detail.php?id=<?php echo $related_row['id']; ?>">
                                
                                <div class="related-news-info">
                                    <div class="related-news-title"><?php echo htmlspecialchars($related_row['tieu_de']); ?></div>
                                    <div class="related-news-date">
                                        <i class="fa-regular fa-calendar"></i>
                                        <?php echo date('d/m/Y', strtotime($related_row['ngay_dang'])); ?>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center">
                <p>Tin tức không tồn tại!</p>
                <a href="news.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay về danh sách tin</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
<?php include('../includes/footer.php'); ?>