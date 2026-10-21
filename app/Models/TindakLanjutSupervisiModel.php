<?php

namespace App\Models;

use CodeIgniter\Model;

class TindakLanjutSupervisiModel extends Model
{
    protected $table            = 'tindak_lanjut_supervisi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jadwal_id',
        'hasil_id',
        // Form C
        'refleksi_guru',
        'pilihan_belajar_pmm',
        'pilihan_belajar_lainnya',
        'tujuan_tindak_lanjut',
        'upaya_tindak_lanjut',
        'periode_tindak_lanjut',
        'kebutuhan_dukungan',
        'detail_dukungan',
        'respon_supervisor_c',
        'catatan_supervisor_c',
        // Form D
        'inspirasi_baru',
        'perubahan_praktik',
        'kendala_tindak_lanjut',
        'rencana_perbaikan_berkelanjutan',
        'penilaian_supervisor_d',
        'catatan_supervisor_d',
        // Status
        'status_tindak_lanjut'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mendapatkan data Form C & D berdasarkan jadwal_id
     */
    public function getByJadwal($jadwalId)
    {
        return $this->where('jadwal_id', $jadwalId)->first();
    }
}
