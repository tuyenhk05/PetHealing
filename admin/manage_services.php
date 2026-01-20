<?php
include('../includes/db_connect.php');

// Kiểm tra quyền hạn (Role-based Access Control - Kiểm soát truy cập dựa trên vai trò)
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
$isAdmin = ($vai_tro == 'Admin');

if (!$isAdmin) {
    include "../includes/admin_header.php";
    echo "<div id='content' class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm border-0 rounded-4'>
                <h1 class='display-4'><i class='fa-solid fa-user-shield'></i></h1>
                <h3>Thông báo: Bạn không có quyền truy cập vào trang này!</h3>
                <p>Khu vực quản trị chỉ dành cho tài khoản có thẩm quyền.</p>
                <a href='index.php' class='btn btn-outline-danger mt-3 rounded-pill px-4'>Quay lại Dashboard</a>
            </div>
          </div>";
    exit();
}

// 1. Tầng xử lý logic - Thêm Dịch vụ (Create - Tạo mới)
if (isset($_POST['add'])) {
    $ten_dich_vu = $_POST['ten_dich_vu'];
    $loai = $_POST['loai'];
    $gia = $_POST['gia'];
    $mo_ta = $_POST['mo_ta'];

    $image_name = $_FILES['image']['name'] ?? '';
    $image_tmp_name = $_FILES['image']['tmp_name'] ?? '';
    $image_error = $_FILES['image']['error'] ?? 4;

    if ($image_error === 0) {
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $image_new_name = uniqid('DV_', true) . '.' . $image_extension;
        $image_upload_path = "../assets/image/" . $image_new_name;

        if (in_array($image_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                $stmt = $conn->prepare("INSERT INTO dichvu (ten_dich_vu, loai, gia, mo_ta, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $ten_dich_vu, $loai, $gia, $mo_ta, $image_new_name);
                $stmt->execute();
                $stmt->close();
                header("Location: manage_services.php?status=success&message=Thêm dịch vụ mới thành công.");
                exit();
            }
        }
    }
}

// 2. Tầng xử lý logic - Xóa (Delete - Xóa dữ liệu)
// Thuật ngữ: Request Parameter (Tham số yêu cầu) - Lấy ID từ URL để thực hiện lệnh xóa.
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM dichvu WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_services.php?status=success&message=Đã xóa dịch vụ thành công.");
    exit();
}

// 3. Logic Tìm kiếm & Lọc dữ liệu (Search & Filtering Logic)
$search = trim($_GET['search'] ?? "");
$filter_type = $_GET['type'] ?? "all";
$filter_price = $_GET['price_range'] ?? "all";

$where_clauses = ["1=1"];

if ($search) {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $where_clauses[] = "(ten_dich_vu LIKE '%$search_esc%' OR mo_ta LIKE '%$search_esc%')";
}
if ($filter_type != 'all') {
    $type_esc = mysqli_real_escape_string($conn, $filter_type);
    $where_clauses[] = "loai = '$type_esc'";
}
if ($filter_price != 'all') {
    if ($filter_price == 'low')
        $where_clauses[] = "gia < 200000";
    elseif ($filter_price == 'mid')
        $where_clauses[] = "gia BETWEEN 200000 AND 1000000";
    elseif ($filter_price == 'high')
        $where_clauses[] = "gia > 1000000";
}

$where_sql = implode(" AND ", $where_clauses);

// 4. Phân trang (Pagination - Chia trang dữ liệu)
$limit = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$total_res = $conn->query("SELECT COUNT(*) AS total FROM dichvu WHERE $where_sql");
$total_records = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$result = $conn->query("SELECT * FROM dichvu WHERE $where_sql ORDER BY id DESC LIMIT $limit OFFSET $offset");

// Lấy danh sách loại dịch vụ để làm dropdown lọc
$types_res = $conn->query("SELECT DISTINCT loai FROM dichvu WHERE loai != ''");

include "../includes/admin_header.php";
?>
    <title>PetHealing - Quản lý dịch vụ</title>

