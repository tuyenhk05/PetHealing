<?php
include('../includes/db_connect.php');
include('../includes/header.php'); // Bao gồm header

$sql = "
    SELECT id, ten, mo_ta, gia, 'thuc-an' AS loai FROM ThucAn
    UNION ALL
    SELECT id, ten, mo_ta, gia, 'phu-kien' AS loai FROM PhuKien
    ORDER BY RAND()
";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHealing - Trang sản phẩm</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/category.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="../assets/js/category.js" defer></script>
</head>
<body>
    <div class="inf">Thêm vào giỏ hàng thành công</div>
    <div class="inner-subnav">
        <div class="overlay">
            <h1>Cửa hàng <span>thú cưng</span></h1>
            <p><span>Boss</span> sủa một tiếng,<span> sen</span> quẹo lựa ngay.</p>

            <form id="searchForm" class="search-box">
                <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>

    <!-- Danh mục sản phẩm -->
    <div class="container-category">
        <div class="store">
            <div class="category">
                <div class="category-filter">
                    <h2><span class="icon"></span>Danh mục</h2>
                    <ul class="category-list">
                        <li data-category="all" class="active">Tất cả</li>
                        <li data-category="thuc-an">Thức ăn</li>
                        <li data-category="phu-kien">Phụ kiện</li>
                    </ul>
                </div>
                <div class="product">
                    <?php while ($row = mysqli_fetch_assoc($result)) { 
                        $formattedPrice = number_format($row['gia'], 0, '', '.');
                        ?>
                        <div class="card" data-category="<?php echo $row['loai']; ?>" data-name="<?php echo strtolower($row['ten']); ?>">
                            <div class="card-image">
                                <img src="../assets/image/<?php echo $row['ten']; ?>.jpg" alt="<?php echo $row['ten']; ?>">
                            </div>
                            <div class="card-content">
                                <h3><?php echo $row['ten']; ?></h3>
                                <p><?php echo $row['mo_ta']; ?></p>
                                <span><?php echo $formattedPrice; ?> VND</span>

                                <!-- Chọn số lượng với nút tăng giảm -->
                                <div class="quantity-selector">
                                    <label for="quantity-<?php echo $row['id']; ?>">Số lượng:</label>
                                    <button class="quantity-btn" onclick="updateQuantity('<?php echo $row['id']; ?>', -1)">-</button>
                                    <input type="number" id="quantity-<?php echo $row['id']; ?>" name="quantity" min="1" value="1" readonly>
                                    <button class="quantity-btn" onclick="updateQuantity('<?php echo $row['id']; ?>', 1)">+</button>
                                </div>

                                <div class="actions">
                                    <a href="product_detail.php?id=<?php echo $row['id']; ?>&type=<?php echo $row['loai']; ?>" class="details">Chi tiết</a>
                                    <button class="btn-filled add-to-cart" 
                                            data-id="<?php echo $row['id']; ?>"
                                            data-name="<?php echo $row['ten']; ?>" 
                                            data-price="<?php echo $row['gia']; ?>"
                                            data-type="<?php echo $row['loai']; ?>">
                                        <i class="fa fa-shopping-cart"></i> Thêm
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="pagination"></div>
            <div class="details"></div>
        </div>
    </div>

    <div class="cart">
        <a href="cart.php" class="btn-cart">
            <i class="fa fa-shopping-cart"></i> <span id="cart-count">0</span> 
        </a>
    </div>

    <script>
       
        const categoryItems = document.querySelectorAll(".category-list li");

        categoryItems.forEach((item) => {
            item.addEventListener("click", function () {
                categoryItems.forEach((item) => item.classList.remove("active"));
                this.classList.add("active");
            });
        });

        const cart = [];

        // Cập nhật giỏ hàng vào localStorage
        function updateCart() {
            localStorage.setItem('cart', JSON.stringify(cart));
            document.getElementById('cart-count').textContent = cart.length;
        }

        // Cập nhật số lượng sản phẩm
        function updateQuantity(productId, change) {
            const quantityInput = document.getElementById('quantity-' + productId);
            let currentQuantity = parseInt(quantityInput.value);
            currentQuantity += change;
            if (currentQuantity < 1) currentQuantity = 1; // Đảm bảo không giảm xuống dưới 1
            quantityInput.value = currentQuantity;
        }

        // Thêm sản phẩm vào giỏ hàng
        const addToCartButtons = document.querySelectorAll('.add-to-cart');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                const productName = this.getAttribute('data-name');
                const productPrice = this.getAttribute('data-price');
                const productType = this.getAttribute('data-type');
                const quantity = document.getElementById('quantity-' + productId).value;

                const product = {
                    id: productId,
                    name: productName,
                    price: parseFloat(productPrice),
                    quantity: parseInt(quantity),
                    type: productType,
                    selected: true
                };

                // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
                const existingProduct = cart.find(item => item.id === productId);

                if (existingProduct) {
                    // Nếu sản phẩm đã có, chỉ cập nhật số lượng
                    existingProduct.quantity += product.quantity;
                } else {
                    // Nếu sản phẩm chưa có, thêm mới
                    cart.push(product);
                }

                // Cập nhật giỏ hàng trong localStorage
                updateCart();
               const infElement = document.querySelector('.inf');
               
                  infElement.classList.add('show');
       

                
                setTimeout(() => {
                  infElement.classList.remove('show');
                }, 2000);  // 

               

            });
        });

        // Tải giỏ hàng khi trang được tải lại
        window.onload = function() {
            const storedCart = JSON.parse(localStorage.getItem('cart')) || [];
            storedCart.forEach(item => {
                cart.push(item);  // Thêm sản phẩm từ localStorage vào giỏ hàng
            });
            updateCart();  // Cập nhật giỏ hàng sau khi tải
        };

        // Xử lý tìm kiếm
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.card');
            
            cards.forEach(card => {
                const productName = card.getAttribute('data-name');
                if (productName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>

<?php
include('../includes/footer.php'); // Bao gồm footer
?>