<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\JenisPenilaianModel;
use App\Models\AspekPenilaianModel;
use App\Models\PraObservasiModel;
use App\Models\CoachingTirtaModel;
use App\Models\DokumenAjarModel;

class PraObservasiController extends BaseController
{
    protected $jadwalModel;
    protected $jenisModel;
    protected $aspekModel;
    protected $praModel;
    protected $coachingModel;
    protected $dokumenModel;

    public function __construct()
    {
        $this->jadwalModel   = new JadwalSupervisiModel();
        $this->jenisModel    = new JenisPenilaianModel();
        $this->aspekModel    = new AspekPenilaianModel();
        $this->praModel      = new PraObservasiModel();
        $this->coachingModel = new CoachingTirtaModel();
        $this->dokumenModel  = new DokumenAjarModel();
    }

    public function review($jadwalId)
    {
        $userId = session()->get('id');

        // Ambil jadwal supervisi dan pastikan supervisor berhak
        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip, kelas.nama_kelas, kelas.fase as kelas_fase, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $userId)
            ->first();

        if (!$jadwal) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Ambil data Form A yang diajukan guru
        $praObservasi = $this->praModel->getByJadwal($jadwalId);

        // Ambil indikator yang dipilih guru
        $indikatorTerpilih = null;
        $fokusList = [];
        $fokusDipilih = [];

        if ($praObservasi && !empty($praObservasi['fokus_perilaku_dipilih'])) {
            $fokusDipilih = json_decode($praObservasi['fokus_perilaku_dipilih'], true) ?: [];
        }

        $indikatorId = $jadwal['indikator_pmm_id'];
        if ($indikatorId) {
            $indikatorTerpilih = $this->jenisModel->find($indikatorId);
            if (!empty($fokusDipilih)) {
                $fokusList = $this->aspekModel
                    ->whereIn('id', $fokusDipilih)
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
            }
        }

        // Ambil dokumen ajar yang diunggah guru untuk jadwal ini
        $dokumenList = $this->dokumenModel->where('jadwal_id', $jadwalId)->findAll();

        // Ambil catatan coaching TIRTA Pra-Observasi jika ada
        $coachingLog = $this->coachingModel
            ->where('jadwal_id', $jadwalId)
            ->where('tahap', 'pra_observasi')
            ->orderBy('id', 'DESC')
            ->first();

        $data = [
            'title'             => 'Review Formulir A & Coaching TIRTA Pra-Observasi',
            'jadwal'            => $jadwal,
            'praObservasi'      => $praObservasi,
            'indikatorTerpilih' => $indikatorTerpilih,
            'fokusList'         => $fokusList,
            'dokumenList'       => $dokumenList,
            'coachingLog'       => $coachingLog,
        ];

        return view('supervisor/pra_observasi/review', $data);
    }

    public function agree($jadwalId)
    {
        $userId = session()->get('id');

        $jadwal = $this->jadwalModel
            ->where('id', $jadwalId)
            ->where('supervisor_id', $userId)
            ->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $praObservasi = $this->praModel->getByJadwal($jadwalId);
        if (!$praObservasi) {
            return redirect()->back()->with('error', 'Formulir A belum diisi oleh guru.');
        }

        $now = date('Y-m-d H:i:s');
        $catatanPra = $this->request->getPost('catatan_supervisor_pra');

        // 1. Simpan Log Coaching Alur TIRTA
        $tujuan       = $this->request->getPost('tujuan_coaching');
        $identifikasi = $this->request->getPost('identifikasi_kondisi');
        $rencanaAksi  = $this->request->getPost('rencana_aksi');
        $tanggungJwb  = $this->request->getPost('tanggung_jawab');

        $coachingData = [
            'jadwal_id'            => $jadwalId,
            'tahap'                => 'pra_observasi',
            'tujuan_coaching'      => $tujuan,
            'identifikasi_kondisi' => $identifikasi,
            'rencana_aksi'         => $rencanaAksi,
            'tanggung_jawab'       => $tanggungJwb,
        ];

        $existingCoaching = $this->coachingModel
            ->where('jadwal_id', $jadwalId)
            ->where('tahap', 'pra_observasi')
            ->first();

        if ($existingCoaching) {
            $coachingData['id'] = $existingCoaching['id'];
        }
        $this->coachingModel->save($coachingData);

        // 2. Update status Formulir A menjadi 'disepakati'
        $this->praModel->update($praObservasi['id'], [
            'catatan_supervisor_pra' => $catatanPra,
            'status_kesepakatan'     => 'disepakati',
            'disepakati_pada'        => $now,
        ]);

        // 3. Update status jadwal menjadi 'observasi' dan set tanggal pra-observasi
        $this->jadwalModel->update($jadwalId, [
            'status'                => 'observasi',
            'tanggal_pra_observasi' => $now,
        ]);

        return redirect()->to(base_url('supervisor/penilaian/form/' . $jadwalId))
            ->with('success', 'Formulir A dan Catatan Coaching TIRTA berhasil disepakati! Anda dapat langsung melanjutkan observasi kelas.');
    }
}
