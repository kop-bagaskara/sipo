{{-- Halaman 19: Lampiran --}}
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

        <p style="text-indent: 50px;">
            dan/atau menyerahkan pekerjaan kepada orang lain tanpa ijin dari atasannya.
        </p>
        <ol type="1" class="ol-main" start="25">
            <li>
                Karyawan dilarang menempatkan/memarkir kendaraan (sepeda pancal/sepeda motor/ mobil) bukan pada tempat
                parkir kendaraan yang telah ditentukan.
            </li>

            <li>
                Petugas Satpam mempunyai wewenang dan berhak memeriksa dan/atau menggeledah karyawan pada waktu keluar
                atau masuk lokasi perusahaan.
            </li>

            <li>
                Karyawan dilarang merokok di area perusahaan pada jam kerja. Merokok hanya diijinkan di luar jam kerja
                dan di area yang telah ditentukan perusahaan.
            </li>

        </ol>
        <!-- HALAMAN PASAL 14 & BAB IV -->
        <div class="page">
            <div class="pasal-title">PASAL 14</div>
            <div class="pasal-subtitle">KEADAAN FORCE MAJEURE</div>

            {{-- <ol class="ol-main"> --}}
            <p>
                Pada saat-saat tertentu dan karena suatu hal yang sangat mendesak dan tidak dapat
                dihindari / darurat (force majeure), perusahaan memperbolehkan cuti bersama bagi
                karyawan. Pengaturan hal ini akan dilakukan sesuai dengan kesepakatan.
            </p>
            {{-- </ol> --}}

            <div class="pasal-title" style="margin-top:18px;">BAB IV</div>
            <div class="pasal-title">HAK DAN KEWAJIBAN KARYAWAN</div>

            <br>

            <div class="pasal-subtitle">PASAL 15</div>
            <div class="pasal-subtitle">HAK KARYAWAN</div>

            <ol class="ol-main">
                <li>
                    Karyawan berhak mendapatkan perlakuan yang sama sesuai/berdasarkan jabatan dan prestasinya
                    masing-masing.
                </li>

                <li>
                    Karyawan berhak mendapatkan kesejahteraan dan jaminan sosial yang layak sesuai dengan Peraturan
                    Perundang-undangan yang berlaku.
                </li>

                <li>
                    Karyawan berhak atas upah sebagai imbalan dari kerja yang dilakukannya sesuai dengan Peraturan
                    Perundang-undangan yang berlaku. </li>

                <li>
                    Karyawan berhak atas cuti tahunan, cuti haid, cuti melahirkan (bersalin) dan cuti khusus. </li>

                <li>
                    Karyawan dan keluarganya berhak untuk memperoleh bantuan pelayanan kesehatan sesuai dengan ketentuan
                    yang berlaku. </li>
                <li>
                    Karyawan berhak memperoleh santunan atas kecacatan dan/atau kematian akibat kecelakaan kerja dalam
                    melakukan tugas perusahaan sesuai dengan ketentuan Perundang-undangan yang berlaku. </li>
            </ol>

        </div>

            <div style="margin-top: 30px;">
                <img src="{{ asset('sipo_krisan/public/images/ttd.png') }}" alt="Page 19" style="width: 30%; height: auto; display: inline-block;">
            </div>
    </div>
</div>
