<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\LaporanModel;
use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\TahunAjarModel;
use App\Models\GuruModel;
use App\Models\JenisPenilaianModel;

class LaporanController extends BaseController
{
    public function index()
    {
        $jadwalModel = new JadwalSupervisiModel();
        $hasilModel = new HasilSupervisiModel();
        $tahunAjarModel = new TahunAjarModel();
        $jenisPenilaianModel = new JenisPenilaianModel();
        $db = \Config\Database::connect();

        // 1. Tahun Ajar Filter
        $tahunAjars = $tahunAjarModel->orderBy('tahun_ajar', 'DESC')->findAll();

        $activeTahunAjar = $tahunAjarModel->getActive();
        if (!$activeTahunAjar && !empty($tahunAjars)) {
            $activeTahunAjar = $tahunAjars[0];
        }

        $tahun_ajar_id = $this->request->getGet('tahun_ajar_id');
        if (empty($tahun_ajar_id) && $activeTahunAjar) {
            $tahun_ajar_id = $activeTahunAjar['id'];
        }

        $selectedTahunAjar = null;
        if ($tahun_ajar_id) {
            foreach ($tahunAjars as $ta) {
                if ($ta['id'] == $tahun_ajar_id) {
                    $selectedTahunAjar = $ta;
                    break;
                }
            }
        }

        // 2. Daftar Jenis Penilaian untuk kolom skor dinamis
        $jenisPenilaians = $jenisPenilaianModel->findAll();

        // 3. Query Jadwal Supervisi pada tahun ajaran yang dipilih
        $jadwalQuery = $jadwalModel
            ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran, kelas.nama_kelas, supervisor.username as nama_supervisor')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('users as supervisor', 'supervisor.id = jadwal_supervisi.supervisor_id', 'left');

        if ($tahun_ajar_id) {
            $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
        }

        $jadwals = $jadwalQuery->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC')->findAll();

        // 4. Hitung Skor per Guru dan per Jenis Penilaian
        $totalJadwal = count($jadwals);
        $selesaiList = [];
        $totalNilaiSum = 0;
        $predikatCounts = [
            'Baik Sekali' => 0,
            'Baik'        => 0,
            'Cukup'       => 0,
            'Kurang'      => 0
        ];

        $skorPerJenisSum = [];
        $countPerJenis = [];
        foreach ($jenisPenilaians as $jp) {
            $skorPerJenisSum[$jp['id']] = 0;
            $countPerJenis[$jp['id']] = 0;
        }

        foreach ($jadwals as &$jadwal) {
            $jadwal['nilai_per_jenis'] = [];
            $jadwal['nilai_akhir'] = null;
            $jadwal['ketercapaian'] = null;
            $jadwal['predikat_badge'] = 'secondary';

            if ($jadwal['status'] === 'Selesai') {
                $hasilRecords = $hasilModel->where('jadwal_supervisi_id', $jadwal['id'])->findAll();

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

                    $jadwal['nilai_per_jenis'][$hasil['jenis_penilaian_id']] = [
                        'skor' => $skor,
                        'maksimal' => $max,
                        'nilai' => $persen
                    ];

                    $grandTotalSkor += $skor;
                    $grandTotalMaks += $max;

                    if (isset($skorPerJenisSum[$hasil['jenis_penilaian_id']])) {
                        $skorPerJenisSum[$hasil['jenis_penilaian_id']] += $persen;
                        $countPerJenis[$hasil['jenis_penilaian_id']]++;
                    }
                }

                if ($grandTotalMaks > 0) {
                    $nilaiAkhir = round(($grandTotalSkor / $grandTotalMaks) * 100, 2);
                    $jadwal['nilai_akhir'] = $nilaiAkhir;

                    if ($nilaiAkhir >= 86) {
                        $ketercapaian = 'Baik Sekali';
                        $predikatBadge = 'success';
                    } elseif ($nilaiAkhir >= 70) {
                        $ketercapaian = 'Baik';
                        $predikatBadge = 'primary';
                    } elseif ($nilaiAkhir >= 55) {
                        $ketercapaian = 'Cukup';
                        $predikatBadge = 'warning';
                    } else {
                        $ketercapaian = 'Kurang';
                        $predikatBadge = 'danger';
                    }

                    $jadwal['ketercapaian'] = $ketercapaian;
                    $jadwal['predikat_badge'] = $predikatBadge;
                    $predikatCounts[$ketercapaian]++;
                    $totalNilaiSum += $nilaiAkhir;
                    $selesaiList[] = $jadwal;
                }
            }
        }
        unset($jadwal);

        $totalSelesai = count($selesaiList);
        $totalBelumSelesai = $totalJadwal - $totalSelesai;
        $rataRataNilai = $totalSelesai > 0 ? round($totalNilaiSum / $totalSelesai, 2) : 0;

        // Persentase Ketercapaian Kualitas (Baik Sekali + Baik)
        $kualitasBaik = $predikatCounts['Baik Sekali'] + $predikatCounts['Baik'];
        $persenKualitas = $totalSelesai > 0 ? round(($kualitasBaik / $totalSelesai) * 100, 1) : 0;

