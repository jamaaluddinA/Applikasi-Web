<?php 
require 'koneksi.php';
session_start();

// Cek jika admin sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login_admin.php");
    exit();
}
$username = $_SESSION['username'];

// Query untuk mengambil semua data payment beserta username dan event
$query = "SELECT p.*, u.nama_lengkap, e.event 
          FROM payment p 
          LEFT JOIN user u ON p.user_id = u.user_id 
          LEFT JOIN event e ON p.event_id = e.event_id 
          ORDER BY p.tgl_payment DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TONGKonser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">AdminPanel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout_admin.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Admin Dashboard</h1>
        <p>Data Pembayaran Tiket:</p>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Payment ID</th>
                    <th>Username</th>
                    <th>Event</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['payment_id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['event']) ?></td>
                    <td><?= $row['tgl_payment'] ?></td>
                    <td>Rp <?= number_format($row['total_payment'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['tipe_payment']) ?></td>
                    <td><?= htmlspecialchars($row['status_payment']) ?></td>
                    <td>
                        <!-- Form untuk update status -->
                        <form action="update-status.php" method="POST" class="d-inline">
                            <input type="hidden" name="payment_id" value="<?= $row['payment_id'] ?>">
                            <select name="status_payment" class="form-select form-select-sm mb-2">
                                <option value="pending" <?= ($row['status_payment'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="confirmed" <?= ($row['status_payment'] == 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>