<?php
// Kết nối cơ sở dữ liệu
include('db_connect.php'); 

// Kiểm tra nếu form đã được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Kiểm tra nếu các trường không trống
    if (empty($name) || empty($phone) || empty($email) || empty($subject) || empty($message)) {
        echo "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Nếu không có lỗi, tiến hành lưu dữ liệu vào cơ sở dữ liệu

        // Sử dụng prepared statement để tránh SQL Injection
        $stmt = $conn->prepare("INSERT INTO contact (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            // Liên kết các tham số với prepared statement
            $stmt->bind_param("sssss", $name, $phone, $email, $subject, $message);
            
            // Thực thi câu lệnh SQL
            if ($stmt->execute()) {
                echo "Cảm ơn bạn đã liên hệ! Chúng tôi sẽ trả lời bạn sớm nhất.";
            } else {
                echo "Có lỗi xảy ra khi lưu dữ liệu: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Có lỗi xảy ra khi chuẩn bị truy vấn: " . $conn->error;
        }
    }
}
?>
