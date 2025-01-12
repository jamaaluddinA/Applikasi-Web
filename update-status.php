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
    
    // Mulai transaksi
    $conn->begin_transaction();
    
    try {
        // Update status pembayaran
        $query = "UPDATE payment SET status_payment = ?, username = ? WHERE payment_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $status_payment, $username, $payment_id);
        $stmt->execute();
        
        // Jika status payment confirmed, update status kursi menjadi penuh
        if($status_payment === 'confirmed') {
            $query_kursi = "UPDATE nomor_kursi nk 
                           JOIN tiket t ON t.kursi_id = nk.kursi_id 
                           JOIN payment p ON p.tiket_id = t.tiket_id 
                           SET nk.status = 'penuh' 
                           WHERE p.payment_id = ?";
            $stmt_kursi = $conn->prepare($query_kursi);
            $stmt_kursi->bind_param("i", $payment_id);
            $stmt_kursi->execute();
        }
        
        // Commit transaksi
        $conn->commit();
        $_SESSION['message'] = "Status pembayaran dan kursi berhasil diupdate!";
        
    } catch (Exception $e) {
        // Rollback jika terjadi error
        $conn->rollback();
        $_SESSION['error'] = "Gagal mengupdate status pembayaran dan kursi!";
    }
    
    header("Location: Dashboard Admin.php");
    exit();
    
} else {
    $_SESSION['error'] = "Data tidak lengkap!";
    header("Location: Dashboard Admin.php");
    exit();
}
?>
