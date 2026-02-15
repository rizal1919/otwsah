<?php
require_once '../../config/db.php';

if (isset($_SESSION['user'])) {
    header("Location: " . url('views/dashboard.php'));
    exit;
}

$error = null;

if (isset($_POST['login'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    $username = mysqli_real_escape_string($koneksi, $username);
    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' OR email = '$username'");

    if (mysqli_num_rows($cek_user) === 1) {
        $row = mysqli_fetch_assoc($cek_user);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = [
                'id_user' => $row['id_user'],
                'fullname' => $row['fullname'], 
                'username' => $row['username'],
                'email' => $row['email'],
                'role' => $row['role']
            ];
            header("Location: " . url('views/dashboard.php'));
            exit;
        } else {
            $error = "Kata sandi salah.";
        }
    } else {
        $error = "Akun tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk || OtwSah</title>
    <link rel="shortcut icon" href="<?= url('assets/images/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/vendors/css/vendors.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/theme.min.css') ?>">
    
    <style>
        /* === PERBAIKAN CSS BACKGROUND === */
        body {
            overflow: hidden; /* Hilangkan scrollbar ganda */
        }
        
        .auth-cover-wrapper {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        /* Container gambar diatur agar di BELAKANG form */
        .auth-cover-content-inner {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0; /* Level paling bawah */
        }

        .wedding-bg {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Agar gambar tidak gepeng */
            object-position: center;
        }

        /* Overlay Putih Transparan supaya Form Lebih Jelas */
        .auth-cover-content-inner::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.3); /* Putih transparan 30% */
            z-index: 1;
        }

        /* Container Form Login diatur agar di DEPAN gambar */
        .auth-cover-sidebar-inner {
            position: absolute;
            top: 0;
            right: 0; /* Form nempel kanan (opsional, bawaan template biasanya kanan/tengah) */
            height: 100%;
            z-index: 10; /* Level paling atas (di atas gambar) */
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9); /* Latar belakang form agak putih solid */
            box-shadow: -5px 0 20px rgba(0,0,0,0.05); /* Bayangan halus */
            width: 500px; /* Lebar area form */
            max-width: 100%;
        }

        /* Responsif untuk Mobile */
        @media (max-width: 768px) {
            .auth-cover-sidebar-inner {
                width: 100%;
                background: rgba(255, 255, 255, 0.95);
            }
        }
    </style>
</head>
<body>
    <main class="auth-cover-wrapper">
        
        <div class="auth-cover-content-inner">
                                <img src="https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=2070&auto=format&fit=crop" class="img-fluid wedding-bg" alt="Wedding Couple">

        </div>

        <div class="auth-cover-sidebar-inner">
            <div class="auth-cover-card-wrapper p-4 p-md-5 w-100">
                <div class="mb-5 text-center text-md-start">
                    <img src="<?= url('assets/images/logo-abbr.png') ?>" alt="" class="img-fluid mb-3" style="max-height: 50px;">
                    <h2 class="fs-20 fw-bolder mb-2">Selamat Datang Kembali</h2>
                    <p class="fs-12 fw-medium text-muted">Masuk untuk melanjutkan desain undanganmu di <strong>OtwSah</strong>.</p>
                </div>
                
                <?php if($error): ?>
                    <div class="alert alert-danger fs-12 mb-4"><?= $error ?></div>
                <?php endif; ?>

                <form action="" method="POST" class="w-100">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Username / Email</label>
                        <input type="text" name="username" value="e@gmail.com" class="form-control" placeholder="Ketik username atau email kamu" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kata Sandi</label>
                        <input type="password" name="password" value="zalz.1919" class="form-control" placeholder="Masukkan kata sandi" required>
                    </div>
                    
                    <div class="mt-5">
                        <button type="submit" name="login" class="btn btn-lg btn-primary w-100">Masuk</button>
                    </div>
                </form>
                
                <div class="mt-5 text-muted text-center fs-12">
                    <span>Belum punya akun?</span>
                    <a href="<?= url('views/authentication/register.php') ?>" class="fw-bold text-primary">Buat Akun Baru</a>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= url('assets/vendors/js/vendors.min.js') ?>"></script>
    <script src="<?= url('assets/js/common-init.min.js') ?>"></script>
</body>
</html>