<?php
// php-rlsm/kelola_lokasi.php
require_once __DIR__ . '/db.php';

try {
    $pdo = get_db_connection();
    $stmt = $pdo->query("SELECT * FROM lokasi");
    $data = $stmt->fetchAll();
} catch (Exception $e) {
    $dbError = $e->getMessage();
    $data = [];
}

$totalKandidat = count($data);
$avgSewa = $totalKandidat > 0 ? array_sum(array_column($data, 'sewa_tanah')) / $totalKandidat : 0;
$bestAkses = $totalKandidat > 0 ? max(array_column($data, 'aksesibilitas')) : 0;
$totalPopulasi = $totalKandidat > 0 ? array_sum(array_column($data, 'populasi')) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Lokasi | Optimasi Supermarket</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #070a13;
            --card-bg: rgba(15, 23, 42, 0.65);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --success: #10b981;
            --success-hover: #059669;
            --warning: #f59e0b;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow-lg: 0 10px 30px -10px rgba(0, 0, 0, 0.7);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at 10% 20%, rgba(37, 99, 235, 0.08) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.08) 0%, transparent 40%);
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 60px;
        }

        /* Glass Nav */
        .glass-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(7, 10, 19, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
        }

        .nav-brand {
            font-size: 1.3rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            background: linear-gradient(135deg, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 16px;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-link.active {
            color: #fff;
            background: rgba(37, 99, 235, 0.15);
            border: 1px solid rgba(37, 99, 235, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto 0;
            padding: 0 24px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 16px;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: var(--text-muted);
            margin-top: 4px;
            font-size: 0.95rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--primary), #8b5cf6);
            opacity: 0.7;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 12px 22px;
            border-radius: var(--radius-md);
            font-weight: 600;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background-color: rgba(255, 255, 255, 0.02);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.95rem;
            color: #cbd5e1;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: rgba(255, 255, 255, 0.02);
        }

        .score-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .action-cell {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            color: #fff;
        }

        .btn-edit {
            background-color: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: var(--warning);
        }

        .btn-edit:hover {
            background-color: var(--warning);
            color: #070a13;
            transform: translateY(-2px);
        }

        .btn-delete {
            background-color: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--danger);
        }

        .btn-delete:hover {
            background-color: var(--danger);
            color: #fff;
            transform: translateY(-2px);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(7, 10, 19, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .modal-content {
            background-color: #0c111e;
            margin: 6% auto;
            padding: 35px;
            border: 1px solid var(--border-color);
            width: 90%;
            max-width: 580px;
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            transform: translateY(20px);
            opacity: 0;
            animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes modalIn {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .close {
            color: var(--text-muted);
            float: right;
            font-size: 28px;
            font-weight: 700;
            cursor: pointer;
            line-height: 1;
        }

        .close:hover {
            color: #fff;
        }

        .modal-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: #fff;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px 16px;
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-size: 0.95rem;
            color: #fff;
        }

        input[type="text"]:focus, input[type="number"]:focus {
            outline: none;
            border-color: var(--primary);
            background-color: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 15px rgba(37, 99, 235, 0.25);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            color: white;
            width: 100%;
            padding: 14px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
        }

        .alert-error a {
            color: #fca5a5;
            font-weight: 600;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<nav class="glass-nav">
    <div class="nav-container">
        <a href="index.php" class="nav-brand">🧬 GA SPK</a>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="kelola_lokasi.php" class="nav-link active">Kelola Lokasi</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="header-section">
        <div>
            <h1>Kelola Data Lokasi</h1>
            <p class="subtitle">Kelola daftar kandidat lokasi supermarket beserta parameter penunjang keputusan.</p>
        </div>
        <button class="btn-add" onclick="openAddModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Lokasi
        </button>
    </div>

    <?php if (isset($dbError)): ?>
        <div class="alert-error">
            <strong>Koneksi Database Gagal!</strong><br>
            Detail error: <?= htmlspecialchars($dbError) ?><br><br>
            Silakan jalankan setup database terlebih dahulu dengan membuka <a href="db_setup.php" target="_blank">db_setup.php</a>.
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Kandidat</div>
            <div class="stat-value"><?= $totalKandidat ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Rata-Rata Sewa</div>
            <div class="stat-value">Rp <?= number_format($avgSewa, 1, ',', '.') ?> Jt/Thn</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Aksesibilitas Terbaik</div>
            <div class="stat-value"><?= $bestAkses ?>/10</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Populasi Tercover</div>
            <div class="stat-value"><?= number_format($totalPopulasi, 0, ',', '.') ?> Ribu Jiwa</div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Alternatif</th>
                        <th>Populasi (Ribu)</th>
                        <th>Pendapatan (Jt/Thn)</th>
                        <th>Aksesibilitas</th>
                        <th>Jarak Pesaing (Km)</th>
                        <th>Sewa Tanah (Jt/Thn)</th>
                        <th>Lalu Lintas (Kend/Jam)</th>
                        <th style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($data) > 0): ?>
                        <?php $no = 1; foreach ($data as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($item['nama_daerah']) ?></strong></td>
                                <td><?= number_format($item['populasi'], 0, ',', '.') ?> Ribu</td>
                                <td>Rp <?= number_format($item['pendapatan'], 0, ',', '.') ?> Jt</td>
                                <td>
                                    <?php
                                    $akses = (int)$item['aksesibilitas'];
                                    $bg = 'rgba(239, 68, 68, 0.15)'; $color = '#f87171';
                                    if ($akses >= 7) { $bg = 'rgba(16, 185, 129, 0.15)'; $color = '#34d399'; }
                                    elseif ($akses >= 4) { $bg = 'rgba(245, 158, 11, 0.15)'; $color = '#fbbf24'; }
                                    ?>
                                    <span class="score-badge" style="background-color: <?= $bg ?>; color: <?= $color ?>;">
                                        <?= $akses ?> / 10
                                    </span>
                                </td>
                                <td><?= number_format($item['jarak_pesaing'], 2, ',', '.') ?> Km</td>
                                <td style="color: #f87171; font-weight: 500;">Rp <?= number_format($item['sewa_tanah'], 0, ',', '.') ?> Jt</td>
                                <td><?= number_format($item['lalu_lintas'], 0, ',', '.') ?>/Jam</td>
                                <td>
                                    <div class="action-cell">
                                        <button class="btn-action btn-edit" title="Edit Data" onclick='openEditModal(<?= json_encode($item) ?>)'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                                        </button>
                                        <form action="process.php?action=delete&id=<?= $item['id_lokasi'] ?>" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kandidat lokasi <?= htmlspecialchars($item['nama_daerah']) ?>?');">
                                            <button type="submit" class="btn-action btn-delete" title="Hapus Data">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 50px 24px;">
                                Belum ada kandidat lokasi. Klik 'Tambah Lokasi' untuk memulai.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle" class="modal-title">Tambah Lokasi Baru</h2>
        
        <form id="lokasiForm" method="POST" action="process.php?action=add">
            <div class="form-group">
                <label for="nama_daerah">Nama Alternatif Lokasi:</label>
                <input type="text" id="nama_daerah" name="nama_daerah" required placeholder="Contoh: Alternatif Lokasi 6">
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="populasi">Populasi Penduduk (Ribu):</label>
                    <input type="number" step="any" min="0.1" id="populasi" name="populasi" required placeholder="Contoh: 120">
                </div>
                <div class="form-group">
                    <label for="pendapatan">Pendapatan Masyarakat (Jt/Thn):</label>
                    <input type="number" step="any" min="0.1" id="pendapatan" name="pendapatan" required placeholder="Contoh: 50">
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="aksesibilitas">Aksesibilitas (Skor 1-10):</label>
                    <input type="number" min="1" max="10" id="aksesibilitas" name="aksesibilitas" required placeholder="Contoh: 8">
                </div>
                <div class="form-group">
                    <label for="jarak_pesaing">Jarak Ke Pesaing (Km):</label>
                    <input type="number" step="any" min="0.01" id="jarak_pesaing" name="jarak_pesaing" required placeholder="Contoh: 3.5">
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="sewa_tanah">Sewa Tanah (Juta/Tahun):</label>
                    <input type="number" step="any" min="1" id="sewa_tanah" name="sewa_tanah" required placeholder="Contoh: 250">
                </div>
                <div class="form-group">
                    <label for="lalu_lintas">Lalu Lintas (Kendaraan/Jam):</label>
                    <input type="number" min="1" id="lalu_lintas" name="lalu_lintas" required placeholder="Contoh: 500">
                </div>
            </div>
            
            <button type="submit" class="btn-submit">Simpan Lokasi</button>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById("formModal");
    const form = document.getElementById("lokasiForm");
    const title = document.getElementById("modalTitle");

    function openAddModal() {
        form.action = "process.php?action=add";
        title.innerText = "Tambah Lokasi Baru";
        form.reset();
        modal.style.display = "block";
    }

    function openEditModal(item) {
        form.action = "process.php?action=edit&id=" + item.id_lokasi;
        title.innerText = "Edit Lokasi: " + item.nama_daerah;
        
        document.getElementById("nama_daerah").value = item.nama_daerah;
        document.getElementById("populasi").value = item.populasi;
        document.getElementById("pendapatan").value = item.pendapatan;
        document.getElementById("aksesibilitas").value = item.aksesibilitas;
        document.getElementById("jarak_pesaing").value = item.jarak_pesaing;
        document.getElementById("sewa_tanah").value = item.sewa_tanah;
        document.getElementById("lalu_lintas").value = item.lalu_lintas;
        
        modal.style.display = "block";
    }

    function closeModal() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

</body>
</html>
