<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiklusSupervisiKurmerTables extends Migration
{
    public function up()
    {
        // 1. Tabel pra_observasi (Formulir A & Pra-Coaching)
        if (!$this->db->tableExists('pra_observasi')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'jadwal_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'unique'     => true,
                ],
                'fokus_perilaku_dipilih' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'upaya_mempelajari' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'tujuan_pembelajaran_sesi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'area_pengembangan' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'strategi_persiapan' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'catatan_supervisor_pra' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'status_kesepakatan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'draft_guru',
                ],
                'disepakati_pada' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('jadwal_id', 'jadwal_supervisi', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('pra_observasi', true);
        }

        // 2. Tabel tindak_lanjut_supervisi (Formulir C & Formulir D)
        if (!$this->db->tableExists('tindak_lanjut_supervisi')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'jadwal_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'unique'     => true,
                ],
                'hasil_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                // Isian Formulir C (Tindak Lanjut Pasca Observasi)
                'refleksi_guru' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'pilihan_belajar_pmm' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'pilihan_belajar_lainnya' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'tujuan_tindak_lanjut' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'upaya_tindak_lanjut' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'periode_tindak_lanjut' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'kebutuhan_dukungan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'detail_dukungan' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'respon_supervisor_c' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'catatan_supervisor_c' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                // Isian Formulir D (Refleksi Tindak Lanjut)
                'inspirasi_baru' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'perubahan_praktik' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'kendala_tindak_lanjut' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'rencana_perbaikan_berkelanjutan' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'penilaian_supervisor_d' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'catatan_supervisor_d' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                // Status siklus tindak lanjut
                'status_tindak_lanjut' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'belum_dimulai',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('jadwal_id', 'jadwal_supervisi', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('tindak_lanjut_supervisi', true);
        }

        // 3. Tabel coaching_tirta_log (Log Percakapan Alur TIRTA)
        if (!$this->db->tableExists('coaching_tirta_log')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'jadwal_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'tahap' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                ],
                'tujuan_coaching' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'identifikasi_kondisi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'rencana_aksi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'tanggung_jawab' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('jadwal_id', 'jadwal_supervisi', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('coaching_tirta_log', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('coaching_tirta_log', true);
        $this->forge->dropTable('tindak_lanjut_supervisi', true);
        $this->forge->dropTable('pra_observasi', true);
    }
}
