</ Kết nối cơ sở dữ liệu-->


    <?php
    // Kết nối với cơ sở dữ liệu bằng MySQLi
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "benhvienthucung";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    ?>
