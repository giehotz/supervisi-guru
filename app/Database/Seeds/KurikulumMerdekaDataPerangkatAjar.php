<?php

namespace App\Database\Seeds;

class KurikulumMerdekaDataPerangkatAjar
{
    public static function data(): array
    {
        return [
            [
                'nama'               => 'TELAAH ALUR TUJUAN PEMBELAJARAN (ATP)',
                'dimensi_pmm'        => null,
                'kategori_supervisi' => 'perangkat_ajar',
                'deskripsi'          => 'Instrumen verifikasi dan telaah kesesuaian Alur Tujuan Pembelajaran (ATP) terhadap Capaian Pembelajaran Fase Kurikulum Merdeka.',
                'fokus' => [
                    [
                        'nama_aspek'          => 'Kesesuaian CP dan Penurunan ke TP',
                        'fokus_perilaku'      => 'Capaian Pembelajaran (CP) diturunkan secara tepat ke dalam Tujuan Pembelajaran (TP) yang memuat Kompetensi dan Lingkup Materi',
                        'perilaku_dianjurkan' => 'TP memuat kompetensi operasional terukur dan lingkup materi yang esensial sesuai Fase.',
                        'perilaku_dihindari'  => 'TP hanya mengulang CP tanpa perincian kompetensi dan materi.',
                    ],
                    [
                        'nama_aspek'          => 'Kelogisan Alur dan Tahapan Belajar',
                        'fokus_perilaku'      => 'Urutan Tujuan Pembelajaran dalam ATP disusun secara logis dan runtut (konkret ke abstrak, mudah ke kompleks)',
                        'perilaku_dianjurkan' => 'Pengurutan TP memperhatikan prasyarat kompetensi dan kesinambungan materi.',
                        'perilaku_dihindari'  => 'Urutan TP melompat tanpa mempertimbangkan pemahaman awal.',
                    ],
                    [
                        'nama_aspek'          => 'Alokasi Waktu dan Integrasi Karakter',
                        'fokus_perilaku'      => 'ATP mencantumkan alokasi Jam Pelajaran (JP) yang realistis serta penguatan Profil Pelajar Pancasila',
                        'perilaku_dianjurkan' => 'Perkiraan JP proporsional dengan kedalaman materi dan dimensi profil lulusan terintegrasi.',
                        'perilaku_dihindari'  => 'Alokasi waktu tidak realistis dan tidak ada integrasi nilai karakter.',
                    ],
                ],
            ],
            [
                'nama'               => 'TELAAH MODUL AJAR / RPP+ KURIKULUM MERDEKA',
                'dimensi_pmm'        => null,
                'kategori_supervisi' => 'perangkat_ajar',
                'deskripsi'          => 'Instrumen telaah kelengkapan dan kualitas Modul Ajar / RPP+ berbasis pembelajaran mendalam dan berdiferensiasi.',
                'fokus' => [
                    [
                        'nama_aspek'          => 'Informasi Umum & Identitas Modul',
                        'fokus_perilaku'      => 'Memuat identitas modul, Fase/Kelas, Kompetensi Awal, Profil Pelajar Pancasila, Sarpras, dan Target Peserta Didik',
                        'perilaku_dianjurkan' => 'Komponen informasi umum lengkap dan relevan dengan karakteristik satuan pendidikan.',
                        'perilaku_dihindari'  => 'Informasi umum tidak lengkap atau hanya copy-paste tanpa adaptasi.',
                    ],
                    [
                        'nama_aspek'          => 'Tujuan Pembelajaran & KKTP',
                        'fokus_perilaku'      => 'Tujuan Pembelajaran jelas serta dilengkapi Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) yang terukur',
                        'perilaku_dianjurkan' => 'KKTP menggunakan rubrik atau interval nilai yang mendeskripsikan capaian kompetensi.',
                        'perilaku_dihindari'  => 'KKTP hanya berupa angka KKM tunggal tanpa deskripsi ketercapaian.',
                    ],
                    [
                        'nama_aspek'          => 'Pemahaman Bermakna & Pertanyaan Pemantik',
                        'fokus_perilaku'      => 'Memuat pemahaman bermakna dan pertanyaan pemantik yang merangsang rasa ingin tahu serta relevan dengan kehidupan nyata',
                        'perilaku_dianjurkan' => 'Pertanyaan pemantik bersifat terbuka dan memicu eksplorasi konsep.',
                        'perilaku_dihindari'  => 'Pertanyaan pemantik bersifat tertutup atau tes hafalan langsung.',
                    ],
                    [
                        'nama_aspek'          => 'Kegiatan Pembelajaran Berdiferensiasi',
                        'fokus_perilaku'      => 'Langkah kegiatan memfasilitasi pembelajaran berdiferensiasi (konten, proses, atau produk) dan berpusat pada murid',
                        'perilaku_dianjurkan' => 'Terdapat diferensiasi kegiatan/tugas untuk mengakomodasi keberagaman murid.',
                        'perilaku_dihindari'  => 'Kegiatan belajar seragam dan didominasi ceramah satu arah.',
                    ],
                    [
                        'nama_aspek'          => 'Rancangan Asesmen Awal, Formatif, & Sumatif',
                        'fokus_perilaku'      => 'Memuat rencana asesmen awal diagnostik, asesmen formatif selama proses, dan asesmen sumatif dengan instrumen yang jelas',
                        'perilaku_dianjurkan' => 'Asesmen formatif digunakan untuk umpan balik pembelajaran berkelanjutan.',
                        'perilaku_dihindari'  => 'Hanya ada asesmen sumatif di akhir tanpa asesmen formatif berkala.',
                    ],
                    [
                        'nama_aspek'          => 'Lampiran LKPD dan Bahan Ajar',
                        'fokus_perilaku'      => 'Menyertakan Lembar Kerja Peserta Didik (LKPD), bahan bacaan guru/murid, glosarium, dan daftar pustaka',
                        'perilaku_dianjurkan' => 'LKPD interaktif, memandu penyelidikan, dan ramah anak.',
                        'perilaku_dihindari'  => 'Lampiran kosong atau hanya berupa bank soal pilihan ganda.',
                    ],
                ],
            ],
        ];
    }
}
