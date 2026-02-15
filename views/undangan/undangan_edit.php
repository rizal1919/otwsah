<?php
require_once '../../config/db.php';

// Cek Login
if (!isset($_SESSION['user'])) {
    header("Location: " . url('views/authentication/login.php'));
    exit;
}

$id_inv = htmlspecialchars($_GET['id']);
$id_user_login = $_SESSION['user']['id_user'];

// Ambil Data Undangan
$query = "SELECT a.*, b.*, t.folder_name 
    FROM invitations a
    JOIN bride_groom b ON a.id_inv = b.id_inv
    JOIN themes t ON a.id_tema = t.id_tema
    WHERE a.id_inv = '$id_inv' AND a.id_user = '$id_user_login'";
$data = query($query);

if (empty($data)) { echo "Akses ditolak."; exit; }
$row = $data[0];

// --- LOGIC PHP (Backend Action) ---

// 1. HAPUS FOTO GALERI
if (isset($_GET['hapus_galeri'])) {
    $id_g = htmlspecialchars($_GET['hapus_galeri']);
    execute("DELETE FROM invitation_gallery WHERE id_galeri = '$id_g'");
    header("Location: ?id=$id_inv"); exit;
}

// 2. HAPUS FOTO PROFIL
if (isset($_GET['hapus_foto'])) {
    $jenis = htmlspecialchars($_GET['hapus_foto']);
    $kolom = "foto_" . $jenis;
    if(in_array($jenis, ['pria', 'wanita', 'cover'])){
        execute("UPDATE invitations SET $kolom = NULL WHERE id_inv = '$id_inv'");
    }
    header("Location: ?id=$id_inv"); exit;
}

// 3. TAMBAH REKENING
if (isset($_POST['tambah_rekening'])) {
    $nama_bank = htmlspecialchars($_POST['nama_bank']);
    $no_rekening = htmlspecialchars($_POST['no_rekening']);
    $atas_nama = htmlspecialchars($_POST['atas_nama']);
    if(!empty($nama_bank)) {
        execute("INSERT INTO invitation_gifts (id_inv, nama_bank, no_rekening, atas_nama) VALUES ('$id_inv', '$nama_bank', '$no_rekening', '$atas_nama')");
        header("Location: ?id=$id_inv"); exit;
    }
}

// 4. HAPUS REKENING
if (isset($_GET['hapus_gift'])) {
    $id_gift = htmlspecialchars($_GET['hapus_gift']);
    execute("DELETE FROM invitation_gifts WHERE id_gift = '$id_gift'");
    header("Location: ?id=$id_inv"); exit;
}

// 5. TAMBAH MUSIK (UPLOAD MP3)
if (isset($_POST['upload_music'])) {
    $judul_lagu = htmlspecialchars($_POST['judul_lagu']);
    $file = $_FILES['file_music'];
    
    if ($file['error'] === 0) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext == 'mp3') {
            $newName = time() . '_' . uniqid() . '.mp3';
            // Pastikan folder ada
            if (!is_dir("../../assets/music/uploads")) mkdir("../../assets/music/uploads", 0777, true);
            
            if(move_uploaded_file($file['tmp_name'], "../../assets/music/uploads/" . $newName)){
                execute("INSERT INTO invitation_music (id_inv, file_music, judul_lagu) VALUES ('$id_inv', '$newName', '$judul_lagu')");
                header("Location: ?id=$id_inv"); exit;
            }
        } else {
            echo "<script>alert('Format harus MP3!');</script>";
        }
    }
}

