<?php
include('../includes/db_connect.php');

// Kiểm tra quyền hạn (Role-based Access Control - Kiểm tra quyền hạn dựa trên vai trò rứa mô)
$vai_tro_cookie = isset($_COOKIE['vai_tro']) ? $_COOKIE['vai_tro'] : '';
$isAdmin = ($vai_tro_cookie === 'Admin');

if (!$isAdmin) {
    include "../includes/admin_header.php";
    echo "<div id='content' class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm border-0 rounded-4'>
                <h1 class='display-4'><i class='fa-solid fa-user-shield'></i></h1>
                <h3>Thông báo: Huynh không có lệnh bài để vào cấm địa này rứa mô!</h3>
                <p>Khu vực này chỉ dành cho tài khoản Quản trị viên cấp cao.</p>
                <a href='index.php' class='btn btn-outline-danger mt-3 rounded-pill px-4'>Quay lại Dashboard</a>
            </div>
          </div>";
    exit();
}

// 1. Tầng xử lý xóa người dùng (Hard Delete Logic - Logic xóa vĩnh viễn rứa)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    // Đảm bảo không tự xóa chính mình hoặc xóa Admin khác để giữ vững long mạch rứa mô
    $conn->query("DELETE FROM nguoidung WHERE id = $id AND vai_tro != 'Admin'");
    header("Location: manage_users.php?status=success&message=Đã trục xuất thành viên khỏi hệ thống thành công rứa!");
    exit();
}

// 2. Logic Tìm kiếm và Phân trang (Search & Pagination)
$search = trim($_GET['search'] ?? "");
$limit = 8;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Xây dựng câu lệnh điều kiện tìm kiếm (Dynamic Query Building - Xây dựng truy vấn động rứa mô)
$where_clause = " WHERE 1";
if ($search) {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $where_clause .= " AND (ho_ten LIKE '%$search_esc%' OR email LIKE '%$search_esc%' OR so_dien_thoai LIKE '%$search_esc%')";
}

// Tính tổng số bản ghi (Total Records Calculation)
$total_res = $conn->query("SELECT COUNT(*) as total FROM nguoidung" . $where_clause);
$total_records = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Lấy dữ liệu người dùng (Data Fetching - Truy xuất dữ liệu rứa mô)
$sql = "SELECT * FROM nguoidung" . $where_clause . " ORDER BY id DESC LIMIT $limit OFFSET $offset";
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

    .btn-pet {
        background-color: var(--pet-green);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 8px 20px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-pet:hover {
        background-color: var(--pet-hover);
        color: white;
        transform: translateY(-2px);
    }

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

    .status-message {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        min-width: 300px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background-color: #f0fdfa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--pet-green);
        font-weight: bold;
        border: 1px solid #d1fae5;
    }
</style>

<div id="content">
    <div class="container-fluid">
        <!-- Thông báo trạng thái (Flash Messages - Thông báo nhanh rứa mô) -->
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success status-message shadow-lg border-0 fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Header Section -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="section-title mb-0">Quản lý người dùng</h2>
                <p class="text-secondary small mt-2">Danh sách thành viên đăng ký trong hệ thống PetHealing rứa mô.</p>
            </div>
            <div class="col-md-6">
                <form method="get" class="input-group shadow-sm rounded-3 overflow-hidden">
                    <input type="text" name="search" class="form-control border-0 py-2 ps-3" 
                           placeholder="Tìm theo tên, email hoặc SĐT..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-pet px-4" type="submit" style="border-radius: 0;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- BẢNG DANH SÁCH NGƯỜI DÙNG -->
        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">ID</th>
                                <th>Người dùng</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Vai trò</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4 text-secondary fw-bold">#<?= $row['id'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="user-avatar">
                                                    <?= strtoupper(substr($row['ho_ten'], 0, 1)) ?>
                                                </div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['ho_ten']) ?></div>
                                            </div>
                                        </td>
                                        <td><span class="text-muted small"><?= htmlspecialchars($row['email']) ?></span></td>
                                        <td><?= htmlspecialchars($row['so_dien_thoai']) ?></td>
                                        <td>
                                            <?php if ($row['vai_tro'] === 'Admin'): ?>
                                                <span class="badge bg-danger rounded-pill px-3 shadow-sm">Quản trị viên</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-white rounded-pill px-3 shadow-sm">Khách hàng</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($row['vai_tro'] !== 'Admin'): ?>
                                                <!-- 
                                                    TRIỆU HỒI MODAL XÁC NHẬN RỨA MÔ!
                                                    Thuật ngữ: Modal Trigger (Bộ kích hoạt hộp thoại rứa)
                                                -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3"
                                                        onclick="showConfirmModal('Bạn có chắc chắn muốn xóa người dùng <?= addslashes($row['ho_ten']) ?> khỏi hệ thống không ?', 'manage_users.php?delete_id=<?= $row['id'] ?>', 'danger')">
                                                    <i class="fa-solid fa-trash-can me-1"></i> Xóa
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small italic"><i class="fa-solid fa-lock me-1"></i> Cố định</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-5 text-secondary">Không tìm thấy kẻ nào khả nghi trong danh sách rứa mô!</td></tr>
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
                <?php
                $query_params = $_GET;
                if ($page > 1):
                    $query_params['page'] = $page - 1;
                    ?>
                    <li class="page-item">
                        <a class="page-link shadow-sm" href="?<?= http_build_query($query_params) ?>">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++):
                    $query_params['page'] = $i;
                    ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link shadow-sm" href="?<?= http_build_query($query_params) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages):
                    $query_params['page'] = $page + 1;
                    ?>
                    <li class="page-item">
                        <a class="page-link shadow-sm" href="?<?= http_build_query($query_params) ?>">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- NẠP PHÁP BẢO MODAL (Cần đặt ở cuối để JS nhận diện rứa mô) -->
<?php include('../includes/confirm_modal.php'); ?>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Tự động ẩn thông báo sau 3 giây (Flash Message Auto-hide rứa mô)
    const statusMsg = document.querySelector('.status-message');
    if (statusMsg) {
        setTimeout(() => {
            statusMsg.classList.remove('show');
            setTimeout(() => statusMsg.remove(), 500);
        }, 3000);
    }
</script>
</body>
</html>