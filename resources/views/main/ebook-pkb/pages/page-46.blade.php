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
    <img src="{{ asset('sipo_krisan/public/46.jpg') }}" style="width: 95%; object-fit: cover;">

    {{-- <div class="page-content-text">
        <div class="article">
            <div class="article-title" style="margin-bottom: 30px;">DAFTAR DELEGASI PERUNDINGAN<br>PERJANJIAN KERJA BERSAMA PERIODE 2026 - 2028</div>

            <p class="paragraph" style="text-indent: 0; margin-top: 20px; font-weight: 600; text-align: center;">
                WAKIL PERUSAHAAN PT KRISANTHIUM OFFSET PRINTING :
            </p>

            <table style="margin-top: 30px; width: 100%; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="width: 50px; padding: 5px 0;">1</td>
                        <td style="padding: 5px 0;">Ratna Yani Astuty</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;">2</td>
                        <td style="padding: 5px 0;">Heni Puspita Sari</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;">3</td>
                        <td style="padding: 5px 0;">Erwin Gunawan</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;">4</td>
                        <td style="padding: 5px 0;">Mohammad Rizal Siddik</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;">5</td>
                        <td style="padding: 5px 0;">Viky Hartanto</td>
                    </tr>
                </tbody>
            </table>

            <p class="paragraph" style="text-indent: 0; margin-top: 40px; font-weight: 600; text-align: center;">
                SPSI UNIT KERJA PT KRISANTHIUM OFFSET PRINTING
            </p>

            <table style="margin-top: 30px; width: 100%; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="width: 50px; padding: 5px 0;">1</td>
                        <td style="padding: 5px 0;">Heri Suharno</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;">2</td>
                        <td style="padding: 5px 0;">Sriadi</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;">3</td>
                        <td style="padding: 5px 0;">Nur Kholimi</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;">4</td>
                        <td style="padding: 5px 0;">Denny Supriyanto</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;">5</td>
                        <td style="padding: 5px 0;">Bambang Gunawan</td>
                    </tr>
                </tbody>
            </table>
            <div style="margin-top: 30px;">
                <img src="{{ asset('sipo_krisan/public/images/ttd.png') }}" alt="Page 46" style="width: 30%; height: auto; display: inline-block;">
            </div>
        </div>
    </div> --}}
</div>
