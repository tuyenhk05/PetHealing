<?php
include "../includes/db_connect.php";
include "../includes/admin_header.php"; // Đã bao gồm sidebar và check quyền Admin

// --- 1. THỐNG KÊ TỔNG QUÁT (STATS) ---
$totalAppointments = $conn->query("SELECT COUNT(*) as count FROM lichhen")->fetch_assoc()['count'];
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM (SELECT id FROM phukien UNION ALL SELECT id FROM thucan) AS sp")->fetch_assoc()['count'];
$totalServices = $conn->query("SELECT COUNT(*) as count FROM dichvu")->fetch_assoc()['count'];
$totalDoctors = $conn->query("SELECT COUNT(*) as count FROM bacsi")->fetch_assoc()['count'];

// Doanh thu tháng hiện tại
$res_month = $conn->query("SELECT SUM(total) AS revenue_month FROM lichsumuahang WHERE MONTH(order_time) = MONTH(NOW()) AND YEAR(order_time) = YEAR(NOW())");
$revenue_month = $res_month->fetch_assoc()['revenue_month'] ?? 0;

// --- 2. TRUY VẤN DOANH THU 6 THÁNG GẦN NHẤT (Cho Biểu Đồ Đường) ---
$query_revenue = "
    SELECT 
        DATE_FORMAT(order_time, '%m/%Y') AS month_label, 
        SUM(total) AS monthly_revenue 
    FROM lichsumuahang 
    WHERE order_time >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY month_label
    ORDER BY MIN(order_time) ASC
";
$result_revenue = $conn->query($query_revenue);

$months_labels = [];
$revenue_values = [];

while ($row = $result_revenue->fetch_assoc()) {
    $months_labels[] = "Tháng " . $row['month_label'];
    $revenue_values[] = (float) $row['monthly_revenue'];
}

// --- 3. TRUY VẤN CƠ CẤU SẢN PHẨM (Cho Biểu Đồ Tròn) ---
$totalPhuKien = $conn->query("SELECT COUNT(*) as count FROM phukien")->fetch_assoc()['count'];
$totalThucAn = $conn->query("SELECT COUNT(*) as count FROM thucan")->fetch_assoc()['count'];

// --- 4. LẤY 5 LỊCH HẸN MỚI NHẤT ---
$recent_apps = $conn->query("
    SELECT lh.*, dv.ten_dich_vu 
    FROM lichhen lh 
    LEFT JOIN dichvu dv ON lh.ten_dich_vu = dv.ten_dich_vu
    ORDER BY lh.id DESC LIMIT 5
");
?>

<div class="container-fluid" id="content">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark"><i class="fa-solid fa-chart-pie text-success me-2"></i> Tổng Quan Chiến Lược</h2>
            <p class="text-secondary">Chào mừng Huynh trở lại! Ngân khố hôm nay có vẻ rất "rủng rỉnh" rứa mô!</p>
        </div>
    </div>

    <!-- HÀNG THỐNG KÊ (Top Stats) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-primary text-white h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 opacity-75">Lịch hẹn</p>
                        <h3 class="fw-bold mb-0"><?= number_format($totalAppointments) ?></h3>
                    </div>
                    <i class="fas fa-calendar-check fa-2x opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-success text-white h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 opacity-75">Doanh thu tháng</p>
                        <h4 class="fw-bold mb-0"><?= number_format($revenue_month, 0, '', '.') ?>₫</h4>
                    </div>
                    <i class="fas fa-chart-line fa-2x opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-warning text-dark h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 opacity-75">Sản phẩm</p>
                        <h3 class="fw-bold mb-0"><?= number_format($totalProducts) ?></h3>
                    </div>
                    <i class="fas fa-box-open fa-2x opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-info text-white h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 opacity-75">Bác sĩ</p>
                        <h3 class="fw-bold mb-0"><?= number_format($totalDoctors) ?></h3>
                    </div>
                    <i class="fas fa-user-md fa-2x opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- HÀNG BIỂU ĐỒ (Charts) -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4">Diễn biến doanh thu (Dữ liệu thực)</h5>
                <canvas id="revenueChart" style="max-height: 320px;"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 h-100 text-center">
                <h5 class="fw-bold mb-4">Cơ cấu Sản phẩm</h5>
                <div style="max-width: 280px; margin: 0 auto;">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="mt-4 small text-secondary">
                    <span><i class="fas fa-circle text-warning me-1"></i> Phụ kiện: <?= $totalPhuKien ?></span> | 
                    <span><i class="fas fa-circle text-orange me-1" style="color:#e67e22"></i> Thức ăn: <?= $totalThucAn ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- HÀNG BẢNG DỮ LIỆU (Recent Activity) -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0"><i class="fas fa-history me-2 text-primary"></i> Lịch hẹn mới nhất</h5>
                    <a href="manage_appointments.php" class="btn btn-sm btn-outline-success rounded-pill">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Khách hàng</th>
                                <th>Thú cưng</th>
                                <th>Dịch vụ</th>
                                <th>Ngày hẹn</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_apps->num_rows > 0): ?>
                                <?php while ($app = $recent_apps->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($app['ten_khach_hang']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($app['ten_thu_cung']) ?></span></td>
                                    <td><?= htmlspecialchars($app['ten_dich_vu']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($app['ngay_hen'])) ?></td>
                                    <td>
                                        <?php
                                        $status = $app['trang_thai'];
                                        $badge_class = 'bg-info';
                                        if ($status == 'Đã duyệt' || $status == 'Hoàn thành')
                                            $badge_class = 'bg-success';
                                        if ($status == 'Đã hủy')
                                            $badge_class = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= $status ?></span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-secondary">Chưa có "anh hùng" nào đặt lịch rứa mô!</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Biểu đồ Doanh thu (Real SQL Data)
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: <?= json_encode($months_labels) ?>,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?= json_encode($revenue_values) ?>,
                borderColor: '#2EB292',
                backgroundColor: 'rgba(46, 178, 146, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: { callback: (value) => value.toLocaleString('vi-VN') + '₫' }
                }
            }
        }
    });

    // 2. Biểu đồ Sản phẩm (Phụ kiện vs Thức ăn)
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: ['Phụ kiện', 'Thức ăn'],
            datasets: [{
                data: [<?= $totalPhuKien ?>, <?= $totalThucAn ?>],
                backgroundColor: ['#f1c40f', '#e67e22'],
                hoverOffset: 10,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            cutout: '70%'
        }
    });
</script>

