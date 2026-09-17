<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalKbmModel extends Model
{
    protected $table            = 'guru_mengajar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'tahun_ajar_id',
        'guru_id',
        'mapel_id',
        'kelas_id',
        'hari',
        'jam_mulai_ke',
        'jam_selesai_ke',
        'status',
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Definisi sesi jam pelajaran standar madrasah (Jam 1 s.d. 8)
     */
    public static function getDaftarSesiJam(): array
    {
        return [
            1 => ['jam_ke' => 1, 'mulai' => '07:15', 'selesai' => '07:55', 'label' => 'Jam Ke-1 (07.15 - 07.55)'],
            2 => ['jam_ke' => 2, 'mulai' => '07:55', 'selesai' => '08:35', 'label' => 'Jam Ke-2 (07.55 - 08.35)'],
            3 => ['jam_ke' => 3, 'mulai' => '08:35', 'selesai' => '09:15', 'label' => 'Jam Ke-3 (08.35 - 09.15)'],
            4 => ['jam_ke' => 4, 'mulai' => '09:15', 'selesai' => '09:55', 'label' => 'Jam Ke-4 (09.15 - 09.55)'],
            5 => ['jam_ke' => 5, 'mulai' => '10:15', 'selesai' => '10:55', 'label' => 'Jam Ke-5 (10.15 - 10.55)'],
            6 => ['jam_ke' => 6, 'mulai' => '10:55', 'selesai' => '11:35', 'label' => 'Jam Ke-6 (10.55 - 11.35)'],
            7 => ['jam_ke' => 7, 'mulai' => '11:35', 'selesai' => '12:15', 'label' => 'Jam Ke-7 (11.35 - 12.15)'],
            8 => ['jam_ke' => 8, 'mulai' => '12:45', 'selesai' => '13:25', 'label' => 'Jam Ke-8 (12.45 - 13.25)'],
        ];
    }

    /**
     * Mengambil daftar jadwal lengkap dengan relasi guru, mapel, kelas, dan tahun ajar
     */
    public function getJadwalWithDetails($tahunAjarId = null, $kelasId = null, $guruId = null, $hari = null): array
    {
        $builder = $this->select('
                guru_mengajar.*, 
                guru.nama as nama_guru, 
                guru.nip, 
                guru.mata_pelajaran as mapel_guru_default,
                mata_pelajaran.nama_mapel, 
                mata_pelajaran.kode_mapel, 
                mata_pelajaran.kelompok, 
                kelas.nama_kelas, 
                tahun_ajar.tahun_ajar, 
                tahun_ajar.semester
            ')
            ->join('guru', 'guru.id = guru_mengajar.guru_id', 'left')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru_mengajar.mapel_id', 'left')
            ->join('kelas', 'kelas.id = guru_mengajar.kelas_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = guru_mengajar.tahun_ajar_id', 'left');

        if ($tahunAjarId) {
            $builder->where('guru_mengajar.tahun_ajar_id', $tahunAjarId);
        }
        if ($kelasId) {
            $builder->where('guru_mengajar.kelas_id', $kelasId);
        }
        if ($guruId) {
            $builder->where('guru_mengajar.guru_id', $guruId);
        }
        if ($hari) {
            $builder->where('guru_mengajar.hari', $hari);
        }

        $builder->orderBy("FIELD(guru_mengajar.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('guru_mengajar.jam_mulai_ke', 'ASC')
            ->orderBy('kelas.nama_kelas', 'ASC');

        return $builder->findAll();
    }

    /**
     * Membangun struktur matriks mingguan per kelas: [hari][jam_ke] => data_jadwal
     */
    public function getMatriksJadwalKelas($tahunAjarId, $kelasId): array
    {
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $matriks = [];
        foreach ($hariList as $h) {
            $matriks[$h] = [];
            for ($jam = 1; $jam <= 8; $jam++) {
                $matriks[$h][$jam] = null;
            }
        }

        if (empty($tahunAjarId) || empty($kelasId)) {
            return $matriks;
        }

        $items = $this->getJadwalWithDetails($tahunAjarId, $kelasId);

        foreach ($items as $item) {
            $h = $item['hari'];
            if (!isset($matriks[$h])) continue;

            $mulai = (int)$item['jam_mulai_ke'];
            $selesai = (int)$item['jam_selesai_ke'];
            $durasi = max(1, $selesai - $mulai + 1);

            for ($jam = $mulai; $jam <= $selesai; $jam++) {
                if ($jam >= 1 && $jam <= 8) {
                    $matriks[$h][$jam] = [
                        'item'       => $item,
                        'is_start'   => ($jam === $mulai),
                        'durasi'     => $durasi,
                        'is_overlap' => ($jam > $mulai), // penanda jika tergabung dalam rentang multi-jam
                    ];
                }
            }
        }

        return $matriks;
    }

    /**
     * Membangun struktur matriks mingguan per guru: [hari][jam_ke] => data_jadwal
     */
    public function getMatriksJadwalGuru($tahunAjarId, $guruId): array
    {
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $matriks = [];
        foreach ($hariList as $h) {
            $matriks[$h] = [];
            for ($jam = 1; $jam <= 8; $jam++) {
                $matriks[$h][$jam] = null;
            }
        }

        if (empty($tahunAjarId) || empty($guruId)) {
            return $matriks;
        }

        $items = $this->getJadwalWithDetails($tahunAjarId, null, $guruId);

        foreach ($items as $item) {
            $h = $item['hari'];
            if (!isset($matriks[$h])) continue;

            $mulai = (int)$item['jam_mulai_ke'];
            $selesai = (int)$item['jam_selesai_ke'];
            $durasi = max(1, $selesai - $mulai + 1);

            for ($jam = $mulai; $jam <= $selesai; $jam++) {
                if ($jam >= 1 && $jam <= 8) {
                    $matriks[$h][$jam] = [
                        'item'       => $item,
                        'is_start'   => ($jam === $mulai),
                        'durasi'     => $durasi,
                        'is_overlap' => ($jam > $mulai),
                    ];
                }
            }
        }

        return $matriks;
    }

    /**
     * Menghitung Rekapitulasi Beban Mengajar (JTM / JP) seluruh guru pada tahun ajaran tertentu
     */
    public function getRekapBebanGuru($tahunAjarId): array
    {
        $guruModel = new GuruModel();
        $gurus = $guruModel->orderBy('nama', 'ASC')->findAll();

        $items = $this->getJadwalWithDetails($tahunAjarId);

        $rekap = [];
        foreach ($gurus as $g) {
            $rekap[$g['id']] = [
                'guru_id'    => $g['id'],
                'nama_guru'  => $g['nama'],
                'nip'        => $g['nip'],
                'mapel_list' => [],
                'kelas_list' => [],
                'total_jtm'  => 0,
                'jadwal_count' => 0,
            ];
        }

        foreach ($items as $it) {
            $gid = $it['guru_id'];
            if (!isset($rekap[$gid])) continue;

            $durasi = max(1, (int)$it['jam_selesai_ke'] - (int)$it['jam_mulai_ke'] + 1);
            $rekap[$gid]['total_jtm'] += $durasi;
            $rekap[$gid]['jadwal_count']++;

            $mapelName = !empty($it['nama_mapel']) ? $it['nama_mapel'] : ($it['mapel_guru_default'] ?? '-');
            if (!in_array($mapelName, $rekap[$gid]['mapel_list'])) {
                $rekap[$gid]['mapel_list'][] = $mapelName;
            }

            $kelasName = !empty($it['nama_kelas']) ? $it['nama_kelas'] : '-';
            if (!in_array($kelasName, $rekap[$gid]['kelas_list'])) {
                $rekap[$gid]['kelas_list'][] = $kelasName;
            }
        }

        // Filter atau urutkan guru yang memiliki beban mengajar atau urut nama
        return array_values($rekap);
    }

    /**
     * Validasi Deteksi Bentrok Cerdas (Conflict Engine)
     * Mengembalikan array error jika ada bentrok, atau null jika aman
     */
    public function checkConflict($tahunAjarId, $guruId, $kelasId, $hari, $jamMulai, $jamSelesai, $excludeId = null): ?array
    {
        // 1. Cek bentrok guru (guru yang sama mengajar di kelas lain pada hari dan jam beririsan)
        $builderGuru = $this->select('guru_mengajar.*, kelas.nama_kelas, mata_pelajaran.nama_mapel')
            ->join('kelas', 'kelas.id = guru_mengajar.kelas_id', 'left')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru_mengajar.mapel_id', 'left')
            ->where('guru_mengajar.tahun_ajar_id', $tahunAjarId)
            ->where('guru_mengajar.guru_id', $guruId)
            ->where('guru_mengajar.hari', $hari)
            ->where('guru_mengajar.jam_mulai_ke <=', $jamSelesai)
            ->where('guru_mengajar.jam_selesai_ke >=', $jamMulai);

        if ($excludeId) {
            $builderGuru->where('guru_mengajar.id !=', $excludeId);
        }

        $bentrokGuru = $builderGuru->first();
        if ($bentrokGuru) {
            return [
                'type'    => 'guru',
                'message' => 'Bentrok Jadwal Guru: Guru tersebut sudah memiliki jadwal mengajar mata pelajaran "' . 
                             esc($bentrokGuru['nama_mapel'] ?? '-') . '" di Kelas ' . esc($bentrokGuru['nama_kelas'] ?? '-') . 
                             ' pada hari ' . $hari . ' jam ke-' . $bentrokGuru['jam_mulai_ke'] . ' s/d ke-' . $bentrokGuru['jam_selesai_ke'] . '.'
            ];
        }

        // 2. Cek bentrok kelas (kelas yang sama sudah diisi mapel/guru lain pada hari dan jam beririsan)
        $builderKelas = $this->select('guru_mengajar.*, guru.nama as nama_guru, mata_pelajaran.nama_mapel')
            ->join('guru', 'guru.id = guru_mengajar.guru_id', 'left')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru_mengajar.mapel_id', 'left')
            ->where('guru_mengajar.tahun_ajar_id', $tahunAjarId)
            ->where('guru_mengajar.kelas_id', $kelasId)
            ->where('guru_mengajar.hari', $hari)
            ->where('guru_mengajar.jam_mulai_ke <=', $jamSelesai)
            ->where('guru_mengajar.jam_selesai_ke >=', $jamMulai);

        if ($excludeId) {
            $builderKelas->where('guru_mengajar.id !=', $excludeId);
        }

        $bentrokKelas = $builderKelas->first();
        if ($bentrokKelas) {
            return [
                'type'    => 'kelas',
                'message' => 'Bentrok Jadwal Kelas: Kelas tersebut sudah memiliki jadwal belajar "' . 
                             esc($bentrokKelas['nama_mapel'] ?? '-') . '" bersama guru ' . esc($bentrokKelas['nama_guru'] ?? '-') . 
                             ' pada hari ' . $hari . ' jam ke-' . $bentrokKelas['jam_mulai_ke'] . ' s/d ke-' . $bentrokKelas['jam_selesai_ke'] . '.'
            ];
        }

        return null;
    }
}
