<?php
include '../config/config.php';
include '../includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $MaSV = trim($_POST['MaSV']);
    $HoTen = trim($_POST['HoTen']);
    $GioiTinh = trim($_POST['GioiTinh']);
    $NgaySinh = trim($_POST['NgaySinh']);
    $MaNganh = trim($_POST['MaNganh']);

    // Xử lý file ảnh
    // Xử lý file ảnh
    $Hinh = "";
    if (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] == 0) {
        $target_dir = "../assets/images/"; // Thư mục đích

        // Kiểm tra và tạo thư mục nếu chưa tồn tại
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $Hinh = $target_dir . basename($_FILES["Hinh"]["name"]);
        
        if (move_uploaded_file($_FILES["Hinh"]["tmp_name"], $Hinh)) {
            echo "<p style='color:green;'>Upload ảnh thành công!</p>";
        } else {
            echo "<p style='color:red;'>Lỗi: Không thể di chuyển file.</p>";
        }
    }


    // Kiểm tra dữ liệu đầu vào
    if (empty($MaSV) || empty($HoTen) || empty($NgaySinh) || empty($MaNganh)) {
        echo "<p style='color:red;'>Vui lòng nhập đầy đủ thông tin!</p>";
    } else {
        $sql = "INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $MaSV, $HoTen, $GioiTinh, $NgaySinh, $Hinh, $MaNganh);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "<p style='color:red;'>Lỗi: " . $conn->error . "</p>";
        }
    }
}
?>

<div class="container mt-4">
    <h2 class="mb-3">THÊM SINH VIÊN</h2>
    <p class="text-muted">SinhViên</p>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Mã SV</label>
            <input type="text" class="form-control" name="MaSV" required>
        </div>

        <div class="form-group">
            <label>Họ Tên</label>
            <input type="text" class="form-control" name="HoTen" required>
        </div>

        <div class="form-group">
            <label>Giới Tính</label>
            <select class="form-control" name="GioiTinh">
                <option value="Nam">Nam</option>
                <option value="Nữ">Nữ</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ngày Sinh</label>
            <input type="date" class="form-control" name="NgaySinh" required>
        </div>

        <div class="form-group">
            <label>Hình</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" name="Hinh">
                <label class="custom-file-label">Chọn</label>
            </div>
        </div>

        <div class="form-group">
            <label>Ngành Học</label>
            <input type="text" class="form-control" name="MaNganh" required>
        </div>

        <button type="submit" class="btn btn-secondary">Create</button>
    </form>

    <a href="index.php" class="d-block mt-3">Back to List</a>
</div>


<?php include '../includes/footer.php'; ?>
