<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAkademikTables extends Migration
{
    public function up()
    {
        // 1. Table: mata_pelajaran
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_mapel' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'nama_mapel' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'kelompok' => [
                'type'       => 'ENUM',
                'constraint' => ['Umum', 'Keagamaan', 'Muatan Lokal', 'Peminatan'],
                'default'    => 'Umum',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Aktif', 'Nonaktif'],
                'default'    => 'Aktif',
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
        $this->forge->createTable('mata_pelajaran', true);

        // 2. Table: guru_mengajar
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tahun_ajar_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'guru_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'mapel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'kelas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'hari' => [
                'type'       => 'ENUM',
                'constraint' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                'default'    => 'Senin',
            ],
            'jam_mulai_ke' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 1,
            ],
            'jam_selesai_ke' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 2,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Aktif', 'Nonaktif'],
                'default'    => 'Aktif',
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
        $this->forge->addForeignKey('tahun_ajar_id', 'tahun_ajar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mapel_id', 'mata_pelajaran', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('guru_mengajar', true);
    }

    public function down()
    {
        $this->forge->dropTable('guru_mengajar', true);
        $this->forge->dropTable('mata_pelajaran', true);
    }
}
