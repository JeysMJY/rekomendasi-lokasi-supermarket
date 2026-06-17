<?php
// php-rlsm/algorithms.php

require_once __DIR__ . '/db.php';

function load_raw_data() {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->query("SELECT * FROM lokasi");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log($e->getMessage());
        return [];
    }
}

// Langkah Pra-pemrosesan: Min-Max Scaling sesuai Rumus Jurnal
function preprocess_and_normalize($daftarLokasi) {
    if (empty($daftarLokasi)) return [];

    $kriteria = ['populasi', 'pendapatan', 'aksesibilitas', 'jarak_pesaing', 'sewa_tanah', 'lalu_lintas'];
    $mins = [];
    $maxs = [];

    // Cari nilai min dan max untuk setiap kriteria
    foreach ($kriteria as $k) {
        $kolom = array_column($daftarLokasi, $k);
        $mins[$k] = min($kolom);
        $maxs[$k] = max($kolom);
    }

    foreach ($daftarLokasi as &$loc) {
        $normalized = [];
        foreach ($kriteria as $k) {
            $denom = ($maxs[$k] - $mins[$k]) == 0 ? 1 : ($maxs[$k] - $mins[$k]);
            
            if ($k === 'sewa_tanah') {
                // Kriteria Cost: (Xmax - Xij) / (Xmax - Xmin)
                $normalized[$k] = ($maxs[$k] - $loc[$k]) / $denom;
            } else {
                // Kriteria Benefit: (Xij - Xmin) / (Xmax - Xmin)
                $normalized[$k] = ($loc[$k] - $mins[$k]) / $denom;
            }
        }
<<<<<<< HEAD
        $loc['normalized'] = $normalized;
=======
    }
    
    $hasilValue = $dpTable[$n][$maxBudget];
    $w = $maxBudget;
    $lokasiTerpilih = [];
    $totalCost = 0;
    
    for ($i = $n; $i > 0; $i--) {
        if ($hasilValue <= 0) {
            break;
        }
        if ($hasilValue == $dpTable[$i - 1][$w]) {
            continue;
        } else {
            $itemTerpilih = $daftarLokasi[$i - 1];
            $lokasiTerpilih[] = $itemTerpilih;
            $hasilValue -= $itemTerpilih['value'];
            $w -= $itemTerpilih['cost'];
            $totalCost += $itemTerpilih['cost'];
        }
    }
    
    return [
        'metode' => 'Dynamic Programming',
        'lokasiTerpilih' => $lokasiTerpilih,
        'totalCost' => $totalCost,
        'totalValue' => $dpTable[$n][$maxBudget]
    ];
}


function jalankanBnB($daftarLokasi, $maxBudget, $batasJarak)
{
    foreach ($daftarLokasi as &$loc) {
        $loc['rasio'] = $loc['cost'] > 0 ? $loc['value'] / $loc['cost'] : 0;
>>>>>>> 95e2d917f2d85196141b3feb088bea60b7b11f46
    }
    unset($loc);
    return $daftarLokasi;
}

<<<<<<< HEAD
// Perhitungan Nilai Fitness berdasarkan Rumus (1) di Jurnal
function hitungFitness($loc, $weights) {
    $n = $loc['normalized'];
    return ($weights['w1'] * $n['populasi']) +
           ($weights['w2'] * $n['pendapatan']) +
           ($weights['w3'] * $n['aksesibilitas']) +
           ($weights['w4'] * $n['jarak_pesaing']) +
           ($weights['w5'] * $n['sewa_tanah']) +
           ($weights['w6'] * $n['lalu_lintas']);
}

