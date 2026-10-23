<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SupervisorModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PenggunaSyncController extends BaseController
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

    public function syncGuruUsernames()
    {
        // Get all users who don't have a username yet (NULL or empty) and have a corresponding guru record
        $usersWithoutUsername = $this->userModel
            ->select('users.*, guru.nama as guru_nama')
            ->join('guru', 'users.id = guru.user_id', 'left')
            ->groupStart()
            ->where('users.username IS NULL')
            ->orWhere('users.username', '')
            ->groupEnd()
            ->where('guru.user_id IS NOT NULL')
            ->findAll();

        // Get all existing usernames to avoid N+1 queries
        $existingUsernames = $this->userModel->findColumn('username') ?? [];
        $existingUsernamesMap = array_flip($existingUsernames);

        $updateData = [];
        $syncCount = 0;

        foreach ($usersWithoutUsername as $user) {
            // Check if this user has a corresponding guru record
            if (!empty($user['guru_nama'])) {
                // Create a base username from the guru's name
                // Basic sanitization: lowercase and replace spaces with underscores
                $baseUsername = strtolower(str_replace(' ', '_', trim($user['guru_nama'])));
                $baseUsername = preg_replace('/[^a-z0-9_]/', '', $baseUsername);

                $username = $baseUsername;
                $counter = 1;

                // Check if the username already exists in our map
                while (isset($existingUsernamesMap[$username])) {
                    $username = $baseUsername . '_' . $counter;
                    $counter++;

                    // Safety check to prevent infinite loop
                    if ($counter > 100) {
                        break;
                    }
                }

                // Add to map so we don't reuse it in this batch
                $existingUsernamesMap[$username] = true;

                // Add to batch update data
                $updateData[] = [
                    'id' => $user['id'],
                    'username' => $username
                ];
                $syncCount++;
            }
        }

        if (!empty($updateData)) {
            $this->userModel->updateBatch($updateData, 'id');
        }

        return redirect()->back()->with('success', "$syncCount pengguna berhasil disinkronisasi dengan data guru.");
    }
    public function syncGuruNip()
    {
        // Get all users that have a corresponding guru record
        $users = $this->userModel
            ->select('users.id as user_id, users.nip as user_nip, guru.nip as guru_nip')
            ->join('guru', 'users.id = guru.user_id', 'left')
            ->where('guru.user_id IS NOT NULL')
            ->findAll();

        $updateData = [];
        $syncCount = 0;

        foreach ($users as $user) {
            // If user's NIP is empty or different from guru's NIP, update it
            if ((empty($user['user_nip']) && !empty($user['guru_nip'])) ||
                (!empty($user['user_nip']) && !empty($user['guru_nip']) && $user['user_nip'] != $user['guru_nip'])
            ) {

                $updateData[] = [
                    'id' => $user['user_id'],
                    'nip' => $user['guru_nip']
                ];
                $syncCount++;
            }
        }

        if (!empty($updateData)) {
            $this->userModel->updateBatch($updateData, 'id');
        }

        return redirect()->back()->with('success', "$syncCount pengguna berhasil disinkronisasi NIP-nya dengan data guru.");
    }
    public function syncGuruData()
    {
        // 1. Sync Usernames
        $usersWithoutUsername = $this->userModel
            ->select('users.*, guru.nama as guru_nama')
            ->join('guru', 'users.id = guru.user_id', 'left')
            ->groupStart()
            ->where('users.username IS NULL')
            ->orWhere('users.username', '')
            ->groupEnd()
            ->where('guru.user_id IS NOT NULL')
            ->findAll();

        $existingUsernames = $this->userModel->findColumn('username') ?? [];
        $existingUsernamesMap = array_flip($existingUsernames);

        $usernameUpdateData = [];
        $usernameSyncCount = 0;

        foreach ($usersWithoutUsername as $user) {
            if (!empty($user['guru_nama'])) {
                $baseUsername = strtolower(str_replace(' ', '_', trim($user['guru_nama'])));
                $baseUsername = preg_replace('/[^a-z0-9_]/', '', $baseUsername);

                $username = $baseUsername;
                $counter = 1;

                while (isset($existingUsernamesMap[$username])) {
                    $username = $baseUsername . '_' . $counter;
                    $counter++;
                    if ($counter > 100) break;
                }

                $existingUsernamesMap[$username] = true;

                $usernameUpdateData[] = [
                    'id' => $user['id'],
                    'username' => $username
                ];
                $usernameSyncCount++;
            }
        }

        if (!empty($usernameUpdateData)) {
            $this->userModel->updateBatch($usernameUpdateData, 'id');
        }

        // 2. Sync NIPs
        $users = $this->userModel
            ->select('users.id as user_id, users.nip as user_nip, guru.nip as guru_nip')
            ->join('guru', 'users.id = guru.user_id', 'left')
            ->where('guru.user_id IS NOT NULL')
            ->findAll();

        $nipUpdateData = [];
        $nipSyncCount = 0;

        foreach ($users as $user) {
            if ((empty($user['user_nip']) && !empty($user['guru_nip'])) ||
                (!empty($user['user_nip']) && !empty($user['guru_nip']) && $user['user_nip'] != $user['guru_nip'])
            ) {

                $nipUpdateData[] = [
                    'id' => $user['user_id'],
                    'nip' => $user['guru_nip']
                ];
                $nipSyncCount++;
            }
        }

        if (!empty($nipUpdateData)) {
            $this->userModel->updateBatch($nipUpdateData, 'id');
        }

        return redirect()->back()->with('success', "$usernameSyncCount username dan $nipSyncCount NIP pengguna berhasil disinkronisasi dengan data guru.");
    }
    public function importGuru()
    {
        return view('admin/pengguna/import_guru');
    }
    public function downloadTemplate()
    {
        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'USERNAME');
        $sheet->setCellValue('B1', 'PASSWORD');
        $sheet->setCellValue('C1', 'EMAIL');
        $sheet->setCellValue('D1', 'ROLE');
        $sheet->setCellValue('E1', 'NAMA_LENGKAP');
        $sheet->setCellValue('F1', 'NIP');
        $sheet->setCellValue('G1', 'PANGKAT_GOLONGAN');
        $sheet->setCellValue('H1', 'MATA_PELAJARAN');
        $sheet->setCellValue('I1', 'STATUS_KEPEGAWAIAN');

        // Add data validation for ROLE column
        $roleValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $roleValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $roleValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $roleValidation->setAllowBlank(false);
        $roleValidation->setShowInputMessage(true);
        $roleValidation->setShowErrorMessage(true);
        $roleValidation->setShowDropDown(true);
        $roleValidation->setErrorTitle('Input error');
        $roleValidation->setError('Value is not in list.');
        $roleValidation->setPromptTitle('Pick from list');
        $roleValidation->setPrompt('Please pick a value from the drop-down list.');
        $roleValidation->setFormula1('"guru,supervisor,kepala,admin"');

        // Add data validation for STATUS_KEPEGAWAIAN column
        $statusValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $statusValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $statusValidation->setAllowBlank(false);
        $statusValidation->setShowInputMessage(true);
        $statusValidation->setShowErrorMessage(true);
        $statusValidation->setShowDropDown(true);
        $statusValidation->setErrorTitle('Input error');
        $statusValidation->setError('Value is not in list.');
        $statusValidation->setPromptTitle('Pick from list');
        $statusValidation->setPrompt('Please pick a value from the drop-down list.');
        $statusValidation->setFormula1('"PNS,PPPK,Honorer"');

        // Apply data validation
        $sheet->setDataValidation('D2:D1000', $roleValidation);
        $sheet->setDataValidation('I2:I1000', $statusValidation);

        // Add sample data row
        $sheet->setCellValue('A2', 'joko_susilo');
        $sheet->setCellValue('B2', 'rahasia123');
        $sheet->setCellValue('C2', 'joko@sekolah.sch.id');
        $sheet->setCellValue('D2', 'guru');
        $sheet->setCellValue('E2', 'Joko Susilo');
        $sheet->setCellValue('F2', '198501012010011001');
        $sheet->setCellValue('G2', 'Penata Muda Tk. I/III b');
        $sheet->setCellValue('H2', 'Matematika');
        $sheet->setCellValue('I2', 'PNS');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);

        // Set header style
        $headerStyle = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'CCCCCC',
                ]
            ]
        ];

        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Redirect output to a client's web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_guru.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    public function processImportGuru()
    {
        // Validate the uploaded file
        $validationRule = [
            'excel_file' => [
                'label' => 'Excel File',
                'rules' => [
                    'uploaded[excel_file]',
                    'ext_in[excel_file,xls,xlsx]',
                    'max_size[excel_file,10240]' // 10MB limit
                ]
            ]
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('excel_file');

        // Process the Excel file
        if ($file->isValid() && !$file->hasMoved()) {
            // Get the temporary file path
            $filePath = $file->getTempName();

            // Load the Excel file
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove the header row
            array_shift($rows);

            $results = [];
            $successCount = 0;
            $errorCount = 0;

            // Start transaction
            $db = \Config\Database::connect();
            $db->transStart();

            try {
                foreach ($rows as $row) {
                    // Check if row is empty
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Extract data from row
                    $username = $row[0] ?? '';
                    $password = $row[1] ?? '';
                    $email = $row[2] ?? '';
                    $role = $row[3] ?? '';
                    $namaLengkap = $row[4] ?? '';
                    $nip = $row[5] ?? '';
                    $pangkatGolongan = $row[6] ?? '';
                    $mataPelajaran = $row[7] ?? '';
                    $statusKepegawaian = $row[8] ?? '';

                    // Validate data
                    $errors = [];

                    // Check if username already exists
                    if (empty($username)) {
                        $errors[] = "Username tidak boleh kosong";
                    } else if ($this->userModel->where('username', $username)->first()) {
                        $errors[] = "Username sudah digunakan";
                    }

                    // Check password length
                    if (strlen($password) < 6) {
                        $errors[] = "Password minimal 6 karakter";
                    }

                    // Check email format
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Format email tidak valid";
                    }

                    // Check NIP for PNS/PPPK
                    if (($statusKepegawaian == 'PNS' || $statusKepegawaian == 'PPPK') && strlen($nip) != 18) {
                        $errors[] = "NIP harus 18 digit untuk PNS/PPPK";
                    }

                    // If there are validation errors, skip this row
                    if (!empty($errors)) {
                        $results[] = [
                            'username' => $username,
                            'status' => '❌ Error',
                            'user_id' => '-',
                            'guru_id' => '-',
                            'keterangan' => implode(', ', $errors)
                        ];
                        $errorCount++;
                        continue;
                    }

                    // Insert into users table
                    $userData = [
                        'username' => $username,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'email' => $email,
                        'role' => $role,
                        'status' => 'Aktif',
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $userId = $this->userModel->insert($userData);

                    if (!$userId) {
                        $results[] = [
                            'username' => $username,
                            'status' => '❌ Error',
                            'user_id' => '-',
                            'guru_id' => '-',
                            'keterangan' => 'Gagal membuat user'
                        ];
                        $errorCount++;
                        continue;
                    }

                    // Insert into guru table
                    $guruData = [
                        'user_id' => $userId,
                        'nama' => $namaLengkap,
                        'nip' => $nip,
                        'pangkat_golongan' => $pangkatGolongan,
                        'mata_pelajaran' => $mataPelajaran,
                        'status_kepegawaian' => $statusKepegawaian,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $guruId = $this->guruModel->insert($guruData);

                    if (!$guruId) {
                        // Rollback by deleting the user
                        $this->userModel->delete($userId);

                        $results[] = [
                            'username' => $username,
                            'status' => '❌ Error',
                            'user_id' => $userId,
                            'guru_id' => '-',
                            'keterangan' => 'Gagal membuat data guru'
                        ];
                        $errorCount++;
                        continue;
                    }

                    // If role is supervisor, also create supervisor record
                    if ($role == 'supervisor') {
                        $supervisorData = [
                            'user_id' => $userId,
                            'tanggal_penugasan' => date('Y-m-d'),
                            'status' => 'Aktif'
                        ];
                        $this->supervisorModel->insert($supervisorData);
                    }

                    $results[] = [
                        'username' => $username,
                        'status' => '✅ Success',
                        'user_id' => $userId,
                        'guru_id' => $guruId,
                        'keterangan' => 'Berhasil dibuat'
                    ];
                    $successCount++;
                }

                // Complete transaction
                $db->transComplete();

                if ($db->transStatus() === FALSE) {
                    return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data. Silakan coba lagi.');
                }

                return view('admin/pengguna/import_result', [
                    'results' => $results,
                    'successCount' => $successCount,
                    'errorCount' => $errorCount
                ]);
            } catch (\Exception $e) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'File tidak valid. Silakan coba lagi.');
        }
    }
    public function cleanupGuruData()
    {
        // Delete guru records for users who are not actually gurus
        $this->guruModel->where('user_id IS NULL')->delete();

        $users = $this->userModel->findAll();
        foreach ($users as $user) {
            if ($user['role'] != 'guru') {
                $this->guruModel->where('user_id', $user['id'])->delete();
            }
        }

        return "Guru data cleanup completed";
    }
}
