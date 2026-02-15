<?php
// Pastikan session sudah start (jika dipanggil terpisah)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil data user dari session, atau gunakan default jika belum login
$user_fullname = isset($_SESSION['user']['fullname']) ? $_SESSION['user']['fullname'] : 'Pengguna OtwSah';
$user_email    = isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : 'user@otwsah.com';
$user_role     = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : 'User';

// Tentukan badge role
$role_badge = ($user_role == 'admin') ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success';
?>

<header class="nxl-header">
    <div class="header-wrapper">
        <div class="header-left d-flex align-items-center gap-4">
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">
                
                <div class="dropdown nxl-h-item">
                    <a class="nxl-head-link me-3" data-bs-toggle="dropdown" href="#" role="button" data-bs-auto-close="outside">
                        <i class="feather-bell"></i>
                        <span class="badge bg-danger nxl-h-badge">1</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
                        <div class="d-flex justify-content-between align-items-center notifications-head">
                            <h6 class="fw-bold text-dark mb-0">Notifikasi</h6>
                        </div>
                        <div class="notifications-item">
                            <div class="notifications-desc">
                                <a href="javascript:void(0);" class="font-body text-truncate-2-line">Selamat datang di OtwSah!</a>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="notifications-date text-muted border-bottom border-bottom-dashed">Baru saja</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="<?= url('assets/images/avatar/1.png') ?>" alt="user-image" class="img-fluid user-avtar me-0">
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="<?= url('assets/images/avatar/1.png') ?>" alt="user-image" class="img-fluid user-avtar">
                                <div>
                                    <h6 class="text-dark mb-0">
                                        <?= htmlspecialchars($user_fullname) ?> 
                                        <span class="badge <?= $role_badge ?> ms-1"><?= strtoupper($user_role) ?></span>
                                    </h6>
                                    <span class="fs-12 fw-medium text-muted"><?= htmlspecialchars($user_email) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="dropdown">
                                <span class="hstack">
                                    <i class="wd-10 ht-10 border border-2 border-gray-1 bg-success rounded-circle me-2"></i>
                                    <span>Active</span>
                                </span>
                                <i class="feather-chevron-right ms-auto me-0"></i>
                            </a>
                            <div class="dropdown-menu">
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-10 ht-10 border border-2 border-gray-1 bg-success rounded-circle me-2"></i>
                                        <span>Active</span>
                                    </span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-10 ht-10 border border-2 border-gray-1 bg-warning rounded-circle me-2"></i>
                                        <span>Busy</span>
                                    </span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-10 ht-10 border border-2 border-gray-1 bg-secondary rounded-circle me-2"></i>
                                        <span>Inactive</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                        
                        <div class="dropdown-divider"></div>

                        <a href="profile.php" class="dropdown-item">
                            <i class="feather-user"></i>
                            <span>Profile Details</span>
                        </a>
                        
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-activity"></i>
                            <span>Activity Feed</span>
                        </a>

                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-dollar-sign"></i>
                            <span>Billing Details</span>
                        </a>
                        
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-bell"></i>
                            <span>Notifications</span>
                        </a>
                        
                        <a href="settings.php" class="dropdown-item">
                            <i class="feather-settings"></i>
                            <span>Account Settings</span>
                        </a>

                        <div class="dropdown-divider"></div>
                        
                        <a href="<?= url('views/authentication/logout.php') ?>" class="dropdown-item text-danger">
                            <i class="feather-log-out"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>