<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjarModel;
use App\Models\KelasModel;
use App\Models\GuruModel;
use App\Models\MataPelajaranModel;
use App\Models\GuruMengajarModel;

class AkademikController extends BaseController
{
    protected $tahunAjarModel;
    protected $kelasModel;
    protected $guruModel;
    protected $mapelModel;
    protected $mengajarModel;

    public function __construct()
    {
        $this->tahunAjarModel = new TahunAjarModel();
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();
        $this->mapelModel = new MataPelajaranModel();
        $this->mengajarModel = new GuruMengajarModel();
    }

    public function tahunAjar()
    {
        return redirect()->to('/admin/pengaturan/tahun-ajar');
    }

    // ==========================================
    // 1. MATA PELAJARAN (MAPEL)
    // ==========================================

    public function mapel()
    {
        $mapels = $this->mapelModel->orderBy('kelompok', 'ASC')->orderBy('nama_mapel', 'ASC')->findAll();

        $totalMapel = count($mapels);
        $totalAktif = count(array_filter($mapels, fn($m) => $m['status'] === 'Aktif'));
        $totalKeagamaan = count(array_filter($mapels, fn($m) => $m['kelompok'] === 'Keagamaan'));
        $totalUmum = count(array_filter($mapels, fn($m) => $m['kelompok'] === 'Umum'));

        $data = [
            'title'          => 'Master Mata Pelajaran',
            'mapels'         => $mapels,
            'totalMapel'     => $totalMapel,
            'totalAktif'     => $totalAktif,
            'totalKeagamaan' => $totalKeagamaan,
            'totalUmum'      => $totalUmum,
        ];

        return view('admin/akademik/mapel/index', $data);
    }

    public function createMapel()
    {
        $rules = [
            'nama_mapel' => 'required|min_length[2]|max_length[100]',
            'kelompok'   => 'required|in_list[Umum,Keagamaan,Muatan Lokal,Peminatan]',
            'status'     => 'required|in_list[Aktif,Nonaktif]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: Periksa input nama dan kelompok mata pelajaran.');
        }

