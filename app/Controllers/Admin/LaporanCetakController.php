<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\TahunAjarModel;
use App\Models\GuruModel;
use App\Models\JenisPenilaianModel;
use App\Models\AspekPenilaianModel;
use App\Models\DetailHasilPenilaianModel;
use App\Models\PraObservasiModel;
use App\Models\TindakLanjutSupervisiModel;
use App\Models\CoachingTirtaModel;
use App\Models\UserModel;

class LaporanCetakController extends BaseController
{
    public function exportPdfRekapDetail()
    {
        try {
            $jadwalModel = new JadwalSupervisiModel();
            $hasilModel = new HasilSupervisiModel();
            $tahunAjarModel = new TahunAjarModel();
            $jenisPenilaianModel = new JenisPenilaianModel();
            $userModel = new UserModel();
            $db = \Config\Database::connect();

            helper('setting');
            helper('date');

            $tahun_ajar_id = $this->request->getGet('tahun_ajar_id');

            // Find selected tahun ajar
            $tahunAjars = $tahunAjarModel->orderBy('tahun_ajar', 'DESC')->findAll();
            $selectedTahunAjar = null;
            if ($tahun_ajar_id) {
                $selectedTahunAjar = $tahunAjarModel->find($tahun_ajar_id);
            } else {
                foreach ($tahunAjars as $ta) {
                    if (!empty($ta['is_active'])) {
                        $selectedTahunAjar = $ta;
                        $tahun_ajar_id = $ta['id'];
                        break;
                    }
                }
                if (!$selectedTahunAjar && !empty($tahunAjars)) {
                    $selectedTahunAjar = $tahunAjars[0];
                    $tahun_ajar_id = $selectedTahunAjar['id'];
                }
            }

            // Get all jenis penilaian
            $jenisPenilaians = $jenisPenilaianModel->findAll();

            // Query completed schedules
            $jadwalQuery = $jadwalModel
                ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran, kelas.nama_kelas, supervisor.username as nama_supervisor, supervisor.nip as nip_supervisor')
                ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
                ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
                ->join('users as supervisor', 'supervisor.id = jadwal_supervisi.supervisor_id', 'left')
                ->where('jadwal_supervisi.status', 'Selesai');

            if ($tahun_ajar_id) {
                $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
            }

            $jadwals = $jadwalQuery->orderBy('guru.nama', 'ASC')->findAll();

            // Calculate detailed score per teacher & per jenis penilaian
            $rekapData = [];
            $predikatStats = [
                'Baik Sekali' => ['count' => 0, 'range' => '86.00 - 100.00', 'color' => '#155724', 'bg' => '#d4edda'],
                'Baik'        => ['count' => 0, 'range' => '70.00 - 85.99',  'color' => '#004085', 'bg' => '#cce7ff'],
                'Cukup'       => ['count' => 0, 'range' => '55.00 - 69.99',  'color' => '#856404', 'bg' => '#fff3cd'],
                'Kurang'      => ['count' => 0, 'range' => '< 55.00',        'color' => '#721c24', 'bg' => '#f8d7da']
            ];
            $totalNilaiSum = 0;

            foreach ($jadwals as $jadwal) {
                $hasilRecords = $hasilModel->where('jadwal_supervisi_id', $jadwal['id'])->findAll();

                $nilaiPerJenis = [];
                $grandTotalSkor = 0;
                $grandTotalMaks = 0;

                foreach ($hasilRecords as $hasil) {
                    $detailHasil = $db->table('detail_hasil_penilaian')
                        ->where('hasil_supervisi_id', $hasil['id'])
                        ->selectSum('skor')
                        ->get()
                        ->getRow();

                    $detailCount = $db->table('detail_hasil_penilaian')
                        ->where('hasil_supervisi_id', $hasil['id'])
                        ->countAllResults();

                    $skor = (float)($detailHasil->skor ?? 0);
                    $max = $detailCount * 4;
                    $persen = ($max > 0) ? round(($skor / $max) * 100, 2) : 0;

                    $nilaiPerJenis[$hasil['jenis_penilaian_id']] = $persen;
                    $grandTotalSkor += $skor;
                    $grandTotalMaks += $max;
                }

                $nilaiAkhir = ($grandTotalMaks > 0) ? round(($grandTotalSkor / $grandTotalMaks) * 100, 2) : 0;
                if ($nilaiAkhir >= 86) {
                    $predikat = 'Baik Sekali';
                } elseif ($nilaiAkhir >= 70) {
                    $predikat = 'Baik';
                } elseif ($nilaiAkhir >= 55) {
                    $predikat = 'Cukup';
                } else {
                    $predikat = 'Kurang';
                }

                $predikatStats[$predikat]['count']++;
                $totalNilaiSum += $nilaiAkhir;

                $jadwal['nilai_per_jenis'] = $nilaiPerJenis;
                $jadwal['nilai_akhir'] = $nilaiAkhir;
                $jadwal['predikat'] = $predikat;
                $rekapData[] = $jadwal;
            }

            $totalGuru = count($rekapData);
            $rataRata = $totalGuru > 0 ? round($totalNilaiSum / $totalGuru, 2) : 0;

            // Get Signatures Data
            $namaKepala = get_pengaturan('nama_kepala', 'Sipuloh, M.Pd');
            $nipKepala = get_pengaturan('nip_kepala', '197005272007011022');
            $kotaMadrasah = get_pengaturan('kecamatan', 'Gisting');

            // Default supervisor or first supervisor found in system
            $firstSupervisor = $userModel->where('role', 'supervisor')->first();
            $namaSupervisor = $firstSupervisor['username'] ?? 'Supervisor Pembina';
            $nipSupervisor = $firstSupervisor['nip'] ?? '-';

            $data = [
                'selectedTahunAjar' => $selectedTahunAjar,
                'jenisPenilaians'   => $jenisPenilaians,
                'rekapData'         => $rekapData,
                'totalGuru'         => $totalGuru,
                'rataRata'          => $rataRata,
                'predikatStats'     => $predikatStats,
                'namaKepala'        => $namaKepala,
                'nipKepala'         => $nipKepala,
                'namaSupervisor'    => $namaSupervisor,
                'nipSupervisor'     => $nipSupervisor,
                'kotaMadrasah'      => $kotaMadrasah,
                'tanggalCetak'      => date('Y-m-d')
            ];

            // Clear buffer
            while (ob_get_level()) {
                ob_end_clean();
            }

            $html = view('admin/laporan/pdf_rekap_detail', $data);

            $options = new \Dompdf\Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isRemoteEnabled', true);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            $filename = 'Rekapitulasi-Detail-Supervisi-' . ($selectedTahunAjar ? preg_replace('/[^A-Za-z0-9]/', '-', $selectedTahunAjar['tahun_ajar'] . '-' . $selectedTahunAjar['semester']) : date('Ymd')) . '.pdf';

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');

            $dompdf->stream($filename, ['Attachment' => 0]);
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Rekap Detail PDF Error: ' . $e->getMessage());
            throw $e;
        }
    }
    public function cetakDetail($id)
    {
        $jadwalModel = new JadwalSupervisiModel();
        $hasilModel = new HasilSupervisiModel();
        $userModel = new \App\Models\UserModel();
        $db = \Config\Database::connect();
        
        // Get schedule with related data
        $schedule = $jadwalModel
            ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.id', $id)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();
            
        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data hasil supervisi tidak ditemukan');
        }
        
