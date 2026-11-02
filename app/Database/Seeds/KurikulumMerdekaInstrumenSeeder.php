<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KurikulumMerdekaInstrumenSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $now = date('Y-m-d H:i:s');

        $allData = array_merge(
            KurikulumMerdekaDataManajemenKelas::data(),
            KurikulumMerdekaDataDukunganPsikologis::data(),
            KurikulumMerdekaDataMetodePembelajaran::data(),
            KurikulumMerdekaDataPerangkatAjar::data()
        );

        foreach ($allData as $item) {
            $existingJenis = $db->table('jenis_penilaian')
                ->where('nama', $item['nama'])
                ->get()
                ->getRowArray();

            $isPmm = ($item['kategori_supervisi'] === 'observasi_kelas_pmm');
            $tipeSkala = $isPmm ? 'perilaku_pmm_1_3' : 'skor_1_4';
            $skorMaks = $isPmm ? 3 : 100;
            $kategoriSkor = $isPmm
                ? json_encode(['1' => 'Belum Dilakukan', '2' => 'Dilakukan tapi Belum Efektif', '3' => 'Dilakukan dan Efektif'])
                : json_encode(['86-100' => 'Baik Sekali', '70-85' => 'Baik', '55-69' => 'Cukup', '0-54' => 'Kurang']);

            $jenisData = [
                'nama'               => $item['nama'],
                'skor_maksimal'      => $skorMaks,
                'kategori_skor'      => $kategoriSkor,
                'kategori_supervisi' => $item['kategori_supervisi'],
                'dimensi_pmm'        => $item['dimensi_pmm'],
                'deskripsi'          => $item['deskripsi'],
                'created_at'         => $now,
            ];

            if ($existingJenis) {
                $jenisId = $existingJenis['id'];
                $db->table('jenis_penilaian')->where('id', $jenisId)->update($jenisData);
            } else {
                $db->table('jenis_penilaian')->insert($jenisData);
                $jenisId = $db->insertID();
            }

            // Insert atau update aspek-aspek / fokus perilaku
            $urutan = 1;
            foreach ($item['fokus'] as $fokus) {
                $existingAspek = $db->table('aspek_penilaian')
                    ->where('jenis_penilaian_id', $jenisId)
                    ->where('nama_aspek', $fokus['nama_aspek'])
                    ->get()
                    ->getRowArray();

                $aspekData = [
                    'jenis_penilaian_id'  => $jenisId,
                    'nama_aspek'          => $fokus['nama_aspek'],
                    'fokus_perilaku'      => $fokus['fokus_perilaku'],
                    'perilaku_dianjurkan' => $fokus['perilaku_dianjurkan'],
                    'perilaku_dihindari'  => $fokus['perilaku_dihindari'],
                    'tipe_skala'          => $tipeSkala,
                    'urutan'              => $urutan++,
                    'created_at'          => $now,
                ];

                if ($existingAspek) {
                    $db->table('aspek_penilaian')->where('id', $existingAspek['id'])->update($aspekData);
                } else {
                    $db->table('aspek_penilaian')->insert($aspekData);
                }
            }
        }
    }
}
