<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\DokumenAjarModel;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;

class DokumenAjarController extends BaseController
{
    protected $dokumenModel;
    protected $jadwalModel;
    protected $guruModel;

    public function __construct()
    {
        $this->dokumenModel = new DokumenAjarModel();
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->guruModel = new GuruModel();
    }

    public function list()
    {
        $userId = session()->get('id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        if (!$guru) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data guru tidak ditemukan');
        }

        // Get all schedules
        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, kelas.nama_kelas, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->where('jadwal_supervisi.guru_id', $guru['id'])
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Daftar Dokumen Ajar',
            'jadwal' => $jadwal
        ];

        return view('guru/dokumen_ajar/list', $data);
    }

    public function index($jadwalId)
    {
        $userId = session()->get('id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        if (!$guru) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data guru tidak ditemukan');
        }

        // Verify jadwal belongs to this guru with enriched data
        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, kelas.nama_kelas, kelas.fase as kelas_fase, tahun_ajar.tahun_ajar, tahun_ajar.semester, users.username as nama_supervisor, jenis_penilaian.nama as nama_indikator')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('jenis_penilaian', 'jenis_penilaian.id = jadwal_supervisi.indikator_pmm_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.guru_id', $guru['id'])
            ->first();

        if (!$jadwal) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $dokumen = $this->dokumenModel
            ->where('jadwal_id', $jadwalId)
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Dokumen Ajar',
            'jadwal' => $jadwal,
            'dokumen' => $dokumen
        ];

        return view('guru/dokumen_ajar/index', $data);
    }

    public function save()
    {
        $userId = session()->get('id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        $jadwalId = $this->request->getPost('jadwal_id');

        // Security check
        $jadwal = $this->jadwalModel->where('id', $jadwalId)->where('guru_id', $guru['id'])->first();
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nama_dokumen'     => 'required|max_length[255]',
            'link_drive'       => 'required|valid_url',
            'kategori_dokumen' => 'permit_empty|string',
            'fase'             => 'permit_empty|in_list[A,B,C,D,E,F]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $diferensiasi = $this->request->getPost('strategi_diferensiasi');
        $diferensiasiStr = is_array($diferensiasi) ? implode(',', $diferensiasi) : $diferensiasi;

        $this->dokumenModel->save([
            'jadwal_id'             => $jadwalId,
            'nama_dokumen'          => $this->request->getPost('nama_dokumen'),
            'kategori_dokumen'      => $this->request->getPost('kategori_dokumen') ?? 'modul_ajar',
            'fase'                  => $this->request->getPost('fase'),
            'tujuan_pembelajaran'   => $this->request->getPost('tujuan_pembelajaran'),
            'strategi_diferensiasi' => $diferensiasiStr,
            'link_drive'            => $this->request->getPost('link_drive'),
            'link_eksternal'        => $this->request->getPost('link_eksternal'),
            'keterangan'            => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to(base_url('guru/dokumen-ajar/manage/' . $jadwalId))->with('success', 'Dokumen Kurikulum Merdeka berhasil disimpan.');

    }

    public function delete($id)
    {
        $userId = session()->get('id');
        $guru = $this->guruModel->where('user_id', $userId)->first();

        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Verify ownership via jadwal
        $jadwal = $this->jadwalModel->where('id', $dokumen['jadwal_id'])->where('guru_id', $guru['id'])->first();
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $this->dokumenModel->delete($id);

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
