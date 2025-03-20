<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit();
}

$MaSV = $_SESSION['MaSV'];

// Lấy MaDK (Mã đăng ký) của sinh viên
$sql = "SELECT MaDK FROM dangky WHERE MaSV = '$MaSV'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row) {
    $MaDK = $row['MaDK'];
} else {
    // Nếu sinh viên chưa có MaDK, tạo mới
    $sql_insert = "INSERT INTO dangky (NgayDK, MaSV) VALUES (NOW(), '$MaSV')";
    $conn->query($sql_insert);
    $MaDK = $conn->insert_id;
}

// Danh sách học phần có thể đăng ký
$sql_courses = "SELECT * FROM hocphan";
$result_courses = $conn->query($sql_courses);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký Học Phần</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="text-center">DANH SÁCH HỌC PHẦN</h2>
        <p>Sinh viên: <strong><?php echo $MaSV; ?></strong></p>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Mã Học Phần</th>
                    <th>Tên Học Phần</th>
                    <th>Số Tín Chỉ</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_courses->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['MaHP']; ?></td>
                        <td><?php echo $row['TenHP']; ?></td>
                        <td><?php echo $row['SoTinChi']; ?></td>
                        <td>
                            <a href="register_action.php?MaHP=<?php echo $row['MaHP']; ?>" class="btn btn-success btn-sm">Đăng Ký</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="logout.php" class="btn btn-danger">Đăng Xuất</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
