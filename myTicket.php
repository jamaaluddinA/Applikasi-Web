<?php
require 'koneksi.php';
session_start();
// Ambil payment_id 
$payment_id = $_SESSION['payment_id'];

// Query untuk mengambil data pembayaran
$query = "SELECT p.*, u.nama_lengkap, e.event
          FROM payment p 
          JOIN user u ON p.user_id = u.user_id 
          JOIN event e ON p.event_id = e.event_id 
          WHERE p.payment_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Detail Pembayaran</h2>
            </div>
            <div class="card-body">
                <p>ID Pembayaran: <?= $payment_id ?></p>
                <p>Event: <?= htmlspecialchars($payment['event']) ?></p>
                <p>Total Pembayaran: Rp <?= number_format($payment['total_payment'], 0, ',', '.') ?></p>
                <p>Metode Pembayaran: <?= htmlspecialchars($payment['tipe_payment']) ?></p>
                <p>Status: <?= htmlspecialchars($payment['status_payment']) ?></p>
                <p>Tanggal: <?= htmlspecialchars($payment['tgl_payment']) ?></p>
                
                <?php if($payment['status_payment'] == 'pending'): ?>
                    <div class="alert alert-info">
                        Silahkan lakukan pembayaran sesuai metode yang dipilih.
                        Setelah melakukan pembayaran, admin akan memverifikasi dan mengubah status pembayaran Anda.
                    </div>
                <?php endif; ?>
                
                <a href="Dashboard User.php" class="btn btn-primary">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>