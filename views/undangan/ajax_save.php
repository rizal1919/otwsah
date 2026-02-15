<?php
require_once '../../config/db.php';

if (!isset($_SESSION['user']) || !isset($_POST['id_inv'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
    exit;
}

$id_inv = htmlspecialchars($_POST['id_inv']);

// 1. UPDATE DATA TEKS (Mempelai & Acara)
$nama_pria = htmlspecialchars($_POST['nama_pria']);
$nama_wanita = htmlspecialchars($_POST['nama_wanita']);
$nama_pria_lengkap = htmlspecialchars($_POST['nama_pria_lengkap']);
$nama_wanita_lengkap = htmlspecialchars($_POST['nama_wanita_lengkap']);
$ortu_pria_ayah = htmlspecialchars($_POST['ortu_pria_ayah']);
$ortu_pria_ibu = htmlspecialchars($_POST['ortu_pria_ibu']);
$ortu_wanita_ayah = htmlspecialchars($_POST['ortu_wanita_ayah']);
$ortu_wanita_ibu = htmlspecialchars($_POST['ortu_wanita_ibu']);

$q_mempelai = "UPDATE bride_groom SET 
             nama_pria='$nama_pria', nama_wanita='$nama_wanita',
             nama_pria_lengkap='$nama_pria_lengkap', nama_wanita_lengkap='$nama_wanita_lengkap',
             ortu_pria_ayah='$ortu_pria_ayah', ortu_pria_ibu='$ortu_pria_ibu',
             ortu_wanita_ayah='$ortu_wanita_ayah', ortu_wanita_ibu='$ortu_wanita_ibu'
             WHERE id_inv='$id_inv'";
execute($q_mempelai);

$tgl_resepsi = htmlspecialchars($_POST['tgl_resepsi']);
$jam_acara   = htmlspecialchars($_POST['jam_acara']);
$lokasi_nama = htmlspecialchars($_POST['lokasi_nama']);
$lokasi_alamat = htmlspecialchars($_POST['lokasi_alamat']);
$link_maps   = htmlspecialchars($_POST['link_maps']);

$q_acara = "UPDATE invitations SET 
            tgl_resepsi='$tgl_resepsi', jam_acara='$jam_acara',
            lokasi_nama='$lokasi_nama', lokasi_alamat='$lokasi_alamat', lokasi_map='$link_maps'
            WHERE id_inv='$id_inv'";
execute($q_acara);

// 2. UPLOAD FOTO SINGLE (Cover, Pria, Wanita)
function uploadAjax($inputName, $id_inv, $koneksi) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === 0) {
        $name = $_FILES[$inputName]['name'];
        $tmp = $_FILES[$inputName]['tmp_name'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $valid = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($ext, $valid)) {
            $newName = uniqid() . ".$ext";
            if (!is_dir("../../assets/images/uploads")) mkdir("../../assets/images/uploads", 0777, true);
            move_uploaded_file($tmp, "../../assets/images/uploads/" . $newName);
            
            // Update DB
            execute("UPDATE invitations SET $inputName = '$newName' WHERE id_inv='$id_inv'");
        }
    }
}

uploadAjax('foto_pria', $id_inv, $koneksi);
uploadAjax('foto_wanita', $id_inv, $koneksi);
uploadAjax('foto_cover', $id_inv, $koneksi);

// 3. UPLOAD MULTIPLE GALERI
// 3. UPLOAD MULTIPLE GALERI
if (isset($_FILES['galeri_files'])) {
    $total = count($_FILES['galeri_files']['name']);
    
    // Looping semua file yang dipilih
    for ($i = 0; $i < $total; $i++) {
        $tmp = $_FILES['galeri_files']['tmp_name'][$i];
        
        if ($tmp != "") {
            $name = $_FILES['galeri_files']['name'][$i];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            // Nama file unik
            $newName = uniqid() . "_galeri." . $ext;
            
            // Pindahkan file
            if (move_uploaded_file($tmp, "../../assets/images/uploads/" . $newName)) {
                // INSERT KE DATABASE (Sesuai Tabel Kamu)
                // Kolom: id_inv, file_foto
                execute("INSERT INTO invitation_gallery (id_inv, file_foto) VALUES ('$id_inv', '$newName')");
            }
        }
    }
}

echo json_encode(['status' => 'success']);
?>