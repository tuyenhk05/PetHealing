<?php
include('../includes/db_connect.php');

// Kiểm tra quyền hạn (Role-based Access Control - Kiểm soát truy cập dựa trên vai trò)
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
$isAdmin = ($vai_tro == 'Admin');

if (!$isAdmin) {
    include "../includes/admin_header.php";
    echo "<div id='content' class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm border-0 rounded-4'>
                <h1 class='display-4'><i class='fa-solid fa-user-lock'></i></h1>
                <h3>Thông báo: Bạn không có quyền truy cập!</h3>
                <p>Khu vực này chỉ dành cho tài khoản Quản trị viên.</p>
                <a href='index.php' class='btn btn-outline-danger mt-3 rounded-pill'>Quay lại Trang chủ</a>
            </div>
          </div>";
    exit();
}

// 1. Xử lý lấy thông tin để sửa bác sĩ (Edit Fetch - Truy xuất dữ liệu chỉnh sửa)
$editingDoctor = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $edit_stmt = $conn->prepare("SELECT * FROM BacSi WHERE id = ?");
    $edit_stmt->bind_param("i", $edit_id);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();
    if ($edit_result->num_rows > 0) {
        $editingDoctor = $edit_result->fetch_assoc();
    }
    $edit_stmt->close();
}

// 2. Xử lý Cập nhật hoặc Thêm mới (Create / Update - Thêm mới hoặc Cập nhật)
if (isset($_POST['save_doctor'])) {
    $ho_ten = $_POST['ho_ten'];
    $chuyen_mon = $_POST['chuyen_mon'];
    $kinh_nghiem = (int) $_POST['kinh_nghiem'];
    $doctor_id = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;
    $image_new_name = $_POST['existing_image'] ?? "";

    // Xử lý tải lên hình ảnh (Image Upload - Tải lên hình ảnh)
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_error = $_FILES['image']['error'];

        if ($image_error === 0) {
            $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $image_new_name = uniqid('DR_', true) . '.' . $ext;
                move_uploaded_file($image_tmp_name, "../assets/image/" . $image_new_name);
            }
        }
    }

    if ($doctor_id > 0) {
        // Cập nhật thông tin bác sĩ
        $stmt = $conn->prepare("UPDATE BacSi SET ho_ten=?, chuyen_mon=?, kinh_nghiem=?, image=? WHERE id=?");
        $stmt->bind_param("ssisi", $ho_ten, $chuyen_mon, $kinh_nghiem, $image_new_name, $doctor_id);
        $msg = "Cập nhật thông tin bác sĩ thành công .";
    } else {
        // Thêm bác sĩ mới
        $stmt = $conn->prepare("INSERT INTO BacSi (ho_ten, chuyen_mon, kinh_nghiem, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $ho_ten, $chuyen_mon, $kinh_nghiem, $image_new_name);
        $msg = "Thêm bác sĩ thành công .";
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: manage_doctors.php?status=success&message=$msg");
        exit();
    }
}

// 3. Xử lý Xóa bác sĩ (Delete Logic - Logic xóa dữ liệu)
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM BacSi WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_doctors.php?status=success&message=Đã xóa bác sĩ khỏi danh sách thành công!");
    exit();
}

// 4. Logic Phân trang (Pagination - Phân trang rứa)
$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1)
    $page = 1;
$offset = ($page - 1) * $limit;

$total_res = $conn->query("SELECT COUNT(*) as total FROM BacSi");
$total_doctors = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_doctors / $limit);

$doctors = $conn->query("SELECT * FROM BacSi ORDER BY id DESC LIMIT $limit OFFSET $offset");

include "../includes/admin_header.php";
?>
    <title>PetHealing - Quản lý thông tin bác sĩ</title>

<style>
    /* CSS CHUẨN HÓA HỆ THỐNG QUẢN TRỊ */
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
        box-shadow: 0 4px 12px rgba(46, 178, 146, 0.2);
    }

    .card-custom {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        background: white;
        overflow: hidden;
    }

    .form-container {
        display: <?= (isset($_GET['edit']) || isset($_GET['add_mode'])) ? 'block' : 'none' ?>;
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

    .table thead {
        background-color: #f8f9fa;
    }

    .doc-img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--pet-green);
        padding: 2px;
    }

    .specialty-badge {
        background: #e0fdf5;
        color: var(--pet-green);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin: 2px;
        display: inline-block;
    }

    /* Pagination Styling */
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
</style>

