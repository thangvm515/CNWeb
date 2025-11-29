<?php
require_once 'config.php';

if (!isset($_POST['submit'])) {
    header('Location: index.php?error=1&message=' . urlencode('Vui lòng sử dụng form để tải tệp.'));
    exit;
}

if ($_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    header('Location: index.php?error=1&message=' . urlencode('Lỗi khi tải tệp lên.'));
    exit;
}

$fileTmpName = $_FILES['csvFile']['tmp_name'];
$fileExt = strtolower(pathinfo($_FILES['csvFile']['name'], PATHINFO_EXTENSION));

if ($fileExt !== 'csv') {
    header('Location: index.php?error=1&message=' . urlencode('Chỉ chấp nhận tệp tin định dạng .csv'));
    exit;
}

$totalRows = 0;
$insertedRows = 0;
$skippedRows = 0;
$skippedErrors = [];

$sql = "INSERT IGNORE INTO sinhvien (username, password, lastname, firstname, city, email, course1) VALUES (?, ?, ?, ?, ?, ?, ?)";$stmt = $conn->prepare($sql);

if ($stmt === FALSE) {
    header('Location: index.php?error=1&message=' . urlencode('Lỗi chuẩn bị truy vấn CSDL: ' . $conn->error));
    exit;
}

$stmt->bind_param("sssssss", $username, $password, $lastname, $firstname, $city, $email, $course1);


if (($handle = fopen($fileTmpName, "r")) !== FALSE) {
    $header = fgetcsv($handle, 1000, ",");
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $totalRows++;
        
        if (count($data) !== 7) {
            $skippedRows++;
            $skippedErrors[] = "Dòng $totalRows có số cột không đúng.";
            continue; 
        }
        
        $username  = trim($data[0]);
        $password  = trim($data[1]);
        $lastname  = trim($data[2]);
        $firstname = trim($data[3]);
        $city      = trim($data[4]);
        $email     = trim($data[5]);
        $course1   = trim($data[6]);
        
        if ($stmt->execute()) {
            $insertedRows++;
        } else {
            $skippedRows++;
            if ($conn->errno === 1062) {
                $skippedErrors[] = "Username **$username** đã tồn tại trong CSDL.";
            } else {
                $skippedErrors[] = "Dòng $totalRows (Username: $username) lỗi chèn: " . $stmt->error;
            }
        }
    }
    
    fclose($handle);
    $stmt->close();
    
    $conn->close();
    
    $message = "Hoàn tất! Tổng cộng **$totalRows** dòng được xử lý. Chèn thành công **$insertedRows** dòng.";
    
    if ($skippedRows > 0) {
        $message .= " Bỏ qua **$skippedRows** dòng do lỗi (Chi tiết: " . implode(' | ', array_slice($skippedErrors, 0, 5)) . (count($skippedErrors) > 5 ? ' và các lỗi khác...' : '') . ").";
    }
    
    header('Location: index.php?message=' . urlencode($message));
    exit;

} else {
    $conn->close();
    header('Location: index.php?error=1&message=' . urlencode('Không thể mở tệp tin để đọc.'));
    exit;
}
?>