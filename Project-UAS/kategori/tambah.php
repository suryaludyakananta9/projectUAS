<?php
include '../config/koneksi.php';
include '../config/middleware.php';
include '../config/fungsi.php';

$error_message = "";
$nama_kategori_input = "";

if (isset($_POST['simpan'])) {
    $nama_kategori_input = trim($_POST['nama_kategori']);
    $nama_kategori_aman  = bersihkan($koneksi, $nama_kategori_input);

    if ($nama_kategori_input === '') {
        $error_message = "Nama kategori wajib diisi.";
    } else {
        $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori_aman')";
        if (mysqli_query($koneksi, $query)) {
            header("Location: index.php?sukses=tambah");
            exit();
        } else {
            $error_message = "Gagal menyimpan data: " . mysqli_error($koneksi);
        }
    }
}

$nama_user = $_SESSION['nama_lengkap'] ?? ($_SESSION['username'] ?? 'Admin');
$inisial   = strtoupper(substr($nama_user, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h("Tambah Kategori") ?> &mdash; Surya Elektronik</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-shell">

  <header class="topbar">
    <div>
      <h1><?= h("Tambah Kategori") ?></h1>
    </div>
    <div class="topbar-right">
      <div class="user-chip">
        <span class="avatar"><?= h($inisial) ?></span>
        <span><?= h($nama_user) ?></span>
      </div>
      <a class="btn-logout" href="../auth/logout.php">Keluar</a>
    </div>
  </header>

  <div class="app-body">
    <aside class="sidebar">
      <div class="brand">
        <span class="dot"></span>
        <div class="brand-text">
          Surya Elektronik
          <small>Sistem Informasi Admin</small>
        </div>
      </div>

      <nav>
        <div class="nav-label">Menu</div>
        <a href="../index.php">
          <span class="icon">&#9673;</span> Dashboard
        </a>
        <a href="../produk/index.php">
          <span class="icon">&#9707;</span> Data Produk
        </a>
        <a href="../kategori/index.php" class="active">
          <span class="icon">&#9776;</span> Kategori
        </a>
      </nav>
    </aside>
    <main class="content">
<div class="card">
  <div class="card-body">

    <?php if (!empty($error_message)): ?>
      <div class="alert alert-error"><?= h($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-validasi" novalidate style="max-width:420px;">
      <div class="field">
        <label for="nama_kategori">Nama Kategori</label>
        <input type="text" id="nama_kategori" name="nama_kategori" class="input"
               placeholder="Contoh: Handphone, Laptop, PC / Komputer"
               value="<?= h($nama_kategori_input) ?>" required>
        <div class="error-text"></div>
      </div>

      <div class="form-actions">
        <button type="submit" name="simpan" class="btn btn-accent">Simpan Kategori</button>
        <a href="index.php" class="btn btn-outline">Batal</a>
      </div>
    </form>

  </div>
</div>

    </main>
  </div>

  <footer class="app-footer">
    <span>&copy; <?= date('Y') ?> Sistem Informasi Toko Elektronik</span>
  </footer>

</div>
<script src="../assets/js/validasi.js"></script>
</body>
</html>
