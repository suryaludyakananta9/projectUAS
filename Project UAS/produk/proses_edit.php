<?php
$base = '../';
include '../config/koneksi.php';
include '../config/middleware.php';
include '../config/fungsi.php';

if (!isset($_POST['update'])) {
    header("Location: index.php");
    exit();
}

$id          = (int) $_POST['id'];
$id_kategori = (int) $_POST['id_kategori'];
$merek       = bersihkan($koneksi, $_POST['merek']);
$nama_produk = bersihkan($koneksi, $_POST['nama_produk']);
$spesifikasi = bersihkan($koneksi, $_POST['spesifikasi']);
$harga       = (float) str_replace(['.', ','], '', $_POST['harga']);
$stok        = (int) $_POST['stok'];
$deskripsi   = bersihkan($koneksi, $_POST['deskripsi']);

$nama_file_final = $_POST['gambar_lama'];

$nama_file   = $_FILES['gambar']['name'];
$ukuran_file = $_FILES['gambar']['size'];
$error_file  = $_FILES['gambar']['error'];
$tmp_name    = $_FILES['gambar']['tmp_name'];

if ($error_file !== 4) {
    $ekstensi_valid = ['jpg', 'jpeg', 'png'];
    $ekstensi_file  = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

    if (!in_array($ekstensi_file, $ekstensi_valid)) {
        echo "<script>alert('Format file harus JPG, JPEG, atau PNG!'); window.history.back();</script>";
        exit;
    }

    if ($ukuran_file > 2000000) {
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB.'); window.history.back();</script>";
        exit;
    }

    $nama_file_baru = uniqid('produk_') . '.' . $ekstensi_file;
    $folder_uploads = '../uploads';
    if (!is_dir($folder_uploads)) {
        mkdir($folder_uploads, 0755, true);
    }
    $folder_tujuan = $folder_uploads . '/' . $nama_file_baru;

    if (move_uploaded_file($tmp_name, $folder_tujuan)) {
        if (!empty($nama_file_final) && file_exists($folder_uploads . '/' . $nama_file_final)) {
            unlink($folder_uploads . '/' . $nama_file_final);
        }
        $nama_file_final = $nama_file_baru;
    }
}

$query_update = "UPDATE produk SET
    id_kategori = '$id_kategori',
    merek = '$merek',
    nama_produk = '$nama_produk',
    spesifikasi = '$spesifikasi',
    harga = '$harga',
    stok = '$stok',
    gambar = '$nama_file_final',
    deskripsi = '$deskripsi'
    WHERE id_produk = '$id'";

if (mysqli_query($koneksi, $query_update)) {
    header("Location: index.php?sukses=edit");
    exit();
} else {
    echo "Gagal memperbarui data: " . mysqli_error($koneksi);
}
?>
