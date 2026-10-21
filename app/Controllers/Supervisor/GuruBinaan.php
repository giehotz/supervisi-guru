<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;
use App\Models\PraObservasiModel;
use App\Models\TindakLanjutSupervisiModel;
use App\Models\JenisPenilaianModel;

class GuruBinaan extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $praModel;
    protected $tlModel;
    protected $jenisModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->guruModel   = new GuruModel();
        $this->praModel    = new PraObservasiModel();
        $this->tlModel     = new TindakLanjutSupervisiModel();
        $this->jenisModel  = new JenisPenilaianModel();
    }

    public function index()
    {
        $supervisorId = session()->get('id');

        // Ambil seluruh jadwal supervisi yang dibina supervisor ini
        $schedules = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran as guru_mapel, kelas.nama_kelas, kelas.fase as kelas_fase, jenis_penilaian.nama as nama_indikator, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('jenis_penilaian', 'jenis_penilaian.id = jadwal_supervisi.indikator_pmm_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'DESC')
            ->findAll();

        $guruBinaan = [];
        $stats = [
            'total'         => count($schedules),
            'pra_observasi' => 0,
            'observasi'     => 0,
            'tindak_lanjut' => 0,
            'selesai'       => 0,
        ];

        foreach ($schedules as $item) {
            $pra = $this->praModel->getByJadwal($item['id']);
            $tl  = $this->tlModel->getByJadwal($item['id']);

            // Status per Formulir
            $formAStatus = 'belum';
            if ($pra) {
                $formAStatus = ($pra['status_kesepakatan'] === 'disepakati') ? 'disepakati' : 'diajukan';
            }

            $formBStatus = 'belum';
            if (in_array(strtolower($item['status']), ['pasca_observasi', 'tindak_lanjut', 'selesai'])) {
                $formBStatus = 'selesai';
            } elseif ($item['status'] === 'observasi') {
                $formBStatus = 'proses';
            }

            $formCStatus = 'belum';
            if ($tl && !empty($tl['status_tindak_lanjut'])) {
                $formCStatus = ($tl['status_tindak_lanjut'] === 'form_c_disepakati' || $tl['status_tindak_lanjut'] === 'selesai' || !empty($tl['catatan_supervisor_c'])) 
                    ? 'disepakati' 
                    : 'terisi';
            }

            $formDStatus = 'belum';
            if ($tl && !empty($tl['evaluasi_d'])) {
                $formDStatus = 'selesai';
            } elseif ($tl && !empty($tl['refleksi_guru'])) {
                $formDStatus = 'terisi';
            }

            // Hitung statistik tahapan
            if (in_array(strtolower($item['status']), ['selesai'])) {
                $stats['selesai']++;
            } elseif (in_array(strtolower($item['status']), ['pasca_observasi', 'tindak_lanjut'])) {
                $stats['tindak_lanjut']++;
            } elseif ($item['status'] === 'observasi') {
                $stats['observasi']++;
            } else {
                $stats['pra_observasi']++;
            }

            $guruBinaan[] = [
                'jadwal'        => $item,
                'pra'           => $pra,
                'tl'            => $tl,
                'formA_status'  => $formAStatus,
                'formB_status'  => $formBStatus,
                'formC_status'  => $formCStatus,
                'formD_status'  => $formDStatus,
            ];
        }

        $data = [
            'title'      => 'Guru Binaan & Pantauan Siklus Kurikulum Merdeka',
            'guruBinaan' => $guruBinaan,
            'stats'      => $stats
        ];

        return view('supervisor/guru_binaan/index', $data);
    }
}
