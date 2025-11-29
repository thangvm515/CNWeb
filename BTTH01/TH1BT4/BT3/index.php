<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tải Lên và Lưu CSV vào CSDL</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; background-color: #f4f4f9; }
        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; }
        form { display: flex; flex-direction: column; gap: 15px; }
        input[type="file"] { padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 15px; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s; }
        button:hover { background-color: #2980b9; }
        .message { margin-top: 15px; padding: 15px; border-radius: 4px; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #fdd; color: #c00; border: 1px solid #c00; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Tải Lên Tệp CSV vào CSDL</h1>

        <?php
        if (isset($_GET['message'])) {
            $msg = $_GET['message'];
            
            $msg_formatted = htmlspecialchars($msg);
            $msg_formatted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $msg_formatted);
            
            $class = isset($_GET['error']) ? 'error' : 'success';
            
            echo "<div class='message $class'>$msg_formatted</div>";
        }
        ?>

        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <label for="csvFile">Chọn tệp tin CSV (.csv):</label>
            <input type="file" name="csvFile" id="csvFile" accept=".csv" required>

            <button type="submit" name="submit">Lưu Dữ Liệu vào CSDL</button>
        </form>
    </div>

</body>
</html>