<?php
// 1. INISIALISASI DATA
$id_inv_active = $inv['id_inv'];

// A. Query Events (Timeline)
$q_events = mysqli_query($koneksi, "SELECT * FROM invitation_events WHERE id_inv = '$id_inv_active' ORDER BY tgl_acara ASC, urutan ASC");
$events = [];
while($row = mysqli_fetch_assoc($q_events)) { $events[] = $row; }

// Fallback jika timeline kosong (Ambil dari data utama)
if(empty($events)) {
    $events[] = [
        'nama_acara' => 'Resepsi Pernikahan',
        'tgl_acara' => $inv['tgl_resepsi'],
        'jam_acara' => $inv['jam_acara'],
        'lokasi_nama' => $inv['lokasi_nama'],
        'lokasi_alamat' => $inv['lokasi_alamat'],
        'link_maps' => $inv['lokasi_map']
    ];
}

// Tentukan Target Countdown (Acara Pertama)
$first_event = $events[0];
$target_date = date('Y/m/d', strtotime($first_event['tgl_acara'])) . ' ' . substr($first_event['jam_acara'], 0, 5) . ':00';

// B. Query Galeri & Video
$galeri_items = [];
$q_galeri_raw = mysqli_query($koneksi, "SELECT * FROM invitation_gallery WHERE id_inv = '$id_inv_active' ORDER BY id_galeri DESC");
while($row = mysqli_fetch_assoc($q_galeri_raw)){ $galeri_items[] = $row; }

// C. Query Lainnya
$q_gifts = mysqli_query($koneksi, "SELECT * FROM invitation_gifts WHERE id_inv = '$id_inv_active'");
$q_ucapan = mysqli_query($koneksi, "SELECT * FROM guestbook WHERE id_inv = '$id_inv_active' ORDER BY id_guest DESC");

// D. Helper Image
function get_img_src($filename, $name_fallback) {
    if (!empty($filename) && file_exists('assets/images/uploads/' . $filename)) {
        return url('assets/images/uploads/' . $filename);
    }
    return "https://ui-avatars.com/api/?name=" . urlencode($name_fallback) . "&background=random&size=200&color=fff";
}

$img_pria   = get_img_src($inv['foto_pria'], $inv['nama_pria']);
$img_wanita = get_img_src($inv['foto_wanita'], $inv['nama_wanita']);
$bg_cover   = !empty($inv['foto_cover']) ? url('assets/images/uploads/'.$inv['foto_cover']) : 'https://images.unsplash.com/photo-1519225469034-0295dec197dc?q=80&w=1000';

