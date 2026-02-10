{{-- Halaman 44: Lampiran Detail Lanjutan --}}
<style>
    /* Halaman mirip A4 */
    .page {

        margin: 0 auto;
        padding: 70px 80px 60px;
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
        list-style-type: lower-alpha;
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
        <div class="article">
            <ol class="ol-main" start="3">
                <li>
                    Apabila terdapat ketentuan dalam perjanjian kerja bersama ini yang bertentangan dengan peraturan perundang-undangan yang berlaku, maka ketentuan tersebut batal demi hukum dan yang berlaku adalah peraturan perundang-undangan.
                </li>
                <li>
                    Dengan berlakunya perjanjian kerja bersama ini, maka semua peraturan pelaksana yang pernah dikeluarkan dinyatakan tetap berlaku sepanjang tidak bertentangan dengan perjanjian kerja bersama ini.
                </li>
            </ol>

            <div class="pasal-title" style="margin-top: 20px;">PASAL 46</div>
            <div class="pasal-subtitle">PENUTUP</div>

            <ol class="ol-main">
                <li>
                    Perjanjian Kerja Bersama ini mengikat para Karyawan dan Pengusaha selama 2 (dua) tahun yaitu berlaku sejak 2 Januari 2026 sampai 2 Januari 2028.
                </li>
                <li>
                    Setelah masa tersebut berakhir, Perjanjian Kerja Bersama ini dapat diperpanjang paling lama 1 (satu) tahun, kecuali apabila salah satu pihak memberitahukan secara tertulis tentang keinginannya untuk membuka permusyawaratan guna memperbaharui Perjanjian Kerja Bersama ini.
                </li>
                <li>
                    Hal-hal tentang perubahan dan/atau yang belum diatur atau dimuat dalam Perjanjian Kerja Bersama ini akan dimusyawarahkan bersama antara pihak Pengusaha dengan pihak Serikat Pekerja (SPSI) dan disesuaikan dengan Peraturan Ketenagakerjaan yang baru.
                </li>
            </ol>
            <div style="margin-top: 30px;">
                <img src="{{ asset('sipo_krisan/public/images/ttd.png') }}" alt="Page 44" style="width: 30%; height: auto; display: inline-block;">
            </div>
        </div>
    </div>
</div>
