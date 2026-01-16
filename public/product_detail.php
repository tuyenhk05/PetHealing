<?php
include('../includes/db_connect.php');
$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : null;

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$product_type = isset($_GET['type']) ? $_GET['type'] : null;

// Kiểm tra loại sản phẩm và lấy thông tin từ bảng tương ứng
if ($product_id && $product_type) {
    if ($product_type == 'thuc-an') {
        $table = 'ThucAn';
    } elseif ($product_type == 'phu-kien') {
        $table = 'PhuKien';
    } else {
        header('Location: index.php');
        exit();
    }
    
    $sql = "SELECT * FROM $table WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    
    if (!$product) {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
include('../includes/header.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo htmlspecialchars($product['ten']); ?> - PetHealing</title>
<link rel="stylesheet" href="../assets/css/style.css" />
<link rel="stylesheet" href="../assets/css/product_detail.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" /></head>
<body>
    <style>
        /* From Uiverse.io by Jedi-hongbin */
        .back-button {
            display: flex;
            height: 3em;
            width: 100px;
            align-items: center;
            justify-content: center;
            background-color: #eeeeee4b;
            border-radius: 3px;
            letter-spacing: 1px;
            transition: all 0.2s linear;
            cursor: pointer;
            border: none;
            background: #fff;
            margin-bottom: 20px;
            font-weight: 600;
        }

            .back-button > svg {
                margin-right: 5px;
                margin-left: 5px;
                font-size: 20px;
                transition: all 0.4s ease-in;
            }

            .back-button:hover > svg {
                font-size: 1.2em;
                transform: translateX(-5px);
            }

            .back-button:hover {
                box-shadow: 9px 9px 33px #d1d1d1, -9px -9px 33px #ffffff;
                transform: translateY(-2px);
            }
    </style>
<div class="inf">Thêm vào giỏ hàng thành công</div>
       
<div class="product-detail-container box">
     <button onclick="window.history.back()" class="back-button">
         <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024"><path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path></svg>
        <span> Quay lại</span>
    </button>
  <div class="product-detail">
    <div class="product-images">
      <div class="main-image">
        <?php 
          $imagePath = "../assets/image/";
          $imageFile = (!empty($product['image']) && file_exists($imagePath . $product['image'])) 
              ? $product['image'] 
              : "default.jpg"; 
        ?>
        <img src="<?php echo $imagePath . htmlspecialchars($imageFile); ?>" alt="<?php echo htmlspecialchars($product['ten']); ?>" />
      </div>
    </div>

    <div class="product-info">
      <h1><?php echo htmlspecialchars($product['ten']); ?></h1>
      <div class="price"><?php echo number_format($product['gia'], 0, '', '.'); ?> VND</div>

      <div class="product-meta">
        <?php if ($product_type == 'thuc-an'): ?>
          <div class="meta-item">
            <span class="meta-label">Dành cho:</span>
            <span class="meta-value"><?php echo htmlspecialchars($product['danh_cho_loai']); ?></span>
          </div>
          <div class="meta-item">
            <span class="meta-label">Độ tuổi phù hợp:</span>
            <span class="meta-value"><?php echo htmlspecialchars($product['phu_hop_voi_tuoi_thu_cung']); ?></span>
          </div>
          <div class="meta-item">
            <span class="meta-label">Xuất xứ:</span>
            <span class="meta-value"><?php echo htmlspecialchars($product['xuat_su']); ?></span>
          </div>
        <?php else: ?>
          <div class="meta-item">
            <span class="meta-label">Dành cho:</span>
            <span class="meta-value"><?php echo htmlspecialchars($product['danh_cho_loai']); ?></span>
          </div>
          <div class="meta-item">
            <span class="meta-label">Chất liệu:</span>
            <span class="meta-value"><?php echo htmlspecialchars($product['chat_lieu']); ?></span>
          </div>
          <div class="meta-item">
            <span class="meta-label">Xuất xứ:</span>
            <span class="meta-value"><?php echo htmlspecialchars($product['xuat_su']); ?></span>
          </div>
        <?php endif; ?>
      </div>

      <div class="quantity-selector">
        <label for="quantity">Số lượng:</label>
        <button class="quantity-btn" onclick="updateQuantity(-1)">-</button>
        <input type="number" id="quantity" name="quantity" min="1" value="1" />
        <button class="quantity-btn" onclick="updateQuantity(1)">+</button>
      </div>

      <div class="actions">
        <button class="btn-filled add-to-cart" 
                data-id="<?php echo $product['id']; ?>" 
                data-name="<?php echo htmlspecialchars($product['ten']); ?>" 
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
          <p><?php echo nl2br(htmlspecialchars($product['mo_ta'])); ?></p>
        </div>

        <?php if ($product_type == 'thuc-an'): ?>
        <div id="ingredients-tab" class="tab-content">
          <h3>Thành phần</h3>
          <p><?php echo nl2br(htmlspecialchars($product['thanh_phan'])); ?></p>
        </div>
        <?php endif; ?>

        <div id="benefits-tab" class="tab-content">
          <h3>Công dụng</h3>
          <p><?php echo nl2br(htmlspecialchars($product['cong_dung'])); ?></p>
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
  function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}
  // Thêm sản phẩm vào giỏ hàng
    document.querySelector('.add-to-cart').addEventListener('click', function () {
        const get = getCookie('user_id');
        if (get) {
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
        }
        else {
            window.location.href = 'login.php';
        }
   
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
