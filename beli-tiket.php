<?php
require 'koneksi.php'; // Koneksi ke database

session_start();

// ambil user_id
    if(isset($_SESSION['user_id'])){
        //mengambil nilai user_id
         $user_id =$_SESSION['user_id'];
    }


    
// Ambil ID event dari URL
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Simpan event_id ke dalam session (menetapkan nilai)
    $_SESSION['event_id'] = $event_id;

    // Ambil data event berdasarkan event_id
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

    // Ambil harga tiket berdasarkan data event yang ada di tabel
    $harga_tiket = [
        'reguler' => $event['harga_reguler'],
        'premium' => $event['harga_premium'],
        'vip' => $event['harga_vip'],
    ];

    // Ambil data kursi berdasarkan event_id
    $query_kursi = "SELECT * FROM nomor_kursi WHERE event_id = ? AND status = 'kosong'";

    $stmt_kursi = $conn->prepare($query_kursi);
    $stmt_kursi->bind_param("i", $event_id);
    $stmt_kursi->execute();
    $result_kursi = $stmt_kursi->get_result();
} else {
    echo "ID acara tidak diberikan.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Tiket - <?= htmlspecialchars($event['event']) ?></title>

    <!-- Link ke CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Pesan Tiket untuk <?= htmlspecialchars($event['event']) ?></h1>
        <p class="text-center text-muted">Tanggal: <?= htmlspecialchars($event['tgl_event']) ?> | Lokasi:
            <?= htmlspecialchars($event['lokasi']) ?>
        </p>

        <form id="ticketForm" action="konfirmasi-pembayaran.php" method="post">
            <input type="hidden" name="event_id" value="<?= htmlspecialchars($event_id) ?>">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
            <input type="hidden" name="ticket_type" id="ticket_type">
            <input type="hidden" name="selected_kursi" id="selected_kursi">


            <div class="row justify-content-center">
                <div class="col-md-6">

                    <!-- Pilih Jenis Tiket -->
                    <div class="card shadow-sm mb-4 border-light">
                        <div class="card-body">
                            <h5 class="card-title text-center">Pilih Jenis Tiket</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ticket_type" id="reguler"
                                    value="reguler" required>
                                <label class="form-check-label" for="reguler">
                                    Reguler - Rp <?= number_format($harga_tiket['reguler'], 0, ',', '.') ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ticket_type" id="premium"
                                    value="premium" required>
                                <label class="form-check-label" for="premium">
                                    Premium - Rp <?= number_format($harga_tiket['premium'], 0, ',', '.') ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ticket_type" id="vip" value="vip"
                                    required>
                                <label class="form-check-label" for="vip">
                                    VIP - Rp <?= number_format($harga_tiket['vip'], 0, ',', '.') ?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Pilih Kursi (Collapse) -->
                    <div class="card shadow-sm mb-4 border-light">
                        <div class="card-body">
                            <h5 class="card-title text-center">Pilih Kursi</h5>
                            <button class="btn btn-outline-primary w-100 mb-3" type="button" data-bs-toggle="collapse"
                                data-bs-target="#kursiCollapse" aria-expanded="false" aria-controls="kursiCollapse">
                                Tampilkan Kursi Tersedia
                            </button>
                            <div class="collapse" id="kursiCollapse">
                                <div class="d-flex flex-wrap justify-content-center">
                                    <?php while ($kursi = $result_kursi->fetch_assoc()): ?>
                                        <div class="col-4 mb-3">
                                            <label>
                                                <input type="checkbox" class="kursi" name="kursi[]"
                                                    value="<?= htmlspecialchars($kursi['nomor_kursi']) ?>"
                                                    data-tipe-tiket="<?= htmlspecialchars($kursi['tipe_tiket']) ?>">
                                                <?= htmlspecialchars($kursi['nomor_kursi']) ?>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Jumlah Tiket -->
                    <div class="card shadow-sm mb-4 border-light">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="jumlah_tiket" class="form-label">Jumlah Tiket</label>
                                <input type="number" name="jumlah_tiket" id="jumlah_tiket" class="form-control" required
                                    min="1" value="1">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Lanjutkan Pembayaran</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
        
    </div>

    <script>
       document.getElementById('ticketForm').addEventListener('submit', function (e) {
    const jumlahTiket = document.getElementById('jumlah_tiket').value;
    const kursiCheckboxes = document.querySelectorAll('.kursi:checked');

    // Validasi jumlah kursi yang dipilih
    if (kursiCheckboxes.length != jumlahTiket) {
        alert("Jumlah kursi yang dipilih harus sama dengan jumlah tiket.");
        e.preventDefault();
        return false;
    }

    // Ambil semua kursi yang dipilih
    const selectedKursi = [];
    let ticketType = null;
    kursiCheckboxes.forEach(function (checkbox) {
        selectedKursi.push(checkbox.value);

        // Ambil tipe tiket dari data-tipe-tiket
        const tipeTiket = checkbox.getAttribute('data-tipe-tiket');
        if (ticketType === null) {
            ticketType = tipeTiket; // Set tipe tiket pertama
        } else if (ticketType !== tipeTiket) {
            // Jika tipe tiket berbeda, tampilkan error
            alert("Semua kursi yang dipilih harus memiliki tipe tiket yang sama.");
            e.preventDefault();
            return false;
        }
    });

    // Isi input hidden dengan data
    document.getElementById('selected_kursi').value = selectedKursi.join(',');
    document.getElementById('ticket_type').value = ticketType;
});

    </script>

    <!-- Link ke CSS dan JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>



</html>