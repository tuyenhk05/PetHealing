<?php
include('../includes/header.php'); // Bao gồm header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/cart.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="cart-page">
    <h1>Giỏ hàng</h1>
    <div id="cart-items" class="cart-items">
        <!-- Các sản phẩm trong giỏ hàng sẽ được thêm vào đây -->
    </div>
    <div class="cart-total">
        <span>Tổng cộng: </span><span id="cart-total">0 VND</span>
    </div>
    <div class="checkout-btn">
        <button class="btn-filled" onclick="window.location.href='checkout.php'">Tiến hành thanh toán</button>
    </div>
</div>

<script>
    // Khi trang được tải, lấy dữ liệu giỏ hàng từ localStorage
    window.onload = function () {

        const storedCart = JSON.parse(localStorage.getItem('cart')) || [];
    
    storedCart.filter(
        item => {
            item.selected = true;
        }

        )
    localStorage.setItem('cart', JSON.stringify(storedCart));
    const cartItemsContainer = document.getElementById('cart-items');
    let totalPrice = 0;
    let selectedItems = [];

    // Nếu giỏ hàng không rỗng, hiển thị các sản phẩm
    if (storedCart.length > 0) {
        storedCart.forEach(item => {
            // Đảm bảo giá trị là số khi lấy ra từ localStorage
            item.price = parseFloat(item.price);
            item.quantity = parseInt(item.quantity);

            const itemElement = document.createElement('div');
            itemElement.classList.add('cart-item');
            itemElement.innerHTML = `
                <img src="../assets/image/${item.name}.jpg" alt="${item.name}">
                <div class="cart-item-details">
                    <h3>${item.name}</h3>
                    <p>Giá: ${item.price.toLocaleString()},000 VND</p>
                    <p>Số lượng: <span class="quantity">${item.quantity}</span></p>
                </div>
                <div class="cart-item-actions">
                    <button onclick="updateQuantity('${item.name}', -1)">-</button>
                    <button onclick="updateQuantity('${item.name}', 1)">+</button>
                    <input type="checkbox" class="select-checkbox" checked="true" onchange="toggleSelectItem('${item.name}')"> Chọn thanh toán
                    <button class="remove-item" onclick="removeItem('${item.name}')">Xóa</button>
                </div>
                <div class="total">
                    <p>Tổng: ${(item.price * item.quantity).toLocaleString()},000 VND</p>
                </div>
            `;
            cartItemsContainer.appendChild(itemElement);

            // Cộng dồn tổng tiền của giỏ hàng
            totalPrice += item.price * item.quantity;
        });

        document.getElementById('cart-total').textContent = totalPrice.toLocaleString() + ',000 VND';
    } else {
        cartItemsContainer.innerHTML = '<p>Giỏ hàng của bạn hiện tại trống.</p>';
    }
};

// Hàm cập nhật số lượng sản phẩm trong giỏ hàng
function updateQuantity(productName, change) {
    const storedCart = JSON.parse(localStorage.getItem('cart')) || [];

    // Duyệt qua giỏ hàng để tìm sản phẩm và cập nhật số lượng
    const updatedCart = storedCart.map(item => {
        item.price = parseFloat(item.price);
            item.quantity = parseInt(item.quantity);
        if (item.name === productName) {
            // Cập nhật số lượng
            item.quantity += change;

            // Đảm bảo số lượng không nhỏ hơn 1
            if (item.quantity < 1) item.quantity = 1;
        }
        return item;
    });

    // Lưu lại giỏ hàng đã cập nhật vào localStorage
    localStorage.setItem('cart', JSON.stringify(updatedCart));

    // Tải lại trang để hiển thị thông tin mới
    window.location.reload();
}

// Hàm xóa sản phẩm khỏi giỏ hàng
function removeItem(productName) {
    let storedCart = JSON.parse(localStorage.getItem('cart')) || [];

    // Xóa sản phẩm khỏi giỏ hàng
    storedCart = storedCart.filter(item => item.name !== productName);

    // Lưu lại giỏ hàng đã cập nhật vào localStorage
    localStorage.setItem('cart', JSON.stringify(storedCart));

    // Tải lại trang để hiển thị thông tin mới
    window.location.reload();
} 
// Hàm chọn sản phẩm để thanh toán
    // Hàm chọn sản phẩm để thanh toán
  
function toggleSelectItem(productName) {
    const storedCart = JSON.parse(localStorage.getItem('cart')) || [];
  
     
    // Tìm sản phẩm trong giỏ hàng và chọn thanh toán nếu được chọn
  
    const selectedProduct = storedCart.find(item => item.name === productName);
    if (selectedProduct) {
        // Nếu sản phẩm đã chọn thì bỏ chọn, ngược lại thì chọn
        selectedProduct.selected = !selectedProduct.selected;
    }
    
    // Cập nhật lại giỏ hàng
    localStorage.setItem('cart', JSON.stringify(storedCart));
       
    // Tính lại tổng tiền sản phẩm đã chọn
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    console.log(cart)
    let totalSelected = 0;
    storedCart.forEach(item => {
        item.price = parseFloat(item.price);
        item.quantity = parseInt(item.quantity);
        
        // Chỉ tính tổng cho các sản phẩm được chọn
        if (item.selected) {
            totalSelected += item.price * item.quantity;
        }
    });

    document.getElementById('cart-total').textContent = totalSelected.toLocaleString() + ',000 VND';
}


</script>

</body>
</html>

<?php
include('../includes/footer.php'); // Bao gồm footer
?>
