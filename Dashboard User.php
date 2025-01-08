<?php
session_start();
require 'koneksi.php';

// Cek apakah session user_id ada
if (!isset($_SESSION['user_id'])) {
  // Jika tidak ada, arahkan ke halaman login
  header("Location: login.php");
  exit();
}

// Ambil data user yang sedang login
$user_id = $_SESSION['user_id'];
$query_user = "SELECT * FROM user WHERE user_id = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Dashboard - TONGKonser</title>
    <link rel="stylesheet" href="styleDU.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM6g0g5z5e5e5e5e5e5e5e5e5e5e5e5e5e5e5" crossorigin="anonymous">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-body-teritory">
      <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="Dashboard User.html"
          >TONGKonser</a
        >
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link fs-6" href="#">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fs-6" href="#eventSection">Event</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fs-6" href="#">My Tickets</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-danger fs-6" href="logout.php">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="col-md-12">
      <section class="main bg-primary text-center py-5 header-background">
        <div class="container">
          <h1 class="text-warning">Welcome to TONGKonser</h1>
          <p class="text-info-emphasis">Your easiest choice for buying tickets</p>
        </div>
      </section>

      <section id="eventSection" class="my-5">
        <div class="container py-5">
          <h2>Events</h2>
          <div class="row">
            <div class="col-md-4">
              <div class="card">
                <img src="images/tulus card.jpg" class="card-img-top" alt="Event 1" style="object-fit: cover; height: 200px;" />
                <div class="card-body">
                  <h5 class="card-title">An Evening with <span class="text-primary">Tulus</span></h5>
                  <p class="card-text">20 Januari 2025, Jakarta</p>
                  <a href="#event1" class="btn btn-primary">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card">
                <img src="images/yura2.jpg" class="card-img-top" alt="Event 2" style="object-fit: cover; height: 200px;"/>
                <div class="card-body">
                  <h5 class="card-title">Jazzy Night with Yura Yunita & Kunto Aji</h5>
                  <p class="card-text">15 Februari 2025, Bandung</p>
                  <a href="#event2" class="btn btn-primary">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card">
                <img src="images/salma2.jpg" class="card-img-top" alt="Event 3" style="object-fit: cover; height: 200px;"/>
                <div class="card-body">
                  <h5 class="card-title">Salma Salsabila: A Journey if Love</h5>
                  <p class="card-text">10 Maret 2025, Jakarta</p>
                  <a href="#event3" class="btn btn-primary">View Details</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <section id="event1" class="position-relative">
        <div class="container-fluid">
          <div
            class="bg-image"
            style="
              background-image: url('images/tulus.jpg');
              background-size: cover;
              background-position: center;
              height: 100vh;
            "
          >
            <div class="d-flex justify-content-center align-items-center h-100">
              <div class="text-center text-white bg-dark bg-opacity-50 p-5 rounded shadow" style="max-width: 600px; width: 100%;">
                <h1 class="display-5 py-4 fw-bold">An Evening with <span class="text-primary">Tulus</span></h1>
                <p class="lead col-md-12 align-items-center" style="font-size: 1rem;">Nikmati malam istimewa bersama Tulus, salah satu penyanyi solo terbaik Indonesia, dalam konser eksklusif yang menghadirkan lagu-lagu hits dari albumnya seperti "Monokrom," "Gajah," dan "Hati-Hati di Jalan." Konser ini akan dilangsungkan di sebuah venue yang intim, memberikan penggemar kesempatan untuk merasakan suasana akustik yang hangat dan penuh emosi, sementara Tulus membawakan lagu-lagu yang sudah memikat hati jutaan orang. Para penonton juga berkesempatan untuk bertemu langsung dengan Tulus dalam sesi meet-and-greet yang hanya tersedia bagi penggemar terpilih.</p>
                <a href="link-ke-tiket.html" class="btn btn-danger btn-md mt-3" style="transition: background-color 0.3s, transform 0.3s;" onmouseover="this.style.backgroundColor='#c82333'; this.style.transform='scale(1.05)';" onmouseout="this.style.backgroundColor='#dc3545'; this.style.transform='scale(1)';">Beli Tiket</a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="event2" class="position-relative">
        <div class="container-fluid">
          <div
            class="bg-image"
            style="
              background-image: url('images/bg konser.jpg');
              background-size: cover;
              background-position: center;
              height: 100vh;
            "
          >
            <div class="d-flex justify-content-center align-items-center h-100">
              <div class="text-center text-white bg-dark bg-opacity-50 p-5 rounded shadow" style="max-width: 600px; width: 100%;">
                <h1>Jazzy Night with Yura Yunita & Kunto Aji</h1>
                <p>Nikmati malam penuh pesona dalam konser jazzy yang dipandu oleh dua penyanyi berbakat Indonesia, Yura Yunita dan Kunto Aji. Kedua artis ini akan membawakan lagu-lagu hits mereka dengan sentuhan jazzy yang memukau, menciptakan suasana yang santai dan penuh perasaan. Yura Yunita dengan suaranya yang lembut dan penuh nuansa, serta Kunto Aji yang dikenal dengan gaya musik soul dan folk-nya, akan menciptakan pengalaman musik yang berbeda dengan perpaduan genre yang luar biasa. Konser ini diadakan di venue eksklusif yang intim, memberi kesempatan bagi penggemar untuk menikmati pertunjukan dengan lebih dekat dan personal. Penggemar juga akan berkesempatan berinteraksi langsung dengan Yura dan Kunto dalam sesi meet-and-greet yang sangat terbatas.</p>
                <a href="link-ke-tiket.html" class="btn btn-danger">Beli Tiket</a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="event3" class="position-relative">
        <div class="container-fluid">
          <div
            class="bg-image"
            style="
              background-image: url('images/salma.jpg');
              background-size: cover;
              background-position: center;
              height: 100vh;
            "
          >
            <div class="d-flex justify-content-center align-items-center h-100">
              <div class="text-center text-white bg-dark bg-opacity-50 p-5 rounded shadow" style="max-width: 600px; width: 100%;">
                <h1>Salma Salsabila: A Journey of Love</h1>
                <p>Bergabunglah dalam konser eksklusif bersama Salma Salsabila, penyanyi muda berbakat Indonesia yang dikenal dengan suara merdunya yang memikat. Dalam konser "A Journey of Love," Salma akan membawakan lagu-lagu hits dari albumnya serta beberapa lagu baru yang belum pernah diperdengarkan sebelumnya. Penonton akan merasakan perjalanan emosional lewat suara indah Salma yang menceritakan kisah cinta, perjuangan, dan harapan. Konser ini diadakan di venue dengan kapasitas terbatas, menciptakan pengalaman yang intim dan mendalam. Salma juga akan berinteraksi langsung dengan penggemar melalui sesi Q&A dan meet-and-greet, memberikan kesempatan kepada mereka untuk lebih dekat mengenal sang penyanyi. Acara ini hanya untuk penggemar terpilih, menjadikan setiap momen dalam konser ini begitu berharga dan eksklusif.</p>
                <a href="link-ke-tiket.html" class="btn btn-danger">Beli Tiket</a>
              </div>
            </div>
          </div>
        </div>
      </section>
      <footer class="bg-dark text-white py-5">
        <div class="container">
          <div class="row">
            <div class="col-md-4">
              <h5 class="fw-bold">TONGKonser</h5>
              <p class="text-white">The Easiest Way to Get Your Concert Tickets</p>
            </div>
            <div class="col-md-4">
              <h5 class="fw-bold">Quick Links</h5>
              <ul class="list-unstyled">
                <li><a href="#" class="text-white text-decoration-none">Home</a></li>
                <li><a href="#eventSection" class="text-white text-decoration-none">Events</a></li>
                <li><a href="#" class="text-white text-decoration-none">My Tickets</a></li>
                <li><a href="logout.php" class="text-white text-decoration-none">Logout</a></li>
              </ul>
            </div>
            <div class="col-md-4">
              <h5 class="fw-bold">Contact Us</h5>
              <p class="text-white">Email: support@tongkonser.com</p>
              <p class="text-white">Phone: +62 123 456 789</p>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col text-center">
              <p class="mb-0">&copy; 2024 TONGKonser. All rights reserved.</p>
            </div>
          </div>
        </div>
      </footer>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>

