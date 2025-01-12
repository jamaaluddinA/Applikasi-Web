<?php
session_start();
include 'koneksi.php'; // Pastikan file ini berisi koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['email']; // Email sebagai primary key

    // Verifikasi password saat ini
    $query = "SELECT password FROM user WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($password_in_db);
    $stmt->fetch();
    $stmt->close();

    if ($current_password === $password_in_db) { // Verifikasi password saat ini
        if ($new_password === $confirm_password) {
            // Update password di database
            $update_query = "UPDATE user SET password = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param('ss', $new_password, $email);

            if ($update_stmt->execute()) {
                echo "<center>Password berhasil diperbarui.
            <center><button><strong><a href='Dashboard User.php'>Kembali ke Dashboard</a></strong>
            </center>";
            } else {
                echo "Gagal memperbarui password. Silakan coba lagi.";
                echo "<center>Password saat ini salah.
        <center><button><a href='update_password_form.php'>Kembali</a>";
            }
            $update_stmt->close();
        } else {
            echo "Password baru dan konfirmasi tidak cocok.";
            echo "<center>Password saat ini salah.
        <center><button><a href='update_password_form.php'>Kembali</a>";
        }
    } else {
        echo "<center>Password saat ini salah.
        <center><button><a href='update_password_form.php'>Kembali</a>";
        
    }
}
?>