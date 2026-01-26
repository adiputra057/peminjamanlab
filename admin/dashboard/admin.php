<?php
include "../../config/config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Total Peralatan
$result_peralatan = mysqli_query($conn, "SELECT COUNT(*) AS total_peralatan FROM peralatan");
$total_peralatan = mysqli_fetch_assoc($result_peralatan)['total_peralatan'];

// Total Pengguna
$result_pengguna = mysqli_query($conn, "SELECT COUNT(*) AS total_pengguna FROM pengguna");
$total_pengguna = mysqli_fetch_assoc($result_pengguna)['total_pengguna'];

// Total Peminjaman
$query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman");
$total_peminjaman = mysqli_fetch_assoc($query_total)['total'];

// Total Pengembalian
$query_selesai = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Selesai'");
$total_selesai = mysqli_fetch_assoc($query_selesai)['total'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Peminjaman Peralatan | Dashboard</title>

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="../assets/vendors/feather/feather.css" />
    <link rel="stylesheet" href="../assets/vendors/ti-icons/css/themify-icons.css" />
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
    <link rel="stylesheet" href="../assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css" />
    <link rel="stylesheet" href="../assets/js/select.dataTables.min.css" />
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/style.css" />
  </head>
  <body>
    <div class="container-scroller">
     <!-- Navbar -->
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <div class="container-fluid page-body-wrapper">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

  <!-- Main Panel -->
  <div class="main-panel">
    <div class="content-wrapper">
      <!-- Breadcrumb -->
      <div class="row">
        <div class="col-md-12 grid-margin">
          <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
              <h6 class="font-weight-normal mb-0">
                Dashboard<span class="text-gray"></span>
              </h6>
            </div>
          </div>
        </div>
      </div>

      <!-- Cards -->
            <div class="row">
              <!-- Statistik Cards -->
               
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                      <p class="font-weight-800">Total Peralatan</p>
                      <i class="bx bx-book icon-data"></i>
                    </div>
                    <p class="fs-30 mb-2 text-primary font-weight-medium mb-3"><?= $total_peralatan ?></p>
                    <p class="text-muted">Total Peralatan Yang Dimiliki</p>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                  <div class="card-body">
                     <div class="d-flex justify-content-between mb-3">
                      <p class="font-weight-800">Total Pengguna</p>
                       <i class="bx bx-user icon-data"></i>
                    </div>
                    <p class="fs-30 text-primary font-weight-medium mb-3"><?= $total_pengguna ?></p>
                    <p class="text-muted">Total Pengguna Saat Ini</p>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                      <p class="font-weight-800">Total Peminjaman</p>
                       <i class="bx bx-calendar-up-arrow icon-data-tr"></i>
                    </div>
                    <p class="fs-30 text-primary font-weight-medium mb-3"><?= $total_peminjaman ?></p>
                    <p class="text-muted">Total Peminjaman Saat Ini</p>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                      <p class="font-weight-800">Total Pengembalian</p>
                      <i class="bx bx-calendar-down-arrow icon-data-ts"></i> 
                    </div>
                    <p class="fs-30 text-primary font-weight-medium mb-3"><?= $total_selesai ?></p>
                    <p class="text-muted">Total Pengembalian Saat Ini</p>
                  </div>
                </div>
              </div>
            </div>

          <div class="row">
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title text-center mt-3">Statistik Peminjaman Peralatan 2025</h4>
                    <canvas id="chartBulanan" height="100"></canvas>
                  </div>
                </div>
              </div>
              <div class="col-lg-5 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title text-center mt-3">10 Peralatan Paling Sering Dipinjam</h5>
                  <canvas id="chartLingkaranPeralatan" width="100" height="100"></canvas>
                </div>
              </div>
              </div>
            </div>
        </div>

    <!-- Footer -->
    <footer class="footer">
      <div class="d-sm-flex justify-content-center justify-content-sm-between">
        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
          Copyright © 2025. <a href="#">Adi Putra</a> Lab Seni & Budaya
        </span>
      </div>
    </footer>
  </div>
</div>

  <!-- Scripts -->
  <?php include(__DIR__ . '/../assets/includes/scripts.php');?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Chart Bulanan
  fetch("chart_bulanan.php")
    .then(res => res.json())
    .then(data => {
      const ctxBar = document.getElementById("chartBulanan").getContext("2d");
      new Chart(ctxBar, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [{
            label: "Total Peminjaman",
            data: data.data,
            backgroundColor: "#4c84ff",
            borderRadius: 10
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              }
            }
          }
        }
      });
    });
fetch("chart_terbanyak_dipinjam.php")
    .then(res => res.json())
    .then(data => {
      const ctx = document.getElementById("chartLingkaranPeralatan").getContext("2d");
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: data.labels,
          datasets: [{
            data: data.data,
            backgroundColor: [
              '#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#9C27B0',
              '#FF9800', '#00BCD4', '#E91E63', '#3F51B5', '#8BC34A'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                boxWidth: 10
              }
            },
            title: {
              display: true,
            }
          }
        }
      });
    });
});
</script>


</body>
</html>
