<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAjarModel extends Model
{
    protected $table = 'tahun_ajar';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tahun_ajar', 'semester', 'status_aktif'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Mengambil satu baris tahun ajaran yang sedang aktif
     */
    public function getActive(): ?array
    {
        return $this->where('status_aktif', 'Aktif')->first();
    }

    /**
     * Mengambil daftar tahun ajaran dengan filter status opsional
     */
    public function getFiltered(?string $status = null): array
    {
        $builder = $this->orderBy('tahun_ajar', 'DESC')->orderBy('semester', 'ASC');

        if (!empty($status) && in_array($status, ['Aktif', 'Nonaktif'])) {
            $builder->where('status_aktif', $status);
        }

        return $builder->findAll();
    }
}