<?php

namespace App\Imports;

use App\Models\MappingItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MappingItemsImport implements ToModel, WithHeadingRow
{

    protected static $dieCutMap = [];
    protected static $dieCutCounter = 1;

    public function model(array $row)
    {
        // dd($row);
        if (!isset($row['kode']) || trim($row['kode']) === '') {
            return null;
        }

        $dimensiKey = $row['p_kertas'] . '-' . $row['l_kertas'] . '-' . $row['t_kertas'];

        if (empty($row['die_cut'])) {
            if (isset(self::$dieCutMap[$dimensiKey])) {
                $dieCutItem = self::$dieCutMap[$dimensiKey];
            } else {
                $dieCutItem = 'DCT-' . str_pad(self::$dieCutCounter, 2, '0', STR_PAD_LEFT);
                self::$dieCutMap[$dimensiKey] = $dieCutItem;
                self::$dieCutCounter++;
            }
        } else {
            $dieCutItem = $row['die_cut'];
            if (!isset(self::$dieCutMap[$dimensiKey])) {
                self::$dieCutMap[$dimensiKey] = $dieCutItem;
            }
        }

        // Ambil tinta dari DB mysql3
        $kodeMaterial = $row['kode'] . '.WIP.CTK';
        $tintaRecords = DB::connection('mysql3')
            ->table('masterbomd') // ganti dengan nama tabel yang sesuai
            ->where('formula', 'like', '%' . $kodeMaterial . '%') // ganti dengan nama kolom formula
            ->first();

        if (!$tintaRecords) {
            return null;
        }

        $detailTInta = DB::connection('mysql3')
            ->table('masterbomd')
            ->leftJoin('mastermaterial', 'masterbomd.MaterialCode', '=', 'mastermaterial.Code')
            ->where('Formula', $tintaRecords->Formula)
            ->where('MaterialCode', 'like', '%T.CO%')
            ->pluck('mastermaterial.Name')
            ->toArray();

        // dd($detailTInta);

        // Urutkan abjad
        sort($detailTInta);

        // Hitung jumlah warna
        $jumlahWarna = count($detailTInta);



        // Isi ke t1-t7
        $tintaArr = [];
        for ($i = 0; $i < 20; $i++) {
            $tintaArr[$i] = isset($detailTInta[$i]) ? $detailTInta[$i] : null;
        }

        return new MappingItem([
            'kode' => $row['kode'],
            'nama_barang' => $row['nama_barang'],
            'jumlah' => $row['jumlah_up'],
            'panjang' => $row['panjang_cetak'],
            'lebar' => $row['lebar_cetak'],
            'gramasi' => $row['gramature'],
            'kg_per_pcs' => $row['kg_per_pcs'],
            'pcs_dos' => $row['pcs_per_dos'],
            'speed' => $row['prod_speed'],
            'target' => $row['target_hours'],
            'tipe_job' => $row['tipe_job'],
            'optimal' => $row['optimal_job_mesin'],
            'information' => $row['information'],
            'jumlah_warna' => $jumlahWarna ?? 0,
            't1' => $tintaArr[0] ?? '',
            't2' => $tintaArr[1] ?? '',
            't3' => $tintaArr[2] ?? '',
            't4' => $tintaArr[3] ?? '',
            't5' => $tintaArr[4] ?? '',
            't6' => $tintaArr[5] ?? '',
            't7' => $tintaArr[6] ?? '',
            't8' => $tintaArr[7] ?? '',
            't9' => $tintaArr[8] ?? '',
            't10' => $tintaArr[9] ?? '',
            't11' => $tintaArr[10] ?? '',
            't12' => $tintaArr[11] ?? '',
            't13' => $tintaArr[12] ?? '',
            't14' => $tintaArr[13] ?? '',
            't15' => $tintaArr[14] ?? '',
            'coating1' => $row['coating1'],
            'coating2' => $row['coating2'],
            'p_kertas' => $row['p_kertas'],
            'l_kertas' => $row['l_kertas'],
            't_kertas' => $row['t_kertas'],
            'die_cut_item' => $dieCutItem,
            'm_ptg' => $row['m_ptg'],
            'm_ctk' => $row['m_ctk'],
            'm_hp' => $row['m_hp'],
            'm_uv' => $row['m_uv'],
            'm_emb' => $row['m_emb'],
            'm_epl' => $row['m_epl'],
            'm_plg' => $row['m_plg'],
            'm_kps' => $row['m_kps'],
            'm_str' => $row['m_str'],
            'm_lem' => $row['m_lem'],
            'm_lmg' => $row['m_lmg'],
            'm_lms' => $row['m_lms'],
            'm_cal' => $row['m_cal'],
            'm_wbv' => $row['m_wbv'],
            'm_em1' => $row['m_em1'],
            'm_uv1' => $row['m_uv1'],
        ]);
    }
}
