<?php

if (isset($_GET['ajax_get_order_details'])) {
    include('../includes/db_connect.php');

    $order_id = intval($_GET['ajax_get_order_details']);

    $sql_order = "SELECT * FROM lichsumuahang WHERE order_id = $order_id";
    $res_order = $conn->query($sql_order);
    $order_data = $res_order ? $res_order->fetch_assoc() : null;

    if ($order_data) {
        // Thỉnh danh sách bảo vật đi kèm rứa
        $sql_items = "SELECT * FROM lichsumuahangchitiet WHERE order_id = $order_id";
        $res_items = $conn->query($sql_items);
        $items = [];
        while ($item = $res_items->fetch_assoc()) {
            $items[] = $item;
        }

        // --- CHIÊU THỨC QUAN TRỌNG: TẨY TỦY JSON RỨA MÔ ---
        if (ob_get_length())
            ob_clean(); // Xóa sạch mọi ký tự thừa thải trước đó rứa
        header('Content-Type: application/json');
        echo json_encode(['order' => $order_data, 'items' => $items]);
        exit(); // Chấm dứt ngay, không cho HTML bên dưới lọt vào mô!
    }
    exit();
}

include('../includes/db_connect.php');

// 2. Kiểm tra lệnh bài Admin (Authentication)
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if ($vai_tro !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

// 3. Xử lý Xóa đơn hàng (Delete Logic)
if (isset($_GET['delete_id'])) {
    $id_to_delete = intval($_GET['delete_id']);
    mysqli_begin_transaction($conn);
    try {
        $conn->query("DELETE FROM lichsumuahangchitiet WHERE order_id = $id_to_delete");
        $conn->query("DELETE FROM lichsumuahang WHERE order_id = $id_to_delete");
        mysqli_commit($conn);
        header("Location: manage_order.php?status=success&message=Đã xóa đơn hàng này!");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: manage_order.php?status=error&message=Lỗi pháp thuật rồi!");
        exit();
    }
}

// 4. Cập nhật trạng thái vận tiêu (Status Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = intval($_POST['new_status']);
    $stmt = $conn->prepare("UPDATE lichsumuahang SET status = ? WHERE order_id = ?");
    $stmt->bind_param("ii", $new_status, $order_id);
    if ($stmt->execute()) {
        header("Location: manage_order.php?status=success&message=Đã cập nhật trạng thái");
        exit();
    }
}

// 5. Logic Tìm kiếm & Bộ lọc tinh gọn
$search = trim($_GET['search'] ?? "");
$f_range = $_GET['f_range'] ?? "all";
$f_status = $_GET['f_status'] ?? "all";

$where_clauses = ["1=1"];
$params = [];
$types = "";

if ($search) {
    $search_esc = "%$search%";
    $where_clauses[] = "(order_code LIKE ? OR receiver_name LIKE ? OR receiver_phone LIKE ?)";
    $params[] = $search_esc;
    $params[] = $search_esc;
    $params[] = $search_esc;
    $types .= "sss";
}
if ($f_range !== 'all') {
    $days = intval($f_range);
    $where_clauses[] = "order_time >= DATE_SUB(NOW(), INTERVAL ? DAY)";
    $params[] = $days;
    $types .= "i";
}
if ($f_status !== 'all') {
    $where_clauses[] = "status = ?";
    $params[] = intval($f_status);
    $types .= "i";
}

$where_sql = implode(" AND ", $where_clauses);
$sql = "SELECT * FROM lichsumuahang WHERE $where_sql ORDER BY order_time DESC";
$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

include "../includes/admin_header.php";
?>