// E. Playlist Musik
$playlist = [];
$q_music = mysqli_query($koneksi, "SELECT file_music FROM invitation_music WHERE id_inv = '$id_inv_active' ORDER BY urutan ASC");
if(mysqli_num_rows($q_music) > 0) {
    while($music = mysqli_fetch_assoc($q_music)){ $playlist[] = url('assets/music/uploads/' . $music['file_music']); }
} else {
    $playlist[] = "https://cdn.pixabay.com/download/audio/2022/10/25/audio_24e393c837.mp3";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { --primary: #D4AF37; --dark: #1a1a1a; --bg: #fdfdfd; }
        body { font-family: 'Lato', sans-serif; background-color: var(--bg); color: #444; overflow-x: hidden; }
        h1, h2, h3 { font-family: 'Cinzel', serif; color: var(--dark); }
        .font-script { font-family: 'Great Vibes', cursive; color: var(--primary); }
        .section { padding: 80px 20px; text-align: center; }
        .bg-pattern { background-image: url('https://www.transparenttextures.com/patterns/cream-paper.png'); }

        /* COVER */
        #cover-page { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url('<?= $bg_cover ?>'); background-size: cover; background-position: center; z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white; transition: transform 1s ease-in-out; }
        .btn-open { background-color: var(--primary); color: white; border: 1px solid white; padding: 12px 35px; border-radius: 50px; text-transform: uppercase; letter-spacing: 2px; margin-top: 30px; animation: pulse 2s infinite; cursor: pointer; font-weight: bold; }
        .slide-up { transform: translateY(-100%); }

        /* COUNTDOWN (Elegant Gold) */
        .countdown-wrapper { margin: 40px auto; max-width: 600px; }
        .countdown-box { display: flex; justify-content: center; gap: 15px; }
        .cd-item { background: linear-gradient(135deg, #D4AF37, #C5A028); color: white; padding: 15px; border-radius: 12px; min-width: 80px; box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3); border: 1px solid #fff; }
        .cd-num { font-size: 2rem; font-weight: bold; display: block; line-height: 1; font-family: 'Cinzel', serif; }
        .cd-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; display: block; }

        /* TIMELINE */
        .timeline { position: relative; max-width: 800px; margin: 50px auto 0; padding: 20px 0; }
        .timeline::after { content: ''; position: absolute; width: 2px; background-color: var(--primary); top: 0; bottom: 0; left: 50%; margin-left: -1px; }
        .timeline-item { padding: 10px 40px; position: relative; width: 50%; box-sizing: border-box; }
        .timeline-item.left { left: 0; text-align: right; }
        .timeline-item.right { left: 50%; text-align: left; }
        .timeline-dot { position: absolute; width: 20px; height: 20px; background: white; border: 4px solid var(--primary); border-radius: 50%; top: 25px; z-index: 1; }
        .left .timeline-dot { right: -50px; } 
        .right .timeline-dot { left: -50px; }
        .timeline-content { padding: 25px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-bottom: 4px solid var(--primary); }
        
        @media screen and (max-width: 768px) {
            .timeline::after { left: 30px; }
            .timeline-item { width: 100%; padding-left: 60px; padding-right: 10px; text-align: left; }
            .timeline-item.right { left: 0; }
            .left .timeline-dot, .right .timeline-dot { left: -40px; right: auto; }
        }

        /* LIVE & VIDEO */
        .live-section { background: #111; color: white; padding: 60px 20px; }
        .live-btn { animation: pulse-red 2s infinite; background: #d32f2f; color: white; border: none; font-weight: bold; padding: 12px 30px; border-radius: 50px; text-decoration: none; display: inline-block; margin-top: 20px; }
        .video-wrapper { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 15px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); margin-bottom: 40px; border: 5px solid white; }
        .video-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }

        /* OTHER STYLES */
        .couple-img { 
            width: 280px;           /* Diperbesar dari 180px */
            height: 380px;          /* Dibuat memanjang (portrait) agar foto lebih utuh */
            object-fit: cover;      /* Memastikan foto mengisi area tanpa gepeng */
            border-radius: 25px;    /* UBAH INI: Dari 50% menjadi 25px agar jadi kotak tumpul */
            border: 4px solid var(--primary); /* Bingkai emas sedikit ditipiskan agar lebih elegan */
            padding: 6px;           /* Jarak antara foto dan bingkai emas */
            margin-bottom: 25px;
            background-color: #fff; /* Latar belakang putih di balik bingkai */
            box-shadow: 0 15px 35px rgba(212, 175, 55, 0.25); /* Efek bayangan emas mewah */
            transition: transform 0.3s ease; /* Animasi halus saat di-hover */
        }

        .couple-img:hover {
            transform: translateY(-5px); /* Efek sedikit naik saat kursor diarahkan */
        }
        @media screen and (max-width: 576px) {
            .couple-img {
                width: 220px;
                height: 300px;
            }
            /* Mengurangi ukuran font nama di HP agar seimbang dengan fotonya */
            .mempelai-section h2.font-script {
                font-size: 2.2rem !important;
            }
        }
        .gallery-photo { width: 100%; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 15px; display: block; }
        .bank-card { background: linear-gradient(135deg, #222, #444); color: #D4AF37; padding: 25px; border-radius: 15px; margin-bottom: 20px; text-align: left; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        /* --- GUESTBOOK LUXURY STYLE --- */
.guestbook-section {
    background: linear-gradient(to bottom, #fdfdfd, #f5f5f0);
    padding: 80px 20px;
    position: relative;
}

/* Form Card Elegan */
.guest-form-card {
    background: #fff;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 20px 50px rgba(212, 175, 55, 0.15); /* Shadow Emas Halus */
    border: 1px solid rgba(212, 175, 55, 0.2);
    position: relative;
    overflow: hidden;
}
.guest-form-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 5px;
    background: linear-gradient(90deg, transparent, var(--primary), transparent);
}

/* Quick Wish Chips */
.quick-wish-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
    margin-bottom: 20px;
}
.wish-chip {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 50px;
    padding: 6px 15px;
    font-size: 0.8rem;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
    user-select: none;
}
.wish-chip:hover, .wish-chip.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
}

/* Form Input Styling */
.form-control-luxury {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 12px 15px;
    background: #fdfdfd;
    transition: 0.3s;
    font-size: 0.95rem;
}
.form-control-luxury:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
    background: #fff;
}
.btn-kirim-luxury {
    background: var(--primary);
    color: white;
    border: none;
    padding: 12px 0;
    border-radius: 50px;
    font-weight: bold;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: 0.3s;
    box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
}
.btn-kirim-luxury:hover {
    background: #b5952f;
    transform: translateY(-2px);
}

/* List Ucapan Chat Style */
.guest-list-wrapper {
    max-height: 600px;
    overflow-y: auto;
    padding-right: 10px;
    margin-top: 40px;
}
/* Scrollbar cantik */
.guest-list-wrapper::-webkit-scrollbar { width: 6px; }
.guest-list-wrapper::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 10px; }

.comment-card {
    background: #fff;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 4px solid var(--primary);
    box-shadow: 0 5px 15px rgba(0,0,0,0.03);
    transition: 0.3s;
    position: relative;
}
.comment-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}
.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    border-bottom: 1px dashed #eee;
    padding-bottom: 10px;
}
.sender-info { display: flex; align-items: center; gap: 10px; }
.sender-avatar {
    width: 40px; height: 40px;
    background: #f0f0f0;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--primary);
    font-weight: bold;
    font-size: 1.2rem;
}
.sender-name { font-weight: bold; color: #333; font-size: 1rem; }
.comment-time { font-size: 0.75rem; color: #999; display: flex; align-items: center; gap: 5px; }

/* Badge Status Kehadiran */
.badge-hadir { background: #e6f4ea; color: #1e7e34; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; border: 1px solid #b7e1c9; }
.badge-tidak { background: #fbe9e7; color: #d93025; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; border: 1px solid #f2c1bd; }
.badge-ragu { background: #fff8e1; color: #e65100; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; border: 1px solid #ffecb3; }

.comment-body { font-size: 0.95rem; color: #555; line-height: 1.6; font-style: italic; }

/* Styling Tombol Balas */
.btn-reply-toggle {
    background: none; border: none; color: var(--primary);
    font-size: 0.8rem; font-weight: bold; cursor: pointer;
    padding: 0; margin-top: 10px; transition: 0.3s;
}
.btn-reply-toggle:hover { text-decoration: underline; color: #b5952f; }

/* Styling Form Balasan */
.reply-form-container {
    background: #f9f9f9; padding: 15px; border-radius: 10px;
    margin-top: 15px; display: none; /* Tersembunyi default */
    border-left: 3px solid var(--primary);
}
.reply-form-container.active { display: block; animation: fadeIn 0.5s; }

/* Styling List Balasan (Anak) */
.child-comments {
    margin-left: 40px; /* Menjorok ke dalam */
    margin-top: 10px;
    border-left: 2px dashed #eee;
    padding-left: 15px;
}
.child-comment-item {
    background: #fdfdfd;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 10px;
    border: 1px solid #eee;
}
        
        /* ANIMATIONS */
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(211, 47, 47, 0.7); } 70% { box-shadow: 0 0 0 15px rgba(211, 47, 47, 0); } }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(212, 175, 55, 0); } }
        
        .music-control { position: fixed; bottom: 30px; right: 30px; width: 50px; height: 50px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 9998; cursor: pointer; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: spin 5s linear infinite; }
        .music-paused { animation-play-state: paused; background: #555; }
        footer { background: #1a1a1a; color: #888; padding: 60px 20px; font-size: 0.9rem; }
    </style>
</head>
<body>

    <audio id="bg-music"></audio> 
    <div class="music-control d-none" id="music-btn" onclick="toggleMusic()"><i class="fas fa-compact-disc fa-lg"></i></div>

    <div id="cover-page">
        <div class="cover-content" data-aos="zoom-in" data-aos-duration="1500">
            <h5 class="mb-3 text-uppercase text-light ls-2">The Wedding Of</h5>
            <h1 class="font-script mb-4" style="font-size: 4.5rem; text-shadow: 0 5px 15px rgba(0,0,0,0.5);"><?= $inv['nama_pria'] ?> & <?= $inv['nama_wanita'] ?></h1>
            <div style="background: rgba(0,0,0,0.4); padding: 20px 40px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(5px);">
                <p class="mb-2 text-white small">Kepada Yth. Bapak/Ibu/Saudara/i</p>
                <h3 class="m-0 text-white fw-bold">Tamu Undangan</h3>
            </div>
            <div class="btn-open" onclick="bukaUndangan()"><i class="fas fa-envelope-open-text me-2"></i> Buka Undangan</div>
        </div>
    </div>

    <main id="main-content">
        
        <header class="section bg-pattern" style="padding-top: 100px;">
            <div class="container" data-aos="fade-up">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/Basmala.svg/2560px-Basmala.svg.png" width="200" class="mb-4 opacity-75">
                <p class="text-uppercase ls-2 mb-4 fw-bold">Assalamu’alaikum Warahmatullahi Wabarakatuh</p>
                <p class="mx-auto text-muted" style="max-width: 700px;">Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud menyelenggarakan pernikahan putra-putri kami:</p>
            </div>
        </header>

        <section class="section">
            <div class="container">
                <div class="row justify-content-center align-items-center g-5">
                    
                    <div class="col-lg-5" data-aos="fade-right">
                        <img src="<?= $img_pria ?>" class="couple-img shadow" alt="Pria">
                        <h2 class="font-script text-primary" style="font-size: 2.8rem;"><?= $inv['nama_pria_lengkap'] ?></h2>
                        <?php if(!empty($inv['ig_pria'])): ?>
                            <a href="https://instagram.com/<?= $inv['ig_pria'] ?>" target="_blank" class="text-muted text-decoration-none small d-block mb-3">
                                <i class="fab fa-instagram"></i><?= $inv['ig_pria'] ?>
                            </a>
                        <?php endif; ?>
                        <p class="small text-muted">Putra dari Pasangan <br><strong>Bpk. <?= $inv['ortu_pria_ayah'] ?> & Ibu <?= $inv['ortu_pria_ibu'] ?></strong></p>
                    </div>
                    
                    <div class="col-lg-2" data-aos="zoom-in"><h1 class="font-script display-1 text-muted opacity-25">&</h1></div>
                    
                    <div class="col-lg-5" data-aos="fade-left">
                        <img src="<?= $img_wanita ?>" class="couple-img shadow" alt="Wanita">
                        <h2 class="font-script text-primary" style="font-size: 2.8rem;"><?= $inv['nama_wanita_lengkap'] ?></h2>
                        <?php if(!empty($inv['ig_wanita'])): ?>
                            <a href="https://instagram.com/<?= $inv['ig_wanita'] ?>" target="_blank" class="text-muted text-decoration-none small d-block mb-3">
                                <i class="fab fa-instagram"></i> <?= $inv['ig_wanita'] ?>
                            </a>
                        <?php endif; ?>
                        <p class="small text-muted">Putri dari Pasangan <br><strong>Bpk. <?= $inv['ortu_wanita_ayah'] ?> & Ibu <?= $inv['ortu_wanita_ibu'] ?></strong></p>
                    </div>

                </div>
            </div>
        </section>

        <?php if(!empty($inv['story_meet_text'])): ?>
            <section class="section">
                <div class="container">
                    <h2 class="mb-5 font-script" data-aos="fade-down" style="font-size: 3.5rem;">Kisah Cinta Kami</h2>
                    
                    <div class="timeline-story">
                        
                        <div class="story-item" data-aos="fade-up">
                            <div class="story-icon"><i class="fas fa-heart text-white"></i></div>
                            <div class="story-content">
                                <span class="badge bg-primary mb-2"><?= date('Y', strtotime($inv['story_meet_date'])) ?></span>
                                <h3 class="font-script text-dark">Pertama Bertemu</h3>
                                <p class="text-muted"><?= nl2br($inv['story_meet_text']) ?></p>
                            </div>
                        </div>

                        <div class="story-quote" data-aos="zoom-in">
                            <p class="font-script text-primary" style="font-size: 1.5rem;">"Saat pertama mata berjumpa, hati langsung berkata: Inilah dia."</p>
                        </div>

                        <?php if(!empty($inv['story_engage_text'])): ?>
                        <div class="story-item" data-aos="fade-up">
                            <div class="story-icon"><i class="fas fa-ring text-white"></i></div>
                            <div class="story-content">
                                <span class="badge bg-primary mb-2"><?= date('F Y', strtotime($inv['story_engage_date'])) ?></span>
                                <h3 class="font-script text-dark">Lamaran</h3>
                                <p class="text-muted"><?= nl2br($inv['story_engage_text']) ?></p>
                            </div>
                        </div>

                        <div class="story-quote" data-aos="zoom-in">
                            <p class="font-script text-primary" style="font-size: 1.5rem;">"Dua jiwa, satu tujuan. Perjalanan ini kita tempuh bersama."</p>
                        </div>
                        <?php endif; ?>

                        <?php if(!empty($inv['story_marry_text'])): ?>
                        <div class="story-item" data-aos="fade-up">
                            <div class="story-icon"><i class="fas fa-dove text-white"></i></div>
                            <div class="story-content">
                                <span class="badge bg-primary mb-2"><?= date('d F Y', strtotime($inv['story_marry_date'])) ?></span>
                                <h3 class="font-script text-dark">Hari Bahagia</h3>
                                <p class="text-muted"><?= nl2br($inv['story_marry_text']) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </section>

            <style>
                .timeline-story { position: relative; max-width: 700px; margin: 0 auto; padding: 20px 0; }
                /* Garis Tengah Putus-putus */
                .timeline-story::before { content: ''; position: absolute; top: 0; bottom: 0; left: 50%; width: 2px; background-image: linear-gradient(to bottom, var(--primary) 50%, transparent 50%); background-size: 2px 20px; margin-left: -1px; }
                
                .story-item { position: relative; margin-bottom: 50px; width: 100%; clear: both; }
                .story-content { width: 45%; background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #eee; position: relative; }
                
                /* Icon Bulat di Tengah */
                .story-icon { position: absolute; top: 0; left: 50%; width: 50px; height: 50px; background: var(--primary); border-radius: 50%; margin-left: -25px; display: flex; align-items: center; justify-content: center; border: 4px solid #fff; box-shadow: 0 0 0 1px var(--primary); z-index: 2; font-size: 1.2rem; }

                /* Pengaturan Kiri/Kanan */
                .story-item:nth-child(odd) .story-content { float: left; text-align: right; border-right: 4px solid var(--primary); }
                .story-item:nth-child(even) .story-content { float: right; text-align: left; border-left: 4px solid var(--primary); }
                
                /* Quotes di tengah */
                .story-quote { text-align: center; margin: 40px 0; position: relative; z-index: 2; background: var(--bg); padding: 10px; display: inline-block; width: 100%; }

                @media (max-width: 768px) {
                    .timeline-story::before { left: 30px; }
                    .story-icon { left: 30px; }
                    .story-item:nth-child(odd) .story-content, 
                    .story-item:nth-child(even) .story-content { float: none; width: 100%; margin-left: 60px; width: auto; text-align: left; border: none; border-left: 4px solid var(--primary); }
                }
            </style>
            <?php endif; ?>

        <section class="section bg-pattern">
            <div class="container">
                <h2 class="mb-2 font-script" data-aos="fade-down" style="font-size: 3.5rem;">Rangkaian Acara</h2>
                <p class="text-muted mb-5">Insya Allah acara akan dilaksanakan pada:</p>

                <div class="countdown-wrapper" data-aos="zoom-in">
                    <div class="countdown-box" id="countdown">
                        <div class="cd-item"><span class="cd-num" id="days">00</span><span class="cd-label">Hari</span></div>
                        <div class="cd-item"><span class="cd-num" id="hours">00</span><span class="cd-label">Jam</span></div>
                        <div class="cd-item"><span class="cd-num" id="minutes">00</span><span class="cd-label">Menit</span></div>
                        <div class="cd-item"><span class="cd-num" id="seconds">00</span><span class="cd-label">Detik</span></div>
                    </div>
                    <p class="small text-muted mt-3 fst-italic" id="cd-msg">Menuju hari bahagia</p>
                </div>
                
                <div class="timeline">
                    <?php 
                    $i = 0;
                    foreach($events as $event): 
                        $pos = ($i++ % 2 == 0) ? 'left' : 'right';
                    ?>
                    <div class="timeline-item <?= $pos ?>" data-aos="fade-up">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h3 class="font-script text-primary mb-2"><?= $event['nama_acara'] ?></h3>
                            <div class="d-flex justify-content-center align-items-center gap-2 mb-3 text-muted small">
                                <span><i class="far fa-calendar-alt"></i> <?= date('d F Y', strtotime($event['tgl_acara'])) ?></span> | 
                                <span><i class="far fa-clock"></i> <?= $event['jam_acara'] ?></span>
                            </div>
                            <p class="fw-bold mb-0 text-dark"><?= $event['lokasi_nama'] ?></p>
                            <p class="small text-muted mb-3"><?= nl2br($event['lokasi_alamat']) ?></p>
                            <?php if(!empty($event['link_maps'])): ?>
                                <a href="<?= $event['link_maps'] ?>" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-4">Google Maps</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </section>

        <?php if(!empty($inv['link_live'])): ?>
        <section class="live-section">
            <div class="container" data-aos="zoom-in">
                <span class="badge bg-danger mb-3 px-3 py-2">LIVE STREAMING</span>
                <h2 class="font-script mb-3" style="font-size: 3rem;">Saksikan Secara Virtual</h2>
                <p class="opacity-75" style="max-width: 600px; margin: 0 auto;">Bagi keluarga dan sahabat yang berhalangan hadir, Anda dapat menyaksikan momen sakral ini melalui siaran langsung.</p>
                <a href="<?= $inv['link_live'] ?>" target="_blank" class="live-btn">
                    <i class="fas fa-video me-2"></i> Tonton Siaran Langsung
                </a>
            </div>
        </section>
        <?php endif; ?>

        <section class="section">
            <div class="container">
                <h2 class="mb-5 font-script" style="font-size: 3.5rem;">Galeri Bahagia</h2>
                
                <?php if(!empty($inv['video_url'])): 
                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $inv['video_url'], $matches);
                    if(isset($matches[1])): ?>
                    <div class="row justify-content-center mb-5">
                        <div class="col-md-10" data-aos="fade-up">
                            <div class="video-wrapper">
                                <iframe src="https://www.youtube.com/embed/<?= $matches[1] ?>" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                <?php endif; endif; ?>

                <?php if(empty($galeri_items)): ?>
                    <p class="text-muted">Menunggu foto momen bahagia...</p>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach($galeri_items as $f): ?>
                        <div class="col-6 col-md-4" data-aos="zoom-in">
                            <img src="<?= url('assets/images/uploads/' . $f['file_foto']) ?>" class="gallery-photo" alt="Galeri">
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="section bg-pattern">
            <div class="container">
                
                <div class="mb-5">
                    <h2 class="mb-3 font-script" style="font-size: 3rem;">Tanda Kasih</h2>
                    <p class="text-muted mb-4">Tanpa mengurangi rasa hormat, bagi Anda yang ingin memberikan tanda kasih:</p>
                    <div class="row justify-content-center">
                        <?php while($gift = mysqli_fetch_assoc($q_gifts)): ?>
                        <div class="col-md-5 mb-3" data-aos="fade-up">
                            <div class="bank-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-light text-dark fs-6"><?= strtoupper($gift['nama_bank']) ?></span>
                                    <i class="fas fa-chip fa-2x text-warning opacity-50"></i>
                                </div>
                                <h3 class="mb-1 text-white" style="letter-spacing: 2px;"><?= $gift['no_rekening'] ?></h3>
                                <p class="text-white-50 small mb-4">A.N. <?= strtoupper($gift['atas_nama']) ?></p>
                                <button class="btn btn-sm btn-light w-100 fw-bold" onclick="copyText('<?= $gift['no_rekening'] ?>')"><i class="far fa-copy"></i> Salin Nomor</button>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <hr class="my-5 opacity-25">

               <section id="guestbook" class="guestbook-section">
                    <div class="container">
                        <div class="text-center mb-5" data-aos="fade-up">
                            <h2 class="font-script text-primary" style="font-size: 3.5rem;">Doa & Harapan</h2>
                            <p class="text-muted">Kirimkan doa terbaik atau konfirmasi kehadiran Anda untuk melengkapi kebahagiaan kami.</p>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-lg-5 mb-5 mb-lg-0" data-aos="fade-right">
                                <div class="guest-form-card">
                                    <form id="formUcapan">
                                        <input type="hidden" name="id_inv" value="<?= $id_inv_active ?>">
                                        
                                        <div class="mb-4">
                                            <label class="fw-bold mb-2 small text-uppercase ls-1">Nama Lengkap</label>
                                            <input type="text" name="nama_tamu" class="form-control form-control-luxury" placeholder="Tulis nama Anda di sini..." required>
                                        </div>

                                        <div class="mb-4">
                                            <label class="fw-bold mb-2 small text-uppercase ls-1">Konfirmasi Kehadiran</label>
                                            <select name="kehadiran" class="form-select form-control-luxury" style="cursor: pointer;">
                                                <option value="hadir">😊 Pasti Hadir dong!</option>
                                                <option value="ragu">🤔 Masih Ragu nih, nanti dikabari ya</option>
                                                <option value="tidak">😥 Maaf banget, aku berhalangan hadir</option>
                                            </select>
                                        </div>

                                        <div class="mb-4">
                                            <label class="fw-bold mb-2 small text-uppercase ls-1">Ucapan & Doa</label>
                                            <textarea name="ucapan" id="ucapanArea" class="form-control form-control-luxury" rows="4" placeholder="Tulis doa manis untuk kami..." required></textarea>
                                            
                                            <div class="mt-3">
                                                <small class="text-muted d-block mb-2"><i class="far fa-lightbulb text-warning"></i> Ide Ucapan (Klik untuk pakai):</small>
                                                <div class="quick-wish-container">
                                                    <span class="wish-chip" onclick="fillWish('Selamat menempuh hidup baru! Semoga menjadi keluarga Sakinah, Mawaddah, Warahmah. 🤲')">✨ Samawa ya!</span>
                                                    <span class="wish-chip" onclick="fillWish('Happy Wedding! Bahagia selalu sampai kakek nenek. 🥰')">👵👴 Longlast!</span>
                                                    <span class="wish-chip" onclick="fillWish('Selamat yaa! Semoga segera diberi momongan yang lucu-lucu. 👶')">👶 Cepet dapet momongan</span>
                                                    <span class="wish-chip" onclick="fillWish('Barakallahu lakuma. Semoga Allah memberkahi pernikahan kalian. 🕌')">🕌 Barakallah</span>
                                                    <span class="wish-chip" onclick="fillWish('Congrats! Welcome to the club, bro/sis! 🥂')">🥂 Welcome to the club</span>
                                                    <span class="wish-chip" onclick="fillWish('So happy for you both! Doa terbaik untuk kalian berdua. ❤️')">❤️ So Happy!</span>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" id="btnKirim" class="btn btn-kirim-luxury w-100 shadow">
                                            <i class="far fa-paper-plane me-2"></i> Kirim Ucapan
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-7" data-aos="fade-left">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold m-0"><i class="far fa-comments text-primary me-2"></i> Ucapan Masuk</h5>
                                    <span class="badge bg-light text-dark border"><?= mysqli_num_rows($q_ucapan) ?> Pesan</span>
                                </div>

                                <div class="guest-list-wrapper" id="listUcapan">
                                    <?php 
                                    // 1. Ambil Komentar Utama (Parent = 0)
                                    $q_utama = mysqli_query($koneksi, "SELECT * FROM guestbook WHERE id_inv = '$id_inv_active' AND parent_id = 0 ORDER BY id_guest DESC");
                                    // var_dump($q_utama);
                                    if(mysqli_num_rows($q_utama) > 0):

                                        

                                        while($main = mysqli_fetch_assoc($q_utama)): 
                                            $initial = strtoupper(substr($main['nama_tamu'], 0, 1));
                                            // Tentukan Badge (Logic sama kayak sebelumnya)
                                            $badgeClass = 'badge-hadir'; 
                                            $textKehadiran = 'Hadir';
                                            if(strpos($main['konfirmasi_hadir'], 'tidak') !== false)
                                            {
                                                 $badgeClass = 'badge-tidak';
                                                 $textKehadiran = "Tidak Hadir";
                                            }

                                            else if(strpos($main['konfirmasi_hadir'], 'ragu') !== false)
                                            {
                                                $badgeClass = 'badge-ragu';
                                                $textKehadiran = "Masih Ragu";
                                            }
                                    ?>
                                    
                                    <div class="comment-card" id="comment-<?= $main['id_guest'] ?>">
                                        <div class="comment-header">
                                            <div class="sender-info">
                                                <div class="sender-avatar"><?= $initial ?></div>
                                                <div>
                                                    <div class="sender-name"><?= htmlspecialchars($main['nama_tamu']) ?></div>
                                                    <div class="comment-time"><i class="far fa-clock"></i> <?= date('d M Y • H:i', strtotime($main['created_at'])) ?> WIB</div>
                                                </div>
                                            </div>
                                            <span class="<?= $badgeClass ?>"><?= $textKehadiran ?></span>
                                        </div>
                                        <div class="comment-body">"<?= nl2br(htmlspecialchars($main['ucapan'])) ?>"</div>
                                        
                                        <button class="btn-reply-toggle" onclick="toggleReplyForm(<?= $main['id_guest'] ?>)">
                                            <i class="fas fa-reply me-1"></i> Balas
                                        </button>

                                        <div class="reply-form-container" id="reply-form-<?= $main['id_guest'] ?>">
                                            <form onsubmit="submitReply(event, <?= $main['id_guest'] ?>)">
                                                <input type="hidden" name="id_inv" value="<?= $id_inv_active ?>">
                                                <input type="hidden" name="parent_id" value="<?= $main['id_guest'] ?>">
                                                <input type="hidden" name="kehadiran" value="Hadir"> <div class="mb-2">
                                                    <input type="text" name="nama_tamu" class="form-control form-control-sm" placeholder="Nama Anda" required>
                                                </div>
                                                <div class="mb-2">
                                                    <textarea name="ucapan" class="form-control form-control-sm" rows="2" placeholder="Tulis balasan..." required></textarea>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Kirim Balasan</button>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="child-comments" id="child-container-<?= $main['id_guest'] ?>">
                                            <?php
                                            // 2. Ambil Balasan untuk komentar ini
                                            $id_parent = $main['id_guest'];
                                            $q_balasan = mysqli_query($koneksi, "SELECT * FROM guestbook WHERE parent_id = '$id_parent' ORDER BY id_guest ASC");
                                            while($reply = mysqli_fetch_assoc($q_balasan)):
                                                $initRep = strtoupper(substr($reply['nama_tamu'], 0, 1));
                                            ?>
                                            <div class="child-comment-item">
                                                <div class="d-flex align-items-center mb-1">
                                                    <div class="sender-avatar" style="width:30px;height:30px;font-size:0.8rem;margin-right:10px;"><?= $initRep ?></div>
                                                    <div>
                                                        <div class="fw-bold small"><?= htmlspecialchars($reply['nama_tamu']) ?></div>
                                                        <div class="text-muted" style="font-size:0.65rem;"><?= date('d M • H:i', strtotime($reply['created_at'])) ?></div>
                                                    </div>
                                                </div>
                                                <p class="mb-0 small text-dark mt-2">"<?= nl2br(htmlspecialchars($reply['ucapan'])) ?>"</p>
                                            </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>

                                    <?php endwhile; else: ?>
                                        <div class="text-center py-5 text-muted" id="empty-msg">
                                            <i class="far fa-comment-dots fa-3x mb-3 opacity-50"></i>
                                            <p>Belum ada ucapan. Jadilah yang pertama!</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
            </section>

                        <?php
                // Logic Auto-Fill
                $close_title = !empty($inv['closing_title']) ? $inv['closing_title'] : "Terima Kasih";
                $close_text  = !empty($inv['closing_text']) ? nl2br($inv['closing_text']) : "Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan do'a restu kepada kami.";
                $close_names = !empty($inv['closing_names']) ? $inv['closing_names'] : $inv['nama_pria'] . " & " . $inv['nama_wanita'];
                // Background Image (Default hitam kalau tidak upload)
                $close_bg = !empty($inv['closing_img']) ? "url('" . url('assets/images/uploads/'.$inv['closing_img']) . "')" : "linear-gradient(#111, #333)";
                ?>

                <style>
                    .closing-section {
                        background: <?= $close_bg ?>;
                        background-size: cover;
                        background-position: center;
                        background-attachment: fixed; /* Efek Parallax */
                        position: relative;
                        padding: 100px 20px;
                        color: white;
                        text-align: center;
                    }
                    /* Overlay gelap supaya teks terbaca */
                    .closing-section::before {
                        content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                        background: rgba(0,0,0,0.6); /* Gelap 60% */
                    }
                    .closing-content {
                        position: relative; z-index: 2;
                    }
                    .closing-names {
                        font-family: 'Cinzel', serif;
                        font-size: 2.5rem;
                        letter-spacing: 3px;
                        margin-top: 30px;
                        text-transform: uppercase;
                    }
                </style>

                <section class="closing-section">
                    <div class="container closing-content" data-aos="zoom-in">
                        <h2 class="font-script mb-4" style="font-size: 4rem;"><?= $close_title ?></h2>
                        <p class="mx-auto" style="max-width: 700px; line-height: 1.8; font-size: 1.1rem;">
                            <?= $close_text ?>
                        </p>
                        <div class="closing-names">
                            <?= $close_names ?>
                        </div>
                        <p class="mt-2 small text-white-50">Kami yang berbahagia</p>
                    </div>
                </section>

        <footer>
            <div class="container text-center">
                <h3 class="text-white mb-2" style="letter-spacing: 5px; font-family: 'Cinzel', serif;">DIGITAL INDONESIA</h3>
                <div class="row justify-content-center my-4">
                    <div class="col-md-6">
                        <div class="p-4" style="border: 1px solid #333; border-radius: 10px;">
                            <p class="text-white-50 small mb-3">Buat undangan digital impianmu. Elegan, Cepat & Mudah.</p>
                            <a href="<?= $BASEURL ?>" target="_blank" class="btn btn-outline-light rounded-pill px-4 btn-sm">Buat Undangan</a>
                        </div>
                    </div>
                </div>
                <div class="mt-4 small text-white-50">&copy; <?= date('Y') ?> OtwSah Project.</div>
            </div>
        </footer>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        AOS.init({ duration: 1000, once: true });

        // COUNTDOWN LOGIC
        const targetDate = new Date("<?= $target_date ?>").getTime();
        const countdownInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                clearInterval(countdownInterval);
                document.getElementById("countdown").innerHTML = "<h4 class='text-primary m-0'>Acara Sedang Berlangsung / Selesai</h4>";
                document.getElementById("cd-msg").style.display = 'none';
                return;
            }

            document.getElementById("days").innerText = Math.floor(distance / (1000 * 60 * 60 * 24));
            document.getElementById("hours").innerText = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            document.getElementById("minutes").innerText = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            document.getElementById("seconds").innerText = Math.floor((distance % (1000 * 60)) / 1000);
        }, 1000);

        // PLAYLIST MUSIC
        const playlist = <?= json_encode($playlist) ?>;
        let currentTrack = 0;
        const audio = document.getElementById('bg-music');
        const musicBtn = document.getElementById('music-btn');
        const cover = document.getElementById('cover-page');
        let isPlaying = false;

        if(playlist.length > 0) audio.src = playlist[currentTrack];

        function bukaUndangan() {
            cover.classList.add('slide-up');
            audio.play().then(() => { isPlaying = true; musicBtn.classList.remove('music-paused'); }).catch(e => console.log("Autoplay blocked"));
            musicBtn.classList.remove('d-none');
            document.body.style.overflow = 'auto';
        }

        function toggleMusic() {
            if (isPlaying) { audio.pause(); musicBtn.classList.add('music-paused'); isPlaying = false; } 
            else { audio.play(); musicBtn.classList.remove('music-paused'); isPlaying = true; }
        }

        // --- HELPER FUNCTION UNTUK QUICK WISH ---
        function fillWish(text) {
            const area = document.getElementById('ucapanArea');
            area.value = text;
            // Efek visual sedikit biar user tau udah keisi
            area.style.backgroundColor = '#fff8e1';
            setTimeout(() => area.style.backgroundColor = '#fff', 300);
            area.focus();
        }

        audio.addEventListener('ended', function() {
            currentTrack = (currentTrack + 1) % playlist.length;
            audio.src = playlist[currentTrack];
            audio.play();
        });

        // ===============================================
        //  FIX BUG: PAUSE OTOMATIS SAAT PINDAH TAB
        // ===============================================
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Jika tab disembunyikan/pindah tab -> PAUSE
                audio.pause();
            } else {
                // Jika balik ke tab ini -> PLAY (Hanya jika statusnya isPlaying = true)
                // Jadi kalau user sebelumnya manual pause, dia gak bakal nyala sendiri.
                if (isPlaying) {
                    audio.play().catch(e => console.log("Auto-resume blocked"));
                }
            }
        });
        // ===============================================

        // COPY REKENING
        function copyText(text) {
            navigator.clipboard.writeText(text);
            Swal.fire({icon: 'success', title: 'Tersalin', text: 'Nomor Rekening berhasil disalin!', timer: 1500, showConfirmButton: false});
        }

        // AJAX GUESTBOOK
        const formUcapan = document.getElementById('formUcapan');
        const listUcapan = document.getElementById('listUcapan');
        const btnKirim = document.getElementById('btnKirim');

        formUcapan.addEventListener('submit', function(e) {
            e.preventDefault();
            
            btnKirim.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            btnKirim.disabled = true;

            const formData = new FormData(formUcapan);
            
            // Ambil teks kehadiran raw dari select option buat logic frontend badge
            const kehadiranRaw = formData.get('kehadiran'); 
            let badgeClass = 'badge-hadir';
            let icon = '<i class="fas fa-check-circle me-1"></i>';
            let label = 'Hadir';

            if(kehadiranRaw.includes('Tidak')) { 
                badgeClass = 'badge-tidak'; 
                icon = '<i class="fas fa-times-circle me-1"></i>';
                label = 'Berhalangan';
            } else if(kehadiranRaw.includes('Ragu')) {
                badgeClass = 'badge-ragu';
                icon = '<i class="fas fa-question-circle me-1"></i>';
                label = 'Masih Ragu';
            }

            fetch('submit_ucapan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const initial = data.data.nama.charAt(0).toUpperCase();
                    
                    // HTML Template Item Baru
                    const item = document.createElement('div');
                    item.className = 'comment-card';
                    item.style.animation = 'fadeIn 0.8s'; // Animasi masuk
                    item.innerHTML = `
                        <div class="comment-header">
                            <div class="sender-info">
                                <div class="sender-avatar">${initial}</div>
                                <div>
                                    <div class="sender-name">${data.data.nama}</div>
                                    <div class="comment-time">
                                        <i class="far fa-clock"></i> ${data.data.waktu} WIB
                                    </div>
                                </div>
                            </div>
                            <span class="${badgeClass}">
                                ${icon} ${label}
                            </span>
                        </div>
                        <div class="comment-body">
                            "${data.data.ucapan}"
                        </div>
                    `;
                    
                    // Masukkan ke paling atas
                    listUcapan.insertBefore(item, listUcapan.firstChild);
                    
                    // Reset Form
                    formUcapan.reset();
                    Swal.fire({
                        icon: 'success', 
                        title: 'Terima Kasih!', 
                        text: 'Doa & harapan Anda sangat berarti bagi kami.', 
                        timer: 2500, 
                        showConfirmButton: false,
                        background: '#fff',
                        color: '#333'
                    });
                } else {
                    Swal.fire({icon: 'error', title: 'Gagal', text: data.msg});
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Koneksi terputus. Coba lagi ya.'});
            })
            .finally(() => {
                btnKirim.innerHTML = '<i class="far fa-paper-plane me-2"></i> Kirim Ucapan';
                btnKirim.disabled = false;
            });
        });

        // 1. Toggle Form Balasan
        function toggleReplyForm(id) {
            const form = document.getElementById('reply-form-' + id);
            if (form.style.display === 'block') {
                form.style.display = 'none';
            } else {
                // Tutup form lain dulu biar rapi
                document.querySelectorAll('.reply-form-container').forEach(el => el.style.display = 'none');
                form.style.display = 'block';
            }
        }

        // 2. Submit Balasan via AJAX
        function submitReply(e, parentId) {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = 'Mengirim...';
            btn.disabled = true;

            const formData = new FormData(form);

            fetch('submit_ucapan.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Buat elemen HTML balasan baru
                    const initial = data.data.nama.charAt(0).toUpperCase();
                    const html = `
                    <div class="child-comment-item" style="animation: fadeIn 0.5s">
                        <div class="d-flex align-items-center mb-1">
                            <div class="sender-avatar" style="width:30px;height:30px;font-size:0.8rem;margin-right:10px;">${initial}</div>
                            <div>
                                <div class="fw-bold small">${data.data.nama}</div>
                                <div class="text-muted" style="font-size:0.65rem;">${data.data.waktu}</div>
                            </div>
                        </div>
                        <p class="mb-0 small text-dark mt-2">"${data.data.ucapan}"</p>
                    </div>`;

                    // Masukkan ke container child
                    const container = document.getElementById('child-container-' + parentId);
                    container.insertAdjacentHTML('beforeend', html);
                    
                    // Reset & Tutup Form
                    form.reset();
                    toggleReplyForm(parentId);
                    Swal.fire({icon: 'success', title: 'Balasan Terkirim', timer: 1500, showConfirmButton: false});
                } else {
                    Swal.fire({icon: 'error', text: data.msg});
                }
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>