<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\DokumenSignatureModel;
use App\Models\GuruModel;
use App\Models\UserModel;
use App\Models\AuditLogModel;
use App\Models\NotifikasiModel;

class SignatureController extends BaseController
{
    protected $jadwalModel;
    protected $signatureModel;
    protected $guruModel;
    protected $userModel;
    protected $auditModel;
    protected $notifikasiModel;

    public function __construct()
    {
        $this->jadwalModel     = new JadwalSupervisiModel();
        $this->signatureModel  = new DokumenSignatureModel();
        $this->guruModel       = new GuruModel();
        $this->userModel       = new UserModel();
        $this->auditModel      = new AuditLogModel();
        $this->notifikasiModel = new NotifikasiModel();
    }

    /**
     * Memproses aksi penandatanganan digital
     */
    public function sign($jadwalId)
    {
        $userId = session()->get('id');
        $role   = session()->get('role');

        if (!$userId || !$role) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Sesi login telah berakhir. Silakan login kembali.'
            ])->setStatusCode(401);
        }

        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data jadwal supervisi tidak ditemukan.'
            ])->setStatusCode(404);
        }

        // Validasi status supervisi: hanya boleh ditandatangani jika sudah 'Selesai'
        if (strtolower($jadwal['status'] ?? '') !== 'selesai') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tanda tangan digital hanya dapat dilakukan setelah seluruh rangkaian penilaian supervisi berstatus Selesai.'
            ])->setStatusCode(400);
        }

        $dokumenType = $this->request->getPost('dokumen_type') ?: 'supervisi';
        $tanggalTtd  = $this->request->getPost('tanggal_ttd') ?: date('Y-m-d');

        // Validasi format tanggal Y-m-d
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalTtd)) {
            $tanggalTtd = date('Y-m-d');
        }

        // Tentukan data identitas penandatangan berdasarkan role
        $signerNama = '';
        $signerNip  = '';

        if ($role === 'guru') {
            $guru = $this->guruModel->where('user_id', $userId)->first();
            if (!$guru || $guru['id'] != $jadwal['guru_id']) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk menandatangani supervisi guru lain.'
                ])->setStatusCode(403);
            }
            $signerNama = $guru['nama'];
            $signerNip  = $guru['nip'] ?? '';
        } elseif ($role === 'supervisor') {
            if ($jadwal['supervisor_id'] != $userId && $role !== 'admin') {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda bukan supervisor yang ditugaskan pada jadwal supervisi ini.'
                ])->setStatusCode(403);
            }
            $user = $this->userModel->find($userId);
            $signerNama = $user['username'] ?? 'Supervisor';
            $signerNip  = $user['nip'] ?? '';
        } elseif ($role === 'kepala') {
            $user = $this->userModel->find($userId);
            $signerNama = $user['username'] ?? 'Kepala Madrasah';
            $signerNip  = $user['nip'] ?? '';
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Peran akun Anda tidak memiliki wewenang untuk menandatangani dokumen.'
            ])->setStatusCode(403);
        }

        // Validasi alur sekuensial (Guru -> Supervisor -> Kepala)
        $canSign = $this->signatureModel->canSign($jadwalId, $role, $dokumenType);
        if (!$canSign['allowed']) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $canSign['message']
            ])->setStatusCode(400);
        }

        // Generate token acak unik (SHA-256)
        $token = hash('sha256', $jadwalId . $role . $userId . microtime(true) . bin2hex(random_bytes(8)));

        $dataInsert = [
            'jadwal_id'        => $jadwalId,
            'dokumen_type'     => $dokumenType,
            'signer_role'      => $role,
            'user_id'          => $userId,
            'signer_nama'      => $signerNama,
            'signer_nip'       => $signerNip,
            'token_verifikasi' => $token,
            'tanggal_ttd'      => $tanggalTtd,
            'signed_at'        => date('Y-m-d H:i:s'),
            'ip_address'       => $this->request->getIPAddress(),
            'user_agent'       => substr((string)$this->request->getUserAgent(), 0, 250),
        ];

        try {
            $this->signatureModel->insert($dataInsert);

            // Catat log aktivitas jika tabel audit aktif
            try {
                $this->auditModel->logActivity(
                    $userId,
                    'digital_signature',
                    "Menandatangani digital dokumen {$dokumenType} jadwal #{$jadwalId} sebagai {$role} (Tgl TTD: {$tanggalTtd})"
                );
            } catch (\Throwable $ignored) {
            }

            // Kirim notifikasi in-app otomatis ke pihak berikutnya
            try {
                $guruRecord = $this->guruModel->find($jadwal['guru_id']);
                $guruUserId = $guruRecord['user_id'] ?? null;
                $namaGuru   = $guruRecord['nama'] ?? 'Guru';

                if ($role === 'guru') {
                    // Notifikasi ke Supervisor
                    if (!empty($jadwal['supervisor_id'])) {
                        $this->notifikasiModel->insert([
                            'user_id' => $jadwal['supervisor_id'],
                            'judul'   => 'Dokumen Supervisi Siap Ditandatangani',
                            'pesan'   => "Guru {$signerNama} telah menandatangani hasil supervisi #{$jadwalId}. Giliran Anda untuk menandatangani secara digital.",
                            'dibaca'  => 0,
                        ]);
                    }
                } elseif ($role === 'supervisor') {
                    // Notifikasi ke seluruh akun Kepala Madrasah
                    $kepalaUsers = $this->userModel->where('role', 'kepala')->findAll();
                    foreach ($kepalaUsers as $kepala) {
                        $this->notifikasiModel->insert([
                            'user_id' => $kepala['id'],
                            'judul'   => 'Permohonan Pengesahan Supervisi',
                            'pesan'   => "Hasil supervisi guru {$namaGuru} telah ditandatangani oleh Guru & Supervisor. Menunggu pengesahan Kepala Madrasah.",
                            'dibaca'  => 0,
                        ]);
                    }
                } elseif ($role === 'kepala') {
                    // Notifikasi ke Guru dan Supervisor
                    if ($guruUserId) {
                        $this->notifikasiModel->insert([
                            'user_id' => $guruUserId,
                            'judul'   => 'Supervisi Telah Disahkan',
                            'pesan'   => "Dokumen hasil supervisi Anda telah resmi disahkan oleh Kepala Madrasah ({$signerNama}).",
                            'dibaca'  => 0,
                        ]);
                    }
                    if (!empty($jadwal['supervisor_id'])) {
                        $this->notifikasiModel->insert([
                            'user_id' => $jadwal['supervisor_id'],
                            'judul'   => 'Supervisi Telah Disahkan',
                            'pesan'   => "Dokumen hasil supervisi guru {$namaGuru} telah disahkan resmi oleh Kepala Madrasah.",
                            'dibaca'  => 0,
                        ]);
                    }
                }
            } catch (\Throwable $ignored) {
            }

            return $this->response->setJSON([
                'status'      => 'success',
                'message'     => 'Dokumen berhasil ditandatangani secara digital!',
                'token'       => $token,
                'signed_role' => $role,
                'tanggal_ttd' => $tanggalTtd
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error saving digital signature: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan tanda tangan digital: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Reset seluruh tanda tangan digital untuk jadwal tertentu (Khusus Admin)
     */
    public function reset($jadwalId)
    {
        $userId = session()->get('id');
        $role   = session()->get('role');

        if ($role !== 'admin') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Hanya Administrator yang berwenang mereset tanda tangan digital.'
            ])->setStatusCode(403);
        }

        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data jadwal supervisi tidak ditemukan.'
            ])->setStatusCode(404);
        }

        $alasan = trim((string)$this->request->getPost('alasan'));
        if (empty($alasan)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Alasan reset tanda tangan wajib diisi.'
            ])->setStatusCode(400);
        }

        $dokumenType = $this->request->getPost('dokumen_type') ?: 'supervisi';

        try {
            $this->signatureModel->where('jadwal_id', $jadwalId)
                                 ->where('dokumen_type', $dokumenType)
                                 ->delete();

            // Catat log aktivitas
            try {
                $this->auditModel->logActivity(
                    $userId,
                    'reset_signature',
                    "Mereset seluruh tanda tangan digital dokumen {$dokumenType} jadwal #{$jadwalId}. Alasan: {$alasan}"
                );
            } catch (\Throwable $ignored) {}

            // Kirim notifikasi ke Guru & Supervisor
            try {
                $guruRecord = $this->guruModel->find($jadwal['guru_id']);
                $guruUserId = $guruRecord['user_id'] ?? null;
                $notifPesan = "Tanda tangan digital dokumen hasil supervisi #{$jadwalId} telah di-reset oleh Admin untuk revisi nilai. Alasan: {$alasan}";

                if ($guruUserId) {
                    $this->notifikasiModel->insert([
                        'user_id' => $guruUserId,
                        'judul'   => 'Tanda Tangan Supervisi Di-reset',
                        'pesan'   => $notifPesan,
                        'dibaca'  => 0,
                    ]);
                }
                if (!empty($jadwal['supervisor_id'])) {
                    $this->notifikasiModel->insert([
                        'user_id' => $jadwal['supervisor_id'],
                        'judul'   => 'Tanda Tangan Supervisi Di-reset',
                        'pesan'   => $notifPesan,
                        'dibaca'  => 0,
                    ]);
                }
            } catch (\Throwable $ignored) {}

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Seluruh tanda tangan digital berhasil di-reset. Penilaian kini dapat diperbarui.'
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error resetting signatures: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mereset tanda tangan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Cek status tanda tangan untuk suatu jadwal
     */
    public function status($jadwalId)
    {
        $signatures = $this->signatureModel->getSignaturesByJadwal($jadwalId);
        $isFull     = $this->signatureModel->isFullySigned($jadwalId);

        return $this->response->setJSON([
            'status'     => 'success',
            'signatures' => $signatures,
            'is_fully_signed' => $isFull
        ]);
    }

    /**
     * Sahkan seluruh peran (Guru, Supervisor, Kepala Madrasah) sekaligus (Khusus Admin)
     */
    public function signAllAdmin($jadwalId)
    {
        $userId = session()->get('id');
        $role   = session()->get('role');

        if ($role !== 'admin') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Hanya Administrator yang berwenang melakukan pengesahan menyeluruh.'
            ])->setStatusCode(403);
        }

        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data jadwal supervisi tidak ditemukan.'
            ])->setStatusCode(404);
        }

        if (strtolower($jadwal['status'] ?? '') !== 'selesai') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tanda tangan digital hanya dapat dilakukan setelah status supervisi Selesai.'
            ])->setStatusCode(400);
        }

        $dokumenType = $this->request->getPost('dokumen_type') ?: 'supervisi';
        $tanggalTtd  = $this->request->getPost('tanggal_ttd') ?: ($jadwal['tanggal_supervisi'] ?? date('Y-m-d'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalTtd)) {
            $tanggalTtd = date('Y-m-d');
        }

        // Ambil data personil
        $guruRecord = $this->guruModel->find($jadwal['guru_id']);
        $guruNama   = $guruRecord['nama'] ?? 'Guru';
        $guruNip    = $guruRecord['nip'] ?? '';
        $guruUserId = $guruRecord['user_id'] ?? null;

        $supervisorRecord = !empty($jadwal['supervisor_id']) ? $this->userModel->find($jadwal['supervisor_id']) : null;
        $supervisorNama   = $supervisorRecord['username'] ?? 'Supervisor';
        $supervisorNip    = $supervisorRecord['nip'] ?? '';

        $kepalaRecord = $this->userModel->where('role', 'kepala')->first();
        $kepalaNama   = $kepalaRecord['username'] ?? 'Kepala Madrasah';
        $kepalaNip    = $kepalaRecord['nip'] ?? '';

        $existingSigs = $this->signatureModel->getSignaturesByJadwal($jadwalId, $dokumenType);

        $rolesData = [
            'guru' => [
                'nama' => $guruNama,
                'nip'  => $guruNip,
            ],
            'supervisor' => [
                'nama' => $supervisorNama,
                'nip'  => $supervisorNip,
            ],
            'kepala' => [
                'nama' => $kepalaNama,
                'nip'  => $kepalaNip,
            ],
        ];

        $now = date('Y-m-d H:i:s');
        $ip  = $this->request->getIPAddress();
        $ua  = substr((string)$this->request->getUserAgent(), 0, 250);

        try {
            foreach ($rolesData as $roleKey => $info) {
                // Hanya simpan jika role belum pernah tanda tangan (idempotent)
                if (empty($existingSigs[$roleKey])) {
                    $token = hash('sha256', $jadwalId . $roleKey . $userId . microtime(true) . bin2hex(random_bytes(8)));
                    $this->signatureModel->insert([
                        'jadwal_id'        => $jadwalId,
                        'dokumen_type'     => $dokumenType,
                        'signer_role'      => $roleKey,
                        'user_id'          => $userId, // Admin sebagai eksekutor
                        'signer_nama'      => $info['nama'],
                        'signer_nip'       => $info['nip'],
                        'token_verifikasi' => $token,
                        'tanggal_ttd'      => $tanggalTtd,
                        'signed_at'        => $now,
                        'ip_address'       => $ip,
                        'user_agent'       => $ua,
                    ]);
                }
            }

            // Catat log aktivitas
            try {
                $this->auditModel->logActivity(
                    $userId,
                    'admin_batch_signature',
                    "Admin mengesahkan seluruh tanda tangan digital (Guru, Supervisor, Kepala) jadwal #{$jadwalId} (Tgl TTD: {$tanggalTtd})"
                );
            } catch (\Throwable $ignored) {}

            // Kirim notifikasi in-app
            try {
                $notifPesan = "Dokumen hasil supervisi #{$jadwalId} ({$guruNama}) telah disahkan lengkap secara resmi oleh Administrator.";
                if ($guruUserId) {
                    $this->notifikasiModel->insert([
                        'user_id' => $guruUserId,
                        'judul'   => 'Supervisi Telah Disahkan Lengkap',
                        'pesan'   => $notifPesan,
                        'dibaca'  => 0,
                    ]);
                }
                if (!empty($jadwal['supervisor_id'])) {
                    $this->notifikasiModel->insert([
                        'user_id' => $jadwal['supervisor_id'],
                        'judul'   => 'Supervisi Telah Disahkan Lengkap',
                        'pesan'   => $notifPesan,
                        'dibaca'  => 0,
                    ]);
                }
            } catch (\Throwable $ignored) {}

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Seluruh tanda tangan digital berhasil disahkan lengkap oleh Administrator!'
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error in signAllAdmin: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengesahkan dokumen: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
