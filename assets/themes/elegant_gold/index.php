<?php
// ==========================================
// 1. LOGIC BACKEND (Simpan Ucapan & Ambil Data)
// ==========================================

$id_inv_active = $inv['id_inv'];

// A. Proses Simpan Ucapan (Jika ada POST)
if (isset($_POST['kirim_ucapan'])) {
    $nama_tamu = htmlspecialchars($_POST['nama_tamu']);
    $ucapan = htmlspecialchars($_POST['ucapan']);
    $kehadiran = htmlspecialchars($_POST['kehadiran']); // Opsional

    if(!empty($nama_tamu) && !empty($ucapan)){
        $insert = mysqli_query($koneksi, "INSERT INTO guestbook (id_inv, nama_tamu, ucapan, kehadiran) VALUES ('$id_inv_active', '$nama_tamu', '$ucapan', '$kehadiran')");
        // Redirect agar tidak resubmit saat refresh
        echo "<script>window.location.href='?slug=".$inv['slug']."#guestbook';</script>";
    }
}

// B. Ambil Data
$q_ucapan = mysqli_query($koneksi, "SELECT * FROM guestbook WHERE id_inv = '$id_inv_active' ORDER BY id_guest DESC");
$q_gifts  = mysqli_query($koneksi, "SELECT * FROM invitation_gifts WHERE id_inv = '$id_inv_active'");

// C. Ambil Galeri
$galeri_items = [];
$q_galeri_raw = mysqli_query($koneksi, "SELECT * FROM invitation_gallery WHERE id_inv = '$id_inv_active' ORDER BY id_galeri DESC");
while($row = mysqli_fetch_assoc($q_galeri_raw)){
    $galeri_items[] = $row;
}

// D. Helper Image (Supaya tidak error jika foto kosong)
function get_img_src($filename, $name_fallback) {
    if (!empty($filename)) {
        // Cek apakah file ada di folder uploads
        // Menggunakan path relatif dari root karena file ini di-include oleh preview.php
        if (file_exists('assets/images/uploads/' . $filename)) {
            return url('assets/images/uploads/' . $filename);
        }
    }
    // Fallback Avatar
    return "https://ui-avatars.com/api/?name=" . urlencode($name_fallback) . "&background=random&size=200&color=fff";
}

$img_pria   = get_img_src($inv['foto_pria'], $inv['nama_pria']);
$img_wanita = get_img_src($inv['foto_wanita'], $inv['nama_wanita']);
// Cover Default jika user belum upload
$bg_cover   = !empty($inv['foto_cover']) ? url('assets/images/uploads/'.$inv['foto_cover']) : 'https://images.unsplash.com/photo-1519225469034-0295dec197dc?q=80&w=1000';

// E. Logic Lagu (Playlist Database)
$playlist = [];

