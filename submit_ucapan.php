<?php
require_once 'config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_inv = htmlspecialchars($_POST['id_inv']);
    $nama   = htmlspecialchars($_POST['nama_tamu']);
    $ucapan = htmlspecialchars($_POST['ucapan']);
    $hadir  = htmlspecialchars($_POST['kehadiran']);
    // Tangkap parent_id, jika tidak ada set ke 0 (Komentar Utama)
    $parent = isset($_POST['parent_id']) ? htmlspecialchars($_POST['parent_id']) : 0;

    if (!empty($nama) && !empty($ucapan)) {
        $q = "INSERT INTO guestbook (id_inv, parent_id, nama_tamu, ucapan, konfirmasi_hadir) 
              VALUES ('$id_inv', '$parent', '$nama', '$ucapan', '$hadir')";
        
        if (mysqli_query($koneksi, $q)) {
            echo json_encode([
                'status' => 'success', 
                'data' => [
                    'nama' => $nama,
                    'ucapan' => nl2br($ucapan),
                    'hadir' => $hadir,
                    'waktu' => date('d M Y • H:i') . ' WIB',
                    'parent_id' => $parent
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Gagal menyimpan database']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Data tidak lengkap']);
    }
}
?>