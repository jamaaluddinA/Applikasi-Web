<?php
require 'koneksi.php';
session_start();

// Cek jika admin sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login_admin.php");
    exit();
}
$username = $_SESSION['username'];

if(isset($_POST['payment_id']) && isset($_POST['status_payment'])) {
    $payment_id = $_POST['payment_id'];
    $status_payment = $_POST['status_payment'];
    
    // Update status pembayaran
    $query = "UPDATE payment SET status_payment = ? WHERE payment_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status_payment, $payment_id);
    
    if($stmt->execute()) {
        // Redirect kembali ke dashboard dengan pesan sukses
        $_SESSION['message'] = "Status pembayaran berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate status pembayaran!";
    }
    
    header("Location: Dashboard Admin.php");
    exit();
} else {
    $_SESSION['error'] = "Data tidak lengkap!";
    header("Location: Dashboard Admin.php");
    exit();
}
?>