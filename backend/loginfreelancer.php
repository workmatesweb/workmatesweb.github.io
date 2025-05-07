<?php
session_start();
require_once 'config.php'; // file koneksi ke DB

// Ambil data dari form
$phone_number = $_POST['phone_number'] ?? '';
$id_number    = $_POST['id_number'] ?? '';

// Cek apakah user ada di database
$sql = "SELECT * FROM freelancers WHERE phone_number = ? AND id_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $phone_number, $id_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Login berhasil
    $user = $result->fetch_assoc();
    $_SESSION['freelancer_id'] = $user['id']; // atau field primary key kamu
    $_SESSION['freelancer_name'] = $user['full_name'];

    echo "<script>alert('Login berhasil!'); window.location.href='../dashboardfreelancer.php';</script>";
} else {
    echo "<script>alert('Login gagal. Cek nomor dan ID Anda!'); window.location.href='../loginfreelancer.php';</script>";
}

$stmt->close();
$conn->close();
?>
