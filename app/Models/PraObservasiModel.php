<?php

namespace App\Models;

use CodeIgniter\Model;

class PraObservasiModel extends Model
{
    protected $table            = 'pra_observasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jadwal_id',
        'fokus_perilaku_dipilih',
        'upaya_mempelajari',
        'tujuan_pembelajaran_sesi',
        'area_pengembangan',
        'strategi_persiapan',
        'catatan_supervisor_pra',
        'status_kesepakatan',
        'disepakati_pada'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mendapatkan data Form A berdasarkan jadwal_id
     */
    public function getByJadwal($jadwalId)
    {
        return $this->where('jadwal_id', $jadwalId)->first();
    }
}
