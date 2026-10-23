<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JenisPenilaianModel;
use App\Models\AspekPenilaianModel;

class AspekPenilaianController extends BaseController
{
    protected $jenisPenilaianModel;
    protected $aspekPenilaianModel;
    protected $db;

    public function __construct()
    {
        $this->jenisPenilaianModel = new JenisPenilaianModel();
        $this->aspekPenilaianModel = new AspekPenilaianModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        return redirect()->to('/admin/instrumen?tab=aspek');
    }

    public function store()
    {
        try {
            $data = [
                'jenis_penilaian_id' => $this->request->getPost('jenis_penilaian_id'),
                'nama_aspek'         => trim($this->request->getPost('nama_aspek') ?? ''),
                'fokus_perilaku'     => trim($this->request->getPost('fokus_perilaku') ?? ''),
                'perilaku_dianjurkan'=> $this->request->getPost('perilaku_dianjurkan') ?? null,
                'perilaku_dihindari' => $this->request->getPost('perilaku_dihindari') ?? null,
                'urutan'             => (int) ($this->request->getPost('urutan') ?? 1),
                'tipe_skala'         => $this->request->getPost('tipe_skala') ?: 'skor_1_4',
                'created_at'         => date('Y-m-d H:i:s')
            ];

            if (empty($data['nama_aspek'])) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Nama aspek penilaian tidak boleh kosong',
                        'token'   => csrf_hash()
                    ]);
                }
                return redirect()->back()->withInput()->with('error', 'Nama aspek penilaian tidak boleh kosong');
            }

            $id = $this->aspekPenilaianModel->insert($data);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Aspek penilaian berhasil ditambahkan',
                    'id'      => $id,
                    'token'   => csrf_hash()
                ]);
            }

            return redirect()->to('/admin/instrumen?tab=aspek')->with('success', 'Aspek penilaian berhasil ditambahkan');
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal menambahkan aspek penilaian: ' . $e->getMessage(),
                    'token'   => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan aspek: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        try {
            $existing = $this->aspekPenilaianModel->find($id);
            if (!$existing) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Data aspek penilaian tidak ditemukan',
                        'token'   => csrf_hash()
                    ]);
                }
                return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Aspek penilaian tidak ditemukan');
            }

            $data = [
                'jenis_penilaian_id' => $this->request->getPost('jenis_penilaian_id') ?? $existing['jenis_penilaian_id'],
                'nama_aspek'         => trim($this->request->getPost('nama_aspek') ?? $existing['nama_aspek']),
                'fokus_perilaku'     => $this->request->getPost('fokus_perilaku') !== null ? trim($this->request->getPost('fokus_perilaku')) : ($existing['fokus_perilaku'] ?? null),
                'perilaku_dianjurkan'=> $this->request->getPost('perilaku_dianjurkan') !== null ? $this->request->getPost('perilaku_dianjurkan') : ($existing['perilaku_dianjurkan'] ?? null),
                'perilaku_dihindari' => $this->request->getPost('perilaku_dihindari') !== null ? $this->request->getPost('perilaku_dihindari') : ($existing['perilaku_dihindari'] ?? null),
                'urutan'             => $this->request->getPost('urutan') !== null ? (int)$this->request->getPost('urutan') : (int)($existing['urutan'] ?? 1),
                'tipe_skala'         => $this->request->getPost('tipe_skala') ?: ($existing['tipe_skala'] ?? 'skor_1_4')
            ];

            $this->aspekPenilaianModel->update($id, $data);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Aspek penilaian berhasil diperbarui',
                    'token'   => csrf_hash()
                ]);
            }

            return redirect()->to('/admin/instrumen?tab=aspek')->with('success', 'Aspek penilaian berhasil diupdate');
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal memperbarui aspek: ' . $e->getMessage(),
                    'token'   => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate aspek: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            // Cek apakah aspek sedang digunakan pada detail penilaian
            $detailCount = $this->db->table('detail_hasil_penilaian')
                ->where('aspek_penilaian_id', $id)
                ->countAllResults();

            if ($detailCount > 0) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Tidak dapat menghapus butir aspek penilaian ini karena telah memiliki riwayat penilaian supervisi (' . $detailCount . ' data).',
                        'token'   => csrf_hash()
                    ]);
                }
                return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Tidak dapat menghapus aspek ini karena masih digunakan dalam riwayat penilaian');
            }

            $this->aspekPenilaianModel->delete($id);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Aspek penilaian berhasil dihapus',
                    'token'   => csrf_hash()
                ]);
            }

            return redirect()->to('/admin/instrumen?tab=aspek')->with('success', 'Aspek penilaian berhasil dihapus');
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal menghapus aspek: ' . $e->getMessage(),
                    'token'   => csrf_hash()
                ]);
            }
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Gagal menghapus aspek: ' . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi Cerdas Master Aspek Penilaian Baku
     * Mengambil master butir aspek baku dari Kurikulum Merdeka (8 Indikator PMM & 2 Telaah Perangkat)
     * serta 4 Komponen Supervisi Administrasi & Pelaksanaan K13.
     */
    public function sync()
    {
        $isAjax = $this->request->isAJAX();

        try {
            $masterData = $this->getMasterAspekData();
            $now = date('Y-m-d H:i:s');
            $inserted = 0;
            $updated  = 0;

            // Cache ID jenis penilaian berdasarkan nama untuk pemetaan fleksibel
            $allJenis = $this->jenisPenilaianModel->findAll();
            $jenisMapByName = [];
            $jenisMapById = [];
            foreach ($allJenis as $j) {
                $namaKey = strtoupper(trim($j['nama'] ?? ''));
                $jenisMapByName[$namaKey] = $j['id'];
                $jenisMapById[$j['id']] = $j;
            }

            foreach ($masterData as $item) {
                // Cari ID jenis penilaian
                $targetJenisId = null;

                if (!empty($item['jenis_id']) && isset($jenisMapById[$item['jenis_id']])) {
                    $targetJenisId = $item['jenis_id'];
                } elseif (!empty($item['jenis_nama'])) {
                    $searchKey = strtoupper(trim($item['jenis_nama']));
                    if (isset($jenisMapByName[$searchKey])) {
                        $targetJenisId = $jenisMapByName[$searchKey];
                    } else {
                        // Fuzzy search by substring
                        foreach ($jenisMapByName as $name => $id) {
                            if (strpos($name, $searchKey) !== false || strpos($searchKey, $name) !== false) {
                                $targetJenisId = $id;
                                break;
                            }
                        }
                    }
                }

                if (!$targetJenisId) {
                    continue;
                }

                // Cek apakah butir aspek ini sudah ada untuk jenis penilaian tersebut
                $existing = $this->aspekPenilaianModel
                    ->where('jenis_penilaian_id', $targetJenisId)
                    ->where('nama_aspek', $item['nama_aspek'])
                    ->first();

                $aspekPayload = [
                    'jenis_penilaian_id' => $targetJenisId,
                    'nama_aspek'         => $item['nama_aspek'],
                    'fokus_perilaku'     => $item['fokus_perilaku'] ?? null,
                    'perilaku_dianjurkan'=> $item['perilaku_dianjurkan'] ?? null,
                    'perilaku_dihindari' => $item['perilaku_dihindari'] ?? null,
                    'tipe_skala'         => $item['tipe_skala'] ?? 'skor_1_4',
                    'urutan'             => (int) ($item['urutan'] ?? 1),
                ];

                if ($existing) {
                    $this->aspekPenilaianModel->update($existing['id'], $aspekPayload);
                    $updated++;
                } else {
                    $aspekPayload['created_at'] = $now;
                    $this->aspekPenilaianModel->insert($aspekPayload);
                    $inserted++;
                }
            }

            if ($isAjax) {
                return $this->response->setJSON([
                    'status'   => 'success',
                    'message'  => "Sinkronisasi berhasil! {$inserted} butir aspek baru ditambahkan dan {$updated} butir diperbarui.",
                    'inserted' => $inserted,
                    'updated'  => $updated,
                    'token'    => csrf_hash()
                ]);
            }

            return redirect()->to('/admin/instrumen?tab=aspek')->with('success', "Sinkronisasi berhasil! {$inserted} aspek baru ditambahkan dan {$updated} diperbarui.");
        } catch (\Exception $e) {
            log_message('error', 'Error sync AspekPenilaian: ' . $e->getMessage());
            if ($isAjax) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage(),
                    'token'   => csrf_hash()
                ]);
            }
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage());
        }
    }

    /**
     * Definisi Master Aspek Baku Supervisi & Kurikulum Merdeka
     */
    protected function getMasterAspekData(): array
    {
        return [
            // =========================================================================
            // JENIS 1: ADMINISTRASI GURU / PERENCANAAN PEMBELAJARAN
            // =========================================================================
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'Kalender Pendidikan', 'urutan' => 1, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Kesesuaian alokasi waktu efektif dan kalender akademik sekolah'],
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'Program Tahunan', 'urutan' => 2, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Distribusi alokasi materi pokok dan kompetensi dalam rentang satu tahun ajaran'],
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'Program Semester', 'urutan' => 3, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Rincian jadwal tatap muka dan materi per minggu dalam satu semester'],
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'Silabus', 'urutan' => 4, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Kelengkapan komponen silabus dan indikator pencapaian kompetensi'],
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'Modul Ajar', 'urutan' => 5, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Ketersediaan Modul Ajar/RPP yang terintegrasi profil pelajar dan diferensiasi'],
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'Jadwal Tatap Muka', 'urutan' => 6, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Kesesuaian pembagian jam mengajar mingguan'],
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'Agenda Harian', 'urutan' => 7, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pencatatan rekam jejak pelaksanaan kegiatan belajar mengajar harian'],
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'Daftar Nilai', 'urutan' => 8, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Rekapitulasi nilai formatif, sumatif, dan catatan perkembangan peserta didik'],
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'KKM', 'urutan' => 9, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Penetapan kriteria ketercapaian tujuan pembelajaran atau KKM'],
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'Absensi Peserta Didik', 'urutan' => 10, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pencatatan kehadiran siswa secara tertib dan berkelanjutan'],
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'Buku Pegangan Guru', 'urutan' => 11, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Ketersediaan buku panduan guru resmi dan referensi pembelajaran relevan'],
            ['jenis_id' => 1, 'jenis_nama' => 'SUPERVISI ADMINISTRASI GURU', 'nama_aspek' => 'Buku Teks Siswa', 'urutan' => 12, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Ketersediaan dan pemanfaatan buku teks peserta didik dalam KBM'],

            // =========================================================================
            // JENIS 2: PROSES PEMBELAJARAN
            // =========================================================================
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Pendahuluan', 'urutan' => 1, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Apersepsi, motivasi, dan penyampaian tujuan pembelajaran'],
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Kegiatan Inti Eksplorasi', 'urutan' => 2, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pemberian stimulus dan penemuan konsep awal oleh peserta didik'],
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Kegiatan Inti Elaborasi', 'urutan' => 3, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Aktivitas eksplorasi mandiri, diskusi kelompok, dan pengerjaan tugas bermakna'],
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Kegiatan Inti Konfirmasi', 'urutan' => 4, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pemberian umpan balik dan pelurusan konsep oleh pendidik'],
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Penutup', 'urutan' => 5, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Refleksi bersama murid, kesimpulan, dan tindak lanjut sesi pembelajaran'],
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Metode Pembelajaran', 'urutan' => 6, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Ketepatan pemilihan metode yang interaktif dan berpusat pada murid'],
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Media Pembelajaran', 'urutan' => 7, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pemanfaatan media ajar visual, digital, atau alat peraga yang relevan'],
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Sumber Belajar', 'urutan' => 8, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Penggunaan sumber belajar yang bervariasi dan kontekstual'],
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Evaluasi', 'urutan' => 9, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pelaksanaan asesmen proses dan pengukuran ketercapaian tujuan'],
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Karakteristik Siswa', 'urutan' => 10, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Perhatian terhadap keunikan dan kebutuhan belajar seluruh murid'],
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Interaksi Guru-Murid', 'urutan' => 11, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Membangun komunikasi positif, hangat, dan iklim saling menghargai'],
            ['jenis_id' => 2, 'jenis_nama' => 'SUPERVISI PROSES PEMBELAJARAN', 'nama_aspek' => 'Penggunaan Waktu', 'urutan' => 12, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Efisiensi dan ketepatan pengelolaan alokasi waktu selama pembelajaran'],

            // =========================================================================
            // JENIS 3: EVALUASI PEMBELAJARAN
            // =========================================================================
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Prinsip Penilaian', 'urutan' => 1, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Kesesuaian penilaian dengan prinsip sahih, objektif, adil, dan terpadu'],
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Teknik Penilaian', 'urutan' => 2, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Variasi teknik tes tertulis, lisan, kinerja, dan observasi perilaku'],
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Bentuk Instrumen', 'urutan' => 3, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Kelengkapan rubrik, kisi-kisi soal, dan pedoman penskoran'],
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Butir Soal', 'urutan' => 4, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Kualitas butir soal sesuai kaidah penulisan dan tingkat kognitif'],
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Analisis Butir Soal', 'urutan' => 5, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pelaksanaan analisis tingkat kesukaran dan daya pembeda butir soal'],
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Analisis Hasil Penilaian', 'urutan' => 6, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pemetaan ketuntasan belajar per individu dan kelompok murid'],
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Tindak Lanjut Hasil Penilaian', 'urutan' => 7, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Penyusunan program remedial dan pengayaan yang terarah'],
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Pelaporan Hasil Penilaian', 'urutan' => 8, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Transparansi dan ketepatan pelaporan capaian nilai kepada murid/wali'],
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Manfaat Hasil Penilaian', 'urutan' => 9, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pemanfaatan hasil asesmen untuk perbaikan mutu pembelajaran selanjutnya'],
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Keterlibatan Peserta Didik', 'urutan' => 10, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pelaksanaan penilaian diri (self assessment) dan penilaian antarteman'],
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Portofolio', 'urutan' => 11, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Dokumentasi kumpulan karya dan perkembangan belajar murid'],
            ['jenis_id' => 3, 'jenis_nama' => 'SUPERVISI EVALUASI PEMBELAJARAN', 'nama_aspek' => 'Karya Siswa', 'urutan' => 12, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pameran atau apresiasi terhadap hasil karya/projek peserta didik'],

            // =========================================================================
            // JENIS 4: PENGEMBANGAN DIRI GURU
            // =========================================================================
            ['jenis_id' => 4, 'jenis_nama' => 'SUPERVISI PENGEMBANGAN DIRI', 'nama_aspek' => 'Mengikuti Pelatihan', 'urutan' => 1, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Keikutsertaan dalam diklat fungsional dan pelatihan keprofesian berkelanjutan'],
            ['jenis_id' => 4, 'jenis_nama' => 'SUPERVISI PENGEMBANGAN DIRI', 'nama_aspek' => 'Mengikuti Workshop', 'urutan' => 2, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Partisipasi aktif dalam kegiatan lokakarya dan seminar pendidikan'],
            ['jenis_id' => 4, 'jenis_nama' => 'SUPERVISI PENGEMBANGAN DIRI', 'nama_aspek' => 'Melaksanakan Penelitian Tindakan Kelas', 'urutan' => 3, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pelaksanaan PTK untuk peningkatan kualitas pembelajaran di kelas'],
            ['jenis_id' => 4, 'jenis_nama' => 'SUPERVISI PENGEMBANGAN DIRI', 'nama_aspek' => 'Menyusun Karya Tulis Ilmiah', 'urutan' => 4, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Penyusunan artikel, buku, atau modul yang dipublikasikan'],
            ['jenis_id' => 4, 'jenis_nama' => 'SUPERVISI PENGEMBANGAN DIRI', 'nama_aspek' => 'Melaksanakan Pengabdian Kepada Masyarakat', 'urutan' => 5, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Keterlibatan dalam kegiatan sosial kemasyarakatan dan kepedulian lingkungan'],
            ['jenis_id' => 4, 'jenis_nama' => 'SUPERVISI PENGEMBANGAN DIRI', 'nama_aspek' => 'Mengikuti Organisasi Profesi', 'urutan' => 6, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Keaktifan dalam forum MGMP, KKG, PGRI, atau komunitas belajar guru'],
            ['jenis_id' => 4, 'jenis_nama' => 'SUPERVISI PENGEMBANGAN DIRI', 'nama_aspek' => 'Mengembangkan Bahan Ajar', 'urutan' => 7, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Inovasi pembuatan lembar kerja, modul digital, atau video tutorial'],
            ['jenis_id' => 4, 'jenis_nama' => 'SUPERVISI PENGEMBANGAN DIRI', 'nama_aspek' => 'Melaksanakan Bimbingan Konseling', 'urutan' => 8, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pemberian pendampingan personal bagi siswa yang mengalami kendala'],
            ['jenis_id' => 4, 'jenis_nama' => 'SUPERVISI PENGEMBANGAN DIRI', 'nama_aspek' => 'Melaksanakan Bimbingan Penelitian', 'urutan' => 9, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Pembimbingan karya ilmiah remaja atau lomba kompetisi peserta didik'],
            ['jenis_id' => 4, 'jenis_nama' => 'SUPERVISI PENGEMBANGAN DIRI', 'nama_aspek' => 'Melaksanakan Bimbingan Akademik', 'urutan' => 10, 'tipe_skala' => 'skor_1_4', 'fokus_perilaku' => 'Bimbingan tutor sebaya dan pendalaman materi ujian'],

            // =========================================================================
            // 8 INDIKATOR OBSERVASI KELAS PMM (JENIS 5 - 12)
            // =========================================================================
            // 1. PENERAPAN DISIPLIN POSITIF (ID: 5)
            [
                'jenis_id' => 5,
                'jenis_nama' => 'PENERAPAN DISIPLIN POSITIF',
                'nama_aspek' => 'Refleksi Dinamika Kelas',
                'fokus_perilaku' => 'Guru melakukan refleksi dinamika kelas untuk menerapkan kesepakatan kelas',
                'perilaku_dianjurkan' => "• Guru mengajak peserta didik melakukan refleksi dinamika kelas mengacu kesepakatan kelas\n• Guru menunjukkan kesediaan mendengarkan pandangan peserta didik tentang dinamika kelas\n• Guru bersikap adaptif sehingga bersedia mengubah kesepakatan kelas bila diperlukan",
                'perilaku_dihindari' => "• Guru mengabaikan pendapat peserta didik tentang apa yang terjadi di kelas\n• Guru bersikap defensif dalam menyikapi umpan balik dari peserta didik terkait kedisiplinan\n• Guru menerapkan hukuman fisik atau intimidasi verbal",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 1
            ],
            [
                'jenis_id' => 5,
                'jenis_nama' => 'PENERAPAN DISIPLIN POSITIF',
                'nama_aspek' => 'Penguatan Positif Perilaku',
                'fokus_perilaku' => 'Guru melakukan penguatan positif terhadap perilaku yang sesuai atau mendukung kesepakatan kelas',
                'perilaku_dianjurkan' => "• Guru memberi pujian terhadap perilaku peserta didik yang sesuai kesepakatan kelas\n• Guru memberi penguatan positif dengan beragam cara yang bermakna\n• Guru mengakui suatu perilaku positif secara spesifik dan menjelaskan alasannya",
                'perilaku_dihindari' => "• Guru tidak konsisten dalam memberikan penguatan positif\n• Guru memberikan reward berlebihan yang mengurangi motivasi intrinsik\n• Guru membanding-bandingkan pencapaian antar murid secara tidak adil",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 2
            ],
            [
                'jenis_id' => 5,
                'jenis_nama' => 'PENERAPAN DISIPLIN POSITIF',
                'nama_aspek' => 'Fasilitasi Restitusi Pelanggaran',
                'fokus_perilaku' => 'Guru memfasilitasi peserta didik menyadari konsekuensi dan memperbaiki perilaku melanggarnya (restitusi)',
                'perilaku_dianjurkan' => "• Guru membantu peserta didik menyadari konsekuensi dari perilaku melanggarnya\n• Guru mendengarkan sudut pandang peserta didik terhadap perilaku melanggarnya\n• Guru memberikan dukungan pada peserta didik dalam melakukan perbaikan perilakunya",
                'perilaku_dihindari' => "• Guru langsung memberikan hukuman tanpa menanyakan alasan\n• Guru mempermalukan peserta didik di depan teman-temannya\n• Guru bersikap acuh tak acuh terhadap pelanggaran berulang",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 3
            ],

            // 2. KETERATURAN SUASANA KELAS (ID: 6)
            [
                'jenis_id' => 6,
                'jenis_nama' => 'KETERATURAN SUASANA KELAS',
                'nama_aspek' => 'Komunikasi Positif Kondusif',
                'fokus_perilaku' => 'Guru melakukan komunikasi positif untuk membangun suasana kelas yang kondusif',
                'perilaku_dianjurkan' => "• Guru memanggil murid dengan menyebut namanya secara hangat\n• Guru menyampaikan harapan positif terhadap iklim belajar di kelas\n• Guru melakukan aktivitas pencair suasana (ice breaking) yang relevan",
                'perilaku_dihindari' => "• Guru memanggil murid dengan sebutan bernada merendahkan/label negatif\n• Guru menggunakan kata-kata bernada sarkasme atau ancaman\n• Guru mengabaikan ketegangan suasana kelas",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 1
            ],
            [
                'jenis_id' => 6,
                'jenis_nama' => 'KETERATURAN SUASANA KELAS',
                'nama_aspek' => 'Strategi Pengelompokan Efektif',
                'fokus_perilaku' => 'Guru melakukan strategi pengelompokan untuk mengaktifkan keterlibatan peserta didik',
                'perilaku_dianjurkan' => "• Guru mengelompokkan murid dengan menyampaikan tujuannya secara transparan\n• Guru menyediakan beragam peran dalam kelompok agar semua anggota aktif\n• Guru mengajak murid berinteraksi dan berkolaborasi dalam kelompok",
                'perilaku_dihindari' => "• Guru membiarkan kelompok didominasi hanya oleh murid tertentu\n• Guru membagi kelompok tanpa mempertimbangkan kebutuhan dan dinamika belajar\n• Guru tidak mendampingi proses interaksi antarkelompok",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 2
            ],
            [
                'jenis_id' => 6,
                'jenis_nama' => 'KETERATURAN SUASANA KELAS',
                'nama_aspek' => 'Mengingatkan Aturan/Kesepakatan',
                'fokus_perilaku' => 'Guru membuat dan mengingatkan aturan/kesepakatan kelas',
                'perilaku_dianjurkan' => "• Guru membuat aturan/kesepakatan kelas bersama peserta didik\n• Guru mengajak peserta didik mengingat aturan/kesepakatan yang telah disepakati\n• Guru mengajak peserta didik menilai seberapa efektif pelaksanaan aturan kelas",
                'perilaku_dihindari' => "• Guru menetapkan aturan sepihak tanpa melibatkan murid\n• Guru mengabaikan pelanggaran kesepakatan kelas\n• Guru membuat aturan yang tidak realistis untuk dijalankan",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 3
            ],

            // 3. EKSPEKTASI PADA PESERTA DIDIK (ID: 7)
            [
                'jenis_id' => 7,
                'jenis_nama' => 'EKSPEKTASI PADA PESERTA DIDIK',
                'nama_aspek' => 'Komunikasi Harapan Masa Depan',
                'fokus_perilaku' => 'Guru mengkomunikasikan harapannya yang tinggi terhadap masa depan seluruh peserta didiknya',
                'perilaku_dianjurkan' => "• Guru mengenal dan menyebutkan potensi unik peserta didik\n• Guru memberikan contoh tokoh inspiratif yang relevan dengan kehidupan murid\n• Guru menyampaikan keyakinan bahwa semua murid mampu berkembang mencapai potensinya",
                'perilaku_dihindari' => "• Guru meremehkan potensi atau cita-cita peserta didik\n• Guru bersikap apatis terhadap masa depan murid\n• Guru melabeli murid dengan predikat yang membatasi perkembangannya",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 1
            ],
            [
                'jenis_id' => 7,
                'jenis_nama' => 'EKSPEKTASI PADA PESERTA DIDIK',
                'nama_aspek' => 'Harapan Positif yang Setara',
                'fokus_perilaku' => 'Guru mengkomunikasikan harapan positif terhadap semua peserta didik secara setara dan tanpa diskriminasi',
                'perilaku_dianjurkan' => "• Guru memberikan perhatian dan kesempatan setara kepada seluruh murid\n• Guru memfasilitasi murid yang butuh bimbingan lebih tanpa merendahkan martabatnya\n• Guru mengapresiasi keberagaman latar belakang dan kemampuan murid",
                'perilaku_dihindari' => "• Guru hanya memberi panggung pada murid berprestasi tinggi\n• Guru mendiskriminasi murid berdasarkan latar belakang, gender, atau status ekonomi\n• Guru bersikap pilih kasih dalam proses pembelajaran",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 2
            ],
            [
                'jenis_id' => 7,
                'jenis_nama' => 'EKSPEKTASI PADA PESERTA DIDIK',
                'nama_aspek' => 'Tantangan Belajar Bermakna',
                'fokus_perilaku' => 'Guru memberikan tantangan yang bermakna disertai motivasi untuk mencapainya',
                'perilaku_dianjurkan' => "• Guru memberi tugas yang menantang namun terjangkau dengan kemampuan murid\n• Guru memandu proses penyelesaian tantangan secara bertahap (scaffolding)\n• Guru memotivasi murid agar tekun dan pantang menyerah saat menghadapi kendala",
                'perilaku_dihindari' => "• Guru memberikan tugas yang terlalu mudah sehingga tidak menantang, atau terlalu sulit tanpa bimbingan\n• Guru membiarkan murid menyerah tanpa ada upaya dukungan\n• Guru membebani murid dengan tugas hafalan mekanis yang tidak bermakna",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 3
            ],

            // 4. PERHATIAN DAN KEPEDULIAN (ID: 8)
            [
                'jenis_id' => 8,
                'jenis_nama' => 'PERHATIAN DAN KEPEDULIAN',
                'nama_aspek' => 'Empati dan Pemahaman Murid',
                'fokus_perilaku' => 'Guru menunjukkan empati untuk mendapatkan pemahaman utuh tentang peserta didik',
                'perilaku_dianjurkan' => "• Guru mendengarkan aktif saat murid menyampaikan pendapat atau keluh kesah\n• Guru peka terhadap perubahan emosi atau perilaku murid di kelas\n• Guru mencari tahu akar masalah murid saat mengalami kesulitan belajar",
                'perilaku_dihindari' => "• Guru mengabaikan keluhan, kesedihan, atau kekhawatiran murid\n• Guru langsung menghakimi perasaan atau perilaku murid\n• Guru bersikap dingin, sinis, dan menjaga jarak emosional",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 1
            ],
            [
                'jenis_id' => 8,
                'jenis_nama' => 'PERHATIAN DAN KEPEDULIAN',
                'nama_aspek' => 'Responsif Kebutuhan Belajar',
                'fokus_perilaku' => 'Guru menunjukkan pemahaman terhadap kebutuhan, kondisi, dan karakteristik peserta didik',
                'perilaku_dianjurkan' => "• Guru memfasilitasi gaya belajar, minat, dan kecepatan belajar yang beragam\n• Guru memberikan fleksibilitas tugas sesuai kondisi spesifik murid\n• Guru merancang aktivitas pembelajaran yang aman, inklusif, dan ramah anak",
                'perilaku_dihindari' => "• Guru menyamaratakan seluruh murid tanpa diferensiasi metode\n• Guru memaksakan satu cara belajar yang tidak cocok bagi semua anak\n• Guru tidak memedulikan kebutuhan khusus peserta didik",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 2
            ],
            [
                'jenis_id' => 8,
                'jenis_nama' => 'PERHATIAN DAN KEPEDULIAN',
                'nama_aspek' => 'Apresiasi Usaha dan Proses',
                'fokus_perilaku' => 'Guru mengakui dan menghargai usaha yang ditunjukkan peserta didik',
                'perilaku_dianjurkan' => "• Guru mengapresiasi proses belajar dan daya juang murid, bukan hanya nilai akhir\n• Guru memberi penguatan atas kemajuan sekecil apapun yang dicapai murid\n• Guru merayakan keberanian murid mencoba hal baru atau menjawab tantangan",
                'perilaku_dihindari' => "• Guru hanya memuji murid yang mendapatkan nilai sempurna\n• Guru mengabaikan murid yang telah bekerja keras namun belum meraih hasil maksimal\n• Guru mencela atau menertawakan kesalahan murid",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 3
            ],

            // 5. UMPAN BALIK KONSTRUKTIF (ID: 9)
            [
                'jenis_id' => 9,
                'jenis_nama' => 'UMPAN BALIK KONSTRUKTIF',
                'nama_aspek' => 'Umpan Balik Spesifik & Terarah',
                'fokus_perilaku' => 'Guru memberikan umpan balik spesifik dan berorientasi tujuan',
                'perilaku_dianjurkan' => "• Guru menjelaskan letak kekuatan dan kekurangan karya murid secara rinci\n• Guru mengaitkan umpan balik dengan kriteria ketercapaian tujuan pembelajaran\n• Guru memberikan langkah konkret yang dapat diambil murid untuk perbaikan",
                'perilaku_dihindari' => "• Guru memberi umpan balik terlalu umum seperti 'Bagus' atau 'Salah' tanpa penjelasan\n• Guru tidak menjelaskan cara memperbaiki kesalahan yang dibuat\n• Guru mengomentari pribadi murid, bukan hasil karyanya",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 1
            ],
            [
                'jenis_id' => 9,
                'jenis_nama' => 'UMPAN BALIK KONSTRUKTIF',
                'nama_aspek' => 'Fokus pada Proses dan Growth Mindset',
                'fokus_perilaku' => 'Guru memberikan umpan balik yang fokus pada proses atau usaha peserta didik',
                'perilaku_dianjurkan' => "• Guru mengapresiasi strategi berpikir yang digunakan murid dalam menyelesaikan masalah\n• Guru mendiskusikan alternatif cara penyelesaian masalah bersama murid\n• Guru mendorong cara pandang bertumbuh (growth mindset) bahwa kesalahan adalah bagian dari belajar",
                'perilaku_dihindari' => "• Guru hanya membandingkan hasil kerja antar murid\n• Guru mengabaikan proses atau strategi yang sudah diupayakan murid\n• Guru fokus pada detail minor yang tidak esensial",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 2
            ],
            [
                'jenis_id' => 9,
                'jenis_nama' => 'UMPAN BALIK KONSTRUKTIF',
                'nama_aspek' => 'Diskusi Reflektif Hasil Belajar',
                'fokus_perilaku' => 'Guru menunjukkan kesediaan mendiskusikan umpan balik dengan peserta didik',
                'perilaku_dianjurkan' => "• Guru membuka sesi dialog terkait hasil asesmen dan respon umpan balik\n• Guru mengajak murid merefleksikan catatan perbaikan yang diterimanya\n• Guru mendengarkan penjelasan murid atas hasil pekerjaannya secara adil",
                'perilaku_dihindari' => "• Guru bersikap otoriter atas koreksi yang diberikan tanpa memberi ruang klarifikasi\n• Guru menolak mendengarkan penjelasan murid\n• Guru terburu-buru memberikan feedback tanpa memastikan murid memahaminya",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 3
            ],

            // 6. AKTIVITAS INTERAKTIF (ID: 10)
            [
                'jenis_id' => 10,
                'jenis_nama' => 'AKTIVITAS INTERAKTIF',
                'nama_aspek' => 'Peran Aktif Semua Murid',
                'fokus_perilaku' => 'Guru memfasilitasi kegiatan pembelajaran yang memberi peran pada semua peserta didik',
                'perilaku_dianjurkan' => "• Guru merancang aktivitas yang membagi peran secara adil dan terukur\n• Guru memastikan setiap murid memiliki tanggung jawab konkret dalam tugas\n• Guru memotivasi murid yang pendiam untuk berani mengambil peran aktif",
                'perilaku_dihindari' => "• Guru membiarkan murid tertentu pasif atau asyik sendiri\n• Guru hanya mengandalkan murid tertentu yang vokal\n• Guru mendominasi seluruh jalannya pembelajaran (teacher-centered murni)",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 1
            ],
            [
                'jenis_id' => 10,
                'jenis_nama' => 'AKTIVITAS INTERAKTIF',
                'nama_aspek' => 'Pertanyaan Pemantik Berpikir Kritis',
                'fokus_perilaku' => 'Guru mengajukan pertanyaan yang menstimulasi proses diskusi dan berpikir kritis',
                'perilaku_dianjurkan' => "• Guru memberikan pertanyaan pemantik terbuka tingkat tinggi (HOTS)\n• Guru memberi jeda waktu tunggu (wait time) yang memadai agar murid berpikir\n• Guru merespon jawaban murid dengan pertanyaan lanjutan yang mendalam",
                'perilaku_dihindari' => "• Guru hanya mengajukan pertanyaan ingatan tertutup (Ya/Tidak)\n• Guru langsung menjawab pertanyaannya sendiri tanpa memberi kesempatan murid berpikir\n• Guru memotong pembicaraan murid saat sedang merumuskan ide",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 2
            ],
            [
                'jenis_id' => 10,
                'jenis_nama' => 'AKTIVITAS INTERAKTIF',
                'nama_aspek' => 'Diskusi Kelompok Kritis & Inklusif',
                'fokus_perilaku' => 'Guru memfasilitasi terjadinya diskusi kelompok yang interaktif, kritis, dan inklusif',
                'perilaku_dianjurkan' => "• Guru memandu jalannya dialog antarmurid agar saling menghargai pendapat\n• Guru mendorong murid saling menanggapi gagasan rekan secara santun\n• Guru berkeliling mendampingi dinamika diskusi setiap kelompok",
                'perilaku_dihindari' => "• Guru duduk diam di meja guru selama murid berdiskusi kelompok\n• Guru membiarkan perdebatan menyimpang dari topik atau menjadi tidak santun\n• Guru membiarkan diskusi berjalan tanpa tujuan atau arahan hasil yang jelas",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 3
            ],

            // 7. INSTRUKSI PEMBELAJARAN (ID: 11)
            [
                'jenis_id' => 11,
                'jenis_nama' => 'INSTRUKSI PEMBELAJARAN',
                'nama_aspek' => 'Penjelasan Sesuai Pemahaman Awal',
                'fokus_perilaku' => 'Guru memberikan penjelasan yang mudah sesuai pemahaman awal peserta didik',
                'perilaku_dianjurkan' => "• Guru menggunakan bahasa yang jelas, lugas, dan mudah dicerna murid\n• Guru mengaitkan konsep baru dengan pengetahuan yang sudah dimiliki murid\n• Guru melakukan pengecekan pemahaman konsep (concept check) secara berkala",
                'perilaku_dihindari' => "• Guru menggunakan istilah teknis/jargon rumit tanpa penjelasan memadai\n• Guru berbicara terlalu cepat atau dengan intonasi monoton\n• Guru mengasumsikan seluruh murid paham tanpa melakukan asesmen awal",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 1
            ],
            [
                'jenis_id' => 11,
                'jenis_nama' => 'INSTRUKSI PEMBELAJARAN',
                'nama_aspek' => 'Contoh Kontekstual Nyata',
                'fokus_perilaku' => 'Guru memberikan contoh yang kontekstual dan relevan dengan keseharian peserta didik',
                'perilaku_dianjurkan' => "• Guru mengambil contoh atau studi kasus dari lingkungan nyata sekitar murid\n• Guru menggunakan analogi visual yang dekat dengan pengalaman murid\n• Guru mengajak murid menceritakan pengalaman pribadi yang relevan dengan topik",
                'perilaku_dihindari' => "• Guru memberikan contoh yang abstrak dan asing bagi dunia murid\n• Guru menggunakan ilustrasi usang yang membingungkan pemahaman\n• Guru mengabaikan kaitan materi dengan aplikasi di dunia nyata",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 2
            ],
            [
                'jenis_id' => 11,
                'jenis_nama' => 'INSTRUKSI PEMBELAJARAN',
                'nama_aspek' => 'Struktur Penjelasan Logis',
                'fokus_perilaku' => 'Guru menyampaikan penjelasan secara terstruktur dan logis',
                'perilaku_dianjurkan' => "• Guru mengurutkan penyampaian materi secara sistematis dari konkret ke abstrak\n• Guru menyajikan kerangka alur belajar atau peta konsep di awal sesi\n• Guru merangkum poin-poin kunci di setiap akhir sub-penjelasan",
                'perilaku_dihindari' => "• Guru melompat-lompat antar bahasan tanpa alur transisi yang runtut\n• Guru menyampaikan penjelasan tanpa tujuan atau benang merah yang jelas\n• Guru berpanjang lebar pada rincian yang tidak esensial",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 3
            ],

            // 8. INSTRUKSI YANG ADAPTIF (ID: 12)
            [
                'jenis_id' => 12,
                'jenis_nama' => 'INSTRUKSI YANG ADAPTIF',
                'nama_aspek' => 'Responsif Dinamika Pembelajaran',
                'fokus_perilaku' => 'Guru melakukan strategi pembelajaran yang responsif terhadap peserta didik',
                'perilaku_dianjurkan' => "• Guru menyesuaikan tempo pembelajaran saat melihat respon bingung pada murid\n• Guru menyiapkan cara alternatif menerangkan materi bila cara awal belum efektif\n• Guru fleksibel dalam penataan waktu kegiatan pembelajaran demi pemahaman murid",
                'perilaku_dihindari' => "• Guru kaku terpaku pada urutan RPP/modul tanpa memedulikan pemahaman murid\n• Guru terus melanjutkan topik baru saat mayoritas murid masih tertinggal\n• Guru menolak menyesuaikan metode di tengah situasi kelas",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 1
            ],
            [
                'jenis_id' => 12,
                'jenis_nama' => 'INSTRUKSI YANG ADAPTIF',
                'nama_aspek' => 'Pembelajaran Berdiferensiasi',
                'fokus_perilaku' => 'Guru melakukan penyesuaian pembelajaran mengacu kondisi, kebutuhan, dan karakteristik peserta didik',
                'perilaku_dianjurkan' => "• Guru menyediakan materi dengan keragaman tingkat kesulitan (diferensiasi konten)\n• Guru memvariasikan bentuk kegiatan mandiri, berpasangan, atau kelompok (diferensiasi proses)\n• Guru mengizinkan murid menunjukkan pemahaman melalui beragam bentuk karya (diferensiasi produk)",
                'perilaku_dihindari' => "• Guru menyamaratakan materi, proses, dan tugas bagi seluruh murid tanpa pilihan\n• Guru mengabaikan hasil asesmen diagnostik awal murid\n• Guru melabeli murid cerdas vs murid lambat secara terbuka",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 2
            ],
            [
                'jenis_id' => 12,
                'jenis_nama' => 'INSTRUKSI YANG ADAPTIF',
                'nama_aspek' => 'Refleksi Praktik Bersama Murid',
                'fokus_perilaku' => 'Guru merefleksikan praktik pembelajaran yang melibatkan peserta didik',
                'perilaku_dianjurkan' => "• Guru mengajak murid menyampaikan hal yang paling dipahami dan yang masih menantang\n• Guru mencatat masukan murid sebagai bahan perbaikan pertemuan berikutnya\n• Guru mengajak murid menyimpulkan pembelajaran bersama-sama secara reflektif",
                'perilaku_dihindari' => "• Guru langsung mengakhiri sesi pembelajaran tanpa waktu refleksi\n• Guru mengabaikan kesan atau masukan dari peserta didik\n• Guru tidak menindaklanjuti kendala belajar yang dirasakan murid",
                'tipe_skala' => 'perilaku_pmm_1_3',
                'urutan' => 3
            ],

            // =========================================================================
            // TELAAH PERANGKAT AJAR KURIKULUM MERDEKA (JENIS 13 & 14)
            // =========================================================================
            // 9. TELAAH ALUR TUJUAN PEMBELAJARAN (ATP) (ID: 13)
            [
                'jenis_id' => 13,
                'jenis_nama' => 'TELAAH ALUR TUJUAN PEMBELAJARAN',
                'nama_aspek' => 'Kesesuaian CP dan Penurunan ke TP',
                'fokus_perilaku' => 'Capaian Pembelajaran (CP) diturunkan secara tepat ke dalam Tujuan Pembelajaran (TP) yang memuat Kompetensi dan Lingkup Materi',
                'perilaku_dianjurkan' => 'TP memuat kompetensi operasional terukur dan lingkup materi yang esensial sesuai Fase.',
                'perilaku_dihindari' => 'TP hanya mengulang CP tanpa perincian kompetensi dan materi.',
                'tipe_skala' => 'skor_1_4',
                'urutan' => 1
            ],
            [
                'jenis_id' => 13,
                'jenis_nama' => 'TELAAH ALUR TUJUAN PEMBELAJARAN',
                'nama_aspek' => 'Kelogisan Alur dan Tahapan Belajar',
                'fokus_perilaku' => 'Urutan Tujuan Pembelajaran dalam ATP disusun secara logis dan runtut (konkret ke abstrak, mudah ke kompleks)',
                'perilaku_dianjurkan' => 'Pengurutan TP memperhatikan prasyarat kompetensi dan kesinambungan materi.',
                'perilaku_dihindari' => 'Urutan TP melompat tanpa mempertimbangkan pemahaman awal.',
                'tipe_skala' => 'skor_1_4',
                'urutan' => 2
            ],
            [
                'jenis_id' => 13,
                'jenis_nama' => 'TELAAH ALUR TUJUAN PEMBELAJARAN',
                'nama_aspek' => 'Alokasi Waktu dan Integrasi Karakter',
                'fokus_perilaku' => 'ATP mencantumkan alokasi Jam Pelajaran (JP) yang realistis serta penguatan Profil Pelajar Pancasila',
                'perilaku_dianjurkan' => 'Perkiraan JP proporsional dengan kedalaman materi dan dimensi profil lulusan terintegrasi.',
                'perilaku_dihindari' => 'Alokasi waktu tidak realistis dan tidak ada integrasi nilai karakter.',
                'tipe_skala' => 'skor_1_4',
                'urutan' => 3
            ],

            // 10. TELAAH MODUL AJAR / RPP+ (ID: 14)
            [
                'jenis_id' => 14,
                'jenis_nama' => 'TELAAH MODUL AJAR',
                'nama_aspek' => 'Informasi Umum & Identitas Modul',
                'fokus_perilaku' => 'Memuat identitas modul, Fase/Kelas, Kompetensi Awal, Profil Pelajar Pancasila, Sarpras, dan Target Peserta Didik',
                'perilaku_dianjurkan' => 'Komponen informasi umum lengkap dan relevan dengan karakteristik satuan pendidikan.',
                'perilaku_dihindari' => 'Informasi umum tidak lengkap atau hanya copy-paste tanpa adaptasi.',
                'tipe_skala' => 'skor_1_4',
                'urutan' => 1
            ],
            [
                'jenis_id' => 14,
                'jenis_nama' => 'TELAAH MODUL AJAR',
                'nama_aspek' => 'Tujuan Pembelajaran & KKTP',
                'fokus_perilaku' => 'Tujuan Pembelajaran jelas serta dilengkapi Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) yang terukur',
                'perilaku_dianjurkan' => 'KKTP menggunakan rubrik atau interval nilai yang mendeskripsikan capaian kompetensi.',
                'perilaku_dihindari' => 'KKTP hanya berupa angka KKM tunggal tanpa deskripsi ketercapaian.',
                'tipe_skala' => 'skor_1_4',
                'urutan' => 2
            ],
            [
                'jenis_id' => 14,
                'jenis_nama' => 'TELAAH MODUL AJAR',
                'nama_aspek' => 'Pemahaman Bermakna & Pertanyaan Pemantik',
                'fokus_perilaku' => 'Memuat pemahaman bermakna dan pertanyaan pemantik yang merangsang rasa ingin tahu serta relevan dengan kehidupan nyata',
                'perilaku_dianjurkan' => 'Pertanyaan pemantik bersifat terbuka dan memicu eksplorasi konsep.',
                'perilaku_dihindari' => 'Pertanyaan pemantik bersifat tertutup atau tes hafalan langsung.',
                'tipe_skala' => 'skor_1_4',
                'urutan' => 3
            ],
            [
                'jenis_id' => 14,
                'jenis_nama' => 'TELAAH MODUL AJAR',
                'nama_aspek' => 'Kegiatan Pembelajaran Berdiferensiasi',
                'fokus_perilaku' => 'Langkah kegiatan memfasilitasi pembelajaran berdiferensiasi (konten, proses, atau produk) dan berpusat pada murid',
                'perilaku_dianjurkan' => 'Terdapat diferensiasi kegiatan/tugas untuk mengakomodasi keberagaman murid.',
                'perilaku_dihindari' => 'Kegiatan belajar seragam dan didominasi ceramah satu arah.',
                'tipe_skala' => 'skor_1_4',
                'urutan' => 4
            ],
            [
                'jenis_id' => 14,
                'jenis_nama' => 'TELAAH MODUL AJAR',
                'nama_aspek' => 'Rancangan Asesmen Awal, Formatif, & Sumatif',
                'fokus_perilaku' => 'Memuat rencana asesmen awal diagnostik, asesmen formatif selama proses, dan asesmen sumatif dengan instrumen yang jelas',
                'perilaku_dianjurkan' => 'Asesmen formatif digunakan untuk umpan balik pembelajaran berkelanjutan.',
                'perilaku_dihindari' => 'Hanya ada asesmen sumatif di akhir tanpa asesmen formatif berkala.',
                'tipe_skala' => 'skor_1_4',
                'urutan' => 5
            ],
            [
                'jenis_id' => 14,
                'jenis_nama' => 'TELAAH MODUL AJAR',
                'nama_aspek' => 'Lampiran LKPD dan Bahan Ajar',
                'fokus_perilaku' => 'Menyertakan Lembar Kerja Peserta Didik (LKPD), bahan bacaan guru/murid, glosarium, dan daftar pustaka',
                'perilaku_dianjurkan' => 'LKPD interaktif, memandu penyelidikan, dan ramah anak.',
                'perilaku_dihindari' => 'Lampiran kosong atau hanya berupa bank soal pilihan ganda.',
                'tipe_skala' => 'skor_1_4',
                'urutan' => 6
            ],
        ];
    }
}
