<?php
/**
 * Data Migration Script
 * Migrasi data dari database lama ke database baru
 * - Skip data yang sudah ada (berdasarkan primary key)
 * - Hanya insert kolom yang ada di kedua database
 */

// =============================================
// KONFIGURASI - SESUAIKAN DENGAN DATABASE KAMU
// =============================================
$config = [
    'host' => 'db',
    'username' => 'root',
    'password' => 'root',
    'db_lama' => 'it_helpdesk',
    'db_baru' => 'it_helpdesk_new',
];

// Tabel yang mau di-skip (opsional)
$skipTables = [
    // 'migrations',
    // 'sessions',
];

// =============================================
// JANGAN UBAH KODE DI BAWAH INI
// =============================================

set_time_limit(0);
ini_set('memory_limit', '512M');

echo "===========================================\n";
echo "    DATA MIGRATION SCRIPT\n";
echo "===========================================\n\n";

try {
    // Koneksi database
    $pdo = new PDO(
        "mysql:host={$config['host']};charset=utf8mb4",
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "[OK] Koneksi database berhasil\n\n";

    // Ambil list tabel dari database lama
    $tablesLama = getTables($pdo, $config['db_lama']);
    $tablesBaru = getTables($pdo, $config['db_baru']);

    echo "Database lama ({$config['db_lama']}): " . count($tablesLama) . " tabel\n";
    echo "Database baru ({$config['db_baru']}): " . count($tablesBaru) . " tabel\n\n";

    $totalInserted = 0;
    $totalSkipped = 0;
    $totalErrors = 0;

    foreach ($tablesLama as $table) {
        // Skip tabel tertentu
        if (in_array($table, $skipTables)) {
            echo "[SKIP] $table (ada di skip list)\n";
            continue;
        }

        // Cek apakah tabel ada di database baru
        if (!in_array($table, $tablesBaru)) {
            echo "[WARN] $table tidak ada di database baru, dilewati\n";
            continue;
        }

        echo "\n--- Migrasi tabel: $table ---\n";

        // Ambil struktur kolom kedua database
        $kolomLama = getColumns($pdo, $config['db_lama'], $table);
        $kolomBaru = getColumns($pdo, $config['db_baru'], $table);

        // Cari kolom yang ada di kedua database
        $kolomSama = array_intersect($kolomLama, $kolomBaru);

        if (empty($kolomSama)) {
            echo "[WARN] Tidak ada kolom yang sama, dilewati\n";
            continue;
        }

        // Cek kolom yang berbeda
        $kolomHanyaDiLama = array_diff($kolomLama, $kolomBaru);
        $kolomHanyaDiBaru = array_diff($kolomBaru, $kolomLama);

        if (!empty($kolomHanyaDiLama)) {
            echo "[INFO] Kolom hanya di db_lama (tidak dimigrasikan): " . implode(', ', $kolomHanyaDiLama) . "\n";
        }
        if (!empty($kolomHanyaDiBaru)) {
            echo "[INFO] Kolom baru di db_baru (akan pakai default): " . implode(', ', $kolomHanyaDiBaru) . "\n";
        }

        // Ambil primary key
        $primaryKey = getPrimaryKey($pdo, $config['db_lama'], $table);

        // Migrasi data
        $result = migrateTableData($pdo, $config, $table, $kolomSama, $primaryKey);
        
        $totalInserted += $result['inserted'];
        $totalSkipped += $result['skipped'];
        $totalErrors += $result['errors'];

        echo "[DONE] Inserted: {$result['inserted']}, Skipped: {$result['skipped']}, Errors: {$result['errors']}\n";
    }

    echo "\n===========================================\n";
    echo "    MIGRASI SELESAI\n";
    echo "===========================================\n";
    echo "Total Inserted: $totalInserted\n";
    echo "Total Skipped : $totalSkipped\n";
    echo "Total Errors  : $totalErrors\n";

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}

// =============================================
// FUNCTIONS
// =============================================

function getTables($pdo, $database) {
    $stmt = $pdo->query("SHOW TABLES FROM `$database`");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getColumns($pdo, $database, $table) {
    $stmt = $pdo->query("SHOW COLUMNS FROM `$database`.`$table`");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return $columns;
}

function getPrimaryKey($pdo, $database, $table) {
    $stmt = $pdo->query("SHOW KEYS FROM `$database`.`$table` WHERE Key_name = 'PRIMARY'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['Column_name'] : 'id';
}

function migrateTableData($pdo, $config, $table, $columns, $primaryKey) {
    $result = ['inserted' => 0, 'skipped' => 0, 'errors' => 0];
    
    $columnList = implode('`, `', $columns);
    
    // Ambil semua data dari database lama
    $stmt = $pdo->query("SELECT `$columnList` FROM `{$config['db_lama']}`.`$table`");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        echo "[INFO] Tabel kosong\n";
        return $result;
    }

    echo "[INFO] Memproses " . count($rows) . " baris data...\n";

    foreach ($rows as $row) {
        try {
            // Cek apakah data sudah ada di database baru (berdasarkan primary key)
            if (isset($row[$primaryKey])) {
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM `{$config['db_baru']}`.`$table` WHERE `$primaryKey` = ?");
                $checkStmt->execute([$row[$primaryKey]]);
                
                if ($checkStmt->fetchColumn() > 0) {
                    $result['skipped']++;
                    continue;
                }
            }

            // Insert data
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $insertSql = "INSERT INTO `{$config['db_baru']}`.`$table` (`$columnList`) VALUES ($placeholders)";
            
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute(array_values($row));
            
            $result['inserted']++;

        } catch (Exception $e) {
            $result['errors']++;
            // Uncomment baris di bawah untuk debug error per row
             echo "[ERROR] " . $e->getMessage() . "\n";
        }
    }

    return $result;
}
