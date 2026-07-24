<?php
include '../config/koneksi.php';
include '../config/middleware.php';
include '../config/fungsi.php';

$query = mysqli_query($koneksi, "
    SELECT k.*, COUNT(p.id_produk) AS jumlah_produk
    FROM kategori k
    LEFT JOIN produk p ON p.id_kategori = k.id_kategori
    GROUP BY k.id_kategori
    ORDER BY k.nama_kategori ASC
");

$nama_user = $_SESSION['nama_lengkap'] ?? ($_SESSION['username'] ?? 'Admin');
$inisial   = strtoupper(substr($nama_user, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h("Kategori Produk") ?> &mdash; Surya Elektronik</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-shell">

  <header class="topbar">
    <div>
      <h1><?= h("Kategori Produk") ?></h1>
      <div class="subtitle"><?= h("Kelola jenis produk: Handphone, Laptop, PC") ?></div>
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
<?php if (isset($_GET['sukses'])): ?>
  <div class="alert alert-success">
    <?php
      $pesan_sukses = [
        'tambah' => 'Kategori baru berhasil ditambahkan.',
        'edit'   => 'Perubahan kategori berhasil disimpan.',
        'hapus'  => 'Kategori berhasil dihapus.',
      ];
      echo h($pesan_sukses[$_GET['sukses']] ?? 'Berhasil.');
    ?>
  </div>
<?php endif; ?>

<?php if (isset($_GET['gagal'])): ?>
  <div class="alert alert-error">
    <?php if (!empty($_GET['nama'])): ?>
      Kategori "<strong><?= h($_GET['nama']) ?></strong>" tidak bisa dihapus karena masih dipakai oleh
      <?= (int) ($_GET['jumlah'] ?? 0) ?> produk. Pindahkan atau hapus produknya terlebih dahulu.
    <?php else: ?>
      Kategori tidak bisa dihapus karena masih dipakai oleh produk. Pindahkan atau hapus produknya terlebih dahulu.
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-head">
    <h2 style="font-size:15px;">Daftar Kategori</h2>
    <a href="tambah.php" class="btn btn-accent">+ Tambah Kategori</a>
  </div>

  <div class="table-wrap">
    <table class="data-table">
      <tr>
        <th>Nama Kategori</th>
        <th>Jumlah Produk</th>
        <th>Aksi</th>
      </tr>
      <?php if (mysqli_num_rows($query) === 0): ?>
        <tr><td colspan="3"><div class="empty-state">Belum ada kategori.</div></td></tr>
      <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($query)): ?>
          <tr>
            <td class="prod-name"><?= h($row['nama_kategori']) ?></td>
            <td class="mono"><?= (int) $row['jumlah_produk'] ?> produk</td>
            <td class="actions-cell">
              <a href="edit.php?id=<?= (int) $row['id_kategori'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="hapus.php?id=<?= (int) $row['id_kategori'] ?>"
                 class="btn btn-danger-outline btn-sm btn-hapus"
                 data-nama="<?= h($row['nama_kategori']) ?>"
                 data-jumlah="<?= (int) $row['jumlah_produk'] ?>">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php endif; ?>
    </table>
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
