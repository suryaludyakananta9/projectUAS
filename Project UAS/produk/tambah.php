<?php
include '../config/koneksi.php';
include '../config/middleware.php';
include '../config/fungsi.php';

$error_message = "";

$form = [
    'id_kategori' => '', 'merek' => '', 'nama_produk' => '',
    'spesifikasi' => '', 'harga' => '', 'stok' => '', 'deskripsi' => '',
];

if (isset($_POST['simpan'])) {
    $form['id_kategori'] = $_POST['id_kategori'];
    $form['merek']       = $_POST['merek'];
    $form['nama_produk'] = $_POST['nama_produk'];
    $form['spesifikasi'] = $_POST['spesifikasi'];
    $form['harga']       = $_POST['harga'];
    $form['stok']        = $_POST['stok'];
    $form['deskripsi']   = $_POST['deskripsi'];

    $nama_file   = $_FILES['gambar']['name'];
    $ukuran_file = $_FILES['gambar']['size'];
    $error_file  = $_FILES['gambar']['error'];
    $tmp_name    = $_FILES['gambar']['tmp_name'];

    if ($error_file === 4) {
        $error_message = "Pilih foto produk terlebih dahulu!";
    } else {
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi_file  = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if (!in_array($ekstensi_file, $ekstensi_valid)) {
            $error_message = "Format file harus JPG, JPEG, atau PNG!";
        } elseif ($ukuran_file > 2000000) {
            $error_message = "Ukuran file terlalu besar! Maksimal 2MB.";
        } else {
            $nama_file_baru = uniqid('produk_') . '.' . $ekstensi_file;

            $folder_uploads = '../uploads';
            if (!is_dir($folder_uploads)) {
                mkdir($folder_uploads, 0755, true);
            }

            $folder_tujuan = $folder_uploads . '/' . $nama_file_baru;

            if (move_uploaded_file($tmp_name, $folder_tujuan)) {
                $id_kategori = (int) $form['id_kategori'];
                $merek       = bersihkan($koneksi, $form['merek']);
                $nama_produk = bersihkan($koneksi, $form['nama_produk']);
                $spesifikasi = bersihkan($koneksi, $form['spesifikasi']);
                $harga       = (float) str_replace(['.', ','], '', $form['harga']);
                $stok        = (int) $form['stok'];
                $deskripsi   = bersihkan($koneksi, $form['deskripsi']);

                $query = "INSERT INTO produk
                    (id_kategori, merek, nama_produk, spesifikasi, harga, stok, gambar, deskripsi)
                    VALUES
                    ('$id_kategori', '$merek', '$nama_produk', '$spesifikasi', '$harga', '$stok', '$nama_file_baru', '$deskripsi')";

                if (mysqli_query($koneksi, $query)) {
                    header("Location: index.php?sukses=tambah");
                    exit();
                } else {
                    $error_message = "Gagal menyimpan data: " . mysqli_error($koneksi);
                }
            } else {
                $error_message = "Gagal mengunggah file ke server.";
            }
        }
    }
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
<title><?= h("Tambah Produk") ?> &mdash; Surya Elektronik</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-shell">

  <header class="topbar">
    <div>
      <h1><?= h("Tambah Produk") ?></h1>
      <div class="subtitle"><?= h("Isi data produk baru") ?></div>
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

    <?php if (!empty($error_message)): ?>
      <div class="alert alert-error"><?= h($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form-validasi" novalidate>
      <div class="form-grid">

        <div class="field">
          <label for="id_kategori">Kategori</label>
          <select id="id_kategori" name="id_kategori" class="input" required>
            <option value="">-- Pilih Kategori --</option>
            <?php while ($k = mysqli_fetch_assoc($daftar_kategori)): ?>
              <option value="<?= (int) $k['id_kategori'] ?>" <?= ($form['id_kategori'] == $k['id_kategori']) ? 'selected' : '' ?>>
                <?= h($k['nama_kategori']) ?>
              </option>
            <?php endwhile; ?>
          </select>
          <div class="error-text"></div>
        </div>

        <div class="field">
          <label for="merek">Merek</label>
          <input type="text" id="merek" name="merek" class="input" placeholder="Contoh: Samsung, ASUS, HP"
                 value="<?= h($form['merek']) ?>" required>
          <div class="error-text"></div>
        </div>

        <div class="field full">
          <label for="nama_produk">Nama Produk</label>
          <input type="text" id="nama_produk" name="nama_produk" class="input" placeholder="Contoh: Galaxy A55 5G"
                 value="<?= h($form['nama_produk']) ?>" required>
          <div class="error-text"></div>
        </div>

        <div class="field">
          <label for="harga">Harga (Rp)</label>
          <input type="number" id="harga" name="harga" class="input" min="0" step="1000"
                 value="<?= h($form['harga']) ?>" required>
          <div class="error-text"></div>
        </div>

        <div class="field">
          <label for="stok">Stok</label>
          <input type="number" id="stok" name="stok" class="input" min="0"
                 value="<?= h($form['stok']) ?>" required>
          <div class="error-text"></div>
        </div>

        <div class="field full">
          <label for="spesifikasi">Spesifikasi</label>
          <textarea id="spesifikasi" name="spesifikasi" class="input" placeholder="Contoh: RAM 8GB, Storage 256GB, Layar 6.6&quot;" required><?= h($form['spesifikasi']) ?></textarea>
          <div class="error-text"></div>
        </div>

        <div class="field full">
          <label for="deskripsi">Deskripsi</label>
          <textarea id="deskripsi" name="deskripsi" class="input" placeholder="Deskripsi singkat produk"><?= h($form['deskripsi']) ?></textarea>
        </div>

        <div class="field full">
          <label for="gambar">Foto Produk</label>
          <input type="file" id="gambar" name="gambar" class="input validasi-gambar" accept=".jpg,.jpeg,.png" required>
          <div class="hint">Format JPG/JPEG/PNG, maksimal 2MB.</div>
          <div class="error-text"></div>
        </div>

      </div>

      <div class="form-actions">
        <button type="submit" name="simpan" class="btn btn-accent">Simpan Produk</button>
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
