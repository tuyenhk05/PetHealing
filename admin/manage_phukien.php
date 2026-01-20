<?php
include('../includes/db_connect.php');

// Kiểm tra quyền hạn (Role-based Access Control)
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
$isAdmin = ($vai_tro == 'Admin');

if (!$isAdmin) {
    include "../includes/admin_header.php";
    echo "<div id='content' class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm border-0 rounded-4'>
                <h1 class='display-4'><i class='fa-solid fa-user-shield'></i></h1>
                <h3>Thông báo: Bạn không có quyền truy cập vào khu vực này!</h3>
                <p>Vui lòng quay lại Dashboard hoặc trang chủ.</p>
                <a href='index.php' class='btn btn-outline-danger mt-3 rounded-pill px-4'>Quay lại Dashboard</a>
            </div>
          </div>";
    exit();
}

// 1. Tầng xử lý logic - Thêm sản phẩm (Create)
if (isset($_POST['add'])) {
    $ten = $_POST['ten'];
    $danh_cho_loai = $_POST['danh_cho_loai'];
    $mo_ta = $_POST['mo_ta'];
    $gia = $_POST['gia'];
    $xuat_su = $_POST['xuat_su'];
    $chat_lieu = $_POST['chat_lieu'];
    $cong_dung = $_POST['cong_dung'];
    $phu_hop = $_POST['phu_hop_voi_tuoi_thu_cung'];
    $soluong = (int) $_POST['so_luong'];

    // Xử lý Image Upload
    $image_name = $_FILES['hinh']['name'] ?? '';
    $image_tmp_name = $_FILES['hinh']['tmp_name'] ?? '';
    $image_error = $_FILES['hinh']['error'] ?? 4;

    if ($image_error === 0) {
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($image_extension, $valid_extensions)) {
            $image_new_name = time() . "_" . basename($image_name);
            $image_upload_path = "../assets/image/" . $image_new_name;

            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                $stmt = $conn->prepare("INSERT INTO phukien (ten, danh_cho_loai, mo_ta, gia, xuat_su, chat_lieu, cong_dung, phu_hop_voi_tuoi_thu_cung, so_luong, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssis", $ten, $danh_cho_loai, $mo_ta, $gia, $xuat_su, $chat_lieu, $cong_dung, $phu_hop, $soluong, $image_new_name);
                $stmt->execute();
                $stmt->close();
                header("Location: manage_phukien.php?status=success&message=Đã thêm phụ kiện mới thành công.");
                exit();
            }
        }
    }
}

// 2. Tầng xử lý logic - Xóa sản phẩm (Delete Logic)
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM phukien WHERE id = $id");
    header("Location: manage_phukien.php?status=success&message=Đã xóa sản phẩm khỏi kho hàng.");
    exit();
}

// 3. Logic Tìm kiếm & Lọc dữ liệu (Search & Filtering)
$search = trim($_GET['search'] ?? "");
$filter_type = $_GET['type'] ?? "all";
$filter_origin = $_GET['origin'] ?? "all";
$filter_price = $_GET['price_range'] ?? "all";

$where_clauses = ["1=1"];

if ($search) {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $where_clauses[] = "(ten LIKE '%$search_esc%' OR mo_ta LIKE '%$search_esc%')";
}
if ($filter_type != 'all') {
    $type_esc = mysqli_real_escape_string($conn, $filter_type);
    $where_clauses[] = "danh_cho_loai = '$type_esc'";
}
if ($filter_origin != 'all') {
    $origin_esc = mysqli_real_escape_string($conn, $filter_origin);
    $where_clauses[] = "xuat_su = '$origin_esc'";
}
if ($filter_price != 'all') {
    if ($filter_price == 'low')
        $where_clauses[] = "gia < 100000";
    elseif ($filter_price == 'mid')
        $where_clauses[] = "gia BETWEEN 100000 AND 500000";
    elseif ($filter_price == 'high')
        $where_clauses[] = "gia > 500000";
}

$where_sql = implode(" AND ", $where_clauses);

