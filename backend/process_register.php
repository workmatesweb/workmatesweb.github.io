<?php
require_once 'config.php'; // Include file config.php untuk koneksi database

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Ambil data dari form
$full_name     = $_POST['full_name'];
$phone_number  = $_POST['phone_number'];
$id_number     = $_POST['id_number'];
$birth_date    = $_POST['birth_date'];
$region        = $_POST['region'];
$address       = $_POST['address'];
$postal_code   = $_POST['postal_code'];
$bank_account  = $_POST['bank_account'];

// Simpan ke database
$sql = "INSERT INTO freelancers (full_name, phone_number, id_number, birth_date, region, address, postal_code, bank_account)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $full_name, $phone_number, $id_number, $birth_date, $region, $address, $postal_code, $bank_account);

if ($stmt->execute()) {
    echo "<script>alert('Registration successful!'); window.location.href='loginfreelancer.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
