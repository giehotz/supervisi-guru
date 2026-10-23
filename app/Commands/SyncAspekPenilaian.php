<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\Admin\AspekPenilaianController;

class SyncAspekPenilaian extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'app:sync-aspek';
    protected $description = 'Sinkronisasi 79 butir aspek standar supervisi (K13 & Kurikulum Merdeka PMM)';

    public function run(array $params)
    {
        CLI::write('Memulai sinkronisasi master aspek penilaian...', 'yellow');

        $controller = new AspekPenilaianController();
        $refMethod = new \ReflectionMethod($controller, 'getMasterAspekData');
        $refMethod->setAccessible(true);
        $masterData = $refMethod->invoke($controller);

        $jenisModel = new \App\Models\JenisPenilaianModel();
        $aspekModel = new \App\Models\AspekPenilaianModel();

        $allJenis = $jenisModel->findAll();
        $jenisMapByName = [];
        $jenisMapById = [];
        foreach ($allJenis as $j) {
            $namaKey = strtoupper(trim($j['nama'] ?? ''));
            $jenisMapByName[$namaKey] = $j['id'];
            $jenisMapById[$j['id']] = $j;
        }

        $inserted = 0;
        $updated  = 0;
        $now = date('Y-m-d H:i:s');

        foreach ($masterData as $item) {
            $targetJenisId = null;

            if (!empty($item['jenis_id']) && isset($jenisMapById[$item['jenis_id']])) {
                $targetJenisId = $item['jenis_id'];
            } elseif (!empty($item['jenis_nama'])) {
                $searchKey = strtoupper(trim($item['jenis_nama']));
                if (isset($jenisMapByName[$searchKey])) {
                    $targetJenisId = $jenisMapByName[$searchKey];
                } else {
                    foreach ($jenisMapByName as $name => $id) {
                        if (strpos($name, $searchKey) !== false || strpos($searchKey, $name) !== false) {
                            $targetJenisId = $id;
                            break;
                        }
                    }
                }
            }

            if (!$targetJenisId) {
                CLI::write("Skip: Komponen tidak ditemukan untuk '{$item['nama_aspek']}'", 'red');
                continue;
            }

            $existing = $aspekModel
                ->where('jenis_penilaian_id', $targetJenisId)
                ->where('nama_aspek', $item['nama_aspek'])
                ->first();

            $aspekPayload = [
                'jenis_penilaian_id' => $targetJenisId,
                'nama_aspek'         => $item['nama_aspek'],
                'fokus_perilaku'     => $item['fokus_perilaku'] ?? null,
                'perilaku_dianjurkan'=> $item['perilaku_dianjurkan'] ?? null,
                'perilaku_dihindari' => $item['perilaku_dihindari'] ?? null,
                'tipe_skala'         => $item['tipe_skala'] ?? 'skor_1_4',
                'urutan'             => (int) ($item['urutan'] ?? 1),
            ];

            if ($existing) {
                $aspekModel->update($existing['id'], $aspekPayload);
                $updated++;
            } else {
                $aspekPayload['created_at'] = $now;
                $aspekModel->insert($aspekPayload);
                $inserted++;
            }
        }

        CLI::write("Sinkronisasi Selesai! {$inserted} butir baru ditambahkan, {$updated} butir diperbarui.", 'green');
    }
}
