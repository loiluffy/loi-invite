<?php
session_start();
include 'db_connect.php';

// Kiểm tra nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dang_nhap.php");
    exit();
}

// Lấy danh mục sản phẩm
$categories = ["Điện thoại", "Tablet", "Laptop"];

// Lấy danh mục từ URL (nếu có)
$category = isset($_GET['category']) ? $_GET['category'] : "Tất cả";

// Truy vấn sản phẩm theo danh mục
$sql = "SELECT * FROM products";
if ($category !== "Tất cả") {
    $sql .= " WHERE danh_muc = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
} else {
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Chủ - Bán Điện Thoại</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<!-- Thanh menu -->
<nav class="navbar navbar-expand-lg navbar-dark bg-warning">
    <div class="container">
        <a class="navbar-brand" href="index.php">📱 SHOP SLT</a>

        <!-- Nút điều hướng bên phải -->
        <div class="d-flex">
            <a href="gio_hang.php" class="btn btn-dark me-2">🛒 Giỏ Hàng</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="navbar-text me-3">👤 Xin chào, <strong><?= $_SESSION['user_name']; ?></strong></span>
                <a href="doi_tai_khoan.php" class="btn btn-warning me-2">🔄 Admin</a>
                <a href="dang_xuat.php" class="btn btn-danger">🚪 Đăng xuất</a>
            <?php else: ?>
                <a href="dang_nhap.php" class="btn btn-primary">🔑 Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Nội dung trang -->
<div class="container mt-4">
    <div class="row">
        <!-- Danh mục -->
        <div class="col-md-3">
            <h4>Danh Mục</h4>
            <ul class="list-group">
                <li class="list-group-item <?= ($category == "Tất cả") ? 'active' : '' ?>">
                    <a href="index.php" class="text-decoration-none text-dark">Tất cả</a>
                </li>
                <?php foreach ($categories as $cat): ?>
                    <li class="list-group-item <?= ($category == $cat) ? 'active' : '' ?>">
                        <a href="index.php?category=<?= urlencode($cat) ?>" class="text-decoration-none text-dark"><?= $cat ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Hiển thị sản phẩm -->
        <div class="col-md-9">
            <h3>Sản Phẩm - <?= htmlspecialchars($category) ?></h3>
            <div class="row">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <img src="images/<?= $row['hinh_anh'] ?>" class="card-img-top" alt="<?= $row['ten_san_pham'] ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $row['ten_san_pham'] ?></h5>
                                    <p class="card-text"><strong class="text-danger"><?= number_format($row['gia'], 0, ',', '.') ?>đ</strong></p>
                                    <a href="chi_tiet.php?id=<?= $row['id'] ?>" class="btn btn-primary">Xem chi tiết</a>
                                    <a href="them_gio_hang.php?id=<?= $row['id'] ?>" class="btn btn-success">🛒 Thêm vào giỏ</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">Không có sản phẩm nào trong danh mục này.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
