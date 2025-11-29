<?php
if (!isset($_POST['submit'])) {
    header('Location: index.php?error=1&message=' . urlencode('Vui lòng sử dụng form để tải tệp.'));
    exit;
}

if ($_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    header('Location: index.php?error=1&message=' . urlencode('Lỗi khi tải tệp lên. Mã lỗi: ' . $_FILES['csvFile']['error']));
    exit;
}

$file = $_FILES['csvFile'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if ($fileExt !== 'csv') {
    header('Location: index.php?error=1&message=' . urlencode('Chỉ chấp nhận tệp tin định dạng .csv'));
    exit;
}

$handle = fopen($fileTmpName, "r");

if ($handle === FALSE) {
    header('Location: index.php?error=1&message=' . urlencode('Không thể mở tệp tin.'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả đọc CSV</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f9; color: #333; }
        h1 { color: #2c3e50; text-align: center; }
        .csv-table-container { margin-top: 20px; overflow-x: auto; background-color: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .csv-table { width: 100%; border-collapse: collapse; margin: 0; }
        .csv-table th, .csv-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            word-break: break-all; 
        }
        .csv-table th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .csv-table tr:nth-child(even) { background-color: #f9f9f9; }
        .csv-table tr:hover { background-color: #f1f1f1; }
        .back-link { display: block; margin-bottom: 20px; text-decoration: none; color: #3498db; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <h1>Nội dung tệp tin CSV</h1>
    <a href="index.php" class="back-link">← Quay lại trang tải tệp</a>

    <div class="csv-table-container">
        <table class="csv-table">
            <?php
            $rowNum = 0;
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                
                if ($rowNum === 0) {
                    echo '<thead><tr>';
                    foreach ($data as $cell) {
                        echo '<th>' . htmlspecialchars($cell) . '</th>';
                    }
                    echo '</tr></thead><tbody>';
                } else {
                    echo '<tr>';
                    foreach ($data as $cell) {
                        echo '<td>' . htmlspecialchars($cell) . '</td>';
                    }
                    echo '</tr>';
                }
                $rowNum++;
            }

            fclose($handle);
            ?>
        </tbody>
        </table>

        <?php
        if ($rowNum === 0) {
            echo '<p>Tệp CSV trống hoặc không hợp lệ.</p>';
        }
        ?>
    </div>

</body>
</html>