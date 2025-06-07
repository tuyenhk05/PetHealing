<?php 
include('../includes/db_connect.php');
include('../includes/header.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tin tức thú cưng mới nhất</title>
    <link rel="stylesheet" href="../assets/css/news.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <main>
        <h1 class="news-section-title">Tin tức thú cưng mới nhất</h1>
        <div class="news-list">
        <?php
        $sql = "SELECT * FROM tintuc ORDER BY ngay_dang DESC";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $image = !empty($row['anh']) ? $row['anh'] : 'default_news.jpg';
            ?>
            <a class="news-card" href="news_detail.php?id=<?php echo $row['id']; ?>">
                <div class="news-thumb-wrap">
                    <img src="../assets/image/<?php echo htmlspecialchars($image); ?>" alt="Ảnh tin tức" class="news-thumb">
                </div>
                <div class="news-info">
                    <div class="news-title"><?php echo htmlspecialchars($row['tieu_de']); ?></div>
                    <div class="news-date">
                        <i class="fa-regular fa-calendar"></i>
                        <?php echo date('d/m/Y', strtotime($row['ngay_dang'])); ?>
                    </div>
                    <div class="news-summary">
                        <?php
                        $tomtat = strip_tags($row['noi_dung']);
                        echo mb_substr($tomtat, 0, 90) . (mb_strlen($tomtat) > 90 ? '...' : '');
                        ?>
                    </div>
                </div>
            </a>
        <?php } ?>
        </div>
    </main>
</body>
</html>
