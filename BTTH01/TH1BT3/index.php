<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tải và xem CSV (PHP)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; background-color: #f4f4f9; }
        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; }
        form { display: flex; flex-direction: column; gap: 15px; }
        input[type="file"] { padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 15px; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s; }
        button:hover { background-color: #2980b9; }
        .message { margin-top: 15px; padding: 10px; border-radius: 4px; }
        .error { background-color: #fdd; color: #c00; border: 1px solid #c00; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Tải lên tệp CSV</h1>

        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <label for="csvFile">Chọn tệp tin CSV (.csv):</label>
            <input type="file" name="csvFile" id="csvFile" accept=".csv" required>

            <button type="submit" name="submit">Xem nội dung CSV</button>
        </form>

        <?php
        // Xử lý thông báo lỗi hoặc thành công
        if (isset($_GET['message'])) {
            $msg = htmlspecialchars($_GET['message']);
            $class = isset($_GET['error']) ? 'error' : '';
            echo "<div class='message $class'>$msg</div>";
        }
        ?>
    </div>

</body>
</html>