<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');
require_once '../../config/db.php'; 

if (!isset($koneksi) || !isset($_POST['id_inv'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid Request']);
    exit;
}

$id_inv = mysqli_real_escape_string($koneksi, $_POST['id_inv']);

function input($koneksi, $post_name) {
    return isset($_POST[$post_name]) ? mysqli_real_escape_string($koneksi, $_POST[$post_name]) : '';
}

// 1. UPDATE DATA TEKS (Mempelai, Acara, IG, Video, Live, Love Story, CLOSING)
if (isset($_POST['nama_pria'])) {
    
    // Data Mempelai
    $nama_pria = input($koneksi, 'nama_pria');
    $ig_pria   = input($koneksi, 'ig_pria');
    
    $q_mempelai = "UPDATE bride_groom SET 
                 nama_pria='$nama_pria', nama_wanita='".input($koneksi,'nama_wanita')."',
                 nama_pria_lengkap='".input($koneksi,'nama_pria_lengkap')."', 
                 nama_wanita_lengkap='".input($koneksi,'nama_wanita_lengkap')."',
                 ortu_pria_ayah='".input($koneksi,'ortu_pria_ayah')."', 
                 ortu_pria_ibu='".input($koneksi,'ortu_pria_ibu')."',
                 ortu_wanita_ayah='".input($koneksi,'ortu_wanita_ayah')."', 
                 ortu_wanita_ibu='".input($koneksi,'ortu_wanita_ibu')."',
                 ig_pria='$ig_pria', ig_wanita='".input($koneksi,'ig_wanita')."'
                 WHERE id_inv='$id_inv'";
    mysqli_query($koneksi, $q_mempelai);

    // Data Fitur Tambahan & CLOSING SECTION
    $video_url = input($koneksi, 'video_url');
    $link_live = input($koneksi, 'link_live');
    
    // Love Story
    $story_meet_date   = input($koneksi, 'story_meet_date');
    $story_meet_text   = input($koneksi, 'story_meet_text');
    $story_engage_date = input($koneksi, 'story_engage_date');
    $story_engage_text = input($koneksi, 'story_engage_text');
    $story_marry_date  = input($koneksi, 'story_marry_date');
    $story_marry_text  = input($koneksi, 'story_marry_text');

    // Closing
    $closing_title = input($koneksi, 'closing_title');
    $closing_text  = input($koneksi, 'closing_text');
    $closing_names = input($koneksi, 'closing_names');

    $q_inv = "UPDATE invitations SET 
                tgl_resepsi='".input($koneksi,'tgl_resepsi')."', 
                jam_acara='".input($koneksi,'jam_acara')."',
                lokasi_nama='".input($koneksi,'lokasi_nama')."', 
                lokasi_alamat='".input($koneksi,'lokasi_alamat')."', 
                lokasi_map='".input($koneksi,'link_maps')."',
                video_url='$video_url', link_live='$link_live',
                story_meet_date='$story_meet_date', story_meet_text='$story_meet_text',
                story_engage_date='$story_engage_date', story_engage_text='$story_engage_text',
                story_marry_date='$story_marry_date', story_marry_text='$story_marry_text',
                closing_title='$closing_title', closing_text='$closing_text', closing_names='$closing_names'
                WHERE id_inv='$id_inv'";
    
    mysqli_query($koneksi, $q_inv);
}

// 2. MANAJEMEN EVENT (Tetap Sama)
if (isset($_POST['action_event'])) {
    if ($_POST['action_event'] == 'add') {
        $nama   = input($koneksi, 'nama_acara_new');
        $tgl    = input($koneksi, 'tgl_acara_new');
        $jam    = input($koneksi, 'jam_acara_new');
        $lokasi = input($koneksi, 'lokasi_nama_new');
        $alamat = input($koneksi, 'lokasi_alamat_new');
        $maps   = input($koneksi, 'link_maps_new');
        if(!empty($nama) && !empty($tgl)) {
            $q = "INSERT INTO invitation_events (id_inv, nama_acara, tgl_acara, jam_acara, lokasi_nama, lokasi_alamat, link_maps) VALUES ('$id_inv', '$nama', '$tgl', '$jam', '$lokasi', '$alamat', '$maps')";
            mysqli_query($koneksi, $q);
        }
    }
    if ($_POST['action_event'] == 'delete') {
        $id_evt = input($koneksi, 'id_event');
        mysqli_query($koneksi, "DELETE FROM invitation_events WHERE id_event = '$id_evt'");
    }
    // Return HTML
    $html = '';
    $q_events = mysqli_query($koneksi, "SELECT * FROM invitation_events WHERE id_inv = '$id_inv' ORDER BY tgl_acara ASC, urutan ASC");
    if(mysqli_num_rows($q_events) > 0) {
        while($evt = mysqli_fetch_assoc($q_events)) {
            $html .= '<div class="list-group-item bg-white border mb-2 rounded"><div class="d-flex justify-content-between align-items-start"><div><h6 class="fw-bold mb-1 text-primary">'.$evt['nama_acara'].'</h6><p class="mb-1 small"><i class="feather-calendar"></i> '.date('d M Y', strtotime($evt['tgl_acara'])).' | '.$evt['jam_acara'].'</p><p class="mb-0 small text-muted">'.$evt['lokasi_nama'].'</p></div><button type="button" class="btn btn-sm btn-outline-danger btn-hapus-event" data-id="'.$evt['id_event'].'"><i class="feather-trash"></i></button></div></div>';
        }
    } else { $html = '<div class="text-center py-3 text-muted border border-dashed rounded">Belum ada acara tambahan.</div>'; }
    echo json_encode(['status' => 'success', 'html' => $html]); exit;
}

// 3. UPLOAD FOTO (Termasuk Closing Img)
function uploadAjax($inputName, $id_inv, $koneksi) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === 0) {
        $name = $_FILES[$inputName]['name'];
        $tmp = $_FILES[$inputName]['tmp_name'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $valid = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $valid)) {
            $newName = uniqid() . ".$ext";
            if (!is_dir("../../assets/images/uploads")) mkdir("../../assets/images/uploads", 0777, true);
            if (move_uploaded_file($tmp, "../../assets/images/uploads/" . $newName)) {
                $q = "UPDATE invitations SET $inputName = '$newName' WHERE id_inv='$id_inv'";
                mysqli_query($koneksi, $q);
            }
        }
    }
}
uploadAjax('foto_pria', $id_inv, $koneksi);
uploadAjax('foto_wanita', $id_inv, $koneksi);
uploadAjax('foto_cover', $id_inv, $koneksi);
uploadAjax('closing_img', $id_inv, $koneksi); // NEW

// 4. UPLOAD GALERI & SORT (Kode sama seperti sebelumnya)
if (isset($_FILES['galeri_files'])) { /* ... kode sama ... */ }
if (isset($_POST['sort_music'])) { /* ... kode sama ... */ }

echo json_encode(['status' => 'success']);
?>