<?php
$base = '../';
include '../config/koneksi.php';
include '../config/middleware.php';

$id = (int) ($_GET['id'] ?? 0);

$query_lama = mysqli_query($koneksi, "SELECT gambar FROM produk WHERE id_produk = '$id'");
$data_lama  = mysqli_fetch_assoc($query_lama);

$query = "DELETE FROM produk WHERE id_produk = '$id'";

if (mysqli_query($koneksi, $query)) {
    if ($data_lama && !empty($data_lama['gambar']) && file_exists('../uploads/' . $data_lama['gambar'])) {
        unlink('../uploads/' . $data_lama['gambar']);
    }
    header("Location: index.php?sukses=hapus");
    exit();
}

header("Location: index.php");
exit();
?>
