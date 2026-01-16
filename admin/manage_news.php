<?php
include('../includes/db_connect.php');

// Kiểm tra quyền hạn (Role-based Access Control - Kiểm soát truy cập dựa trên vai trò rứa mô)
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
$isAdmin = ($vai_tro == 'Admin');

if (!$isAdmin) {
    include "../includes/admin_header.php";
    echo "<div id='content' class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm border-0 rounded-4'>
                <h1 class='display-4'><i class='fa-solid fa-user-secret'></i></h1>
                <h3>Thông báo: Huynh không có lệnh bài Admin để vào cấm địa này rứa mô!</h3>
                <p>Khu vực quản trị chỉ dành cho tài khoản có thẩm quyền.</p>
                <a href='index.php' class='btn btn-outline-danger mt-3 rounded-pill px-4'>Quay lại Dashboard</a>
            </div>
          </div>";
    exit();
}

// 1. Tầng xử lý logic - Xóa tin tức (Delete Logic - Logic xóa dữ liệu rứa mô)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tintuc WHERE id = $id");
    header("Location: manage_news.php?status=deleted&message=Đã tiễn bài tin tức về cõi hư vô thành công rứa mô!");
    exit();
}

// 2. Tầng xử lý logic - Thêm hoặc Cập nhật tin (Create / Update - Thêm mới hoặc Cập nhật)
if (isset($_POST['save_news'])) {
    $tieu_de = $_POST['tieu_de'];
    $noi_dung = $_POST['noi_dung'];
    $ngay_dang = $_POST['ngay_dang'];
    $news_id = isset($_POST['news_id']) ? intval($_POST['news_id']) : 0;

    if ($news_id > 0) {
        // Cập nhật thông tin (Update)
        $stmt = $conn->prepare("UPDATE tintuc SET tieu_de=?, noi_dung=?, ngay_dang=? WHERE id=?");
        $stmt->bind_param("sssi", $tieu_de, $noi_dung, $ngay_dang, $news_id);
        $msg = "Đã cập nhật bài đăng thành công!";
    } else {
        // Thêm mới (Create)
        $stmt = $conn->prepare("INSERT INTO tintuc (tieu_de, noi_dung, ngay_dang) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $tieu_de, $noi_dung, $ngay_dang);
        $msg = "Đã đăng tin tức mới vào sử sách thành công!";
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: manage_news.php?status=success&message=$msg");
        exit();
    }
}

// 3. Logic Tìm kiếm & Lọc theo ngày (Search & Date Filtering Logic)
$search = trim($_GET['search'] ?? "");
$filter_date = $_GET['filter_date'] ?? "";

$where_clauses = ["1=1"];

if ($search) {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $where_clauses[] = "(tieu_de LIKE '%$search_esc%' OR noi_dung LIKE '%$search_esc%')";
}

if ($filter_date) {
    $date_esc = mysqli_real_escape_string($conn, $filter_date);
    $where_clauses[] = "ngay_dang = '$date_esc'";
}

$where_sql = implode(" AND ", $where_clauses);

// 4. Phân trang (Pagination - Phân trang rứa mô) chuẩn hóa
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Tính tổng số bản ghi sau khi lọc
$total_res = $conn->query("SELECT COUNT(*) as total FROM tintuc WHERE $where_sql");
$total_news = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_news / $limit);

// Lấy dữ liệu tin tức (Read with Pagination - Truy xuất dữ liệu có phân trang)
$news = $conn->query("SELECT * FROM tintuc WHERE $where_sql ORDER BY id DESC LIMIT $limit OFFSET $offset");

// 5. Nếu đang trong chế độ Sửa (Edit mode)
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $e_id = intval($_GET['edit_id']);
    $res_edit = $conn->query("SELECT * FROM tintuc WHERE id = $e_id");
    $edit_data = $res_edit->fetch_assoc();
}

include "../includes/admin_header.php";
?>

