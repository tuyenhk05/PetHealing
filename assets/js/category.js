document.addEventListener("DOMContentLoaded", function () {
    const searchForm = document.getElementById("searchForm");
    const searchInput = document.getElementById("searchInput");
    const productContainer = document.querySelector(".product");
    const productCards = Array.from(productContainer.querySelectorAll(".card"));
    const paginationContainer = document.querySelector(".pagination");
    const categoryList = document.querySelectorAll(".category-list li");

    const itemsPerPage = 6;
    let currentPage = 1;
    let currentCategory = "all";

    // Lọc sản phẩm theo từ khóa và danh mục
    function filterProducts() {
        const keyword = searchInput.value.trim().toLowerCase();

        return productCards.filter(card => {
            const name = card.querySelector("h3").textContent.toLowerCase();
            const description = card.querySelector("p").textContent.toLowerCase();
            const category = card.getAttribute("data-category");

            const matchKeyword = name.includes(keyword) || description.includes(keyword);
            const matchCategory = currentCategory === "all" || category === currentCategory;

            return matchKeyword && matchCategory;
        });
    }

    // Hiển thị sản phẩm theo trang
    function displayProducts(page) {
        const filtered = filterProducts();
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        productCards.forEach(card => card.style.display = "none");

        filtered.slice(start, end).forEach(card => {
            card.style.display = "block";
        });

        updatePagination(filtered.length);
    }

    // Cập nhật nút phân trang
    function updatePagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        paginationContainer.innerHTML = "";

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            btn.className = (i === currentPage) ? "active" : "";
            btn.addEventListener("click", () => {
                currentPage = i;
                displayProducts(currentPage);
            });
            paginationContainer.appendChild(btn);
        }
        
    }

    // Bắt sự kiện lọc theo danh mục
   categoryList.forEach(item => {
    item.addEventListener("click", function () {
        currentCategory = this.getAttribute("data-category");
        currentPage = 1;

        // Nếu chọn lại "Tất cả" → xóa từ khóa tìm kiếm
        if (currentCategory === "all") {
            searchInput.value = "";
        }

        displayProducts(currentPage);
    });
});


    // Bắt sự kiện tìm kiếm
    searchForm.addEventListener("submit", function (e) {
        e.preventDefault();
        currentPage = 1;
        displayProducts(currentPage);
    });

    
    // Hiển thị mặc định khi tải trang
    displayProducts(currentPage);
});