// 4. Phân trang (Pagination)
$limit = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$total_res = $conn->query("SELECT COUNT(*) AS total FROM phukien WHERE $where_sql");
$total_records = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$result = $conn->query("SELECT * FROM phukien WHERE $where_sql ORDER BY id DESC LIMIT $limit OFFSET $offset");

$types_res = $conn->query("SELECT DISTINCT danh_cho_loai FROM phukien WHERE danh_cho_loai != ''");
$origins_res = $conn->query("SELECT DISTINCT xuat_su FROM phukien WHERE xuat_su != ''");

include "../includes/admin_header.php";
?>
    <title>PetHealing - Quản lý phụ kiện</title>

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
        position: absolute; left: 0; bottom: 0;
        width: 50px; height: 4px;
        background-color: var(--pet-green);
        border-radius: 2px;
    }

    .filter-wrapper {
        background: white; padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .btn-pet {
        background-color: var(--pet-green);
        color: white; border: none;
        border-radius: 10px; padding: 10px 20px;
        font-weight: 600; transition: 0.3s;
    }

    .btn-pet:hover {
        background-color: var(--pet-hover);
        color: white; transform: translateY(-2px);
    }

    .card-custom {
        border: none; border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        background: white; overflow: hidden;
    }

    .table img {
        border-radius: 8px; object-fit: cover;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .form-container {
        display: none; background: #fff;
        padding: 25px; border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        margin-bottom: 30px; border: 1px solid #eee;
    }

    .pagination .page-link {
        color: var(--pet-green); border: none;
        margin: 0 4px; border-radius: 8px;
        font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .pagination .page-item.active .page-link {
        background-color: var(--pet-green); color: white;
    }

    .status-message {
        position: fixed; top: 20px; right: 20px;
        z-index: 1050; min-width: 300px;
    }
</style>

<div id="content">
    <div class="container-fluid">
        <!-- Thông báo -->
        <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
            <div class="alert alert-success status-message shadow-lg border-0 fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Quản lý kho phụ kiện</h2>
            <button id="toggleFormBtn" class="btn btn-pet shadow-sm">
                <i class="fa-solid fa-plus-circle me-2"></i> Thêm sản phẩm mới
            </button>
        </div>

        <!-- FORM THÊM MỚI -->
        <div id="formContainer" class="form-container">
            <h5 class="fw-bold mb-4 text-secondary"><i class="fa-solid fa-pen-to-square me-2 text-success"></i> Nhập thông tin phụ kiện mới rứa mô</h5>
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tên sản phẩm</label>
                    <input type="text" name="ten" class="form-control rounded-3" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Dành cho loại</label>
                    <input type="text" name="danh_cho_loai" class="form-control rounded-3" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giá bán (VND)</label>
                    <input type="number" step="0.01" name="gia" class="form-control rounded-3" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Số lượng</label>
                    <input type="number" name="so_luong" class="form-control rounded-3" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Chất liệu</label>
                    <input type="text" name="chat_lieu" class="form-control rounded-3">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Xuất xứ</label>
                    <input type="text" name="xuat_su" class="form-control rounded-3">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Độ tuổi</label>
                    <input type="text" name="phu_hop_voi_tuoi_thu_cung" class="form-control rounded-3">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Mô tả sản phẩm</label>
                    <textarea name="mo_ta" class="form-control rounded-3" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Công dụng</label>
                    <textarea name="cong_dung" class="form-control rounded-3" rows="2"></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Hình ảnh minh họa</label>
                    <input type="file" name="hinh" class="form-control rounded-3" accept="image/*" required>
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="button" onclick="toggleForm()" class="btn btn-light rounded-pill me-2 px-4 border">Đóng</button>
                    <button type="submit" name="add" class="btn btn-pet px-5 rounded-pill shadow-sm">Lưu vào kho</button>
                </div>
            </form>
        </div>

        <!-- BỘ LỌC -->
        <div class="filter-wrapper">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control rounded-3 shadow-none" placeholder="Tên sản phẩm..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Loài</label>
                    <select name="type" class="form-select rounded-3 shadow-none">
                        <option value="all">Tất cả</option>
                        <?php while ($t = $types_res->fetch_assoc()): ?>
                            <option value="<?= $t['danh_cho_loai'] ?>" <?= ($filter_type == $t['danh_cho_loai']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['danh_cho_loai']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Xuất xứ</label>
                    <select name="origin" class="form-select rounded-3 shadow-none">
                        <option value="all">Tất cả</option>
                        <?php while ($o = $origins_res->fetch_assoc()): ?>
                            <option value="<?= $o['xuat_su'] ?>" <?= ($filter_origin == $o['xuat_su']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($o['xuat_su']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Giá bán</label>
                    <select name="price_range" class="form-select rounded-3 shadow-none">
                        <option value="all">Tất cả giá</option>
                        <option value="low" <?= ($filter_price == 'low') ? 'selected' : '' ?>>Dưới 100.000₫</option>
                        <option value="mid" <?= ($filter_price == 'mid') ? 'selected' : '' ?>>100.000₫ - 500.000₫</option>
                        <option value="high" <?= ($filter_price == 'high') ? 'selected' : '' ?>>Trên 500.000₫</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-pet w-100 shadow-sm rounded-3">
                        <i class="fa-solid fa-filter"></i> Lọc
                    </button>
                </div>
            </form>
        </div>

        <!-- BẢNG DỮ LIỆU -->
        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">ID</th>
                                <th>Ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Loài</th>
                                <th>Giá bán</th>
                                <th>Tồn kho</th>
                                <th>Xuất xứ</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()):
                                    $imagePath = (!empty($row['image'])) ? "../assets/image/" . $row['image'] : "../assets/image/default.jpg";
                                    ?>
                                    <tr>
                                        <td class="ps-4 text-secondary fw-bold">#<?= $row['id'] ?></td>
                                        <td><img src="<?= htmlspecialchars($imagePath) ?>" width="60" height="60" alt="Product" onerror="this.src='../assets/image/default.jpg';"></td>
                                        <td><div class="fw-bold text-dark"><?= htmlspecialchars($row['ten']) ?></div></td>
                                        <td><span class="badge bg-light text-primary border rounded-pill px-3"><?= htmlspecialchars($row['danh_cho_loai']) ?></span></td>
                                        <td class="fw-bold text-orange" style="color: var(--pet-orange);"><?= number_format($row['gia'], 0, ',', '.') ?>₫</td>
                                        <td><span class="fw-bold <?= ($row['so_luong'] < 10) ? 'text-danger' : 'text-dark' ?>"><?= (int) $row['so_luong'] ?></span></td>
                                        <td class="small text-secondary"><?= htmlspecialchars($row['xuat_su']) ?></td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm rounded-pill overflow-hidden bg-white border">
                                                <a href="edit_phukien.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary border-0 py-2 px-3" title="Chỉnh sửa"><i class="fa-solid fa-edit"></i></a>
                                                <!-- Thuật ngữ: Dynamic Confirmation (Xác nhận động) - Sử dụng JavaScript Modal thay cho confirm() -->
                                                <a href="javascript:void(0)" onclick="showConfirmModal('Huynh chắc chắn muốn tiễn biệt sản phẩm <?= addslashes($row['ten']) ?> khỏi kho hàng rứa mô?', '?delete=<?= $row['id'] ?>', 'danger')" 
                                                   class="btn btn-sm btn-outline-danger border-0 py-2 px-3" title="Xóa"><i class="fa-solid fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center py-5 text-secondary small">Không tìm thấy phụ kiện nào phù hợp rứa mô!</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PHÂN TRANG -->
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

<?php include "../includes/confirm_modal.php"; ?>

<script>
    // Tự động ẩn thông báo
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
            formContainer.style.display = "none";
            toggleFormBtn.innerHTML = '<i class="fa-solid fa-plus-circle me-2"></i> Thêm sản phẩm mới';
        }
    }
    toggleFormBtn.addEventListener("click", toggleForm);
</script>
</body>
</html>