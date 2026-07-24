<?php
$base = '../';
include '../config/koneksi.php';
include '../config/middleware.php';

$id = (int) ($_GET['id'] ?? 0);

$query_cek = mysqli_query($koneksi, "
    SELECT k.nama_kategori, COUNT(p.id_produk) AS jumlah_produk
    FROM kategori k
    LEFT JOIN produk p ON p.id_kategori = k.id_kategori
    WHERE k.id_kategori = '$id'
    GROUP BY k.id_kategori
");
$data_kategori = mysqli_fetch_assoc($query_cek);

if (!$data_kategori) {
    header("Location: index.php");
    exit();
}

if ((int) $data_kategori['jumlah_produk'] > 0) {
    header("Location: index.php?gagal=1"
        . "&nama=" . urlencode($data_kategori['nama_kategori'])
        . "&jumlah=" . (int) $data_kategori['jumlah_produk']);
    exit();
}

$query = "DELETE FROM kategori WHERE id_kategori = '$id'";

if (mysqli_query($koneksi, $query)) {
    header("Location: index.php?sukses=hapus");
} else {
    header("Location: index.php?gagal=1&nama=" . urlencode($data_kategori['nama_kategori']));
}
exit();
?>
