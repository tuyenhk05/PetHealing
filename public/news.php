<?php
include('../includes/db_connect.php');
include('../includes/header.php');

// Phân trang
$per_page = 6;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $per_page;
$sql = "SELECT * FROM tintuc ORDER BY ngay_dang DESC LIMIT $per_page OFFSET $offset";
$result = mysqli_query($conn, $sql);

// Đếm tổng số bài viết
$sql_total = "SELECT COUNT(*) as total FROM tintuc";
$result_total = mysqli_query($conn, $sql_total);
$total_articles = mysqli_fetch_assoc($result_total)['total'];
$total_pages = ceil($total_articles / $per_page);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tin tức thú cưng mới nhất</title>
    <meta name="description" content="Cập nhật tin tức mới nhất về chăm sóc thú cưng, sức khỏe và các mẹo hữu ích tại PetHealing.">
    <link rel="stylesheet" href="../assets/css/news.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <main>
        <h1 class="news-section-title">Tin tức thú cưng mới nhất</h1>
        <div class="news-list">
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            $image = !empty($row['anh']) ? $row['anh'] : 'default_news.jpg';
            ?>
            <a class="news-card" href="news_detail.php?id=<?php echo $row['id']; ?>">
               
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
        <div class="news-sidebar">
            
            <h3>Bài viết nổi bật</h3>
            <ul>
                <?php
                $sql_popular = "SELECT * FROM tintuc ORDER BY luot_xem DESC LIMIT 3";
                $result_popular = mysqli_query($conn, $sql_popular);
                while ($row_popular = mysqli_fetch_assoc($result_popular)) {
                    ?>
                    <li><a href="news_detail.php?id=<?php echo $row_popular['id']; ?>"><?php echo htmlspecialchars($row_popular['tieu_de']); ?></a></li>
                <?php } ?>
            </ul>
        </div>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">Trước</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php echo $i == $page ? 'style="background: #1d4ed8;"' : ''; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Sau</a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
<?php include('../includes/footer.php'); ?>