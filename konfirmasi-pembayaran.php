<?php
require 'koneksi.php'; // Koneksi ke database

session_start();
if (isset($_SESSION['user_id'], $_SESSION['event_id'])) {
    $user_id = $_SESSION['user_id'];
    $event_id = $_SESSION['event_id'];
}

// Pastikan data yang diperlukan ada
if (isset($_POST['user_id'], $_POST['event_id'], $_POST['selected_kursi'], $_POST['jumlah_tiket'])) {
    // Ambil data dari formulir
    $user_id = $_POST['user_id'];
    $event_id = $_POST['event_id'];
    $selected_kursi = $_POST['selected_kursi']; // String kursi yang dipilih, dipisahkan dengan koma
    $jumlah_tiket = $_POST['jumlah_tiket'];

    // Pecah string $selected_kursi menjadi array
    $kursi_array = explode(',', $selected_kursi);

    // Mulai transaksi untuk memasukkan data
    $conn->begin_transaction(); // Memulai transaksi

    try {
        foreach ($kursi_array as $no_kursi) {
            $no_kursi = trim($no_kursi); // Trim untuk menghapus spasi di sekitar nomor kursi

            // Query SELECT untuk mendapatkan kursi_id
            $query_select = "SELECT kursi_id FROM nomor_kursi WHERE nomor_kursi = ? AND event_id = ?";
            $stmt_select = $conn->prepare($query_select);
            $stmt_select->bind_param("si", $no_kursi, $event_id);
            $stmt_select->execute();
            $stmt_select->bind_result($kursi_id);
            $stmt_select->fetch();
            $stmt_select->close(); // Tutup statement setelah selesai

            if ($kursi_id === null) {
                throw new Exception("Kursi $no_kursi tidak ditemukan untuk event $event_id!");
            }

            // Simpan kursi_id ke dalam session
            $_SESSION['kursi_id'] = $kursi_id;

            echo "Kursi ID: " . $kursi_id . " telah disimpan dalam session.";

            // Query INSERT untuk menambahkan tiket
            $query_insert = "INSERT INTO tiket (user_id, event_id, kursi_id, no_kursi, jumlah_tiket) 
                             VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($query_insert);
            $stmt_insert->bind_param("iiisi", $user_id, $event_id, $kursi_id, $no_kursi, $jumlah_tiket);

            if (!$stmt_insert->execute()) {
                throw new Exception("Gagal menyimpan tiket untuk kursi $no_kursi: " . $stmt_insert->error);
            }
        }

        // Jika semua berhasil, commit transaksi
        $conn->commit();
        echo "Transaksi berhasil! Tiket telah disimpan.";

    } catch (Exception $e) {
        // Jika terjadi kesalahan, rollback transaksi
        $conn->rollback();
        echo "Transaksi gagal: " . $e->getMessage();
    }
} else {
    echo "Data tidak lengkap!";
}


// Memastikan event_id ada di URL
if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Ambil data event berdasarkan event_id dari database
    $query = "SELECT * FROM event WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    // Periksa apakah event ditemukan
    if (!$event) {
        echo "Event tidak ditemukan.";
        exit;
    }
}

// Memastikan data tiket dan kursi ada di POST
if (isset($_POST['ticket_type']) && isset($_POST['jumlah_tiket']) && isset($_POST['selected_kursi'])) {
    $ticket_type = $_POST['ticket_type'];
    $jumlah_tiket = $_POST['jumlah_tiket'];
    $selected_kursi = explode(',', $_POST['selected_kursi']); // Mengambil kursi yang dipilih (array)

    // Hitung total harga tiket berdasarkan kursi yang dipilih
    $total_price = 0;

    foreach ($selected_kursi as $kursi) {
        // Ambil harga kursi dari tabel nomor_kursi berdasarkan nomor kursi yang dipilih
        $query_kursi = "SELECT harga FROM nomor_kursi WHERE event_id = ? AND nomor_kursi = ? AND status = 'kosong'";
        $stmt_kursi = $conn->prepare($query_kursi);
        $stmt_kursi->bind_param("is", $event_id, $kursi); // Bind param untuk event_id dan nomor kursi
        $stmt_kursi->execute();
        $result_kursi = $stmt_kursi->get_result();

        if ($row_kursi = $result_kursi->fetch_assoc()) {
            // Tambahkan harga kursi ke total harga
            $total_price += $row_kursi['harga'];
        }
    }
} else {
    echo "Data tiket tidak lengkap.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran - <?= htmlspecialchars($event['event']) ?></title>

    <!-- Link ke CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Konfirmasi Pembayaran untuk <?= htmlspecialchars($event['event']) ?></h1>
        <p class="text-center">Tanggal: <?= htmlspecialchars($event['tgl_event']) ?> | Lokasi:
            <?= htmlspecialchars($event['lokasi']) ?>
        </p>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Detail Tiket:</h5>
                        <p><strong>Jenis Tiket:</strong> <?= ucfirst($ticket_type) ?></p>
                        <p><strong>Harga Tiket:</strong> Rp
                            <?= number_format($total_price / $jumlah_tiket, 0, ',', '.') ?>
                        </p>
                        <p><strong>Jumlah Tiket:</strong> <?= htmlspecialchars($jumlah_tiket) ?></p>
                        <p><strong>Total Pembayaran:</strong> Rp <?= number_format($total_price, 0, ',', '.') ?></p>

                        <h5 class="card-title mt-4">Metode Pembayaran:</h5>
                        <form action="proses-pembayaran.php" method="post">
                            <input type="hidden" name="event_id" value="<?= htmlspecialchars($event_id) ?>">
                            <input type="hidden" name="ticket_type" value="<?= htmlspecialchars($ticket_type) ?>">
                            <input type="hidden" name="total_price" value="<?= htmlspecialchars($total_price) ?>">

                            <div class="form-group mb-3">
                                <label for="payment_method">Pilih Metode Pembayaran:</label>
                                <select class="form-control" action="proses-pembayaran.php" id="payment_method"
                                    name="payment_method" method="post" required>
                                    <option value="transfer bank">Transfer Bank</option>
                                    <option value="kartu kredit">Kartu Kredit</option>
                                    <option value="gopay">GoPay</option>
                                    <option value="dana">DANA</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Konfirmasi Pembayaran</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Link ke JavaScript Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>