{{-- Halaman 41: Lampiran Detail Lanjutan --}}
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
            <div class="article-title">BAB XI<br>JAMINAN SOSIAL</div>

            <p style="margin-top: 20px; text-align: center; font-weight: 700;">
                KARYAWAN DIIKUTSERTAKAN DALAM PROGRAM BPJS KESEHATAN DAN BPJS KETENAGAKERJAAN.
            </p>

            <div class="pasal-title" style="margin-top: 20px;">PASAL 39</div>
            <div class="pasal-subtitle">BPJS KESEHATAN</div>

            <p style="margin-top: 12px; font-weight: 700;">
                Cakupan pelayanan :
            </p>

            <ol class="ol-main">
                <li>
                    Pelayanan kesehatan tingkat I (pertama)
                </li>
                <li>
                    Pelayanan kesehatan tingkat lanjutan
                </li>
                <li>
                    Pelayanan gawat darurat
                </li>
            </ol>

            <p style="margin-top: 12px; font-weight: 700;">
                BPJS Ketenagakerjaan
            </p>

            <p style="margin-top: 6px;">
                Untuk menciptakan rasa aman bagi karyawan dalam bekerja dan sesuai dengan peraturan perundang-undangan yang berlaku, seluruh karyawan diikutsertakan program BPJS Ketenagakerjaan untuk memberikan jaminan terhadap resiko kecelakaan kerja, kematian, jaminan hari tua, dan jaminan pensiun.
            </p>

            <div class="pasal-title" style="margin-top: 20px;">PASAL 40</div>
            <div class="pasal-subtitle">KUNJUNGAN SAKIT, MELAYAT DAN UANG DUKA</div>

            <ol class="ol-main">
                <li>
                    Kunjungan karyawan sakit atau melayat adalah merupakan wujud rasa kekeluargaan dan kepedulian sosial yang harus diperhatikan dan diatur pelaksanaannya.
                </li>
                <li>
                    Untuk karyawan yang sedang sakit dan dirawat di rumah maupun di Rumah Sakit dapat dikunjungi pada waktu diluar jam kerja selama bukan penyakit pandemi/menular berbahaya.
                </li>
                <li>
                    Melayat orang meninggal pada jam kerja hanya untuk kematian karyawan dan diatur agar tidak mengganggu pekerjaan.
                </li>
                <li>
                    Pelayatan yang dapat dihadiri sesuai ketentuan pada ayat (3) adalah dengan batasan radius 100 (seratus) kilometer dihitung dari lokasi Perusahaan dan bukan meninggal diakibatkan oleh penyakit pandemi/menular berbahaya.
                </li>
                <li>
                    Dalam hal karyawan meninggal dunia maka Perusahaan memberikan santunan kepada ahli warisnya sesuai ketentuan Pemerintah / Peraturan Perundang-undangan yang berlaku.
                </li>
                <li>
                    Dalam hal Suami/Isteri karyawan, Orangtua (Ayah/Ibu) kandung, Anak Kandung karyawan meninggal dunia, maka Perusahaan memberikan uang duka sebesar Rp. 500.000,- (Lima ratus ribu rupiah).
                </li>
            </ol>
        </div>
            <div style="margin-top: 30px;">
                <img src="{{ asset('sipo_krisan/public/images/ttd.png') }}" alt="Page 41" style="width: 30%; height: auto; display: inline-block;">
            </div>
    </div>
</div>
