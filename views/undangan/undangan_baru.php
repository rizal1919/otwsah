<?php
require_once '../../config/db.php';

// Cek Login
if (!isset($_SESSION['user'])) {
    header("Location: " . url('views/authentication/login.php'));
    exit;
}

$error = null;
$success = null;

// PROSES SIMPAN UNDANGAN
if (isset($_POST['buat_undangan'])) {
    $id_user    = $_SESSION['user']['id_user'];
    $id_tema    = htmlspecialchars($_POST['id_tema']);
    $slug       = htmlspecialchars($_POST['slug']); // Link unik
    $tgl_resepsi= htmlspecialchars($_POST['tgl_resepsi']);
    
    // 1. Validasi Slug (Cek apakah link sudah dipakai orang lain)
    $slug_safe = mysqli_real_escape_string($koneksi, $slug);
    $cek_slug = mysqli_query($koneksi, "SELECT id_inv FROM invitations WHERE slug = '$slug_safe'");
    
    if (mysqli_num_rows($cek_slug) > 0) {
        $error = "Link <b>$slug</b> sudah dipakai orang lain. Coba ganti yang lain ya!";
    } else {
        // 2. Insert ke Tabel Invitations
        $q_inv = "INSERT INTO invitations (id_user, id_tema, slug, tgl_resepsi, status_bayar) 
                  VALUES ('$id_user', '$id_tema', '$slug_safe', '$tgl_resepsi', 'pending')";
        
        if (execute($q_inv)) {
            // Ambil ID Undangan barusan
            $last_id = mysqli_insert_id($koneksi);

            // 3. Insert Data Dummy ke Tabel Detail Pengantin (Supaya nanti pas Edit tidak error data kosong)
            $q_detail = "INSERT INTO bride_groom (id_inv) VALUES ('$last_id')";
            execute($q_detail);

            // Redirect ke Halaman Edit (Nanti kita buat)
            echo "<script>
                alert('Undangan berhasil dibuat! Silakan lengkapi datanya.');
                window.location = 'undangan_edit.php?id=$last_id'; 
            </script>";
            exit;
        } else {
            $error = "Gagal membuat undangan: " . mysqli_error($koneksi);
        }
    }
}

// Ambil Daftar Tema untuk Dipilih
$themes = query("SELECT * FROM themes ORDER BY id_tema DESC");

$pageTitle = "Buat Undangan";
$pageHeader = "Buat Undangan Baru";
$breadcrumbs = ["Undangan", "Baru"];

include '../../components/header.php';
include '../../components/sidebar.php';
include '../../components/navbar.php';
?>

<main class="nxl-container">
    <div class="nxl-content">
        <?php include '../../components/page_header.php'; ?>

        <div class="main-content">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <form action="" method="POST">
                        
                        <?php if($error): ?>
                            <div class="alert alert-danger mb-4"><?= $error ?></div>
                        <?php endif; ?>

                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">1. Pilih Tema Tampilan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <?php if(empty($themes)): ?>
                                        <div class="col-12 text-center text-muted py-5">
                                            <i class="feather-alert-circle fs-1"></i>
                                            <p class="mt-2">Belum ada tema tersedia. Hubungi Admin.</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach($themes as $theme): ?>
                                            <div class="col-md-4 col-sm-6">
                                                <label class="w-100 cursor-pointer">
                                                    <input type="radio" name="id_tema" value="<?= $theme['id_tema'] ?>" class="d-none theme-radio" required>
                                                    <div class="theme-card border rounded p-2 text-center position-relative">
                                                        <img src="<?= url('assets/images/themes/' . $theme['preview_img']) ?>" 
                                                             class="img-fluid rounded mb-2 shadow-sm" 
                                                             style="height: 250px; object-fit: cover; width: 100%;">
                                                        
                                                        <h6 class="fw-bold text-dark mt-2 mb-0"><?= $theme['nama_tema'] ?></h6>
                                                        
                                                        <div class="check-icon position-absolute top-50 start-50 translate-middle text-primary d-none">
                                                            <i class="feather-check-circle fs-1 bg-white rounded-circle"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="card stretch stretch-full mt-4">
                            <div class="card-header">
                                <h5 class="card-title">2. Informasi Dasar</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Link Undangan (Slug) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">otwsah.com/</span>
                                        <input type="text" name="slug" class="form-control" placeholder="romeo-juliet" required pattern="[a-zA-Z0-9-]+" title="Hanya boleh huruf, angka, dan strip (-)">
                                    </div>
                                    <small class="text-muted">Gunakan nama panggilan tanpa spasi. Contoh: <code>budi-rani</code></small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Tanggal Resepsi <span class="text-danger">*</span></label>
                                    <input type="date" name="tgl_resepsi" class="form-control" required>
                                </div>

                                <div class="text-end">
                                    <a href="dashboard.php" class="btn btn-light-secondary me-2">Batal</a>
                                    <button type="submit" name="buat_undangan" class="btn btn-primary btn-lg">
                                        <i class="feather-save me-2"></i> Buat Undangan Sekarang
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <?php include '../../components/footer.php'; ?>
    </div>
</main>

<?php include '../../components/scripts.php'; ?>

<style>
    /* Saat radio button dipilih, kasih border biru ke card */
    .theme-radio:checked + .theme-card {
        border: 2px solid #3461ff !important;
        background-color: #f0f7ff;
    }
    /* Tampilkan ikon checklist */
    .theme-radio:checked + .theme-card .check-icon {
        display: block !important;
    }
    .theme-card {
        transition: all 0.2s;
    }
    .theme-card:hover {
        transform: translateY(-5px);
    }
</style>