        // Get all jenis penilaian for tahap ordering and accurate naming
        $jenisModel = new \App\Models\JenisPenilaianModel();
        $allJenis = $jenisModel->findAllWithMappedColumns();
        $jenisById = [];
        $tahapNumber = [];
        foreach ($allJenis as $idx => $j) {
            $jenisById[$j['id']] = $j;
            $tahapNumber[$j['id']] = $idx + 1;
        }

        // Get hasil records for this schedule
        $hasilList = $hasilModel->where('jadwal_supervisi_id', $id)->findAll();
        foreach ($hasilList as &$h) {
            $jId = $h['jenis_penilaian_id'];
            $h['nama_jenis'] = $jenisById[$jId]['nama'] ?? ($jenisById[$jId]['nama_jenis'] ?? ('Komponen ' . $jId));
            $h['tahap_no'] = $tahapNumber[$jId] ?? $jId;
            $h['skor_maksimal'] = $jenisById[$jId]['skor_maksimal'] ?? 4;
        }
        unset($h);

        usort($hasilList, function($a, $b) {
            return ($a['tahap_no'] ?? 0) <=> ($b['tahap_no'] ?? 0);
        });
        
        // Get detail results grouped by jenis_penilaian_id
        $detailResults = [];
        foreach ($hasilList as $hasil) {
            $details = $db->table('detail_hasil_penilaian dhp')
                ->select('dhp.*, ap.nama_aspek, ap.tipe_skala')
                ->join('aspek_penilaian ap', 'ap.id = dhp.aspek_penilaian_id', 'left')
                ->where('dhp.hasil_supervisi_id', $hasil['id'])
                ->get()
                ->getResultArray();
                
            $detailResults[$hasil['jenis_penilaian_id']] = $details;
        }
        
