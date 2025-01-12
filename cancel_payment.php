<?php
require 'koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Anda belum login. Harap login terlebih dahulu.');
}

if (isset($_POST['payment_id'])) {
    $payment_id = $_POST['payment_id'];
    $user_id = $_SESSION['user_id'];
    
    // Mulai transaksi
    $conn->begin_transaction();
    
    try {
        // Cek apakah pembayaran milik user yang login dan masih dalam 24 jam
        $check_query = "SELECT p.payment_id, p.tiket_id 
                       FROM payment p 
                       WHERE p.payment_id = ? 
                       AND p.user_id = ? 
                       AND p.status_payment = 'pending'
                       AND TIMESTAMPDIFF(HOUR, p.tgl_payment, NOW()) <= 24";
        
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $payment_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update status kursi menjadi kosong
            $update_kursi = "UPDATE nomor_kursi nk 
                            JOIN tiket t ON t.kursi_id = nk.kursi_id 
                            JOIN payment p ON p.tiket_id = t.tiket_id 
                            SET nk.status = 'kosong' 
                            WHERE p.payment_id = ?";
            $stmt_kursi = $conn->prepare($update_kursi);
            $stmt_kursi->bind_param("i", $payment_id);
            $stmt_kursi->execute();

            // Hapus payment
            $delete_payment = "DELETE FROM payment WHERE payment_id = ?";
            $stmt_payment = $conn->prepare($delete_payment);
            $stmt_payment->bind_param("i", $payment_id);
            $stmt_payment->execute();
            
            // Hapus tiket terlebih dahulu
            $delete_tiket = "DELETE t FROM tiket t 
                           JOIN payment p ON p.tiket_id = t.tiket_id 
                           WHERE p.payment_id = ?";
            $stmt_tiket = $conn->prepare($delete_tiket);
            $stmt_tiket->bind_param("i", $payment_id);
            $stmt_tiket->execute();
            
            $conn->commit();
            $_SESSION['message'] = "Pesanan berhasil dibatalkan.";
        } else {
            throw new Exception("Tidak dapat membatalkan pesanan. Pembatalan hanya dapat dilakukan dalam 24 jam pertama.");
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Data tidak lengkap!";
}

header("Location: myTicket.php");
exit();
?>