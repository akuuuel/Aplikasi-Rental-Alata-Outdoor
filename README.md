# 🌲 OutdoorRent Management System

**Sistem Manajemen Persewaan Alat Outdoor Premium**

Sistem ini dirancang untuk mempermudah operasional bisnis rental peralatan outdoor dengan antarmuka modern (Super-App Style) yang responsif, transparan, dan aman.

---

## 🚀 Fitur Utama

1. **Dashboard Real-time**: Statistik cepat mengenai stok alat ready, jumlah pelanggan, sewa aktif, dan total pendapatan.
2. **Sistem Keranjang "Live"**: Menambahkan banyak alat ke dalam satu transaksi secara instan tanpa reload halaman.
3. **Validasi Stok Ketat**:
   - **Frontend**: Mencegah penambahan alat jika jumlah melebihi stok yang ada.
   - **Backend (Database Lock)**: Menggunakan sistem kunci database saat simpan untuk memastikan stok tidak akan pernah menjadi negatif ("mines") meskipun diakses bersamaan.
4. **Kalkulasi Biaya Transparan**: Perhitungan otomatis berdasarkan **Harga Sewa x Jumlah Alat x Durasi Hari**.
5. **Manajemen Pengembalian Otomatis**:
   - Menghitung denda keterlambatan secara otomatis.
   - Mengembalikan stok barang ke gudang secara otomatis saat barang dikembalikan.
6. **Cetak Struk Thermal**: Invoice profesional yang siap dicetak untuk pelanggan.
7. **Anti-Duplicate**: Proteksi tombol "Gas Sewa" untuk mencegah transaksi ganda jika tombol ditekan berulang kali.
8. **Notifikasi Premium**: Menggunakan **SweetAlert2** untuk pengalaman pengguna yang lebih interaktif.

---

## 📂 Skema Database

Sistem ini menggunakan 6 tabel utama:

1. **`users`**: Menyimpan data akun admin/staff.
2. **`barang`**: Master data peralatan (Nama, Harga, Stok, Status).
3. **`pelanggan`**: Master data penyewa (Nama, No Telp, Alamat, KTP).
4. **`transaksi`**: Data induk penyewaan (Header).
5. **`detail_transaksi`**: Rincian alat yang disewa dalam satu transaksi (Itemized).
6. **`pengembalian`**: Catatan pengembalian alat dan denda.

---

## 🔐 Akun Akses Default

| Administrator | `admin` | `password` |

---

## 🛠 Panduan Penggunaan

### 1. Membuat Pesanan Baru

- Masuk ke menu **Buat Pesanan**.
- **Step 1**: Pilih alat dan masukkan jumlah (Qty), lalu klik **Tambah ke Keranjang**.
- **Step 2**: Pilih Nama Pelanggan dan tentukan Tanggal Pinjam & Tanggal Kembali.
- **Step 3**: Periksa rincian di Keranjang. Jika sudah benar, klik **GAS SEWA**.
- Sistem akan otomatis mencetak struk dan memotong stok barang.

### 2. Mengembalikan Barang

- Masuk ke menu **Riwayat Pesanan**.
- Cari transaksi yang dimaksud, lalu klik baris transaksi tersebut.
- Akan muncul modal rincian, klik tombol hijau **KEMBALIKAN BARANG**.
- Sistem akan menghitung denda jika terlambat. Klik **Konfirmasi** untuk menyelesaikan.
- Stok akan otomatis bertambah kembali ke gudang.

### 3. Manajemen Barang

- Masuk ke menu **Alat Outdoor**.
- Anda bisa menambah, mengedit, atau menghapus alat. Pastikan stok selalu diupdate jika ada pengadaan barang baru.

---

## 💻 Spesifikasi Teknis

- **Bahasa**: PHP Native (Fast & Lightweight)
- **Database**: MySQL (MariaDB)
- **CSS Framework**: Bootstrap 5 + Custom Premium CSS
- **Icons**: Bootstrap Icons
- **Alerts**: SweetAlert2
- **Font**: Plus Jakarta Sans (Premium Sans Typography)

---

## ⚖️ Lisensi & Hak Cipta

Aplikasi ini dibuat oleh Imran, Mahasiswa semester 5 Institute Teknologi Dan Bisnis Nobel Indonesia, Program Studi Sistem Dan teknologi Informasi.
dilarang memperjual belikan aplikasi ini tanpa izin pembuat.

---

**Developed with ❤️ for Outdoor Business Excellence.**
