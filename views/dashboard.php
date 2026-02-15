<?php
require_once '../config/db.php';

// Cek login
if (!isset($_SESSION['user'])) {
    header("Location: " . url('views/authentication/login.php'));
    exit;
}

$user = $_SESSION['user'];
$role = $user['role']; // 'admin' atau 'user'
$id_user = $user['id_user'];

// === LOGIC UTAMA: Pisahkan Query Admin & User ===

if ($role == 'admin') {
    // --- MODE ADMIN (Lihat Semua Data) ---
    
    // 1. Total Pengguna (Selain Admin)
    $q_users = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role = 'user'");
    $stat1 = mysqli_fetch_assoc($q_users)['total'];
    $label1 = "Total Pengguna";
    $icon1 = "feather-users";
    $color1 = "primary";

    // 2. Total Undangan (Seluruh Sistem)
    $q_inv = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM invitations");
    $stat2 = mysqli_fetch_assoc($q_inv)['total'];
    $label2 = "Total Undangan Dibuat";
    $icon2 = "feather-file-text";
    $color2 = "success";

    // 3. Undangan Lunas (Pendapatan)
    $q_paid = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM invitations WHERE status_bayar = 'lunas'");
    $stat3 = mysqli_fetch_assoc($q_paid)['total'];
    $label3 = "Undangan Terbayar";
    $icon3 = "feather-dollar-sign";
    $color3 = "warning";

    // 4. Data Tabel (5 User Terbaru)
    $table_title = "Pendaftar Terbaru";
    $recent_data = query("SELECT fullname, email, created_at FROM users WHERE role='user' ORDER BY created_at DESC LIMIT 5");

} else {
    // --- MODE USER (Lihat Data Sendiri) ---

    // 1. Undangan Saya
    $q_inv = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM invitations WHERE id_user = '$id_user'");
    $stat1 = mysqli_fetch_assoc($q_inv)['total'];
    $label1 = "Undangan Saya";
    $icon1 = "feather-mail";
    $color1 = "primary";

    // 2. Total Tamu
    $q_tamu = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM guestbook g JOIN invitations i ON g.id_inv = i.id_inv WHERE i.id_user = '$id_user'");
    $stat2 = mysqli_fetch_assoc($q_tamu)['total'];
    $label2 = "Total Tamu";
    $icon2 = "feather-users";
    $color2 = "success";

    // 3. Ucapan Masuk
    $q_ucapan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM guestbook g JOIN invitations i ON g.id_inv = i.id_inv WHERE i.id_user = '$id_user' AND g.ucapan IS NOT NULL");
    $stat3 = mysqli_fetch_assoc($q_ucapan)['total'];
    $label3 = "Ucapan Masuk";
    $icon3 = "feather-message-square";
    $color3 = "info";

    // 4. Data Tabel (5 Undangan Terakhir Saya)
    $table_title = "Undangan Terakhir Saya";
    $recent_data = query("SELECT slug, tgl_resepsi, status_bayar, id_inv FROM invitations WHERE id_user = '$id_user' ORDER BY id_inv DESC LIMIT 5");
}

$pageTitle = "Dashboard";
$pageHeader = ($role == 'admin') ? "Dashboard Admin" : "Ringkasan";
$breadcrumbs = ["Home", "Dashboard"];

include '../components/header.php';
include '../components/sidebar.php';
include '../components/navbar.php';
?>

<main class="nxl-container">
    <div class="nxl-content">
        <?php include '../components/page_header.php'; ?>

        <div class="main-content">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="text-white fw-bold">Halo, <?= htmlspecialchars($user['fullname']) ?>! 👋</h2>
                                    <p class="mb-0 text-white-50">
                                        <?= ($role == 'admin') ? 'Pantau performa bisnis OtwSah hari ini.' : 'Siap membagikan kabar bahagiamu hari ini?' ?>
                                    </p>
                                </div>
                                <div>
                                    <?php if($role == 'user'): ?>
                                        <a href="undangan_baru.php" class="btn btn-light text-primary fw-bold">
                                            <i class="feather-plus me-2"></i> Buat Undangan
                                        </a>
                                    <?php else: ?>
                                        <a href="laporan.php" class="btn btn-light text-primary fw-bold">
                                            <i class="feather-download me-2"></i> Download Laporan
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-soft-<?= $color1 ?> text-<?= $color1 ?>">
                                    <i class="<?= $icon1 ?>"></i>
                                </div>
                                <div>
                                    <div class="text-muted mb-1"><?= $label1 ?></div>
                                    <h4 class="fw-bolder mb-0"><?= $stat1 ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-soft-<?= $color2 ?> text-<?= $color2 ?>">
                                    <i class="<?= $icon2 ?>"></i>
                                </div>
                                <div>
                                    <div class="text-muted mb-1"><?= $label2 ?></div>
                                    <h4 class="fw-bolder mb-0"><?= $stat2 ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-soft-<?= $color3 ?> text-<?= $color3 ?>">
                                    <i class="<?= $icon3 ?>"></i>
                                </div>
                                <div>
                                    <div class="text-muted mb-1"><?= $label3 ?></div>
                                    <h4 class="fw-bolder mb-0"><?= $stat3 ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title"><?= $table_title ?></h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <?php if($role == 'admin'): ?>
                                                <th>Nama Lengkap</th>
                                                <th>Email</th>
                                                <th>Tanggal Daftar</th>
                                            <?php else: ?>
                                                <th>URL Undangan</th>
                                                <th>Tanggal Acara</th>
                                                <th>Status</th>
                                                <th class="text-end">Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($recent_data)): ?>
                                            <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data.</td></tr>
                                        <?php else: ?>
                                            <?php foreach($recent_data as $row): ?>
                                                <tr>
                                                    <?php if($role == 'admin'): ?>
                                                        <td><span class="fw-bold"><?= htmlspecialchars($row['fullname']) ?></span></td>
                                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                                        <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                                                    <?php else: ?>
                                                        <td>
                                                            <a href="<?= url($row['slug']) ?>" target="_blank" class="fw-bold text-primary">
                                                                /<?= $row['slug'] ?>
                                                            </a>
                                                        </td>
                                                        <td><?= $row['tgl_resepsi'] ? date('d M Y', strtotime($row['tgl_resepsi'])) : '-' ?></td>
                                                        <td>
                                                            <span class="badge bg-soft-<?= $row['status_bayar'] == 'lunas' ? 'success' : 'warning' ?> text-<?= $row['status_bayar'] == 'lunas' ? 'success' : 'warning' ?>">
                                                                <?= ucfirst($row['status_bayar']) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-end">
                                                            <a href="edit_undangan.php?id=<?= $row['id_inv'] ?>" class="btn btn-sm btn-light-primary"><i class="feather-edit"></i></a>
                                                        </td>
                                                    <?php endif; ?>
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
        <?php include '../components/footer.php'; ?>
    </div>
</main>
<?php include '../components/scripts.php'; ?>