<?php
include('../includes/db_connect.php');  
include('../includes/header.php'); 
$querybs = 'SELECT * FROM dichvu';
$result = mysqli_query($conn, $querybs);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Dịch vụ - PetHealing</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="../assets/css/services.css">
 <link rel="stylesheet" href="../assets/css/style.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>
<body>
<section class="services-section">
  <div class="intro-card-bg">
    <div class="intro-content-overlay">
      <h2>
  CHĂM SÓC <span class="highlight-pink">THÚ CƯNG</span> TOÀN DIỆN TẠI <span class="highlight-blue">PetHealing</span>
</h2>
      <p>Chúng tôi cung cấp các dịch vụ y tế và làm đẹp chuyên nghiệp, giúp thú cưng của bạn luôn khỏe mạnh và xinh đẹp. Đội ngũ bác sĩ tận tâm, trang thiết bị hiện đại, cam kết mang đến sự hài lòng tuyệt đối.</p>
    </div>
  </div>
  <div class="service-row">
    <div class="service-card">
      <img src="../assets/image/Tổng quát.jpg" alt="Khám tổng quát">
      <div class="service-content">
        <h3>Khám tổng quát</h3>
        <p>Kiểm tra toàn diện sức khỏe thú cưng: mắt, tai, răng, tim, phổi...</p>
        <div class="service-price">200.000đ</div>
      </div>
    </div>
    <div class="service-card">
      <img src="../assets/image/Tiêm phòng.jpg" alt="Tiêm chủng">
      <div class="service-content">
        <h3>Tiêm chủng</h3>
        <p>Cung cấp vắc-xin phòng bệnh định kỳ theo lứa tuổi thú cưng.</p>
        <div class="service-price">150.000đ</div>
      </div>
    </div>
  </div>

  <div class="service-row">
    <div class="service-card">
      <img src="../assets/image/Phẫu thuật.jpg" alt="Phẫu thuật">
      <div class="service-content">
        <h3>Phẫu thuật</h3>
        <p>Thực hiện các ca phẫu thuật an toàn: triệt sản, lấy dị vật, mổ u,...</p>
        <div class="service-price">Từ 800.000 - 1.000.000đ</div>
      </div>
    </div>
    <div class="service-card">
      <img src="..\assets\image\Tỉa lông.jpg" alt="Cắt tỉa lông">
      <div class="service-content">
        <h3>Cắt tỉa lông</h3>
        <p>Dịch vụ làm đẹp, tắm, vệ sinh và tạo kiểu lông chuyên nghiệp.</p>
        <div class="service-price">100.000 - 250.000đ</div>
      </div>
    </div>
  </div>

  <div class="service-row">
    <div class="service-card">
      <img src="../assets/image/Răng miệng.jpg" alt="Chăm sóc răng miệng">
      <div class="service-content">
        <h3>Chăm sóc răng miệng</h3>
        <p>Vệ sinh, điều trị nha chu, loại bỏ cao răng, hơi thở thơm tho.</p>
        <div class="service-price">200.000 - 300.000đ</div>
      </div>
    </div>
    <div class="service-card">
      <img src="../assets/image/Khoa Nội.jpg" alt="Khoa nội">
      <div class="service-content">
        <h3>Khoa nội</h3>
        <p>Điều trị các bệnh lý nội khoa: tiêu hóa, hô hấp, gan, thận...</p>
        <div class="service-price">Theo chẩn đoán</div>
      </div>
    </div>
  </div>

  <div class="service-row">
    <div class="service-card">
      <img src="../assets/image/Khoa Ngoại.jpg" alt="Khoa ngoại">
      <div class="service-content">
        <h3>Khoa ngoại</h3>
        <p>Khám & điều trị bệnh ngoài da, khớp, vết thương hở,...</p>
        <div class="service-price">Tùy ca</div>
      </div>
    </div>
    <div class="service-card">
      <img src="..\assets\image\cấp cứu.jpg" alt="Cấp cứu 24/7">
      <div class="service-content">
        <h3>Cấp cứu 24/7</h3>
        <p>Tiếp nhận & xử lý nhanh các ca cấp cứu với đội ngũ túc trực.</p>
        <div class="service-price">Theo tình trạng</div>
      </div>
    </div>
  </div>
  <div class="service-row">
  <div class="service-card">
    <img src="../assets/image/pethotel.jpg" alt="Kí túc xá thú cưng">
    <div class="service-content">
      <h3>Kí túc xá thú cưng</h3>
      <p>Dịch vụ chăm sóc và lưu trú an toàn, tiện nghi cho thú cưng khi bạn vắng nhà.</p>
      <div class="service-price">300.000đ/ngày</div>
    </div>
  </div>

  <div class="service-card">
    <img src="../assets/image/spa.jpg" alt="Spa và làm đẹp">
    <div class="service-content">
      <h3>Spa và làm đẹp</h3>
      <p>Dịch vụ chăm sóc, làm đẹp chuyên nghiệp giúp thú cưng luôn khỏe mạnh và xinh đẹp.</p>
      <div class="service-price">150.000 - 350.000đ</div>
    </div>
  </div>
</div>
<section class="full-width-card">
  <div class="container">
    <div class="content-left">
      <h2>Tại sao chọn dịch vụ của chúng tôi?</h2>
      <p>Chúng tôi tự hào cung cấp dịch vụ chăm sóc thú cưng chất lượng cao với đội ngũ bác sĩ tận tâm và thiết bị hiện đại.</p>

      <ul class="features-list">
        <li>
          <span class="icon">
            <!-- Icon Heart -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21l-7.682-7.682a4.5 4.5 0 010-6.364z"/></svg>
          </span>
          <strong>Đội ngũ giàu kinh nghiệm</strong>
          <p>Bác sĩ chuyên nghiệp, tận tâm.</p>
        </li>
        <li>
          <span class="icon">
            <!-- Icon Device -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L6 13.5m0 0L2.25 17m3.75-3.5v9M12 4.5v15m0 0L15.75 13.5m0 0L19.5 17m-3.75-3.5v9"/></svg>
          </span>
          <strong>Thiết bị hiện đại</strong>
          <p>Chẩn đoán và điều trị chính xác.</p>
        </li>
        <li>
          <span class="icon">
            <!-- Icon Clock -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </span>
          <strong>Hỗ trợ 24/7</strong>
          <p>Sẵn sàng phục vụ mọi lúc.</p>
        </li>
        <li>
          <span class="icon">
            <!-- Icon Chat -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.95L3 20l1.161-3.243A7.97 7.97 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
          </span>
          <strong>Tư vấn chuyên sâu</strong>
          <p>Hỗ trợ chăm sóc và phòng bệnh.</p>
        </li>
      </ul>

      <div class="bottom-card">
        <div class="icon">🐾</div>
      </div>
    </div>

    <div class="image-right">
      <img src="../assets/image/thucungvuive.jpg" alt="Thú cưng vui vẻ" />
    </div>
  </div>
</section>
</body> 
</html> <script src="../assets/js/formHome.js"></script> <!-- Liên kết với file app.js -->
<?php include('../includes/footer.php'); ?>
