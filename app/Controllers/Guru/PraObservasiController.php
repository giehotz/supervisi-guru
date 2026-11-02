<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;
use App\Models\JenisPenilaianModel;
use App\Models\AspekPenilaianModel;
use App\Models\PraObservasiModel;
use App\Models\DokumenAjarModel;

class PraObservasiController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $jenisModel;
    protected $aspekModel;
    protected $praModel;
    protected $dokumenModel;

    public function __construct()
    {
        $this->jadwalModel  = new JadwalSupervisiModel();
        $this->guruModel    = new GuruModel();
        $this->jenisModel   = new JenisPenilaianModel();
        $this->aspekModel   = new AspekPenilaianModel();
        $this->praModel     = new PraObservasiModel();
        $this->dokumenModel = new DokumenAjarModel();
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
            ->select('jadwal_supervisi.*, kelas.nama_kelas, kelas.fase as kelas_fase, tahun_ajar.tahun_ajar, tahun_ajar.semester, 
                      COALESCE(supervisor_guru.nama, users.username) as supervisor_name')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('guru as supervisor_guru', 'supervisor_guru.user_id = users.id', 'left')
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

        // Ambil dokumen perangkat ajar yang telah diunggah untuk jadwal ini
        $dokumenList = $this->dokumenModel
            ->where('jadwal_id', $jadwalId)
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [
            'title'              => 'Formulir A: Diskusi Persiapan Observasi Kinerja',
            'jadwal'             => $jadwal,
            'indikatorPmm'       => $indikatorPmm,
            'fokusPerIndikator'  => $fokusPerIndikator,
            'praObservasi'       => $praObservasi,
            'fokusDipilih'       => $fokusDipilih,
            'dokumenList'        => $dokumenList,
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

        $existing = $this->praModel->getByJadwal($jadwalId);
        if ($existing && $existing['status_kesepakatan'] === 'disepakati') {
            return redirect()->back()->with('error', 'Formulir A telah disepakati bersama supervisor dan tidak dapat diubah lagi.');
        }

        $aksi = $this->request->getPost('aksi'); // 'draft' atau 'ajukan'
        $indikatorId  = (int)$this->request->getPost('indikator_pmm_id');
        $fokusDipilih = $this->request->getPost('fokus_perilaku_dipilih');
        if (!is_array($fokusDipilih)) {
            $fokusDipilih = [];
        }

        if ($aksi === 'ajukan') {
            // Validasi ketat saat Pengajuan ke Supervisor
            if (empty($indikatorId)) {
                return redirect()->back()->withInput()->with('error', 'Pilihlah 1 target Indikator Kinerja PMM.');
            }

            $countFokus = count($fokusDipilih);
            if ($countFokus < 1 || $countFokus > 3) {
                return redirect()->back()->withInput()->with('error', 'Pilih minimal 1 dan maksimal 3 fokus perilaku yang ingin diobservasi.');
            }

            // Validasi bahwa fokus perilaku yang dipilih benar-benar milik indikator terpilih
            $validAspeks = $this->aspekModel
                ->where('jenis_penilaian_id', $indikatorId)
                ->whereIn('id', $fokusDipilih)
                ->findAll();

            if (count($validAspeks) !== $countFokus) {
                return redirect()->back()->withInput()->with('error', 'Fokus perilaku yang dipilih harus sesuai dengan indikator kinerja yang Anda pilih.');
            }

            // Validasi isian narasi esensial
            $upayaMempelajari = trim((string)$this->request->getPost('upaya_mempelajari'));
            $tujuanSesi       = trim((string)$this->request->getPost('tujuan_pembelajaran_sesi'));

            if ($upayaMempelajari === '' || $tujuanSesi === '') {
                return redirect()->back()->withInput()->with('error', 'Harap isi Upaya Mempelajari Target Perilaku dan Tujuan Pembelajaran Sesi sebelum mengajukan.');
            }

            $statusKesepakatan = 'diajukan';
        } else {
            // Mode Draf: Lebih fleksibel
            if (!empty($indikatorId) && !empty($fokusDipilih)) {
                // Filter hanya fokus milik indikator terpilih jika ada
                $validAspeks = $this->aspekModel
                    ->where('jenis_penilaian_id', $indikatorId)
                    ->whereIn('id', $fokusDipilih)
                    ->findAll();
                $fokusDipilih = array_column($validAspeks, 'id');
                if (count($fokusDipilih) > 3) {
                    $fokusDipilih = array_slice($fokusDipilih, 0, 3);
                }
            }
            $statusKesepakatan = 'draft_guru';
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

    /**
     * Menarik kembali pengajuan Formulir A agar dapat diedit kembali oleh guru (sebelum disepakati supervisor)
     */
    public function tarik($jadwalId)
    {
        $userId = session()->get('id');
        $guru   = $this->guruModel->where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $jadwal = $this->jadwalModel->where('id', $jadwalId)->where('guru_id', $guru['id'])->first();
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal supervisi tidak ditemukan.');
        }

        $praObservasi = $this->praModel->getByJadwal($jadwalId);
        if (!$praObservasi) {
            return redirect()->back()->with('error', 'Data Formulir A tidak ditemukan.');
        }

        if ($praObservasi['status_kesepakatan'] === 'disepakati') {
            return redirect()->back()->with('error', 'Formulir A telah disepakati bersama supervisor dan tidak dapat ditarik kembali.');
        }

        if ($praObservasi['status_kesepakatan'] !== 'diajukan') {
            return redirect()->back()->with('info', 'Formulir A saat ini tidak dalam status diajukan.');
        }

        $this->praModel->update($praObservasi['id'], [
            'status_kesepakatan' => 'draft_guru',
        ]);

        return redirect()->to(base_url('guru/pra-observasi/' . $jadwalId))
            ->with('success', 'Pengajuan Formulir A berhasil ditarik kembali. Anda dapat mengedit dan melengkapi draf kembali.');
    }
}
