{{-- Halaman 27: Pasal Hak dan Kewajiban --}}
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
            <div class="pasal-title" style="margin-top: 10px;">PASAL 24</div>
            <div class="pasal-subtitle">KOMPONEN UPAH/GAJI</div>

            <p class="paragraph" style="text-indent: 0; margin-top: 20px;">
                Komponen Upah/Gaji karyawan ditetapkan oleh perusahaan, terdiri atas :
            </p>

            <ol class="ol-main">
                <li>Upah/Gaji Pokok.</li>
                <li>Tunjangan Tetap.</li>
                <li>Tunjangan Tidak Tetap.</li>
            </ol>

            <p class="paragraph" style="text-indent: 0; margin-top: 10px;">
                Besarnya Tunjangan Tidak Tetap (T3) karyawan ditentukan oleh perusahaan dengan mempertimbangkan faktor
                prestasi kerja dari karyawan yang bersangkutan sesuai dengan Penilaian Karyawan.
            </p>
        </div>

        <div class="article" style="margin-top: 30px;">
            <div class="pasal-title">PASAL 25</div>
            <div class="pasal-subtitle">PENYESUAIAN UPAH</div>

            <ol class="ol-main">
                <li>
                    Upah menyesuaikan Peraturan Pemerintah tentang Upah Minimum Kabupaten / Kota (UMK) yang
                    ditetapkan.
                </li>
                <li>
                    Besarnya kenaikan upah akan ditentukan oleh faktor-faktor sebagai berikut :
                    <ol class="ol-alpha">
                        <li>Kemampuan perusahaan.</li>
                        <li>
                            Key Performance Indicator (KPI) atau penilaian evaluasi karyawan pada periode sebelumnya
                            yang dilakukan oleh Team Leader, head dan manager, yang didalamnya menyangkut penilaian
                            produktifitas dan kehadiran (Absensi) serta kedisiplinan.
                        </li>
                    </ol>
                </li>
            </ol>
        </div>

        <div class="article" style="margin-top: 30px;">
            <div class="pasal-title">PASAL 26</div>
            <div class="pasal-subtitle">PEMOTONGAN UPAH</div>

            <p class="paragraph" style="text-indent: 0; margin-top: 20px;">
                Pemotongan upah/gaji dapat dilakukan perusahaan untuk hal-hal sebagai berikut :
            </p>

            <ol class="ol-main">
                <li>
                    Iuran BPJS ketenagakerjaan dan BPJS kesehatan sesuai peraturan perundang-undangan yang berlaku.
                </li>
                <li>
                    Karyawan yang tidak hadir tanpa mengisi form yang sudah di isi dan disetujui oleh Atasan
                    masing-masing serta tidak dapat memberikan bukti yang sah.
                </li>
                <li>Karyawan yang mengalami demosi.</li>
                <li>
                    Karyawan yang terlambat datang atau pulang lebih awal untuk keperluan pribadi maka upah tidak
                    dibayar dengan perhitungan sesuai dengan pasal 8 ayat 3 dalam perjanjian kerja bersama ini.
                </li>
                <li>
                    Karyawan yang menerima Surat Peringatan 2 dan Surat Peringatan 3 yang sudah ditetapkan pada
                    perjanjian kerja bersama ini.
                </li>
                <li>
                    Teledor, ceroboh karena kurang hati-hati dalam melaksanakan tugas karyawan dan/atau melakukan
                    pelanggaran sebagaimana diatur dalam Perjanjian Kerja Bersama ini sehingga mengakibatkan perusahaan
                    harus menanggung kerugian yang ditimbulkan baik berupa material maupun immaterial, maka perusahaan
                    berhak melakukan pemotongan upah atas ganti rugi. Mekanisme penggantiannya akan diatur tersendiri
                    di dalam Petunjuk Pelaksanaan (Juklak).
                </li>
            </ol>
            <div style="margin-top: 30px;">
                <img src="{{ asset('sipo_krisan/public/images/ttd.png') }}" alt="Page 27" style="width: 30%; height: auto; display: inline-block;">
            </div>
        </div>
    </div>
</div>
