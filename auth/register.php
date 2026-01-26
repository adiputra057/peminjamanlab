<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Register</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- SweetAlert -->
<?php if (isset($_SESSION['status'])): ?>
  <script>
    <?php
    switch ($_SESSION['status']) {
      case 'register_duplikat':
        echo "Swal.fire({ icon: 'warning', title: 'Pendaftaran Gagal', text: 'Username sudah digunakan!', timer: 3000, showConfirmButton: false });";
        break;
      case 'register_gagal':
        $err = addslashes($_SESSION['error'] ?? 'Terjadi kesalahan pada database.');
        echo "Swal.fire({ icon: 'error', title: 'Error', text: '$err', showConfirmButton: true });";
        break;
      case 'register_sukses':
        echo "Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Pendaftaran berhasil, silakan login!', timer: 2000, showConfirmButton: false }).then(() => { window.location.href = 'login.php'; });";
        break;
      case 'register_kosong':
        echo "Swal.fire({ icon: 'warning', title: 'Form Tidak Lengkap', text: 'Harap isi semua data!', timer: 2000, showConfirmButton: false });";
        break;
      case 'password_too_short':
        echo "Swal.fire({ icon: 'warning', title: 'Password Terlalu Pendek', text: 'Password harus minimal 6 karakter!', timer: 2000, showConfirmButton: false });";
        break;
      case 'no_hp_duplikat':
        echo "Swal.fire({ icon: 'warning', title: 'Nomor HP Terdaftar', text: 'Nomor HP Sudah Terdaftar Gunakan Nomer HP lain', timer: 2000, showConfirmButton: false });";
        break;
      case 'email_invalid':
        echo "Swal.fire({ icon: 'warning', title: 'Email Tidak Valid', text: 'Harap gunakan email STIKOM (@stikom-bali.ac.id)!', timer: 3000, showConfirmButton: false });";
        break;

      case 'email_duplikat':
          echo "Swal.fire({ icon: 'warning', title: 'Email Sudah Terdaftar', text: 'Gunakan email Outlook lain!', timer: 3000, showConfirmButton: false });";
          break;

    }
    unset($_SESSION['status'], $_SESSION['error']);
    ?>
  </script>
<?php endif; ?>
<!-- End SweetAlert -->

<div class="container min-vh-100 d-flex justify-content-center align-items-center">
  <div class="col-lg-4 col-md-3">
    <div class="card shadow p-4">
      <h3 class="text-center mb-2">Register</h3>
      <form action="proses_register.php" method="POST">
  <div class="row">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required
        value="<?= htmlspecialchars($_SESSION['old']['username'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" id="email" class="form-control" required
        placeholder="Contoh: user@stikom-bali.ac.id"
        value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Nama Lengkap</label>
      <input type="text" name="nama_lengkap" class="form-control" required
        value="<?= htmlspecialchars($_SESSION['old']['nama_lengkap'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-control" required>
        <option value="">-- Pilih Status --</option>
        <option value="Mahasiswa" <?= (isset($_SESSION['old']['status']) && $_SESSION['old']['status'] === 'mahasiswa') ? 'selected' : '' ?>>Mahasiswa</option>
        <option value="Pegawai" <?= (isset($_SESSION['old']['status']) && $_SESSION['old']['status'] === 'pegawai') ? 'selected' : '' ?>>Pegawai</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">No. HP</label>
      <input type="text" name="no_hp" id="no_hp" class="form-control" required placeholder="Contoh: 08123456789"
        value="<?= htmlspecialchars($_SESSION['old']['no_hp'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required
        value="<?= isset($_SESSION['old']['password']) ? htmlspecialchars($_SESSION['old']['password']) : '' ?>">
    </div>
  </div>

      <div class="text-center">
      <button type="submit" name="daftar" class="btn btn-primary px-3 fw-bold">Daftar</button>
      </div>
    </form>

        <p class="mt-2 text-center">
          Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold">Login</a>
        </p>
    </div>
  </div>
</div>

<!-- Script untuk ubah otomatis 08 menjadi +62 -->
<script>
document.getElementById('no_hp').addEventListener('input', function (e) {
    let val = e.target.value;

    if (val.startsWith('08')) {
        e.target.value = '+62' + val.slice(1);
    }
    if (val.startsWith('+62+62')) {
        e.target.value = '+62' + val.slice(6);
    }
    e.target.value = e.target.value.replace(/[^0-9\+]/g, '');
});
</script>

<?php unset($_SESSION['old']); ?>
</body>
</html>
