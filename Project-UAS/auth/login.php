<?php
session_start();
include '../config/koneksi.php';
include '../config/fungsi.php';

if (isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit();
}

$error_message = "";

if (isset($_POST['login'])) {
    $username = bersihkan($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    $user  = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['login']        = true;
        $_SESSION['username']     = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

        header("Location: ../index.php");
        exit();
    } else {
        $error_message = "Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Surya Elektronik</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="login-shell">
  <div class="login-card">
    <div class="brand">
      <span class="dot"></span>
      <strong>Surya Elektronik</strong>
    </div>
    <h1>Masuk ke Panel Admin</h1>
    <p class="lead">Kelola data produk HP, laptop, dan PC toko kamu.</p>

    <?php if (!empty($error_message)): ?>
      <div class="alert alert-error"><?= h($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-validasi" novalidate>
      <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" class="input" required autofocus>
        <div class="error-text"></div>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="input" required>
        <div class="error-text"></div>
      </div>
      <button type="submit" name="login" class="btn btn-primary">Masuk</button>
    </form>
  </div>
</div>
<script src="../assets/js/validasi.js"></script>
</body>
</html>
