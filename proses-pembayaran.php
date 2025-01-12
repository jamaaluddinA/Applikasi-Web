<?php
require 'koneksi.php'; // Koneksi ke database
session_start();

try {
    if (!isset($_SESSION['user_id'], $_SESSION['event_id'], $_SESSION['kursi_id'])) {
        throw new Exception("Session tidak lengkap. Harap login dan pilih kursi/event terlebih dahulu.");
    }

    $user_id = $_SESSION['user_id'];
    $event_id = $_SESSION['event_id'];
    $kursi_id = $_SESSION['kursi_id'];

    // Ambil tiket_id berdasarkan kursi_id
    $query_tiket = "SELECT tiket_id FROM tiket WHERE kursi_id = ?";
    $stmt = $conn->prepare($query_tiket);
    $stmt->bind_param("i", $kursi_id);
    $stmt->execute();
    $stmt->bind_result($ticket_id);
    $stmt->fetch();
    $stmt->close();

    if ($ticket_id === null) {
        throw new Exception("Tiket tidak ditemukan untuk kursi yang dipilih.");
    }

    $_SESSION['tiket_id'] = $ticket_id;

    // Validasi input POST
    if (!isset($_POST['total_price'], $_POST['payment_method'])) {
        throw new Exception("Data pembayaran tidak lengkap.");
    }

    $total_price = $_POST['total_price'];
    $payment_method = $_POST['payment_method'];

    if (!is_numeric($total_price) || $total_price <= 0) {
        throw new Exception("Total harga tidak valid.");
    }

    // Mulai transaksi
    $conn->begin_transaction();

    date_default_timezone_set('Asia/Jakarta');
    $tgl_payment = date("Y-m-d H:i:s");
    $status_payment = "pending";

    // Insert ke tabel payment
    $query_insert = "INSERT INTO payment (user_id, tiket_id, event_id, tgl_payment, total_payment, tipe_payment, status_payment)
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query_insert);
    $stmt->bind_param("iiissss", $user_id, $ticket_id, $event_id, $tgl_payment, $total_price, $payment_method, $status_payment);

    if (!$stmt->execute()) {
        throw new Exception("Gagal menyimpan pembayaran: " . $stmt->error);
    }

    // Dapatkan payment_id yang baru dibuat
    $payment_id = $conn->insert_id;
    $_SESSION['payment_id'] = $payment_id;

    // Update status kursi menjadi 'penuh'
    $query_kursi = "UPDATE nomor_kursi nk 
                    JOIN tiket t ON t.kursi_id = nk.kursi_id 
                    SET nk.status = 'penuh' 
                    WHERE t.tiket_id = ?";
    $stmt_kursi = $conn->prepare($query_kursi);
    $stmt_kursi->bind_param("i", $ticket_id);

    if (!$stmt_kursi->execute()) {
        throw new Exception("Gagal mengubah status kursi: " . $stmt_kursi->error);
    }

    // Commit transaksi
    $conn->commit();

    // Redirect ke halaman konfirmasi
    header("Location: myTicket.php");
    exit();

} catch (Exception $e) {
    if ($conn->in_transaction) {
        $conn->rollback();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
