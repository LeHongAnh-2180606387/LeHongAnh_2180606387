<?php
session_start();
include '../config/config.php';

// Kiểm tra nếu sinh viên đã đăng nhập
if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit();
}

$MaSV = $_SESSION['MaSV']; // Lấy mã sinh viên từ session

// Lấy MaDK của sinh viên
$sqlGetMaDK = "SELECT MaDK FROM dangky WHERE MaSV = ?";
$stmt = $conn->prepare($sqlGetMaDK);
$stmt->bind_param("s", $MaSV);
$stmt->execute();
$result = $stmt->get_result();
$MaDKRow = $result->fetch_assoc();
$MaDK = $MaDKRow['MaDK'] ?? null; // Kiểm tra nếu không có MaDK

// Nếu MaDK tồn tại, xử lý các yêu cầu xóa
if ($MaDK) {
    // Xóa một học phần theo MaHP
    if (isset($_GET['remove'])) {
        $removeMaHP = $_GET['remove'];
        
        // Xóa khỏi chitietdangky trước
        $sqlDeleteCTDK = "DELETE FROM chitietdangky WHERE MaDK = ? AND MaHP = ?";
        $stmt = $conn->prepare($sqlDeleteCTDK);
        $stmt->bind_param("ss", $MaDK, $removeMaHP);
        $stmt->execute();

        // Kiểm tra nếu sinh viên không còn học phần nào, thì xóa luôn MaDK trong dangky
        $sqlCheckCTDK = "SELECT COUNT(*) as count FROM chitietdangky WHERE MaDK = ?";
        $stmt = $conn->prepare($sqlCheckCTDK);
        $stmt->bind_param("s", $MaDK);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            $sqlDeleteDK = "DELETE FROM dangky WHERE MaDK = ?";
            $stmt = $conn->prepare($sqlDeleteDK);
            $stmt->bind_param("s", $MaDK);
            $stmt->execute();
        }

        header("Location: register_cart.php");
        exit();
    }

    // Xóa tất cả học phần đã đăng ký
    if (isset($_GET['clear'])) {
        $sqlClearCTDK = "DELETE FROM chitietdangky WHERE MaDK = ?";
        $stmt = $conn->prepare($sqlClearCTDK);
        $stmt->bind_param("s", $MaDK);
        $stmt->execute();

        $sqlClearDK = "DELETE FROM dangky WHERE MaDK = ?";
        $stmt = $conn->prepare($sqlClearDK);
        $stmt->bind_param("s", $MaDK);
        $stmt->execute();

        header("Location: register_cart.php");
        exit();
    }
}

// Lấy danh sách học phần từ database
$sql = "SELECT hp.MaHP, hp.TenHP, hp.SoTinChi
        FROM hocphan hp
        JOIN chitietdangky ctdk ON hp.MaHP = ctdk.MaHP
        JOIN dangky dk ON dk.MaDK = ctdk.MaDK
        WHERE dk.MaSV = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $MaSV);
$stmt->execute();
$result = $stmt->get_result();
$registered_courses = $result->fetch_all(MYSQLI_ASSOC);

// Tính tổng số tín chỉ
$totalCredits = array_sum(array_column($registered_courses, 'SoTinChi'));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng Kí Học Phần</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Header -->
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <h2 class="font-weight-bold">Đăng Kí Học Phần</h2>

        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>MaHP</th>
                    <th>Tên Học Phần</th>
                    <th>Số Tín Chỉ</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($registered_courses)): ?>
                    <?php foreach ($registered_courses as $course): ?>
                        <tr>
                            <td><?= htmlspecialchars($course['MaHP']) ?></td>
                            <td><?= htmlspecialchars($course['TenHP']) ?></td>
                            <td><?= htmlspecialchars($course['SoTinChi']) ?></td>
                            <td><a href="?remove=<?= urlencode($course['MaHP']) ?>" class="text-danger">Xóa</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Không có học phần nào được đăng ký.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <p class="text-danger font-weight-bold">Số học phần: <?= count($registered_courses) ?></p>
        <p class="text-danger font-weight-bold">Tổng số tín chỉ: <?= $totalCredits ?></p>

        <div class="d-flex justify-content-between">
            <a href="?clear=true" class="text-danger">Xóa Đăng Kí</a>
            <a href="save_registration.php" class="text-primary">Lưu đăng ký</a>
        </div>
    </div>
</body>
</html>