// 6. HAPUS MUSIK
if (isset($_GET['hapus_music'])) {
    $id_m = htmlspecialchars($_GET['hapus_music']);
    // Ambil nama file untuk dihapus (opsional)
    // $m = query("SELECT file_music FROM invitation_music WHERE id_music='$id_m'")[0];
    // unlink("../../assets/music/uploads/".$m['file_music']);
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
                            <li class="nav-item"><button class="nav-link" id="acara-tab" data-bs-toggle="tab" data-bs-target="#acara" type="button">2. Acara</button></li>
                            <li class="nav-item"><button class="nav-link" id="galeri-tab" data-bs-toggle="tab" data-bs-target="#galeri" type="button">3. Galeri</button></li>
                            <li class="nav-item"><button class="nav-link" id="musik-tab" data-bs-toggle="tab" data-bs-target="#musik" type="button">4. Musik Latar</button></li>
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
                                                        <a href="?id=<?= $id_inv ?>&hapus_foto=pria" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" onclick="return confirm('Hapus foto pria?')" style="width:24px;height:24px;line-height:12px;">&times;</a>
                                                    <?php else: ?>
                                                        <img src="https://via.placeholder.com/100x100?text=Pria" class="rounded-circle border">
                                                    <?php endif; ?>
                                                </div>
                                                <input type="file" name="foto_pria" class="form-control form-control-sm" accept="image/*">
                                            </div>
                                            <div class="col-md-9">
                                                <div class="mb-2"><input type="text" name="nama_pria" class="form-control" value="<?= $row['nama_pria'] ?>" placeholder="Nama Panggilan"></div>
                                                <div class="mb-2"><input type="text" name="nama_pria_lengkap" class="form-control" value="<?= $row['nama_pria_lengkap'] ?>" placeholder="Nama Lengkap"></div>
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
                                                        <a href="?id=<?= $id_inv ?>&hapus_foto=wanita" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" onclick="return confirm('Hapus foto wanita?')" style="width:24px;height:24px;line-height:12px;">&times;</a>
                                                    <?php else: ?>
                                                        <img src="https://via.placeholder.com/100x100?text=Wanita" class="rounded-circle border">
                                                    <?php endif; ?>
                                                </div>
                                                <input type="file" name="foto_wanita" class="form-control form-control-sm" accept="image/*">
                                            </div>
                                            <div class="col-md-9">
                                                <div class="mb-2"><input type="text" name="nama_wanita" class="form-control" value="<?= $row['nama_wanita'] ?>" placeholder="Nama Panggilan"></div>
                                                <div class="mb-2"><input type="text" name="nama_wanita_lengkap" class="form-control" value="<?= $row['nama_wanita_lengkap'] ?>" placeholder="Nama Lengkap"></div>
                                                <div class="row">
                                                    <div class="col-6"><input type="text" name="ortu_wanita_ayah" class="form-control" value="<?= $row['ortu_wanita_ayah'] ?>" placeholder="Nama Ayah"></div>
                                                    <div class="col-6"><input type="text" name="ortu_wanita_ibu" class="form-control" value="<?= $row['ortu_wanita_ibu'] ?>" placeholder="Nama Ibu"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="acara">
                                <div class="card stretch stretch-full">
                                    <div class="card-header"><h5 class="card-title">Detail Acara</h5></div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Tanggal</label>
                                                <input type="date" name="tgl_resepsi" class="form-control" value="<?= $row['tgl_resepsi'] ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Jam</label>
                                                <input type="text" name="jam_acara" class="form-control" value="<?= $row['jam_acara'] ?>" placeholder="08:00 - Selesai">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Lokasi</label>
                                                <input type="text" name="lokasi_nama" class="form-control" value="<?= $row['lokasi_nama'] ?>" placeholder="Nama Gedung...">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Alamat</label>
                                                <textarea name="lokasi_alamat" class="form-control" rows="2"><?= $row['lokasi_alamat'] ?></textarea>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold d-flex justify-content-between">
                                                    Link Maps <a href="#" data-bs-toggle="modal" data-bs-target="#modalMaps" class="text-primary fs-12">Bantuan</a>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-map-pin"></i></span>
                                                    <input type="text" name="link_maps" class="form-control" value="<?= $row['lokasi_map'] ?>" placeholder="Paste link...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="galeri">
                                <div class="card stretch stretch-full">
                                    <div class="card-header"><h5 class="card-title">Galeri Foto</h5></div>
                                    <div class="card-body">
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
                                    <div class="card-header"><h5 class="card-title">Playlist Lagu</h5></div>
                                    <div class="card-body">
                                        
                                        <div class="mb-4 bg-light p-3 rounded border">
                                            <h6 class="fw-bold mb-3"><i class="feather-music me-2"></i>Upload Lagu Baru</h6>
                                            <div class="row g-2">
                                                <div class="col-md-5">
                                                    <input type="text" name="judul_lagu" form="formMusic" class="form-control" placeholder="Judul Lagu (Opsional)">
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="file" name="file_music" form="formMusic" class="form-control" accept=".mp3,audio/mpeg" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" name="upload_music" form="formMusic" class="btn btn-primary w-100">Upload</button>
                                                </div>
                                            </div>
                                            <small class="text-muted mt-2 d-block">* Format wajib MP3. Lagu akan diputar berurutan dari yang teratas.</small>
                                        </div>

                                        <h6 class="fw-bold">Daftar Lagu (Playlist):</h6>
                                        <div class="list-group">
                                            <?php
                                            // Ambil lagu khusus untuk undangan ini
                                            $q_music = query("SELECT * FROM invitation_music WHERE id_inv = '$id_inv' ORDER BY id_music ASC");
                                            
                                            if(!empty($q_music)):
                                                $no = 1;
                                                foreach($q_music as $m): 
                                                    $judul = !empty($m['judul_lagu']) ? $m['judul_lagu'] : $m['file_music'];
                                            ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-light text-dark border me-3"><?= $no++ ?></span>
                                                    <div>
                                                        <fw-bold class="text-dark d-block"><?= $judul ?></fw-bold>
                                                        <small class="text-muted">
                                                            <a href="<?= url('assets/music/uploads/'.$m['file_music']) ?>" target="_blank" class="text-decoration-none text-muted"><i class="feather-play-circle"></i> Putar Preview</a>
                                                        </small>
                                                    </div>
                                                </div>
                                                <a href="?id=<?= $id_inv ?>&hapus_music=<?= $m['id_music'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus lagu ini?')">
                                                    <i class="feather-trash-2"></i>
                                                </a>
                                            </div>
                                            <?php endforeach; else: ?>
                                            <div class="text-center py-4 text-muted border rounded border-dashed">
                                                <i class="feather-music fa-2x mb-2 d-block"></i>
                                                Belum ada lagu yang diupload. <br> Lagu default tema akan diputar.
                                            </div>
                                            <?php endif; ?>
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

<script>
    const form = document.getElementById('formUndangan');
    const previewFrame = document.getElementById('previewFrame');
    const statusLabel = document.getElementById('autoSaveStatus');
    let timeout = null;

    // AJAX Save Logic
    function saveData() {
        statusLabel.innerHTML = '<i class="feather-loader fa-spin"></i> Menyimpan...';
        statusLabel.className = 'text-warning small fw-bold';
        const formData = new FormData(form);
        fetch('ajax_save.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
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
    
    document.getElementById('btnManualSave').addEventListener('click', function() {
        saveData();
        previewFrame.contentWindow.location.reload();
    });

    const triggerTabList = document.querySelectorAll('#myTab button')
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', event => {
            event.preventDefault()
            tabTrigger.show()
        })
    })
</script>