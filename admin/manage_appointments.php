<?php
include('../includes/db_connect.php');

// 1. Tầng xử lý logic - Xóa lịch hẹn (Delete Logic)
// Thuật ngữ: Debugging (Gỡ lỗi) - Tìm và sửa sai sót trong mã nguồn.
if (isset($_GET['delete_id'])) {
    $id_to_delete = intval($_GET['delete_id']);
    $conn->query("DELETE FROM lichhen WHERE id = $id_to_delete");
    header("Location: manage_appointments.php?status=success&message=Đã xóa lịch hẹn thành công rứa mô!");
    exit();
}

// Kiểm tra quyền hạn (Role-based Access Control)
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
$isAdmin = ($vai_tro == 'Admin');

if (!$isAdmin) {
    include "../includes/admin_header.php";
    echo "<div id='content' class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm border-0 rounded-4'>
                <h1 class='display-4'><i class='fa-solid fa-user-lock'></i></h1>
                <h3>Thông báo: Huynh không có lệnh bài để vào cấm địa này rứa mô!</h3>
                <p>Vui lòng quay lại Dashboard hoặc trang chủ.</p>
                <a href='index.php' class='btn btn-outline-danger mt-3 rounded-pill px-4'>Quay lại Dashboard</a>
            </div>
          </div>";
    exit();
}

// Cập nhật trạng thái lịch hẹn (Status Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $stmt = $conn->prepare("UPDATE lichhen SET trang_thai=? WHERE id=?");
    $stmt->bind_param("si", $_POST['action'], $_POST['id']);
    $stmt->execute();
    header("Location: manage_appointments.php?status=success&message=Cập nhật trạng thái thành công!");
    exit();
}

// Logic Tìm kiếm & Phân loại (Search & Filtering Logic)
$search = trim($_GET['search'] ?? "");
$filter = $_GET['filter'] ?? "all";

$search_query = "";
if ($search) {
    $search_query = " AND (ten_khach_hang LIKE '%$search%' OR ten_thu_cung LIKE '%$search%' OR ten_dich_vu LIKE '%$search%')";
}

$is_all = ($filter == 'all');
$show_processing = ($is_all || in_array($filter, ['Chờ xác nhận', 'Đã xác nhận']));
$show_history = ($is_all || in_array($filter, ['Đã xong', 'Đã hủy']));

// Truy vấn danh sách lịch hẹn đang xử lý
$sql1 = "SELECT * FROM lichhen WHERE trang_thai IN ('Chờ xác nhận', 'Đã xác nhận')";
if ($filter != 'all' && $show_processing) {
    $sql1 = "SELECT * FROM lichhen WHERE trang_thai = '$filter'";
}
$sql1 .= $search_query . " ORDER BY ngay_hen DESC, gio_hen DESC";
$res1 = $conn->query($sql1);

// Truy vấn danh sách lịch hẹn lịch sử
$sql2 = "SELECT * FROM lichhen WHERE trang_thai IN ('Đã xong', 'Đã hủy')";
if ($filter != 'all' && $show_history) {
    $sql2 = "SELECT * FROM lichhen WHERE trang_thai = '$filter'";
}
$sql2 .= $search_query . " ORDER BY ngay_hen DESC, gio_hen DESC";
$res2 = $conn->query($sql2);

include "../includes/admin_header.php";
?>
    <title>PetHealing - Quản lý lịch hẹn</title>

