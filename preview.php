<?php
// Pastikan path ke config benar (karena file ini ada di root)
require_once 'config/db.php';

if (!isset($_GET['slug'])) {
    die("Error: Slug tidak ditemukan.");
}

$slug = mysqli_real_escape_string($koneksi, $_GET['slug']);

// Ambil Data Undangan & Tema
$query = "SELECT a.*, b.*, t.folder_name 
          FROM invitations a
          JOIN bride_groom b ON a.id_inv = b.id_inv
          JOIN themes t ON a.id_tema = t.id_tema
          WHERE a.slug = '$slug'";

$result = query($query);

if (empty($result)) {
    die("Error: Data undangan tidak ditemukan untuk slug: " . htmlspecialchars($slug));
}

$inv = $result[0];
$folder_tema = $inv['folder_name'];

// Cek folder tema
$path_tema = "assets/themes/" . $folder_tema . "/index.php";

if (file_exists($path_tema)) {
    include $path_tema;
} else {
    echo "<div style='text-align:center; padding:50px;'>";
    echo "<h3>Maaf, File Tema Tidak Ditemukan!</h3>";
    echo "<p>Sistem mencari file di: <code>$path_tema</code></p>";
    echo "<p>Pastikan kamu sudah membuat folder <b>assets/themes/$folder_tema/</b> dan file <b>index.php</b> di dalamnya.</p>";
    echo "</div>";
}
?>