<?php
session_start();
if (!isset($_SESSION['email'])) {
    // Jika pengguna belum login, redirect ke halaman login
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Email</title>
    <!-- Tambahkan CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>Update Email</h4>
                    </div>
                    <div class="card-body">
                        <form action="process_update_email.php" method="POST">
                            <div class="mb-3">
                                <label for="current_email" class="form-label">Email Saat Ini:</label>
                                <input type="email" id="current_email" name="current_email" 
                                    class="form-control" 
                                    value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="new_email" class="form-label">Email Baru:</label>
                                <input type="email" id="new_email" name="new_email" 
                                    class="form-control" placeholder="Masukkan email baru" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Update Email</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="Dashboard User.php" class="text-decoration-none">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
