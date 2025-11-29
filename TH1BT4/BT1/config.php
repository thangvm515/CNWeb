<?php
// config.php

// Thông tin kết nối CSDL
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Tên người dùng mặc định của XAMPP
define('DB_PASSWORD', '');     // Mật khẩu mặc định của XAMPP (thường là rỗng)
define('DB_NAME', 'quanlyhoa'); // Tên CSDL bạn vừa tạo

// Kết nối đến CSDL
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

// Thiết lập mã hóa UTF8 để hiển thị tiếng Việt
$conn->set_charset("utf8mb4");

// Không cần đóng $conn ở đây, nó sẽ được sử dụng trong các file khác.
?>