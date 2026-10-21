<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\JenisPenilaianModel;
use App\Models\AspekPenilaianModel;
use App\Models\HasilSupervisiModel;
use App\Models\DetailHasilPenilaianModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class PenilaianController extends BaseController
{
    protected $jadwalModel;
    protected $jenisModel;
    protected $aspekModel;
    protected $hasilModel;
    protected $detailModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->jenisModel = new JenisPenilaianModel();
        $this->aspekModel = new AspekPenilaianModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->detailModel = new DetailHasilPenilaianModel();
    }

    public function form($jadwalId)
    {
        $supervisorId = session()->get('id');

        // Get schedule details - izinkan seluruh tahapan siklus supervisi aktif dan selesai
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.mata_pelajaran as guru_mata_pelajaran')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->whereIn('jadwal_supervisi.status', ['Terjadwal', 'dijadwalkan', 'pra_observasi', 'observasi', 'pasca_observasi', 'tindak_lanjut', 'Selesai', 'selesai'])
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal tidak ditemukan atau tidak dapat dinilai');
        }


        // Get all assessment types with mapped columns to ensure we get latest skor_maksimal
        $jenisPenilaian = $this->jenisModel->findAllWithMappedColumns();

        // Get all assessment aspects
        $aspekPenilaian = $this->aspekModel
            ->select('aspek_penilaian.*, jenis_penilaian.nama as nama_jenis')
            ->join('jenis_penilaian', 'jenis_penilaian.id = aspek_penilaian.jenis_penilaian_id')
            ->orderBy('aspek_penilaian.jenis_penilaian_id')
            ->orderBy('aspek_penilaian.urutan')
            ->findAll();

        // Group aspects by jenis penilaian
        $aspekByJenis = [];
        foreach ($aspekPenilaian as $aspek) {
            $aspekByJenis[$aspek['jenis_penilaian_id']][] = $aspek;
        }

        // Get existing results if any
        $existingResults = [];
        $existingDetails = [];
        foreach ($jenisPenilaian as $jenis) {
            $existingResults[$jenis['id']] = $this->hasilModel
                ->where('jadwal_supervisi_id', $jadwalId)
                ->where('jenis_penilaian_id', $jenis['id'])
                ->first();
            
            // If there are existing results, get the details
            if ($existingResults[$jenis['id']]) {
                $hasilId = $existingResults[$jenis['id']]['id'];
                $details = $this->detailModel->where('hasil_supervisi_id', $hasilId)->findAll();
                foreach ($details as $detail) {
                    $existingDetails[$jenis['id']][$detail['aspek_penilaian_id']] = $detail;
                }
            }
        }

        $fotoModel = new \App\Models\FotoBuktiModel();
        $existingPhotos = $fotoModel->where('jadwal_supervisi_id', $jadwalId)->findAll();

        $data = [
            'schedule' => $schedule,
            'jenisPenilaian' => $jenisPenilaian,
            'aspekByJenis' => $aspekByJenis,
            'existingResults' => $existingResults,
            'existingDetails' => $existingDetails,
            'existingPhotos' => $existingPhotos
        ];

        return view('supervisor/penilaian/form', $data);
    }

    public function save()
    {
        // Cek jika request berasal dari AJAX
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $jadwalId = $this->request->getPost('jadwal_id');
            $jenisPenilaianId = $this->request->getPost('jenis_penilaian_id');
            $rekomendasi = $this->request->getPost('rekomendasi');

            $supervisorId = session()->get('id');

            // Validasi jadwal - izinkan seluruh tahapan siklus supervisi aktif dan selesai
            $schedule = $this->jadwalModel
                ->where('id', $jadwalId)
                ->where('supervisor_id', $supervisorId)
                ->whereIn('status', ['Terjadwal', 'dijadwalkan', 'pra_observasi', 'observasi', 'pasca_observasi', 'tindak_lanjut', 'Selesai', 'selesai'])
                ->first();

            if (!$schedule) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Jadwal tidak ditemukan atau tidak dapat dinilai',
                    'token' => csrf_hash()
                ]);
            }


            // Cek apakah hasil untuk jenis penilaian ini sudah ada
            $existingHasil = $this->hasilModel
                ->where('jadwal_supervisi_id', $jadwalId)
                ->where('jenis_penilaian_id', $jenisPenilaianId)
                ->first();

            // Simpan atau update hasil supervisi
            $hasilData = [
                'jadwal_supervisi_id' => $jadwalId,
                'jenis_penilaian_id' => $jenisPenilaianId,
                'rekomendasi' => $rekomendasi
            ];

            if ($existingHasil) {
                $this->hasilModel->update($existingHasil['id'], $hasilData);
                $hasilId = $existingHasil['id'];
            } else {
                $hasilId = $this->hasilModel->insert($hasilData);
            }

            // Jika gagal menyimpan hasil supervisi
            if (!$hasilId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan hasil supervisi',
                    'token' => csrf_hash()
                ]);
            }

            // Siapkan array untuk menyimpan detail penilaian
            $detailsToInsert = [];

            // Siapkan detail hasil penilaian
            $aspekList = $this->aspekModel
                ->where('jenis_penilaian_id', $jenisPenilaianId)
                ->findAll();

            foreach ($aspekList as $aspek) {
                $skorKey = 'skor_' . $aspek['id'];
                $catatanKey = 'catatan_' . $aspek['id'];

                $skor = $this->request->getPost($skorKey);
                $catatan = $this->request->getPost($catatanKey);

                // Hanya tambahkan ke array jika skor tidak kosong
                if ($skor !== null && $skor !== '') {
                    $detailsToInsert[] = [
                        'hasil_supervisi_id' => $hasilId,
                        'aspek_penilaian_id' => $aspek['id'],
                        'skor' => $skor,
                        'catatan' => $catatan
                    ];
                }
            }

            // Hapus detail hasil yang lama hanya jika ada detail baru yang akan disimpan
            if (!empty($detailsToInsert)) {
                $this->detailModel->where('hasil_supervisi_id', $hasilId)->delete();
                
                // Simpan semua detail penilaian sekaligus
                $this->detailModel->insertBatch($detailsToInsert);
            } else {
                // Jika tidak ada detail yang disimpan, hapus detail lama saja
                $this->detailModel->where('hasil_supervisi_id', $hasilId)->delete();
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data penilaian berhasil disimpan',
                'token' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            // Log error untuk debugging
            log_message('error', 'Error saat menyimpan penilaian: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function view($jadwalId)
    {
        $supervisorId = session()->get('id');

        // Get schedule details
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip, guru.mata_pelajaran as guru_mata_pelajaran')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil penilaian tidak ditemukan');
        }

        // Get all assessment results
        $hasilPenilaian = $this->hasilModel
            ->select('hasil_supervisi.*, jenis_penilaian.nama as nama_jenis, jenis_penilaian.skor_maksimal')
            ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
            ->where('hasil_supervisi.jadwal_supervisi_id', $jadwalId)
            ->findAll();

        // Get detail results and calculate nilai_akhir and ketercapaian dynamically
        $detailResults = [];
        $processedHasilPenilaian = [];
        
        foreach ($hasilPenilaian as $hasil) {
            $details = $this->detailModel
                ->select('detail_hasil_penilaian.*, aspek_penilaian.nama_aspek, aspek_penilaian.tipe_skala')
                ->join('aspek_penilaian', 'aspek_penilaian.id = detail_hasil_penilaian.aspek_penilaian_id')
                ->where('detail_hasil_penilaian.hasil_supervisi_id', $hasil['id'])
                ->findAll();
            
            $detailResults[$hasil['jenis_penilaian_id']] = $details;
            
            // Calculate nilai_akhir and ketercapaian for this jenis penilaian dinamis
            $totalSkor = 0;
            $maxScore = 0;
            foreach ($details as $detail) {
                $totalSkor += (float) $detail['skor'];
                if (($detail['tipe_skala'] ?? '') === 'perilaku_pmm_1_3') {
                    $maxScore += 3;
                } elseif (($detail['tipe_skala'] ?? '') === 'skor_1_4') {
                    $maxScore += 4;
                } elseif (($hasil['skor_maksimal'] ?? 0) == 3) {
                    $maxScore += 3;
                } else {
                    $maxScore += 4;
                }
            }
            
            $nilaiAkhir = $maxScore > 0 ? ($totalSkor / $maxScore) * 100 : 0;
            
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
            
            // Update the hasil data with calculated values
            $hasil['nilai_akhir'] = $nilaiAkhir;
            $hasil['ketercapaian'] = $ketercapaian;
            $hasil['total_skor'] = $totalSkor;
            
            $processedHasilPenilaian[] = $hasil;
        }

        // Get uploaded photos
        $fotoBuktiModel = new \App\Models\FotoBuktiModel();
        $uploadedPhotos = $fotoBuktiModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->findAll();

        $data = [
            'schedule' => $schedule,
            'hasilPenilaian' => $processedHasilPenilaian,
            'detailResults' => $detailResults,
            'uploadedPhotos' => $uploadedPhotos
        ];

        return view('supervisor/penilaian/view', $data);
    }
    
    public function complete($jadwalId)
    {
        $supervisorId = session()->get('id');
        
        // Validasi jadwal - izinkan seluruh tahapan siklus supervisi aktif dan selesai
        $schedule = $this->jadwalModel
            ->where('id', $jadwalId)
            ->where('supervisor_id', $supervisorId)
            ->whereIn('status', ['Terjadwal', 'dijadwalkan', 'pra_observasi', 'observasi', 'pasca_observasi', 'tindak_lanjut', 'Selesai', 'selesai'])
            ->first();

        if (!$schedule) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Jadwal tidak ditemukan atau tidak dapat diselesaikan',
                'token' => csrf_hash()
            ]);
        }

        
        try {
            // Hitung total skor dan nilai akhir untuk seluruh supervisi
            $hasilData = $this->hasilModel
                ->select('hasil_supervisi.*, jenis_penilaian.skor_maksimal, jenis_penilaian.kategori_supervisi')
                ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
                ->where('jadwal_supervisi_id', $jadwalId)
                ->findAll();
                
            $totalSkor = 0;
            $totalMaksimal = 0;
            
            foreach ($hasilData as $hasil) {
                $details = $this->detailModel
                    ->select('detail_hasil_penilaian.*, aspek_penilaian.tipe_skala')
                    ->join('aspek_penilaian', 'aspek_penilaian.id = detail_hasil_penilaian.aspek_penilaian_id', 'left')
                    ->where('hasil_supervisi_id', $hasil['id'])
                    ->findAll();
                    
                $skorJenis = 0;
                $skorMaksimal = 0;
                foreach ($details as $detail) {
                    $skorJenis += (float) $detail['skor'];
                    if (($detail['tipe_skala'] ?? '') === 'perilaku_pmm_1_3' || ($hasil['kategori_supervisi'] ?? '') === 'observasi_kelas_pmm') {
                        $skorMaksimal += 3;
                    } elseif (($detail['tipe_skala'] ?? '') === 'skor_1_4') {
                        $skorMaksimal += 4;
                    } elseif (($hasil['skor_maksimal'] ?? 0) == 3) {
                        $skorMaksimal += 3;
                    } else {
                        $skorMaksimal += 4;
                    }
                }
                
                $totalSkor += $skorJenis;
                $totalMaksimal += $skorMaksimal;
            }
            
            // Hitung nilai akhir berdasarkan rumus proporsional
            $nilaiAkhir = $totalMaksimal > 0 ? ($totalSkor / $totalMaksimal) * 100 : 0;
            
            // Tentukan ketercapaian berdasarkan nilai akhir
            $ketercapaian = '';
            if ($nilaiAkhir >= 86) {
                $ketercapaian = 'Baik Sekali';
            } elseif ($nilaiAkhir >= 70) {
                $ketercapaian = 'Baik';
            } elseif ($nilaiAkhir >= 55) {
                $ketercapaian = 'Cukup';
            } else {
                $ketercapaian = 'Kurang';
            }
            
            // Cek apakah jadwal ini Kurikulum Merdeka (memiliki indikator PMM)
            $isKurmer = !empty($schedule['indikator_pmm_id']);
            $statusBaru = $isKurmer ? 'pasca_observasi' : 'Selesai';
            
            $updateData = [
                'status' => $statusBaru,
                'nilai_akhir' => $nilaiAkhir,
                'ketercapaian' => $ketercapaian
            ];
            if ($isKurmer) {
                $updateData['tanggal_pasca_observasi'] = date('Y-m-d H:i:s');
            }
            
            $this->jadwalModel->update($jadwalId, $updateData);
            
            $pesanSukses = $isKurmer
                ? 'Observasi kelas berhasil diselesaikan. Jadwal kini memasuki tahap tindak lanjut (Formulir C & D).'
                : 'Supervisi berhasil diselesaikan';
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $pesanSukses,
                'redirect' => base_url('supervisor/jadwal'),
                'token' => csrf_hash()
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error saat menyelesaikan supervisi: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyelesaikan supervisi: ' . $e->getMessage(),
                'token' => csrf_hash()
            ]);
        }
    }

}