        // Get photo evidence
        $fotoBukti = $db->table('foto_bukti')
            ->where('jadwal_supervisi_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
            
        // Get kepala sekolah data
        $kepalaSekolah = $userModel
            ->select('username, nip')
            ->where('role', 'kepala')
            ->first();
            
        // Set nama dan nip kepala sekolah
        $schedule['nama_kepala'] = '';
        $schedule['nip_kepala'] = '';
        
        if ($kepalaSekolah) {
            $schedule['nama_kepala'] = $kepalaSekolah['username'] ?? 'Kepala Sekolah';
            $schedule['nip_kepala'] = $kepalaSekolah['nip'] ?? '';
        }
        
        // Get supervisor data if not already in schedule
        if (empty($schedule['nama_supervisor']) && !empty($schedule['supervisor_id'])) {
            $supervisor = $userModel
                ->select('username, nip')
                ->where('id', $schedule['supervisor_id'])
                ->first();
                
            if ($supervisor) {
                $schedule['nama_supervisor'] = $supervisor['username'] ?? 'Supervisor';
                $schedule['nip_supervisor'] = $supervisor['nip'] ?? '';
            }
        } else if (empty($schedule['nama_supervisor'])) {
            $schedule['nama_supervisor'] = 'Supervisor';
            $schedule['nip_supervisor'] = '';
        }
        
        // Ensure nip fields exist
        if (!isset($schedule['nip_supervisor'])) {
            $schedule['nip_supervisor'] = '';
        }

        $signatureModel = new \App\Models\DokumenSignatureModel();
        $signatures     = $signatureModel->getSignaturesByJadwal($id);
        $isPolos        = ($this->request->getGet('mode') === 'polos');
        $isDraft        = false; // Admin view does not watermark draft

        $data = [
            'schedule'      => $schedule,
            'hasilList'     => $hasilList,
            'detailResults' => $detailResults,
            'fotoBukti'     => $fotoBukti,
            'jenisById'     => $jenisById,
            'tahapNumber'   => $tahapNumber,
            'signatures'    => $signatures,
            'is_draft'      => $isDraft,
            'is_polos'      => $isPolos,
        ];
        
        // Clear any previous output
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $html = view('supervisor/hasil/pdf_view', $data);
        
        // Setup Dompdf
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Stream the PDF directly to the browser
        $filename = 'detail_hasil_supervisi_' . ($schedule['nama_guru'] ?? 'guru') . '_' . date('Y-m-d');
        $dompdf->stream($filename . ".pdf", ['Attachment' => false]);
        exit();
    }
    public function cetakKurmer($jadwalId)
    {
        $jadwalModel = new JadwalSupervisiModel();
        $hasilModel = new HasilSupervisiModel();
        $detailModel = new \App\Models\DetailHasilPenilaianModel();

        $schedule = $jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, users.username as nama_supervisor, users.nip as nip_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan');
        }

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

        $hasil = $hasilModel->where('jadwal_supervisi_id', $jadwalId)->first();
        $detailsByAspek = [];
        if ($hasil) {
            $details = $detailModel->where('hasil_supervisi_id', $hasil['id'])->findAll();
            foreach ($details as $d) {
                $detailsByAspek[$d['aspek_penilaian_id']] = $d;
            }
        }

        $signatureModel = new \App\Models\DokumenSignatureModel();
        $signatures     = $signatureModel->getSignaturesByJadwal($jadwalId);
        $isPolos        = ($this->request->getGet('mode') === 'polos');
        $isDraft        = false;

        $data = [
            'schedule'       => $schedule,
            'praObservasi'   => $praObservasi,
            'tindakLanjut'   => $tindakLanjut,
            'fokusList'      => $fokusList,
            'indikatorNama'  => $indikatorNama,
            'hasilUtama'     => $hasil,
            'detailsByAspek' => $detailsByAspek,
            'signatures'     => $signatures,
            'is_draft'       => $isDraft,
            'is_polos'       => $isPolos,
        ];

        while (ob_get_level()) {
            ob_end_clean();
        }

