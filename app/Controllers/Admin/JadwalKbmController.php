<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JadwalKbmModel;
use App\Models\TahunAjarModel;
use App\Models\KelasModel;
use App\Models\GuruModel;
use App\Models\MataPelajaranModel;

class JadwalKbmController extends BaseController
{
    protected $jadwalKbmModel;
    protected $tahunAjarModel;
    protected $kelasModel;
    protected $guruModel;
    protected $mapelModel;

    public function __construct()
    {
        $this->jadwalKbmModel = new JadwalKbmModel();
        $this->tahunAjarModel = new TahunAjarModel();
        $this->kelasModel     = new KelasModel();
        $this->guruModel      = new GuruModel();
        $this->mapelModel     = new MataPelajaranModel();
    }

    /**
     * Halaman Utama Penyusunan Jadwal KBM & Pembagian Mengajar Guru
     */
    public function index()
    {
        // 1. Parameter Filter & Tab
        $currentTab      = $this->request->getGet('tab') ?? 'kelas'; // 'kelas', 'guru', 'rekap', 'tabel'
        $tahunAjarFilter = $this->request->getGet('tahun_ajar_id');
        $activeTahun     = $this->tahunAjarModel->getActive();

        // Default ke tahun ajaran aktif saat pertama kali dibuka
        if ($tahunAjarFilter === null) {
            $tahunAjarFilter = $activeTahun ? (string)$activeTahun['id'] : '';
        }

        $tahunAjarId = ($tahunAjarFilter === 'all' || empty($tahunAjarFilter)) 
            ? ($activeTahun ? $activeTahun['id'] : null) 
            : $tahunAjarFilter;

        // 2. Master Data
        $tahunAjars = $this->tahunAjarModel->orderBy('tahun_ajar', 'DESC')->orderBy('semester', 'ASC')->findAll();
        
        $kelasesQuery = $this->kelasModel->where('status', 'Aktif');
        if ($tahunAjarId) {
            $kelasesQuery->where('tahun_ajar_id', $tahunAjarId);
        }
        $kelases = $kelasesQuery->orderBy('nama_kelas', 'ASC')->findAll();
        // Fallback jika belum ada kelas pada TA tersebut
        if (empty($kelases)) {
            $kelases = $this->kelasModel->where('status', 'Aktif')->orderBy('nama_kelas', 'ASC')->findAll();
        }

        $gurus  = $this->guruModel->orderBy('nama', 'ASC')->findAll();
        $mapels = $this->mapelModel->where('status', 'Aktif')->orderBy('kelompok', 'ASC')->orderBy('nama_mapel', 'ASC')->findAll();
        $sesiJam = JadwalKbmModel::getDaftarSesiJam();

        // 3. Selection untuk Tab Kelas & Guru
        $selectedKelasId = $this->request->getGet('kelas_id');
        if (empty($selectedKelasId) && !empty($kelases)) {
            $selectedKelasId = $kelases[0]['id'];
        }

        $selectedGuruId = $this->request->getGet('guru_id');
        if (empty($selectedGuruId) && !empty($gurus)) {
            $selectedGuruId = $gurus[0]['id'];
        }

        // 4. Data spesifik per Tab
        $matriksKelas = [];
        $selectedKelas = null;
        if ($selectedKelasId) {
            $matriksKelas  = $this->jadwalKbmModel->getMatriksJadwalKelas($tahunAjarId, $selectedKelasId);
            $selectedKelas = $this->kelasModel->find($selectedKelasId);
        }

        $matriksGuru = [];
        $selectedGuru = null;
        if ($selectedGuruId) {
            $matriksGuru  = $this->jadwalKbmModel->getMatriksJadwalGuru($tahunAjarId, $selectedGuruId);
            $selectedGuru = $this->guruModel->find($selectedGuruId);
        }

        $rekapBeban = $this->jadwalKbmModel->getRekapBebanGuru($tahunAjarId);

        // Data untuk Tab 4 (Semua Jadwal / Tabel)
        $filterTahunForList = ($tahunAjarFilter === 'all' || empty($tahunAjarFilter)) ? null : $tahunAjarFilter;
        $allJadwals = $this->jadwalKbmModel->getJadwalWithDetails($filterTahunForList);

        $data = [
            'title'           => 'Pembagian Mengajar Guru & Jadwal KBM',
            'currentTab'      => $currentTab,
            'tahun_ajars'     => $tahunAjars,
            'kelases'         => $kelases,
            'gurus'           => $gurus,
            'mapels'          => $mapels,
            'sesiJam'         => $sesiJam,
            'selectedTahunId' => $tahunAjarFilter,
            'activeTahun'     => $activeTahun,
            'tahunAjarId'     => $tahunAjarId,
            'selectedKelasId' => $selectedKelasId,
            'selectedKelas'   => $selectedKelas,
            'selectedGuruId'  => $selectedGuruId,
            'selectedGuru'    => $selectedGuru,
            'matriksKelas'    => $matriksKelas,
            'matriksGuru'     => $matriksGuru,
            'rekapBeban'      => $rekapBeban,
            'allJadwals'      => $allJadwals,
            'totalJadwal'     => count($allJadwals),
        ];

        return view('admin/akademik/mengajar/index', $data);
    }