<style>
    :root {
        --pet-green: #2EB292;
        --pet-orange: #f39c12;
        --pet-blue: #3498db;
    }
    
    #content { padding: 30px 40px; }
    .section-title { color: var(--pet-green); font-weight: 700; position: relative; padding-bottom: 10px; margin-bottom: 25px; }
    .section-title::after { content: ''; position: absolute; left: 0; bottom: 0; width: 50px; height: 4px; background-color: var(--pet-green); border-radius: 2px; }

    .filter-wrapper { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
    .card-custom { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: #fff; overflow: hidden; }

    .status-0 { color: var(--pet-orange); font-weight: 700; }
    .status-1 { color: var(--pet-blue); font-weight: 700; }
    .status-2 { color: var(--pet-green); font-weight: 700; }

    .status-select { border-radius: 20px; padding: 4px 12px; font-size: 0.85rem; border: 1px solid #ddd; }
    .status-message { position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 300px; }
    
    /* 6. CSS CHO BOX CHI TIẾT (Modal Styling rứa mô) */
    #detailBox .modal-content { border-radius: 25px; border: none; overflow: hidden; }
    .detail-header-bg { background: linear-gradient(135deg, #2EB292 0%, #1e7a64 100%); color: white; padding: 25px; }
    .detail-item-list { background: #f8fbf9; border-radius: 15px; padding: 15px; }
    .item-row { border-bottom: 1px dashed #ddd; padding: 10px 0; }
    .item-row:last-child { border-bottom: none; }
</style>
<title>PetHealing - Quản lý đơn hàng</title>
<div id="content">
    <div class="container-fluid">
        <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
            <div class="alert alert-success status-message shadow-lg border-0 fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="section-title mb-0">Quản lý Đơn hàng</h2>
            </div>
            <div class="col-md-6">
                <form method="get" class="input-group shadow-sm rounded-pill overflow-hidden bg-white border">
                    <input type="text" name="search" class="form-control border-0 py-2 ps-4" 
                           placeholder="Mã đơn, tên khách ..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn px-4" type="submit" style="color: var(--pet-green);"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
        </div>

        <!-- Filter Area -->
        <div class="filter-wrapper">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">Mốc thời gian</label>
                    <select name="f_range" class="form-select rounded-3">
                        <option value="all" <?= $f_range == 'all' ? 'selected' : '' ?>>Tất cả thời gian</option>
                        <option value="1" <?= $f_range == '1' ? 'selected' : '' ?>>Gần nhất 1 ngày</option>
                        <option value="30" <?= $f_range == '30' ? 'selected' : '' ?>>Gần nhất 30 ngày</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">Trạng thái</label>
                    <select name="f_status" class="form-select rounded-3">
                        <option value="all" <?= $f_status == 'all' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                        <option value="0" <?= $f_status == '0' ? 'selected' : '' ?>>Chờ xác nhận</option>
                        <option value="2" <?= $f_status == '2' ? 'selected' : '' ?>>Đã giao xong</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100 rounded-3 border-0" style="background-color: var(--pet-green);">Lọc đơn</button>
                </div>
                <div class="col-md-2">
                    <a href="manage_order.php" class="btn btn-outline-secondary w-100 rounded-3">Xóa lọc</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Mã đơn</th>
                                <th>Người nhận</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)):
                                    $s_val = (int) $row['status'];
                                    ?>
                                <tr>
                                    <td class="ps-4 fw-bold">#<?= $row['order_code'] ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['receiver_name']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($row['receiver_phone']) ?></div>
                                    </td>
                                    <td class="fw-bold text-success"><?= number_format($row['total'], 0, ',', '.') ?>₫</td>
                                    <td>
                                        <form method="POST" class="m-0">
                                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                            <select name="new_status" class="form-select form-select-sm status-select status-<?= $s_val ?>" onchange="this.form.submit()">
                                                <option value="0" <?= $s_val === 0 ? 'selected' : '' ?>>Chờ xác nhận</option>
                                                <option value="1" <?= $s_val === 1 ? 'selected' : '' ?>>Đã gửi</option>
                                                <option value="2" <?= $s_val === 2 ? 'selected' : '' ?>>Đã giao</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden bg-white border">
                                            <button type="button" class="btn btn-sm btn-outline-primary border-0 py-2" 
                                                    onclick="viewOrderDetails(<?= $row['order_id'] ?>)" title="Xem nhanh">
                                                <i class="fa-solid fa-bolt"></i>
                                            </button>
                                            <a href="javascript:void(0)" onclick="showConfirmModal('Xóa đơn này ?', 'manage_order.php?delete_id=<?= $row['order_id'] ?>', 'danger')" 
                                               class="btn btn-sm btn-outline-danger border-0 py-2"><i class="fa-solid fa-trash-can"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BOX CHI TIẾT ĐƠN HÀNG (Quick View Modal) -->
<div class="modal fade" id="detailBox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="detail-header-bg">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-receipt me-2"></i> Chi tiết đơn hàng</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <p class="mb-0 opacity-75 mt-1" id="modalOrderCode">Mã đơn: #...</p>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted small fw-bold text-uppercase mb-2">Người nhận :</h6>
                        <p class="mb-1 fw-bold" id="modalReceiverName">...</p>
                        <p class="mb-1 small" id="modalReceiverPhone">...</p>
                        <p class="mb-0 small text-muted" id="modalReceiverAddress">...</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-muted small fw-bold text-uppercase mb-2">Thời gian khởi tạo:</h6>
                        <p class="mb-1" id="modalOrderTime">...</p>
                        <p class="mb-0 fw-bold text-success" id="modalTotal">Tổng tiền: ...</p>
                    </div>
                </div>
                
                <h6 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-box-open me-2"></i> Danh sách sản phẩm :</h6>
                <div class="detail-item-list" id="modalItemList">
                    <!-- Dữ liệu bảo vật sẽ được JavaScript "hô biến" vào đây -->
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng </button>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/confirm_modal.php"; ?>

<script>
    /**
     * Tuyệt kỹ ViewOrderDetails (Thỉnh dữ liệu bằng AJAX)
     * Thuật ngữ: Fetch API - Pháp bảo truy vấn dữ liệu từ xa rứa mô.
     */
    async function viewOrderDetails(orderId) {
        const modalElement = document.getElementById('detailBox');
        const modal = new bootstrap.Modal(modalElement);
        const itemList = document.getElementById('modalItemList');
        
        itemList.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-success" role="status"></div><p class="mt-2 small text-muted">Đang thỉnh sớ đơn hàng rứa...</p></div>';
        modal.show();

        try {
            // Vận công thỉnh dữ liệu bằng Fetch API rứa mô
            const response = await fetch(`manage_order.php?ajax_get_order_details=${orderId}`);
            
            // Kiểm tra xem phản hồi có phải là JSON chuẩn không mô
            if (!response.ok) throw new Error('Mạng lưới gặp sự cố rứa!');
            
            const data = await response.json();

            // Đổ linh khí vào các ô thông tin rứa
            document.getElementById('modalOrderCode').innerText = `Mã đơn: #${data.order.order_code}`;
            document.getElementById('modalReceiverName').innerText = data.order.receiver_name;
            document.getElementById('modalReceiverPhone').innerText = `SĐT: ${data.order.receiver_phone}`;
            document.getElementById('modalReceiverAddress').innerText = `Địa chỉ: ${data.order.receiver_address}`;
            document.getElementById('modalOrderTime').innerText = data.order.order_time;
            document.getElementById('modalTotal').innerText = `Tổng cộng: ${new Intl.NumberFormat('vi-VN').format(data.order.total)}₫`;

            // Vẽ danh sách bảo vật rứa mô
            let html = '';
            data.items.forEach(item => {
                html += `
                    <div class="item-row d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-dark">${item.product_name}</span>
                            <br><small class="text-muted">Đơn giá: ${new Intl.NumberFormat('vi-VN').format(item.unit_price)}₫</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-white text-dark border px-2">x${item.quantity}</span>
                        </div>
                    </div>
                `;
            });
            itemList.innerHTML = html;

        } catch (error) {
            console.error('Lỗi thỉnh dữ liệu:', error);
            itemList.innerHTML = '<p class="text-danger text-center">Ôi thôi! Pháp thuật gặp lỗi (có thể do rò rỉ HTML rứa mô). Huynh hãy kiểm tra file db_connect.php xem có echo nhầm cái gì không rứa!</p>';
        }
    }

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