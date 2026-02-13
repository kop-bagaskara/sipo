# Setup Google Drive untuk Video Training

## Ringkasan
Sistem sekarang mendukung video dari Google Drive dengan **kontrol penuh** (prevent skip, speed control tetap berfungsi) karena video di-stream melalui Laravel.

## Cara Kerja
1. Video disimpan di Google Drive (oleh IT/EDP)
2. Sistem mengambil File ID dari Google Drive
3. Video di-stream melalui Laravel dengan kontrol penuh menggunakan Plyr.js
4. Semua fitur kontrol (prevent skip, progress tracking, speed control) tetap berfungsi

## Setup

### 1. Dapatkan Google Drive API Key
1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih project yang ada
3. Enable **Google Drive API**
4. Buat **API Key** (Credentials > Create Credentials > API Key)
5. Restrict API Key ke **Google Drive API** (opsional, untuk keamanan)

### 2. Konfigurasi di Laravel
Tambahkan ke file `.env`:
```env
GOOGLE_DRIVE_API_KEY=your_api_key_here
```

### 3. Upload Video ke Google Drive
1. Upload video ke Google Drive (oleh IT/EDP)
2. Set sharing permission ke **"Anyone with the link"** atau **"Public"**
3. Copy File ID atau URL lengkap dari Google Drive

### 4. Input di Sistem
Saat membuat/edit Training Session:
1. Centang "Sesi ini memiliki video"
2. Pilih "Google Drive" sebagai sumber video
3. Masukkan File ID atau URL lengkap dari Google Drive
   - Contoh File ID: `1a2b3c4d5e6f7g8h9i0j`
   - Contoh URL: `https://drive.google.com/file/d/1a2b3c4d5e6f7g8h9i0j/view`
4. Sistem akan otomatis mengekstrak File ID

## Format URL Google Drive yang Didukung
- `https://drive.google.com/file/d/FILE_ID/view`
- `https://drive.google.com/open?id=FILE_ID`
- `https://drive.google.com/uc?id=FILE_ID`
- `FILE_ID` (langsung)

## Fitur yang Tetap Berfungsi
✅ Prevent skip video (tidak bisa skip bagian yang belum ditonton)
✅ Progress tracking (track progress video)
✅ Speed control (tetap bisa dikontrol, default 1x)
✅ Video completion check (harus selesai sebelum mulai soal)

## Catatan Penting
- Video harus di-share dengan permission "Anyone with the link" atau "Public"
- API Key harus memiliki akses ke Google Drive API
- Video di-stream melalui Laravel, jadi performa tergantung pada koneksi server ke Google Drive
- Untuk video besar, pertimbangkan menggunakan CDN atau cache

## Troubleshooting

### Video tidak muncul
- Pastikan API Key sudah dikonfigurasi di `.env`
- Pastikan video di Google Drive sudah di-share dengan permission yang benar
- Cek log Laravel untuk error detail

### Video tidak bisa di-stream
- Pastikan Google Drive API sudah di-enable di Google Cloud Console
- Pastikan API Key tidak di-restrict terlalu ketat
- Cek koneksi server ke internet

### Error 403 Forbidden
- Pastikan video di Google Drive sudah di-share dengan permission "Anyone with the link"
- Pastikan API Key memiliki akses ke Google Drive API