    /**
     * Menyimpan Jadwal Mengajar Baru
     */
    public function store()
    {
        $rules = [
            'tahun_ajar_id'  => 'required|numeric',
            'guru_id'        => 'required|numeric',
            'mapel_id'       => 'required|numeric',
            'kelas_id'       => 'required|numeric',
            'hari'           => 'required|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
            'jam_mulai_ke'   => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[8]',
            'jam_selesai_ke' => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: Harap lengkapi semua kolom dengan benar.');
        }

        $tahunId    = $this->request->getPost('tahun_ajar_id');
        $guruId     = $this->request->getPost('guru_id');
        $mapelId    = $this->request->getPost('mapel_id');
        $kelasId    = $this->request->getPost('kelas_id');
        $hari       = $this->request->getPost('hari');
        $jamMulai   = (int)$this->request->getPost('jam_mulai_ke');
        $jamSelesai = (int)$this->request->getPost('jam_selesai_ke');

        if ($jamMulai > $jamSelesai) {
            return redirect()->back()->withInput()->with('error', 'Jam mulai (ke-' . $jamMulai . ') tidak boleh lebih besar dari jam selesai (ke-' . $jamSelesai . ').');
        }

        // Cek Bentrok Cerdas
        $conflict = $this->jadwalKbmModel->checkConflict($tahunId, $guruId, $kelasId, $hari, $jamMulai, $jamSelesai);
        if ($conflict) {
            return redirect()->back()->withInput()->with('error', $conflict['message']);
        }

        $this->jadwalKbmModel->insert([
            'tahun_ajar_id'  => $tahunId,
            'guru_id'        => $guruId,
            'mapel_id'       => $mapelId,
            'kelas_id'       => $kelasId,
            'hari'           => $hari,
            'jam_mulai_ke'   => $jamMulai,
            'jam_selesai_ke' => $jamSelesai,
            'status'         => 'Aktif',
        ]);

        $redirectTab = $this->request->getPost('redirect_tab') ?? 'kelas';
        return redirect()->to(base_url("admin/akademik/mengajar?tab={$redirectTab}&tahun_ajar_id={$tahunId}&kelas_id={$kelasId}&guru_id={$guruId}"))
            ->with('success', 'Plotting jadwal mengajar berhasil ditambahkan.');
    }

    /**
     * Memperbarui Jadwal Mengajar
     */
    public function update($id)
    {
        $existing = $this->jadwalKbmModel->find($id);
        if (!$existing) {
            return redirect()->back()->with('error', 'Data jadwal mengajar tidak ditemukan.');
        }

        $rules = [
            'tahun_ajar_id'  => 'required|numeric',
            'guru_id'        => 'required|numeric',
            'mapel_id'       => 'required|numeric',
            'kelas_id'       => 'required|numeric',
            'hari'           => 'required|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
            'jam_mulai_ke'   => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[8]',
            'jam_selesai_ke' => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: Harap periksa input data jadwal.');
        }

        $tahunId    = $this->request->getPost('tahun_ajar_id');
        $guruId     = $this->request->getPost('guru_id');
        $mapelId    = $this->request->getPost('mapel_id');
        $kelasId    = $this->request->getPost('kelas_id');
        $hari       = $this->request->getPost('hari');
        $jamMulai   = (int)$this->request->getPost('jam_mulai_ke');
        $jamSelesai = (int)$this->request->getPost('jam_selesai_ke');

        if ($jamMulai > $jamSelesai) {
            return redirect()->back()->withInput()->with('error', 'Jam mulai tidak boleh lebih besar dari jam selesai.');
        }

        // Cek Bentrok Cerdas (exclude ID saat ini)
        $conflict = $this->jadwalKbmModel->checkConflict($tahunId, $guruId, $kelasId, $hari, $jamMulai, $jamSelesai, $id);
        if ($conflict) {
            return redirect()->back()->withInput()->with('error', $conflict['message']);
        }

        $this->jadwalKbmModel->update($id, [
            'tahun_ajar_id'  => $tahunId,
            'guru_id'        => $guruId,
            'mapel_id'       => $mapelId,
            'kelas_id'       => $kelasId,
            'hari'           => $hari,
            'jam_mulai_ke'   => $jamMulai,
            'jam_selesai_ke' => $jamSelesai,
        ]);

        $redirectTab = $this->request->getPost('redirect_tab') ?? 'kelas';
        return redirect()->to(base_url("admin/akademik/mengajar?tab={$redirectTab}&tahun_ajar_id={$tahunId}&kelas_id={$kelasId}&guru_id={$guruId}"))
            ->with('success', 'Jadwal mengajar berhasil diperbarui.');
    }

