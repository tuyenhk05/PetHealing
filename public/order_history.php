<?php
include('../includes/db_connect.php');

// 1. Kiểm tra danh tính rứa mô (Authentication - Xác thực người dùng rứa)
$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null;
if (!$user_id) {
    header("Location: ./login.php");
    exit();
}

/**
 * 2. Tầng xử lý Xóa/Hủy đơn hàng
 * Thuật ngữ: Request Handling (Xử lý yêu cầu rứa mô)
 */
if (isset($_GET['delete_order_id'])) {
    $order_id_to_delete = intval($_GET['delete_order_id']);
    mysqli_begin_transaction($conn);
    try {
        $conn->query("DELETE FROM lichsumuahangchitiet WHERE order_id = $order_id_to_delete");
        $conn->query("DELETE FROM lichsumuahang WHERE order_id = $order_id_to_delete AND user_id = $user_id");
        mysqli_commit($conn);
        header("Location: order_history.php?status=success&msg=" . urlencode("Đã xóa đơn hàng"));
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: order_history.php?status=error&msg=" . urlencode("Lỗi"));
        exit();
    }
}

// 3. Xử lý bộ lọc Tinh gọn (Simplified Filter Logic rứa)
// Thuật ngữ: Query Filtering (Lọc truy vấn rứa mô)
$f_range = isset($_GET['f_range']) ? $_GET['f_range'] : 'all';
$f_status = isset($_GET['f_status']) ? $_GET['f_status'] : 'all';

$where_clause = "user_id = ?";
$params = [$user_id];
$types = "i";

// Lọc theo mốc ngày (1, 10, 30 ngày rứa)
if ($f_range !== 'all') {
    $days = intval($f_range);
    $where_clause .= " AND order_time >= DATE_SUB(NOW(), INTERVAL ? DAY)";
    $params[] = $days;
    $types .= "i";
}

// Lọc theo trạng thái vận tiêu mô
if ($f_status !== 'all') {
    $where_clause .= " AND status = ?";
    $params[] = intval($f_status);
    $types .= "i";
}

// 4. Triệu hồi danh sách từ Database (Prepared Statement rứa mô)
$sql = "SELECT * FROM lichsumuahang WHERE $where_clause ORDER BY order_time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$orders = mysqli_stmt_get_result($stmt);

