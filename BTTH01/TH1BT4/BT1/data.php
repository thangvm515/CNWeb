<?php
// index.php

// 1. Nhúng file kết nối CSDL
require_once 'config.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Shop Hoa Tươi (Động từ CSDL)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .flower-img { width: 100%; height: 250px; object-fit: cover; }
        /* ... (Các style khác) ... */
    </style>
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <h1 class="text-center text-primary mb-4">Danh Sách Các Loài Hoa Đẹp (Dữ liệu Động)</h1>
    
    <div class="text-end mb-4">
        <a href="admin.php" class="btn btn-outline-dark">Đến trang Quản trị (CRUD) &rarr;</a>
    </div>

    <div class="row">
        <?php
        // 2. Truy vấn dữ liệu từ CSDL
        $sql = "SELECT id, name, description, main_image_path FROM flowers ORDER BY id DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // 3. Lặp qua các dòng dữ liệu nhận được
            while($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4 mb-4">';
                echo '<div class="card h-100 shadow-sm border-0">';
                // Hiển thị ảnh (Giả định ảnh nằm trong thư mục 'images/')
                echo '<img src="' . $row["main_image_path"] . '" class="card-img-top flower-img" alt="' . $row["name"] . '">';
                echo '<div class="card-body">';
                echo '<h4 class="card-title text-success">' . $row["name"] . '</h4>';
                echo '<p class="card-text text-secondary">' . $row["description"] . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p class="text-center">Chưa có dữ liệu hoa nào được thêm vào CSDL.</p>';
        }
        
        // 4. Đóng kết nối
        $conn->close();
        ?>
    </div>
</div>

</body>
</html>