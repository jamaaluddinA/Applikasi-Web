<?php
session_start(); // Session harus dimulai di awal
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['pass_login'];
    
    // Menggunakan prepared statement untuk keamanan
    $sql = "SELECT * FROM user WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Ambil data user
        $row = $result->fetch_assoc();
        
        // Set session setelah login berhasil
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['email'] = $row['email'];
        
        // Redirect ke dashboard yang sudah diubah menjadi .php
        header("Location: Dashboard User.php");
        exit();
    } else {
        echo "<center>
                <h1>Email atau Password Anda Salah. Silahkan Coba Login Kembali.</h1>
                <button><strong><a href='login.html'>Login</a></strong></button>
              </center>";
    }
    
    // Tutup statement dan koneksi
    $stmt->close();
    $conn->close();
}
?>
