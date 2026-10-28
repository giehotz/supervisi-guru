<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DokumenSignatureModel;
use App\Models\JadwalSupervisiModel;

class VerifikasiController extends BaseController
{
    protected $signatureModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->signatureModel = new DokumenSignatureModel();
        $this->jadwalModel    = new JadwalSupervisiModel();
        helper(['setting', 'date']);
    }

    /**
     * Halaman Publik Verifikasi Dokumen & Tanda Tangan Digital (via Scan QR Code)
     */
    public function dokumen(string $token)
    {
        $targetSignature = $this->signatureModel->getByToken($token);

        if (!$targetSignature) {
            return view('verifikasi/dokumen', [
                'isValid'         => false,
                'token'           => $token,
                'targetSignature' => null,
                'jadwal'          => null,
                'allSignatures'   => [],
                'kop'             => get_kop_data(),
            ]);
        }

        // Ambil data jadwal lengkap
        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran as guru_mapel, kelas.nama_kelas, users.username as nama_supervisor, users.nip as nip_supervisor')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->find($targetSignature['jadwal_id']);

        // Ambil seluruh tanda tangan yang terdaftar untuk jadwal ini
        $allSignatures = $this->signatureModel->getSignaturesByJadwal(
            $targetSignature['jadwal_id'],
            $targetSignature['dokumen_type']
        );

        $isFullySigned = !empty($allSignatures['kepala']);

        return view('verifikasi/dokumen', [
            'isValid'         => true,
            'token'           => $token,
            'targetSignature' => $targetSignature,
            'jadwal'          => $jadwal,
            'allSignatures'   => $allSignatures,
            'isFullySigned'   => $isFullySigned,
            'kop'             => get_kop_data(),
        ]);
    }
}
