<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "db_apk_uas";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi Gagal: " . mysqli_connect_error());
} else {
    echo "Koneksi Berhasil";
}
?>