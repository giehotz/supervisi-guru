<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnsurePenilaianColumns extends Migration
{
    public function up()
    {
        // Kolom yang dipakai KurikulumMerdekaInstrumenSeeder di jenis_penilaian
        $jenisFields = [];
        if (!$this->db->fieldExists('skor_maksimal', 'jenis_penilaian')) {
            $jenisFields['skor_maksimal'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 100,
            ];
        }
        if (!$this->db->fieldExists('kategori_skor', 'jenis_penilaian')) {
            $jenisFields['kategori_skor'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }
        if (!$this->db->fieldExists('deskripsi', 'jenis_penilaian')) {
            $jenisFields['deskripsi'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }
        if (!$this->db->fieldExists('kategori_supervisi', 'jenis_penilaian')) {
            $jenisFields['kategori_supervisi'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'perangkat_ajar',
            ];
        }
        if (!$this->db->fieldExists('dimensi_pmm', 'jenis_penilaian')) {
            $jenisFields['dimensi_pmm'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ];
        }
        if (!$this->db->fieldExists('created_at', 'jenis_penilaian')) {
            $jenisFields['created_at'] = [
                'type' => 'TIMESTAMP',
                'null' => true,
            ];
        }
        if (!empty($jenisFields)) {
            $this->forge->addColumn('jenis_penilaian', $jenisFields);
        }

        // Kolom yang dipakai seeder di aspek_penilaian
        $aspekFields = [];
        if (!$this->db->fieldExists('fokus_perilaku', 'aspek_penilaian')) {
            $aspekFields['fokus_perilaku'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ];
        }
        if (!$this->db->fieldExists('perilaku_dianjurkan', 'aspek_penilaian')) {
            $aspekFields['perilaku_dianjurkan'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }
        if (!$this->db->fieldExists('perilaku_dihindari', 'aspek_penilaian')) {
            $aspekFields['perilaku_dihindari'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }
        if (!$this->db->fieldExists('tipe_skala', 'aspek_penilaian')) {
            $aspekFields['tipe_skala'] = [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'skor_1_4',
            ];
        }
        if (!$this->db->fieldExists('urutan', 'aspek_penilaian')) {
            $aspekFields['urutan'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ];
        }
        if (!$this->db->fieldExists('created_at', 'aspek_penilaian')) {
            $aspekFields['created_at'] = [
                'type' => 'TIMESTAMP',
                'null' => true,
            ];
        }
        if (!empty($aspekFields)) {
            $this->forge->addColumn('aspek_penilaian', $aspekFields);
        }
    }

    public function down()
    {
        $jenisCols = ['skor_maksimal', 'kategori_skor', 'deskripsi', 'kategori_supervisi', 'dimensi_pmm', 'created_at'];
        foreach ($jenisCols as $col) {
            if ($this->db->fieldExists($col, 'jenis_penilaian')) {
                $this->forge->dropColumn('jenis_penilaian', $col);
            }
        }

        $aspekCols = ['fokus_perilaku', 'perilaku_dianjurkan', 'perilaku_dihindari', 'tipe_skala', 'created_at'];
        foreach ($aspekCols as $col) {
            if ($this->db->fieldExists($col, 'aspek_penilaian')) {
                $this->forge->dropColumn('aspek_penilaian', $col);
            }
        }
    }
}
