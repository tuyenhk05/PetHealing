<?php
// User final code block (baocao_gui)

include('../includes/db_connect.php');
$vai_tro = isset($_COOKIE["vai_tro"]) ? $_COOKIE["vai_tro"] : "";
if ($vai_tro == 'Admin') {
    $isAdmin = true;
} else {
    $isAdmin = false;
    $content = "<br/><h1> Bạn không có quyền truy cập trang này. </h1><br/><br/><br/>";
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['report_type'])) {
        $type = $_POST['report_type'];

        header("Content-Type: text/csv; charset=UTF-8");
        header("Content-Disposition: attachment; filename={$type}_report.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel

        switch ($type) {
            case "tongquan":
                echo "=== BÁO CÁO TỔNG QUAN ===\n";
                $tables = [
                    "nguoidung" => "Người dùng",
                    "thucung" => "Thú cưng",
                    "bacsi" => "Bác sĩ",
                    "dichvu" => "Dịch vụ",
                    "lichhen" => "Lịch hẹn",
                    "gio_hang" => "Giỏ hàng"
                ];
                echo "Danh mục\tTổng số bản ghi\n";
                foreach ($tables as $tbl => $label) {
                    $res = $conn->query("SELECT COUNT(*) as total FROM `$tbl`");
                    $row = $res->fetch_assoc();
                    echo "$label\t" . $row['total'] . "\n";
                }
                break;

            case "doanhthu":
                echo "=== DOANH THU DỊCH VỤ (LỊCH HẸN) ===\n";
                $res = $conn->query("SELECT lh.ten_dich_vu, COUNT(*) AS so_luot, dv.gia FROM lichhen lh JOIN dichvu dv ON lh.ten_dich_vu = dv.ten_dich_vu GROUP BY lh.ten_dich_vu, dv.gia");
                echo "Dịch vụ\tSố lượt\tDoanh thu\n";
                $tong_dv = 0;
                while ($row = $res->fetch_assoc()) {
                    $tong = $row['so_luot'] * $row['gia'];
                    $tong_dv += $tong;
                    echo $row['ten_dich_vu'] . "\t" . $row['so_luot'] . "\t" . $tong . "\n";
                }
                echo "Tổng doanh thu dịch vụ:\t\t" . $tong_dv . " VND\n";

                echo "\n=== DOANH THU BÁN SẢN PHẨM (GIỎ HÀNG) ===\n";
                echo "Tên sản phẩm\tSố lượng\tGiá\tDoanh thu\tTỷ lệ (%)\n";

                $res = $conn->query("SELECT name_sp, SUM(so_luong) AS sl FROM gio_hang GROUP BY name_sp");
                $tongDoanhThu = 0;
                $data = [];

                while ($row = $res->fetch_assoc()) {
                    $ten = $conn->real_escape_string($row['name_sp']);
                    $soluong = $row['sl'];
                    $gia = 0;
                    $sqlGia = "SELECT gia FROM thucan WHERE ten = '$ten' UNION SELECT gia FROM phukien WHERE ten = '$ten'";
                    $resGia = $conn->query($sqlGia);
                    if ($resGia && $giaRow = $resGia->fetch_assoc()) {
                        $gia = $giaRow['gia'];
                    }

                    $doanhthu = $gia * $soluong;
                    $tongDoanhThu += $doanhthu;

                    $data[] = [
                        'ten' => $ten,
                        'soluong' => $soluong,
                        'gia' => $gia,
                        'doanhthu' => $doanhthu
                    ];
                }

                foreach ($data as $row) {
                    $tyle = ($tongDoanhThu > 0) ? round($row['doanhthu'] / $tongDoanhThu * 100, 2) : 0;
                    echo "{$row['ten']}\t{$row['soluong']}\t{$row['gia']}\t{$row['doanhthu']}\t$tyle%\n";
                }

                echo "\nTổng doanh thu sản phẩm:\t\t\t$tongDoanhThu VND\n";
                echo "\nTỔNG DOANH THU TOÀN HỆ THỐNG:\t" . ($tongDoanhThu + $tong_dv) . " VND\n";
                break;

            case "doanhthu_thang":
                $month = $_POST['month'] ?? date('m');
                $year = $_POST['year'] ?? date('Y');

                echo "=== DOANH THU DỊCH VỤ THÁNG $month/$year ===\n";
                $res = $conn->query("SELECT lh.ten_dich_vu, COUNT(*) AS so_luot, dv.gia FROM lichhen lh JOIN dichvu dv ON lh.ten_dich_vu = dv.ten_dich_vu WHERE MONTH(lh.ngay_hen) = $month AND YEAR(lh.ngay_hen) = $year GROUP BY lh.ten_dich_vu, dv.gia");
                echo "Dịch vụ\tSố lượt\tDoanh thu\n";
                $tong = 0;
                while ($row = $res->fetch_assoc()) {
                    $subtotal = $row['so_luot'] * $row['gia'];
                    echo $row['ten_dich_vu'] . "\t" . $row['so_luot'] . "\t" . $subtotal . "\n";
                    $tong += $subtotal;
                }
                echo "Tổng doanh thu:\t\t$tong VND\n";
                break;

            case "tonkho":
                echo "=== TỒN KHO THỨC ĂN ===\n";
                $res = $conn->query("SELECT ten, danh_cho_loai, so_luong FROM thucan");
                echo "Tên\tDành cho\tTồn kho\n";
                while ($row = $res->fetch_assoc()) {
                    echo $row['ten'] . "\t" . $row['danh_cho_loai'] . "\t" . $row['so_luong'] . "\n";
                }

                echo "\n=== TỒN KHO PHỤ KIỆN ===\n";
                $res2 = $conn->query("SELECT ten, danh_cho_loai, so_luong FROM phukien");
                echo "Tên\tDành cho\tTồn kho\n";
                while ($row = $res2->fetch_assoc()) {
                    echo $row['ten'] . "\t" . $row['danh_cho_loai'] . "\t" . $row['so_luong'] . "\n";
                }
                break;

            default:
                echo "Không có báo cáo phù hợp.";
        }
        exit();
    }
}
;
    



