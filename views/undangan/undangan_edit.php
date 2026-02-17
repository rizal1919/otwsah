<?php
require_once '../../config/db.php';

// Cek Login
if (!isset($_SESSION['user'])) {
    header("Location: " . url('views/authentication/login.php'));
    exit;
}

$id_inv = htmlspecialchars($_GET['id']);
$id_user_login = $_SESSION['user']['id_user'];

// Ambil Data Undangan Utama
$query = "SELECT a.*, b.*, t.folder_name 
    FROM invitations a
    JOIN bride_groom b ON a.id_inv = b.id_inv
    JOIN themes t ON a.id_tema = t.id_tema
    WHERE a.id_inv = '$id_inv' AND a.id_user = '$id_user_login'";
$data = query($query);

if (empty($data)) { echo "Akses ditolak."; exit; }
$row = $data[0];

// --- LOGIC PHP (Backend Action) ---

// 1. TAMBAH ACARA (NEW FEATURE)
if (isset($_POST['tambah_acara'])) {
    $nama_acara = htmlspecialchars($_POST['nama_acara']);
    $tgl_acara  = htmlspecialchars($_POST['tgl_acara']);
    $jam_acara  = htmlspecialchars($_POST['jam_acara']);
    $lokasi     = htmlspecialchars($_POST['lokasi_nama']);
    $alamat     = htmlspecialchars($_POST['lokasi_alamat']);
    $maps       = htmlspecialchars($_POST['link_maps']);
    
    if(!empty($nama_acara) && !empty($tgl_acara)) {
        $q = "INSERT INTO invitation_events (id_inv, nama_acara, tgl_acara, jam_acara, lokasi_nama, lokasi_alamat, link_maps) 
              VALUES ('$id_inv', '$nama_acara', '$tgl_acara', '$jam_acara', '$lokasi', '$alamat', '$maps')";
        execute($q);
        header("Location: ?id=$id_inv"); exit;
    }
}

// 2. HAPUS ACARA
if (isset($_GET['hapus_acara'])) {
    $id_evt = htmlspecialchars($_GET['hapus_acara']);
    execute("DELETE FROM invitation_events WHERE id_event = '$id_evt'");
    header("Location: ?id=$id_inv"); exit;
}

// 3. LOGIC LAINNYA (GANTI FOTO, REKENING, MUSIK - TETAP SAMA)
if (isset($_GET['hapus_galeri'])) {
    $id_g = htmlspecialchars($_GET['hapus_galeri']);
    execute("DELETE FROM invitation_gallery WHERE id_galeri = '$id_g'");
    header("Location: ?id=$id_inv"); exit;
}
if (isset($_GET['hapus_foto'])) {
    $jenis = htmlspecialchars($_GET['hapus_foto']);
    $kolom = ($jenis == 'closing') ? 'closing_img' : "foto_" . $jenis; // Handle closing_img
    
    if(in_array($jenis, ['pria', 'wanita', 'cover', 'closing'])){
        execute("UPDATE invitations SET $kolom = NULL WHERE id_inv = '$id_inv'");
    }
    header("Location: ?id=$id_inv"); exit;
}
if (isset($_POST['tambah_rekening'])) {
    $nama_bank = htmlspecialchars($_POST['nama_bank']);
    $no_rekening = htmlspecialchars($_POST['no_rekening']);
    $atas_nama = htmlspecialchars($_POST['atas_nama']);
    if(!empty($nama_bank)) {
        execute("INSERT INTO invitation_gifts (id_inv, nama_bank, no_rekening, atas_nama) VALUES ('$id_inv', '$nama_bank', '$no_rekening', '$atas_nama')");
        header("Location: ?id=$id_inv"); exit;
    }
}
if (isset($_GET['hapus_gift'])) {
    $id_gift = htmlspecialchars($_GET['hapus_gift']);
    execute("DELETE FROM invitation_gifts WHERE id_gift = '$id_gift'");
    header("Location: ?id=$id_inv"); exit;
}
if (isset($_POST['upload_music'])) {
    $judul_lagu = htmlspecialchars($_POST['judul_lagu']);
    $file = $_FILES['file_music'];
    if ($file['error'] === 0) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext == 'mp3' || $ext == 'm4a') {
            $newName = time() . '_' . uniqid() . '.' . $ext;
            if (!is_dir("../../assets/music/uploads")) mkdir("../../assets/music/uploads", 0777, true);
            if(move_uploaded_file($file['tmp_name'], "../../assets/music/uploads/" . $newName)){
                execute("INSERT INTO invitation_music (id_inv, file_music, judul_lagu, urutan) VALUES ('$id_inv', '$newName', '$judul_lagu', 99)");
                header("Location: ?id=$id_inv"); exit;
            }
        }
    }
}
if (isset($_GET['hapus_music'])) {
    $id_m = htmlspecialchars($_GET['hapus_music']);
    execute("DELETE FROM invitation_music WHERE id_music = '$id_m'");
    header("Location: ?id=$id_inv"); exit;
}

