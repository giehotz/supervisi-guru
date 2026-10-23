<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SupervisorModel;

class PenggunaController extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $supervisorModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->supervisorModel = new SupervisorModel();
    }

    public function index()
    {
        $data['users'] = $this->userModel->findAll();
        return view('admin/pengguna/index', $data);
    }
    public function semua()
    {
        $data['users'] = $this->userModel->findAll();
        return view('admin/pengguna/index', $data);
    }
    public function guru()
    {
        $data['gurus'] = $this->guruModel
            ->select('guru.*, guru.user_id as account_id, users.username, users.email, guru.is_supervisor')
            ->join('users', 'users.id = guru.user_id', 'left')
            ->findAll();
        return view('admin/pengguna/guru', $data);
    }
    public function supervisor()
    {
        $data['supervisors'] = $this->supervisorModel
            ->select('supervisor.*, users.username, users.email, guru.nama as nama_guru')
            ->join('users', 'users.id = supervisor.user_id', 'left')
            ->join('guru', 'guru.user_id = supervisor.user_id', 'left')
            ->findAll();
        return view('admin/pengguna/supervisor', $data);
    }
    public function create()
    {
        return view('admin/pengguna/create');
    }
    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'username' => 'required|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role' => 'required|in_list[admin,kepala,supervisor,guru]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check if is_supervisor checkbox is checked
        $isSupervisor = ($this->request->getPost('role') == 'guru' && $this->request->getPost('is_supervisor')) ? 1 : 0;

        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $isSupervisor ? 'supervisor' : $this->request->getPost('role'), // Set role to supervisor if is_supervisor is checked
            'status' => 'Aktif',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $userId = $this->userModel->insert($userData);

            if ($userId) {
                // If role is guru or supervisor, create guru record
                if ($this->request->getPost('role') == 'guru' || $isSupervisor) {
                    $guruData = [
                        'user_id' => $userId,
                        'nama' => $this->request->getPost('nama_lengkap'),
                        'nip' => $this->request->getPost('nip'),
                        'pangkat_golongan' => $this->request->getPost('pangkat_golongan'),
                        'mata_pelajaran' => $this->request->getPost('mata_pelajaran'),
                        'status_kepegawaian' => $this->request->getPost('status_kepegawaian'),
                        'is_supervisor' => $isSupervisor, // Set is_supervisor field
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->guruModel->insert($guruData);

                    // If is_supervisor is checked, also create supervisor record
                    if ($isSupervisor) {
                        $supervisorData = [
                            'user_id' => $userId,
                            'tanggal_penugasan' => date('Y-m-d'),
                            'status' => 'Aktif'
                        ];
                        $this->supervisorModel->insert($supervisorData);
                    }
                }

                return redirect()->to('/admin/pengguna')->with('success', 'User berhasil ditambahkan');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user');
            }
        } catch (\Exception $e) {
            // Check if it's a duplicate entry error
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()->withInput()->with('error', 'Username atau email sudah digunakan. Silakan gunakan username atau email lain.');
            } else {
                // For other exceptions, show a generic error message
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
            }
        }
    }
    public function edit($id)
    {
        $data['user'] = $this->userModel->find($id);
        return view('admin/pengguna/edit', $data);
    }
    public function editGuru($id = null)
    {
        if (!$id) {
            return redirect()->to('/admin/pengguna/guru')->with('error', 'ID Pengguna tidak valid');
        }

        $data['user'] = $this->userModel->find($id);

        if (!$data['user']) {
            return redirect()->to('/admin/pengguna/guru')->with('error', 'User tidak ditemukan');
        }

        // Check if user actually has role 'guru'
        if ($data['user']['role'] != 'guru') {
            // Redirect to the general edit page if user is not a guru
            return redirect()->to('/admin/pengguna/' . $id . '/edit');
        }

        $data['guru'] = $this->guruModel->where('user_id', $id)->first();
        return view('admin/pengguna/edit_guru', $data);
    }
    public function editSupervisor($id)
    {
        $data['user'] = $this->userModel->find($id);
        $data['supervisor'] = $this->supervisorModel->where('user_id', $id)->first();
        $data['gurus'] = $this->guruModel->findAll();
        return view('admin/pengguna/edit_supervisor', $data);
    }
    public function update($id)
    {
        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status')
        ];

        // Only update password if provided
        if ($this->request->getPost('password')) {
            $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        try {
            $result = $this->userModel->update($id, $userData);

            if ($result) {
                return redirect()->to('/admin/pengguna')->with('success', 'User berhasil diupdate');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengupdate user');
            }
        } catch (\Exception $e) {
            // Check if it's a duplicate entry error
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()->withInput()->with('error', 'Username atau email sudah digunakan. Silakan gunakan username atau email lain.');
            } else {
                // For other exceptions, show a generic error message
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
            }
        }
    }
    public function updateGuru($id)
    {
        // Get user data
        $user = $this->userModel->find($id);

        // Prevent updating non-guru users through this method
        if ($user['role'] != 'guru' && $user['role'] != 'supervisor') {
            return redirect()->back()->with('error', 'User ini bukan guru atau supervisor, tidak dapat diupdate melalui form guru');
        }

        // Validation rules
        $validation = \Config\Services::validation();

        $rules = [
            'nama' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Check if is_supervisor checkbox is checked
        $isSupervisor = $this->request->getPost('is_supervisor') ? 1 : 0;

        // Update user data (email is kept as is)
        $userData = [
            'status' => $this->request->getPost('status'),
            'role' => $isSupervisor ? 'supervisor' : 'guru' // Set role based on is_supervisor
        ];

        // Only update password if provided
        if ($this->request->getPost('password')) {
            $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userResult = $this->userModel->update($id, $userData);

        // Update guru data
        $guruData = [
            'nama' => $this->request->getPost('nama'),
            'nip' => $this->request->getPost('nip'),
            'pangkat_golongan' => $this->request->getPost('pangkat_golongan'),
            'mata_pelajaran' => $this->request->getPost('mata_pelajaran'),
            'status_kepegawaian' => $this->request->getPost('status_kepegawaian'),
            'is_supervisor' => $isSupervisor // Set is_supervisor field
        ];

        // Check if guru record exists for this user
        $guruRecord = $this->guruModel->where('user_id', $id)->first();

        if ($guruRecord) {
            // Update existing guru record
            $guruResult = $this->guruModel->where('user_id', $id)->set($guruData)->update();
        } else {
            // Create new guru record if it doesn't exist
            $guruData['user_id'] = $id;
            $guruResult = $this->guruModel->insert($guruData);
        }

        // Handle role change to supervisor
        if ($isSupervisor) {
            // Check if supervisor record exists for this user
            $supervisorRecord = $this->supervisorModel->where('user_id', $id)->first();

            if (!$supervisorRecord) {
                // Create supervisor record if it doesn't exist
                $supervisorData = [
                    'user_id' => $id,
                    'tanggal_penugasan' => date('Y-m-d'),
                    'status' => 'Aktif'
                ];
                $this->supervisorModel->insert($supervisorData);
            }
        }

        if ($userResult && $guruResult) {
            // Redirect based on the new role
            if ($isSupervisor) {
                return redirect()->to('/admin/pengguna/supervisor')->with('success', 'Guru berhasil diupdate dan dijadikan supervisor');
            } else {
                return redirect()->to('/admin/pengguna/guru')->with('success', 'Guru berhasil diupdate');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate guru');
        }
    }
    public function updateSupervisor($id)
    {
        // Get user data
        $user = $this->userModel->find($id);

        // Prevent updating non-supervisor users through this method
        if ($user['role'] != 'supervisor') {
            return redirect()->back()->with('error', 'User ini bukan supervisor, tidak dapat diupdate melalui form supervisor');
        }

        // Update user data
        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status')
        ];

        // Only update password if provided
        if ($this->request->getPost('password')) {
            $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        // Remove null or empty values to prevent "There is no data to update" error
        $userData = array_filter($userData, function ($value) {
            return !is_null($value) && $value !== '';
        });

        $userResult = true; // Default to true, will be updated if we actually perform an update

        // Only update if there's data to update
        if (!empty($userData)) {
            $userResult = $this->userModel->update($id, $userData);
        }

        // Update supervisor data
        $supervisorData = [
            'tanggal_penugasan' => $this->request->getPost('tanggal_penugasan'),
            'status' => $this->request->getPost('supervisor_status')
        ];

        // Remove null or empty values to prevent "There is no data to update" error
        $supervisorData = array_filter($supervisorData, function ($value) {
            return !is_null($value) && $value !== '';
        });

        $supervisorResult = true; // Default to true, will be updated if we actually perform an update

        // Check if supervisor record exists for this user
        $supervisorRecord = $this->supervisorModel->where('user_id', $id)->first();

        if ($supervisorRecord) {
            // Update existing supervisor record only if there's data to update
            if (!empty($supervisorData)) {
                $supervisorResult = $this->supervisorModel->where('user_id', $id)->set($supervisorData)->update();
            }
        } else {
            // Create new supervisor record if it doesn't exist
            $supervisorData['user_id'] = $id;
            $supervisorResult = $this->supervisorModel->insert($supervisorData);
        }

        // Handle role change from supervisor to guru
        if ($userData['role'] != 'supervisor') {
            // If role is changed from supervisor to something else, we might want to remove the supervisor record
            // But we'll keep it for now to maintain history
        }

        if ($userResult && $supervisorResult) {
            // Redirect based on the new role
            if (isset($userData['role']) && $userData['role'] == 'guru') {
                return redirect()->to('/admin/pengguna/guru')->with('success', 'Supervisor berhasil diupdate dan dikembalikan menjadi guru');
            } else {
                return redirect()->to('/admin/pengguna/supervisor')->with('success', 'Supervisor berhasil diupdate');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate supervisor');
        }
    }
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('/admin/pengguna')->with('error', 'ID Pengguna tidak valid');
        }

        $result = $this->userModel->delete($id);

        if ($result) {
            return redirect()->to('/admin/pengguna')->with('success', 'User berhasil dihapus');
        } else {
            return redirect()->to('/admin/pengguna')->with('error', 'Gagal menghapus user');
        }
    }
    public function show($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/pengguna')->with('error', 'User tidak ditemukan');
        }

        $data['user'] = $user;

        // Load related data based on role
        if ($user['role'] === 'guru') {
            $data['guru'] = $this->guruModel->where('user_id', $id)->first();
        } elseif ($user['role'] === 'supervisor') {
            $data['supervisor'] = $this->supervisorModel->where('user_id', $id)->first();
        }

        return view('admin/pengguna/show', $data);
    }
    public function makeSupervisor($id)
    {
        // Get guru data
        $guru = $this->guruModel->find($id);

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Update guru to set is_supervisor = 1
        $this->guruModel->update($id, ['is_supervisor' => 1]);

        // Update user role to supervisor
        $this->userModel->update($guru['user_id'], ['role' => 'supervisor']);

        // Check if supervisor record exists
        $supervisorRecord = $this->supervisorModel->where('user_id', $guru['user_id'])->first();

        if (!$supervisorRecord) {
            // Create supervisor record
            $supervisorData = [
                'user_id' => $guru['user_id'],
                'tanggal_penugasan' => date('Y-m-d'),
                'status' => 'Aktif'
            ];
            $this->supervisorModel->insert($supervisorData);
        }

        return redirect()->to('/admin/pengguna/supervisor')->with('success', 'Guru berhasil diangkat menjadi supervisor.');
    }
    public function toggleStatus($id)
    {
        $user = $this->userModel->find($id);

        if ($user) {
            $newStatus = ($user['status'] == 'Aktif') ? 'Non-aktif' : 'Aktif';
            $this->userModel->update($id, ['status' => $newStatus]);

            return redirect()->back()->with('success', 'Status pengguna berhasil diubah.');
        }

        return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
    }
    public function resetPassword($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        $newPassword = $this->request->getPost('password') ?: '12345678';

        if (strlen($newPassword) < 8) {
            return redirect()->back()->with('error', 'Password minimal 8 karakter.');
        }

        $this->userModel->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        return redirect()->back()->with('success', "Password untuk pengguna '{$user['username']}' berhasil direset menjadi: {$newPassword}");
    }
}
