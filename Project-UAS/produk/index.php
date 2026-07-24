<?php
include '../config/koneksi.php';
include '../config/middleware.php';
include '../config/fungsi.php';

$mode           = 'daftar';
$produk_detail  = null;

if (isset($_GET['lihat'])) {
    $id_lihat = (int) $_GET['lihat'];
    $query_detail = mysqli_query($koneksi, "
        SELECT p.*, k.nama_kategori
        FROM produk p
        JOIN kategori k ON k.id_kategori = p.id_kategori
        WHERE p.id_produk = '$id_lihat'
    ");
    $produk_detail = mysqli_fetch_assoc($query_detail);
    if ($produk_detail) {
        $mode = 'detail';
    }
}

$kata_kunci = isset($_GET['cari']) ? trim($_GET['cari']) : '';

if ($mode === 'daftar') {
    $kata_kunci_aman = bersihkan($koneksi, $kata_kunci);

    $where = "";
    if ($kata_kunci !== '') {
        $where = "WHERE p.nama_produk LIKE '%$kata_kunci_aman%'
                  OR p.merek LIKE '%$kata_kunci_aman%'
                  OR k.nama_kategori LIKE '%$kata_kunci_aman%'";
    }

    $per_halaman_pilihan = [5, 10];
    $per_halaman = isset($_GET['per_halaman']) && in_array((int) $_GET['per_halaman'], $per_halaman_pilihan)
        ? (int) $_GET['per_halaman']
        : 10;

    $halaman_sekarang = isset($_GET['halaman']) ? max(1, (int) $_GET['halaman']) : 1;

    $total_data_query = mysqli_query($koneksi, "
        SELECT COUNT(*) AS jumlah
        FROM produk p
        JOIN kategori k ON k.id_kategori = p.id_kategori
        $where
    ");
    $total_data     = mysqli_fetch_assoc($total_data_query)['jumlah'];
    $total_halaman  = max(1, ceil($total_data / $per_halaman));
    $halaman_sekarang = min($halaman_sekarang, $total_halaman);
    $offset = ($halaman_sekarang - 1) * $per_halaman;


    $query = mysqli_query($koneksi, "
        SELECT p.*, k.nama_kategori
        FROM produk p
        JOIN kategori k ON k.id_kategori = p.id_kategori
        $where
        ORDER BY p.id_produk DESC
        LIMIT $offset, $per_halaman
    ");
}

function buat_link_halaman($nomor, $kata_kunci, $per_halaman) {
    $params = ['halaman' => $nomor, 'per_halaman' => $per_halaman];
    if ($kata_kunci !== '') { $params['cari'] = $kata_kunci; }
    return '?' . http_build_query($params);
}

$nama_user = $_SESSION['nama_lengkap'] ?? ($_SESSION['username'] ?? 'Admin');
$inisial   = strtoupper(substr($nama_user, 0, 1));

$judul_halaman    = $mode === 'detail' ? $produk_detail['nama_produk'] : 'Data Produk';
$subjudul_halaman = $mode === 'detail' ? 'Detail Produk' : 'Kelola daftar produk HP, laptop, dan PC';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($judul_halaman) ?> &mdash; Surya Elektronik</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-shell">

  <header class="topbar">
    <div>
      <h1><?= h($judul_halaman) ?></h1>
      <div class="subtitle"><?= h($subjudul_halaman) ?></div>
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
<?php if ($mode === 'detail'): ?>

  <div class="card">
    <div class="card-head">
      <h2 style="font-size:15px;">Detail Produk</h2>
      <a href="index.php" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar</a>
    </div>
    <div class="card-body">
      <div class="detail-grid">

        <div>
          <?php if (!empty($produk_detail['gambar']) && file_exists('../uploads/' . $produk_detail['gambar'])): ?>
            <img src="../uploads/<?= h($produk_detail['gambar']) ?>" class="detail-photo" alt="<?= h($produk_detail['nama_produk']) ?>">
          <?php else: ?>
            <div class="detail-photo placeholder">Belum ada foto</div>
          <?php endif; ?>
        </div>

        <div>
          <div class="detail-kategori">
            <span class="pill"><span class="dot"></span><?= h($produk_detail['nama_kategori']) ?></span>
          </div>
          <h2 class="detail-title"><?= h($produk_detail['nama_produk']) ?></h2>
          <div class="detail-merek"><?= h($produk_detail['merek']) ?></div>

          <div class="detail-price mono"><?= format_rupiah($produk_detail['harga']) ?></div>

          <div class="detail-meta">
            <span class="pill <?= $produk_detail['stok'] < 5 ? 'stock-low' : '' ?>">
              <span class="dot"></span><?= (int) $produk_detail['stok'] ?> unit tersedia
            </span>
          </div>

          <div class="detail-section-title">Spesifikasi</div>
          <div class="detail-text"><?= nl2br(h($produk_detail['spesifikasi'] ?: '-')) ?></div>

          <div class="detail-section-title">Deskripsi</div>
          <div class="detail-text"><?= nl2br(h($produk_detail['deskripsi'] ?: '-')) ?></div>

          <div class="form-actions" style="margin-top:22px;">
            <a href="edit.php?id=<?= (int) $produk_detail['id_produk'] ?>" class="btn btn-accent">Edit Produk</a>
            <a href="hapus.php?id=<?= (int) $produk_detail['id_produk'] ?>"
               class="btn btn-danger-outline btn-hapus"
               data-nama="<?= h($produk_detail['nama_produk']) ?>">Hapus Produk</a>
          </div>
        </div>

      </div>
    </div>
  </div>

<?php else: ?>

<?php if (isset($_GET['sukses'])): ?>
  <div class="alert alert-success">
    <?php
      $pesan_sukses = [
        'tambah' => 'Produk baru berhasil ditambahkan.',
        'edit'   => 'Perubahan produk berhasil disimpan.',
        'hapus'  => 'Produk berhasil dihapus.',
      ];
      echo h($pesan_sukses[$_GET['sukses']] ?? 'Berhasil.');
    ?>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-head">
    <form class="search-form" method="GET">
      <input type="text" name="cari" class="input" placeholder="Cari nama produk, merek, atau kategori..."
             value="<?= h($kata_kunci) ?>" style="width:260px;">
      <select name="per_halaman" class="input" onchange="this.form.submit()" title="Jumlah data per halaman">
        <?php foreach ($per_halaman_pilihan as $pilihan): ?>
          <option value="<?= $pilihan ?>" <?= $per_halaman === $pilihan ? 'selected' : '' ?>>
            <?= $pilihan ?> / halaman
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-outline">Cari</button>
      <?php if ($kata_kunci !== ''): ?>
        <a href="index.php" class="btn btn-outline">Reset</a>
      <?php endif; ?>
    </form>
    <a href="tambah.php" class="btn btn-accent">+ Tambah Produk</a>
  </div>

  <div class="table-wrap">
    <table class="data-table">
      <tr>
        <th>Produk</th>
        <th>Kategori</th>
        <th>Spesifikasi</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Aksi</th>
      </tr>
      <?php if (mysqli_num_rows($query) === 0): ?>
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <div class="icon">&#128230;</div>
              <?php if ($kata_kunci !== ''): ?>
                Tidak ada produk yang cocok dengan pencarian "<?= h($kata_kunci) ?>".
              <?php else: ?>
                Belum ada data produk. Klik "Tambah Produk" untuk mulai menambahkan.
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($query)): ?>
          <tr>
            <td>
              <a href="index.php?lihat=<?= (int) $row['id_produk'] ?>" class="prod-link">
                <?php if (!empty($row['gambar']) && file_exists('../uploads/' . $row['gambar'])): ?>
                  <img src="../uploads/<?= h($row['gambar']) ?>" class="prod-thumb" alt="<?= h($row['nama_produk']) ?>">
                <?php else: ?>
                  <div class="prod-thumb placeholder">N/A</div>
                <?php endif; ?>
                <div>
                  <div class="prod-name"><?= h($row['nama_produk']) ?></div>
                  <div class="prod-merek"><?= h($row['merek']) ?></div>
                </div>
              </a>
            </td>
            <td><span class="pill"><span class="dot"></span><?= h($row['nama_kategori']) ?></span></td>
            <td style="max-width:220px; color:var(--text-dim); font-size:12.5px;">
              <?= h(mb_strimwidth($row['spesifikasi'] ?? '-', 0, 60, '...')) ?>
            </td>
            <td class="mono"><?= format_rupiah($row['harga']) ?></td>
            <td>
              <span class="pill <?= $row['stok'] < 5 ? 'stock-low' : '' ?>">
                <span class="dot"></span><?= (int) $row['stok'] ?>
              </span>
            </td>
            <td class="actions-cell">
              <a href="edit.php?id=<?= (int) $row['id_produk'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="hapus.php?id=<?= (int) $row['id_produk'] ?>"
                 class="btn btn-danger-outline btn-sm btn-hapus"
                 data-nama="<?= h($row['nama_produk']) ?>">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php endif; ?>
    </table>
  </div>

  <div class="pagination">
    <span class="page-info">
      Menampilkan <?= $total_data === 0 ? 0 : $offset + 1 ?>&ndash;<?= min($offset + $per_halaman, $total_data) ?>
      dari <?= (int) $total_data ?> produk
    </span>

    <?php if ($halaman_sekarang <= 1): ?>
      <span class="disabled">&laquo;</span>
    <?php else: ?>
      <a href="<?= buat_link_halaman($halaman_sekarang - 1, $kata_kunci, $per_halaman) ?>">&laquo;</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
      <?php if ($i === $halaman_sekarang): ?>
        <span class="current"><?= $i ?></span>
      <?php else: ?>
        <a href="<?= buat_link_halaman($i, $kata_kunci, $per_halaman) ?>"><?= $i ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <?php if ($halaman_sekarang >= $total_halaman): ?>
      <span class="disabled">&raquo;</span>
    <?php else: ?>
      <a href="<?= buat_link_halaman($halaman_sekarang + 1, $kata_kunci, $per_halaman) ?>">&raquo;</a>
    <?php endif; ?>
  </div>
</div>

<?php endif; ?>

    </main>
  </div>

  <footer class="app-footer">
    <span>&copy; <?= date('Y') ?> Sistem Informasi Toko Elektronik</span>
  </footer>

</div>
<script src="../assets/js/validasi.js"></script>
</body>
</html>