// Implementasi Genetic Algorithm (GA) untuk Pemilihan Lokasi Terbaik
function jalankanGA($daftarLokasi, $maxGenerasi = 100, $ukuranPopulasi = 30) {
    $n_lokasi = count($daftarLokasi);
    if ($n_lokasi == 0) return null;

    // Definisikan bobot seimbang jika tidak diatur (Total = 1)
    $weights = ['w1'=>0.1667, 'w2'=>0.1667, 'w3'=>0.1667, 'w4'=>0.1667, 'w5'=>0.1667, 'w6'=>0.1667];

    // Hitung jumlah bit yang diperlukan untuk merepresentasikan indeks (Chromosomes length)
    // B = ceil(log2(N))
    $chromeLength = (int)ceil(log(max(2, $n_lokasi), 2));

    // Helper: Decode biner array menjadi integer desimal modulo N
    $decodeIndex = function($chromosome) use ($n_lokasi) {
        $decimal = 0;
        foreach ($chromosome as $bit) {
            $decimal = ($decimal << 1) | $bit;
        }
        return $decimal % $n_lokasi;
    };

    // Helper: Hitung fitness untuk individu biner
    $evalIndividu = function($chromosome) use ($decodeIndex, $daftarLokasi, $weights) {
        $index = $decodeIndex($chromosome);
        return [
            'chromosome' => $chromosome,
            'indeks_lokasi' => $index,
            'fitness' => hitungFitness($daftarLokasi[$index], $weights)
        ];
    };

    // 1. Inisialisasi Populasi secara acak
    $populasi = [];
    for ($i = 0; $i < $ukuranPopulasi; $i++) {
        $chrome = [];
        for ($g = 0; $g < $chromeLength; $g++) {
            $chrome[] = rand(0, 1);
=======
    usort($daftarLokasi, function ($a, $b) {
        if ($a['rasio'] == $b['rasio']) {
            return 0;
        }
        return ($a['rasio'] > $b['rasio']) ? -1 : 1;
    });

    $bestValue = 0;
    $bestPath = [];
    $n = count($daftarLokasi);

    // PERBAIKAN: Masukkan parameter $currentPath agar tahu lokasi mana saja yang sudah diambil
    $hitungBatasAtas = function ($index, $currentCost, $currentValue, $currentPath) use ($daftarLokasi, $maxBudget, $batasJarak, $n) {
        if ($currentCost >= $maxBudget) {
            return 0;
        }

        $bound = $currentValue;
        $totalCost = $currentCost;
        $i = $index;

        // Simulasi jalur terpilih untuk pengecekan jarak di dalam bound
        $simulatedPath = $currentPath;

        while ($i < $n && ($totalCost + $daftarLokasi[$i]['cost'] <= $maxBudget)) {
            // PERBAIKAN: Hanya hitung item yang lolos validasi jarak aman
            if (cekJarakAman($daftarLokasi[$i], $simulatedPath, $batasJarak)) {
                $totalCost += $daftarLokasi[$i]['cost'];
                $bound += $daftarLokasi[$i]['value'];
                $simulatedPath[] = $daftarLokasi[$i]; // Tambahkan ke simulasi jika aman
            }
            $i++;
        }

        // Bagian pecahan (fractional) juga hanya dihitung jika aman secara jarak
        if ($i < $n && ($maxBudget - $totalCost) > 0) {
            if (cekJarakAman($daftarLokasi[$i], $simulatedPath, $batasJarak)) {
                $sisaKapasitas = $maxBudget - $totalCost;
                $bound += $sisaKapasitas * $daftarLokasi[$i]['rasio'];
            }
        }

        return $bound;
    };

    $dfs = function ($index, $currentCost, $currentValue, $currentPath) use (
        &$dfs,
        $daftarLokasi,
        $maxBudget,
        $batasJarak,
        $n,
        $hitungBatasAtas,
        &$bestValue,
        &$bestPath
    ) {
        if ($currentValue > $bestValue) {
            $bestValue = $currentValue;
            $bestPath = $currentPath;
>>>>>>> 95e2d917f2d85196141b3feb088bea60b7b11f46
        }
        $populasi[$i] = $evalIndividu($chrome);
    }

<<<<<<< HEAD
    $historyFitness = [];
    $bestGlobal = null;

    // Loop Generasi (Evolusi)
    for ($gen = 1; $gen <= $maxGenerasi; $gen++) {
        // Cari individu terbaik dalam generasi saat ini untuk Elitism
        $bestGen = $populasi[0];
        foreach ($populasi as $ind) {
            if ($ind['fitness'] > $bestGen['fitness']) {
                $bestGen = $ind;
            }
        }

        // Update Best Global
        if ($bestGlobal === null || $bestGen['fitness'] > $bestGlobal['fitness']) {
            $bestGlobal = $bestGen;
        }

        $historyFitness[$gen] = $bestGlobal['fitness'];

        // Siapkan populasi baru
        $populasiBaru = [];

        // Terapkan Elitism: Salin 2 individu terbaik langsung ke generasi berikutnya
        usort($populasi, function($a, $b) {
            return $b['fitness'] <=> $a['fitness'];
        });
        $populasiBaru[] = $populasi[0];
        if ($ukuranPopulasi > 1) {
            $populasiBaru[] = $populasi[1];
        }

        // Generate sisa populasi baru melalui Seleksi, Crossover, dan Mutasi
        while (count($populasiBaru) < $ukuranPopulasi) {
            // A. SELEKSI: Tournament Selection (ukuran = 3)
            $pilihParent = function() use ($populasi, $ukuranPopulasi) {
                $best = $populasi[rand(0, $ukuranPopulasi - 1)];
                for ($k = 0; $k < 2; $k++) {
                    $opponent = $populasi[rand(0, $ukuranPopulasi - 1)];
                    if ($opponent['fitness'] > $best['fitness']) {
                        $best = $opponent;
                    }
                }
                return $best['chromosome'];
            };

            $parent1 = $pilihParent();
            $parent2 = $pilihParent();

            // B. CROSSOVER: Single-Point Crossover (Probabilitas = 0.8)
            $child1 = $parent1;
            $child2 = $parent2;
            if (rand(0, 100) < 80 && $chromeLength > 1) {
                $crossPoint = rand(1, $chromeLength - 1);
                for ($g = $crossPoint; $g < $chromeLength; $g++) {
                    $child1[$g] = $parent2[$g];
                    $child2[$g] = $parent1[$g];
                }
            }

            // C. MUTASI: Bit-Flip Mutation (Probabilitas per bit = 0.1)
            $mutate = function($chrome) use ($chromeLength) {
                for ($g = 0; $g < $chromeLength; $g++) {
                    if (rand(0, 100) < 10) {
                        $chrome[$g] = $chrome[$g] === 1 ? 0 : 1;
                    }
                }
                return $chrome;
            };

            $child1 = $mutate($child1);
            $child2 = $mutate($child2);

            $populasiBaru[] = $evalIndividu($child1);
            if (count($populasiBaru) < $ukuranPopulasi) {
                $populasiBaru[] = $evalIndividu($child2);
            }
        }

        $populasi = $populasiBaru;
=======
        if ($index >= $n) {
            return;
        }

        // PERBAIKAN: Mengirimkan $currentPath ke fungsi hitungBatasAtas
        $batasAtas = $hitungBatasAtas($index, $currentCost, $currentValue, $currentPath);
        if ($batasAtas <= $bestValue) {
            return; // Pruning dilakukan dengan akurat sekarang
        }

        $item = $daftarLokasi[$index];

        // CABANG KEPUTUSAN 1: PILIH LOKASI INI
        if ($currentCost + $item['cost'] <= $maxBudget) {
            if (cekJarakAman($item, $currentPath, $batasJarak)) {
                $jalurBaru = $currentPath;
                $jalurBaru[] = $item;
                $dfs(
                    $index + 1,
                    $currentCost + $item['cost'],
                    $currentValue + $item['value'],
                    $jalurBaru
                );
            }
        }

        // CABANG KEPUTUSAN 2: LEWATKAN LOKASI INI
        $dfs($index + 1, $currentCost, $currentValue, $currentPath);
    };

    $dfs(0, 0, 0, []);

    $totalCost = 0;
    foreach ($bestPath as $loc) {
        $totalCost += $loc['cost'];
>>>>>>> 95e2d917f2d85196141b3feb088bea60b7b11f46
    }

    return [
        'lokasiTerbaik' => $daftarLokasi[$bestGlobal['indeks_lokasi']],
        'fitnessTerbaik' => $bestGlobal['fitness'],
        'history' => $historyFitness
    ];
}