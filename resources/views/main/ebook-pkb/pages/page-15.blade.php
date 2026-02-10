{{-- Halaman 15: Pasal Pengawasan --}}
<style>
    /* Halaman mirip A4 */
    .page {

        margin: 0 auto;
        /* padding: 70px 80px 60px; */
        /* atas kanan-kiri bawah */
        background: #fff;
        color: #000;
        font-family: "Times New Roman", Times, serif;
        font-size: 12pt;
        line-height: 1.55;
        box-sizing: border-box;
        position: relative;
    }

    /* Header kecil kiri atas (opsional) */
    .doc-mark {
        position: absolute;
        top: 22px;
        left: 80px;
        font-size: 10.5pt;
        font-style: italic;
    }

    .doc-mark u {
        text-underline-offset: 2px;
    }

    /* Judul pasal */
    .pasal-title {
        text-align: center;
        font-weight: 700;
        text-transform: uppercase;
        margin: 18px 0 2px;
    }

    .pasal-subtitle {
        text-align: center;
        font-weight: 700;
        text-transform: uppercase;
        margin: 0 0 12px;
    }

    /* Paragraf & list */
    p {
        margin: 0 0 10px;
        text-align: justify;
        text-justify: inter-word;
    }

    .ol-main {
        margin: 0 0 10px 24px;
        padding: 0;
    }

    .ol-main>li {
        margin: 0 0 10px;
        text-align: justify;
        text-justify: inter-word;
    }

    .ol-alpha {
        margin: 6px 0 0 18px;
        padding: 0;
    }

    .ol-alpha>li {
        margin: 0 0 6px;
        text-align: justify;
        text-justify: inter-word;
    }

    /* Angka list agak rapat seperti dokumen */
    .ol-main>li::marker,
    .ol-alpha>li::marker {
        font-weight: 700;
    }

    /* Footer nomor halaman kanan bawah */
    .page-no {
        position: absolute;
        right: 80px;
        bottom: 24px;
        font-size: 10.5pt;
    }
</style>

<div class="ebook-page-standard">
    <div class="page-content-text">
        <!-- LANJUTAN (halaman berikutnya) -->
        <div class="page">
            {{-- <div class="doc-mark"><u>PT Krisanthium</u></div> --}}

            <div class="pasal-title">PASAL 10</div>
            <div class="pasal-subtitle">PAKAIAN KERJA</div>

            <ol class="ol-main">
                <li>
                    Setiap karyawan/karyawati yang bekerja di lingkungan PTKrisanthium wajib berpakaian sopan,
                    rapi, celana panjang/rok dan bersepatu.
                </li>
                <li>
                    Bagi karyawan yang tidak memakai pakaian sesuai ayat 1, maka petugas satpam berhak menolak
                    untuk masuk lokasi pabrik dan karyawan yang bersangkutan dinyatakan tidak hadir/mangkir.
                </li>
                <li>
                    Bagi karyawan yang melakukan pelanggaran sebagaimana dimaksud dalam ayat (2) diatas, akan
                    dikenakan sanksi berupa surat peringatan tertulis dan upah tidak dibayar sebesar jam-jam
                    tidak melakukan pekerjaan sesuai dengan Pasal 8 ayat 3.
                </li>
            </ol>

            <div class="pasal-title" style="margin-top:18px;">PASAL 11</div>
            <div class="pasal-subtitle">PERLENGKAPAN KERJA DAN ALAT PELINDUNG DIRI</div>

            <ol class="ol-main">
                <li>
                    Untuk melindungi keselamatan dan kesehatan karyawan selama bekerja di tempat-tempat dan/atau
                    dalam keadaan-keadaan yang oleh perusahaan dianggap perlu, maka perusahaan akan menyediakan
                    alat-alat keselamatan kerja untuk dipakai atau dipergunakan pada waktu kerja.
                </li>
                <li>
                    Perusahaan akan menentukan macam dan jenis alat keselamatan kerja yang dipinjamkan atau
                    dipergunakan berdasarkan kebutuhan, kondisi, dan keadaan pekerjaan yang harus dilakukan
                    oleh karyawan.
                </li>
                <li>
                    Apabila karyawan menemukan hal-hal yang dapat membahayakan terhadap keselamatan karyawan dan
                    perusahaan, harus segera melaporkan kepada pimpinan atau atasannya.
                </li>
                <li>
                    Di luar waktu kerja yang telah ditentukan oleh perusahaan, setiap karyawan tidak diperbolehkan
                    memakai/menggunakan alat-alat keselamatan kerja milik perusahaan untuk keperluan pribadi tanpa
                    seizin pimpinan perusahaan.
                </li>
                <li>
                    Setiap karyawan wajib memelihara dan menjaga kebersihan alat-alat/perlengkapan keselamatan kerja
                    dengan baik dan tertib.
                </li>
                <li>
                    Bagi karyawan yang karena sifat, fungsi dan lokasi pekerjaannya berhubungan dengan bahan-bahan
                    kimia maka karyawan yang bersangkutan setiap hari diberikan 1 (satu) gelas/bungkus susu.
                </li>
            </ol>

        </div>

            <div style="margin-top: 30px;">
                <img src="{{ asset('sipo_krisan/public/images/ttd.png') }}" alt="Page 15" style="width: 30%; height: auto; display: inline-block;">
            </div>
    </div>
</div>