$pageTitle = "Edit Undangan";
$pageHeader = "Edit Undangan: /" . $row['slug'];
$breadcrumbs = ["Undangan", "Edit"];

include '../../components/header.php';
include '../../components/sidebar.php';
include '../../components/navbar.php';
?>

<main class="nxl-container">
    <div class="nxl-content">
        <?php include '../../components/page_header.php'; ?>

        <div class="main-content">
            <div class="row">
                
                <div class="col-lg-7 col-xl-8">
                    
                    <form id="formUndangan" enctype="multipart/form-data">
                        <input type="hidden" name="id_inv" value="<?= $id_inv ?>">

                        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                            <li class="nav-item"><button class="nav-link active" id="mempelai-tab" data-bs-toggle="tab" data-bs-target="#mempelai" type="button">1. Mempelai</button></li>
                            <li class="nav-item"><button class="nav-link" id="story-tab" data-bs-toggle="tab" data-bs-target="#story" type="button">2. Kisah Cinta</button></li> 
                            <li class="nav-item"><button class="nav-link" id="acara-tab" data-bs-toggle="tab" data-bs-target="#acara" type="button">3. Rangkaian Acara</button></li>
                            <li class="nav-item"><button class="nav-link" id="galeri-tab" data-bs-toggle="tab" data-bs-target="#galeri" type="button">4. Galeri</button></li>
                            <li class="nav-item"><button class="nav-link" id="musik-tab" data-bs-toggle="tab" data-bs-target="#musik" type="button">5. Musik</button></li>
                            <li class="nav-item"><button class="nav-link" id="closing-tab" data-bs-toggle="tab" data-bs-target="#closing" type="button">6. Penutup</button></li>
                        </ul>

                        <div class="tab-content">
                            
                            <div class="tab-pane fade show active" id="mempelai">
                                <div class="card stretch stretch-full">
                                    <div class="card-header"><h5 class="card-title">Mempelai Pria</h5></div>
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-3 text-center mb-3">
                                                <div class="position-relative d-inline-block mb-2">
                                                    <?php if(!empty($row['foto_pria'])): ?>
                                                        <img src="<?= url('assets/images/uploads/'.$row['foto_pria']) ?>" class="rounded-circle border" style="width:100px;height:100px;object-fit:cover;">
                                                        <a href="?id=<?= $id_inv ?>&hapus_foto=pria" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" onclick="return confirm('Hapus?')" style="width:24px;height:24px;line-height:12px;">&times;</a>
                                                    <?php else: ?>
                                                        <img src="https://via.placeholder.com/100x100?text=Pria" class="rounded-circle border">
                                                    <?php endif; ?>
                                                </div>
                                                <input type="file" name="foto_pria" class="form-control form-control-sm" accept="image/*">
                                            </div>
                                            <div class="col-md-9">
                                                <div class="mb-2"><input type="text" name="nama_pria" class="form-control" value="<?= $row['nama_pria'] ?>" placeholder="Nama Panggilan"></div>
                                                <div class="mb-2"><input type="text" name="nama_pria_lengkap" class="form-control" value="<?= $row['nama_pria_lengkap'] ?>" placeholder="Nama Lengkap"></div>
                                                <div class="mb-2">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text"><i class="feather-instagram"></i></span>
                                                        <input type="text" name="ig_pria" class="form-control" value="<?= $row['ig_pria'] ?? '' ?>" placeholder="Username IG (tanpa @)">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6"><input type="text" name="ortu_pria_ayah" class="form-control" value="<?= $row['ortu_pria_ayah'] ?>" placeholder="Nama Ayah"></div>
                                                    <div class="col-6"><input type="text" name="ortu_pria_ibu" class="form-control" value="<?= $row['ortu_pria_ibu'] ?>" placeholder="Nama Ibu"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card stretch stretch-full mt-3">
                                    <div class="card-header"><h5 class="card-title">Mempelai Wanita</h5></div>
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-3 text-center mb-3">
                                                <div class="position-relative d-inline-block mb-2">
                                                    <?php if(!empty($row['foto_wanita'])): ?>
                                                        <img src="<?= url('assets/images/uploads/'.$row['foto_wanita']) ?>" class="rounded-circle border" style="width:100px;height:100px;object-fit:cover;">
                                                        <a href="?id=<?= $id_inv ?>&hapus_foto=wanita" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" onclick="return confirm('Hapus?')" style="width:24px;height:24px;line-height:12px;">&times;</a>
                                                    <?php else: ?>
                                                        <img src="https://via.placeholder.com/100x100?text=Wanita" class="rounded-circle border">
                                                    <?php endif; ?>
                                                </div>
                                                <input type="file" name="foto_wanita" class="form-control form-control-sm" accept="image/*">
                                            </div>
                                            <div class="col-md-9">
                                                <div class="mb-2"><input type="text" name="nama_wanita" class="form-control" value="<?= $row['nama_wanita'] ?>" placeholder="Nama Panggilan"></div>
                                                <div class="mb-2"><input type="text" name="nama_wanita_lengkap" class="form-control" value="<?= $row['nama_wanita_lengkap'] ?>" placeholder="Nama Lengkap"></div>
                                                <div class="mb-2">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text"><i class="feather-instagram"></i></span>
                                                        <input type="text" name="ig_wanita" class="form-control" value="<?= $row['ig_wanita'] ?? '' ?>" placeholder="Username IG (tanpa @)">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6"><input type="text" name="ortu_wanita_ayah" class="form-control" value="<?= $row['ortu_wanita_ayah'] ?>" placeholder="Nama Ayah"></div>
                                                    <div class="col-6"><input type="text" name="ortu_wanita_ibu" class="form-control" value="<?= $row['ortu_wanita_ibu'] ?>" placeholder="Nama Ibu"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="story">
                                <div class="card stretch stretch-full">
                                    <div class="card-header"><h5 class="card-title">Perjalanan Cinta (Love Story)</h5></div>
                                    <div class="card-body">
                                        
                                        <div class="mb-4 border-bottom pb-4">
                                            <h6 class="fw-bold text-primary mb-3"><i class="feather-heart me-2"></i> 1. Awal Bertemu</h6>
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <label class="small fw-bold">Tanggal / Tahun</label>
                                                    <input type="date" name="story_meet_date" class="form-control" value="<?= $row['story_meet_date'] ?>">
                                                </div>
                                                <div class="col-md-8 mb-2">
                                                    <label class="small fw-bold">Cerita Singkat</label>
                                                    <textarea name="story_meet_text" class="form-control" rows="4" placeholder="Ceritakan momen pertemuan..."><?= $row['story_meet_text'] ?></textarea>
                                                    
                                                    <div class="alert alert-light border mt-2 p-2">
                                                        <small class="text-muted d-block fw-bold mb-1"><i class="feather-info"></i> Contoh Cerita:</small>
                                                        <small class="text-muted fst-italic">
                                                            "Pertama kali kami bertemu di bangku kuliah semester 3 tahun 2018. Awalnya hanya sebatas teman satu kelompok tugas, namun canda tawa di sela-sela belajar membuat kami semakin dekat. Tidak ada yang menyangka pertemuan sederhana itu adalah awal dari segalanya."
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4 border-bottom pb-4">
                                            <h6 class="fw-bold text-primary mb-3"><i class="feather-box me-2"></i> 2. Lamaran (Engagement)</h6>
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <label class="small fw-bold">Tanggal</label>
                                                    <input type="date" name="story_engage_date" class="form-control" value="<?= $row['story_engage_date'] ?>">
                                                </div>
                                                <div class="col-md-8 mb-2">
                                                    <label class="small fw-bold">Cerita Singkat</label>
                                                    <textarea name="story_engage_text" class="form-control" rows="4" placeholder="Ceritakan momen lamaran..."><?= $row['story_engage_text'] ?></textarea>
                                                    
                                                    <div class="alert alert-light border mt-2 p-2">
                                                        <small class="text-muted d-block fw-bold mb-1"><i class="feather-info"></i> Contoh Cerita:</small>
                                                        <small class="text-muted fst-italic">
                                                            "Setelah 4 tahun bersama melewati suka dan duka, akhirnya Dia memberanikan diri datang ke rumah menemui orang tua saya. Dengan restu keluarga, kami melangkah ke jenjang yang lebih serius."
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <h6 class="fw-bold text-primary mb-3"><i class="feather-users me-2"></i> 3. Menuju Pernikahan</h6>
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <label class="small fw-bold">Tanggal Rencana</label>
                                                    <input type="date" name="story_marry_date" class="form-control" value="<?= $row['story_marry_date'] ?>">
                                                </div>
                                                <div class="col-md-8 mb-2">
                                                    <label class="small fw-bold">Harapan / Cerita</label>
                                                    <textarea name="story_marry_text" class="form-control" rows="4" placeholder="Harapan untuk pernikahan..."><?= $row['story_marry_text'] ?></textarea>
                                                    
                                                    <div class="alert alert-light border mt-2 p-2">
                                                        <small class="text-muted d-block fw-bold mb-1"><i class="feather-info"></i> Contoh Cerita:</small>
                                                        <small class="text-muted fst-italic">
                                                            "Insya Allah kami akan melangsungkan pernikahan suci ini. Kami memohon doa restu agar menjadi keluarga yang Sakinah, Mawaddah, dan Warahmah. Ini adalah awal dari perjalanan panjang kami berdua."
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="acara">
                                <div class="card stretch stretch-full">
                                    <div class="card-header"><h5 class="card-title">Rangkaian Acara (Timeline)</h5></div>
                                    <div class="card-body">
                                        
                                        <div id="listEventContainer" class="mb-4">
                                            <?php 
                                            $q_events = query("SELECT * FROM invitation_events WHERE id_inv = '$id_inv' ORDER BY tgl_acara ASC, urutan ASC");
                                            if(!empty($q_events)): foreach($q_events as $evt): ?>
                                            <div class="list-group-item bg-white border mb-2 rounded">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="fw-bold mb-1 text-primary"><?= $evt['nama_acara'] ?></h6>
                                                        <p class="mb-1 small"><i class="feather-calendar"></i> <?= date('d M Y', strtotime($evt['tgl_acara'])) ?> | <i class="feather-clock"></i> <?= $evt['jam_acara'] ?></p>
                                                        <p class="mb-0 small text-muted"><i class="feather-map-pin"></i> <?= $evt['lokasi_nama'] ?></p>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-event" data-id="<?= $evt['id_event'] ?>"><i class="feather-trash"></i></button>
                                                </div>
                                            </div>
                                            <?php endforeach; else: ?>
                                            <div class="text-center py-3 text-muted border border-dashed rounded">Belum ada acara tambahan.</div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="bg-light p-3 rounded border">
                                            <h6 class="fw-bold mb-3"><i class="feather-plus-circle me-1"></i> Tambah Acara</h6>
                                            <div class="row g-2">
                                                <div class="col-md-6"><input type="text" id="new_nama" class="form-control form-control-sm" placeholder="Nama Acara (Akad/Resepsi)"></div>
                                                <div class="col-md-3"><input type="date" id="new_tgl" class="form-control form-control-sm"></div>
                                                <div class="col-md-3"><input type="text" id="new_jam" class="form-control form-control-sm" placeholder="Jam"></div>
                                                <div class="col-md-12"><input type="text" id="new_lokasi" class="form-control form-control-sm" placeholder="Nama Gedung"></div>
                                                <div class="col-md-12"><textarea id="new_alamat" class="form-control form-control-sm" rows="1" placeholder="Alamat"></textarea></div>
                                                <div class="col-md-12"><input type="text" id="new_maps" class="form-control form-control-sm" placeholder="Link Maps"></div>
                                            </div>
                                            <button type="button" id="btnAddEvent" class="btn btn-primary btn-sm w-100 mt-3">Simpan ke Timeline</button>
                                        </div>

                                        <hr>

                                        <h6 class="fw-bold mb-3 text-danger"><i class="feather-video me-2"></i> Live Streaming</h6>
                                        <div class="form-group">
                                            <label class="small text-muted">Link Siaran Langsung (Youtube/Instagram)</label>
                                            <input type="text" name="link_live" class="form-control" value="<?= $row['link_live'] ?? '' ?>" placeholder="https://youtube.com/live/...">
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="galeri">
                                <div class="card stretch stretch-full">
                                    <div class="card-header"><h5 class="card-title">Video & Galeri Foto</h5></div>
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <label class="fw-bold mb-2"><i class="feather-youtube me-2"></i> Video Dokumentasi</label>
                                            <input type="text" name="video_url" class="form-control" value="<?= $row['video_url'] ?? '' ?>" placeholder="Link Youtube (Contoh: https://youtu.be/...)">
                                            <small class="text-muted">Masukkan link video Youtube prewedding atau teaser.</small>
                                        </div>
                                        <div class="mb-4 bg-light p-3 rounded">
                                            <label class="fw-bold d-flex justify-content-between">
                                                Foto Cover <?php if(!empty($row['foto_cover'])): ?><a href="?id=<?= $id_inv ?>&hapus_foto=cover" class="text-danger fs-12" onclick="return confirm('Hapus cover?')">Hapus</a><?php endif; ?>
                                            </label>
                                            <input type="file" name="foto_cover" class="form-control mt-2" accept="image/*">
                                            <?php if(!empty($row['foto_cover'])): ?>
                                                <img src="<?= url('assets/images/uploads/'.$row['foto_cover']) ?>" height="80" class="mt-2 rounded border">
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-4">
                                            <label class="fw-bold mb-2">Upload Banyak Foto</label>
                                            <input type="file" name="galeri_files[]" class="form-control" multiple accept="image/*">
                                        </div>
                                        <div class="row g-2">
                                            <?php
                                            $q_gal = query("SELECT * FROM invitation_gallery WHERE id_inv = '$id_inv' ORDER BY id_galeri DESC");
                                            foreach($q_gal as $g): ?>
                                            <div class="col-6 col-md-3 position-relative">
                                                <img src="<?= url('assets/images/uploads/'.$g['file_foto']) ?>" class="img-fluid rounded border w-100" style="height:100px;object-fit:cover;">
                                                <a href="?id=<?= $id_inv ?>&hapus_galeri=<?= $g['id_galeri'] ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-1" onclick="return confirm('Hapus?')" style="line-height:1;"><i class="feather-x"></i></a>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card stretch stretch-full mt-4">
                                    <div class="card-header"><h5 class="card-title">Rekening</h5></div>
                                    <div class="card-body">
                                        <div class="table-responsive mb-3">
                                            <table class="table table-bordered table-sm">
                                                <?php $gifts = query("SELECT * FROM invitation_gifts WHERE id_inv = '$id_inv'"); ?>
                                                <?php foreach($gifts as $g): ?>
                                                <tr>
                                                    <td><?= $g['nama_bank'] ?></td>
                                                    <td><?= $g['no_rekening'] ?></td>
                                                    <td><a href="?id=<?= $id_inv ?>&hapus_gift=<?= $g['id_gift'] ?>" class="text-danger" onclick="return confirm('Hapus?')"><i class="feather-trash"></i></a></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </table>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-3"><input type="text" name="nama_bank" form="formBank" class="form-control form-control-sm" placeholder="Bank" required></div>
                                            <div class="col-md-4"><input type="text" name="no_rekening" form="formBank" class="form-control form-control-sm" placeholder="No Rek" required></div>
                                            <div class="col-md-3"><input type="text" name="atas_nama" form="formBank" class="form-control form-control-sm" placeholder="A.N" required></div>
                                            <div class="col-md-2"><button type="submit" form="formBank" name="tambah_rekening" class="btn btn-success btn-sm w-100">Add</button></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="musik">
                                <div class="card stretch stretch-full">
                                    <div class="card-header d-flex justify-content-between">
                                        <h5 class="card-title">Playlist Lagu</h5>
                                        <small class="text-muted"><i class="feather-move"></i> Drag & Drop</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-4 bg-light p-3 rounded border">
                                            <h6 class="fw-bold mb-3">Upload Lagu Baru</h6>
                                            <div class="row g-2">
                                                <div class="col-md-5"><input type="text" name="judul_lagu" form="formMusic" class="form-control" placeholder="Judul Lagu"></div>
                                                <div class="col-md-5"><input type="file" name="file_music" form="formMusic" class="form-control" accept=".mp3" required></div>
                                                <div class="col-md-2"><button type="submit" name="upload_music" form="formMusic" class="btn btn-primary w-100">Upload</button></div>
                                            </div>
                                        </div>
                                        <div class="list-group" id="playlistSortable">
                                            <?php
                                            $q_music = query("SELECT * FROM invitation_music WHERE id_inv = '$id_inv' ORDER BY urutan ASC");
                                            if(!empty($q_music)): foreach($q_music as $m): $judul = !empty($m['judul_lagu']) ? $m['judul_lagu'] : $m['file_music']; ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $m['id_music'] ?>" style="cursor: move;">
                                                <div class="d-flex align-items-center">
                                                    <i class="feather-menu text-muted me-3 handle"></i> 
                                                    <div><fw-bold class="text-dark d-block"><?= $judul ?></fw-bold></div>
                                                </div>
                                                <a href="?id=<?= $id_inv ?>&hapus_music=<?= $m['id_music'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')"><i class="feather-trash-2"></i></a>
                                            </div>
                                            <?php endforeach; else: ?><p class="text-muted text-center">Belum ada lagu.</p><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="closing">
                                <div class="card stretch stretch-full">
                                    <div class="card-header"><h5 class="card-title">Bagian Penutup (Closing)</h5></div>
                                    <div class="card-body">
                                        
                                        <div class="mb-4">
                                            <label class="fw-bold mb-2">Background Gambar Penutup</label>
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if(!empty($row['closing_img'])): ?>
                                                    <img src="<?= url('assets/images/uploads/'.$row['closing_img']) ?>" class="rounded border" style="width:100px;height:60px;object-fit:cover;">
                                                    <a href="?id=<?= $id_inv ?>&hapus_foto=closing" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus background?')">Hapus</a>
                                                <?php else: ?>
                                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted small" style="width:100px;height:60px;">No Image</div>
                                                <?php endif; ?>
                                                <input type="file" name="closing_img" class="form-control form-control-sm" accept="image/*">
                                            </div>
                                            <small class="text-muted">*Disarankan gambar gelap/monokrom agar teks terbaca.</small>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Judul Penutup</label>
                                                <input type="text" name="closing_title" class="form-control" value="<?= $row['closing_title'] ?? 'Terima Kasih' ?>" placeholder="Contoh: Terima Kasih / Thank You">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Kalimat Penutup</label>
                                                <textarea name="closing_text" class="form-control" rows="3" placeholder="Contoh: Merupakan suatu kehormatan dan kebahagiaan..."><?= $row['closing_text'] ?></textarea>
                                                <small class="text-muted">Biarkan kosong untuk menggunakan kata-kata default sistem.</small>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Nama Pasangan (Footer)</label>
                                                <input type="text" name="closing_names" class="form-control" value="<?= $row['closing_names'] ?>" placeholder="Contoh: Romeo & Juliet">
                                                <small class="text-muted">Biarkan kosong, sistem akan otomatis mengambil nama panggilan mempelai.</small>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                        
                        <div class="card mt-3 sticky-bottom shadow">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <span class="text-success small fw-bold" id="autoSaveStatus"><i class="feather-check-circle"></i> Perubahan tersimpan otomatis</span>
                                <button type="button" id="btnManualSave" class="btn btn-primary"><i class="feather-save me-2"></i> Simpan & Refresh</button>
                            </div>
                        </div>

                    </form>
                    
                    <form id="formBank" method="POST"></form>
                    <form id="formMusic" method="POST" enctype="multipart/form-data"></form>
                    <form id="formAcara" method="POST"></form>

                </div>

                <div class="col-lg-5 col-xl-4 d-none d-lg-block">
                    <div class="sticky-mockup" style="position: sticky; top: 100px;">
                        <div class="text-center mb-3">
                            <h6 class="fw-bold">Live Preview</h6>
                            <small class="text-muted">Simpan dulu untuk melihat perubahan</small>
                        </div>
                        <div class="smartphone-mockup" style="width: 300px; height: 620px; border: 12px solid #333; border-radius: 30px; overflow: hidden; margin: auto; position: relative; background: #fff;">
                            <iframe id="previewFrame" src="<?= url('preview.php?slug=' . $row['slug']) ?>" style="width: 100%; height: 100%; border: none;"></iframe>
                        </div>
                        <div class="text-center mt-3">
                            <a href="<?= url('preview.php?slug=' . $row['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="feather-external-link"></i> Buka Fullscreen</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php include '../../components/footer.php'; ?>
    </div>
