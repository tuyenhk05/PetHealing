<?php
// File: export_baocao_chuyenmuc.php (đã fix lỗi encoding CSV xuất ra)

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=doanhthu_thang_report.csv");
header("Pragma: no-cache");
header("Expires: 0");

include('../includes/db_connect.php');

// Output UTF-8 BOM
echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel

$action = $_POST['action'] ?? '';

switch ($action) {
    case "doanhthu_thang":
        $month = $_POST['month'] ?? date('m');
        $year = $_POST['year'] ?? date('Y');

        echo "=== DOANH THU DỊCH VỤ THÁNG $month/$year ===\n";
        $res = $conn->query("SELECT lh.ten_dich_vu, COUNT(*) AS so_luot, dv.gia 
            FROM lichhen lh 
            JOIN dichvu dv ON lh.ten_dich_vu = dv.ten_dich_vu 
            WHERE MONTH(lh.ngay_hen) = $month AND YEAR(lh.ngay_hen) = $year 
            GROUP BY lh.ten_dich_vu, dv.gia");

        echo "Dịch vụ\tSố lượt\tDoanh thu\n";
        $tong_dv = 0;
        while ($row = $res->fetch_assoc()) {
            $subtotal = $row['so_luot'] * $row['gia'];
            echo "{$row['ten_dich_vu']}\t{$row['so_luot']}\t{$subtotal}\n";
            $tong_dv += $subtotal;
        }
        echo "Tổng doanh thu:\t\t$tong_dv VND\n\n";

        echo "=== DOANH THU SẢN PHẨM THÁNG $month/$year ===\n";
        echo "Tên sản phẩm\tSố lượng\tGiá\tDoanh thu\tTỷ lệ (%)\n";

        $res = $conn->query("SELECT name_sp, SUM(so_luong) AS sl 
            FROM gio_hang 
            WHERE MONTH(created_at) = $month AND YEAR(created_at) = $year 
            GROUP BY name_sp");

        $tong_sp = 0;
        $data = [];

        while ($row = $res->fetch_assoc()) {
            $ten = $row['name_sp'];
            $sl = $row['sl'];
            $gia = 0;

            $stmt = $conn->prepare("SELECT gia FROM thucan WHERE ten COLLATE utf8mb4_general_ci = ?
                UNION
                SELECT gia FROM phukien WHERE ten COLLATE utf8mb4_general_ci = ?");
            $stmt->bind_param("ss", $ten, $ten);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $r = $result->fetch_assoc()) {
                $gia = $r['gia'];
            }

            $doanhthu = $gia * $sl;
            $tong_sp += $doanhthu;
            $data[] = ['ten' => $ten, 'sl' => $sl, 'gia' => $gia, 'doanhthu' => $doanhthu];
        }

        foreach ($data as $item) {
            $tyle = ($tong_sp > 0) ? round($item['doanhthu'] / $tong_sp * 100, 2) : 0;
            echo "{$item['ten']}\t{$item['sl']}\t{$item['gia']}\t{$item['doanhthu']}\t$tyle%\n";
        }

        echo "\nTổng doanh thu sản phẩm:\t\t\t$tong_sp VND\n";
        echo "\nTỔNG DOANH THU THÁNG $month/$year:\t" . ($tong_sp + $tong_dv) . " VND\n";
        break;

    default:
        echo "Không có hành động hợp lệ";
        break;
}
?>
