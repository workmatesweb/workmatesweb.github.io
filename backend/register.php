<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek email sudah digunakan
    $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Email sudah digunakan!');</script>";
        echo "<script>window.location.href='../login.php';</script>";
    } else {
        $insert = $conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')");
        if ($insert) {
            echo "<script>alert('Registration successful!'); window.location.href='../login.php';</script>";
        } else {
            echo "<script>alert('gagal mendaftar!');</script>" . $conn->error;
            echo "<script>window.location.href='../login.php';</script>";
        }
    }
}
?>
