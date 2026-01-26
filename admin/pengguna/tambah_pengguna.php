<?php
session_start();
include "../../config/config.php";

// Ambil old data jika ada, jika tidak buat array default
$old = $_SESSION['old'] ?? [
    'username'      => '',
    'nama_lengkap'  => '',
    'no_hp'         => '',
    'email'         => '',
    'status'        => '',
    'role'          => ''
];

// Hapus old setelah diambil agar tidak muncul terus
unset($_SESSION['old']);
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Tambah Pengguna</title>

  <!-- Styles -->
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>

<?php if (isset($_SESSION['status'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const status = "<?= $_SESSION['status']; ?>";

    switch (status) {
        case 'username_terdaftar':
            Swal.fire({ icon: 'error', title: 'Username Sudah Terdaftar', text: 'Gunakan username lain!' });
            break;

        case 'password_terlalu_pendek':
            Swal.fire({ icon: 'error', title: 'Password Terlalu Pendek', text: 'Minimal 6 karakter!' });
            break;

        case 'email_invalid':
            Swal.fire({ icon: 'error', title: 'Email Tidak Valid', text: 'Gunakan email STIKOM (@stikom-bali.ac.id)!' });
            break;

        case 'email_terdaftar':
            Swal.fire({ icon: 'error', title: 'Email Sudah Terdaftar', text: 'Gunakan email lain!' });
            break;

        case 'sukses_tambah':
            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Pengguna berhasil ditambahkan!' });
            break;

        case 'gagal_tambah':
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan!' });
            break;
    }
});
</script>
<?php unset($_SESSION['status']); ?>
<?php endif; ?>

<div class="container-scroller">
  <?php include __DIR__ . '/../layout/navbar.php'; ?>
  <div class="container-fluid page-body-wrapper">
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>
    <div class="main-panel">
      <div class="content-wrapper">
        <div class="row mb-3">
          <div class="col">
            <h12>Dashboard / <span class="text-gray">Tambah Pengguna</span></h12>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <p class="card-title mb-3">Tambah Pengguna</p>
               <form action="../pengguna/pengguna_aksi.php" method="POST" id="userForm">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Username <code>*</code></label>
                       <input type="text" name="username" class="form-control" value="<?= $old['username']; ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Email <code>*</code></label>
                        <input type="text" name="email" class="form-control" value="<?= $old['email']; ?>" required>
                    </div> 
                </div>
                 <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Nama Lengkap <code>*</code></label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= $old['nama_lengkap']; ?>" required>
                    </div>
                     <div class="form-group col-md-6">
                        <label>Role <code>*</code></label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" <?= ($old['role']=='admin'?'selected':''); ?>>Admin</option>
                            <option value="user" <?= ($old['role']=='user'?'selected':''); ?>>User</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>No HP <code>*</code></label>
                        <input type="tel" name="no_hp" class="form-control" value="<?= $old['no_hp']; ?>" required>
                    </div>
                   <div class="form-group col-md-6">
                        <label>Status <code>*</code></label>
                        <select name="status" class="form-control" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Mahasiswa" <?= ($old['status']=='Mahasiswa'?'selected':''); ?>>Mahasiswa</option>
                            <option value="Pegawai" <?= ($old['status']=='Pegawai'?'selected':''); ?>>Pegawai</option>
                        </select>
                    </div>
                </div>
                 <div class="form-row">
                    
                   <div class="form-group col-md-6">
                        <label>Password <code>*</code></label>
                        <input type="password" name="password" id="password" class="form-control" required autocomplete="new-password">
                    </div>
                </div>
                <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                <a href="../dashboard/index.php?page=pengguna" class="btn btn-secondary">Batal</a>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
          <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
            &copy; 2025 Adi Putra – Lab Seni & Budaya
          </span>
        </div>
      </footer>
    </div>
  </div>
</div>

<?php include(__DIR__ . '/../assets/includes/scripts.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('userForm');
  const passwordField = document.getElementById('password');
  const confirmField = document.getElementById('konfirmasi_password');
  const emailField = document.querySelector('input[name="email"]');
  const noHpInput = document.querySelector('input[name="no_hp"]');

  // Validasi saat submit
  if (form) {
    form.addEventListener('submit', function (e) {
      const password = passwordField.value.trim();
      const konfirmasi = confirmField.value.trim();
      const email = emailField.value.trim();

      // Validasi email STIKOM
      const stikomRegex = /^[a-zA-Z0-9._%+-]+@stikom-bali\.ac\.id$/i;
      if (!stikomRegex.test(email)) {
        e.preventDefault();
        Swal.fire({
          icon: 'error',
          title: 'Email Tidak Valid',
          text: 'Harap gunakan email STIKOM (@stikom-bali.ac.id)!',
          confirmButtonColor: '#3085d12'
        });
        return;
      }

      // Validasi password cocok
      if (password !== konfirmasi) {
        e.preventDefault();
        Swal.fire({
          icon: 'error',
          title: 'Password Tidak Cocok',
          text: 'Password dan konfirmasi harus sama!',
          confirmButtonColor: '#3085d12'
        });
        return;
      }

      // Validasi panjang password
      if (password.length < 6) {
        e.preventDefault();
        Swal.fire({
          icon: 'error',
          title: 'Password Terlalu Pendek',
          text: 'Minimal 6 karakter!',
          confirmButtonColor: '#3085d12'
        });
        return;
      }
    });
  }

  // Format nomor HP
  if (noHpInput) {
    noHpInput.addEventListener('input', function () {
      let originalValue = this.value;
      let numericOnly = originalValue.replace(/[^0-9]/g, '');

      if (numericOnly.startsWith('0')) {
        this.value = '+62' + numericOnly.substring(1);
      } else if (numericOnly.startsWith('62')) {
        this.value = '+62' + numericOnly.substring(2);
      } else if (originalValue.startsWith('+620')) {
        this.value = '+62' + numericOnly.substring(3);
      } else if (!originalValue.startsWith('+62')) {
        this.value = '+62' + numericOnly;
      }
    });
  }
});
</script>


</body>
</html>