        $kode = trim($this->request->getPost('kode_mapel') ?? '');
        if (empty($kode)) {
            $clean = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $this->request->getPost('nama_mapel')));
            $kode = substr($clean, 0, 5);
        }

        $this->mapelModel->insert([
            'kode_mapel' => strtoupper($kode),
            'nama_mapel' => trim($this->request->getPost('nama_mapel')),
            'kelompok'   => $this->request->getPost('kelompok'),
            'status'     => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/akademik/mapel')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function updateMapel($id)
    {
        $mapel = $this->mapelModel->find($id);
        if (!$mapel) {
            return redirect()->to('/admin/akademik/mapel')->with('error', 'Mata pelajaran tidak ditemukan.');
        }

        $rules = [
            'nama_mapel' => 'required|min_length[2]|max_length[100]',
            'kelompok'   => 'required|in_list[Umum,Keagamaan,Muatan Lokal,Peminatan]',
            'status'     => 'required|in_list[Aktif,Nonaktif]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data yang diisi tidak valid.');
        }

        $kode = trim($this->request->getPost('kode_mapel') ?? '');
        if (empty($kode)) {
            $kode = $mapel['kode_mapel'];
        }

        $this->mapelModel->update($id, [
            'kode_mapel' => strtoupper($kode),
            'nama_mapel' => trim($this->request->getPost('nama_mapel')),
            'kelompok'   => $this->request->getPost('kelompok'),
            'status'     => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/akademik/mapel')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function deleteMapel($id)
    {
        $mapel = $this->mapelModel->find($id);
        if (!$mapel) {
            return redirect()->to('/admin/akademik/mapel')->with('error', 'Mata pelajaran tidak ditemukan.');
        }

        // Cek keterikatan di guru_mengajar
        $terpakai = $this->mengajarModel->where('mapel_id', $id)->countAllResults();
        if ($terpakai > 0) {
            return redirect()->to('/admin/akademik/mapel')->with('error', 'Mata pelajaran tidak dapat dihapus karena masih digunakan pada ' . $terpakai . ' data pembagian mengajar guru.');
        }

        $this->mapelModel->delete($id);
        return redirect()->to('/admin/akademik/mapel')->with('success', 'Mata pelajaran "' . esc($mapel['nama_mapel']) . '" berhasil dihapus.');
    }

    // ==========================================
    // 2. KELAS & ROMBEL
    // ==========================================

    public function kelas()
    {
        $tahunAjarFilter = $this->request->getGet('tahun_ajar_id');

        $query = $this->kelasModel
            ->select('kelas.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_wali')
            ->join('tahun_ajar', 'tahun_ajar.id = kelas.tahun_ajar_id', 'left')
            ->join('guru', 'guru.id = kelas.wali_kelas', 'left');

        if (!empty($tahunAjarFilter)) {
            $query->where('kelas.tahun_ajar_id', $tahunAjarFilter);
        }

        $kelases = $query->orderBy('kelas.nama_kelas', 'ASC')->findAll();

        $totalKelas = count($kelases);
        $totalAktif = count(array_filter($kelases, fn($k) => $k['status'] === 'Aktif'));

        $data = [
            'title'            => 'Manajemen Kelas & Rombel',
            'kelases'          => $kelases,
            'tahun_ajars'      => $this->tahunAjarModel->orderBy('tahun_ajar', 'DESC')->findAll(),
            'gurus'            => $this->guruModel->orderBy('nama', 'ASC')->findAll(),
            'totalKelas'       => $totalKelas,
            'totalAktif'       => $totalAktif,
            'selectedTahunId'  => $tahunAjarFilter,
        ];

        return view('admin/akademik/kelas', $data);
    }

    public function createKelas()
    {
        $rules = [
            'nama_kelas'    => 'required|min_length[1]|max_length[20]',
            'tahun_ajar_id' => 'required|numeric',
            'status'        => 'required|in_list[Aktif,Nonaktif]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Input data kelas tidak lengkap.');
        }

        $wali = $this->request->getPost('wali_kelas');
        $this->kelasModel->insert([
            'nama_kelas'    => trim($this->request->getPost('nama_kelas')),
            'tahun_ajar_id' => $this->request->getPost('tahun_ajar_id'),
            'wali_kelas'    => !empty($wali) ? $wali : null,
            'status'        => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/akademik/kelas')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function editKelas($id)
    {
        $data['kelas'] = $this->kelasModel->find($id);

        if (!$data['kelas']) {
            return redirect()->to('/admin/akademik/kelas')->with('error', 'Kelas tidak ditemukan');
        }

        $data['title']       = 'Edit Kelas';
        $data['tahun_ajars'] = $this->tahunAjarModel->findAll();
        $data['gurus']       = $this->guruModel->orderBy('nama', 'ASC')->findAll();

        return view('admin/akademik/edit_kelas', $data);
    }

    public function updateKelas($id)
    {
        $kelas = $this->kelasModel->find($id);
        if (!$kelas) {
            return redirect()->to('/admin/akademik/kelas')->with('error', 'Kelas tidak ditemukan.');
        }

        $rules = [
            'nama_kelas'    => 'required|min_length[1]|max_length[20]',
            'tahun_ajar_id' => 'required|numeric',
            'status'        => 'required|in_list[Aktif,Nonaktif]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data yang diisi tidak valid.');
        }

        $wali = $this->request->getPost('wali_kelas');
        $this->kelasModel->update($id, [
            'nama_kelas'    => trim($this->request->getPost('nama_kelas')),
            'tahun_ajar_id' => $this->request->getPost('tahun_ajar_id'),
            'wali_kelas'    => !empty($wali) ? $wali : null,
            'status'        => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/akademik/kelas')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function deleteKelas($id)
    {
        $kelas = $this->kelasModel->find($id);
        if (!$kelas) {
            return redirect()->to('/admin/akademik/kelas')->with('error', 'Kelas tidak ditemukan.');
        }

        // Cek apakah ada jadwal supervisi yang merujuk kelas_id ini
        $db = \Config\Database::connect();
        $hasJadwal = $db->table('jadwal_supervisi')->where('kelas_id', $id)->countAllResults();
        if ($hasJadwal > 0) {
            return redirect()->to('/admin/akademik/kelas')->with('error', 'Kelas tidak dapat dihapus karena tercatat pada ' . $hasJadwal . ' riwayat jadwal supervisi.');
        }

        // Cek di pembagian mengajar
        $hasMengajar = $this->mengajarModel->where('kelas_id', $id)->countAllResults();
        if ($hasMengajar > 0) {
            return redirect()->to('/admin/akademik/kelas')->with('error', 'Kelas tidak dapat dihapus karena masih digunakan dalam pembagian mengajar guru.');
        }

        $this->kelasModel->delete($id);
        return redirect()->to('/admin/akademik/kelas')->with('success', 'Kelas "' . esc($kelas['nama_kelas']) . '" berhasil dihapus.');
    }

    // ==========================================
    // 3. PEMBAGIAN MENGAJAR (JADWAL KBM)
    // ==========================================

    public function mengajar()
    {
        $tahunAjarFilter = $this->request->getGet('tahun_ajar_id');
        $kelasFilter     = $this->request->getGet('kelas_id');

        // Default tahun ajar aktif jika belum dipilih
        if (empty($tahunAjarFilter)) {
            $aktifTahun = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
            if ($aktifTahun) {
                $tahunAjarFilter = $aktifTahun['id'];
            }
        }

        $jadwals = $this->mengajarModel->getJadwalWithDetails($tahunAjarFilter, $kelasFilter);

        $data = [
            'title'           => 'Pembagian Mengajar Guru (Jadwal KBM)',
            'jadwals'         => $jadwals,
            'tahun_ajars'     => $this->tahunAjarModel->orderBy('tahun_ajar', 'DESC')->findAll(),
            'kelases'         => $this->kelasModel->where('status', 'Aktif')->orderBy('nama_kelas', 'ASC')->findAll(),
            'gurus'           => $this->guruModel->orderBy('nama', 'ASC')->findAll(),
            'mapels'          => $this->mapelModel->where('status', 'Aktif')->orderBy('nama_mapel', 'ASC')->findAll(),
            'selectedTahunId' => $tahunAjarFilter,
            'selectedKelasId' => $kelasFilter,
            'totalPlotting'   => count($jadwals),
        ];

        return view('admin/akademik/mengajar/index', $data);
    }

    public function createMengajar()
    {
        $rules = [
            'tahun_ajar_id'  => 'required|numeric',
            'guru_id'        => 'required|numeric',
            'mapel_id'       => 'required|numeric',
            'kelas_id'       => 'required|numeric',
            'hari'           => 'required|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
            'jam_mulai_ke'   => 'required|numeric',
            'jam_selesai_ke' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Harap lengkapi semua kolom isian pembagian mengajar.');
        }

        $jamMulai = (int) $this->request->getPost('jam_mulai_ke');
        $jamSelesai = (int) $this->request->getPost('jam_selesai_ke');

        if ($jamMulai > $jamSelesai) {
            return redirect()->back()->withInput()->with('error', 'Jam mulai tidak boleh lebih besar dari jam selesai.');
        }

        $tahunId = $this->request->getPost('tahun_ajar_id');
        $guruId  = $this->request->getPost('guru_id');
        $kelasId = $this->request->getPost('kelas_id');
        $hari    = $this->request->getPost('hari');

        // Validasi bentrok guru pada hari dan jam yang sama di kelas lain
        $bentrokGuru = $this->mengajarModel
            ->where('tahun_ajar_id', $tahunId)
            ->where('guru_id', $guruId)
            ->where('hari', $hari)
            ->where('jam_mulai_ke <=', $jamSelesai)
            ->where('jam_selesai_ke >=', $jamMulai)
            ->first();

        if ($bentrokGuru) {
            return redirect()->back()->withInput()->with('error', 'Jadwal bentrok: Guru tersebut sudah memiliki jadwal mengajar pada hari ' . $hari . ' sesi jam ke-' . $bentrokGuru['jam_mulai_ke'] . ' s/d ' . $bentrokGuru['jam_selesai_ke'] . '.');
        }

        // Validasi bentrok kelas pada hari dan jam yang sama dengan guru lain
        $bentrokKelas = $this->mengajarModel
            ->where('tahun_ajar_id', $tahunId)
            ->where('kelas_id', $kelasId)
            ->where('hari', $hari)
            ->where('jam_mulai_ke <=', $jamSelesai)
            ->where('jam_selesai_ke >=', $jamMulai)
            ->first();

        if ($bentrokKelas) {
            return redirect()->back()->withInput()->with('error', 'Jadwal bentrok: Kelas tersebut sudah memiliki kegiatan belajar mengajar pada hari ' . $hari . ' sesi jam ke-' . $bentrokKelas['jam_mulai_ke'] . ' s/d ' . $bentrokKelas['jam_selesai_ke'] . '.');
        }

        $this->mengajarModel->insert([
            'tahun_ajar_id'  => $tahunId,
            'guru_id'        => $guruId,
            'mapel_id'       => $this->request->getPost('mapel_id'),
            'kelas_id'       => $kelasId,
            'hari'           => $hari,
            'jam_mulai_ke'   => $jamMulai,
            'jam_selesai_ke' => $jamSelesai,
            'status'         => 'Aktif',
        ]);

        return redirect()->to('/admin/akademik/mengajar?tahun_ajar_id=' . $tahunId . '&kelas_id=' . $kelasId)
            ->with('success', 'Pembagian mengajar berhasil disimpan.');
    }

    public function deleteMengajar($id)
    {
        $item = $this->mengajarModel->find($id);
        if (!$item) {
            return redirect()->to('/admin/akademik/mengajar')->with('error', 'Data pembagian mengajar tidak ditemukan.');
        }

        $tahunId = $item['tahun_ajar_id'];
        $kelasId = $item['kelas_id'];

        $this->mengajarModel->delete($id);
        return redirect()->to('/admin/akademik/mengajar?tahun_ajar_id=' . $tahunId . '&kelas_id=' . $kelasId)
            ->with('success', 'Jadwal mengajar berhasil dihapus.');
    }
}
