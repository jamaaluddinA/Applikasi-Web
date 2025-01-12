<?php
session_start();
include 'koneksi.php'; // Pastikan file ini berisi koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_email = $_POST['current_email'];
    $new_email = $_POST['new_email'];

    if ($current_email === $_SESSION['email']) {
        // Lakukan pembaruan email di database
        $query = "UPDATE user SET email = ? WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $new_email, $current_email);

        if ($stmt->execute()) {
            // Perbarui email di sesi
            $_SESSION['email'] = $new_email;
            echo "<center>Email berhasil diperbarui.
            <button><strong><a href='Dashboard User.php'>Kembali ke Dashboard</a></strong>
            </center>";
        } else {
            echo "<center>Gagal memperbarui email. Silakan coba lagi.";
            echo "<center><button><a href='update_email_form.php'>Kembali</a>";
        }
    } else {
        echo "<center>Email saat ini tidak cocok.";
        echo "<center><button><a href='update_email_form.php'>Kembali</a>";
    }
}
?>
