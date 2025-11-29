<?php
// admin.php - Chỉ hiển thị dữ liệu (Read)
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản trị hệ thống (CSDL)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .thumb-img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; margin-right: 5px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-danger">Quản Trị Danh Sách Hoa (Dữ liệu từ CSDL)</h2>
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-home"></i> Xem trang chủ</a>
    </div>

    <button class="btn btn-success mb-3"><i class="fas fa-plus-circle"></i> Thêm mới (Chưa có chức năng)</button>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th style="width: 5%">ID</th>
                    <th style="width: 20%">Tên Hoa</th>
                    <th style="width: 40%">Mô Tả</th>
                    <th style="width: 20%">Hình Ảnh</th>
                    <th style="width: 15%">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Truy vấn dữ liệu từ CSDL
                $sql = "SELECT id, name, description, main_image_path FROM flowers";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td class="text-center"><?php echo $row['id']; ?></td>
                    <td class="fw-bold text-primary"><?php echo $row['name']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td>
                        <img src="<?php echo $row['main_image_path']; ?>" class="thumb-img" alt="Ảnh hoa">
                    </td>
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm"><i class="fas fa-pen"></i> Sửa</button>
                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Xóa</button>
                    </td>
                </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center">Chưa có dữ liệu. Hãy thêm dữ liệu thủ công vào CSDL.</td></tr>';
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>