<?php

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

if (!function_exists('generate_qr_data_uri')) {
    /**
     * Generate data-URI QR Code string menggunakan chillerlan/php-qrcode
     *
     * @param string $data
     * @return string data:image/svg+xml;base64,...
     */
    function generate_qr_data_uri(string $data): string
    {
        try {
            $options = new QROptions([
                'outputBase64' => true,
                'scale'        => 3,
            ]);

            return (new QRCode($options))->render($data);
        } catch (\Throwable $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Error generating QR Code: ' . $e->getMessage());
            }
            return '';
        }
    }
}

if (!function_exists('get_verification_url')) {
    /**
     * Dapatkan URL verifikasi publik untuk token dokumen tertentu
     *
     * @param string $token
     * @return string
     */
    function get_verification_url(string $token): string
    {
        return function_exists('base_url') ? base_url('verifikasi/dokumen/' . $token) : '/verifikasi/dokumen/' . $token;
    }
}

if (!function_exists('render_signature_block_dompdf')) {
    /**
     * Render cell tanda tangan untuk tampilan Dompdf
     *
     * @param string $roleLabel Judul jabatan / peran (e.g. "Guru yang Disupervisi", "Supervisor", "Kepala Madrasah")
     * @param string $nama Nama personil
     * @param string|null $nip NIP personil
     * @param array|null $sigRecord Data baris dokumen_signatures jika sudah tanda tangan
     * @param bool $isPolos Mode polos tanpa tanda tangan digital untuk cetak basah manual
     * @param string|null $kotaDate Tanggal/kota opsional untuk header (e.g. "Gisting, 12 Oktober 2026")
     * @return string HTML
     */
    function render_signature_block_dompdf(
        string $roleLabel,
        string $nama,
        ?string $nip,
        ?array $sigRecord = null,
        bool $isPolos = false,
        ?string $kotaDate = null
    ): string {
        $html = '<div style="text-align: center; font-size: 10pt; line-height: 1.3;">';
        
        if (!empty($kotaDate)) {
            $cleanKotaDate = nl2br(str_ireplace(['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'], '<br>', esc($kotaDate)), false);
            $html .= '<p style="margin: 0 0 2px 0;">' . $cleanKotaDate . '</p>';
        }

        $cleanRoleLabel = nl2br(str_ireplace(['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'], '<br>', esc($roleLabel)), false);
        if (!str_ends_with(rtrim(strip_tags($cleanRoleLabel)), ',')) {
            $cleanRoleLabel .= ',';
        }
        $html .= '<p style="margin: 0 0 4px 0; font-weight: 500;">' . $cleanRoleLabel . '</p>';

        // Jika mode polos (tanda tangan basah manual)
        if ($isPolos) {
            $html .= '<div style="height: 60px;"></div>';
            $html .= '<p style="margin: 2px 0; font-weight: bold; text-decoration: underline;">' . esc($nama ?: '...........................') . '</p>';
            $html .= '<p style="margin: 2px 0; font-size: 9pt;">NIP: ' . esc($nip ?: '...........................') . '</p>';
            $html .= '</div>';
            return $html;
        }

        // Jika sudah ditandatangani digital
        if (!empty($sigRecord) && !empty($sigRecord['token_verifikasi'])) {
            $verifyUrl = get_verification_url($sigRecord['token_verifikasi']);
            $qrDataUri = generate_qr_data_uri($verifyUrl);

            $tglTtdFormatted = date('d/m/Y', strtotime($sigRecord['tanggal_ttd'] ?? $sigRecord['signed_at']));

            $html .= '<div style="margin: 2px auto 4px auto; text-align: center;">';
            if ($qrDataUri) {
                $html .= '<img src="' . $qrDataUri . '" style="width: 58px; height: 58px; display: inline-block; border: 1px solid #ddd; padding: 2px; background: #fff;" alt="QR TTD"><br>';
            }
            $html .= '<span style="font-size: 7pt; color: #166534; font-weight: bold; display: block; margin-top: 1px;">Ditandatangani Elektronik</span>';
            $html .= '<span style="font-size: 6.5pt; color: #555;">Tgl: ' . esc($tglTtdFormatted) . '</span>';
            $html .= '</div>';

            $html .= '<p style="margin: 2px 0; font-weight: bold; text-decoration: underline;">' . esc($nama) . '</p>';
            $html .= '<p style="margin: 2px 0; font-size: 9pt;">NIP: ' . esc($nip ?: '-') . '</p>';
        } else {
            // Belum ditandatangani secara digital
            $html .= '<div style="height: 60px; display: table; width: 100%; border: 1px dashed #cbd5e1; border-radius: 4px; margin: 4px 0; background: #f8fafc;">';
            $html .= '<div style="display: table-cell; vertical-align: middle; text-align: center; color: #94a3b8; font-size: 8pt; font-style: italic;">[ Belum Ditandatangani ]</div>';
            $html .= '</div>';
            $html .= '<p style="margin: 2px 0; font-weight: bold; text-decoration: underline; color: #475569;">' . esc($nama ?: '...........................') . '</p>';
            $html .= '<p style="margin: 2px 0; font-size: 9pt; color: #475569;">NIP: ' . esc($nip ?: '...........................') . '</p>';
        }

        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('render_pdf_draft_watermark')) {
    /**
     * Render CSS dan elemen watermark DRAFT untuk Dompdf
     *
     * @param bool $isDraft
     * @return string
     */
    function render_pdf_draft_watermark(bool $isDraft): string
    {
        if (!$isDraft) {
            return '';
        }

        return '
        <style>
            .watermark-draft {
                position: fixed;
                top: 40%;
                left: 10%;
                width: 80%;
                text-align: center;
                opacity: 0.12;
                transform: rotate(-35deg);
                transform-origin: 50% 50%;
                z-index: -1000;
                font-family: Arial, sans-serif;
                font-size: 48pt;
                font-weight: 900;
                color: #b91c1c;
                text-transform: uppercase;
                letter-spacing: 4px;
                border: 6px dashed #b91c1c;
                padding: 15px;
                border-radius: 12px;
            }
        </style>
        <div class="watermark-draft">DRAFT / BELUM DISAHKAN</div>';
    }
}