include('../includes/header.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>PetHealing - Lịch sử mua hàng</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/order_history.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Tinh chỉnh bộ lọc cho gọn gàng rứa mô */
        .filter-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 35px;
            border: 1px solid #e0f2f1;
            box-shadow: 0 5px 20px rgba(32, 178, 170, 0.05);
        }
        .filter-box select {
            border-radius: 12px;
            border: 1px solid #d1d5db;
            padding: 10px 15px;
            font-size: 0.95rem;
            transition: 0.3s;
        }
        .filter-box select:focus {
            border-color: #20b2aa;
            box-shadow: 0 0 0 3px rgba(32, 178, 170, 0.1);
        }
        .btn-filter {
            background-color: #20b2aa;
            color: white;
            border-radius: 12px;
            padding: 10px 30px;
            border: none;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-filter:hover { background-color: #1a8e88; transform: translateY(-2px); }
    </style>
</head>
<body>

<main class="history-container">
    <h1>Lịch sử mua hàng </h1>

    <!-- 5. BỘ LỌC TINH GỌN (Simplified Advanced Filter rứa mô) -->
    <section class="filter-box">
        <form method="GET" class="row g-3 align-items-end justify-content-center">
            <!-- Ô 1: Chọn thời gian -->
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted"><i class="fa-regular fa-clock me-1"></i> Mốc thời gian</label>
                <select name="f_range" class="form-select shadow-none">
                    <option value="all" <?= $f_range === 'all' ? 'selected' : '' ?>>Tất cả thời gian</option>
                    <option value="1" <?= $f_range === '1' ? 'selected' : '' ?>>Gần nhất 1 ngày trước</option>
                    <option value="10" <?= $f_range === '10' ? 'selected' : '' ?>>Gần nhất 10 ngày trước</option>
                    <option value="30" <?= $f_range === '30' ? 'selected' : '' ?>>Gần nhất 30 ngày </option>
                </select>
            </div>
            
            <!-- Ô 2: Chọn trạng thái -->
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted"><i class="fa-solid fa-signal me-1"></i> Tình trạng đơn</label>
                <select name="f_status" class="form-select shadow-none">
                    <option value="all" <?= $f_status === 'all' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                    <option value="0" <?= $f_status === '0' ? 'selected' : '' ?>>Đang chờ xác nhận </option>
                    <option value="1" <?= $f_status === '1' ? 'selected' : '' ?>>Đã gửi hàng </option>
                    <option value="2" <?= $f_status === '2' ? 'selected' : '' ?>>Đã giao thành công </option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn-filter w-100 shadow-sm">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                </button>
            </div>
        </form>
    </section>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 text-center">
            <i class="fa-solid fa-bell-concierge me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($orders) > 0): ?>
        <?php while ($order = mysqli_fetch_assoc($orders)):
            $status_val = (int) $order['status'];
            $status_text = "Chờ xác nhận";
            $status_class = "status-0";

            if ($status_val === 1) {
                $status_text = "Đã gửi hàng";
                $status_class = "status-1";
            } elseif ($status_val === 2) {
                $status_text = "Đã giao thành công ";
                $status_class = "status-2";
            }
            ?>
            <section class="order-block">
                <div class="order-header">
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark"><i class="fa-solid fa-receipt me-1"></i> #<?php echo $order['order_code']; ?></span>
                        <span class="small text-muted"><?php echo date('H:i - d/m/Y', strtotime($order['order_time'])); ?></span>
                    </div>
                    <span class="status-badge <?php echo $status_class; ?>">
                        <i class="fa-solid <?php echo ($status_val == 0 ? 'fa-hourglass-half' : ($status_val == 1 ? 'fa-truck-fast' : 'fa-circle-check')); ?> me-1"></i>
                        <?php echo $status_text; ?>
                    </span>
                </div>
                
                <div class="order-body">
                    <div class="order-info shadow-sm">
                        <div><b>Người nhận:</b> <?php echo htmlspecialchars($order['receiver_name']); ?></div>
                        <div><b>Điện thoại:</b> <?php echo htmlspecialchars($order['receiver_phone']); ?></div>
                        <div class="grid-span-2"><b>Địa chỉ:</b> <?php echo htmlspecialchars($order['receiver_address']); ?></div>
                    </div>

                    <div class="order-products">
                        <p class="small fw-bold text-muted text-uppercase mb-2"><i class="fa-solid fa-box-open me-1"></i> Danh sách sản phẩm :</p>
                        <ul>
                            <?php
                            $detail = mysqli_query($conn, "SELECT * FROM lichsumuahangchitiet WHERE order_id=" . (int) $order['order_id']);
                            while ($prod = mysqli_fetch_assoc($detail)): ?>
                                <li>
                                    <span class="prod-name"><?php echo htmlspecialchars($prod['product_name']); ?> <small class="text-muted">x<?php echo $prod['quantity']; ?></small></span>
                                    <span class="prod-price"><?php echo number_format($prod['unit_price'], 0, '', '.'); ?> <small>VND</small></span>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                        <div class="text-end mt-3">
                            <span class="h5 fw-bold" style="color: var(--pet-teal);">Tổng cộng: <?php echo number_format($order['total'], 0, '', '.'); ?> VND</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="small text-muted italic">Thanh toán: <?php echo $order['payment_method'] == 'cod' ? 'Tiền mặt rứa' : 'Chuyển khoản mô'; ?></span>
                        
                        <?php if ($status_val == 0): ?>
                            <a href="javascript:void(0)" class="btn-delete-order" 
                               onclick="showConfirmModal('Bạn có thực sự muốn hủy đơn này không ?', 'order_history.php?delete_order_id=<?php echo $order['order_id']; ?>', 'danger')">
                                <i class="fa-solid fa-trash-can me-1"></i> Hủy đơn hàng 
                            </a>
                        <?php elseif ($status_val == 2): ?>
                            <a href="javascript:void(0)" class="btn-delete-order" style="color: #999; border-color: #eee;"
                               onclick="showConfirmModal('Xóa lịch sử đơn hàng này?', 'order_history.php?delete_order_id=<?php echo $order['order_id']; ?>', 'danger')">
                                <i class="fa-solid fa-circle-xmark me-1"></i> Xóa lịch sử 
                            </a>
                        <?php else: ?>
                            <span class="small text-muted fw-bold italic" style="color: #3498db;"><i class="fa-solid fa-truck-moving me-1"></i> Đang giao, không thể hủy !</span>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center py-5 opacity-50">
            <i class="fa-solid fa-magnifying-glass mb-3" style="font-size: 80px;"></i>
            <h3>Không tìm thấy đơn hàng nào phù hợp rứa mô!</h3>
            <a href="order_history.php" class="btn btn-sm btn-outline-secondary rounded-pill mt-2">Xem toàn bộ lịch sách </a>
        </div>
    <?php endif; ?>
</main>

<!-- NẠP PHÁP BẢO MODAL XÁC NHẬN -->
<?php include('../includes/confirm_modal.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include('../includes/footer.php'); ?>
</body>
</html>