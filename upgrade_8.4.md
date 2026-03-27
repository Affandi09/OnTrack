# Laporan Kompatibilitas Library (PHP 8.4)

Berdasarkan analisis folder [vendor](file:///Users/macbook/Documents/web/OnTrack/includes/functions.php#384-391) dan `vendor/classes`, berikut adalah rincian library yang akan bermasalah jika Anda langsung pindah ke PHP 8.4 tanpa update:

## 1. mPDF (v7.1.0)
- **Masalah**: Versi ini dibuat tahun 2018. Menggunakan fungsi `each()` yang sudah **dihapus** di PHP 8.0 dan `utf8_encode()` yang didepresiasi.
- **Efek**: **Fatal Error** (aplikasi berhenti total saat cetak PDF).
- **Solusi**: Update ke mPDF 8.2+.
- **Tindakan**: Migrasi dari cara manual ke Composer atau ganti file di folder mpdf dengan versi terbaru.

## 2. Medoo (class.medoo.php)
- **Masalah**: Versi yang ada sangat lama. Menggunakan teknik perbandingan loose dan sintaksis yang tidak kompatibel dengan penanganan error PHP 8 yang lebih ketat.
- **Efek**: Query database mungkin gagal tanpa peringatan yang jelas atau error saat inisialisasi.
- **Solusi**: Update ke Medoo 2.1.10.
- **Tindakan**: Rekomendasi menggantinya dengan library Medoo terbaru via Composer.

## 3. PHPMailer (v6.0.3)
- **Masalah**: PHP 8.1+ tidak memperbolehkan nilai `null` dilewatkan ke fungsi string internal PHP (seperti `strlen` atau `str_replace`). PHPMailer lama banyak melakukan ini.
- **Efek**: **Fatal Error** saat mengirim email jika ada data input yang kosong/null.
- **Solusi**: Update ke PHPMailer 6.9.3.

## 4. HTMLPurifier (v4.9.3)
- **Masalah**: Versi ini tidak memiliki deklarasi property yang eksplisit. Di PHP 8.2+, "Dynamic Properties" didepresiasi.
- **Efek**: Log error akan penuh dengan ribuan baris **Deprecation Warning**, yang bisa membuat server lambat atau disk penuh.
- **Solusi**: Update ke v4.17.0.

## 5. FPDI (v1.6.2 & setasign/fpdi)
- **Masalah**: Versi 1.x tidak mendukung PHP 8 sama sekali karena perubahan internal pada cara PHP menangani resource/object stream.
- **Efek**: Cetak PDF dengan template akan gagal.
- **Solusi**: Update ke FPDI 2.6.

---

## Ringkasan Tindak Lanjut

### Apakah perlu mengganti library?
**TIDAK** perlu mengganti dengan library berbeda (kecuali Anda ingin modernisasi total). Namun, Anda **WAJIB** memperbarui versi library tersebut.

### Strategi Rekomendasi:
1. **Gunakan Composer**: Proyek Anda sudah punya jejak Composer tapi sepertinya tidak dipakai untuk update. Kita harus menjalankan `composer update` dengan file `composer.json` yang sudah disesuaikan agar menarik versi terbaru yang support PHP 8.4.
2. **Hapus Manual Copy**: Library di `vendor/classes` (seperti Medoo) sebaiknya dihapus dan dikelola via Composer agar update di masa depan lebih mudah.
3. **Fix di functions.php**: Setelah library diupdate, kita baru bisa memperbaiki kode buatan sendiri (custom code) di `functions.php`.

Apakah Anda setuju jika kita mulai dengan menyiapkan file `composer.json` yang modern untuk menarik semua update ini sekaligus?
