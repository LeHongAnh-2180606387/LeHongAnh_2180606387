<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['MaSV'])) {
    header("Location: login.php");
    exit();
}

$MaSV = $_SESSION['MaSV'];
$MaHP = $_GET['MaHP'];

// Lấy MaDK của sinh viên
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

// Chèn học phần vào bảng chitietdangky (không giới hạn tín chỉ)
$sql_insert = "INSERT INTO chitietdangky (MaDK, MaHP) VALUES ('$MaDK', '$MaHP')";
$conn->query($sql_insert);

header("Location: register_course.php");
exit();
?>