        // Rata-rata per Jenis Penilaian
        $avgPerJenis = [];
        $chartJenisLabels = [];
        $chartJenisData = [];
        foreach ($jenisPenilaians as $jp) {
            $val = ($countPerJenis[$jp['id']] > 0) 
                ? round($skorPerJenisSum[$jp['id']] / $countPerJenis[$jp['id']], 2) 
                : 0;
            $avgPerJenis[$jp['id']] = $val;
            $chartJenisLabels[] = $jp['nama'] ?? ('Jenis ' . $jp['id']);
            $chartJenisData[] = $val;
        }

        $data = [
            'title' => 'Laporan dan Analisis Hasil Supervisi',
            'tahun_ajars' => $tahunAjars,
            'tahun_ajar_id' => $tahun_ajar_id,
            'selectedTahunAjar' => $selectedTahunAjar,
            'jenisPenilaians' => $jenisPenilaians,
            'jadwals' => $jadwals,
            'metrics' => [
                'total_jadwal' => $totalJadwal,
                'total_selesai' => $totalSelesai,
                'total_belum' => $totalBelumSelesai,
                'rata_rata_nilai' => $rataRataNilai,
                'persen_kualitas' => $persenKualitas,
                'predikat_counts' => $predikatCounts,
                'avg_per_jenis' => $avgPerJenis
            ],
            'chart_predikat' => [
                'labels' => ['Baik Sekali (86-100)', 'Baik (70-85)', 'Cukup (55-69)', 'Kurang (<55)'],
                'data'   => [
                    $predikatCounts['Baik Sekali'],
                    $predikatCounts['Baik'],
                    $predikatCounts['Cukup'],
                    $predikatCounts['Kurang']
                ]
            ],
            'chart_jenis' => [
                'labels' => $chartJenisLabels,
                'data'   => $chartJenisData
            ]
        ];

        return view('admin/laporan/index', $data);
    }
    public function statistikPengguna()
    {
        $laporanModel = new LaporanModel();

        $role = $this->request->getGet('role');
        $status = $this->request->getGet('status');

        $data['users'] = $laporanModel->getUsers($role, $status);
        $data['roleCounts'] = $laporanModel->getUserRoleCounts();
        $data['statusCounts'] = $laporanModel->getUserStatusCounts();
        $data['role'] = $role;
        $data['status'] = $status;

        return view('admin/laporan/statistik_pengguna', $data);
    }
    public function hasilSupervisi()
    {
        $jadwalModel = new JadwalSupervisiModel();
        $hasilModel = new HasilSupervisiModel();
        $tahunAjarModel = new TahunAjarModel();
        $guruModel = new GuruModel();
        $db = \Config\Database::connect();
        
        // Get active tahun ajar
        $activeTahun = $tahunAjarModel->getActive();

        // Get filter parameters
        $tahun_ajar_id = $this->request->getGet('tahun_ajar_id');
        $status = $this->request->getGet('status');
        
        // Default to active academic year when opening page without filter
        if ($tahun_ajar_id === null) {
            $tahun_ajar_id = $activeTahun ? (string)$activeTahun['id'] : '';
        }

        // Get tahun ajaran list for filter
        $data['tahun_ajars'] = $tahunAjarModel->orderBy('tahun_ajar', 'DESC')->orderBy('semester', 'ASC')->findAll();
        $data['activeTahun'] = $activeTahun;
        
        // Build query for supervision schedules
        $jadwalQuery = $jadwalModel
            ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id');
            
        // Apply filters
        if (!empty($tahun_ajar_id) && $tahun_ajar_id !== 'all') {
            $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
        }
        
        if (!empty($status) && $status !== 'all') {
            $jadwalQuery->where('jadwal_supervisi.status', $status);
        }
        
        // Get supervision schedules
        $data['jadwals'] = $jadwalQuery->findAll();
        
        // Add hasil information to each schedule
        foreach ($data['jadwals'] as &$jadwal) {
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
        }
        
        // Count statistics
        $data['total_jadwal'] = count($data['jadwals']);
        $data['selesai'] = array_filter($data['jadwals'], function($jadwal) {
            return $jadwal['status'] == 'Selesai';
        });
        $data['belum_selesai'] = array_filter($data['jadwals'], function($jadwal) {
            return $jadwal['status'] != 'Selesai';
        });
        
        $data['tahun_ajar_id'] = $tahun_ajar_id;
        $data['status'] = $status;
        
        return view('admin/laporan/hasil_supervisi', $data);
    }
    public function detail($id)
    {
        $jadwalModel = new JadwalSupervisiModel();
        $hasilModel = new HasilSupervisiModel();
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
        
        // Get hasil records for this schedule
        $hasilList = $hasilModel->where('jadwal_supervisi_id', $id)->findAll();
        
        // Get detail results grouped by jenis_penilaian_id
        $detailResults = [];
        foreach ($hasilList as $hasil) {
            $details = $db->table('detail_hasil_penilaian dhp')
                ->select('dhp.*, ap.nama_aspek')
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
        
        $data = [
            'schedule' => $schedule,
            'hasilList' => $hasilList,
            'detailResults' => $detailResults,
            'fotoBukti' => $fotoBukti
        ];
        
        return view('admin/laporan/detail', $data);
    }
}
