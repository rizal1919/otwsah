<?php
require_once '../../config/db.php';

// Hapus semua session
session_destroy();
unset($_SESSION['user']);

// Redirect ke halaman login
header("Location: " . url('views/authentication/login.php'));
exit;
?>