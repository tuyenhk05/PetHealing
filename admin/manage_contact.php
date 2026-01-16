<?php
include('../includes/db_connect.php');

// Kiểm tra quyền hạn (Role-based Access Control)
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
$isAdmin = ($vai_tro == 'Admin');

if (!$isAdmin) {
    include "../includes/admin_header.php";
    echo "<div id='content' class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm border-0 rounded-4'>
                <h1 class='display-4'><i class='fa-solid fa-user-lock'></i></h1>
                <h3>Thông báo: Bạn không có quyền truy cập vào trang này!</h3>
                <p>Khu vực này chỉ dành cho tài khoản Quản trị viên.</p>
                <a href='index.php' class='btn btn-outline-danger mt-3 rounded-pill'>Quay lại Trang chủ</a>
            </div>
          </div>";
    exit();
}

// 1. Logic Phân trang (Pagination) chuẩn hóa
$limit = 6;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1)
    $page = 1;
$offset = ($page - 1) * $limit;

// Tính tổng số liên hệ để chia trang
$total_res = $conn->query("SELECT COUNT(*) as total FROM contact");
$total_contacts = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_contacts / $limit);

// 2. Lấy dữ liệu liên hệ (Read with Pagination)
$sql = "SELECT * FROM contact ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

include "../includes/admin_header.php";
?>

<style>
    /* CSS CHUẨN HÓA HỆ THỐNG QUẢN TRỊ PETHEALING */
    :root {
        --pet-green: #2EB292;
        --pet-hover: #248f76;
        --pet-orange: #e67e22;
        --bg-light: #f4f7f6;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--bg-light);
    }

    #content {
        padding: 30px 40px;
        transition: all 0.3s ease;
    }

    .section-title {
        color: var(--pet-green);
        font-weight: 700;
        position: relative;
        padding-bottom: 10px;
        margin-bottom: 30px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        left: 0; bottom: 0;
        width: 50px; height: 4px;
        background-color: var(--pet-green);
        border-radius: 2px;
    }

    .card-custom {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        background: white;
        overflow: hidden;
    }

    .table thead {
        background-color: #f8f9fa;
    }

    .contact-btn {
        background-color: var(--pet-green);
        color: white;
        padding: 8px 16px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
    }

    .contact-btn:hover {
        background-color: var(--pet-hover);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(46, 178, 146, 0.2);
    }

    /* Pagination Styling chuẩn */
    .pagination .page-link {
        color: var(--pet-green);
        border: none;
        margin: 0 5px;
        border-radius: 8px;
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .pagination .page-item.active .page-link {
        background-color: var(--pet-green);
        color: white;
    }

    .msg-preview {
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        font-size: 0.9rem;
    }

    .time-badge {
        font-size: 0.75rem;
        background: #f0fdfa;
        color: var(--pet-green);
        border: 1px solid #d1fae5;
    }
</style>

<div id="content">
    <div class="container-fluid">
        <!-- Tiêu đề trang -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="section-title mb-0">Quản lý liên hệ khách hàng</h2>
                <p class="text-secondary small mt-2">Nơi lắng nghe tâm tư và phản hồi từ các quan khách rứa mô!</p>
            </div>
        </div>

        <!-- BẢNG DANH SÁCH LIÊN HỆ -->
        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">Khách hàng</th>
                                <th>Thông tin liên lạc</th>
                                <th>Chủ đề</th>
                                <th style="width: 25%;">Nội dung lời nhắn</th>
                                <th>Thời gian gửi</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                                    </td>
                                    <td>
                                        <div class="small text-dark"><i class="fa-solid fa-envelope text-muted me-1"></i> <?= htmlspecialchars($row['email']) ?></div>
                                        <div class="small text-muted"><i class="fa-solid fa-phone text-muted me-1"></i> <?= htmlspecialchars($row['phone']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-primary border rounded-pill px-3">
                                            <?= htmlspecialchars($row['subject']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="msg-preview text-muted">
                                            <?= htmlspecialchars($row['message']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge time-badge rounded-pill px-2 py-1">
                                            <i class="fa-regular fa-clock me-1"></i>
                                            <?= date('H:i d/m/Y', strtotime($row['created_at'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a class="contact-btn" target="_blank"
                                           href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= $row['email'] ?>&su=<?= urlencode('Phản hồi từ PetHealing: ' . $row['subject']) ?>">
                                           <i class="fa-solid fa-paper-plane"></i> Phản hồi
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-5 text-secondary">Hiện tại chưa có khách hàng nào gửi thư tín rứa mô!</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PHÂN TRANG (Pagination) -->
        <?php if ($total_pages > 1): ?>
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link shadow-sm" href="?page=<?= $page - 1 ?>">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link shadow-sm" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link shadow-sm" href="?page=<?= $page + 1 ?>">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>