    /**
     * Menghapus Jadwal Mengajar
     */
    public function delete($id)
    {
        $existing = $this->jadwalKbmModel->find($id);
        if (!$existing) {
            return redirect()->to(base_url('admin/akademik/mengajar'))->with('error', 'Data jadwal mengajar tidak ditemukan.');
        }

        $tahunId = $existing['tahun_ajar_id'];
        $kelasId = $existing['kelas_id'];
        $guruId  = $existing['guru_id'];
        $tab     = $this->request->getGet('tab') ?? 'kelas';

        $this->jadwalKbmModel->delete($id);

        return redirect()->to(base_url("admin/akademik/mengajar?tab={$tab}&tahun_ajar_id={$tahunId}&kelas_id={$kelasId}&guru_id={$guruId}"))
            ->with('success', 'Jadwal mengajar berhasil dihapus.');
    }

    /**
     * Cetak Jadwal Pelajaran Mingguan per Kelas (Print View)
     */
    public function cetakKelas($kelasId)
    {
        $tahunAjarId = $this->request->getGet('tahun_ajar_id');
        $activeTahun = $this->tahunAjarModel->getActive();
        if (empty($tahunAjarId)) {
            $tahunAjarId = $activeTahun ? $activeTahun['id'] : null;
        }

        $kelas = $this->kelasModel
            ->select('kelas.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_wali, guru.nip as nip_wali')
            ->join('tahun_ajar', 'tahun_ajar.id = kelas.tahun_ajar_id', 'left')
            ->join('guru', 'guru.id = kelas.wali_kelas', 'left')
            ->find($kelasId);

        if (!$kelas) {
            return redirect()->to(base_url('admin/akademik/mengajar'))->with('error', 'Kelas tidak ditemukan.');
        }

        $matriksKelas = $this->jadwalKbmModel->getMatriksJadwalKelas($tahunAjarId, $kelasId);
        $sesiJam      = JadwalKbmModel::getDaftarSesiJam();

        $data = [
            'title'        => 'Jadwal Pelajaran Kelas ' . ($kelas['nama_kelas'] ?? ''),
            'kelas'        => $kelas,
            'matriksKelas' => $matriksKelas,
            'sesiJam'      => $sesiJam,
            'tahunAjar'    => $this->tahunAjarModel->find($tahunAjarId),
        ];

        return view('admin/akademik/mengajar/cetak_kelas', $data);
    }

    /**
     * Cetak Jadwal Mengajar Mingguan per Guru (Print View)
     */
    public function cetakGuru($guruId)
    {
        $tahunAjarId = $this->request->getGet('tahun_ajar_id');
        $activeTahun = $this->tahunAjarModel->getActive();
        if (empty($tahunAjarId)) {
            $tahunAjarId = $activeTahun ? $activeTahun['id'] : null;
        }

        $guru = $this->guruModel->find($guruId);
        if (!$guru) {
            return redirect()->to(base_url('admin/akademik/mengajar'))->with('error', 'Guru tidak ditemukan.');
        }

        $matriksGuru = $this->jadwalKbmModel->getMatriksJadwalGuru($tahunAjarId, $guruId);
        $sesiJam     = JadwalKbmModel::getDaftarSesiJam();

        // Hitung total jam mengajar guru ini
        $totalJtm = 0;
        foreach ($matriksGuru as $hari => $jamSlots) {
            foreach ($jamSlots as $slot) {
                if ($slot && !empty($slot['is_start'])) {
                    $totalJtm += $slot['durasi'];
                }
            }
        }

        $data = [
            'title'       => 'Jadwal Mengajar - ' . ($guru['nama'] ?? ''),
            'guru'        => $guru,
            'matriksGuru' => $matriksGuru,
            'sesiJam'     => $sesiJam,
            'totalJtm'    => $totalJtm,
            'tahunAjar'   => $this->tahunAjarModel->find($tahunAjarId),
        ];

        return view('admin/akademik/mengajar/cetak_guru', $data);
    }
}
