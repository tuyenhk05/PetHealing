<?php

$doctors = array(
    array(
        "name" => "BS. Bùi Vũ Hải Phong",
        "role" => "Bác sĩ thú y",
        "img" => "doctor1.jpg",
        "id" => 1
    ),
    array(
        "name" => "BS. Lê Hoài Vũ",
        "role" => "Chuyên gia dinh dưỡng",
        "img" => "doctor2.jpg",
        "id" => 2
    ),
    array(
        "name" => "BS. Huỳnh Kim Tuyền",
        "role" => "Phẫu thuật viên",
        "img" => "doctor3.jpg",
        "id" => 3
    ),
    array(
        "name" => "BS. Lâm Thị D",
        "role" => "Chẩn đoán hình ảnh",
        "img" => "doctor4.jpg",
        "id" => 4
    )
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đội ngũ bác sĩ</title>
    <style>
        body {
            font-family: Arial, sans-serif; 
            background: #F3F4F6; 
            margin: 0;
        }
        .doctors-title {
            text-align: center; 
            font-size: 2em; 
            color: #EB4798; 
            margin-top: 25px;
        }
        .doctors-list { 
            display: flex; 
            flex-wrap: wrap; 
            justify-content: center; 
            gap: 24px; 
        }
        .doctor-card {
            width: 240px; 
            background: #fff; 
            border-radius: 14px;
            box-shadow: 0 2px 10px #ccc; 
            text-align: center; 
            margin-bottom: 28px; 
            border: 2px solid #34C9A5;
            transition: box-shadow 0.2s;
        }
        .doctor-card:hover {
            box-shadow: 0 6px 18px #EB479844;
        }
        .doctor-img { 
            width: 100%; 
            height: 180px; 
            object-fit: cover; 
            border-top-left-radius:14px; 
            border-top-right-radius:14px;
        }
        .doctor-name { 
            font-weight: bold; 
            font-size: 1.1em; 
            margin: 10px 0 5px;
        }
        .doctor-role { 
            color: #34C9A5; 
            margin-bottom: 12px; 
            font-size: 0.96em;
        }
        .btn-view { 
            background: #EB4798; 
            color:#fff; 
            border:none; 
            padding: 8px 22px; 
            border-radius: 30px; 
            cursor:pointer; 
            margin-bottom: 14px;
            font-size: 1em;
            transition: background 0.18s;
        }
        .btn-view:hover {
            background: #34C9A5;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="doctors-title">Đội ngũ bác sĩ</div>
    <div class="doctors-list">
        <?php
        foreach($doctors as $bs) {
            echo '<div class="doctor-card">';
            echo '<img src="assets/image/' . $bs["img"] . '" alt="'.$bs["name"].'" class="doctor-img">';
            echo '<div class="doctor-name">' . $bs["name"] . '</div>';
            echo '<div class="doctor-role">' . $bs["role"] . '</div>';
            echo '<form method="get" action="doctor_detail.php" style="margin-bottom:0;">';
            echo '<input type="hidden" name="id" value="'.$bs["id"].'">';
            echo '<button type="submit" class="btn-view">Xem thông tin</button>';
            echo '</form>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
