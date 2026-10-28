<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumenSignatureModel extends Model
{
    protected $table            = 'dokumen_signatures';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jadwal_id',
        'dokumen_type',
        'signer_role',
        'user_id',
        'signer_nama',
        'signer_nip',
        'token_verifikasi',
        'tanggal_ttd',
        'signed_at',
        'ip_address',
        'user_agent'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil seluruh tanda tangan untuk jadwal supervisi tertentu
     * Return dalam bentuk array associative [ 'guru' => [...], 'supervisor' => [...], 'kepala' => [...] ]
     */
    public function getSignaturesByJadwal($jadwalId, $dokumenType = 'supervisi'): array
    {
        $rows = $this->where('jadwal_id', $jadwalId)
                     ->where('dokumen_type', $dokumenType)
                     ->findAll();

        $result = [
            'guru'       => null,
            'supervisor' => null,
            'kepala'     => null,
        ];

        foreach ($rows as $row) {
            $role = strtolower($row['signer_role']);
            if (array_key_exists($role, $result)) {
                $result[$role] = $row;
            }
        }

        return $result;
    }

    /**
     * Periksa apakah dokumen sudah mulai ditandatangani oleh salah satu pihak
     */
    public function hasAnySignature($jadwalId, $dokumenType = 'supervisi'): bool
    {
        return $this->where('jadwal_id', $jadwalId)
                    ->where('dokumen_type', $dokumenType)
                    ->countAllResults() > 0;
    }

    /**
     * Periksa apakah dokumen sudah disahkan final (oleh Kepala Madrasah)
     */
    public function isFullySigned($jadwalId, $dokumenType = 'supervisi'): bool
    {
        $sigs = $this->getSignaturesByJadwal($jadwalId, $dokumenType);
        return !empty($sigs['kepala']);
    }

    /**
     * Validasi apakah role tertentu diizinkan tanda tangan sesuai urutan sekuensial:
     * Guru -> Supervisor -> Kepala Madrasah
     */
    public function canSign($jadwalId, $role, $dokumenType = 'supervisi'): array
    {
        $sigs = $this->getSignaturesByJadwal($jadwalId, $dokumenType);
        $role = strtolower($role);

        if (!empty($sigs[$role])) {
            return [
                'allowed' => false,
                'message' => 'Anda sudah menandatangani dokumen ini pada ' . date('d/m/Y H:i', strtotime($sigs[$role]['signed_at']))
            ];
        }

        if ($role === 'guru') {
            return ['allowed' => true, 'message' => ''];
        }

        if ($role === 'supervisor') {
            if (empty($sigs['guru'])) {
                return [
                    'allowed' => false,
                    'message' => 'Guru yang bersangkutan belum menandatangani dokumen ini. Menunggu tanda tangan Guru terlebih dahulu.'
                ];
            }
            return ['allowed' => true, 'message' => ''];
        }

        if ($role === 'kepala') {
            if (empty($sigs['guru'])) {
                return [
                    'allowed' => false,
                    'message' => 'Guru belum menandatangani dokumen ini.'
                ];
            }
            if (empty($sigs['supervisor'])) {
                return [
                    'allowed' => false,
                    'message' => 'Supervisor belum menandatangani dokumen ini. Menunggu tanda tangan Supervisor terlebih dahulu.'
                ];
            }
            return ['allowed' => true, 'message' => ''];
        }

        return ['allowed' => false, 'message' => 'Role tidak dikenal.'];
    }

    /**
     * Cari tanda tangan berdasarkan token verifikasi publik
     */
    public function getByToken(string $token): ?array
    {
        return $this->where('token_verifikasi', $token)->first();
    }
}
