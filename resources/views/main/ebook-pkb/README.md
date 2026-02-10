# E-Book PKB - Panduan Mengisi Konten

## Struktur File

Halaman PKB disimpan di folder `resources/views/main/ebook-pkb/pages/` dengan format:
- `page-1.blade.php` - Halaman Cover (sudah dibuat)
- `page-2.blade.php` - Halaman Keputusan (sudah dibuat)
- `page-3.blade.php` sampai `page-50.blade.php` - Halaman konten PKB

## Cara Mengisi Konten dari DOCX

1. **Buka file DOCX** `public/PKB FINAL.docx`

2. **Untuk setiap halaman:**
   - Buka file blade sesuai nomor halaman (contoh: `page-3.blade.php`)
   - Copy paste konten dari DOCX ke dalam file blade
   - Format dengan HTML sesuai kebutuhan

## Template HTML untuk Konten PKB

### Pasal (Article)
```html
<div class="article">
    <div class="article-title">PASAL 1</div>
    <p class="paragraph">
        Isi pasal di sini...
    </p>
</div>
```

### Ayat/Paragraf
```html
<p class="paragraph">
    <span class="paragraph-number">(1)</span>
    Isi ayat di sini...
</p>
```

### List Numbering
```html
<ol>
    <li class="list-item">Item pertama</li>
    <li class="list-item">Item kedua</li>
</ol>
```

### List dengan sub-item
```html
<ol type="a">
    <li class="list-item-numbered">Item a</li>
    <li class="list-item-numbered">Item b</li>
</ol>
```

### Tabel
```html
<table>
    <thead>
        <tr>
            <th>Kolom 1</th>
            <th>Kolom 2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Data 1</td>
            <td>Data 2</td>
        </tr>
    </tbody>
</table>
```

### Tanda Tangan
```html
<div class="signature-section">
    <div class="signature-box">
        <p>Pihak Perusahaan</p>
        <div class="signature-name">Nama Penandatangan</div>
        <p>Jabatan</p>
    </div>
    <div class="signature-box">
        <p>Pihak Pekerja</p>
        <div class="signature-name">Nama Penandatangan</div>
        <p>Jabatan</p>
    </div>
</div>
```

## Tips

- Pastikan format HTML konsisten untuk setiap halaman
- Gunakan class CSS yang sudah disediakan untuk styling yang konsisten
- Test tampilan di browser setelah mengisi konten
- Search functionality akan otomatis mencari di semua halaman yang sudah diisi

