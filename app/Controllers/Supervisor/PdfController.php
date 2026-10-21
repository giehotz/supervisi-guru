<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\DetailHasilPenilaianModel;
use App\Models\GuruModel;
use App\Models\FotoBuktiModel;
use App\Models\UserModel;

// Include Dompdf library
require_once APPPATH . '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfController extends BaseController
{
    protected $jadwalModel;
    protected $hasilModel;
    protected $detailModel;
    protected $guruModel;
    protected $fotoModel;
    protected $userModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->detailModel = new DetailHasilPenilaianModel();
        $this->guruModel = new GuruModel();
        $this->fotoModel = new FotoBuktiModel();
        $this->userModel = new UserModel();
    }

    public function cetak($jadwalId)
    {
        $supervisorId = session()->get('id');

        // Get schedule details
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran, guru.status_kepegawaian, users.username as nama_supervisor, users.nip as nip_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil supervisi tidak ditemukan');
        }

        // Get all assessment results for this schedule
        $hasilList = $this->hasilModel
            ->select('hasil_supervisi.*, jenis_penilaian.nama as nama_jenis')
            ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
            ->where('jadwal_supervisi_id', $jadwalId)
            ->findAll();
            
        if (empty($hasilList)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data hasil tidak ditemukan');
        }
        
        // Group detail results by jenis_penilaian_id
        $detailResults = [];
        foreach ($hasilList as $hasil) {
            $details = $this->detailModel
                ->select('detail_hasil_penilaian.*, aspek_penilaian.nama_aspek')
                ->join('aspek_penilaian', 'aspek_penilaian.id = detail_hasil_penilaian.aspek_penilaian_id', 'left')
                ->where('detail_hasil_penilaian.hasil_supervisi_id', $hasil['id'])
                ->findAll();
                
            $detailResults[$hasil['jenis_penilaian_id']] = $details;
        }

        // Get photo evidence
        $fotoBukti = $this->fotoModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get kepala sekolah data
        $kepalaSekolah = $this->userModel
            ->select('username, nip')
            ->where('role', 'kepala')
            ->first();
            
        // Set default values
        $schedule['nama_kepala'] = 'Kepala Sekolah';
        $schedule['nip_kepala'] = '';
        
        // Set nama dan nip kepala sekolah
        if ($kepalaSekolah) {
            $schedule['nama_kepala'] = $kepalaSekolah['username'] ?? 'Kepala Sekolah';
            $schedule['nip_kepala'] = $kepalaSekolah['nip'] ?? '';
        }

        // Ensure supervisor data is properly set
        if (empty($schedule['nama_supervisor'])) {
            // Coba cari supervisor berdasarkan ID
            $supervisor = $this->userModel
                ->select('username, nip')
                ->where('id', $supervisorId)
                ->first();
                
            if ($supervisor) {
                $schedule['nama_supervisor'] = $supervisor['username'] ?? 'Supervisor';
                $schedule['nip_supervisor'] = $supervisor['nip'] ?? '';
            } else {
                $schedule['nama_supervisor'] = 'Supervisor';
                $schedule['nip_supervisor'] = '';
            }
        }
        
        // Pastikan NIP supervisor selalu ada
        if (!isset($schedule['nip_supervisor']) || empty($schedule['nip_supervisor'])) {
            $schedule['nip_supervisor'] = '';
        }

        $data = [
            'schedule' => $schedule,
            'hasilList' => $hasilList,
            'detailResults' => $detailResults,
            'fotoBukti' => $fotoBukti
        ];

        // Clear any previous output
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Render view khusus PDF
        $html = view('supervisor/hasil/pdf_view', $data);

        // Generate PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Stream the PDF directly to the browser
        $dompdf->stream("hasil_supervisi_" . $schedule['nama_guru'] . ".pdf", ['Attachment' => false]);
        
        // Exit to prevent any additional output
        exit();
    }

    public function cetakKurmer($jadwalId)
    {
        $supervisorId = session()->get('id');

        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, users.username as nama_supervisor, users.nip as nip_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan');
        }

        // Ambil data Form A dan Form C & D
        $praModel   = new \App\Models\PraObservasiModel();
        $tlModel    = new \App\Models\TindakLanjutSupervisiModel();
        $jenisModel = new \App\Models\JenisPenilaianModel();
        $aspekModel = new \App\Models\AspekPenilaianModel();

        $praObservasi = $praModel->getByJadwal($jadwalId);
        $tindakLanjut = $tlModel->getByJadwal($jadwalId);

        $fokusList = [];
        $indikatorNama = 'Praktik Kinerja Pembelajaran (PMM)';
        if (!empty($schedule['indikator_pmm_id'])) {
            $ind = $jenisModel->find($schedule['indikator_pmm_id']);
            if ($ind) {
                $indikatorNama = $ind['nama'];
            }
        }

        if ($praObservasi && !empty($praObservasi['fokus_perilaku_dipilih'])) {
            $fokusIds = json_decode($praObservasi['fokus_perilaku_dipilih'], true) ?: [];
            if (!empty($fokusIds)) {
                $fokusList = $aspekModel->whereIn('id', $fokusIds)->findAll();
            }
        }

        // Ambil detail hasil observasi jika ada
        $hasil = $this->hasilModel->where('jadwal_supervisi_id', $jadwalId)->first();
        $detailsByAspek = [];
        if ($hasil) {
            $details = $this->detailModel->where('hasil_supervisi_id', $hasil['id'])->findAll();
            foreach ($details as $d) {
                $detailsByAspek[$d['aspek_penilaian_id']] = $d;
            }
        }

        $data = [
            'schedule'       => $schedule,
            'praObservasi'   => $praObservasi,
            'tindakLanjut'   => $tindakLanjut,
            'fokusList'      => $fokusList,
            'indikatorNama'  => $indikatorNama,
            'hasilUtama'     => $hasil,
            'detailsByAspek' => $detailsByAspek,
        ];

        while (ob_get_level()) {
            ob_end_clean();
        }

        $html = view('pdf/formulir_abcd_pmm', $data);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("Formulir_ABCD_PMM_" . str_replace(' ', '_', $schedule['nama_guru']) . ".pdf", ['Attachment' => false]);
        exit();
    }

    public function cetakTelaah($jadwalId, $jenisId = null)
    {
        $supervisorId = session()->get('id');

        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran as guru_mata_pelajaran, kelas.nama_kelas, kelas.fase as kelas_fase, users.username as nama_supervisor, users.nip as nip_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan atau akses ditolak');
        }

        $jenisModel = new \App\Models\JenisPenilaianModel();
        $aspekModel = new \App\Models\AspekPenilaianModel();

        // Cari jenis penilaian telaah (13 = ATP, 14 = Modul Ajar)
        if (!$jenisId) {
            $hasilTelaah = $this->hasilModel
                ->where('jadwal_supervisi_id', $jadwalId)
                ->whereIn('jenis_penilaian_id', [13, 14])
                ->first();
            $jenisId = $hasilTelaah ? $hasilTelaah['jenis_penilaian_id'] : 14;
        }

        $jenisPenilaian = $jenisModel->find($jenisId);
        if (!$jenisPenilaian) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Instrumen telaah tidak ditemukan');
        }

        $aspekList = $aspekModel->where('jenis_penilaian_id', $jenisId)->orderBy('urutan', 'ASC')->findAll();

        $hasil = $this->hasilModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->where('jenis_penilaian_id', $jenisId)
            ->first();

        $detailResults = [];
        if ($hasil) {
            $details = $this->detailModel->where('hasil_supervisi_id', $hasil['id'])->findAll();
            foreach ($details as $d) {
                $detailResults[$d['aspek_penilaian_id']] = $d;
            }
        }

        $data = [
            'schedule'       => $schedule,
            'jenisPenilaian' => $jenisPenilaian,
            'aspekList'      => $aspekList,
            'hasil'          => $hasil,
            'detailResults'  => $detailResults
        ];

        while (ob_get_level()) {
            ob_end_clean();
        }

        $html = view('pdf/telaah_modul_ajar', $data);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Telaah_" . str_replace(' ', '_', $jenisPenilaian['nama']) . "_" . str_replace(' ', '_', $schedule['nama_guru']) . ".pdf";
        $dompdf->stream($filename, ['Attachment' => false]);
        exit();
    }

    public function cetakCoaching($jadwalId)
    {
        $supervisorId = session()->get('id');

        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran as guru_mata_pelajaran, kelas.nama_kelas, kelas.fase as kelas_fase, users.username as nama_supervisor, users.nip as nip_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan atau akses ditolak');
        }

        $coachingModel = new \App\Models\CoachingTirtaModel();
        $praModel      = new \App\Models\PraObservasiModel();
        $tlModel       = new \App\Models\TindakLanjutSupervisiModel();

        $coachingList = $coachingModel->where('jadwal_id', $jadwalId)->orderBy('id', 'ASC')->findAll();
        $praObservasi = $praModel->getByJadwal($jadwalId);
        $tindakLanjut = $tlModel->getByJadwal($jadwalId);

        $data = [
            'schedule'     => $schedule,
            'coachingList' => $coachingList,
            'praObservasi' => $praObservasi,
            'tindakLanjut' => $tindakLanjut,
        ];

        while (ob_get_level()) {
            ob_end_clean();
        }

        $html = view('pdf/lembar_coaching_tirta', $data);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Berita_Acara_Coaching_TIRTA_" . str_replace(' ', '_', $schedule['nama_guru']) . ".pdf";
        $dompdf->stream($filename, ['Attachment' => false]);
        exit();
    }
}