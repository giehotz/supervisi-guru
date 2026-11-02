<?php

namespace App\Database\Seeds;

class KurikulumMerdekaDataDukunganPsikologis
{
    public static function data(): array
    {
        return [
            [
                'nama'               => 'EKSPEKTASI PADA PESERTA DIDIK',
                'dimensi_pmm'        => 'dukungan_psikologis',
                'kategori_supervisi' => 'observasi_kelas_pmm',
                'deskripsi'          => 'Upaya mengomunikasikan ekspektasi tinggi terhadap kemampuan dan potensi seluruh peserta didik.',
                'fokus' => [
                    [
                        'nama_aspek'          => 'Komunikasi Harapan Masa Depan',
                        'fokus_perilaku'      => 'Guru mengkomunikasikan harapannya yang tinggi terhadap masa depan seluruh peserta didiknya',
                        'perilaku_dianjurkan' => "• Guru mengenal dan menyebutkan potensi unik peserta didik\n• Guru memberikan contoh tokoh inspiratif yang relevan dengan kehidupan murid\n• Guru menyampaikan keyakinan bahwa semua murid mampu berkembang mencapai potensinya",
                        'perilaku_dihindari'  => "• Guru meremehkan potensi atau cita-cita peserta didik\n• Guru bersikap apatis terhadap masa depan murid\n• Guru melabeli murid dengan predikat yang membatasi perkembangannya",
                    ],
                    [
                        'nama_aspek'          => 'Harapan Positif yang Setara',
                        'fokus_perilaku'      => 'Guru mengkomunikasikan harapan positif terhadap semua peserta didik secara setara dan tanpa diskriminasi',
                        'perilaku_dianjurkan' => "• Guru memberikan perhatian dan kesempatan setara kepada seluruh murid\n• Guru memfasilitasi murid yang butuh bimbingan lebih tanpa merendahkan martabatnya\n• Guru mengapresiasi keberagaman latar belakang dan kemampuan murid",
                        'perilaku_dihindari'  => "• Guru hanya memberi panggung pada murid berprestasi tinggi\n• Guru mendiskriminasi murid berdasarkan latar belakang, gender, atau status ekonomi\n• Guru bersikap pilih kasih dalam proses pembelajaran",
                    ],
                    [
                        'nama_aspek'          => 'Tantangan Belajar Bermakna',
                        'fokus_perilaku'      => 'Guru memberikan tantangan yang bermakna disertai motivasi untuk mencapainya',
                        'perilaku_dianjurkan' => "• Guru memberi tugas yang menantang namun terjangkau dengan kemampuan murid\n• Guru memandu proses penyelesaian tantangan secara bertahap (scaffolding)\n• Guru memotivasi murid agar tekun dan pantang menyerah saat menghadapi kendala",
                        'perilaku_dihindari'  => "• Guru memberikan tugas yang terlalu mudah sehingga tidak menantang, atau terlalu sulit tanpa bimbingan\n• Guru membiarkan murid menyerah tanpa ada upaya dukungan\n• Guru membebani murid dengan tugas hafalan mekanis yang tidak bermakna",
                    ],
                ],
            ],
            [
                'nama'               => 'PERHATIAN DAN KEPEDULIAN',
                'dimensi_pmm'        => 'dukungan_psikologis',
                'kategori_supervisi' => 'observasi_kelas_pmm',
                'deskripsi'          => 'Upaya menunjukkan empati, penghargaan, dan kepedulian terhadap kebutuhan belajar dan kondisi emosional murid.',
                'fokus' => [
                    [
                        'nama_aspek'          => 'Empati dan Pemahaman Murid',
                        'fokus_perilaku'      => 'Guru menunjukkan empati untuk mendapatkan pemahaman utuh tentang peserta didik',
                        'perilaku_dianjurkan' => "• Guru mendengarkan aktif saat murid menyampaikan pendapat atau keluh kesah\n• Guru peka terhadap perubahan emosi atau perilaku murid di kelas\n• Guru mencari tahu akar masalah murid saat mengalami kesulitan belajar",
                        'perilaku_dihindari'  => "• Guru mengabaikan keluhan, kesedihan, atau kekhawatiran murid\n• Guru langsung menghakimi perasaan atau perilaku murid\n• Guru bersikap dingin, sinis, dan menjaga jarak emosional",
                    ],
                    [
                        'nama_aspek'          => 'Responsif Kebutuhan Belajar',
                        'fokus_perilaku'      => 'Guru menunjukkan pemahaman terhadap kebutuhan, kondisi, dan karakteristik peserta didik',
                        'perilaku_dianjurkan' => "• Guru memfasilitasi gaya belajar, minat, dan kecepatan belajar yang beragam\n• Guru memberikan fleksibilitas tugas sesuai kondisi spesifik murid\n• Guru merancang aktivitas pembelajaran yang aman, inklusif, dan ramah anak",
                        'perilaku_dihindari'  => "• Guru menyamaratakan seluruh murid tanpa diferensiasi metode\n• Guru memaksakan satu cara belajar yang tidak cocok bagi semua anak\n• Guru tidak memedulikan kebutuhan khusus peserta didik",
                    ],
                    [
                        'nama_aspek'          => 'Apresiasi Usaha dan Proses',
                        'fokus_perilaku'      => 'Guru mengakui dan menghargai usaha yang ditunjukkan peserta didik',
                        'perilaku_dianjurkan' => "• Guru mengapresiasi proses belajar dan daya juang murid, bukan hanya nilai akhir\n• Guru memberi penguatan atas kemajuan sekecil apapun yang dicapai murid\n• Guru merayakan keberanian murid mencoba hal baru atau menjawab tantangan",
                        'perilaku_dihindari'  => "• Guru hanya memuji murid yang mendapatkan nilai sempurna\n• Guru mengabaikan murid yang telah bekerja keras namun belum meraih hasil maksimal\n• Guru mencela atau menertawakan kesalahan murid",
                    ],
                ],
            ],
            [
                'nama'               => 'UMPAN BALIK KONSTRUKTIF',
                'dimensi_pmm'        => 'dukungan_psikologis',
                'kategori_supervisi' => 'observasi_kelas_pmm',
                'deskripsi'          => 'Upaya memberikan informasi umpan balik yang spesifik, tepat waktu, dan berorientasi pada proses perbaikan belajar.',
                'fokus' => [
                    [
                        'nama_aspek'          => 'Umpan Balik Spesifik & Terarah',
                        'fokus_perilaku'      => 'Guru memberikan umpan balik spesifik dan berorientasi tujuan',
                        'perilaku_dianjurkan' => "• Guru menjelaskan letak kekuatan dan kekurangan karya murid secara rinci\n• Guru mengaitkan umpan balik dengan kriteria ketercapaian tujuan pembelajaran\n• Guru memberikan langkah konkret yang dapat diambil murid untuk perbaikan",
                        'perilaku_dihindari'  => "• Guru memberi umpan balik terlalu umum seperti 'Bagus' atau 'Salah' tanpa penjelasan\n• Guru tidak menjelaskan cara memperbaiki kesalahan yang dibuat\n• Guru mengomentari pribadi murid, bukan hasil karyanya",
                    ],
                    [
                        'nama_aspek'          => 'Fokus pada Proses dan Growth Mindset',
                        'fokus_perilaku'      => 'Guru memberikan umpan balik yang fokus pada proses atau usaha peserta didik',
                        'perilaku_dianjurkan' => "• Guru mengapresiasi strategi berpikir yang digunakan murid dalam menyelesaikan masalah\n• Guru mendiskusikan alternatif cara penyelesaian masalah bersama murid\n• Guru mendorong cara pandang bertumbuh (growth mindset) bahwa kesalahan adalah bagian dari belajar",
                        'perilaku_dihindari'  => "• Guru hanya membandingkan hasil kerja antar murid\n• Guru mengabaikan proses atau strategi yang sudah diupayakan murid\n• Guru fokus pada detail minor yang tidak esensial",
                    ],
                    [
                        'nama_aspek'          => 'Diskusi Reflektif Hasil Belajar',
                        'fokus_perilaku'      => 'Guru menunjukkan kesediaan mendiskusikan umpan balik dengan peserta didik',
                        'perilaku_dianjurkan' => "• Guru membuka sesi dialog terkait hasil asesmen dan respon umpan balik\n• Guru mengajak murid merefleksikan catatan perbaikan yang diterimanya\n• Guru mendengarkan penjelasan murid atas hasil pekerjaannya secara adil",
                        'perilaku_dihindari'  => "• Guru bersikap otoriter atas koreksi yang diberikan tanpa memberi ruang klarifikasi\n• Guru menolak mendengarkan penjelasan murid\n• Guru terburu-buru memberikan feedback tanpa memastikan murid memahaminya",
                    ],
                ],
            ],
        ];
    }
}
