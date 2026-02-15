<?php
// Pastikan db.php terpanggil (jika file ini diakses langsung)
require_once __DIR__ . '/../config/db.php';

// Ambil 3 Tema Terbaru dari Database untuk Preview
$themes = query("SELECT * FROM themes ORDER BY id_tema DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OtwSah - Sebar Undangan Pernikahan Digital</title>
    
    <link rel="shortcut icon" href="<?= url('assets/images/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/vendors/css/vendors.min.css') ?>">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.css">

    <style>
        /* --- CUSTOM LANDING STYLE (Duralux Vibe) --- */
        :root {
            --primary: #3461ff; /* Warna Duralux */
            --dark: #111c43;
            --light: #f5f6fa;
        }

        body { font-family: 'Inter', sans-serif; overflow-x: hidden; color: #6c757d; }
        
        h1, h2, h3, h4, h5 { color: var(--dark); font-weight: 700; }

        /* Navbar */
        .navbar { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 15px 0; box-shadow: 0 2px 15px rgba(0,0,0,0.05); }
        .nav-link { font-weight: 600; color: var(--dark); margin: 0 10px; }
        .nav-link:hover { color: var(--primary); }

        /* Hero Section */
        .hero-section {
            padding: 150px 0 100px;
            background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-img-wrapper {
            position: relative;
            z-index: 2;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(52, 97, 255, 0.15);
            transform: rotate(-2deg);
            transition: transform 0.5s;
        }
        .hero-img-wrapper:hover { transform: rotate(0deg); }

        /* Counter Section */
        .counter-box {
            background: white;
            padding: 40px 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.3s;
        }
        .counter-box:hover { transform: translateY(-10px); }
        .counter-num { font-size: 2.5rem; font-weight: 800; color: var(--primary); }

        /* Feature Section */
        .feature-icon {
            width: 60px; height: 60px;
            background: rgba(52, 97, 255, 0.1);
            color: var(--primary);
            border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Update CSS ini di views/landing.php */
.floating-card {
    background: white;
    padding: 15px 20px;
    border-radius: 15px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    position: absolute;
    
    /* Z-INDEX WAJIB TINGGI AGAR DI DEPAN GAMBAR */
    z-index: 100; 
    
    min-width: 220px;
    bottom: 30px; 
    left: -20px; /* Geser sedikit biar gak terlalu minggir */
    animation: floatCard 4s ease-in-out infinite;
}

        /* Theme Card */
        .theme-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        .theme-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .theme-img { height: 250px; object-fit: cover; width: 100%; }

        /* Footer */
        footer { background: var(--dark); color: #aab0c6; padding: 60px 0 20px; }
        footer a { color: #aab0c6; text-decoration: none; }
        footer a:hover { color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="<?= url('assets/images/logo-abbr.png') ?>" alt="Logo" height="30">
                <span class="fw-bold text-dark fs-4">OtwSah</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#fitur">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tema">Tema</a></li>
                    <li class="nav-item"><a class="nav-link" href="#harga">Harga</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="<?= url('views/authentication/login.php') ?>" class="btn btn-outline-primary px-4 fw-bold rounded-pill">Masuk</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="<?= url('views/authentication/register.php') ?>" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">Daftar Gratis</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                    <span class="badge bg-soft-primary text-primary px-3 py-2 mb-3 rounded-pill fw-bold">#1 Platform Undangan Digital</span>
                    <h1 class="display-4 mb-4">Sebar Kabar Bahagia,<br>Tanpa Batas Jarak.</h1>
                    <p class="lead mb-5">Buat undangan pernikahan digital yang elegan, hemat biaya, dan mudah dibagikan via WhatsApp. Tersedia berbagai tema premium.</p>
                    <div class="d-flex gap-3">
                        <a href="<?= url('views/authentication/register.php') ?>" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg hover-scale">Buat Sekarang</a>
                        <a href="#tema" class="btn btn-white bg-white text-dark btn-lg px-4 rounded-pill shadow-sm border">Lihat Contoh</a>
                    </div>
                    <div class="mt-5 d-flex align-items-center gap-3">
                        <div class="d-flex">
                            <img src="<?= url('assets/images/avatar/1.png') ?>" class="rounded-circle border border-2 border-white" width="40" height="40" style="margin-right: -15px;">
                            <img src="<?= url('assets/images/avatar/2.png') ?>" class="rounded-circle border border-2 border-white" width="40" height="40" style="margin-right: -15px;">
                            <img src="<?= url('assets/images/avatar/3.png') ?>" class="rounded-circle border border-2 border-white" width="40" height="40" style="margin-right: -15px;">
                            <span class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center border border-2 border-white" style="width:40px; height:40px; font-size: 10px;">+2K</span>
                        </div>
                        <span class="fs-12 fw-bold">Pengantin telah bergabung</span>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0" data-aos="fade-left" data-aos-duration="1200">
                    <div class="hero-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?q=80&w=1000&auto=format&fit=crop" alt="Aplikasi Undangan Digital" class="img-fluid rounded-4 shadow-lg w-100"></div>
                        <div class="bg-white p-3 rounded-4 floating-card shadow position-absolute d-none d-md-block" style="bottom: -30px; left: -30px; width: 200px;" data-aos="fade-up" data-aos-delay="400">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-soft-success text-success p-2 rounded-circle"><i class="feather-check"></i></div>
                                <div>
                                    <h6 class="mb-0 fs-14 fw-bold">RSVP Realtime</h6>
                                    <small class="text-muted">Notifikasi Instan</small>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                    <div class="counter-box">
                        <div class="counter-num" data-val="1500">0</div>
                        <p class="mb-0 fw-bold text-dark">Undangan Dibuat</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="counter-box">
                        <div class="counter-num" data-val="5200">0</div>
                        <p class="mb-0 fw-bold text-dark">Tamu Diundang</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="counter-box">
                        <div class="counter-num" data-val="49">0</div>
                        <p class="mb-0 fw-bold text-dark">Tema Premium</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="fitur" class="py-5" style="background-color: #f8faff;">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="text-primary fw-bold text-uppercase ls-2">Fitur Unggulan</span>
                <h2 class="mt-2">Semua yang Anda Butuhkan</h2>
                <p class="text-muted">Fitur lengkap untuk momen spesial Anda.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-white p-4 rounded-4 shadow-sm h-100 border border-light">
                        <div class="feature-icon"><i class="feather-layout"></i></div>
                        <h5>Desain Responsif</h5>
                        <p class="mb-0">Tampilan undangan menyesuaikan layar HP, Tablet, maupun Desktop dengan sempurna.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-white p-4 rounded-4 shadow-sm h-100 border border-light">
                        <div class="feature-icon"><i class="feather-music"></i></div>
                        <h5>Backsound Musik</h5>
                        <p class="mb-0">Pilih lagu romantis favorit Anda untuk mengiringi tamu saat membuka undangan.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-white p-4 rounded-4 shadow-sm h-100 border border-light">
                        <div class="feature-icon"><i class="feather-database"></i></div>
                        <h5>Buku Tamu Digital</h5>
                        <p class="mb-0">Tamu bisa memberikan ucapan dan konfirmasi kehadiran (RSVP) secara realtime.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="bg-white p-4 rounded-4 shadow-sm h-100 border border-light">
                        <div class="feature-icon"><i class="feather-gift"></i></div>
                        <h5>Amplop Digital</h5>
                        <p class="mb-0">Terima hadiah cashless via transfer bank atau E-Wallet dengan mudah.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="bg-white p-4 rounded-4 shadow-sm h-100 border border-light">
                        <div class="feature-icon"><i class="feather-map-pin"></i></div>
                        <h5>Peta Lokasi</h5>
                        <p class="mb-0">Integrasi Google Maps memudahkan tamu menemukan lokasi acara Anda.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="bg-white p-4 rounded-4 shadow-sm h-100 border border-light">
                        <div class="feature-icon"><i class="feather-share-2"></i></div>
                        <h5>Kirim via WhatsApp</h5>
                        <p class="mb-0">Bagikan link undangan ke ribuan kontak WhatsApp hanya dengan sekali klik.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="tema" class="py-5 bg-white">
        <div class="container py-5">
            <div class="row align-items-end mb-5">
                <div class="col-lg-8" data-aos="fade-right">
                    <h2 class="mb-2">Katalog Tema Pilihan</h2>
                    <p class="text-muted">Pilih desain yang sesuai dengan karakteristik pernikahanmu.</p>
                </div>
                <div class="col-lg-4 text-lg-end" data-aos="fade-left">
                    <a href="#" class="btn btn-outline-primary rounded-pill px-4">Lihat Semua Tema</a>
                </div>
            </div>

            <div class="row g-4">
                <?php if(empty($themes)): ?>
                    <div class="col-12 text-center text-muted">
                        <p>Belum ada tema yang diupload admin.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($themes as $theme): ?>
                    <div class="col-md-4" data-aos="zoom-in">
                        <div class="card theme-card h-100">
                            <img src="<?= url('assets/images/themes/' . $theme['preview_img']) ?>" class="card-img-top theme-img" alt="Tema">
                            <div class="card-body">
                                <h5 class="card-title"><?= $theme['nama_tema'] ?></h5>
                                <p class="card-text text-muted small">Kategori: Wedding</p>
                                <a href="<?= url('views/authentication/register.php') ?>" class="btn btn-sm btn-primary w-100 rounded-pill">Pakai Tema Ini</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="py-5" style="background: linear-gradient(45deg, #3461ff, #6c8dff);">
        <div class="container text-center py-5">
            <h2 class="text-white mb-4" data-aos="fade-up">Siap Membuat Undanganmu?</h2>
            <p class="text-white-50 mb-5 w-75 mx-auto" data-aos="fade-up" data-aos-delay="100">
                Bergabunglah dengan ribuan pengantin lainnya. Gratis untuk mencoba, upgrade kapan saja.
            </p>
            <a href="<?= url('views/authentication/register.php') ?>" class="btn btn-light btn-lg text-primary px-5 rounded-pill fw-bold shadow" data-aos="zoom-in" data-aos-delay="200">
                Buat Undangan Gratis
            </a>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <a class="d-flex align-items-center gap-2 mb-3" href="#">
                        <img src="<?= url('assets/images/logo-abbr.png') ?>" alt="Logo" height="30">
                        <span class="fw-bold text-white fs-4">OtwSah</span>
                    </a>
                    <p class="small">Platform pembuatan undangan pernikahan digital berbasis website. Praktis, Elegan, dan Hemat Biaya.</p>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-white mb-3">Produk</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Tema</a></li>
                        <li class="mb-2"><a href="#">Harga</a></li>
                        <li class="mb-2"><a href="#">Fitur</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-white mb-3">Dukungan</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Bantuan</a></li>
                        <li class="mb-2"><a href="#">Syarat & Ketentuan</a></li>
                        <li class="mb-2"><a href="#">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white mb-3">Kontak</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="feather-mail me-2"></i> halo@otwsah.com</li>
                        <li class="mb-2"><i class="feather-phone me-2"></i> +62 812 3456 7890</li>
                        <li class="mb-2"><i class="feather-map-pin me-2"></i> Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-5">
            <div class="text-center small">
                &copy; <?= date('Y') ?> OtwSah. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="<?= url('assets/vendors/js/vendors.min.js') ?>"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Init AOS (Animation)
        AOS.init({
            once: true,
            duration: 1000,
        });

        // Simple Counter Animation
        let valueDisplays = document.querySelectorAll(".counter-num");
        let interval = 2000;

        valueDisplays.forEach((valueDisplay) => {
            let startValue = 0;
            let endValue = parseInt(valueDisplay.getAttribute("data-val"));
            let duration = Math.floor(interval / endValue);
            let counter = setInterval(function () {
                startValue += 10; // Increment step
                if(startValue > endValue) startValue = endValue;
                valueDisplay.textContent = startValue + "+";
                if (startValue == endValue) {
                    clearInterval(counter);
                }
            }, duration < 10 ? 10 : duration); // Min duration cap
        });
    </script>
</body>
</html>