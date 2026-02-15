<?php
require_once '../../config/db.php';

// Cek Login & Role Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: " . url('views/dashboard.php'));
    exit;
}

$error = null;
$success = null;

if (isset($_POST['simpan'])) {
    $nama_tema   = htmlspecialchars($_POST['nama_tema']);
    $folder_name = htmlspecialchars($_POST['folder_name']);
    
    // Upload Gambar
    $foto           = $_FILES['preview_img'];
    $nama_foto      = time() . "_" . $foto['name'];
    $tmp_foto       = $foto['tmp_name'];
    $folder_tujuan  = "../../assets/images/themes/"; // Path simpan gambar

    $ekstensi_ok    = ['png', 'jpg', 'jpeg', 'webp'];
    $x              = explode('.', $nama_foto);
    $ekstensi       = strtolower(end($x));

    if (in_array($ekstensi, $ekstensi_ok)) {
        if (!is_dir($folder_tujuan)) mkdir($folder_tujuan, 0777, true);

        if (move_uploaded_file($tmp_foto, $folder_tujuan . $nama_foto)) {
            $q = "INSERT INTO themes (nama_tema, folder_name, preview_img) VALUES ('$nama_tema', '$folder_name', '$nama_foto')";
            if (execute($q)) {
                $success = "Tema berhasil ditambahkan!";
            } else {
                $error = "Gagal simpan ke database.";
            }
        } else {
            $error = "Gagal upload gambar ke folder assets.";
        }
    } else {
        $error = "Format gambar harus JPG, PNG, atau WEBP.";
    }
}

$pageTitle = "Tambah Tema";
$pageHeader = "Tambah Tema Baru";
$breadcrumbs = ["Master Data", "Tema", "Baru"];

include '../../components/header.php';
include '../../components/sidebar.php';
include '../../components/navbar.php';
?>

<main class="nxl-container">
    <div class="nxl-content">
        <?php include '../../components/page_header.php'; ?>

        <div class="main-content">
            <div class="row">
                
                <div class="col-lg-8">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Informasi Tema</h5>
                        </div>
                        <div class="card-body">
                            
                            <?php if($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>
                            
                            <?php if($success): ?>
                                <div class="alert alert-success">
                                    <?= $success ?> 
                                    <a href="index.php" class="fw-bold">Kembali ke List Tema</a>
                                </div>
                            <?php endif; ?>

                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="mb-4">
                                    <label class="form-label">Nama Tema <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_tema" class="form-control" placeholder="Misal: Wedding Rustic Gold" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Nama Folder (Kode) <span class="text-danger">*</span></label>
                                    <input type="text" name="folder_name" class="form-control" placeholder="Misal: rustic_gold" required>
                                    <small class="text-muted d-block mt-1">
                                        <i class="feather-info"></i> Ini adalah nama folder tempat kamu menyimpan file kodingan (index.php) tema ini nanti. Jangan pakai spasi.
                                    </small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Thumbnail / Gambar Preview <span class="text-danger">*</span></label>
                                    <input type="file" id="imgInput" name="preview_img" class="form-control" accept="image/*" required>
                                    <small class="text-muted">Upload screenshot tampilan tema agar user bisa melihat contohnya.</small>
                                </div>

                                <div class="d-flex gap-2 mt-5">
                                    <button type="submit" name="simpan" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> Simpan Tema
                                    </button>
                                    <a href="index.php" class="btn btn-light-secondary">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card stretch stretch-full bg-light">
                        <div class="card-header">
                            <h5 class="card-title">Preview Tampilan</h5>
                        </div>
                        <div class="card-body text-center d-flex align-items-center justify-content-center" style="min-height: 400px;">
                            <div id="placeholderText" class="text-muted">
                                <i class="feather-image fs-1 d-block mb-2 opacity-25"></i>
                                <span>Gambar akan muncul di sini<br>setelah kamu memilih file.</span>
                            </div>
                            
                            <img id="previewImage" src="#" alt="Preview Tema" class="img-fluid rounded shadow-sm d-none" style="max-height: 500px; object-fit: contain;">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php include '../../components/footer.php'; ?>
    </div>
</main>

<?php include '../../components/scripts.php'; ?>

<script>
    const imgInput = document.getElementById('imgInput');
    const previewImage = document.getElementById('previewImage');
    const placeholderText = document.getElementById('placeholderText');

    imgInput.onchange = evt => {
        const [file] = imgInput.files;
        if (file) {
            previewImage.src = URL.createObjectURL(file);
            previewImage.classList.remove('d-none'); // Tampilkan gambar
            placeholderText.classList.add('d-none'); // Sembunyikan teks placeholder
        }
    }
</script>