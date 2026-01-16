<!-- 
    Hộp thoại xác nhận (Confirmation Modal) dùng chung cho hệ thống PetHealing 
    Sử dụng Bootstrap 5 và FontAwesome 6
-->
<div class="modal fade" id="petConfirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-light rounded-top-4 py-3">
                <h5 class="modal-title fw-bold text-dark" id="confirmModalLabel">
                    <i class="fa-solid fa-circle-question text-warning me-2"></i> Xác nhận thao tác
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 px-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-opacity-10 bg-warning p-3 rounded-circle me-3">
                        <i class="fa-solid fa-triangle-exclamation text-warning fs-4"></i>
                    </div>
                    <div>
                        <p id="confirmMessage" class="mb-0 text-secondary fw-medium">Bạn có chắc chắn muốn thực hiện hành động này không?</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4 py-3">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                <a id="confirmBtn" href="#" class="btn rounded-pill px-4 shadow-sm" style="background-color: #2EB292; color: white;">
                    Đồng ý !
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Hàm triệu hồi hộp thoại xác nhận (Show Confirmation Modal)
     * @param {string} message - Lời nhắn muốn hiển thị cho quan khách
     * @param {string} url - Đường dẫn sẽ thực thi khi nhấn "Đồng ý"
     * @param {string} type - Loại hành động (delete, logout, etc.) để thay đổi màu nút nếu cần
     */
    function showConfirmModal(message, url, type = 'primary') {
        const modal = new bootstrap.Modal(document.getElementById('petConfirmModal'));
        const msgElement = document.getElementById('confirmMessage');
        const btnElement = document.getElementById('confirmBtn');

        // Cập nhật nội dung lời nhắn
        msgElement.innerText = message;
        
        // Cập nhật đường dẫn thực thi
        btnElement.setAttribute('href', url);

        // Chỉnh sửa màu sắc nút dựa trên loại hành động
        if (type === 'danger') {
            btnElement.style.backgroundColor = '#dc3545'; // Màu đỏ cho hành động xóa
        } else {
            btnElement.style.backgroundColor = '#2EB292'; // Màu xanh PetHealing mặc định
        }

        modal.show();
    }
</script>

<style>
    /* Gia cố thêm CSS để Modal trông "xịn" hơn rứa mô */
    #petConfirmModal .modal-content {
        animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #confirmBtn:hover {
        filter: brightness(90%);
        transform: translateY(-1px);
        transition: 0.2s;
    }
</style>