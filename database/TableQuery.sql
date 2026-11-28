-- Tabel Admin
CREATE TABLE admin (
    id_admin SERIAL PRIMARY KEY,
    nama_admin VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL
);

-- Tabel Petugas
CREATE TABLE petugas (
    id_petugas SERIAL PRIMARY KEY,
    id_desa INTEGER NOT NULL,
    nama_petugas VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    status_aktif BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_petugas_desa FOREIGN KEY (id_desa) REFERENCES desa_wilayah(id_desa) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Masyarakat
CREATE TABLE masyarakat (
    id_masyarakat SERIAL PRIMARY KEY,
    id_desa INTEGER NULL,
    nik VARCHAR(255) UNIQUE NOT NULL,
    nama_masyarakat VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NULL,
    username VARCHAR(50) UNIQUE NULL,
    password_hash VARCHAR(255) NULL,
    CONSTRAINT fk_masyarakat_desa FOREIGN KEY (id_desa) REFERENCES desa_wilayah(id_desa) ON UPDATE CASCADE ON DELETE SET NULL
);

-- Tabel Provinsi
CREATE TABLE provinsi (
    id_provinsi SERIAL PRIMARY KEY,
    nama_provinsi VARCHAR(100) NOT NULL
);

-- Tabel Kabupaten_Kota
CREATE TABLE kabupaten_kota (
    id_kabupaten_kota SERIAL PRIMARY KEY,
    id_provinsi INTEGER NOT NULL,
    nama_kabupaten_kota VARCHAR(100) NOT NULL,
    jenis_wilayah VARCHAR(20) NOT NULL CHECK (jenis_wilayah IN ('Kabupaten', 'Kota')),
    CONSTRAINT fk_kabkota_provinsi FOREIGN KEY (id_provinsi) REFERENCES provinsi(id_provinsi) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Kecamatan
CREATE TABLE kecamatan (
    id_kecamatan SERIAL PRIMARY KEY,
    id_kabupaten_kota INTEGER NOT NULL,
    nama_kecamatan VARCHAR(100) NOT NULL,
    CONSTRAINT fk_kecamatan_kabkota FOREIGN KEY (id_kabupaten_kota) REFERENCES kabupaten_kota(id_kabupaten_kota) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Desa_Wilayah
CREATE TABLE desa_wilayah (
    id_desa SERIAL PRIMARY KEY,
    id_kecamatan INTEGER NOT NULL,
    nama_desa VARCHAR(100) NOT NULL,
    CONSTRAINT fk_desa_kecamatan FOREIGN KEY (id_kecamatan) REFERENCES kecamatan(id_kecamatan) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Sekolah (Untuk Kasus Keracunan MBG)
CREATE TABLE sekolah (
    id_sekolah SERIAL PRIMARY KEY,
    id_desa INTEGER NOT NULL,
    nama_sekolah VARCHAR(150) NOT NULL,
    alamat_sekolah VARCHAR(255) NOT NULL,
    CONSTRAINT fk_sekolah_desa FOREIGN KEY (id_desa) REFERENCES desa_wilayah(id_desa) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Kategori Bencana
CREATE TABLE IF NOT EXISTS kategori_bencana (
    id_kategori SERIAL PRIMARY KEY,
    nama_kategori VARCHAR(100) UNIQUE NOT NULL,
    deskripsi TEXT NULL
);

-- Tabel Jenis Bencana
CREATE TABLE IF NOT EXISTS jenis_bencana (
    id_jenis_bencana SERIAL PRIMARY KEY,
    id_kategori INTEGER NOT NULL,
    nama_bencana VARCHAR(100) UNIQUE NOT NULL,
    jika_mbg BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT fk_jenis_kategori FOREIGN KEY (id_kategori) 
        REFERENCES kategori_bencana(id_kategori) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Laporan Utama
CREATE TABLE IF NOT EXISTS laporan (
    id_laporan SERIAL PRIMARY KEY,
    kode_unik VARCHAR(30) UNIQUE NOT NULL,
    id_masyarakat INTEGER NOT NULL,
    id_jenis_bencana INTEGER NOT NULL,
    id_desa INTEGER NOT NULL,
    id_sekolah INTEGER NULL,
    judul_laporan VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    tanggal_kejadian DATE NOT NULL,
    waktu_kejadian TIME NOT NULL,
    tanggal_laporan DATE NOT NULL DEFAULT CURRENT_DATE,
    waktu_laporan TIME NOT NULL DEFAULT CURRENT_TIME,
    alamat_lokasi VARCHAR(255) NOT NULL,
    status_verifikasi VARCHAR(30) NOT NULL DEFAULT 'Menunggu Verifikasi' 
        CHECK (status_verifikasi IN ('Menunggu Verifikasi', 'Disetujui', 'Ditolak')),
    status_penanganan VARCHAR(30) NOT NULL DEFAULT 'Belum Ditangani' 
        CHECK (status_penanganan IN ('Belum Ditangani', 'Sedang Ditangani', 'Selesai')),
    tingkat_keparahan VARCHAR(20) NULL 
        CHECK (tingkat_keparahan IN ('Rendah', 'Sedang', 'Tinggi')),
    CONSTRAINT fk_laporan_masyarakat FOREIGN KEY (id_masyarakat) 
        REFERENCES masyarakat(id_masyarakat) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_laporan_jenis FOREIGN KEY (id_jenis_bencana) 
        REFERENCES jenis_bencana(id_jenis_bencana) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_laporan_desa FOREIGN KEY (id_desa) 
        REFERENCES desa_wilayah(id_desa) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_laporan_sekolah FOREIGN KEY (id_sekolah) 
        REFERENCES sekolah(id_sekolah) ON UPDATE CASCADE ON DELETE SET NULL
);

-- Tabel Foto Laporan (Mendukung banyak foto bukti dalam satu laporan)
CREATE TABLE IF NOT EXISTS foto_laporan (
    id_foto SERIAL PRIMARY KEY,
    id_laporan INTEGER NOT NULL,
    url_foto VARCHAR(500) NOT NULL,
    keterangan_foto VARCHAR(150) NULL,
    tanggal_upload DATE NOT NULL DEFAULT CURRENT_DATE,
    waktu_upload TIME NOT NULL DEFAULT CURRENT_TIME,
    CONSTRAINT fk_foto_laporan FOREIGN KEY (id_laporan) 
        REFERENCES laporan(id_laporan) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Tabel Update Laporan (Informasi/foto lanjutan dari masyarakat saat kasus aktif)
CREATE TABLE IF NOT EXISTS update_laporan (
    id_update SERIAL PRIMARY KEY,
    id_laporan INTEGER NOT NULL,
    id_masyarakat INTEGER NOT NULL,
    deskripsi_update TEXT NOT NULL,
    foto_update VARCHAR(255) NULL,
    tanggal_update DATE NOT NULL DEFAULT CURRENT_DATE,
    waktu_update TIME NOT NULL DEFAULT CURRENT_TIME,
    CONSTRAINT fk_update_laporan FOREIGN KEY (id_laporan) 
        REFERENCES laporan(id_laporan) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_update_masyarakat FOREIGN KEY (id_masyarakat) 
        REFERENCES masyarakat(id_masyarakat) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Verifikasi Laporan (Oleh Petugas)
CREATE TABLE IF NOT EXISTS verifikasi_laporan (
    id_verifikasi SERIAL PRIMARY KEY,
    id_laporan INTEGER UNIQUE NOT NULL,
    id_petugas INTEGER NOT NULL,
    keputusan VARCHAR(20) NOT NULL CHECK (keputusan IN ('Disetujui', 'Ditolak')),
    alasan_penolakan TEXT NULL,
    tanggal_verifikasi DATE NOT NULL DEFAULT CURRENT_DATE,
    waktu_verifikasi TIME NOT NULL DEFAULT CURRENT_TIME,
    CONSTRAINT fk_verifikasi_laporan FOREIGN KEY (id_laporan) 
        REFERENCES laporan(id_laporan) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_verifikasi_petugas FOREIGN KEY (id_petugas) 
        REFERENCES petugas(id_petugas) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Penanganan Bencana
CREATE TABLE IF NOT EXISTS penanganan (
    id_penanganan SERIAL PRIMARY KEY,
    id_laporan INTEGER NOT NULL,
    id_petugas INTEGER NOT NULL,
    tindakan_penanganan TEXT NOT NULL,
    tanggal_mulai DATE NOT NULL DEFAULT CURRENT_DATE,
    waktu_mulai TIME NOT NULL DEFAULT CURRENT_TIME,
    tanggal_selesai DATE NULL,
    waktu_selesai TIME NULL,
    status_penanganan VARCHAR(30) NOT NULL DEFAULT 'Sedang Ditangani' 
        CHECK (status_penanganan IN ('Belum Ditangani', 'Sedang Ditangani', 'Selesai')),
    keterangan TEXT NULL,
    CONSTRAINT fk_penanganan_laporan FOREIGN KEY (id_laporan) 
        REFERENCES laporan(id_laporan) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_penanganan_petugas FOREIGN KEY (id_petugas) 
        REFERENCES petugas(id_petugas) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Koordinasi Eksternal (PMI, Relawan, Puskesmas, Damkar, dll.)
CREATE TABLE IF NOT EXISTS koordinasi_eksternal (
    id_koordinasi SERIAL PRIMARY KEY,
    id_penanganan INTEGER NOT NULL,
    nama_instansi VARCHAR(150) NOT NULL,
    peran_bantuan TEXT NOT NULL,
    tanggal_koordinasi DATE NOT NULL DEFAULT CURRENT_DATE,
    CONSTRAINT fk_koordinasi_penanganan FOREIGN KEY (id_penanganan) 
        REFERENCES penanganan(id_penanganan) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Tabel Riwayat Status Laporan (Log Audit Perubahan Status)
CREATE TABLE IF NOT EXISTS riwayat_status_laporan (
    id_riwayat SERIAL PRIMARY KEY,
    id_laporan INTEGER NOT NULL,
    id_masyarakat INTEGER NULL,
    id_petugas INTEGER NULL,
    status_lama VARCHAR(50) NULL,
    status_baru VARCHAR(50) NOT NULL,
    keterangan TEXT NULL,
    tanggal_perubahan DATE NOT NULL DEFAULT CURRENT_DATE,
    waktu_perubahan TIME NOT NULL DEFAULT CURRENT_TIME,
    CONSTRAINT fk_riwayat_laporan FOREIGN KEY (id_laporan) 
        REFERENCES laporan(id_laporan) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_riwayat_masyarakat FOREIGN KEY (id_masyarakat) 
        REFERENCES masyarakat(id_masyarakat) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_riwayat_petugas FOREIGN KEY (id_petugas) 
        REFERENCES petugas(id_petugas) ON UPDATE CASCADE ON DELETE SET NULL
);

-- Tabel Pengumuman Wilayah / Laporan
CREATE TABLE IF NOT EXISTS pengumuman_wilayah (
    id_pengumuman SERIAL PRIMARY KEY,
    id_desa_wilayah INTEGER NOT NULL,
    id_laporan INTEGER NULL,
    id_petugas INTEGER NOT NULL,
    judul_pengumuman VARCHAR(150) NOT NULL,
    isi_pengumuman TEXT NOT NULL,
    tanggal_terbit DATE NOT NULL DEFAULT CURRENT_DATE,
    waktu_terbit TIME NOT NULL DEFAULT CURRENT_TIME,
    status_aktif BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_pengumuman_desa FOREIGN KEY (id_desa_wilayah) 
        REFERENCES desa_wilayah(id_desa) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_pengumuman_laporan FOREIGN KEY (id_laporan) 
        REFERENCES laporan(id_laporan) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_pengumuman_petugas FOREIGN KEY (id_petugas) 
        REFERENCES petugas(id_petugas) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Notifikasi (Untuk Masyarakat maupun Petugas)
CREATE TABLE IF NOT EXISTS notifikasi (
    id_notifikasi SERIAL PRIMARY KEY,
    id_laporan INTEGER NULL,
    id_masyarakat INTEGER NULL,
    id_petugas INTEGER NULL,
    judul_notifikasi VARCHAR(150) NOT NULL,
    isi_notifikasi TEXT NOT NULL,
    jenis_notifikasi VARCHAR(50) NOT NULL,
    status_dibaca BOOLEAN NOT NULL DEFAULT FALSE,
    tanggal_notifikasi DATE NOT NULL DEFAULT CURRENT_DATE,
    waktu_kirim TIME NOT NULL DEFAULT CURRENT_TIME,
    CONSTRAINT fk_notif_laporan FOREIGN KEY (id_laporan) 
        REFERENCES laporan(id_laporan) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_notif_masyarakat FOREIGN KEY (id_masyarakat) 
        REFERENCES masyarakat(id_masyarakat) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_notif_petugas FOREIGN KEY (id_petugas) 
        REFERENCES petugas(id_petugas) ON UPDATE CASCADE ON DELETE CASCADE
);