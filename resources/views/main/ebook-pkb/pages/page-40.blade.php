{{-- Halaman 40: Lampiran Detail Lanjutan --}}
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
            <div class="pasal-title">PASAL 37</div>
            <div class="pasal-subtitle">HAK-HAK AKIBAT PEMUTUSAN HUBUNGAN KERJA</div>

            <ol class="ol-main">
                <li>
                    Karyawan yang mengalami pemutusan hubungan kerja mendapatkan uang pesangon, uang penghargaan masa kerja dan uang pisah sesuai dengan ketentuan perundang-undangan yang berlaku.
                </li>
                <li>
                    Komponen upah/gaji yang menjadi dasar perhitungan uang pesangon, uang penghargaan masa kerja, dan uang pisah adalah upah pokok ditambah tunjangan yang bersifat tetap dan akan dikenakan potongan sesuai peraturan perpajakan yang berlaku.
                </li>
                <li>
                    Dalam hal undang-undang juga memerintahkan pemberian uang pisah, maka besaran uang pisah diberikan kepada Karyawan yang mempunyai Masa kerja lebih dari 3 tahun mendapatkan uang pisah Rp. 600.000,-
                </li>
            </ol>

            <div class="pasal-title" style="margin-top: 20px;">PASAL 38</div>
            <div class="pasal-subtitle">KESEMPATAN MEMBELA DIRI</div>

            <ol class="ol-main">
                <li>
                    Dalam hal pengenaan sanksi atau tindakan atas pelanggaran/kesalahan yang telah dilakukan karyawan, karyawan dapat untuk membela diri dalam waktu 1 (satu) hari kerja sejak tanggal pengenaan sanksi.
                </li>
                <li>
                    Membela diri sebagaimana dimaksud dalam ayat (1), karyawan membuat risalah tentang pembelaan diri karyawan yang memuat keterangan karyawan, keterangan saksi, dan dokumen pendukung sebagai bukti, yang kemudian disampaikan kepada Perusahaan.
                </li>
                <li>
                    Perusahaan akan mempertimbangkan risalah tentang pembelaan diri karyawan untuk tetap atau tidak memberlakukan pengenaan sanksi atau tindakan atas pelanggaran/kesalahan.
                </li>
            </ol>
            <div style="margin-top: 30px;">
                <img src="{{ asset('sipo_krisan/public/images/ttd.png') }}" alt="Page 40" style="width: 30%; height: auto; display: inline-block;">
            </div>
        </div>
    </div>
</div>
