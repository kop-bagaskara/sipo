{{-- Halaman 30: Pasal Perubahan dan Evaluasi --}}
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
    <div class="page-content-text">
        <div class="article">
            <ol class="ol-main" start="5">
                <li>
                    Segala sesuatu yang bertentangan dengan ayat 4 dalam pasal ini, maka perusahaan dapat menolak /
                    tidak wajib membayar upah lembur yang telah dilaksanakan.
                </li>
                <li>
                    Apabila lembur kurang 60 menit tidak akan diperhitungkan sebagai kerja lembur dan direntang 60
                    menit keatas maka akan diperhitungkan lembur.
                </li>
            </ol>

            <div class="pasal-title" style="margin-top: 20px;">PASAL 30</div>
            <div class="pasal-subtitle">UPAH LEMBUR</div>

            <p class="paragraph" style="margin-top: 18px;">
                Perhitungan upah lembur diatur sesuai dengan ketentuan undang-undang yang berlaku sebagai berikut :
            </p>

            <ol class="ol-main">
                <li>
                    Kerja lembur dilakukan pada hari kerja biasa :
                    <ol class="ol-alpha">
                        <li>
                            Jam lembur pertama = 1,5 x upah sejam.
                        </li>
                        <li>
                            Jam lembur kedua dan seterusnya = 2 x upah sejam.
                        </li>
                    </ol>
                </li>
                <li>
                    Apabila Kerja lembur dilakukan pada hari istirahat mingguan dan/atau hari libur resmi untuk waktu
                    kerja 40(empat puluh) jam seminggu, maka
                    <ol class="ol-alpha">
                        <li>
                            Perhitungan lembur :
                            <ol class="ol-main" style="margin-left: 36px; margin-top: 6px;">
                                <li>
                                    7 (tujuh) Jam pertama dibayar = 2 x upah sejam.
                                </li>
                                <li>
                                    Jam lembur ke 8 (delapan) = 3 x upah sejam.
                                </li>
                                <li>
                                    Jam lembur ke 9 (sembilan) dan seterusnya = 4 x upah sejam.
                                </li>
                            </ol>
                        </li>
                        <li>
                            Apabila hari libur resmi jatuh pada hari kerja terpendek maka perhitungan upah lembur
                            sebagai berikut :
                            <ol class="ol-main" style="margin-left: 36px; margin-top: 6px;">
                                <li>
                                    5 (lima) jam pertama dibayar = 2 x upah sejam.
                                </li>
                                <li>
                                    Jam lembur ke 6 (enam) = 3 x upah sejam.
                                </li>
                                <li>
                                    Jam lembur ke 7(tujuh) dan seterusnya = 4 x upah sejam.
                                </li>
                            </ol>
                        </li>
                    </ol>
                </li>
                <li>
                    Perhitungan upah lembur perjam adalah :
                    <div style="border: 1px solid #000; padding: 6px 24px; display: inline-block; margin-top: 10px;">
                        (Gaji pokok + Tunj Tetap) x 1/173.
                    </div>
                </li>
                <li>
                    Bagi karyawan dengan golongan Staff ke atas tidak berhak mendapatkan upah lembur.
                </li>
            </ol>
            <div style="margin-top: 30px;">
                <img src="{{ asset('sipo_krisan/public/images/ttd.png') }}" alt="Page 30" style="width: 30%; height: auto; display: inline-block;">
            </div>
        </div>
    </div>
</div>
