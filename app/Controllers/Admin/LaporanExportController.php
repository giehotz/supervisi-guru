<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\JadwalSupervisiModel;
use App\Models\TahunAjarModel;
use App\Models\GuruModel;
use App\Models\HasilSupervisiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanExportController extends BaseController
{
    public function exportExcel()
    {
        try {
            $jadwalModel = new JadwalSupervisiModel();
            $tahunAjarModel = new TahunAjarModel();
            $guruModel = new GuruModel();
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
            
            // Get results for each schedule
            $completedJadwals = [];
            foreach ($jadwals as $jadwal) {
                if ($jadwal['status'] == 'Selesai') {
                    // Get average nilai_akhir for this schedule
                    $hasilQuery = $db->table('hasil_supervisi')
                        ->select('AVG(nilai_akhir) as avg_nilai')
                        ->where('jadwal_supervisi_id', $jadwal['id']);
                        
                    $result = $hasilQuery->get()->getRow();
                    
                    if ($result) {
                        $jadwal['hasil']['nilai_akhir'] = $result->avg_nilai;
                        
                        // Determine ketercapaian based on nilai
                        if ($result->avg_nilai >= 3.5) {
                            $jadwal['hasil']['ketercapaian'] = 'Sangat Baik';
                        } elseif ($result->avg_nilai >= 2.5) {
                            $jadwal['hasil']['ketercapaian'] = 'Baik';
                        } elseif ($result->avg_nilai >= 1.5) {
                            $jadwal['hasil']['ketercapaian'] = 'Cukup';
                        } else {
                            $jadwal['hasil']['ketercapaian'] = 'Kurang';
                        }
                    }
                }
                $completedJadwals[] = $jadwal;
            }
            
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("Sistem Supervisi")
                ->setLastModifiedBy("Sistem Supervisi")
                ->setTitle("Laporan Hasil Supervisi")
                ->setSubject("Laporan Hasil Supervisi")
                ->setDescription("Laporan hasil supervisi guru");
                
            // Header
            $sheet->setCellValue('A1', 'LAPORAN HASIL SUPERVISI GURU');
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // School name
            $sheet->setCellValue('A2', get_nama_madrasah());
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Filter info
            $filterText = 'Filter: ';
            if (!empty($tahun_ajar_id) && $tahun_ajar_id !== 'all') {
                $tahunAjar = $tahunAjarModel->find($tahun_ajar_id);
                if ($tahunAjar) {
                    $filterText .= 'Tahun Ajaran ' . $tahunAjar['tahun_ajar'] . ' - ' . $tahunAjar['semester'];
                }
            } elseif ($tahun_ajar_id === 'all') {
                $filterText .= 'Semua Tahun Ajaran';
            }
            
            if (!empty($status) && $status !== 'all') {
                $filterText .= (!empty($tahun_ajar_id) ? ', ' : '') . 'Status ' . $status;
            }
            
            if ($filterText === 'Filter: ') {
                $filterText = 'Filter: Semua Data';
            }
            
            $sheet->setCellValue('A3', $filterText);
            $sheet->mergeCells('A3:H3');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Table header
            $headerRow = 5;
            $sheet->setCellValue('A' . $headerRow, 'No');
            $sheet->setCellValue('B' . $headerRow, 'Tahun Ajaran');
            $sheet->setCellValue('C' . $headerRow, 'Nama Guru');
            $sheet->setCellValue('D' . $headerRow, 'Mata Pelajaran');
            $sheet->setCellValue('E' . $headerRow, 'Kelas');
            $sheet->setCellValue('F' . $headerRow, 'Tanggal Supervisi');
            $sheet->setCellValue('G' . $headerRow, 'Nilai Akhir');
            $sheet->setCellValue('H' . $headerRow, 'Ketercapaian');
            
            // Style table header
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4e73df');
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
            
            // Table data
            $row = $headerRow + 1;
            $no = 1;
            foreach ($completedJadwals as $jadwal) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $jadwal['tahun_ajar'] . ' - ' . $jadwal['semester']);
                $sheet->setCellValue('C' . $row, $jadwal['nama_guru']);
                $sheet->setCellValue('D' . $row, $jadwal['mata_pelajaran']);
                $sheet->setCellValue('E' . $row, $jadwal['nama_kelas'] ?? $jadwal['kelas']);
                $sheet->setCellValue('F' . $row, format_tanggal_indonesia($jadwal['tanggal_supervisi']));
                
                if (isset($jadwal['hasil']['nilai_akhir'])) {
                    $sheet->setCellValue('G' . $row, number_format((float)$jadwal['hasil']['nilai_akhir'], 2, '.', ''));
                    $sheet->setCellValue('H' . $row, $jadwal['hasil']['ketercapaian'] ?? '');
                } else {
                    $sheet->setCellValue('G' . $row, '-');
                    $sheet->setCellValue('H' . $row, '-');
                }
                
                $row++;
            }
            
            // Auto-size columns
            foreach (range('A', 'H') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Freeze the first row
            $sheet->freezePane('A' . ($headerRow + 1));
            
            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="laporan-hasil-supervisi-' . date('Y-m-d') . '.xlsx"');
            header('Cache-Control: max-age=0');
            
            // Create Excel writer
            $writer = new Xlsx($spreadsheet);
            
            // Fix for Excel issue - ensure all data is written correctly
            $writer->setPreCalculateFormulas(false);
            
            // Clear any previous output
            ob_end_clean();
            
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Excel Export Error: ' . $e->getMessage());
            throw $e;
        }
    }
    public function batchExcel()
    {
        try {
            $selectedIds = $this->request->getPost('selected_ids');
            $tahun_ajar_id = $this->request->getPost('tahun_ajar_id');
            $status = $this->request->getPost('status');
            
            if (empty($selectedIds) || !is_array($selectedIds)) {
                return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk diexport');
            }
            
            $jadwalModel = new JadwalSupervisiModel();
            $tahunAjarModel = new TahunAjarModel();
            $guruModel = new GuruModel();
            $hasilModel = new HasilSupervisiModel();
            $db = \Config\Database::connect();
            
            // Get selected schedules
            $jadwalQuery = $jadwalModel
                ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.mata_pelajaran, kelas.nama_kelas')
                ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
                ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
                ->whereIn('jadwal_supervisi.id', $selectedIds);
                
            if (!empty($tahun_ajar_id) && $tahun_ajar_id !== 'all') {
                $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
            }
            
            if (!empty($status) && $status !== 'all') {
                $jadwalQuery->where('jadwal_supervisi.status', $status);
            }
            
            $jadwalQuery->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC');
            
            $jadwals = $jadwalQuery->findAll();
            
            // Process results for each schedule
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
            
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("Sistem Supervisi")
                ->setLastModifiedBy("Sistem Supervisi")
                ->setTitle("Laporan Hasil Supervisi")
                ->setSubject("Laporan Hasil Supervisi")
                ->setDescription("Laporan hasil supervisi guru");
                
            // Header
            $sheet->setCellValue('A1', 'LAPORAN HASIL SUPERVISI');
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Filter info
            $filterText = 'Filter: ';
            if ($tahun_ajar_id) {
                $tahunAjar = $tahunAjarModel->find($tahun_ajar_id);
                if ($tahunAjar) {
                    $filterText .= 'Tahun Ajaran ' . $tahunAjar['tahun_ajar'] . ' - ' . $tahunAjar['semester'];
                }
            }
            
            if ($status) {
                $filterText .= ($tahun_ajar_id ? ', ' : '') . 'Status ' . $status;
            }
            
            if (!$tahun_ajar_id && !$status) {
                $filterText .= 'Semua Data';
            }
            
            $sheet->setCellValue('A2', $filterText);
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Table header
            $headerRow = 4;
            $sheet->setCellValue('A' . $headerRow, 'No');
            $sheet->setCellValue('B' . $headerRow, 'Tahun Ajaran');
            $sheet->setCellValue('C' . $headerRow, 'Nama Guru');
            $sheet->setCellValue('D' . $headerRow, 'Mata Pelajaran');
            $sheet->setCellValue('E' . $headerRow, 'Kelas');
            $sheet->setCellValue('F' . $headerRow, 'Tanggal Supervisi');
            $sheet->setCellValue('G' . $headerRow, 'Nilai Akhir');
            $sheet->setCellValue('H' . $headerRow, 'Ketercapaian');
            
            // Style table header
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4e73df');
            $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
            
            // Table data
            $row = $headerRow + 1;
            $no = 1;
            foreach ($completedJadwals as $jadwal) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $jadwal['tahun_ajar'] . ' - ' . $jadwal['semester']);
                $sheet->setCellValue('C' . $row, $jadwal['nama_guru']);
                $sheet->setCellValue('D' . $row, $jadwal['mata_pelajaran']);
                $sheet->setCellValue('E' . $row, $jadwal['nama_kelas'] ?? $jadwal['kelas'] ?? '-');
                $sheet->setCellValue('F' . $row, format_tanggal_indonesia($jadwal['tanggal_supervisi']));
                
                if (isset($jadwal['hasil']['nilai_akhir'])) {
                    $sheet->setCellValue('G' . $row, number_format((float)$jadwal['hasil']['nilai_akhir'], 2, '.', ''));
                    $sheet->setCellValue('H' . $row, $jadwal['hasil']['ketercapaian'] ?? '');
                } else {
                    $sheet->setCellValue('G' . $row, '-');
                    $sheet->setCellValue('H' . $row, '-');
                }
                
                $row++;
            }
            
            // Auto-size columns
            foreach (range('A', 'H') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Freeze the first row
            $sheet->freezePane('A' . ($headerRow + 1));
            
            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="laporan-hasil-supervisi-batch.xlsx"');
            header('Cache-Control: max-age=0');
            
            // Create Excel writer
            $writer = new Xlsx($spreadsheet);
            
            // Fix for Excel issue - ensure all data is written correctly
            $writer->setPreCalculateFormulas(false);
            
            // Clear any previous output
            ob_end_clean();
            
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Batch Excel Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage());
        }
    }
    public function exportExcelDetail($id)
    {
        try {
            $jadwalModel = new JadwalSupervisiModel();
            $hasilModel = new HasilSupervisiModel();
            $db = \Config\Database::connect();
            
            // Get schedule with related data
            $schedule = $jadwalModel
                ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru')
                ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
                ->find($id);
                
            if (!$schedule) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
            
            // Get hasil records for this schedule
            $hasilList = $hasilModel->where('jadwal_supervisi_id', $id)->findAll();
            
            // Get detail results grouped by jenis_penilaian_id
            $detailResults = [];
            foreach ($hasilList as $hasil) {
                $details = $db->table('detail_hasil_penilaian dhp')
                    ->select('dhp.*, ap.nama_aspek')
                    ->join('aspek_penilaian ap', 'ap.id = dhp.aspek_penilaian_id')
                    ->where('dhp.hasil_supervisi_id', $hasil['id'])
                    ->get()
                    ->getResultArray();
                    
                $detailResults[$hasil['jenis_penilaian_id']] = $details;
            }
            
            // Clean output buffer to prevent issues
            if (ob_get_length()) {
                ob_clean();
            }
            
            // Set headers for Excel export
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $filename = 'detail-hasil-supervisi-' . ($schedule['nama_guru'] ?? 'guru') . '-' . date('Y-m-d-H-i-s') . '.xlsx';
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
            
            // Create new PHPExcel object
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("Sistem Supervisi")
                ->setLastModifiedBy("Sistem Supervisi")
                ->setTitle("Detail Hasil Supervisi")
                ->setSubject("Detail Hasil Supervisi")
                ->setDescription("Detail hasil supervisi guru");
            
            // Set sheet title
            $sheet->setTitle('Detail Hasil Supervisi');
            
            // Informasi Guru section
            $sheet->setCellValue('A1', 'DETAIL HASIL SUPERVISI');
            $sheet->mergeCells('A1:D1');
            
            $sheet->setCellValue('A3', 'Nama Guru');
            $sheet->setCellValue('B3', ': ' . ($schedule['nama_guru'] ?? ''));
            
            $sheet->setCellValue('A4', 'NIP');
            $sheet->setCellValue('B4', ': ' . ($schedule['nip_guru'] ?? ''));
            
            $sheet->setCellValue('A5', 'Mata Pelajaran');
            $sheet->setCellValue('B5', ': ' . ($schedule['mata_pelajaran'] ?? ''));
            
            $sheet->setCellValue('A6', 'Tanggal Supervisi');
            $sheet->setCellValue('B6', ': ' . format_tanggal_indonesia($schedule['tanggal_supervisi']));
            
            $sheet->setCellValue('A7', 'Kelas');
            $sheet->setCellValue('B7', ': ' . ($schedule['kelas'] ?? ''));
            
            // Style for header
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Style for labels
            $sheet->getStyle('A3:A7')->getFont()->setBold(true);
            
            // Assessment Results section
            $currentRow = 9;
            if (!empty($hasilList)) {
                foreach ($hasilList as $hasil) {
                    $jenisId = $hasil['jenis_penilaian_id'];
                    $jenisNama = '';
                    switch($jenisId) {
                        case 1: $jenisNama = 'Administrasi Guru'; break;
                        case 2: $jenisNama = 'Proses Pembelajaran'; break;
                        case 3: $jenisNama = 'Evaluasi Pembelajaran'; break;
                        case 4: $jenisNama = 'Pengembangan Diri'; break;
                        default: $jenisNama = 'Komponen Lain';
                    }
                    
                    // Section header
                    $sheet->setCellValue('A' . $currentRow, $jenisNama);
                    $sheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
                    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                    $sheet->getStyle('A' . $currentRow)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFD3D3D3');
                    $currentRow++;
                    
                    // Table header for this section
                    $sheet->setCellValue('A' . $currentRow, 'No');
                    $sheet->setCellValue('B' . $currentRow, 'Aspek Penilaian');
                    $sheet->setCellValue('C' . $currentRow, 'Skor');
                    $sheet->setCellValue('D' . $currentRow, 'Catatan');
                    $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getFont()->setBold(true);
                    $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFE0E0E0');
                    $currentRow++;
                    
                    // Table data for this section
                    if (isset($detailResults[$jenisId]) && !empty($detailResults[$jenisId])) {
                        $no = 1;
                        foreach ($detailResults[$jenisId] as $detail) {
                            $sheet->setCellValue('A' . $currentRow, $no++);
                            $sheet->setCellValue('B' . $currentRow, $detail['nama_aspek'] ?? '');
                            $sheet->setCellValue('C' . $currentRow, $detail['skor'] ?? '0');
                            $sheet->setCellValue('D' . $currentRow, $detail['catatan'] ?? '');
                            $currentRow++;
                        }
                    } else {
                        $sheet->setCellValue('A' . $currentRow, '');
                        $sheet->setCellValue('B' . $currentRow, 'Tidak ada detail penilaian');
                        $sheet->mergeCells('B' . $currentRow . ':D' . $currentRow);
                        $currentRow++;
                    }
                    
                    // Add some space between sections
                    $currentRow++;
                }
            } else {
                $sheet->setCellValue('A' . $currentRow, 'Belum ada hasil penilaian');
                $currentRow++;
            }
            
            // Auto-size columns
            foreach (range('A', 'D') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Create Excel writer
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            
            // Fix for Excel issue - ensure all data is written correctly
            $writer->setPreCalculateFormulas(false);
            
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Excel Detail Export Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
