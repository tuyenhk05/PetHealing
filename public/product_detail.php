<?php
include('../includes/db_connect.php');
include('../includes/header.php');

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? $_GET['id'] : null;
$product_type = isset($_GET['type']) ? $_GET['type'] : null;

// Kiểm tra loại sản phẩm và lấy thông tin từ bảng tương ứng
if ($product_id && $product_type) {
    if ($product_type == 'thuc-an') {
        $table = 'ThucAn';
    } elseif ($product_type == 'phu-kien') {
        $table = 'PhuKien';
    } else {
        // Nếu không phải 2 loại trên, chuyển hướng về trang chủ
        header('Location: index.php');
        exit();
    }
    
    // Truy vấn thông tin sản phẩm
    $sql = "SELECT * FROM $table WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    
    if (!$product) {
        // Nếu không tìm thấy sản phẩm, chuyển hướng về trang chủ
        header('Location: index.php');
        exit();
    }
} else {
    // Nếu không có ID hoặc loại sản phẩm, chuyển hướng về trang chủ
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['ten']; ?> - PetHealing</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/product_detail.css">
   
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
</head>
<body>
      <div class="inf">Thêm vào giỏ hàng thành công</div>
    <div class="product-detail-container box">
        <div class="product-detail">
            <div class="product-images">
                <div class="main-image">
                    <img src="../assets/image/<?php echo $product['ten']; ?>.jpg" alt="<?php echo $product['ten']; ?>">
                </div>
            </div>
            
            <div class="product-info">
                <h1><?php echo $product['ten']; ?></h1>
                <div class="price"><?php echo number_format($product['gia'], 0, '', '.'); ?> VND</div>
                
                <div class="product-meta">
                    <?php if ($product_type == 'thuc-an'): ?>
                        <div class="meta-item">
                            <span class="meta-label">Dành cho:</span>
                            <span class="meta-value"><?php echo $product['danh_cho_loai']; ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Độ tuổi phù hợp:</span>
                            <span class="meta-value"><?php echo $product['phu_hop_voi_tuoi_thu_cung']; ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Xuất xứ:</span>
                            <span class="meta-value"><?php echo $product['xuat_su']; ?></span>
                        </div>
                    <?php else: ?>
                        <div class="meta-item">
                            <span class="meta-label">Dành cho:</span>
                            <span class="meta-value"><?php echo $product['danh_cho_loai']; ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Chất liệu:</span>
                            <span class="meta-value"><?php echo $product['chat_lieu']; ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Xuất xứ:</span>
                            <span class="meta-value"><?php echo $product['xuat_su']; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="quantity-selector">
                    <label for="quantity">Số lượng:</label>
                    <button class="quantity-btn" onclick="updateQuantity(-1)">-</button>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" readonly>
                    <button class="quantity-btn" onclick="updateQuantity(1)">+</button>
                </div>
                
                <div class="actions">
                    <button class="btn-filled add-to-cart" 
                            data-id="<?php echo $product['id']; ?>" 
                            data-name="<?php echo $product['ten']; ?>" 
                            data-price="<?php echo $product['gia']; ?>"
                            data-type="<?php echo $product_type; ?>">
                        <i class="fa fa-shopping-cart"></i> Thêm vào giỏ
                    </button>
                </div>
                
                <div class="product-description">
                    <div class="description-tabs">
                        <button class="tab-btn active" onclick="openTab(event, 'description-tab')">Mô tả</button>
                        <?php if ($product_type == 'thuc-an'): ?>
                            <button class="tab-btn" onclick="openTab(event, 'ingredients-tab')">Thành phần</button>
                        <?php endif; ?>
                        <button class="tab-btn" onclick="openTab(event, 'benefits-tab')">Công dụng</button>
                    </div>
                    
                    <div id="description-tab" class="tab-content active">
                        <h3>Mô tả sản phẩm</h3>
                        <p><?php echo $product['mo_ta']; ?></p>
                    </div>
                    
                    <?php if ($product_type == 'thuc-an'): ?>
                    <div id="ingredients-tab" class="tab-content">
                        <h3>Thành phần</h3>
                        <p><?php echo $product['thanh_phan']; ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div id="benefits-tab" class="tab-content">
                        <h3>Công dụng</h3>
                        <p><?php echo $product['cong_dung']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="cart">
        <a href="cart.php" class="btn-cart">
            <i class="fa fa-shopping-cart"></i> 
        </a>
    </div>


    <script>
        // Hàm cập nhật số lượng
        function updateQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            let currentQuantity = parseInt(quantityInput.value);
            currentQuantity += change;
            if (currentQuantity < 1) currentQuantity = 1;
            quantityInput.value = currentQuantity;
        }
        
        // Thêm sản phẩm vào giỏ hàng
        document.querySelector('.add-to-cart').addEventListener('click', function () {
            const productId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const quantity = parseInt(document.getElementById('quantity').value);

            fetch('../includes/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_san_pham: productId,
                    so_luong: quantity,
                    name_sp: name
                })
            })
                .then(res => res.text())
                .then(data => {
                    console.log(data);
                    const infElement = document.querySelector('.inf');
                    infElement.classList.add('show');
                    setTimeout(() => infElement.classList.remove('show'), 2000);
                })
                .catch(error => console.error('Lỗi:', error));


        });
        
        // Hàm chuyển tab
        function openTab(evt, tabName) {
            // Ẩn tất cả các tab content
            const tabContents = document.getElementsByClassName("tab-content");
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove("active");
            }
            
            // Xóa active class từ tất cả các tab buttons
            const tabButtons = document.getElementsByClassName("tab-btn");
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove("active");
            }
            
            // Hiển thị tab hiện tại và thêm active class vào button
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
    </script>
</body>
</html>

<?php
include('../includes/footer.php');
?>