<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKurikulumMerdekaColumns extends Migration
{
    public function up()
    {
        // 1. Tabel kelas: Tambah kolom fase
        if (!$this->db->fieldExists('fase', 'kelas')) {
            $this->forge->addColumn('kelas', [
                'fase' => [
                    'type'       => 'ENUM',
                    'constraint' => ['A', 'B', 'C', 'D', 'E', 'F'],
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'nama_kelas',
                ],
            ]);
        }

        // 2. Tabel dokumen_ajar: Tambah metadata Kurikulum Merdeka
        $dokumenFields = [];
        if (!$this->db->fieldExists('kategori_dokumen', 'dokumen_ajar')) {
            $dokumenFields['kategori_dokumen'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'modul_ajar',
                'after'      => 'nama_dokumen',
            ];
        }
        if (!$this->db->fieldExists('fase', 'dokumen_ajar')) {
            $dokumenFields['fase'] = [
                'type'       => 'ENUM',
                'constraint' => ['A', 'B', 'C', 'D', 'E', 'F'],
                'null'       => true,
                'after'      => 'nama_dokumen',
            ];
        }
        if (!$this->db->fieldExists('tujuan_pembelajaran', 'dokumen_ajar')) {
            $dokumenFields['tujuan_pembelajaran'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }
        if (!$this->db->fieldExists('strategi_diferensiasi', 'dokumen_ajar')) {
            $dokumenFields['strategi_diferensiasi'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ];
        }
        if (!$this->db->fieldExists('link_eksternal', 'dokumen_ajar')) {
            $dokumenFields['link_eksternal'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ];
        }
        if (!empty($dokumenFields)) {
            $this->forge->addColumn('dokumen_ajar', $dokumenFields);
        }

        // 3. Tabel jenis_penilaian: Tambah kategori supervisi & dimensi PMM
        $jenisFields = [];
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
        if (!empty($jenisFields)) {
            $this->forge->addColumn('jenis_penilaian', $jenisFields);
        }

        // 4. Tabel aspek_penilaian: Tambah fokus perilaku, anjuran, larangan, & tipe skala
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
        if (!empty($aspekFields)) {
            $this->forge->addColumn('aspek_penilaian', $aspekFields);
        }

        // 5. Tabel jadwal_supervisi: Tambah tanggal pra/pasca & indikator PMM
        $jadwalFields = [];
        if (!$this->db->fieldExists('indikator_pmm_id', 'jadwal_supervisi')) {
            $jadwalFields['indikator_pmm_id'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'mata_pelajaran',
            ];
        }
        if (!$this->db->fieldExists('tanggal_pra_observasi', 'jadwal_supervisi')) {
            $jadwalFields['tanggal_pra_observasi'] = [
                'type' => 'DATETIME',
                'null' => true,
            ];
        }
        if (!$this->db->fieldExists('tanggal_pasca_observasi', 'jadwal_supervisi')) {
            $jadwalFields['tanggal_pasca_observasi'] = [
                'type' => 'DATETIME',
                'null' => true,
            ];
        }
        if (!empty($jadwalFields)) {
            $this->forge->addColumn('jadwal_supervisi', $jadwalFields);
        }

        // Ubah tipe kolom status jadwal_supervisi agar fleksibel menampung tahapan siklus baru
        $this->db->query("ALTER TABLE `jadwal_supervisi` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'Terjadwal'");
    }

    public function down()
    {
        if ($this->db->fieldExists('fase', 'kelas')) {
            $this->forge->dropColumn('kelas', 'fase');
        }

        $dokumenCols = ['kategori_dokumen', 'fase', 'tujuan_pembelajaran', 'strategi_diferensiasi', 'link_eksternal'];
        foreach ($dokumenCols as $col) {
            if ($this->db->fieldExists($col, 'dokumen_ajar')) {
                $this->forge->dropColumn('dokumen_ajar', $col);
            }
        }

        $jenisCols = ['kategori_supervisi', 'dimensi_pmm'];
        foreach ($jenisCols as $col) {
            if ($this->db->fieldExists($col, 'jenis_penilaian')) {
                $this->forge->dropColumn('jenis_penilaian', $col);
            }
        }

        $aspekCols = ['fokus_perilaku', 'perilaku_dianjurkan', 'perilaku_dihindari', 'tipe_skala'];
        foreach ($aspekCols as $col) {
            if ($this->db->fieldExists($col, 'aspek_penilaian')) {
                $this->forge->dropColumn('aspek_penilaian', $col);
            }
        }

        $jadwalCols = ['indikator_pmm_id', 'tanggal_pra_observasi', 'tanggal_pasca_observasi'];
        foreach ($jadwalCols as $col) {
            if ($this->db->fieldExists($col, 'jadwal_supervisi')) {
                $this->forge->dropColumn('jadwal_supervisi', $col);
            }
        }
    }
}
