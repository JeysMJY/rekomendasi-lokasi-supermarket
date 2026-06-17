<?php
// php-rlsm/db.php

function get_db_connection() {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'db_lokasi';
    
    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Mengembalikan baris data sebagai array asosiatif (nama_kolom => nilai)
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Menonaktifkan emulasi agar tipe data dari MySQL (seperti float/int) 
            // tidak otomatis diubah menjadi string saat dibaca di PHP.
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        return new PDO($dsn, $user, $password, $options);
    } catch (\PDOException $e) {
        // Log pesan error ke sistem internal demi keamanan
        error_log("Koneksi database gagal: " . $e->getMessage());
        throw new \PDOException("Gagal terhubung ke database lokasi. Pastikan MySQL aktif dan db_setup.php sudah dijalankan.", (int)$e->getCode());
    }
}