// 1. Ambil Lagu Custom dari Database
$q_music = mysqli_query($koneksi, "SELECT file_music FROM invitation_music WHERE id_inv = '$id_inv_active' ORDER BY id_music ASC");
if(mysqli_num_rows($q_music) > 0) {
    while($music = mysqli_fetch_assoc($q_music)){
        $playlist[] = url('assets/music/uploads/' . $music['file_music']);
    }
} else {
    // 2. Jika tidak ada lagu custom, pakai lagu default
    $playlist[] = "https://cdn.pixabay.com/download/audio/2022/10/25/audio_24e393c837.mp3"; // Beautiful Piano
    $playlist[] = "https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3"; // Wedding Dream
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Wedding of <?= $inv['nama_pria'] ?> & <?= $inv['nama_wanita'] ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cinzel:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root { --primary: #D4AF37; --dark: #1a1a1a; --bg: #f9f9f9; }
        body { font-family: 'Lato', sans-serif; background-color: var(--bg); color: #444; overflow-x: hidden; }
        h1, h2, h3 { font-family: 'Cinzel', serif; color: var(--dark); }
        .font-script { font-family: 'Great Vibes', cursive; color: var(--primary); }

        /* COVER SCREEN */
        #cover-page {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url('<?= $bg_cover ?>');
            background-size: cover; background-position: center;
            z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white;
            transition: transform 1s ease-in-out;
        }
        .btn-open { background-color: var(--primary); color: white; border: 1px solid white; padding: 12px 35px; border-radius: 50px; text-transform: uppercase; letter-spacing: 2px; margin-top: 30px; text-decoration: none; animation: pulse 2s infinite; cursor: pointer; font-weight: bold;}
        .slide-up { transform: translateY(-100%); }

        /* SECTIONS */
        .section { padding: 80px 20px; text-align: center; }
        .bg-pattern { background-image: url('https://www.transparenttextures.com/patterns/cream-paper.png'); }
        
        /* MEMPELAI STYLE */
        .couple-img { width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 5px solid var(--primary); padding: 5px; margin-bottom: 20px; transition: transform 0.5s; box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .couple-img:hover { transform: rotate(5deg) scale(1.05); }
        .parent-names { font-size: 0.9rem; color: #777; line-height: 1.6; }

        /* EVENT CARD */
        .event-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 15px 40px rgba(0,0,0,0.05); border-top: 5px solid var(--primary); }
        
        /* GALLERY LAYOUT */
        .gallery-photo { width: 100%; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 15px; display: block; transition: 0.3s; }
        .gallery-photo:hover { transform: scale(1.02); }
        .quote-box { background: white; padding: 40px 25px; margin: 40px 0; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-left: 4px solid var(--primary); position: relative; }
        .quote-icon { font-size: 2rem; color: #ddd; position: absolute; top: 10px; left: 10px; }

        /* GIFTS & GUESTBOOK */
        .bank-card { background: linear-gradient(135deg, #222, #444); color: #D4AF37; padding: 25px; border-radius: 15px; margin-bottom: 20px; text-align: left; position: relative; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .guestbook-list { max-height: 400px; overflow-y: auto; text-align: left; background: white; padding: 20px; border-radius: 10px; border: 1px solid #eee; }
        .guest-item { border-bottom: 1px solid #f0f0f0; padding: 15px 0; }
        .guest-item:last-child { border-bottom: none; }
        
        /* MUSIC CONTROL */
        .music-control { position: fixed; bottom: 30px; right: 30px; width: 50px; height: 50px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 9998; cursor: pointer; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: spin 5s linear infinite; }
        .music-paused { animation-play-state: paused; background: #555; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.7); } 70% { box-shadow: 0 0 0 15px rgba(212, 175, 55, 0); } 100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); } }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        
        /* FOOTER */
        footer { background: #1a1a1a; color: #888; padding: 60px 20px; font-size: 0.9rem; }
        .cta-box { background: #333; padding: 30px; border-radius: 15px; margin-top: 30px; border: 1px solid #444; }
    </style>
</head>
<body>

    <audio id="bg-music"></audio> 
    
    <div class="music-control d-none" id="music-btn" onclick="toggleMusic()" title="Play/Pause Music">
        <i class="fas fa-compact-disc fa-lg"></i>
    </div>

    <div id="cover-page">
        <div class="cover-content" data-aos="zoom-in" data-aos-duration="1500">
            <h5 class="mb-3 text-uppercase text-light ls-2" style="letter-spacing: 3px; font-size: 0.9rem;">The Wedding Of</h5>
            <h1 class="font-script mb-4" style="font-size: 4.5rem; text-shadow: 0 5px 15px rgba(0,0,0,0.5);"><?= $inv['nama_pria'] ?> & <?= $inv['nama_wanita'] ?></h1>
            
            <div style="border-top: 1px solid rgba(255,255,255,0.3); border-bottom: 1px solid rgba(255,255,255,0.3); padding: 20px 40px; display: inline-block; background: rgba(0,0,0,0.3); border-radius: 10px; backdrop-filter: blur(5px);">
                <p class="mb-2 text-white small">Kepada Yth. Bapak/Ibu/Saudara/i</p>
                <h3 class="m-0 text-white fw-bold">Tamu Undangan</h3>
            </div>
            <br>
            <div class="btn-open" onclick="bukaUndangan()">
                <i class="fas fa-envelope-open-text me-2"></i> Buka Undangan
            </div>
        </div>
    </div>

    <main id="main-content">

        <header class="section bg-pattern" style="padding-top: 100px;">
            <div class="container" data-aos="fade-up">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/Basmala.svg/2560px-Basmala.svg.png" width="200" class="mb-4 opacity-75">
                <p class="text-uppercase ls-2 mb-4 fw-bold">Assalamu’alaikum Warahmatullahi Wabarakatuh</p>
                <p class="mx-auto" style="max-width: 700px; color: #666;">
                    Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud menyelenggarakan pernikahan putra-putri kami yang Insya Allah akan dilaksanakan pada:
                </p>
            </div>
        </header>

        <section class="section">
            <div class="container">
                <div class="row justify-content-center align-items-center g-5">
                    
                    <div class="col-lg-5" data-aos="fade-right">
                        <div class="d-flex flex-column align-items-center">
                            <img src="<?= $img_pria ?>" class="couple-img" alt="Pria">
                            <h2 class="font-script text-primary" style="font-size: 3rem;"><?= $inv['nama_pria_lengkap'] ?></h2>
                            <p class="fw-bold mb-1">Putra dari Pasangan</p>
                            <div class="parent-names">
                                Bpk. <?= $inv['ortu_pria_ayah'] ?> <br>& Ibu <?= $inv['ortu_pria_ibu'] ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2" data-aos="zoom-in">
                        <h1 class="font-script display-1 text-muted opacity-25">&</h1>
                    </div>

                    <div class="col-lg-5" data-aos="fade-left">
                        <div class="d-flex flex-column align-items-center">
                            <img src="<?= $img_wanita ?>" class="couple-img" alt="Wanita">
                            <h2 class="font-script text-primary" style="font-size: 3rem;"><?= $inv['nama_wanita_lengkap'] ?></h2>
                            <p class="fw-bold mb-1">Putri dari Pasangan</p>
                            <div class="parent-names">
                                Bpk. <?= $inv['ortu_wanita_ayah'] ?> <br>& Ibu <?= $inv['ortu_wanita_ibu'] ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class="section bg-pattern">
            <div class="container">
                <h2 class="mb-5 font-script" data-aos="fade-down" style="font-size: 3.5rem;">Waktu & Tempat</h2>
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="event-card" data-aos="flip-up">
                            <h3 class="mb-4 text-uppercase" style="letter-spacing: 2px; font-size: 1.5rem;">Resepsi Pernikahan</h3>
                            
                            <div class="d-flex justify-content-center gap-4 mb-4 text-center py-4 border-top border-bottom">
                                <div>
                                    <i class="far fa-calendar-alt fa-2x mb-2 text-warning"></i>
                                    <h5 class="fw-bold"><?= date('d F Y', strtotime($inv['tgl_resepsi'])) ?></h5>
                                </div>
                                <div style="border-left: 1px solid #ddd;"></div>
                                <div>
                                    <i class="far fa-clock fa-2x mb-2 text-warning"></i>
                                    <h5 class="fw-bold"><?= $inv['jam_acara'] ?? '08:00 - Selesai' ?></h5>
                                </div>
                            </div>
                            
                            <p class="fw-bold mb-0 fs-5"><?= $inv['lokasi_nama'] ?></p>
                            <p class="text-muted mb-4"><?= nl2br($inv['lokasi_alamat']) ?></p>
                            
                            <?php if(!empty($inv['link_maps'])): ?>
                            <a href="<?= $inv['link_maps'] ?>" target="_blank" class="btn btn-outline-dark rounded-pill px-5 py-2">
                                <i class="fas fa-map-marked-alt me-2"></i> Petunjuk Arah
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <h2 class="mb-5 font-script" style="font-size: 3.5rem;">Galeri Bahagia</h2>
                
                <?php if(empty($galeri_items)): ?>
                    <div class="text-center py-5 bg-light rounded">
                        <i class="far fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Menunggu foto momen bahagia kami diunggah...</p>
                    </div>
                <?php else: ?>
                    
                    <div class="row g-4 mb-4">
                        <?php 
                        $first_two = array_slice($galeri_items, 0, 2);
                        foreach($first_two as $foto): 
                        ?>
                        <div class="col-md-6" data-aos="fade-up">
                            <img src="<?= url('assets/images/uploads/' . $foto['file_foto']) ?>" class="gallery-photo" style="height: 400px; object-fit: cover;" alt="Momen">
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="quote-box" data-aos="zoom-in">
                        <i class="fas fa-quote-left quote-icon"></i>
                        <h4 class="font-script text-primary mb-3" style="font-size: 2rem;">"Cinta Bukan Mencari Seseorang yang Sempurna"</h4>
                        <p class="text-muted fst-italic">
                            "Tetapi melengkapi ketidaksempurnaan orang lain dengan cara yang sempurna. Pernikahan adalah ibadah terpanjang, semoga Allah memberkahi langkah kami."
                        </p>
                    </div>

                    <?php 
                    $remaining = array_slice($galeri_items, 2);
                    if(!empty($remaining)): 
                    ?>
                    <div class="row g-3">
                        <?php foreach($remaining as $foto): ?>
                        <div class="col-6 col-md-4" data-aos="fade-up" data-aos-delay="100">
                            <img src="<?= url('assets/images/uploads/' . $foto['file_foto']) ?>" class="gallery-photo" style="height: 200px; object-fit: cover;" alt="Momen">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
        </section>

        <section class="section bg-pattern">
            <div class="container">
                <h2 class="mb-4 font-script" style="font-size: 3.5rem;">Tanda Kasih</h2>
                
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center mb-5" data-aos="fade-up">
                        <p class="text-muted" style="line-height: 1.8;">
                            Doa restu Anda merupakan karunia yang sangat berarti bagi kami. 
                            Namun jika Anda berhalangan hadir dan ingin memberikan tanda kasih, 
                            Anda dapat mengirimkannya melalui:
                        </p>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <?php if(mysqli_num_rows($q_gifts) > 0): ?>
                        <?php while($gift = mysqli_fetch_assoc($q_gifts)): ?>
                        <div class="col-md-5 mb-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="bank-card">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <span class="badge bg-light text-dark fs-5 shadow-sm"><?= strtoupper($gift['nama_bank']) ?></span>
                                    <i class="fas fa-chip fa-2x text-warning opacity-50"></i>
                                </div>
                                <h3 class="mb-1 text-white" style="letter-spacing: 3px; font-family: monospace;"><?= $gift['no_rekening'] ?></h3>
                                <p class="text-white-50 mb-0 small">A.N. <?= strtoupper($gift['atas_nama']) ?></p>
                                
                                <div class="mt-4 text-end">
                                    <button class="btn btn-sm btn-light text-dark shadow-sm fw-bold" onclick="copyText('<?= $gift['no_rekening'] ?>')">
                                        <i class="far fa-copy me-2"></i> Salin Nomor
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12"><p class="text-muted">Tidak ada informasi rekening.</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section id="guestbook" class="section">
            <div class="container">
                <h2 class="mb-5 font-script" style="font-size: 3.5rem;">Doa & Harapan</h2>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        
                        <div class="card mb-5 shadow border-0" data-aos="fade-up">
                            <div class="card-body p-4 text-start">
                                <form action="" method="POST">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nama Anda</label>
                                        <input type="text" name="nama_tamu" class="form-control" placeholder="Tulis nama lengkap..." required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Ucapan & Doa</label>
                                        <textarea name="ucapan" class="form-control" rows="3" placeholder="Tulis doa restu untuk kami..." required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Konfirmasi Kehadiran</label>
                                        <select name="kehadiran" class="form-select">
                                            <option value="Hadir">Hadir</option>
                                            <option value="Tidak Hadir">Berhalangan</option>
                                            <option value="Masih Ragu">Masih Ragu</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="kirim_ucapan" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">Kirim Ucapan</button>
                                </form>
                            </div>
                        </div>

                        <div class="guestbook-list" data-aos="fade-up">
                            <h5 class="fw-bold mb-4 sticky-top bg-white pb-2 border-bottom">Ucapan Terbaru</h5>
                            <?php if(mysqli_num_rows($q_ucapan) > 0): ?>
                                <?php while($guest = mysqli_fetch_assoc($q_ucapan)): ?>
                                    <div class="guest-item">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="fw-bold text-primary mb-1">
                                                <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($guest['nama_tamu']) ?>
                                            </h6>
                                            <span class="badge bg-light text-muted border"><?= $guest['kehadiran'] ?></span>
                                        </div>
                                        <p class="mb-1 small text-muted"><?= date('d M Y - H:i', strtotime($guest['created_at'])) ?></p>
                                        <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($guest['ucapan'])) ?></p>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-center text-muted py-3">Belum ada ucapan. Jadilah yang pertama mendoakan!</p>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <footer>
            <div class="container text-center">
                <h3 class="text-white mb-2" style="letter-spacing: 5px; font-family: 'Cinzel', serif;">DIGITAL INDONESIA</h3>
                <p class="small text-white-50">Providing The Best Digital Invitation Experience</p>
                
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="cta-box" data-aos="zoom-in">
                            <h5 class="text-white mb-3">Suka dengan undangan ini?</h5>
                            <p class="text-white-50 small mb-4">Buat undangan pernikahan digital impianmu sekarang juga. Mudah, Cepat, dan Elegan bersama OtwSah.</p>
                            <a href="<?= $BASEURL ?>" target="_blank" class="btn btn-outline-light rounded-pill px-5 fw-bold hover-scale">
                                Buat Undangan Gratis
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-5 small text-white-50">
                    &copy; <?= date('Y') ?> OtwSah by Digital Indonesia. All Rights Reserved.
                </div>
            </div>
        </footer>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Init Animation
        AOS.init({ duration: 1000, once: true });

        // --- PLAYLIST LOGIC (JAVASCRIPT) ---
        const playlist = <?= json_encode($playlist) ?>; // Ambil array PHP ke JS
        let currentTrack = 0;
        
        const audio = document.getElementById('bg-music');
        const musicBtn = document.getElementById('music-btn');
        const cover = document.getElementById('cover-page');
        let isPlaying = false;

        // Set lagu pertama
        audio.src = playlist[currentTrack];

        // Fungsi Buka Undangan
        function bukaUndangan() {
            cover.classList.add('slide-up');
            playMusic();
            musicBtn.classList.remove('d-none');
            document.body.style.overflow = 'auto';
        }

        // Fungsi Play Music
        function playMusic() {
            audio.play().then(() => {
                isPlaying = true;
                musicBtn.classList.remove('music-paused');
            }).catch(e => console.log("Auto-play blocked, waiting for interaction"));
        }

        // Fungsi Pause Music
        function pauseMusic() {
            audio.pause();
            isPlaying = false;
            musicBtn.classList.add('music-paused');
        }

        // Toggle Button
        function toggleMusic() {
            if (isPlaying) { pauseMusic(); } 
            else { playMusic(); }
        }

        // Auto Next Track
        audio.addEventListener('ended', function() {
            currentTrack++;
            if (currentTrack >= playlist.length) {
                currentTrack = 0; // Loop balik ke awal
            }
            audio.src = playlist[currentTrack];
            playMusic();
        });

        // Copy Text Function
        function copyText(text) {
            navigator.clipboard.writeText(text);
            alert('Nomor Rekening ' + text + ' berhasil disalin!');
        }
    </script>
</body>
</html>