        $html = view('pdf/formulir_abcd_pmm', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("Formulir_ABCD_PMM_" . str_replace(' ', '_', $schedule['nama_guru']) . ".pdf", ['Attachment' => false]);
        exit();
    }
    public function cetakTelaah($jadwalId, $jenisId = null)
    {
        $jadwalModel = new JadwalSupervisiModel();
        $hasilModel = new HasilSupervisiModel();
        $detailModel = new \App\Models\DetailHasilPenilaianModel();

        $schedule = $jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran as guru_mata_pelajaran, kelas.nama_kelas, kelas.fase as kelas_fase, users.username as nama_supervisor, users.nip as nip_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan');
        }

        $jenisModel = new \App\Models\JenisPenilaianModel();
        $aspekModel = new \App\Models\AspekPenilaianModel();

        if (!$jenisId) {
            $hasilTelaah = $hasilModel
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

        $hasil = $hasilModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->where('jenis_penilaian_id', $jenisId)
            ->first();

        $detailResults = [];
        if ($hasil) {
            $details = $detailModel->where('hasil_supervisi_id', $hasil['id'])->findAll();
            foreach ($details as $d) {
                $detailResults[$d['aspek_penilaian_id']] = $d;
            }
        }

        $signatureModel = new \App\Models\DokumenSignatureModel();
        $signatures     = $signatureModel->getSignaturesByJadwal($jadwalId);
        $isPolos        = ($this->request->getGet('mode') === 'polos');
        $isDraft        = false;

        $data = [
            'schedule'       => $schedule,
            'jenisPenilaian' => $jenisPenilaian,
            'aspekList'      => $aspekList,
            'hasil'          => $hasil,
            'detailResults'  => $detailResults,
            'signatures'     => $signatures,
            'is_draft'       => $isDraft,
            'is_polos'       => $isPolos,
        ];

        while (ob_get_level()) {
            ob_end_clean();
        }

        $html = view('pdf/telaah_modul_ajar', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Telaah_" . str_replace(' ', '_', $jenisPenilaian['nama']) . "_" . str_replace(' ', '_', $schedule['nama_guru']) . ".pdf";
        $dompdf->stream($filename, ['Attachment' => false]);
        exit();
    }
    public function cetakCoaching($jadwalId)
    {
        $jadwalModel = new JadwalSupervisiModel();

        $schedule = $jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran as guru_mata_pelajaran, kelas.nama_kelas, kelas.fase as kelas_fase, users.username as nama_supervisor, users.nip as nip_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan');
        }

        $coachingModel = new \App\Models\CoachingTirtaModel();
        $praModel      = new \App\Models\PraObservasiModel();
        $tlModel       = new \App\Models\TindakLanjutSupervisiModel();

        $coachingList = $coachingModel->where('jadwal_id', $jadwalId)->orderBy('id', 'ASC')->findAll();
        $praObservasi = $praModel->getByJadwal($jadwalId);
        $tindakLanjut = $tlModel->getByJadwal($jadwalId);

        $signatureModel = new \App\Models\DokumenSignatureModel();
        $signatures     = $signatureModel->getSignaturesByJadwal($jadwalId);
        $isPolos        = ($this->request->getGet('mode') === 'polos');
        $isDraft        = false;

        $data = [
            'schedule'     => $schedule,
            'coachingList' => $coachingList,
            'praObservasi' => $praObservasi,
            'tindakLanjut' => $tindakLanjut,
            'signatures'   => $signatures,
            'is_draft'     => $isDraft,
            'is_polos'     => $isPolos,
        ];

        while (ob_get_level()) {
            ob_end_clean();
        }

        $html = view('pdf/lembar_coaching_tirta', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Berita_Acara_Coaching_TIRTA_" . str_replace(' ', '_', $schedule['nama_guru']) . ".pdf";
        $dompdf->stream($filename, ['Attachment' => false]);
        exit();
    }
    public function exportPdf()
    {
        try {
            $jadwalModel = new JadwalSupervisiModel();
            $tahunAjarModel = new TahunAjarModel();
            $guruModel = new GuruModel();
            $hasilModel = new HasilSupervisiModel();
            $db = \Config\Database::connect();
            
            // Get filter parameters
            $tahun_ajar_id = $this->request->getGet('tahun_ajar_id');
            $status = $this->request->getGet('status');
            
            if ($tahun_ajar_id === null) {
                $activeTahun = $tahunAjarModel->getActive();
                $tahun_ajar_id = $activeTahun ? (string)$activeTahun['id'] : '';
            }

            // Build query for completed schedules
            $jadwalQuery = $jadwalModel
                ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.mata_pelajaran, kelas.nama_kelas')
                ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
                ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left');
                
            if (!empty($tahun_ajar_id) && $tahun_ajar_id !== 'all') {
                $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
            }
            
            if (!empty($status) && $status !== 'all') {
                $jadwalQuery->where('jadwal_supervisi.status', $status);
            }
            
            $jadwalQuery->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC');
            
            $jadwals = $jadwalQuery->findAll();
            
            // Get results for each schedule (consistent with hasilSupervisi function)
            $completedJadwals = [];
            foreach ($jadwals as $jadwal) {
                if ($jadwal['status'] == 'Selesai') {
                    // Get all hasil records for this schedule
                    $hasilRecords = $hasilModel->where('jadwal_supervisi_id', $jadwal['id'])->findAll();
                    
                    if (!empty($hasilRecords)) {
                        // Calculate nilai_akhir dynamically based on detail_hasil_penilaian
                        $totalSkor = 0;
                        $totalMaksimal = 0;
                        
                        foreach ($hasilRecords as $hasil) {
                            // Get detail hasil penilaian for this hasil record
                            $detailHasil = $db->table('detail_hasil_penilaian')
                                ->where('hasil_supervisi_id', $hasil['id'])
                                ->selectSum('skor')
                                ->get()
                                ->getRow();
                            
                            // Get count of detail penilaian to calculate max possible score
                            $detailCount = $db->table('detail_hasil_penilaian')
                                ->where('hasil_supervisi_id', $hasil['id'])
                                ->countAllResults();
                            
                            // Add to totals
                            $totalSkor += $detailHasil->skor ?? 0;
                            // Assuming max score per item is 4 (based on system specs)
                            $totalMaksimal += $detailCount * 4;
                        }
                        
                        // Calculate nilai_akhir: (actual score sum / max possible score) × 100
                        $nilaiAkhir = ($totalMaksimal > 0) ? ($totalSkor / $totalMaksimal) * 100 : 0;
                        
                        // Determine ketercapaian based on nilai_akhir
                        if ($nilaiAkhir >= 86) {
                            $ketercapaian = 'Baik Sekali';
                        } elseif ($nilaiAkhir >= 70) {
                            $ketercapaian = 'Baik';
                        } elseif ($nilaiAkhir >= 55) {
                            $ketercapaian = 'Cukup';
                        } else {
                            $ketercapaian = 'Kurang';
                        }
                        
                        // Create a mock hasil record with calculated nilai_akhir
                        $jadwal['hasil'] = [
                            'nilai_akhir' => $nilaiAkhir,
                            'ketercapaian' => $ketercapaian,
                            'total_skor' => $totalSkor,
                            'total_maksimal' => $totalMaksimal
                        ];
                    } else {
                        $jadwal['hasil'] = null;
                    }
                } else {
                    $jadwal['hasil'] = null;
                }
                $completedJadwals[] = $jadwal;
            }
            
            $data = [
                'jadwals' => $completedJadwals,
                'tahun_ajar_id' => $tahun_ajar_id,
                'status' => $status,
                'tahun_ajars' => $tahunAjarModel->findAll(),
                'nama_madrasah' => get_nama_madrasah()
            ];
            
            // Get tahun ajar filter info
            if (!empty($tahun_ajar_id) && $tahun_ajar_id !== 'all') {
                $tahunAjar = $tahunAjarModel->find($tahun_ajar_id);
                if ($tahunAjar) {
                    $data['filter_tahun_ajar'] = $tahunAjar['tahun_ajar'] . ' - ' . $tahunAjar['semester'];
                }
            } elseif ($tahun_ajar_id === 'all') {
                $data['filter_tahun_ajar'] = 'Semua Tahun Ajaran';
            }
            
            // Clear any previous output
            ob_end_clean();
            
            // Load the view and generate HTML
            $html = view('admin/laporan/pdf_hasil_supervisi', $data);
            
            // Setup Dompdf
            $options = new \Dompdf\Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            // Set proper headers for PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="laporan-hasil-supervisi-' . date('Y-m-d') . '.pdf"');
            
            // Output the PDF
            $dompdf->stream('laporan-hasil-supervisi-' . date('Y-m-d') . '.pdf', ['Attachment' => 0]);
            exit();
        } catch (\Exception $e) {
            log_message('error', 'PDF Export Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
