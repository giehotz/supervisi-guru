<?php

namespace App\Database\Seeds;

class KurikulumMerdekaDataMetodePembelajaran
{
    public static function data(): array
    {
        return [
            [
                'nama'               => 'AKTIVITAS INTERAKTIF',
                'dimensi_pmm'        => 'metode_pembelajaran',
                'kategori_supervisi' => 'observasi_kelas_pmm',
                'deskripsi'          => 'Upaya memfasilitasi keterlibatan aktif peserta didik melalui dialog, kolaborasi, dan eksplorasi gagasan.',
                'fokus' => [
                    [
                        'nama_aspek'          => 'Peran Aktif Semua Murid',
                        'fokus_perilaku'      => 'Guru memfasilitasi kegiatan pembelajaran yang memberi peran pada semua peserta didik',
                        'perilaku_dianjurkan' => "• Guru merancang aktivitas yang membagi peran secara adil dan terukur\n• Guru memastikan setiap murid memiliki tanggung jawab konkret dalam tugas\n• Guru memotivasi murid yang pendiam untuk berani mengambil peran aktif",
                        'perilaku_dihindari'  => "• Guru membiarkan murid tertentu pasif atau asyik sendiri\n• Guru hanya mengandalkan murid tertentu yang vokal\n• Guru mendominasi seluruh jalannya pembelajaran (teacher-centered murni)",
                    ],
                    [
                        'nama_aspek'          => 'Pertanyaan Pemantik Berpikir Kritis',
                        'fokus_perilaku'      => 'Guru mengajukan pertanyaan yang menstimulasi proses diskusi dan berpikir kritis',
                        'perilaku_dianjurkan' => "• Guru memberikan pertanyaan pemantik terbuka tingkat tinggi (HOTS)\n• Guru memberi jeda waktu tunggu (wait time) yang memadai agar murid berpikir\n• Guru merespon jawaban murid dengan pertanyaan lanjutan yang mendalam",
                        'perilaku_dihindari'  => "• Guru hanya mengajukan pertanyaan ingatan tertutup (Ya/Tidak)\n• Guru langsung menjawab pertanyaannya sendiri tanpa memberi kesempatan murid berpikir\n• Guru memotong pembicaraan murid saat sedang merumuskan ide",
                    ],
                    [
                        'nama_aspek'          => 'Diskusi Kelompok Kritis & Inklusif',
                        'fokus_perilaku'      => 'Guru memfasilitasi terjadinya diskusi kelompok yang interaktif, kritis, dan inklusif',
                        'perilaku_dianjurkan' => "• Guru memandu jalannya dialog antarmurid agar saling menghargai pendapat\n• Guru mendorong murid saling menanggapi gagasan rekan secara santun\n• Guru berkeliling mendampingi dinamika diskusi setiap kelompok",
                        'perilaku_dihindari'  => "• Guru duduk diam di meja guru selama murid berdiskusi kelompok\n• Guru membiarkan perdebatan menyimpang dari topik atau menjadi tidak santun\n• Guru membiarkan diskusi berjalan tanpa tujuan atau arahan hasil yang jelas",
                    ],
                ],
            ],
            [
                'nama'               => 'INSTRUKSI PEMBELAJARAN',
                'dimensi_pmm'        => 'metode_pembelajaran',
                'kategori_supervisi' => 'observasi_kelas_pmm',
                'deskripsi'          => 'Upaya menyampaikan penjelasan konsep secara terstruktur, kontekstual, dan mudah dipahami sesuai kemampuan awal murid.',
                'fokus' => [
                    [
                        'nama_aspek'          => 'Penjelasan Sesuai Pemahaman Awal',
                        'fokus_perilaku'      => 'Guru memberikan penjelasan yang mudah sesuai pemahaman awal peserta didik',
                        'perilaku_dianjurkan' => "• Guru menggunakan bahasa yang jelas, lugas, dan mudah dicerna murid\n• Guru mengaitkan konsep baru dengan pengetahuan yang sudah dimiliki murid\n• Guru melakukan pengecekan pemahaman konsep (concept check) secara berkala",
                        'perilaku_dihindari'  => "• Guru menggunakan istilah teknis/jargon rumit tanpa penjelasan memadai\n• Guru berbicara terlalu cepat atau dengan intonasi monoton\n• Guru mengasumsikan seluruh murid paham tanpa melakukan asesmen awal",
                    ],
                    [
                        'nama_aspek'          => 'Contoh Kontekstual Nyata',
                        'fokus_perilaku'      => 'Guru memberikan contoh yang kontekstual dan relevan dengan keseharian peserta didik',
                        'perilaku_dianjurkan' => "• Guru mengambil contoh atau studi kasus dari lingkungan nyata sekitar murid\n• Guru menggunakan analogi visual yang dekat dengan pengalaman murid\n• Guru mengajak murid menceritakan pengalaman pribadi yang relevan dengan topik",
                        'perilaku_dihindari'  => "• Guru memberikan contoh yang abstrak dan asing bagi dunia murid\n• Guru menggunakan ilustrasi usang yang membingungkan pemahaman\n• Guru mengabaikan kaitan materi dengan aplikasi di dunia nyata",
                    ],
                    [
                        'nama_aspek'          => 'Struktur Penjelasan Logis',
                        'fokus_perilaku'      => 'Guru menyampaikan penjelasan secara terstruktur dan logis',
                        'perilaku_dianjurkan' => "• Guru mengurutkan penyampaian materi secara sistematis dari konkret ke abstrak\n• Guru menyajikan kerangka alur belajar atau peta konsep di awal sesi\n• Guru merangkum poin-poin kunci di setiap akhir sub-penjelasan",
                        'perilaku_dihindari'  => "• Guru melompat-lompat antar bahasan tanpa alur transisi yang runtut\n• Guru menyampaikan penjelasan tanpa tujuan atau benang merah yang jelas\n• Guru berpanjang lebar pada rincian yang tidak esensial",
                    ],
                ],
            ],
            [
                'nama'               => 'INSTRUKSI YANG ADAPTIF',
                'dimensi_pmm'        => 'metode_pembelajaran',
                'kategori_supervisi' => 'observasi_kelas_pmm',
                'deskripsi'          => 'Upaya menyesuaikan praktik pembelajaran berdasarkan respon, kebutuhan, dan perkembangan belajar murid.',
                'fokus' => [
                    [
                        'nama_aspek'          => 'Responsif Dinamika Pembelajaran',
                        'fokus_perilaku'      => 'Guru melakukan strategi pembelajaran yang responsif terhadap peserta didik',
                        'perilaku_dianjurkan' => "• Guru menyesuaikan tempo pembelajaran saat melihat respon bingung pada murid\n• Guru menyiapkan cara alternatif menerangkan materi bila cara awal belum efektif\n• Guru fleksibel dalam penataan waktu kegiatan pembelajaran demi pemahaman murid",
                        'perilaku_dihindari'  => "• Guru kaku terpaku pada urutan RPP/modul tanpa memedulikan pemahaman murid\n• Guru terus melanjutkan topik baru saat mayoritas murid masih tertinggal\n• Guru menolak menyesuaikan metode di tengah situasi kelas",
                    ],
                    [
                        'nama_aspek'          => 'Pembelajaran Berdiferensiasi',
                        'fokus_perilaku'      => 'Guru melakukan penyesuaian pembelajaran mengacu kondisi, kebutuhan, dan karakteristik peserta didik',
                        'perilaku_dianjurkan' => "• Guru menyediakan materi dengan keragaman tingkat kesulitan (diferensiasi konten)\n• Guru memvariasikan bentuk kegiatan mandiri, berpasangan, atau kelompok (diferensiasi proses)\n• Guru mengizinkan murid menunjukkan pemahaman melalui beragam bentuk karya (diferensiasi produk)",
                        'perilaku_dihindari'  => "• Guru menyamaratakan materi, proses, dan tugas bagi seluruh murid tanpa pilihan\n• Guru mengabaikan hasil asesmen diagnostik awal murid\n• Guru melabeli murid cerdas vs murid lambat secara terbuka",
                    ],
                    [
                        'nama_aspek'          => 'Refleksi Praktik Bersama Murid',
                        'fokus_perilaku'      => 'Guru merefleksikan praktik pembelajaran yang melibatkan peserta didik',
                        'perilaku_dianjurkan' => "• Guru mengajak murid menyampaikan hal yang paling dipahami dan yang masih menantang\n• Guru mencatat masukan murid sebagai bahan perbaikan pertemuan berikutnya\n• Guru mengajak murid menyimpulkan pembelajaran bersama-sama secara reflektif",
                        'perilaku_dihindari'  => "• Guru langsung mengakhiri sesi pembelajaran tanpa waktu refleksi\n• Guru mengabaikan kesan atau masukan dari peserta didik\n• Guru tidak menindaklanjuti kendala belajar yang dirasakan murid",
                    ],
                ],
            ],
        ];
    }
}
