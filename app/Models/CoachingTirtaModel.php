<?php

namespace App\Models;

use CodeIgniter\Model;

class CoachingTirtaModel extends Model
{
    protected $table            = 'coaching_tirta_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jadwal_id',
        'tahap',
        'tujuan_coaching',
        'identifikasi_kondisi',
        'rencana_aksi',
        'tanggung_jawab'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mendapatkan log coaching berdasarkan jadwal dan tahapan
     */
    public function getLogByJadwal($jadwalId, $tahap = null)
    {
        $builder = $this->where('jadwal_id', $jadwalId);
        if ($tahap) {
            $builder->where('tahap', $tahap);
        }
        return $builder->findAll();
    }
}
