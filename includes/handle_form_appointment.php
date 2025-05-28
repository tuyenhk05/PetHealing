<?php
include('db_connect.php'); // Kết nối cơ sở dữ liệu

// Kiểm tra nếu có dữ liệu gửi qua phương thức POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $full_name = $_POST['full-name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $pet_name = $_POST['pet-name'];
    $pet_type = $_POST['pet-type'];
    $appointment_date = $_POST['appointment-date'];
    $appointment_time = $_POST['appointment-time'];
    $service = $_POST['service'];
    $notes = $_POST['notes'];

    // Kiểm tra dữ liệu form (ví dụ: đảm bảo các trường bắt buộc đã được điền đầy đủ)
    if (empty($full_name) || empty($phone) || empty($email) || empty($pet_name) || empty($pet_type) || empty($appointment_date) || empty($appointment_time) || empty($service)) {
        echo "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Nếu không có lỗi, tiến hành lưu dữ liệu vào cơ sở dữ liệu

        // Sử dụng prepared statement để tránh SQL Injection
        $stmt = $conn->prepare("INSERT INTO lichhen (ten_khach_hang, so_dien_thoai_khach_hang, email_khach_hang, ten_thu_cung, loai_thu_cung, ten_dich_vu, ngay_hen, gio_hen, ghi_chu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssssssss", $full_name, $phone, $email, $pet_name, $pet_type, $service, $appointment_date, $appointment_time, $notes);
            if ($stmt->execute()) {
                echo "Đặt lịch hẹn thành công!";
            } else {
                echo "Có lỗi xảy ra khi lưu dữ liệu: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Có lỗi xảy ra khi chuẩn bị truy vấn: " . $conn->error;
        }
        // Đóng kết nối cơ sở dữ liệu
         
    }
}
?>
