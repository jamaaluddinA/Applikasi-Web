<?php
require 'koneksi.php';
session_start(); // Session harus dimulai di awal


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Menggunakan prepared statement untuk keamanan
    $sql = "SELECT * FROM admin WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Ambil data user
        $row = $result->fetch_assoc();
        
        // Set session setelah login berhasil
        $_SESSION['username'] = $username;
        
        // Redirect ke dashboard yang sudah diubah menjadi .php
        header("Location: Dashboard Admin.php");
        exit();
    } else {
        echo "<center>
                <h1>Username atau Password Anda Salah. Silahkan Coba Kembali.</h1>
                <button><strong><a href='login_admin.html'>Login</a></strong></button>
              </center>";
    }
    
    // Tutup statement dan koneksi
    $stmt->close();
    $conn->close();
}
?>
