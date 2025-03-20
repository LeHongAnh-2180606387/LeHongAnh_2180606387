<?php
include '../config/config.php';
if (isset($_GET['MaSV'])) {
    $MaSV = $_GET['MaSV'];
    $sql = "DELETE FROM SinhVien WHERE MaSV = '$MaSV'";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>