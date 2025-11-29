<?php include 'data.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Hoa Tươi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS để ảnh hiển thị đều nhau */
        .flower-img { 
            width: 100%; 
            height: 200px; 
            object-fit: cover; 
            border-bottom: 1px solid #eee;
        }
        /* Hiệu ứng hover cho thẻ card */
        .flower-card:hover { transform: translateY(-5px); transition: 0.3s; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <h1 class="text-center text-primary mb-4">14 loài hoa tuyệt đẹp thích hợp trồng để khoe hương sắc dịp xuân hè</h1>
    
    <div class="text-end mb-4">
        <a href="admin.php" class="btn btn-outline-dark">Đến trang Quản trị &rarr;</a>
    </div>

    <div class="row">
        <?php foreach ($flowers as $flower): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm flower-card border-0">
                    
                    <div class="row g-0">
                        <?php foreach ($flower['images'] as $index => $img): ?>
                            <div class="col-6">
                                <img src="<?php echo $img; ?>" class="flower-img" alt="<?php echo $flower['name']; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title text-success"><?php echo $flower['name']; ?></h4>
                        <p class="card-text text-secondary"><?php echo $flower['description']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>