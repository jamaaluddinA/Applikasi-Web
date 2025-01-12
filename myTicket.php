<?php
require 'koneksi.php';
session_start();

// Cek jika user_id ada dalam session
if (!isset($_SESSION['user_id'])) {
    die('Anda belum login. Harap login terlebih dahulu.');
}

$user_id = $_SESSION['user_id'];

// Query untuk mengambil daftar pembayaran
$query = "SELECT p.payment_id, p.total_payment, p.tipe_payment, p.status_payment, 
          p.tgl_payment, e.event, 
          TIMESTAMPDIFF(HOUR, p.tgl_payment, NOW()) as hours_passed
          FROM payment p
          JOIN event e ON p.event_id = e.event_id
          WHERE p.user_id = ?";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Error dalam menyiapkan query: ' . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2>Detail Pembayaran</h2>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID Pembayaran</th>
                                <th>Event</th>
                                <th>Total Pembayaran</th>
                                <th>Metode Pembayaran</th>
                                <th>Status Pembayaran</th>
                                <th>Tanggal Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($payment = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($payment['payment_id']) ?></td>
                                    <td><?= htmlspecialchars($payment['event']) ?></td>
                                    <td>Rp <?= number_format($payment['total_payment'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($payment['tipe_payment']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($payment['status_payment']) ?>
                                        <?php if ($payment['status_payment'] == 'pending'): ?>
                                            <div class="alert alert-info mt-2 p-1">
                                                Silahkan lakukan pembayaran sesuai metode yang dipilih.
                                                Admin akan memverifikasi pembayaran Anda.
                                            </div>
                                        <?php elseif ($payment['status_payment'] == 'confirmed'): ?>
                                            <div class="alert alert-success mt-2 p-1">
                                                Pembayaran Anda telah berhasil dikonfirmasi oleh admin.
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($payment['tgl_payment']) ?>
                                        <?php if ($payment['hours_passed'] <= 24): ?>
                                            <small class="text-muted d-block">
                                                Sisa waktu pembatalan:
                                                <?= 24 - $payment['hours_passed'] ?> jam
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($payment['status_payment'] == 'pending' && $payment['hours_passed'] <= 24): ?>
                                            <form action="cancel_payment.php" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                                <input type="hidden" name="payment_id"
                                                    value="<?= htmlspecialchars($payment['payment_id']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Batalkan Pesanan
                                                </button>
                                            </form>
                                        <?php elseif ($payment['status_payment'] == 'pending'): ?>
                                            <small class="text-muted">
                                                Batas waktu pembatalan telah berakhir
                                            </small>
                                        <?php elseif ($payment['status_payment'] == 'confirmed'): ?>
                                            <small class="text-success">
                                                Pembayaran telah dikonfirmasi. Tiket Anda sedang diproses.
                                            </small>
                                            <a href="download_ticket.php?payment_id=<?= htmlspecialchars($payment['payment_id']) ?>"
                                                class="btn btn-success btn-sm mt-2">
                                                Unduh Tiket
                                            </a>
                                        <?php endif; ?>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Anda belum melakukan pembayaran untuk tiket apapun.</p>
                <?php endif; ?>
                <a href="Dashboard User.php" class="btn btn-primary">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>

</html>
