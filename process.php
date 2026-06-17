<?php
// php-rlsm/process.php
require_once __DIR__ . '/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = get_db_connection();
        
        // 1. PROSES TAMBAH DATA LOKASI BARU
        if ($action === 'add') {
            $nama_daerah   = $_POST['nama_daerah'];
            $populasi      = (float)$_POST['populasi'];
            $pendapatan    = (float)$_POST['pendapatan'];
            $aksesibilitas = (float)$_POST['aksesibilitas'];
            $jarak_pesaing = (float)$_POST['jarak_pesaing'];
            $sewa_tanah    = (float)$_POST['sewa_tanah'];
            $lalu_lintas   = (float)$_POST['lalu_lintas'];
            
            $sql = "INSERT INTO lokasi (nama_daerah, populasi, pendapatan, aksesibilitas, jarak_pesaing, sewa_tanah, lalu_lintas) 
                    VALUES (:nama_daerah, :populasi, :pendapatan, :aksesibilitas, :jarak_pesaing, :sewa_tanah, :lalu_lintas)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama_daerah'   => $nama_daerah,
                ':populasi'      => $populasi,
                ':pendapatan'    => $pendapatan,
                ':aksesibilitas' => $aksesibilitas,
                ':jarak_pesaing' => $jarak_pesaing,
                ':sewa_tanah'    => $sewa_tanah,
                ':lalu_lintas'   => $lalu_lintas
            ]);
        }
        
        // 2. PROSES EDIT / UPDATE DATA LOKASI
        else if ($action === 'edit') {
            $id            = (int)$_GET['id'];
            $nama_daerah   = $_POST['nama_daerah'];
            $populasi      = (float)$_POST['populasi'];
            $pendapatan    = (float)$_POST['pendapatan'];
            $aksesibilitas = (float)$_POST['aksesibilitas'];
            $jarak_pesaing = (float)$_POST['jarak_pesaing'];
            $sewa_tanah    = (float)$_POST['sewa_tanah'];
            $lalu_lintas   = (float)$_POST['lalu_lintas'];
            
            $sql = "UPDATE lokasi SET 
                        nama_daerah = :nama_daerah, 
                        populasi = :populasi, 
                        pendapatan = :pendapatan, 
                        aksesibilitas = :aksesibilitas, 
                        jarak_pesaing = :jarak_pesaing, 
                        sewa_tanah = :sewa_tanah, 
                        lalu_lintas = :lalu_lintas 
                    WHERE id_lokasi = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama_daerah'   => $nama_daerah,
                ':populasi'      => $populasi,
                ':pendapatan'    => $pendapatan,
                ':aksesibilitas' => $aksesibilitas,
                ':jarak_pesaing' => $jarak_pesaing,
                ':sewa_tanah'    => $sewa_tanah,
                ':lalu_lintas'   => $lalu_lintas,
                ':id'            => $id
            ]);
        }
        
        // 3. PROSES HAPUS DATA LOKASI
        else if ($action === 'delete') {
            $id = (int)$_GET['id'];
            
            $sql = "DELETE FROM lokasi WHERE id_lokasi = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
        }
        
    } catch (Exception $e) {
        error_log("Error di process.php (action=$action): " . $e->getMessage());
    }
}

// Alihkan kembali halaman ke menu manajemen lokasi
header("Location: kelola_lokasi.php");
exit;