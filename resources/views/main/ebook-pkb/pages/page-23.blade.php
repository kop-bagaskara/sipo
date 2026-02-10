{{-- Halaman 23: Pasal Tambahan --}}
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
            <p style="text-indent: 30px;">berlaku</p>
            <ol class="ol-main" start="2">
                <li>
                    Penerimaan karyawan disesuaikan dengan kebutuhan ketenagakerjaan di Perusahaan.
                </li>
                <li>
                    Perusahaan melaksanakan penerimaan karyawan melalui seleksi dengan memperhatikan kualifikasi, kecakapan, keahlian, dan pengalaman yang diperlukan.
                </li>
                <li>
                    Syarat umum calon karyawan adalah sebagai berikut :
                    <ol class="ol-alpha">
                        <li>
                            Karyawan yang diterima adalah yang mempunyai persyaratan usia, pendidikan, kesehatan, keahlian sesuai dengan persyaratan jabatan yang telah ditetapkan.
                        </li>
                        <li>
                            Calon karyawan yang memenuhi persyaratan dan diterima berdasarkan prosedur rekrutmen yang telah ditetapkan perusahaan.
                        </li>
                        <li>
                            Bersedia mematuhi semua peraturan dalam Perjanjian Kerja Bersama yang berlaku.
                        </li>
                    </ol>
                </li>
            </ol>

            <div class="pasal-title" style="margin-top: 30px;">PASAL 20</div>
            <div class="pasal-subtitle">PROMOSI, DEMOSI DAN MUTASI</div>

            <ol class="ol-main">
                <li>
                    Promosi karyawan dapat diberlakukan dengan alasan :
                    <ol class="ol-alpha">
                        <li>
                            Catatan kerja karyawan yang bersangkutan berdasarkan laporan tingkat prestasi yang baik pada tahun-tahun yang telah dilalui.
                        </li>
                        <li>
                            Absensi pada tahun-tahun yang telah dilalui berkategori baik (dalam periode 3 bulan terakhir / berturut-turut Nihil Absen).
                        </li>
                        <li>
                            Mempunyai kemampuan kerja dan potensi yang terkait dalam pangkat/jabatan baru.
                        </li>
                        <li>
                            Memenuhi syarat-syarat untuk pangkat / jabatan baru.
                        </li>
                        <li>
                            Untuk jabatan tertentu (level Team Leader keatas) wajib lulus test psikologi yang dilakukan oleh lembaga psikologi yang ditunjuk.
                        </li>
                        <li>
                            Masa percobaan untuk promosi adalah 6 (enam) bulan dan bilamana dinyatakan lolos oleh pimpinan kerjanya, maka akan diberikan Surat Keputusan Pengangkatan dari HRGA.
                        </li>
                    </ol>
                </li>
                <li>
                    Demosi (penurunan jabatan ke tingkat yang lebih rendah) berdasarkan pertimbangan prestasi yang menurun antara lain :
                    <ol class="ol-alpha">
                        <li>
                            Berdasarkan hasil penilaian / evaluasi tidak menunjukkan hasil yang baik.
                        </li>
                        <li>
                            Kurang atau tidak bertanggungjawab atas setiap tugas yang diberikan sesuai dengan jabatan / pangkatnya.
                        </li>
                        <li>
                            Melakukan tindakan yang bertentangan atau tidak mentaati peraturan / ketentuan yang berlaku.
                        </li>
                    </ol>
                </li>
            </ol>
            <div style="margin-top: 30px;">
                <img src="{{ asset('sipo_krisan/public/images/ttd.png') }}" alt="Page 23" style="width: 30%; height: auto; display: inline-block;">
            </div>
        </div>
    </div>
</div>
