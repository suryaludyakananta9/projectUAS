<?php
// Kumpulan fungsi bantuan kecil yang dipakai di banyak halaman,
// supaya kode tidak berulang-ulang (DRY).

// Format angka menjadi Rupiah, contoh: 5999000 -> "Rp 5.999.000"
function format_rupiah($angka) {
    return "Rp " . number_format((float) $angka, 0, ',', '.');
}

// Membungkus htmlspecialchars supaya output ke HTML aman dari XSS sederhana
function h($teks) {
    return htmlspecialchars($teks ?? '', ENT_QUOTES, 'UTF-8');
}

// Membersihkan input string sebelum dipakai di query mysqli
// (melanjutkan pola mysqli procedural pada source asli, hanya
// ditambah pembersihan dasar supaya tidak rentan SQL Injection)
function bersihkan($koneksi, $data) {
    return mysqli_real_escape_string($koneksi, trim($data));
}
?>