<style>
    :root {
        --pet-green: #2EB292;
        --pet-hover: #248f76;
        --bg-light: #f4f7f6;
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
        margin-bottom: 25px;
    }

    .section-title::after {
        content: '';
        position: absolute; left: 0; bottom: 0;
        width: 50px; height: 4px;
        background-color: var(--pet-green);
        border-radius: 2px;
    }

    .filter-wrapper {
        background: white; padding: 12px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .nav-pills .nav-link {
        color: #666; font-weight: 600;
        border-radius: 10px; padding: 10px 20px;
        transition: 0.3s;
    }

    .nav-pills .nav-link.active {
        background-color: var(--pet-green);
        color: white;
        box-shadow: 0 4px 10px rgba(46, 178, 146, 0.3);
    }

    .card-custom {
        border: none; border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        background: #fff; overflow: hidden;
    }

    .status-dropdown {
        border-radius: 20px; padding: 5px 12px;
        font-size: 0.85rem; font-weight: 600;
        border: 1px solid #dee2e6; cursor: pointer;
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
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="section-title mb-0">Quản lý lịch hẹn thú cưng</h2>
            </div>
            <div class="col-md-6">
                <form method="get" class="input-group shadow-sm rounded-pill overflow-hidden bg-white border">
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                    <input type="text" name="search" class="form-control border-0 py-2 ps-4" 
                           placeholder="Tìm tên khách, thú cưng hoặc dịch vụ..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn px-4" type="submit" style="color: var(--pet-green);">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Phân loại -->
        <div class="filter-wrapper">
            <ul class="nav nav-pills nav-fill">
                <?php
                $types = ['all' => 'Tất cả', 'Chờ xác nhận' => 'Chờ xác nhận', 'Đã xác nhận' => 'Đã xác nhận', 'Đã xong' => 'Đã xong', 'Đã hủy' => 'Đã hủy'];
                foreach ($types as $key => $val):
                    ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($filter == $key) ? 'active' : '' ?>" href="?filter=<?= $key ?>&search=<?= urlencode($search) ?>">
                        <?= $val ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- BẢNG 1: ĐANG XỬ LÝ -->
        <?php if ($show_processing): ?>
        <div class="card card-custom mb-5">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 text-dark fw-bold"><i class="fa-solid fa-spinner text-warning me-2"></i> Lịch hẹn Đang xử lý</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Khách hàng</th>
                                <th>Thú cưng</th>
                                <th>Dịch vụ</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($res1->num_rows > 0): ?>
                                <?php while ($row = $res1->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold"><?= htmlspecialchars($row['ten_khach_hang']) ?></div>
                                        <div class="small text-muted"><?= $row['so_dien_thoai_khach_hang'] ?></div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['ten_thu_cung']) ?></span></td>
                                    <td class="text-primary"><?= htmlspecialchars($row['ten_dich_vu']) ?></td>
                                    <td>
                                        <div class="fw-bold small"><?= date('d/m/Y', strtotime($row['ngay_hen'])) ?></div>
                                        <div class="small text-secondary"><?= $row['gio_hen'] ?></div>
                                    </td>
                                    <td>
                                        <form method="post" class="m-0">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <select name="action" class="form-select form-select-sm status-dropdown" onchange="this.form.submit()">
                                                <?php foreach (['Chờ xác nhận', 'Đã xác nhận', 'Đã xong', 'Đã hủy'] as $opt): ?>
                                                    <option value="<?= $opt ?>" <?= $row['trang_thai'] == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <!-- Thuật ngữ: Dynamic Parameter (Tham số động) - Truyền ID thật của dòng vào hàm JS -->
                                        <a href="javascript:void(0)" onclick="showConfirmModal('Huynh có chắc muốn xóa lịch hẹn của khách <?= addslashes($row['ten_khach_hang']) ?> không rứa?', '?delete_id=<?= $row['id'] ?>', 'danger')" 
                                           class="btn btn-sm btn-outline-danger border-0"><i class="fa-solid fa-trash-can"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4 text-secondary small">Không có dữ liệu rứa mô.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- BẢNG 2: LỊCH SỬ -->
        <?php if ($show_history): ?>
        <div class="card card-custom border-0" style="background-color: rgba(0,0,0,0.02);">
            <div class="card-header bg-transparent py-3 border-0 text-center">
                <h5 class="mb-0 text-secondary fw-bold text-uppercase tracking-wider">Lịch sử (Đã xong & Đã hủy)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="bg-white">
                            <tr>
                                <th class="ps-4">Khách hàng</th>
                                <th>Thú cưng</th>
                                <th>Dịch vụ</th>
                                <th>Ngày hẹn</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($res2->num_rows > 0): ?>
                                <?php while ($row = $res2->fetch_assoc()): ?>
                                <tr class="opacity-75">
                                    <td class="ps-4"><?= htmlspecialchars($row['ten_khach_hang']) ?></td>
                                    <td><?= htmlspecialchars($row['ten_thu_cung']) ?></td>
                                    <td><?= htmlspecialchars($row['ten_dich_vu']) ?></td>
                                    <td><small><?= date('d/m/Y', strtotime($row['ngay_hen'])) ?></small></td>
                                    <td>
                                        <span class="badge rounded-pill <?= ($row['trang_thai'] == 'Đã xong') ? 'bg-success' : 'bg-secondary' ?>"><?= $row['trang_thai'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:void(0)" class="text-danger small" 
                                           onclick="showConfirmModal('Xóa vĩnh viễn lịch hẹn này khỏi sử sách rứa mô?', '?delete_id=<?= $row['id'] ?>', 'danger')">
                                            <i class="fa-solid fa-xmark"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted small">Chưa có lịch sử phù hợp rứa mô.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>


<?php include "../includes/confirm_modal.php"; ?>

<script>
    // Tự động ẩn thông báo sau 3 giây
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