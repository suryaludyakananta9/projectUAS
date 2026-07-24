<?php
include '../config/koneksi.php';
include '../config/middleware.php';
include '../config/fungsi.php';

$id = (int) ($_GET['id'] ?? 0);

$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = '$id'");
$data  = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: index.php");
    exit();
}

$daftar_kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

$nama_user = $_SESSION['nama_lengkap'] ?? ($_SESSION['username'] ?? 'Admin');
$inisial   = strtoupper(substr($nama_user, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h("Edit Produk") ?> &mdash; Surya Elektronik</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-shell">

  <header class="topbar">
    <div>
      <h1><?= h("Edit Produk") ?></h1>
      <div class="subtitle"><?= h($data['nama_produk']) ?></div>
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
        <a href="../produk/index.php" class="active">
          <span class="icon">&#9707;</span> Data Produk
        </a>
        <a href="../kategori/index.php">
          <span class="icon">&#9776;</span> Kategori
        </a>
      </nav>
    </aside>
    <main class="content">
<div class="card">
  <div class="card-body">

    <form action="proses_edit.php" method="POST" enctype="multipart/form-data" class="form-validasi" novalidate>
      <input type="hidden" name="id" value="<?= (int) $data['id_produk'] ?>">
      <input type="hidden" name="gambar_lama" value="<?= h($data['gambar']) ?>">

      <div class="form-grid">

        <div class="field">
          <label for="id_kategori">Kategori</label>
          <select id="id_kategori" name="id_kategori" class="input" required>
            <?php while ($k = mysqli_fetch_assoc($daftar_kategori)): ?>
              <option value="<?= (int) $k['id_kategori'] ?>" <?= ($data['id_kategori'] == $k['id_kategori']) ? 'selected' : '' ?>>
                <?= h($k['nama_kategori']) ?>
              </option>
            <?php endwhile; ?>
          </select>
          <div class="error-text"></div>
        </div>

        <div class="field">
          <label for="merek">Merek</label>
          <input type="text" id="merek" name="merek" class="input" value="<?= h($data['merek']) ?>" required>
          <div class="error-text"></div>
        </div>

        <div class="field full">
          <label for="nama_produk">Nama Produk</label>
          <input type="text" id="nama_produk" name="nama_produk" class="input" value="<?= h($data['nama_produk']) ?>" required>
          <div class="error-text"></div>
        </div>

        <div class="field">
          <label for="harga">Harga (Rp)</label>
          <input type="number" id="harga" name="harga" class="input" min="0" step="1000" value="<?= (int) $data['harga'] ?>" required>
          <div class="error-text"></div>
        </div>

        <div class="field">
          <label for="stok">Stok</label>
          <input type="number" id="stok" name="stok" class="input" min="0" value="<?= (int) $data['stok'] ?>" required>
          <div class="error-text"></div>
        </div>

        <div class="field full">
          <label for="spesifikasi">Spesifikasi</label>
          <textarea id="spesifikasi" name="spesifikasi" class="input" required><?= h($data['spesifikasi']) ?></textarea>
          <div class="error-text"></div>
        </div>

        <div class="field full">
          <label for="deskripsi">Deskripsi</label>
          <textarea id="deskripsi" name="deskripsi" class="input"><?= h($data['deskripsi']) ?></textarea>
        </div>

        <div class="field full">
          <label for="gambar">Foto Produk</label>
          <?php if (!empty($data['gambar']) && file_exists('../uploads/' . $data['gambar'])): ?>
            <div class="current-image">
              <img src="../uploads/<?= h($data['gambar']) ?>" alt="Foto saat ini">
              <span class="hint">Foto saat ini. Unggah file baru untuk menggantinya.</span>
            </div>
          <?php else: ?>
            <div class="hint" style="margin-bottom:8px;">Belum ada foto produk.</div>
          <?php endif; ?>
          <input type="file" id="gambar" name="gambar" class="input validasi-gambar" accept=".jpg,.jpeg,.png">
          <div class="hint">Kosongkan jika tidak ingin mengganti foto. Format JPG/JPEG/PNG, maksimal 2MB.</div>
          <div class="error-text"></div>
        </div>

      </div>

      <div class="form-actions">
        <button type="submit" name="update" class="btn btn-accent">Simpan Perubahan</button>
        <a href="index.php" class="btn btn-outline">Batal</a>
      </div>
    </form>

  </div>
</div>

    </main>
  </div>

  <footer class="app-footer">
    <span>&copy; <?= date('Y') ?> Sistem Informasi Toko Surya Elektronik</span>
  </footer>

</div>
<script src="../assets/js/validasi.js"></script>
</body>
</html>
