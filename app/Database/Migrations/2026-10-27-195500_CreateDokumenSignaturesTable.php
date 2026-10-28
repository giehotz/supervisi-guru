<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDokumenSignaturesTable extends Migration
{
    public function up()
    {
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
            'dokumen_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'supervisi',
            ],
            'signer_role' => [
                'type'       => 'VARCHAR',
                'constraint' => 20, // 'guru', 'supervisor', 'kepala'
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'signer_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'signer_nip' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'token_verifikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
            'tanggal_ttd' => [
                'type' => 'DATE',
            ],
            'signed_at' => [
                'type' => 'DATETIME',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'user_agent' => [
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
        $this->forge->addKey('jadwal_id');
        $this->forge->addKey('signer_role');
        $this->forge->addUniqueKey('token_verifikasi');
        $this->forge->addUniqueKey(['jadwal_id', 'dokumen_type', 'signer_role'], 'unique_signature_per_role');

        $this->forge->createTable('dokumen_signatures', true);
    }

    public function down()
    {
        $this->forge->dropTable('dokumen_signatures', true);
    }
}
