<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MapelSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Ambil nama mapel unik yang ada di tabel guru
        $gurus = $db->table('guru')
            ->select('mata_pelajaran')
            ->where('mata_pelajaran IS NOT NULL')
            ->where('mata_pelajaran !=', '')
            ->get()
            ->getResultArray();

        $subjects = [];
        foreach ($gurus as $g) {
            $name = trim($g['mata_pelajaran']);
            if ($name !== '' && !in_array($name, $subjects)) {
                $subjects[] = $name;
            }
        }

        // Jika data guru sedikit / kosong, sertakan mapel standar madrasah
        $defaultSubjects = [
            'Al-Qur\'an Hadis',
            'Akidah Akhlak',
            'Fikih',
            'Sejarah Kebudayaan Islam',
            'Bahasa Arab',
            'Pendidikan Pancasila dan Kewarganegaraan',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'Matematika',
            'Ilmu Pengetahuan Alam',
            'Ilmu Pengetahuan Sosial',
            'Seni Budaya',
            'Pendidikan Jasmani, Olahraga, dan Kesehatan',
            'Informatika'
        ];

        foreach ($defaultSubjects as $def) {
            if (!in_array($def, $subjects)) {
                $subjects[] = $def;
            }
        }

        foreach ($subjects as $s) {
            $exists = $db->table('mata_pelajaran')->where('nama_mapel', $s)->countAllResults();
            if ($exists === 0) {
                // Generate kode mapel singkat (3-5 huruf)
                $clean = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $s));
                $kode = substr($clean, 0, 5);

                // Kategorisasi otomatis
                $kelompok = 'Umum';
                $lower = strtolower($s);
                if (
                    str_contains($lower, 'agama') ||
                    str_contains($lower, 'qur') ||
                    str_contains($lower, 'hadis') ||
                    str_contains($lower, 'fiq') ||
                    str_contains($lower, 'akidah') ||
                    str_contains($lower, 'akhlak') ||
                    str_contains($lower, 'sejarah kebudayaan islam') ||
                    str_contains($lower, 'ski') ||
                    str_contains($lower, 'pai') ||
                    str_contains($lower, 'arab')
                ) {
                    $kelompok = 'Keagamaan';
                }

                $db->table('mata_pelajaran')->insert([
                    'kode_mapel' => $kode,
                    'nama_mapel' => $s,
                    'kelompok'   => $kelompok,
                    'status'     => 'Aktif',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
