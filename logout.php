<?php
session_start(); // Mulai session

// Hapus semua data session
$_SESSION = [];
session_unset();
session_destroy();

// Redirect ke halaman login atau beranda
header("Location: login.php"); // Ganti dengan halaman tujuan setelah logout
exit;
