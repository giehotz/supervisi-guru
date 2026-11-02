<?php

namespace App\Database\Seeds;

class KurikulumMerdekaDataManajemenKelas
{
    public static function data(): array
    {
        return [
            [
                'nama'               => 'PENERAPAN DISIPLIN POSITIF',
                'dimensi_pmm'        => 'manajemen_kelas',
                'kategori_supervisi' => 'observasi_kelas_pmm',
                'deskripsi'          => 'Upaya menerapkan prinsip disiplin positif untuk mengelola perilaku dan kebiasaan kelas yang disepakati bersama.',
                'fokus' => [
                    [
                        'nama_aspek'          => 'Refleksi Dinamika Kelas',
                        'fokus_perilaku'      => 'Guru melakukan refleksi dinamika kelas untuk menerapkan kesepakatan kelas',
                        'perilaku_dianjurkan' => "• Guru mengajak peserta didik melakukan refleksi dinamika kelas mengacu kesepakatan kelas\n• Guru menunjukkan kesediaan mendengarkan pandangan peserta didik tentang dinamika kelas\n• Guru bersikap adaptif sehingga bersedia mengubah kesepakatan kelas bila diperlukan",
                        'perilaku_dihindari'  => "• Guru mengabaikan pendapat peserta didik tentang apa yang terjadi di kelas\n• Guru bersikap defensif dalam menyikapi umpan balik dari peserta didik terkait kedisiplinan\n• Guru menerapkan hukuman fisik atau intimidasi verbal",
                    ],
                    [
                        'nama_aspek'          => 'Penguatan Positif Perilaku',
                        'fokus_perilaku'      => 'Guru melakukan penguatan positif terhadap perilaku yang sesuai atau mendukung kesepakatan kelas',
                        'perilaku_dianjurkan' => "• Guru memberi pujian terhadap perilaku peserta didik yang sesuai kesepakatan kelas\n• Guru memberi penguatan positif dengan beragam cara yang bermakna\n• Guru mengakui suatu perilaku positif secara spesifik dan menjelaskan alasannya",
                        'perilaku_dihindari'  => "• Guru tidak konsisten dalam memberikan penguatan positif\n• Guru memberikan reward berlebihan yang mengurangi motivasi intrinsik\n• Guru membanding-bandingkan pencapaian antar murid secara tidak adil",
                    ],
                    [
                        'nama_aspek'          => 'Fasilitasi Restitusi Pelanggaran',
                        'fokus_perilaku'      => 'Guru memfasilitasi peserta didik menyadari konsekuensi dan memperbaiki perilaku melanggarnya (restitusi)',
                        'perilaku_dianjurkan' => "• Guru membantu peserta didik menyadari konsekuensi dari perilaku melanggarnya\n• Guru mendengarkan sudut pandang peserta didik terhadap perilaku melanggarnya\n• Guru memberikan dukungan pada peserta didik dalam melakukan perbaikan perilakunya",
                        'perilaku_dihindari'  => "• Guru langsung memberikan hukuman tanpa menanyakan alasan\n• Guru mempermalukan peserta didik di depan teman-temannya\n• Guru bersikap acuh tak acuh terhadap pelanggaran berulang",
                    ],
                ],
            ],
            [
                'nama'               => 'KETERATURAN SUASANA KELAS',
                'dimensi_pmm'        => 'manajemen_kelas',
                'kategori_supervisi' => 'observasi_kelas_pmm',
                'deskripsi'          => 'Upaya membangun suasana kelas yang kondusif untuk proses belajar, dengan minimal gangguan yang mengalihkan perhatian.',
                'fokus' => [
                    [
                        'nama_aspek'          => 'Komunikasi Positif Kondusif',
                        'fokus_perilaku'      => 'Guru melakukan komunikasi positif untuk membangun suasana kelas yang kondusif',
                        'perilaku_dianjurkan' => "• Guru memanggil murid dengan menyebut namanya secara hangat\n• Guru menyampaikan harapan positif terhadap iklim belajar di kelas\n• Guru melakukan aktivitas pencair suasana (ice breaking) yang relevan",
                        'perilaku_dihindari'  => "• Guru memanggil murid dengan sebutan bernada merendahkan/label negatif\n• Guru menggunakan kata-kata bernada sarkasme atau ancaman\n• Guru mengabaikan ketegangan suasana kelas",
                    ],
                    [
                        'nama_aspek'          => 'Strategi Pengelompokan Efektif',
                        'fokus_perilaku'      => 'Guru melakukan strategi pengelompokan untuk mengaktifkan keterlibatan peserta didik',
                        'perilaku_dianjurkan' => "• Guru mengelompokkan murid dengan menyampaikan tujuannya secara transparan\n• Guru menyediakan beragam peran dalam kelompok agar semua anggota aktif\n• Guru mengajak murid berinteraksi dan berkolaborasi dalam kelompok",
                        'perilaku_dihindari'  => "• Guru membiarkan kelompok didominasi hanya oleh murid tertentu\n• Guru membagi kelompok tanpa mempertimbangkan kebutuhan dan dinamika belajar\n• Guru tidak mendampingi proses interaksi antarkelompok",
                    ],
                    [
                        'nama_aspek'          => 'Mengingatkan Aturan/Kesepakatan',
                        'fokus_perilaku'      => 'Guru membuat dan mengingatkan aturan/kesepakatan kelas',
                        'perilaku_dianjurkan' => "• Guru membuat aturan/kesepakatan kelas bersama peserta didik\n• Guru mengajak peserta didik mengingat aturan/kesepakatan yang telah disepakati\n• Guru mengajak peserta didik menilai seberapa efektif pelaksanaan aturan kelas",
                        'perilaku_dihindari'  => "• Guru menetapkan aturan sepihak tanpa melibatkan murid\n• Guru mengabaikan pelanggaran kesepakatan kelas\n• Guru membuat aturan yang tidak realistis untuk dijalankan",
                    ],
                ],
            ],
        ];
    }
}
