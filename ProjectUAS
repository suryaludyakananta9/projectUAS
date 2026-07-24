<?php
include 'config/koneksi.php';
include 'config/middleware.php';
include 'config/fungsi.php';

$total_produk   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM produk"))['jumlah'];
$total_kategori = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM kategori"))['jumlah'];
$total_stok     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(stok),0) AS jumlah FROM produk"))['jumlah'];
$stok_menipis   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM produk WHERE stok < 5"))['jumlah'];

$produk_menipis = mysqli_query($koneksi, "
    SELECT p.nama_produk, p.merek, p.stok, k.nama_kategori
    FROM produk p
    JOIN kategori k ON k.id_kategori = p.id_kategori
    WHERE p.stok < 5
    ORDER BY p.stok ASC
    LIMIT 5
");

$nama_user = $_SESSION['nama_lengkap'] ?? ($_SESSION['username'] ?? 'Admin');
$inisial   = strtoupper(substr($nama_user, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Surya Elektronik</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-shell">

  <header class="topbar">
    <div>
      <h1>Dashboard</h1>
      <div class="subtitle">Ringkasan data Sistem Informasi Toko Surya Elektronik</div>
    </div>
    <div class="topbar-right">
      <div class="user-chip">
        <span class="avatar"><?= h($inisial) ?></span>
        <span><?= h($nama_user) ?></span>
      </div>
      <a class="btn-logout" href="auth/logout.php">Keluar</a>
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
        <a href="index.php" class="active">
          <span class="icon">&#9673;</span> Dashboard
        </a>
        <a href="produk/index.php">
          <span class="icon">&#9707;</span> Data Produk
        </a>
        <a href="kategori/index.php">
          <span class="icon">&#9776;</span> Kategori
        </a>
      </nav>
    </aside>

    <main class="content">

      <div class="stat-grid">
        <div class="stat-card accent">
          <div class="stat-label">Total Produk</div>
          <div class="stat-value mono"><?= (int) $total_produk ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Kategori</div>
          <div class="stat-value mono"><?= (int) $total_kategori ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Unit Stok</div>
          <div class="stat-value mono"><?= (int) $total_stok ?></div>
        </div>
        <div class="stat-card warn">
          <div class="stat-label">Stok Menipis (&lt;5)</div>
          <div class="stat-value mono"><?= (int) $stok_menipis ?></div>
        </div>
      </div>

      <div class="card">
        <div class="card-head">
          <h2 style="font-size:15px;">Produk Perlu Restok</h2>
          <a href="produk/index.php" class="btn btn-outline btn-sm">Lihat Semua Produk</a>
        </div>
        <div class="table-wrap">
          <table class="data-table">
            <tr>
              <th>Produk</th>
              <th>Kategori</th>
              <th>Sisa Stok</th>
            </tr>
            <?php if (mysqli_num_rows($produk_menipis) === 0): ?>
              <tr><td colspan="3" style="text-align:center; color:var(--text-dim); padding:24px;">Semua stok produk aman &mdash; tidak ada yang menipis.</td></tr>
            <?php else: ?>
              <?php while ($row = mysqli_fetch_assoc($produk_menipis)): ?>
                <tr>
                  <td>
                    <div class="prod-name"><?= h($row['nama_produk']) ?></div>
                    <div class="prod-merek"><?= h($row['merek']) ?></div>
                  </td>
                  <td><?= h($row['nama_kategori']) ?></td>
                  <td><span class="pill stock-low"><span class="dot"></span><?= (int) $row['stok'] ?> unit</span></td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </table>
        </div>
      </div>

      <div class="card">
        <div class="card-body" style="display:flex; gap:14px; flex-wrap:wrap;">
          <a href="produk/tambah.php" class="btn btn-accent">+ Tambah Produk</a>
          <a href="kategori/index.php" class="btn btn-outline">Kelola Kategori</a>
        </div>
      </div>

    </main>
  </div>

  <footer class="app-footer">
    <span>&copy; <?= date('Y') ?> Sistem Informasi Toko Surya Elektronik</span>
  </footer>

</div>
<script src="assets/js/validasi.js"></script>
</body>
</html>
