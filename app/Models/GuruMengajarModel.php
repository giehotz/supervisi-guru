<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruMengajarModel extends Model
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

    public function getJadwalWithDetails($tahunAjarId = null, $kelasId = null)
    {
        $builder = $this->select('guru_mengajar.*, guru.nama as nama_guru, guru.nip, mata_pelajaran.nama_mapel, mata_pelajaran.kode_mapel, mata_pelajaran.kelompok, kelas.nama_kelas, tahun_ajar.tahun_ajar, tahun_ajar.semester')
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

        // Urutkan berdasarkan hari dan sesi jam
        $builder->orderBy("FIELD(guru_mengajar.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('guru_mengajar.jam_mulai_ke', 'ASC');

        return $builder->findAll();
    }
}
