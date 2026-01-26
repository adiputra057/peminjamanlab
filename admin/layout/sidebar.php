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
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets//css/style.css" />
    
    <!-- Custom CSS untuk fixed sidebar -->
    <style>
      /* Fixed Sidebar Styling */
      .sidebar {
        position: fixed !important;
        top: 0;
        left: 0;
        height: 100vh !important;
        width: 250px; /* Sesuaikan dengan lebar sidebar Anda */
        overflow-y: hidden !important; /* Hilangkan scroll vertikal */
        overflow-x: hidden !important; /* Hilangkan scroll horizontal */
        z-index: 1000;
        background-color: #fff; /* Sesuaikan dengan warna sidebar Anda */
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        margin-top: 20px;
      }

      /* Styling untuk nav items agar tidak overflow */
      .sidebar .nav {
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 15px 0;
        margin: 0;
       
      }

      /* Styling untuk nav items */
      .sidebar .nav-item {
        flex-shrink: 0; /* Prevent items from shrinking */
        margin-bottom: 8px;
      }

      /* Styling untuk nav links */
      .sidebar .nav-link {
        padding: 15px 25px;
        display: flex;
        align-items: center;
        color: #495057;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 600;
      }

      .sidebar .nav-link:hover {
        background-color: #e9ecef;
        color: #212529;
        font-weight: 700;
      }

      .sidebar .nav-link.active,
      .sidebar .nav-item.active .nav-link {
        background-color: #007bff;
        color: #fff;
        font-weight: 700;
      }

      /* Icon styling */
      .sidebar .menu-icon {
        margin-right: 15px;
        font-size: 20px;
        width: 24px;
        text-align: center;
      }

      /* Menu title */
      .sidebar .menu-title {
        font-size: 15px;
        font-weight: 600;
        line-height: 1.4;
      }

      /* Sidebar heading */
      .sidebar-heading {
        padding: 15px 25px;
        font-size: 13px;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1.2px;
      }

      /* HR styling */
      .sidebar hr {
        margin: 7px 25px;
        border: none;
        height: 1px;
        background-color: #dee2e6;
      }

      /* Adjust main content to account for fixed sidebar */
      .main-panel {
        margin-left: 250px; /* Same as sidebar width */
        width: calc(100% - 250px);
      }

      /* Responsive adjustments */
      @media (max-width: 768px) {
        .sidebar {
          transform: translateX(-100%);
          transition: transform 0.3s ease;
        }
        
        .sidebar.show {
          transform: translateX(0);
        }
        
        .main-panel {
          margin-left: 0;
          width: 100%;
        }
      }

      /* Dropdown styling - Simplified */
      .sidebar .nav-item.dropdown .nav-link {
        position: relative;
        padding-right: 45px;
        cursor: pointer;
      }
      
      .sidebar .nav-item.dropdown .nav-link::after {
        content: '';
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 6px solid currentColor;
        transition: transform 0.3s ease;
      }
      
      .sidebar .nav-item.dropdown .nav-link[aria-expanded="true"]::after {
        transform: translateY(-50%) rotate(180deg);
      }
      
      .sidebar .dropdown-menu {
        position: static !important;
        display: none;
        float: none;
        width: 100%;
        background-color: #f8f9fa;
        border: none;
        border-radius: 0;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        margin: 0;
        padding: 0;
        transform: none !important;
        inset: auto !important;
      }
      
      .sidebar .dropdown-menu.show {
        display: block !important;
      }
      
      .sidebar .dropdown-item {
        padding: 12px 25px 12px 45px;
        color: #6c757d;
        font-weight: 600;
        font-size: 14px;
        border: none;
        background: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        width: 100%;
        text-decoration: none;
      }
      
      .sidebar .dropdown-item i {
        margin-right: 10px;
        font-size: 16px;
        width: 20px;
        text-align: center;
        color: #6c757d;
      }
      
      .sidebar .dropdown-item:hover,
      .sidebar .dropdown-item:focus {
        background-color: #e9ecef;
        color: #495057;
        font-weight: 700;
      }
      
      .sidebar .dropdown-item:hover i,
      .sidebar .dropdown-item:focus i {
        color: #495057;
      }
      
      .sidebar .dropdown-item.active,
      .sidebar .dropdown-item:active {
        background-color: #007bff;
        color: #fff;
        font-weight: 700;
      }
      
      .sidebar .dropdown-item.active i,
      .sidebar .dropdown-item:active i {
        color: #fff;
      }

      /* Prevent text selection on sidebar */
      .sidebar {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
      }
    </style>
  </head>
  <body>
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <ul class="nav font-weight-bold">
        <li class="nav-item active" style="margin-top: 25px;">
          <a class="nav-link" href="index.php?page=home">
            <i class="icon-grid menu-icon"></i>
            <span class="menu-title">Dashboard</span>
          </a>
        </li>
        <hr />
        <div class="sidebar-heading">Data Master</div>
        <hr />
        
        <!-- Dropdown untuk Peralatan -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" aria-expanded="false">
            <i class="menu-icon bx bx-package"></i>
            <span class="menu-title">Peralatan</span>
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="index.php?page=peralatan">
              <i class="bx bx-edit me-2"></i>Data Peralatan</a></li>
            <li><a class="dropdown-item" href="index.php?page=daftar_peralatan">
              <i class="bx bx-list-ul me-2"></i>Daftar Peralatan</a></li>
          </ul>
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=pengguna">
            <i class="menu-icon bx bx-user"></i>
            <span class="menu-title">Data Pengguna</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=peminjaman">
            <i class="menu-icon bx bx-box"></i>
            <span class="menu-title">Data Peminjaman</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=pengembalian">
            <i class="menu-icon bx bx-history"></i>
            <span class="menu-title">Data Pengembalian</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=laporan">
            <i class="menu-icon bx bxs-report"></i>
            <span class="menu-title">Laporan</span>
          </a>
        </li>
      </ul>
    </nav>

    <script>
      // Simple dropdown functionality for sidebar
      document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggles = document.querySelectorAll('.sidebar .dropdown-toggle');
        
        dropdownToggles.forEach(function(toggle) {
          toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const dropdownMenu = this.nextElementSibling;
            const isOpen = dropdownMenu.classList.contains('show');
            
            // Close all dropdowns first
            document.querySelectorAll('.sidebar .dropdown-menu').forEach(function(menu) {
              menu.classList.remove('show');
            });
            
            document.querySelectorAll('.sidebar .dropdown-toggle').forEach(function(toggle) {
              toggle.setAttribute('aria-expanded', 'false');
            });
            
            // Toggle current dropdown
            if (!isOpen) {
              dropdownMenu.classList.add('show');
              this.setAttribute('aria-expanded', 'true');
            }
          });
        });
      });

      // Optional: Toggle sidebar on mobile
      function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('show');
      }
    </script>

  </body>
</html>