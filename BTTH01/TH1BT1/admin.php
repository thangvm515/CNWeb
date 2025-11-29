<?php include 'data.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .thumb-img { 
            width: 60px; 
            height: 60px; 
            object-fit: cover; 
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-danger">Quản trị danh sách hoa</h2>
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-home"></i> Xem trang chủ</a>
    </div>

    <button class="btn btn-success mb-3"><i class="fas fa-plus-circle"></i> Thêm mới</button>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 20%">Tên Hoa</th>
                    <th style="width: 40%">Mô Tả</th>
                    <th style="width: 20%">Hình Ảnh</th>
                    <th style="width: 15%">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                foreach ($flowers as $flower): 
                ?>
                <tr>
                    <td class="text-center"><?php echo $i++; ?></td>
                    <td class="fw-bold text-primary"><?php echo $flower['name']; ?></td>
                    <td><?php echo $flower['description']; ?></td>
                    <td>
                        <?php foreach ($flower['images'] as $img): ?>
                            <img src="<?php echo $img; ?>" class="thumb-img" alt="thumb">
                        <?php endforeach; ?>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></button>
                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>