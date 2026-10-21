<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class MapelController extends BaseController
{
    /**
     * Daftar statis mata pelajaran Kurikulum Merdeka Madrasah Ibtidaiyah (MI)
     */
    public const MAPEL = [
        // Kelompok Agama & Bahasa Arab
        ['id' => 1, 'kode_mapel' => 'QH', 'nama_mapel' => "Al-Qur'an Hadis", 'kelompok' => 'Kelompok Agama & Bahasa Arab', 'db_kelompok' => 'Keagamaan'],
        ['id' => 2, 'kode_mapel' => 'AA', 'nama_mapel' => 'Akidah Akhlak', 'kelompok' => 'Kelompok Agama & Bahasa Arab', 'db_kelompok' => 'Keagamaan'],
        ['id' => 3, 'kode_mapel' => 'FIK', 'nama_mapel' => 'Fikih', 'kelompok' => 'Kelompok Agama & Bahasa Arab', 'db_kelompok' => 'Keagamaan'],
        ['id' => 4, 'kode_mapel' => 'SKI', 'nama_mapel' => 'Sejarah Kebudayaan Islam (SKI)', 'kelompok' => 'Kelompok Agama & Bahasa Arab', 'db_kelompok' => 'Keagamaan'],
        ['id' => 5, 'kode_mapel' => 'ARB', 'nama_mapel' => 'Bahasa Arab', 'kelompok' => 'Kelompok Agama & Bahasa Arab', 'db_kelompok' => 'Keagamaan'],

        // Kelompok Umum
        ['id' => 6, 'kode_mapel' => 'PPKN', 'nama_mapel' => 'Pendidikan Pancasila', 'kelompok' => 'Kelompok Umum', 'db_kelompok' => 'Umum'],
        ['id' => 7, 'kode_mapel' => 'BIND', 'nama_mapel' => 'Bahasa Indonesia', 'kelompok' => 'Kelompok Umum', 'db_kelompok' => 'Umum'],
        ['id' => 8, 'kode_mapel' => 'MAT', 'nama_mapel' => 'Matematika', 'kelompok' => 'Kelompok Umum', 'db_kelompok' => 'Umum'],
        ['id' => 9, 'kode_mapel' => 'IPAS', 'nama_mapel' => 'IPAS', 'kelompok' => 'Kelompok Umum', 'db_kelompok' => 'Umum'],
        ['id' => 10, 'kode_mapel' => 'PJOK', 'nama_mapel' => 'PJOK', 'kelompok' => 'Kelompok Umum', 'db_kelompok' => 'Umum'],
        ['id' => 11, 'kode_mapel' => 'SB', 'nama_mapel' => 'Seni dan Budaya', 'kelompok' => 'Kelompok Umum', 'db_kelompok' => 'Umum'],

        // Kelompok Pilihan & Mulok
        ['id' => 12, 'kode_mapel' => 'BING', 'nama_mapel' => 'Bahasa Inggris', 'kelompok' => 'Kelompok Pilihan & Mulok', 'db_kelompok' => 'Muatan Lokal'],
        ['id' => 13, 'kode_mapel' => 'AI', 'nama_mapel' => 'Koding & Kecerdasan AI', 'kelompok' => 'Kelompok Pilihan & Mulok', 'db_kelompok' => 'Muatan Lokal'],
        ['id' => 14, 'kode_mapel' => 'MLOK', 'nama_mapel' => 'Muatan Lokal', 'kelompok' => 'Kelompok Pilihan & Mulok', 'db_kelompok' => 'Muatan Lokal'],
    ];

    /**
     * Mengembalikan response JSON seluruh mapel yang dikelompokkan
     */
    public function index(): ResponseInterface
    {
        $grouped = [];
        foreach (self::MAPEL as $mapel) {
            $grouped[$mapel['kelompok']][] = $mapel;
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $grouped,
        ]);
    }
}
