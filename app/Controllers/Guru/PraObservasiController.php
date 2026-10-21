<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;
use App\Models\JenisPenilaianModel;
use App\Models\AspekPenilaianModel;
use App\Models\PraObservasiModel;

class PraObservasiController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $jenisModel;
    protected $aspekModel;
    protected $praModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->guruModel   = new GuruModel();
        $this->jenisModel  = new JenisPenilaianModel();
        $this->aspekModel  = new AspekPenilaianModel();
        $this->praModel    = new PraObservasiModel();
    }

    public function index($jadwalId)
    {
        $userId = session()->get('id');
        $guru   = $this->guruModel->where('user_id', $userId)->first();

        if (!$guru) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data guru tidak ditemukan');
        }

        // Ambil jadwal supervisi dan verifikasi hak akses
        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, kelas.nama_kelas, kelas.fase as kelas_fase, tahun_ajar.tahun_ajar, tahun_ajar.semester, users.username as supervisor_name')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.guru_id', $guru['id'])
            ->first();

        if (!$jadwal) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Ambil 8 indikator praktik kinerja PMM
        $indikatorPmm = $this->jenisModel
            ->where('kategori_supervisi', 'observasi_kelas_pmm')
            ->orderBy('id', 'ASC')
            ->findAll();

        // Ambil seluruh aspek/fokus perilaku untuk indikator PMM
        $fokusList = [];
        if (!empty($indikatorPmm)) {
            $jenisIds = array_column($indikatorPmm, 'id');
            $fokusList = $this->aspekModel
                ->whereIn('jenis_penilaian_id', $jenisIds)
                ->orderBy('urutan', 'ASC')
                ->findAll();
        }

        // Kelompokkan fokus perilaku berdasarkan jenis_penilaian_id
        $fokusPerIndikator = [];
        foreach ($fokusList as $fokus) {
            $fokusPerIndikator[$fokus['jenis_penilaian_id']][] = $fokus;
        }

        // Ambil data Form A yang sudah pernah disimpan jika ada
        $praObservasi = $this->praModel->getByJadwal($jadwalId);
        $fokusDipilih = [];
        if ($praObservasi && !empty($praObservasi['fokus_perilaku_dipilih'])) {
            $decoded = json_decode($praObservasi['fokus_perilaku_dipilih'], true);
            $fokusDipilih = is_array($decoded) ? $decoded : [];
        }

        $data = [
            'title'              => 'Formulir A: Diskusi Persiapan Observasi Kinerja',
            'jadwal'             => $jadwal,
            'indikatorPmm'       => $indikatorPmm,
            'fokusPerIndikator'  => $fokusPerIndikator,
            'praObservasi'       => $praObservasi,
            'fokusDipilih'       => $fokusDipilih,
        ];

        return view('guru/pra_observasi/form', $data);
    }

    public function save()
    {
        $userId = session()->get('id');
        $guru   = $this->guruModel->where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $jadwalId = $this->request->getPost('jadwal_id');
        $jadwal   = $this->jadwalModel->where('id', $jadwalId)->where('guru_id', $guru['id'])->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal supervisi tidak ditemukan.');
        }

        $fokusDipilih = $this->request->getPost('fokus_perilaku_dipilih');
        if (empty($fokusDipilih) || !is_array($fokusDipilih)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal 1 fokus perilaku yang ingin diobservasi.');
        }

        $aksi = $this->request->getPost('aksi'); // 'draft' atau 'ajukan'
        $statusKesepakatan = ($aksi === 'ajukan') ? 'diajukan' : 'draft_guru';

        $indikatorId = $this->request->getPost('indikator_pmm_id');
        if (empty($indikatorId) && !empty($fokusDipilih)) {
            $firstAspek = $this->aspekModel->find($fokusDipilih[0]);
            if ($firstAspek) {
                $indikatorId = $firstAspek['jenis_penilaian_id'];
            }
        }

        $dataSimpan = [
            'jadwal_id'                => $jadwalId,
            'fokus_perilaku_dipilih'   => json_encode(array_values(array_map('intval', $fokusDipilih))),
            'upaya_mempelajari'        => $this->request->getPost('upaya_mempelajari'),
            'tujuan_pembelajaran_sesi' => $this->request->getPost('tujuan_pembelajaran_sesi'),
            'area_pengembangan'        => $this->request->getPost('area_pengembangan'),
            'strategi_persiapan'       => $this->request->getPost('strategi_persiapan'),
            'status_kesepakatan'       => $statusKesepakatan,
        ];

        $existing = $this->praModel->getByJadwal($jadwalId);
        if ($existing) {
            $dataSimpan['id'] = $existing['id'];
        }

        $this->praModel->save($dataSimpan);

        // Update indikator_pmm_id dan status jadwal
        $updateJadwal = [];
        if ($indikatorId) {
            $updateJadwal['indikator_pmm_id'] = $indikatorId;
        }
        if ($aksi === 'ajukan' && ($jadwal['status'] === 'Terjadwal' || $jadwal['status'] === 'dijadwalkan')) {
            $updateJadwal['status'] = 'pra_observasi';
        }
        if (!empty($updateJadwal)) {
            $this->jadwalModel->update($jadwalId, $updateJadwal);
        }

        $msg = ($aksi === 'ajukan') 
            ? 'Formulir A berhasil diajukan ke Supervisor untuk ditinjau dan disepakati.' 
            : 'Draft Formulir A berhasil disimpan.';

        return redirect()->to(base_url('guru/pra-observasi/' . $jadwalId))->with('success', $msg);
    }
}
