<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\TahunAjarModel;

class JadwalController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $kelasModel;
    protected $tahunAjarModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->tahunAjarModel = new TahunAjarModel();
    }

    public function index()
    {
        $userId = session()->get('id');
        
        // Get guru data by user ID
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        if (!$guru) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data guru tidak ditemukan');
        }

        // Ambil tahun ajaran aktif dan filter yang dipilih
        $activeTahun = $this->tahunAjarModel->getActive();
        $tahunAjarFilter = $this->request->getGet('tahun_ajar_id');
        if ($tahunAjarFilter === null) {
            $tahunAjarFilter = $activeTahun ? (string)$activeTahun['id'] : '';
        }
        
        // Get jadwal where guru_id matches with enriched relational data
        $query = $this->jadwalModel
            ->select('jadwal_supervisi.*, kelas.nama_kelas, kelas.fase as kelas_fase, tahun_ajar.tahun_ajar, tahun_ajar.semester, 
                      COALESCE(supervisor_guru.nama, users.username) as supervisor_name,
                      pra_observasi.status_kesepakatan as status_form_a,
                      pra_observasi.id as pra_observasi_id,
                      (SELECT COUNT(*) FROM dokumen_ajar WHERE dokumen_ajar.jadwal_id = jadwal_supervisi.id) as total_dokumen')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('guru as supervisor_guru', 'supervisor_guru.user_id = users.id', 'left')
            ->join('pra_observasi', 'pra_observasi.jadwal_id = jadwal_supervisi.id', 'left')
            ->where('jadwal_supervisi.guru_id', $guru['id']);

        if (!empty($tahunAjarFilter) && $tahunAjarFilter !== 'all') {
            $query->where('jadwal_supervisi.tahun_ajar_id', $tahunAjarFilter);
        }
        
        $jadwal = $query->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC')->findAll();
        
        $data = [
            'title'             => 'Jadwal Supervisi Guru',
            'jadwal'            => $jadwal,
            'guru'              => $guru,
            'tahun_ajars'       => $this->tahunAjarModel->orderBy('tahun_ajar', 'DESC')->orderBy('semester', 'ASC')->findAll(),
            'selectedTahunAjar' => $tahunAjarFilter,
            'activeTahun'       => $activeTahun,
            'jadwalModel'       => $this->jadwalModel,
        ];

        return view('guru/jadwal/index', $data);
    }
}