<div id="content">
    <div class="container-fluid">
        <!-- Thông báo trạng thái (Flash Messages) -->
        <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
            <div class="alert alert-<?= ($_GET['status'] == 'success') ? 'success' : 'danger' ?> status-message shadow-lg border-0 fade show" role="alert">
                <i class="fa-solid <?= ($_GET['status'] == 'success') ? 'fa-circle-check' : 'fa-circle-exclamation' ?> me-2"></i>
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Tiêu đề và Nút thao tác -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Quản lý danh sách bác sĩ</h2>
            <button id="toggleFormBtn" class="btn btn-pet">
                <i class="fa-solid <?= isset($_GET['edit']) ? 'fa-xmark' : 'fa-plus-circle' ?> me-2"></i> 
                <?= isset($_GET['edit']) ? 'Hủy bỏ' : 'Thêm bác sĩ' ?>
            </button>
        </div>

        <!-- FORM THÊM/SỬA BÁC SĨ -->
        <div id="formContainer" class="form-container">
            <h5 class="fw-bold mb-4 text-secondary">
                <i class="fa-solid <?= $editingDoctor ? 'fa-user-pen' : 'fa-user-plus' ?> me-2 text-success"></i> 
                <?= $editingDoctor ? 'Cập nhật thông tin bác sĩ' : 'Nhập thông tin bác sĩ mới rứa mô' ?>
            </h5>
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <input type="hidden" name="doctor_id" value="<?= $editingDoctor['id'] ?? 0 ?>">
                <input type="hidden" name="existing_image" value="<?= $editingDoctor['image'] ?? '' ?>">
                
                <div class="col-md-6">
                    <label class="form-label fw-bold">Họ và tên</label>
                    <input type="text" name="ho_ten" class="form-control rounded-3" 
                           placeholder="Nhập họ và tên bác sĩ..." required 
                           value="<?= htmlspecialchars($editingDoctor['ho_ten'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Kinh nghiệm (Năm)</label>
                    <input type="number" name="kinh_nghiem" class="form-control rounded-3" 
                           min="0" placeholder="Số năm kinh nghiệm..." required
                           value="<?= $editingDoctor['kinh_nghiem'] ?? '' ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Chuyên môn (Phân cách bằng dấu phẩy)</label>
                    <textarea name="chuyen_mon" class="form-control rounded-3" rows="2" 
                              placeholder="Ví dụ: Phẫu thuật, Tiêm chủng, Tư vấn dinh dưỡng..." required><?= htmlspecialchars($editingDoctor['chuyen_mon'] ?? '') ?></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Hình ảnh đại diện</label>
                    <input type="file" name="image" class="form-control rounded-3" accept="image/*">
                    <?php if (isset($editingDoctor['image']) && $editingDoctor['image']): ?>
                        <div class="mt-2 small text-muted">Bảo bối ảnh hiện tại: <?= $editingDoctor['image'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="button" onclick="window.location.href='manage_doctors.php'" class="btn btn-light rounded-pill me-2 px-4 border">Đóng</button>
                    <button type="submit" name="save_doctor" class="btn btn-pet px-5 rounded-pill shadow-sm">
                        <?= $editingDoctor ? 'Cập nhật' : 'Thêm mới' ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- BẢNG DANH SÁCH BÁC SĨ -->
        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">ID</th>
                                <th>Ảnh đại diện</th>
                                <th>Họ và tên</th>
                                <th style="width: 35%;">Chuyên môn</th>
                                <th>Kinh nghiệm</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($doctors->num_rows > 0):
                                while ($row = $doctors->fetch_assoc()):
                                    $imagePath = $row['image'] ? "../assets/image/{$row['image']}" : "../assets/image/{$row['ho_ten']}.jpg";
                                    ?>
                            <tr class="<?= (isset($edit_id) && $edit_id == $row['id']) ? 'table-info' : '' ?>">
                                <td class="ps-4 text-secondary fw-bold">#<?= $row['id'] ?></td>
                                <td>
                                    <img src="<?= $imagePath ?>" class="doc-img shadow-sm" alt="Bác sĩ" onerror="this.src='../assets/image/default_doctor.jpg';">
                                </td>
                                <td><div class="fw-bold text-dark"><?= htmlspecialchars($row['ho_ten']) ?></div></td>
                                <td>
                                    <?php
                                    $specialties = explode(",", $row['chuyen_mon']);
                                    foreach ($specialties as $sm):
                                        if (trim($sm)):
                                            ?>
                                        <span class="specialty-badge shadow-sm"><?= htmlspecialchars(trim($sm)) ?></span>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill px-3">
                                        <i class="fa-solid fa-award text-warning me-1"></i>
                                        <?= htmlspecialchars($row['kinh_nghiem']) ?> năm
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm rounded-pill overflow-hidden bg-white border">
                                        <a href="?edit=<?= $row['id'] ?>&page=<?= $page ?>" class="btn btn-sm btn-outline-primary border-0 py-2 px-3" title="Chỉnh sửa">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <!-- 
                                            TRIỆU HỒI MODAL XÁC NHẬN RỨA MÔ! 
                                            Thuật ngữ: Modal Invocation (Triệu hồi hộp thoại rứa)
                                        -->
                                        <a href="javascript:void(0)" 
                                           onclick="showConfirmModal('Huynh có chắc chắn muốn tiễn biệt y sĩ <?= addslashes($row['ho_ten']) ?> khỏi danh sách không rứa mô?', '?delete=<?= $row['id'] ?>', 'danger')" 
                                           class="btn btn-sm btn-outline-danger border-0 py-2 px-3" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-secondary">Hiện tại chưa có y sĩ nào ẩn cư tại đây rứa mô!</td></tr>
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
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link shadow-sm" href="?page=<?= $page - 1 ?>"><i class="fa-solid fa-chevron-left"></i></a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link shadow-sm" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link shadow-sm" href="?page=<?= $page + 1 ?>"><i class="fa-solid fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- NẠP PHÁP BẢO MODAL (Phải nằm ở cuối trang rứa mô) -->
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
            if (window.location.search.includes('edit')) {
                window.location.href = 'manage_doctors.php';
            } else {
                formContainer.style.display = "none";
                toggleFormBtn.innerHTML = '<i class="fa-solid fa-plus-circle me-2"></i> Thêm bác sĩ';
            }
        }
    }

    toggleFormBtn.addEventListener("click", toggleForm);
</script>
</body>
</html>