# Dokumentasi Hak Akses Tiket (User & Staff)

Dokumen ini menjelaskan logika otorisasi yang telah diperbarui untuk memastikan user dapat melihat tiket mereka sendiri tanpa gangguan, sembari menjaga keamanan fitur administratif.

## 1. Identifikasi Pemilik Tiket (Ticket Ownership)

Sistem menganggap seorang user adalah "Owner" (pemilik) sebuah tiket jika salah satu kondisi berikut terpenuhi:
- **`userid`** pada tiket sama dengan **ID** user yang sedang login.
- **[email](file:///Users/macbook/Documents/web/OnTrack/includes/classes/class.ticket.php#495-616)** pada tiket sama dengan **Email** user yang sedang login.

## 2. Mekanisme Owner-Bypass (Terbaru)

Untuk memberikan pengalaman akses yang lancar, sistem sekarang menerapkan **Owner-Bypass** pada rute `tickets/manage` (Tampilan Detail) dan aksi `addTicketReply` (Balas Tiket):

- Jika user adalah **Owner** tiket:
  - Sistem melompati (bypass) pengecekan hak akses admin (`manageTicket`).
  - Sistem melompati (bypass) pengecekan kepemilikan client ([isOwner](file:///Users/macbook/Documents/web/OnTrack/includes/functions.php#628-646)).
  - User dapat langsung melihat detail dan mengirim balasan.
  
- Jika user **Bukan Owner**:
  - Sistem akan mengecek apakah user memiliki hak akses `manageTicket`.
  - Jika ya (biasanya Staff/Admin), sistem kemudian mengecek apakah client tiket tersebut sesuai dengan client user tersebut ([isOwner](file:///Users/macbook/Documents/web/OnTrack/includes/functions.php#628-646)).

## 3. Pembatasan Fitur untuk User Biasa

Meskipun user dapat mengakses halaman `manageTicket`, fitur-fitur administratif tetap dibatasi agar user hanya bisa **melihat dan membalas**.

### Fitur yang Dinonaktifkan (Hanya untuk Admin/Staff):
- **Tampilan UI (manage.html)**:
  - Tombol **Close Ticket** (Tutup Tiket)
  - Tombol **Edit Ticket** (Ubah Tiket)
  - Tombol **Delete Ticket** (Hapus Tiket)
  - Tombol **Assign to me** (Tugaskan ke Saya)
  - Tab **Internal Comments** (Hanya terlihat jika punya izin `viewComments`)
  - Tab **Action** (Hanya terlihat jika punya izin `manageTicketNotes`)

### Keamanan Backend:
Aksi-aksi berikut di backend ([quickactions.php](file:///Users/macbook/Documents/web/OnTrack/includes/controllers/quickactions.php)) telah diproteksi dengan verifikasi ganda ([isAuthorized](file:///Users/macbook/Documents/web/OnTrack/includes/functions.php#617-627) & [isOwner](file:///Users/macbook/Documents/web/OnTrack/includes/functions.php#628-646)):
- `ticketClose`
- `ticketReopen`
- `ticketAssignToMe`

## 4. Contoh Kasus (User: "est")
Pada kasus user "est" dari screenshot sebelumnya:
1. User mengklik tiket di Dashboard.
2. Dashboard mengirim ID tiket ke `route=tickets/manage`.
3. Sistem mendeteksi Email atau UserID "est" cocok dengan tiket tersebut (**Owner-Bypass aktif**).
4. User masuk ke halaman detail tanpa error "Unauthorized", namun hanya melihat form balasan dan histori tiket tanpa tombol-tombol admin lainnya.
