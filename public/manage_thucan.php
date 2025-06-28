<?php
include('../includes/db_connect.php');
if (isset($_POST['add'])) {
    $ten = $_POST['ten'];
    $danh_cho_loai = $_POST['danh_cho_loai'];
    $mo_ta = $_POST['mo_ta'];
    $gia = $_POST['gia'];
    $xuat_su = $_POST['xuat_su'];
    $chat_lieu = $_POST['thanh_phan'];
    $cong_dung = $_POST['cong_dung'];
    $phu_hop = $_POST['phu_hop_voi_tuoi_thu_cung'];

    

    $query = "INSERT INTO ThucAn (ten, danh_cho_loai, mo_ta, gia, xuat_su, thanh_phan, cong_dung, phu_hop_voi_tuoi_thu_cung) 
              VALUES ('$ten', '$danh_cho_loai', '$mo_ta', '$gia', '$xuat_su', '$chat_lieu', '$cong_dung', '$phu_hop')";
    mysqli_query($conn, $query);
    header("Location: manage_thucan.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM ThucAn WHERE id=$id");
    header("Location: manage_thucan.php");
    exit();
}

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM ThucAn");
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$result = mysqli_query($conn, "SELECT * FROM ThucAn LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý Thức ăn</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f9f9f9;
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        tr:hover { background-color: #f1f1f1; }
        img {
            width: 40px;
            height: auto;
            border-radius: 8px;
        }
        form {
            margin: 30px auto;
            padding: 20px;
            background: white;
            max-width: 600px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background-color: #218838; }

        .pagination {
        display: flex;
        justify-content: center;
        margin-top: 30px;
        gap: 6px;
        }

        .pagination a {
        display: inline-block;
        padding: 8px 14px;
        background-color: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.25s ease-in-out;
        }

        .pagination a:hover {
        background-color: #218838;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
        }

        .pagination a.active {
        background-color: #155724;
        pointer-events: none;
        transform: scale(1.05);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.3);
        }
        .back a{
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            font-size: 15px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 40px;   
        }
        .back a:hover {
            background-color: #ccc;
            color: #000;
        }
</style>


    </style>
</head>
<body>
        
    <h2>Quản lý Phụ Kiện</h2>
    <body>
    <button id="toggleFormBtn" class="btn btn-success mb-3">Thêm sản phẩm</button>
    <div id="formContainer" style="display: none;">
        
            <form method="POST" enctype="multipart/form-data">
            <input type="text" name="ten" placeholder="Tên sản phẩm" required>
            <input type="text" name="danh_cho_loai" placeholder="Dành cho loại" required>
            <textarea name="mo_ta" placeholder="Mô tả"></textarea>
            <input type="number" name="gia" placeholder="Giá" required>
            <input type="text" name="xuat_su" placeholder="Xuất xứ">
            <input type="text" name="thanh_phan" placeholder="Thành phần">
            <textarea name="cong_dung" placeholder="Công dụng"></textarea>
            <input type="text" name="phu_hop_voi_tuoi_thu_cung" placeholder="Phù hợp với tuổi thú cưng">
            <input type="file" name="hinh" accept="image/*" required>
            <button type="submit" name="add">Thêm sản phẩm</button>
            </form>
        </div>
    
</div>
    <table>
        <tr>
            <th>Hình ảnh</th><th>Tên sản phẩm</th><th>Loại thú cưng</th><th>Giá</th><th>Xóa</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)) {
            $imgPath = "assets/image/" . $row['ten'] . ".jpg";
        ?>
        <tr>
            <td><img src="../assets/image/<?php echo $row['ten']; ?>.jpg" alt="<?php echo $row['ten']; ?>"></td>
            <td><?php echo $row['ten']; ?></td>
            <td><?php echo $row['danh_cho_loai']; ?></td>
            <td><?php echo number_format($row['gia']); ?> VND</td>
            <td><a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Xác nhận xóa?')">Xóa</a></td>
        </tr>
        <?php } ?>
    </table>
    <div class="back">
        <a href="manage_product.php">Quay lại</a>
    </div>
   
 <div class="pagination">
  <?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <a href="?page=<?= $i ?>" style="margin: 0 5px; <?= ($i == $page) ? 'font-weight:bold; text-decoration:underline;' : '' ?>">
      <?= $i ?>
    </a>
  <?php endfor; ?>
</div>




<script>
  document.getElementById("toggleFormBtn").addEventListener("click", function () {
    var form = document.getElementById("formContainer");
    if (form.style.display === "none") {
      form.style.display = "block";
      this.innerText = "Ẩn form thêm";
    } else {
      form.style.display = "none";
      this.innerText = "+ Thêm sản phẩm";
    }
  });
</script>
    
    
</body>
</html>