<?php
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['pass'];

    
    $sql = "INSERT INTO user (nama_lengkap, email, no_tlp, alamat, password) 
    VALUES ('$fullname','$email','$phone','$address','$password')";

    if (mysqli_query($conn, $sql)) {
        header("Location: login.html");
        exit();
    } else {
        // Jika login gagal, tampilkan pesan error
        echo "Pendaftaran Gagal : " . mysqli_error($conn);
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $conn->close();
}
?>
