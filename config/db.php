<?php
// Hitungan: 24 jam * 60 menit * 60 detik = 86400 detik
$durasi_session = 86400;

// 1. Set durasi sampah (Garbage Collection) di server
ini_set('session.gc_maxlifetime', $durasi_session);

// 2. Set durasi cookie di browser client
session_set_cookie_params($durasi_session);

// 3. Mulai Session (Cek dulu biar gak error double start)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === KONFIGURASI DATABASE & PROJECT ===
$host       = 'localhost';
$user       = 'root';
$pass       = '';
$ip         = '192.168.100.8';
$db         = 'otwsah'; // Nama DB kita
$BASEURL    = "http://$ip:8080/otwsah"; // Sesuaikan nama folder di htdocs

// Koneksi Native (MySQLi) - Untuk fungsi query bawaanmu
$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Koneksi MySQLi Gagal: " . mysqli_connect_error());
}

// Koneksi PDO - Untuk jaga-jaga kalau butuh fitur advance/secure nantinya
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'DB connection failed: '.$e->getMessage()]));
}

// === HELPER FUNCTIONS ===

// Fungsi URL Dinamis (PENTING BANGET KARENA PAKE FOLDER VIEWS)
function url($url = null){
    global $BASEURL;
    $url_utama = $BASEURL;
    if ($url != null) {
        // Hapus slash di depan jika user ngetik '/assets' jadi 'assets' biar gak double slash
        return $url_utama . '/' . ltrim($url, '/');
    } else {
        return $url_utama;
    }
}

// Fungsi Debugging (DD)
function dd($data, $stop_process = true) {
    echo '<style>
        .debug-container { background-color: #1e1e1e; color: #d4d4d4; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 5px solid #f44336; font-family: "Consolas", monospace; font-size: 14px; z-index: 99999; position: relative; text-align: left; }
        .debug-container pre { margin: 0; white-space: pre-wrap; }
    </style>';
    echo '<div class="debug-container"><strong>DEBUG RESULT:</strong><br><hr style="border-color: #444;"><pre>';
    var_dump($data);
    echo '</pre></div>';
    if ($stop_process) die();
}

// Fungsi Execute Query (Insert/Update/Delete)
function execute($query){
    global $koneksi;
    return mysqli_query($koneksi, $query);
}

// Fungsi Ambil Data (Select)
function query($query){
    global $koneksi;
    $result = mysqli_query($koneksi, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
?>