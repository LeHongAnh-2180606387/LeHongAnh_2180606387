<?php
include '../config/config.php';
include '../includes/header.php';

if (isset($_GET['MaSV'])) {
    $MaSV = $_GET['MaSV'];
    $sql = "SELECT SinhVien.*, NganhHoc.TenNganh FROM SinhVien 
            LEFT JOIN NganhHoc ON SinhVien.MaNganh = NganhHoc.MaNganh
            WHERE MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $MaSV);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "<p style='color:red;'>Không tìm thấy sinh viên!</p>";
        exit();
    }
} else {
    echo "<p style='color:red;'>Mã sinh viên không hợp lệ!</p>";
    exit();
}
?>

<div class="container">
    <h2>Chi tiết sinh viên</h2>
    <p><strong>Mã SV:</strong> <?= htmlspecialchars($row['MaSV']) ?></p>
    <p><strong>Họ Tên:</strong> <?= htmlspecialchars($row['HoTen']) ?></p>
    <p><strong>Giới Tính:</strong> <?= htmlspecialchars($row['GioiTinh']) ?></p>
    <p><strong>Ngày Sinh:</strong> <?= htmlspecialchars($row['NgaySinh']) ?></p>
    <p><strong>Ngành Học:</strong> <?= htmlspecialchars($row['TenNganh']) ?></p>
    <p><strong>Hình:</strong> 
        <?php if (!empty($row['Hinh'])): ?>
            <img src="<?= htmlspecialchars($row['Hinh']) ?>" alt="Ảnh sinh viên" width="150">
        <?php else: ?>
            Không có ảnh
        <?php endif; ?>
    </p>
    <a href="index.php">Quay lại danh sách</a>
</div>

