<?php
// php-rlsm/db_setup.php

$host = 'localhost';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS db_lokasi");
    echo "Database 'db_lokasi' berhasil dibuat atau sudah ada.<br>";
    
    $pdo->exec("USE db_lokasi");
    
    // Sesuaikan kolom dengan 6 kriteria utama di jurnal
    $createTableSql = "
    CREATE TABLE IF NOT EXISTS lokasi (
        id_lokasi INT AUTO_INCREMENT PRIMARY KEY,
        nama_daerah VARCHAR(255) NOT NULL,
        populasi DOUBLE NOT NULL,          -- Ribu Jiwa (Benefit)
        pendapatan DOUBLE NOT NULL,        -- Juta/Tahun (Benefit)
        aksesibilitas DOUBLE NOT NULL,     -- Skor 1-10 (Benefit)
        jarak_pesaing DOUBLE NOT NULL,     -- Km (Benefit)
        sewa_tanah DOUBLE NOT NULL,        -- Juta/Tahun (Cost)
        lalu_lintas DOUBLE NOT NULL        -- Kendaraan/Jam (Benefit)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($createTableSql);
    echo "Tabel 'lokasi' berhasil disesuaikan dengan kriteria jurnal.<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM lokasi");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        echo "Mengimpor 5 sampel data awal berdasarkan Tabel 2 Jurnal...<br>";
        
        // Data diambil dari cuplikan Tabel 2 pada Jurnal
        $insertQuery = "
        INSERT INTO lokasi (nama_daerah, populasi, pendapatan, aksesibilitas, jarak_pesaing, sewa_tanah, lalu_lintas)
        VALUES 
        ('Alternatif Lokasi 1', 122, 81, 9, 6.5,  321, 273),
        ('Alternatif Lokasi 2', 112, 33, 3, 1.73, 114, 691),
        ('Alternatif Lokasi 3', 34,  52, 3, 1.05, 294, 452),
        ('Alternatif Lokasi 4', 126, 44, 3, 6.65, 416, 429),
        ('Alternatif Lokasi 5', 91,  72, 4, 2.75, 169, 718)
        ";
        
        $pdo->exec($insertQuery);
        echo "Data awal berhasil diimpor.<br>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}