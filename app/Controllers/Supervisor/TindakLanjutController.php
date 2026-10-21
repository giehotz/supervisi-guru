<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\TindakLanjutSupervisiModel;

class TindakLanjutController extends BaseController
{
    protected $jadwalModel;
    protected $hasilModel;
    protected $tlModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->hasilModel  = new HasilSupervisiModel();
        $this->tlModel     = new TindakLanjutSupervisiModel();
    }

    public function review($jadwalId)
    {
        $userId = session()->get('id');

        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip, kelas.nama_kelas, kelas.fase as kelas_fase, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $userId)
            ->first();

        if (!$jadwal) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $hasil = $this->hasilModel->where('jadwal_supervisi_id', $jadwalId)->first();
        $tindakLanjut = $this->tlModel->getByJadwal($jadwalId);

        $data = [
            'title'        => 'Review Tindak Lanjut & Evaluasi Refleksi Guru (Form C & D)',
            'jadwal'       => $jadwal,
            'hasil'        => $hasil,
            'tindakLanjut' => $tindakLanjut,
        ];

        return view('supervisor/tindak_lanjut/review', $data);
    }

    public function feedbackC($jadwalId)
    {
        $userId = session()->get('id');
        $jadwal = $this->jadwalModel->where('id', $jadwalId)->where('supervisor_id', $userId)->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $tindakLanjut = $this->tlModel->getByJadwal($jadwalId);
        if (!$tindakLanjut) {
            return redirect()->back()->with('error', 'Formulir C belum diisi oleh guru.');
        }

        $this->tlModel->update($tindakLanjut['id'], [
            'respon_supervisor_c'  => $this->request->getPost('respon_supervisor_c'),
            'catatan_supervisor_c' => $this->request->getPost('catatan_supervisor_c'),
            'status_tindak_lanjut' => 'form_c_disepakati',
        ]);

        return redirect()->to(base_url('supervisor/tindak-lanjut/' . $jadwalId))
            ->with('success', 'Umpan balik dan penilaian Formulir C berhasil disimpan.');
    }

    public function evaluateD($jadwalId)
    {
        $userId = session()->get('id');
        $jadwal = $this->jadwalModel->where('id', $jadwalId)->where('supervisor_id', $userId)->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $tindakLanjut = $this->tlModel->getByJadwal($jadwalId);
        if (!$tindakLanjut || empty($tindakLanjut['inspirasi_baru'])) {
            return redirect()->back()->with('error', 'Formulir D belum diisi oleh guru.');
        }

        $this->tlModel->update($tindakLanjut['id'], [
            'penilaian_supervisor_d' => $this->request->getPost('penilaian_supervisor_d'),
            'catatan_supervisor_d'   => $this->request->getPost('catatan_supervisor_d'),
            'status_tindak_lanjut'   => 'selesai',
        ]);

        // Tandai jadwal supervisi selesai secara paripurna
        $this->jadwalModel->update($jadwalId, [
            'status'                  => 'Selesai',
            'tanggal_pasca_observasi' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('supervisor/tindak-lanjut/' . $jadwalId))
            ->with('success', 'Evaluasi akhir Formulir D berhasil disimpan! Seluruh siklus supervisi kini telah selesai.');
    }
}
