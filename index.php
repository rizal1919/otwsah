<?php
/**
 * OtwSah - Online Wedding Invitation
 * File: Root Index
 */

require_once 'config/db.php';

// LOGIKA REDIRECT
if (isset($_SESSION['user'])) {
    // 1. Jika User Sudah Login -> Masuk Dashboard
    header("Location: " . url('views/dashboard.php'));
    exit;
} else {
    // 2. Jika Belum Login -> Tampilkan Landing Page
    // Kita include file landing page di sini agar URL tetap bersih (localhost/otwsah/)
    require_once 'views/landing.php';
}
?>