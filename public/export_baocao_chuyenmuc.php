<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=doanhthu_thang_report.xls");

include('../includes/db_connect.php');

$action = $_POST['action'] ?? '';

switch ($action) {
    case "doanhthu_thang":
        $month = $_POST['month'] ?? date('m');
        $year = $_POST['year'] ?? date('Y');

        // === DOANH THU DỊCH VỤ ===
        echo "=== DOANH THU DICH VU THANG $month/$year ===\n";
        $res = $conn->query("SELECT lh.ten_dich_vu, COUNT(*) AS so_luot, dv.gia 
            FROM lichhen lh 
            JOIN dichvu dv ON lh.ten_dich_vu = dv.ten_dich_vu 
            WHERE MONTH(lh.ngay_hen) = $month AND YEAR(lh.ngay_hen) = $year
            GROUP BY lh.ten_dich_vu, dv.gia");

        echo "Dich vu\tSo luot\tDoanh thu\n";
        $tong_dv = 0;
        while ($row = $res->fetch_assoc()) {
            $subtotal = $row['so_luot'] * $row['gia'];
            echo "{$row['ten_dich_vu']}\t{$row['so_luot']}\t{$subtotal}\n";
            $tong_dv += $subtotal;
        }
        echo "Tong doanh thu:\t\t$tong_dv VND\n\n";

        // === DOANH THU SẢN PHẨM ===
        echo "=== DOANH THU SAN PHAM THANG $month/$year ===\n";
        echo "Ten san pham\tSo luong\tGia\tDoanh thu\tTy le (%)\n";

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

            // Dùng prepared statement để tìm giá từ thucan hoặc phukien
            $stmt = $conn->prepare("
                SELECT gia FROM thucan WHERE ten COLLATE utf8mb4_general_ci = ?
                UNION
                SELECT gia FROM phukien WHERE ten COLLATE utf8mb4_general_ci = ?
            ");
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

        echo "\nTong doanh thu san pham:\t\t\t$tong_sp VND\n";
        echo "\nTONG DOANH THU THANG $month/$year:\t" . ($tong_sp + $tong_dv) . " VND\n";
        break;

    default:
        echo "Khong co hanh dong hop le";
        break;
}
?>
