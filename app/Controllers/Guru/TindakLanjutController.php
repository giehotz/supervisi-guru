<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;
use App\Models\HasilSupervisiModel;
use App\Models\TindakLanjutSupervisiModel;

class TindakLanjutController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $hasilModel;
    protected $tlModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->guruModel   = new GuruModel();
        $this->hasilModel  = new HasilSupervisiModel();
        $this->tlModel     = new TindakLanjutSupervisiModel();
    }

    public function index($jadwalId)
    {
        $userId = session()->get('id');
        $guru   = $this->guruModel->where('user_id', $userId)->first();

        if (!$guru) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data guru tidak ditemukan');
        }

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

        // Ambil data hasil observasi jika ada
        $hasil = $this->hasilModel->where('jadwal_supervisi_id', $jadwalId)->first();

        // Ambil data tindak lanjut Formulir C & D
        $tindakLanjut = $this->tlModel->getByJadwal($jadwalId);

        $data = [
            'title'        => 'Formulir C & D: Tindak Lanjut & Refleksi Kinerja',
            'jadwal'       => $jadwal,
            'hasil'        => $hasil,
            'tindakLanjut' => $tindakLanjut,
        ];

        return view('guru/tindak_lanjut/index', $data);
    }

    public function saveFormC()
    {
        $userId = session()->get('id');
        $guru   = $this->guruModel->where('user_id', $userId)->first();

        $jadwalId = $this->request->getPost('jadwal_id');
        $jadwal   = $this->jadwalModel->where('id', $jadwalId)->where('guru_id', $guru['id'])->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $dataSimpan = [
            'jadwal_id'               => $jadwalId,
            'refleksi_guru'           => $this->request->getPost('refleksi_guru'),
            'pilihan_belajar_pmm'     => $this->request->getPost('pilihan_belajar_pmm'),
            'pilihan_belajar_lainnya' => $this->request->getPost('pilihan_belajar_lainnya'),
            'tujuan_tindak_lanjut'    => $this->request->getPost('tujuan_tindak_lanjut'),
            'upaya_tindak_lanjut'     => $this->request->getPost('upaya_tindak_lanjut'),
            'periode_tindak_lanjut'   => $this->request->getPost('periode_tindak_lanjut'),
            'kebutuhan_dukungan'      => $this->request->getPost('kebutuhan_dukungan'),
            'detail_dukungan'         => $this->request->getPost('detail_dukungan'),
            'status_tindak_lanjut'    => 'form_c_diisi',
        ];

        $existing = $this->tlModel->getByJadwal($jadwalId);
        if ($existing) {
            $dataSimpan['id'] = $existing['id'];
        }

        $this->tlModel->save($dataSimpan);

        // Update status jadwal menjadi 'tindak_lanjut'
        $this->jadwalModel->update($jadwalId, ['status' => 'tindak_lanjut']);

        return redirect()->to(base_url('guru/tindak-lanjut/' . $jadwalId))
            ->with('success', 'Formulir C (Rencana Tindak Lanjut) berhasil disimpan dan diajukan ke Supervisor.');
    }

    public function saveFormD()
    {
        $userId = session()->get('id');
        $guru   = $this->guruModel->where('user_id', $userId)->first();

        $jadwalId = $this->request->getPost('jadwal_id');
        $jadwal   = $this->jadwalModel->where('id', $jadwalId)->where('guru_id', $guru['id'])->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $existing = $this->tlModel->getByJadwal($jadwalId);
        if (!$existing) {
            return redirect()->back()->with('error', 'Silakan isi Formulir C terlebih dahulu sebelum mengisi Formulir D.');
        }

        $dataSimpan = [
            'id'                              => $existing['id'],
            'inspirasi_baru'                  => $this->request->getPost('inspirasi_baru'),
            'perubahan_praktik'               => $this->request->getPost('perubahan_praktik'),
            'kendala_tindak_lanjut'           => $this->request->getPost('kendala_tindak_lanjut'),
            'rencana_perbaikan_berkelanjutan' => $this->request->getPost('rencana_perbaikan_berkelanjutan'),
            'status_tindak_lanjut'            => 'form_d_diisi',
        ];

        $this->tlModel->save($dataSimpan);

        return redirect()->to(base_url('guru/tindak-lanjut/' . $jadwalId))
            ->with('success', 'Formulir D (Refleksi Tindak Lanjut) berhasil disimpan dan diajukan ke Supervisor.');
    }
}