include('../includes/header.php'); // Bao gồm header
?>

<!-- Giao diện chọn báo cáo -->
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
        <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Xuất báo cáo chuyên mục</title>
  <style>
  
    h2 { text-align: center; margin-bottom: 20px; }
    select, button { width: 100%; padding: 10px; margin-top: 10px; }
    button { background: #28a745; color: white; border: none; border-radius: 5px; font-weight: bold;width:150px; }
    button:hover { background: #218838; }
      .back-button {
          background: #34C9A5;
          color: white;
          border: none;
          padding: 8px 14px;
          border-radius: 5px;
          font-size: 14px;
          cursor: pointer;
          display: inline-flex;
          align-items: center;
          margin-bottom: 20px;
          width:150px;
      }

          .back-button i {
              margin-right: 6px;
          }

          .back-button:hover {
              background: #22866E;
          }
      .container1 {
          margin-bottom: 20px;
          max-width: 1200px;
          margin-left: auto;
          margin-right: auto;
      }
  </style>
</head>
<body>
     <?php if ($isAdmin) { ?>
    
    <div class="container1">
        <div  style=" max-width: 1200px; margin-left: auto; margin-right: auto;">
    <button onclick="window.history.back()" class="back-button">
        <i class="fas fa-arrow-left"></i> Quay lại
    </button></div>
  <h2>Xuất Báo Cáo</h2>
  <form method="POST">
    <label>Chọn loại báo cáo:</label>
    <select name="report_type" onchange="toggleDateInputs(this.value)" required>
      <option value="tongquan">📊 Tổng quan</option>
      <option value="doanhthu">💰 Doanh thu tổng</option>
      <option value="doanhthu_thang">📅 Doanh thu theo tháng</option>
    

      <option value="tonkho">📦 Tồn kho</option>
    </select>

    <div id="dateInputs" style="display: none;">
      <label>Tháng:</label>
      <select name="month">
        <?php for ($m = 1; $m <= 12; $m++): ?>
          <option value="<?= $m ?>"><?= $m ?></option>
        <?php endfor; ?>
      </select>

      <label>Năm:</label>
      <select name="year">
        <?php for ($y = 2023; $y <= date("Y"); $y++): ?>
          <option value="<?= $y ?>"><?= $y ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <button type="submit">Xuất file Excel</button>
  </form>
</div>
<script>
function toggleDateInputs(value) {
  document.getElementById('dateInputs').style.display = value === 'doanhthu_thang' ? 'block' : 'none';
}
</script>
    <?php } else {
         echo $content;
     } ?>


</body>
</html>
<?php
include('../includes/footer.php');
?>
