<?php
// Koneksi ke database
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = $_POST['payment_id'];

    if (!empty($payment_id)) {
        // Query untuk menghapus pembayaran
        $query = "DELETE FROM payment WHERE payment_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $payment_id);

        if ($stmt->execute()) {
            // Redirect kembali ke dashboard admin dengan pesan sukses
            header("Location: Dashboard Admin.php?msg=success-delete");
        } else {
            // Redirect dengan pesan error
            header("Location: Dashboard Admin.php?msg=error-delete");
        }

        $stmt->close();
    } else {
        header("Location: Dashboard Admin.php?msg=invalid-id");
    }
} else {
    header("Location: Dashboard Admin.php");
}

$conn->close();
?>