</main>

<div class="modal fade" id="modalMaps" tabindex="-1">...</div>

<?php include '../../components/scripts.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
    const form = document.getElementById('formUndangan');
    const previewFrame = document.getElementById('previewFrame');
    const statusLabel = document.getElementById('autoSaveStatus');
    let timeout = null;

    // --- SORTABLE PLAYLIST ---
    const playlistEl = document.getElementById('playlistSortable');
    if(playlistEl){
        new Sortable(playlistEl, {
            animation: 150, handle: '.handle',
            onEnd: function() {
                let order = [];
                document.querySelectorAll('#playlistSortable .list-group-item').forEach((el) => { order.push(el.getAttribute('data-id')); });
                savePlaylistOrder(order);
            }
        });
    }

    function savePlaylistOrder(order) {
        statusLabel.innerHTML = 'Menyimpan Urutan...';
        let formData = new FormData();
        formData.append('sort_music', JSON.stringify(order));
        formData.append('id_inv', '<?= $id_inv ?>'); 
        fetch('ajax_save.php', { method: 'POST', body: formData }).then(res => res.json()).then(data => {
            if(data.status === 'success') { statusLabel.innerHTML = 'Urutan Tersimpan'; previewFrame.contentWindow.location.reload(); }
        });
    }

    // --- AUTO SAVE TEXT ---
    function saveData() {
        statusLabel.innerHTML = '<i class="feather-loader fa-spin"></i> Menyimpan...';
        statusLabel.className = 'text-warning small fw-bold';
        const formData = new FormData(form);
        fetch('ajax_save.php', { method: 'POST', body: formData }).then(response => response.json()).then(data => {
            if(data.status === 'success') {
                statusLabel.innerHTML = '<i class="feather-check-circle"></i> Tersimpan';
                statusLabel.className = 'text-success small fw-bold';
                if(document.activeElement.tagName !== "INPUT" && document.activeElement.tagName !== "TEXTAREA") {
                    previewFrame.contentWindow.location.reload();
                }
            }
        });
    }

    form.addEventListener('input', function(e) { clearTimeout(timeout); timeout = setTimeout(saveData, 1000); });
    form.addEventListener('change', function(e) { saveData(); });
    document.getElementById('btnManualSave').addEventListener('click', function() { saveData(); previewFrame.contentWindow.location.reload(); });

    const triggerTabList = document.querySelectorAll('#myTab button')
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', event => { event.preventDefault(); tabTrigger.show(); })
    })
</script>