{{-- Halaman 18: Tanda Tangan --}}
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
        <!-- LANJUTAN: PASAL 13 - TATA TERTIB KARYAWAN -->
        <div class="page">
            {{-- <div class="doc-mark"><u>PT Krisanthium</u></div> --}}


            <ol type="1" class="ol-main" start="12">
                <li>
                    Karyawan dilarang tidur, tiduran, bermalas-malasan, bermain-main pada jam kerja.
                    Dan/atau istirahat sebelum jam yang telah ditentukan.
                </li>

                <li>
                    Karyawan dilarang menyalahgunakan kedudukan jabatan dan kekudukan,
                    menyalahgunakan wewenang yang telah diberikan, dicapainya oleh Manajemen/perusahaan
                    baik yang menerima maupun yang tidak menerima secara formal, misalnya melakukan hal
                    materi atau pribadi lain berkaitan dengan tugas dan jabatan.
                </li>

                <li>
                    Karyawan dilarang mengatur, membawa orang lain yang bukan terdaftar sebagai
                    karyawan PT. Krisanthium, masuk ke dalam lokasi pabrik tanpa izin dari Pimpinan Perusahaan.
                </li>

                <li>
                    Karyawan dilarang memfoto produk, property perusahaan/memfoto copy/memfasilitasi
                    dokumen/menyebarkan program/data menyalahi tujuan perusahaan tanpa izin orang lain.
                </li>

                <li>
                    Karyawan dilarang memakai/menggunakan alat-alat produksi milik perusahaan untuk
                    kepentingan pribadi tanpa izin atasannya.
                </li>

                <li>
                    Karyawan dilarang menghilangkan, merusakkan, dan/atau mempertahankan dengan
                    tidak baik dan tidak bertanggung jawab terhadap barang-barang, peralatan, alat
                    dan dokumen milik perusahaan yang dibebankan kepadanya.
                </li>

                <li>
                    Karyawan dilarang mengkonsumsi makanan, minuman, dan barang pribadi lain
                    di dalam kawasan kerja Perusahaan.
                </li>

                <li>
                    Karyawan dilarang menggunakan sumber dan dana perusahaan milik perusahaan
                    atau bagian yang tidak diperlukan di luar.
                </li>

                <li>
                    Karyawan dilarang membuat atau bekerja pada waktu istirahat/tugas perusahaan lain
                    tanpa izin oleh HRGA dan jelas terkait tujuan.
                </li>

                <li>
                    Karyawan tidak boleh bepergian tanpa izin atasan.
                </li>
            </ol>
        </div>

            <div style="margin-top: 30px;">
                <img src="{{ asset('sipo_krisan/public/images/ttd.png') }}" alt="Page 18" style="width: 30%; height: auto; display: inline-block;">
            </div>
    </div>
</div>
