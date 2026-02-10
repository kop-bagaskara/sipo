{{-- Halaman 45: Daftar Delegasi Perundingan --}}
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

    <img src="{{ asset('sipo_krisan/public/45.jpg') }}" style="width: 95%; object-fit: cover;">


    {{-- <div class="page-content-text">
        <div class="article" style="margin-top: 40px;">
            <div class="article-title" style="margin-bottom: 20px;">LEMBAR PERSETUJUAN KEDUA BELAH PIHAK<br>PERIODE 2026 - 2028</div>

            <p class="paragraph" style="text-indent: 0; margin-top: 20px; text-align: center;">
                Perjanjian Kerja ini telah disetujui dan disepakati oleh kedua belah pihak di Surabaya pada tanggal 28 November 2025.
            </p>

            <div style="display: flex; justify-content: space-between; margin-top: 80px; align-items: flex-start;">
                <div style="flex: 1; text-align: center;">
                    <p style="font-weight: 700; margin-bottom: 10px;">SPSI UNIT KERJA</p>
                    <p style="margin-bottom: 10px;">PT KRISANTHIUM OFFSET PRINT</p>
                    <div style="margin-top: 100px;">
                        <p style="font-weight: 600;">HERI SUHARNO</p>
                    </div>
                </div>

                <div style="flex: 1; text-align: center;">
                    <p style="font-weight: 700; margin-bottom: 10px;">PIMPINAN PERUSAHAAN</p>
                    <p style="margin-bottom: 10px;">PT KRISANTHIUM OFFSET PRINT</p>
                    <div style="margin-top: 100px;">
                        <p style="font-weight: 600;">SHIERLY A. SANTOSO</p>
                    </div>
                </div>
            </div>
            <div style="margin-top: 30px; text-align: center;">
                <img src="{{ asset('sipo_krisan/public/images/ttd.png') }}" alt="Page 45" style="width: 30%; height: auto; display: inline-block;">
            </div>
        </div>
    </div> --}}
</div>
