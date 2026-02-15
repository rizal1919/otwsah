<?php
// Naik 2 tingkat ke config
require_once '../../config/db.php';

// Cek Login & Role Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: " . url('views/dashboard.php'));
    exit;
}

// Logic Hapus Tema
if (isset($_GET['hapus'])) {
    $id = htmlspecialchars($_GET['hapus']);
    
    // Ambil info gambar buat dihapus (Naik 2 tingkat ke assets)
    $q_cek = query("SELECT preview_img FROM themes WHERE id_tema = '$id'")[0];
    if ($q_cek) {
        $file_path = "../../assets/images/themes/" . $q_cek['preview_img'];
        if (file_exists($file_path)) unlink($file_path); 
        
        execute("DELETE FROM themes WHERE id_tema = '$id'");
        echo "<script>alert('Tema berhasil dihapus!'); window.location='index.php';</script>";
    }
}

$themes = query("SELECT * FROM themes ORDER BY id_tema DESC");

$pageTitle = "Kelola Tema";
$pageHeader = "Katalog Tema";
$breadcrumbs = ["Master Data", "Tema"];

// Naik 2 tingkat ke components
include '../../components/header.php';
include '../../components/sidebar.php';
include '../../components/navbar.php';
?>

<main class="nxl-container">
    <div class="nxl-content">
        <?php include '../../components/page_header.php'; ?>

        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Daftar Tema Tersedia</h5>
                            <a href="tambah.php" class="btn btn-primary">
                                <i class="feather-plus me-2"></i> Tambah Tema Baru
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Preview</th>
                                            <th>Nama Tema</th>
                                            <th>Kode Folder</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($themes)): ?>
                                            <tr><td colspan="4" class="text-center py-5">Belum ada tema. Yuk upload!</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($themes as $row): ?>
                                                <tr>
                                                    <td>
                                                        <img src="<?= url('assets/images/themes/' . $row['preview_img']) ?>" 
                                                             alt="Preview" class="rounded border" style="width: 80px; height: 120px; object-fit: cover;">
                                                    </td>
                                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_tema']) ?></td>
                                                    <td><code>/themes/<?= htmlspecialchars($row['folder_name']) ?>/</code></td>
                                                    <td class="text-end">
                                                        <a href="?hapus=<?= $row['id_tema'] ?>" class="btn btn-sm btn-light-danger" onclick="return confirm('Yakin hapus tema ini?')">
                                                            <i class="feather-trash-2"></i> Hapus
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../../components/footer.php'; ?>
    </div>
</main>

<?php include '../../components/scripts.php'; ?>