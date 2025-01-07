<?php
require 'koneksi.php'; // Koneksi ke database

session_start();

if (isset($_SESSION['user_id'], $_SESSION['event_id'], $_SESSION['kursi_id'])) {
    $user_id = $_SESSION['user_id'];
    $event_id = $_SESSION['event_id'];
    $kursi_id = $_SESSION['kursi_id'];

}

// Query untuk mengambil ticket_id berdasarkan kursi_id
$query_tiket = "SELECT tiket_id FROM tiket WHERE kursi_id = ?";
$stmt = $conn->prepare($query_tiket);
$stmt->bind_param("i", $kursi_id); // Bind kursi_id
$stmt->execute();
$stmt->bind_result($ticket_id);
$stmt->fetch();
$stmt->close(); // Tutup statement setelah selesai

// Cek apakah ticket_id ditemukan
if ($ticket_id === null) {
    echo "Ticket ID tidak ditemukan untuk kursi_id $kursi_id!";
    exit;
}

// Simpan ticket_id ke dalam session
$_SESSION['tiket_id'] = $ticket_id;

echo "Ticket ID: " . $ticket_id . " telah disimpan dalam session.";



try {
    // Pastikan data yang diperlukan ada
    if (isset($_POST['total_price'], $_POST['payment_method'])) {
        // Ambil data dari POST
        $tgl_payment = date("Y-m-d H:i:s"); // Tanggal pembayaran saat ini
        $total_price = $_POST['total_price'];
        $payment_method = $_POST['payment_method'];
        $status_payment = "pending"; // Status default

        var_dump($_POST);

        $ticket_id = $_SESSION['tiket_id'];

        // Query untuk memasukkan data ke tabel payment
        $query_insert = "INSERT INTO payment (user_id, tiket_id, event_id, tgl_payment, total_payment, tipe_payment, status_payment) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query_insert);


        // Bind parameter
        $stmt->bind_param("iiissss", $user_id, $ticket_id, $event_id, $tgl_payment, $total_price, $payment_method, $status_payment);

        // Eksekusi query
        if ($stmt->execute()) {
            // Dapatkan payment_id yang baru saja dibuat
            $payment_id = $conn->insert_id;

            // Simpan payment_id ke dalam session
            $_SESSION['payment_id'] = $payment_id;

            // Redirect ke halaman konfirmasi dengan payment_id
            header("Location: myTicket.php");
            exit();

        } else {
            throw new Exception("Gagal menyimpan pembayaran: " . $stmt->error);
        }

        // Tutup statement
        $stmt->close();
        $conn->close();
    } else {
        throw new Exception("Data tidak lengkap. Harap periksa kembali.");
    }
} catch (Exception $e) {
    // Jika terjadi kesalahan, kirim respons error
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}

?>