<?php
session_start();

// Cek jika file PDF ada di session
if (isset($_SESSION['ticket_pdf'])) {
    $file_path = $_SESSION['ticket_pdf'];
    
    if (file_exists($file_path)) {
        // Mengirim header untuk mengunduh file
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($file_path));
        
        // Membaca dan mengirim file ke browser
        readfile($file_path);
        
        // Hapus file PDF setelah diunduh
        unlink($file_path);
        unset($_SESSION['ticket_pdf']); // Hapus path dari session
        exit();
    } else {
        echo "Tiket tidak ditemukan.";
    }
} else {
    echo "Tidak ada file tiket untuk diunduh.";
}
?>