<style>
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

    .table img {
        border-radius: 10px;
        object-fit: cover;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .form-container {
        display: none;
        background: #fff;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        margin-bottom: 30px;
        border: 1px solid #eee;
    }

    .pagination .page-link {
        color: var(--pet-green);
        border: none;
        margin: 0 4px;
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

    .price-text {
        color: var(--pet-orange);
        font-weight: 700;
    }
</style>

<div id="content">
    <div class="container-fluid">
        <!-- Thông báo trạng thái -->
        <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
            <div class="alert alert-success status-message shadow-lg border-0 fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Quản lý dịch vụ chăm sóc</h2>
            <button id="toggleFormBtn" class="btn btn-pet shadow-sm">
                <i class="fa-solid fa-plus-circle me-2"></i> Thêm dịch vụ mới
            </button>
        </div>

        <!-- FORM THÊM DỊCH VỤ -->
        <div id="formContainer" class="form-container">
            <h5 class="fw-bold mb-4 text-secondary"><i class="fa-solid fa-pen-to-square me-2 text-success"></i> Nhập thông tin dịch vụ mới</h5>
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tên dịch vụ</label>
                    <input type="text" name="ten_dich_vu" class="form-control rounded-3" placeholder="Ví dụ: Khám tổng quát" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Phân loại</label>
                    <select name="loai" class="form-select rounded-3" required>
                        <option value="cham-soc">Chăm sóc</option>
                        <option value="kham-chua-benh">Khám chữa bệnh</option>
                        <option value="van-chuyen">Vận chuyển</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Giá dịch vụ (VND)</label>
                    <input type="number" name="gia" class="form-control rounded-3" placeholder="Đơn giá" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Mô tả chi tiết</label>
                    <textarea name="mo_ta" class="form-control rounded-3" rows="3" placeholder="Nội dung dịch vụ..." required></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Hình ảnh đại diện</label>
                    <input type="file" name="image" class="form-control rounded-3" accept="image/*" required>
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="button" onclick="toggleForm()" class="btn btn-light rounded-pill me-2 px-4 border">Hủy bỏ</button>
                    <button type="submit" name="add" class="btn btn-pet px-5 rounded-pill shadow-sm">Lưu thông tin</button>
                </div>
            </form>
        </div>

        <!-- THANH CÔNG CỤ LỌC (Filtering Tools) -->
        <div class="filter-wrapper">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Từ khóa tìm kiếm</label>
                    <input type="text" name="search" class="form-control rounded-3" placeholder="Tên dịch vụ..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Loại dịch vụ</label>
                    <select name="type" class="form-select rounded-3">
                        <option value="all">-- Tất cả loại --</option>
                        <?php while ($t = $types_res->fetch_assoc()): ?>
                            <option value="<?= $t['loai'] ?>" <?= ($filter_type == $t['loai']) ? 'selected' : '' ?>>
                                <?= ucfirst(htmlspecialchars($t['loai'])) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Khoảng giá</label>
                    <select name="price_range" class="form-select rounded-3">
                        <option value="all">-- Tất cả mức giá --</option>
                        <option value="low" <?= ($filter_price == 'low') ? 'selected' : '' ?>>Dưới 200.000₫</option>
                        <option value="mid" <?= ($filter_price == 'mid') ? 'selected' : '' ?>>200.000₫ - 1.000.000₫</option>
                        <option value="high" <?= ($filter_price == 'high') ? 'selected' : '' ?>>Trên 1.000.000₫</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-pet w-100 shadow-sm">
                        <i class="fa-solid fa-filter me-2"></i> Lọc
                    </button>
                </div>
            </form>
        </div>

        <!-- BẢNG DANH SÁCH DỊCH VỤ -->
        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">ID</th>
                                <th>Hình ảnh</th>
                                <th>Tên Dịch Vụ</th>
                                <th>Loại</th>
                                <th>Giá</th>
                                <th style="width: 30%;">Mô Tả</th>
                                <th class="text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()):
                                    $imagePath = $row['image'] ? "../assets/image/" . $row['image'] : "../assets/image/" . $row['ten_dich_vu'] . ".jpg";
                                    ?>
                                    <tr>
                                        <td class="ps-4 text-secondary fw-bold">#<?= $row['id'] ?></td>
                                        <td>
                                            <img src="<?= htmlspecialchars($imagePath) ?>" width="80" height="60" alt="Service" onerror="this.src='../assets/image/default_service.jpg';">
                                        </td>
                                        <td><div class="fw-bold text-dark"><?= htmlspecialchars($row['ten_dich_vu']) ?></div></td>
                                        <td>
                                            <?php
                                            $badgeClass = ($row['loai'] == 'cham-soc') ? 'bg-info' : (($row['loai'] == 'kham-chua-benh') ? 'bg-danger' : 'bg-warning text-dark');
                                            ?>
                                            <span class="badge <?= $badgeClass ?> rounded-pill px-3"><?= ucfirst($row['loai']) ?></span>
                                        </td>
                                        <td class="price-text"><?= number_format($row['gia'], 0, ',', '.') ?>₫</td>
                                        <td class="small text-muted">
                                            <div style="max-height: 45px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                                <?= htmlspecialchars($row['mo_ta']) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm rounded-pill overflow-hidden bg-white border">
                                                <a href="edit_services.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary border-0 py-2 px-3" title="Chỉnh sửa">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <!-- 
                                                    TRIỆU HỒI MODAL XÁC NHẬN - SỬ DỤNG TỪ NGỮ BÌNH THƯỜNG RỨA MÔ
                                                    Thuật ngữ: Modal Trigger (Bộ kích hoạt hộp thoại)
                                                -->
                                                <a href="javascript:void(0)" 
                                                   onclick="showConfirmModal('Bạn có chắc chắn muốn xóa dịch vụ <?= addslashes($row['ten_dich_vu']) ?> này không?', '?delete=<?= $row['id'] ?>', 'danger')" 
                                                   class="btn btn-sm btn-outline-danger border-0 py-2 px-3" title="Xóa">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center py-5 text-secondary">Không tìm thấy dịch vụ phù hợp với yêu cầu lọc.</td></tr>
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

<!-- NẠP PHÁP BẢO MODAL DÙNG CHUNG (Cần đặt ở cuối để JS nhận diện rứa mô) -->
<?php include('../includes/confirm_modal.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Tự động ẩn thông báo sau 3 giây (Auto-dismiss Alert - Tự động ẩn thông báo)
    const statusMsg = document.querySelector('.status-message');
    if (statusMsg) {
        setTimeout(() => {
            statusMsg.classList.remove('show');
            setTimeout(() => statusMsg.remove(), 500);
        }, 3000);
    }

    // Đóng mở Form thêm mới
    const toggleFormBtn = document.getElementById("toggleFormBtn");
    const formContainer = document.getElementById("formContainer");

    function toggleForm() {
        if (formContainer.style.display === "none" || formContainer.style.display === "") {
            formContainer.style.display = "block";
            toggleFormBtn.innerHTML = '<i class="fa-solid fa-minus-circle me-2"></i> Đóng Form';
            formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            formContainer.style.display = "none";
            toggleFormBtn.innerHTML = '<i class="fa-solid fa-plus-circle me-2"></i> Thêm dịch vụ mới';
        }
    }

    toggleFormBtn.addEventListener("click", toggleForm);
</script>
</body>
</html>