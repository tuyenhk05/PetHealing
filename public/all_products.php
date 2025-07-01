<?php
include('../includes/db_connect.php');
include('../includes/header.php'); // Bao gồm header

$sql = "
    SELECT id, ten, mo_ta, gia, image, 'thuc-an' AS loai FROM ThucAn
    UNION ALL
    SELECT id, ten, mo_ta, gia, image, 'phu-kien' AS loai FROM PhuKien
    ORDER BY RAND()
";

$id = isset($_COOKIE["user_id"]) ? $_COOKIE["user_id"] : "";  
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PetHealing - Trang sản phẩm</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/category.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <script src="../assets/js/category.js" defer></script>
</head>
<body>
    <div class="inf">Thêm vào giỏ hàng thành công</div>
    <div class="box">
        <div class="inner-subnav">
            <div class="overlay">
                <h1>Cửa hàng <span>thú cưng</span></h1>
                <p><span>Boss</span> sủa một tiếng, <span>sen</span> quẹo lựa ngay.</p>

                <form id="searchForm" class="search-box">
                    <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm..." />
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
                        <?php 
                        $imagePath = "../assets/image/";
                        while ($row = mysqli_fetch_assoc($result)) { 
                            $formattedPrice = number_format($row['gia'], 0, '', '.');
                            $name = htmlspecialchars($row['ten']);  
                            // Xử lý ảnh: nếu có ảnh và file tồn tại mới lấy, không thì lấy default
                            if (!empty($row['image']) && file_exists($imagePath . $row['image'])) {
                                $image_path = $imagePath . $row['image'];
                            } else {
                                $image_path = $imagePath . "default.jpg";
                            }
                        ?>
                        <div class="card" data-category="<?php echo $row['loai']; ?>" data-name="<?php echo $name; ?>">
                            <div class="card-image">
                                <img src="<?php echo $image_path; ?>" alt="<?php echo $name; ?>" />
                            </div>
                            <div class="card-content">
                                <h3><?php echo $name; ?></h3>
                                <p><?php echo htmlspecialchars($row['mo_ta']); ?></p>
                                <span><?php echo $formattedPrice; ?> VND</span>

                                <!-- Chọn số lượng với nút tăng giảm -->
                                <div class="quantity-selector">
                                    <label for="quantity-<?php echo $row['id']; ?>">Số lượng:</label>
                                    <button class="quantity-btn" onclick="updateQuantity('<?php echo $row['id']; ?>', -1)">-</button>
                                    <input type="number" id="quantity-<?php echo $row['id']; ?>" name="quantity" min="1" value="1" readonly />
                                    <button class="quantity-btn" onclick="updateQuantity('<?php echo $row['id']; ?>', 1)">+</button>
                                </div>

                                <div class="actions">
                                    <a href="product_detail.php?id=<?php echo $row['id']; ?>&type=<?php echo $row['loai']; ?>" class="details">Chi tiết</a>
                                    <button 
                                        class="btn-filled add-to-cart" 
                                        data-id="<?php echo $row['id']; ?>"
                                        data-name="<?php echo $name; ?>" 
                                        data-price="<?php echo $row['gia']; ?>"
                                        data-type="<?php echo $row['loai']; ?>"
                                    >
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
                <i class="fa fa-shopping-cart"></i>
            </a>
        </div>
    </div>

    <script>
        const categoryItems = document.querySelectorAll(".category-list li");

        categoryItems.forEach((item) => {
            item.addEventListener("click", function () {
                categoryItems.forEach((i) => i.classList.remove("active"));
                this.classList.add("active");

                const category = this.getAttribute("data-category");
                const cards = document.querySelectorAll(".card");
                cards.forEach((card) => {
                    if (category === "all" || card.getAttribute("data-category") === category) {
                        card.style.display = "block";
                    } else {
                        card.style.display = "none";
                    }
                });
            });
        });

        // Cập nhật số lượng
        function updateQuantity(productId, change) {
            const quantityInput = document.getElementById('quantity-' + productId);
            let currentQuantity = parseInt(quantityInput.value);
            currentQuantity += change;
            if (currentQuantity < 1) currentQuantity = 1;
            quantityInput.value = currentQuantity;
        }

        const isLoggedIn = <?= $id !== "" ? 'true' : 'false' ?>;

        // Xử lý thêm vào giỏ hàng
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                if (!isLoggedIn) {
                    window.location.href = 'login.php';
                    return;
                }

                const productId = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const quantity = parseInt(document.getElementById('quantity-' + productId).value);

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
        });

        // Tìm kiếm sản phẩm
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                const productName = card.getAttribute('data-name').toLowerCase();
                card.style.display = productName.includes(searchTerm) ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>

<?php
include('../includes/footer.php');
?>
