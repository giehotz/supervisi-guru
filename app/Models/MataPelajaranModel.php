<?php

namespace App\Models;

use CodeIgniter\Model;

class MataPelajaranModel extends Model
{
    protected $table            = 'mata_pelajaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['kode_mapel', 'nama_mapel', 'kelompok', 'status'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules  = [
        'nama_mapel' => 'required|min_length[2]|max_length[100]',
        'kelompok'   => 'required|in_list[Umum,Keagamaan,Muatan Lokal,Peminatan]',
        'status'     => 'required|in_list[Aktif,Nonaktif]',
    ];
}
