<?php
// Pastikan session aktif untuk cek role
if (session_status() === PHP_SESSION_NONE) session_start();
$user_role = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : 'user';
?>

<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="<?= url('views/dashboard.php') ?>" class="b-brand">
                <img src="<?= url('assets/images/logo-full.png') ?>" alt="" class="logo logo-lg">
                <img src="<?= url('assets/images/logo-abbr.png') ?>" alt="" class="logo logo-sm">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption"><label>Menu Utama</label></li>
                
                <li class="nxl-item">
                    <a href="<?= url('views/dashboard.php') ?>" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <?php if($user_role == 'admin'): ?>
                    
                    <li class="nxl-item nxl-caption"><label>Master Data</label></li>
                    
                    <li class="nxl-item">
                        <a href="<?= url('views/users.php') ?>" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Kelola Pengguna</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <a href="<?= url('views/tema/index.php') ?>" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-layout"></i></span>
                            <span class="nxl-mtext">Katalog Tema</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <a href="<?= url('views/musik.php') ?>" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-music"></i></span>
                            <span class="nxl-mtext">Musik Sistem</span>
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-mail"></i></span>
                            <span class="nxl-mtext">Undangan</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="<?= url('views/undangan/undangan_baru.php') ?>">Buat Baru</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="<?= url('views/undangan_saya.php') ?>">List Undangan</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item">
                        <a href="<?= url('views/tema_katalog.php') ?>" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-grid"></i></span>
                            <span class="nxl-mtext">Lihat Tema</span>
                        </a>
                    </li>

                <?php endif; ?>

                <li class="nxl-item nxl-caption"><label>Akun</label></li>
                <li class="nxl-item">
                    <a href="<?= url('views/authentication/logout.php') ?>" class="nxl-link text-danger">
                        <span class="nxl-micon"><i class="feather-log-out"></i></span>
                        <span class="nxl-mtext">Keluar</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>