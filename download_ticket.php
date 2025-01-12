<?php
require('tcpdf/tcpdf.php');
require 'koneksi.php'; // Untuk koneksi database jika perlu mengambil data tiket

// Pastikan user sudah login dan punya tiket yang valid
session_start();
if (!isset($_SESSION['user_id'], $_SESSION['tiket_id'])) {
    die('Anda harus login terlebih dahulu dan memiliki tiket.');
}

$user_id = $_SESSION['user_id'];
$tiket_id = $_SESSION['tiket_id'];

// Ambil data tiket dan event dari database
$query_tiket = "SELECT e.event, t.no_kursi, e.tgl_event 
                FROM tiket t 
                JOIN event e ON e.event_id = t.event_id 
                WHERE t.tiket_id = ?";
$stmt = $conn->prepare($query_tiket);
$stmt->bind_param("i", $tiket_id);
$stmt->execute();
$stmt->bind_result($event, $kursi, $tgl_event);
$stmt->fetch();
$stmt->close();

// Ambil data pembayaran terkait tiket ini
$query_pembayaran = "SELECT p.total_payment, p.tipe_payment, p.status_payment, u.nama_lengkap, u.email, u.no_tlp 
                     FROM payment p 
                     JOIN user u ON p.user_id = u.user_id 
                     WHERE p.tiket_id = ?";
$stmt_pembayaran = $conn->prepare($query_pembayaran);
$stmt_pembayaran->bind_param("i", $tiket_id);
$stmt_pembayaran->execute();
$stmt_pembayaran->bind_result($total_payment, $tipe_payment, $status_payment, $nama_lengkap, $email, $no_tlp);
$stmt_pembayaran->fetch();
$stmt_pembayaran->close();

if (!$event) {
    die('Data tiket tidak ditemukan.');
}
// Membuat objek TCPDF
$pdf = new TCPDF();
$pdf->SetMargins(15, 20, 15); // Set margin
$pdf->AddPage();

// Header PDF - Menambahkan Logo atau Judul
$pdf->SetFont('helvetica', 'B', 20);
$pdf->SetTextColor(0, 102, 204); // Warna biru
$pdf->Cell(0, 15, 'TONGKonser', 0, 1, 'C');
$pdf->Ln(10);

// Menambahkan garis pembatas
$pdf->SetLineWidth(0.5);
$pdf->Line(10, 35, 200, 35);

// ------------------------------------
// Biodata Pembeli
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(0, 0, 0); // Set warna teks ke hitam
$pdf->Cell(0, 10, 'Biodata Pembeli', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(40, 10, 'Nama: ' . $nama_lengkap);
$pdf->Ln();
$pdf->Cell(40, 10, 'Email: ' . $email);
$pdf->Ln();
$pdf->Cell(40, 10, 'Nomor Telepon: ' . $no_tlp);
$pdf->Ln(10);

// Garis pemisah antara bagian biodata dan event
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ------------------------------------
// Keterangan Event
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Keterangan Event', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(40, 10, 'Event: ' . $event);
$pdf->Ln();
$pdf->Cell(40, 10, 'Kursi: ' . $kursi);
$pdf->Ln();
$pdf->Cell(40, 10, 'Tanggal: ' . $tgl_event);
$pdf->Ln(10);

// Garis pemisah antara bagian event dan pembayaran
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ------------------------------------
// Rincian Pembayaran
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Rincian Pembayaran', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(40, 10, 'Total Pembayaran: Rp ' . number_format($total_payment, 0, ',', '.'));
$pdf->Ln();
$pdf->Cell(40, 10, 'Metode Pembayaran: ' . $tipe_payment);
$pdf->Ln();
$pdf->Cell(40, 10, 'Status Pembayaran: ' . $status_payment);
$pdf->Ln(15);


// Menambahkan Footer
$pdf->SetY(-35);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(150, 150, 150); // Warna abu-abu untuk footer
$pdf->Cell(0, 10, 'Terima kasih telah membeli tiket melalui platform kami.', 0, 0, 'C');

// Tentukan path dan output file PDF
$file_path = 'tiket/tiket' . $tiket_id . '.pdf';
$pdf->Output($file_path, 'I'); // Untuk menampilkan PDF langsung ke browser

// Set session atau response agar user bisa mengunduh file
$_SESSION['ticket_pdf'] = $file_path;  // Simpan path tiket PDF di session
header("Location: download_page.php");  // Arahkan ke halaman download
exit();
?>
