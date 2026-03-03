<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
$scriptpath = '/var/www/html';
require('/var/www/html/config.php');
require('/var/www/html/includes/loader.php');

$csv = <<<CSV
1,KRANGGAN,"TDN Kranggan, KSN KRANGGAN, GROSIR KRANGGAN, HO LT 1 KRANGGAN "
2,GDC DEPOK,"KSN DEPOK, GROSIR DEPOK "
3,JATIRAWAMANGUN,"TDN Jati Rawamangun, Kedai Jati Rawamangun, GROSIR - JTR / RAWAMANGUN "
4,DURI KOSAMBI,"TDN Duri Kosambi, KSN KOSAMBI, GROSIR - KSB / DURI KOSAMBI "
5,CIKARANG UTARA,Delta 2 Cikarang 
6,CIKAMPEK,"KSN CIKAMPEK, GROSIR - CKP / CIKAMPEK "
7,SOEPOMO TEBET,"HO (Semua Lantai & Basement), LT 4, TDN Tebet, NUSTRO TEBET, GROSIR SPM / TEBET "
8,SOREANG,KEDAI BANDUNG 
9,KRANJI,"TDN Kranji, GROSIR - KRJ / KRANJI "
10,KEMANG,"TDN Kemang, NUTRO KEMANG, GROSIR - KMG "
11,CEGER PS REBO,TDN Ceger 
12,MATRAMAN,"TDN Matraman, Kedai Matraman, GROSIR - MTM / MATRAMAN "
13,KARAWACI,"TDN Karawaci, Kedai Karawaci, GROSIR - KWC / KARAWACI "
14,PURWOKERTO,"TDN Purwokerto, Kedai Purwokerto, Grosir Purwokerto "
15,SEMARANG,"TDN Semarang, Kedai Semarang, GROSIR - SMG / SEMARANG "
16,BOGOR,"TDN Bogor, GROSIR - BOG / BOGOR "
17,CIBINONG,TDN Cibinong 
18,CILEUNGSI,TDN Cileungsi 
24,KEBAGUSAN,TDN Kebagusan 
26,TANJUNG DUREN,TDN Tanjung Duren 
29,GRAHA 71 BINTARO,TDN Graha 71 Bintaro 
CSV;

$lines = explode("\n", $csv);
$insertLines = [];

foreach ($lines as $line) {
    $data = str_getcsv(trim($line));
    if (count($data) < 3)
        continue;

    $branch_id = trim($data[0]);
    $locationNames = explode(',', $data[2]);

    foreach ($locationNames as $locName) {
        $locName = trim($locName);
        if (empty($locName))
            continue;

        $loc = $database->get("locations", "*", ["name" => $locName]);
        $loc_id = isset($loc['id']) ? $loc['id'] : "NULL";

        $sub = "NULL";
        $locUpper = strtoupper($locName);
        if (strpos($locUpper, 'TDN') !== false)
            $sub = 5;
        elseif (strpos($locUpper, 'KSN') !== false)
            $sub = 1;
        elseif (strpos($locUpper, 'GROSIR') !== false)
            $sub = 4;
        elseif (strpos($locUpper, 'HO') !== false || strpos($locUpper, 'LT ') !== false || strpos($locUpper, 'BASEMENT') !== false)
            $sub = 6;
        elseif (strpos($locUpper, 'NUSTRO') !== false || strpos($locUpper, 'NUTRO') !== false)
            $sub = 2;
        elseif (strpos($locUpper, 'KEDAI') !== false)
            $sub = 6; // Temporary mapped to HO or NULL? No let's map to HO for now.
        elseif (strpos($locUpper, 'DELTA') !== false)
            $sub = 6;

        if ($loc_id != "NULL") {
            $insertLines[] = "($branch_id, $sub, $loc_id)";
        } else {
            echo "-- WARNING: Location Name '{$locName}' not found in DB!\n";
        }
    }
}

echo "INSERT INTO location_mappings (branch_id, submitter_id, location_id) VALUES \n";
echo implode(",\n", $insertLines) . ";\n";