<style>
    /* CSS CHUẨN HÓA HỆ THỐNG QUẢN TRỊ PETHEALING */
    :root {
        --pet-green: #2EB292;
        --pet-hover: #248f76;
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

    .filter-wrapper {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .btn-pet {
        background-color: var(--pet-green);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-pet:hover {
        background-color: var(--pet-hover);
        color: white;
        transform: translateY(-2px);
    }

    .card-custom {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        background: white;
        overflow: hidden;
    }

    .form-container {
        display: <?= (isset($_GET['edit_id'])) ? 'block' : 'none' ?>;
        background: #fff;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        margin-bottom: 30px;
        border: 1px solid #eee;
    }

    .status-message {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        min-width: 300px;
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

    .text-preview {
        max-width: 350px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
</style>

<div id="content">
    <div class="container-fluid">
        <!-- Thông báo trạng thái (Flash Messages - Thông báo nhanh rứa mô) -->
        <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
            <div class="alert alert-success status-message shadow-lg border-0 fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Quản lý bài đăng tin tức</h2>
            <button id="toggleFormBtn" class="btn btn-pet shadow-sm">
                <i class="fa-solid <?= isset($_GET['edit_id']) ? 'fa-xmark' : 'fa-plus-circle' ?> me-2"></i> 
                <?= isset($_GET['edit_id']) ? 'Hủy chỉnh sửa' : 'Soạn tin mới' ?>
            </button>
        </div>

        <!-- FORM THÊM/SỬA TIN TỨC -->
        <div id="formContainer" class="form-container">
            <h5 class="fw-bold mb-4 text-secondary">
                <i class="fa-solid <?= isset($edit_data) ? 'fa-pen-to-square' : 'fa-feather-pointed' ?> me-2 text-success"></i> 
                <?= isset($edit_data) ? 'Cập nhật nội dung tin tức' : 'Soạn thảo nội dung mới ' ?>
            </h5>
            <form method="POST" class="row g-3">
                <input type="hidden" name="news_id" value="<?= $edit_data['id'] ?? 0 ?>">
                
                <div class="col-md-8">
                    <label class="form-label fw-bold">Tiêu đề tin tức</label>
                    <input type="text" name="tieu_de" class="form-control rounded-3" 
                           placeholder="Nhập tiêu đề..." required 
                           value="<?= htmlspecialchars($edit_data['tieu_de'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ngày đăng</label>
                    <input type="date" name="ngay_dang" class="form-control rounded-3" 
                           value="<?= $edit_data['ngay_dang'] ?? date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Nội dung chi tiết</label>
                    <textarea name="noi_dung" class="form-control rounded-3" rows="5" 
                              placeholder="Nhập nội dung bài đăng..." required><?= htmlspecialchars($edit_data['noi_dung'] ?? '') ?></textarea>
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="button" onclick="window.location.href='manage_news.php'" class="btn btn-light rounded-pill me-2 px-4 border">Đóng</button>
                    <button type="submit" name="save_news" class="btn btn-pet px-5 rounded-pill shadow-sm">
                        <?= isset($edit_data) ? 'Lưu cập nhật' : 'Đăng bài ngay ' ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- THANH CÔNG CỤ LỌC (Filtering Tools) -->
        <div class="filter-wrapper">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Từ khóa tìm kiếm</label>
                    <input type="text" name="search" class="form-control rounded-3 shadow-none" placeholder="Tìm theo tiêu đề hoặc nội dung..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Lọc theo ngày đăng</label>
                    <input type="date" name="filter_date" class="form-control rounded-3 shadow-none" value="<?= htmlspecialchars($filter_date) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-pet w-100 shadow-sm rounded-3">
                        <i class="fa-solid fa-filter me-2"></i> Lọc tin
                    </button>
                </div>
            </form>
        </div>

        <!-- BẢNG DANH SÁCH TIN TỨC -->
        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">ID</th>
                                <th style="width: 25%;">Tiêu đề</th>
                                <th style="width: 40%;">Nội dung lược trích</th>
                                <th>Ngày đăng</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($news->num_rows > 0):
                                while ($row = $news->fetch_assoc()):
                                    ?>
                            <tr class="<?= (isset($e_id) && $e_id == $row['id']) ? 'table-info' : '' ?>">
                                <td class="ps-4 text-secondary fw-bold">#<?= $row['id'] ?></td>
                                <td><div class="fw-bold text-dark"><?= htmlspecialchars($row['tieu_de']) ?></div></td>
                                <td class="small text-muted">
                                    <div class="text-preview">
                                        <?= htmlspecialchars($row['noi_dung']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill px-3">
                                        <i class="fa-regular fa-calendar-check me-1 text-success"></i>
                                        <?= date('d/m/Y', strtotime($row['ngay_dang'])) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm rounded-pill overflow-hidden bg-white border">
                                        <a href="?edit_id=<?= $row['id'] ?>&page=<?= $page ?>&search=<?= urlencode($search) ?>&filter_date=<?= $filter_date ?>" class="btn btn-sm btn-outline-primary border-0 py-2 px-3" title="Chỉnh sửa">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <!-- 
                                            TRIỆU HỒI MODAL XÁC NHẬN RỨA MÔ! 
                                            Thuật ngữ: Modal Invocation (Triệu hồi hộp thoại rứa)
                                        -->
                                        <a href="javascript:void(0)" 
                                           onclick="showConfirmModal('Bạn có chắc chắn muốn Xóa bài báo <?= addslashes($row['tieu_de']) ?> này không ?', '?delete=<?= $row['id'] ?>', 'danger')" 
                                           class="btn btn-sm btn-outline-danger border-0 py-2 px-3" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-secondary">Không tìm thấy tin tức nào phù hợp rứa mô!</td></tr>
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
                    <li class="page-item"><a class="page-link shadow-sm" href="?<?= http_build_query($query_params) ?>"><i class="fa-solid fa-chevron-left"></i></a></li>
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
                    <li class="page-item"><a class="page-link shadow-sm" href="?<?= http_build_query($query_params) ?>"><i class="fa-solid fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- NẠP PHÁP BẢO MODAL DÙNG CHUNG RỨA MÔ -->
<?php include('../includes/confirm_modal.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Tự động ẩn thông báo sau 3 giây (Auto-dismiss Alert - Tự động ẩn thông báo rứa)
    const statusMsg = document.querySelector('.status-message');
    if (statusMsg) {
        setTimeout(() => {
            statusMsg.classList.remove('show');
            setTimeout(() => statusMsg.remove(), 500);
        }, 3000);
    }

    const toggleFormBtn = document.getElementById("toggleFormBtn");
    const formContainer = document.getElementById("formContainer");

    function toggleForm() {
        if (formContainer.style.display === "none" || formContainer.style.display === "") {
            formContainer.style.display = "block";
            toggleFormBtn.innerHTML = '<i class="fa-solid fa-minus-circle me-2"></i> Thu gọn';
            formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            if (window.location.search.includes('edit_id')) {
                window.location.href = 'manage_news.php';
            } else {
                formContainer.style.display = "none";
                toggleFormBtn.innerHTML = '<i class="fa-solid fa-plus-circle me-2"></i> Soạn tin mới';
            }
        }
    }

    toggleFormBtn.addEventListener("click", toggleForm);
</script>
</body>
</html>