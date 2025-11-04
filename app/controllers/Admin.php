<?php
class Admin extends Controller {
    private $beneficiaryModel;
    private $userModel;
    private $importJobModel;

    public function __construct() {
        Session::requireRole('administrator');
        $this->beneficiaryModel = $this->model('Beneficiary');
        $this->userModel = $this->model('User');
        $this->importJobModel = $this->model('ImportJob');
    }

    public function dashboard() {
        // Beneficiary stats and recent items
        $stats = $this->beneficiaryModel->getStatistics();
        $recent = $this->beneficiaryModel->getPaginated([], 1, 5);
        
        // Gender distributions for each tranche
        $firstGender = $this->beneficiaryModel->getFirstTrancheGenderDistribution();
        $secondGender = $this->beneficiaryModel->getSecondTrancheGenderDistribution();
        $thirdGender = $this->beneficiaryModel->getThirdTrancheGenderDistribution();

        $data = [
            'title' => 'Administrator Dashboard',
            'description' => 'Overview of beneficiaries and system administration',
            'stats' => $stats,
            'recent' => $recent,
            'firstGender' => $firstGender,
            'secondGender' => $secondGender,
            'thirdGender' => $thirdGender
        ];

        $this->view('admin/dashboard', $data);
    }

    public function users() {
        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'create':
                        $this->createUser();
                        return;
                    case 'update':
                        $this->updateUser();
                        return;
                    case 'delete':
                        $this->deleteUser();
                        return;
                }
            }
        }

        // Get filter
        $roleFilter = isset($_GET['role']) && !empty($_GET['role']) ? $_GET['role'] : null;
        
        // Pagination
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 25;

        // Get users from database
        $users = $this->userModel->getPaginated($page, $perPage, $roleFilter);
        $totalRecords = $this->userModel->getTotalCount($roleFilter);
        $totalPages = ceil($totalRecords / $perPage);

        $data = [
            'title' => 'User Management',
            'description' => 'Manage system users and their roles',
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'roleFilter' => $roleFilter,
            'roles' => ['administrator', 'editor', 'reviewer', 'viewer']
        ];

        $this->view('admin/users', $data);
    }

    private function createUser() {
        // Validate input
        $errors = [];
        
        if (empty($_POST['name'])) {
            $errors[] = 'Name is required';
        }
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        } elseif ($this->userModel->emailExists($_POST['email'])) {
            $errors[] = 'Email already exists';
        }
        if (empty($_POST['password']) || strlen($_POST['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        if (empty($_POST['role'])) {
            $errors[] = 'Role is required';
        }

        if (empty($errors)) {
            $userData = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'role' => $_POST['role']
            ];

            if ($this->userModel->create($userData)) {
                Session::flash('success', 'User created successfully');
            } else {
                Session::flash('error', 'Failed to create user');
            }
        } else {
            Session::flash('error', implode('<br>', $errors));
        }

        header('location: ' . URLROOT . '/admin/users');
        exit;
    }

    private function updateUser() {
        // Validate input
        $errors = [];
        
        if (empty($_POST['id'])) {
            $errors[] = 'User ID is required';
        }
        if (empty($_POST['name'])) {
            $errors[] = 'Name is required';
        }
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        } elseif ($this->userModel->emailExists($_POST['email'], $_POST['id'])) {
            $errors[] = 'Email already exists';
        }
        if (!empty($_POST['password']) && strlen($_POST['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        if (empty($_POST['role'])) {
            $errors[] = 'Role is required';
        }

        if (empty($errors)) {
            $userData = [
                'id' => $_POST['id'],
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'role' => $_POST['role']
            ];

            if ($this->userModel->update($userData)) {
                Session::flash('success', 'User updated successfully');
            } else {
                Session::flash('error', 'Failed to update user');
            }
        } else {
            Session::flash('error', implode('<br>', $errors));
        }

        header('location: ' . URLROOT . '/admin/users');
        exit;
    }

    private function deleteUser() {
        if (!empty($_POST['id'])) {
            // Prevent deleting yourself
            if ($_POST['id'] == Session::get('user_id')) {
                Session::flash('error', 'You cannot delete your own account');
            } else {
                if ($this->userModel->delete($_POST['id'])) {
                    Session::flash('success', 'User deleted successfully');
                } else {
                    Session::flash('error', 'Failed to delete user');
                }
            }
        }

        header('location: ' . URLROOT . '/admin/users');
        exit;
    }

    public function beneficiaries() {
        // Get filter parameters
        $filters = [];
        if (isset($_GET['lga']) && !empty($_GET['lga'])) {
            $filters['lga'] = $_GET['lga'];
        }
        if (isset($_GET['ward']) && !empty($_GET['ward'])) {
            $filters['ward'] = $_GET['ward'];
        }
        if (isset($_GET['community']) && !empty($_GET['community'])) {
            $filters['community'] = $_GET['community'];
        }
        if (isset($_GET['tranche_status']) && !empty($_GET['tranche_status'])) {
            $filters['tranche_status'] = $_GET['tranche_status'];
        }

        // Pagination
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 25;

        // Get beneficiaries from database with pagination
        $beneficiaries = $this->beneficiaryModel->getPaginated($filters, $page, $perPage);
        $totalRecords = $this->beneficiaryModel->getTotalCount($filters);
        $totalPages = ceil($totalRecords / $perPage);

        // Get filter options
        $lgas = $this->beneficiaryModel->getLGAs();
        $wards = $this->beneficiaryModel->getWards();
        $communities = $this->beneficiaryModel->getCommunities();

        $data = [
            'title' => 'Beneficiaries',
            'description' => 'Manage beneficiaries and their information',
            'beneficiaries' => $beneficiaries,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'lgas' => $lgas,
            'wards' => $wards,
            'communities' => $communities,
            'filters' => $filters
        ];

        $this->view('admin/beneficiaries', $data);
    }

    // Handle CSV upload and queue background import
    public function importBeneficiaries() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('location: ' . URLROOT . '/admin/beneficiaries');
            exit;
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Please upload a valid CSV file.');
            header('location: ' . URLROOT . '/admin/beneficiaries');
            exit;
        }

        $file = $_FILES['csv_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            Session::flash('error', 'Invalid file type. Please upload a .csv file.');
            header('location: ' . URLROOT . '/admin/beneficiaries');
            exit;
        }

        // Move file to storage/imports
        $uploadsDir = PROJECTROOT . '/storage/imports';
        if (!is_dir($uploadsDir)) {
            @mkdir($uploadsDir, 0777, true);
        }
        $targetPath = $uploadsDir . '/' . date('Ymd_His') . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file['name']);
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            Session::flash('error', 'Failed to save uploaded file.');
            header('location: ' . URLROOT . '/admin/beneficiaries');
            exit;
        }

        // Create import job
        $userId = $_SESSION['user_id'] ?? null;
        $jobId = $this->importJobModel->create($file['name'], $targetPath, $userId);
        if (!$jobId) {
            Session::flash('error', 'Failed to create import job.');
            header('location: ' . URLROOT . '/admin/beneficiaries');
            exit;
        }

        // Spawn background process to process the CSV
        $script = PROJECTROOT . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'import_beneficiaries.php';
        $php = defined('PHP_BINARY') ? PHP_BINARY : 'C:\\xampp\\php\\php.exe';
        // If PHP_BINARY points to a non-existent path (common under Apache), fallback to xampp php.exe
        if (stripos(PHP_OS, 'WIN') !== false && (!is_string($php) || !is_file($php))) {
            $php = 'C:\\xampp\\php\\php.exe';
        }
        if (stripos(PHP_OS, 'WIN') !== false) {
            // Use cmd built-in 'start' to detach; must invoke via cmd /c
            $cmd = 'cmd /c start "" /B "' . $php . '" -f "' . $script . '" ' . (int)$jobId . ' > NUL 2>&1';
            // Try multiple strategies based on available functions
            if (function_exists('popen')) {
                @pclose(@popen($cmd, 'r'));
            } elseif (function_exists('exec')) {
                @exec($cmd);
            } elseif (function_exists('shell_exec')) {
                @shell_exec($cmd);
            } else {
                // Last resort: run synchronously (may block request)
                @system($cmd);
            }
        } else {
            // Fallback for non-Windows
            $cmd = escapeshellcmd($php) . ' -f ' . escapeshellarg($script) . ' ' . (int)$jobId . ' > /dev/null 2>&1 &';
            exec($cmd);
        }

        Session::flash('success', 'Import started in the background. You can continue using the app.');
        header('location: ' . URLROOT . '/admin/beneficiaries');
        exit;
    }

    public function viewBeneficiary($nidhh = null) {
        if (!$nidhh) {
            header('location: ' . URLROOT . '/admin/beneficiaries');
            exit;
        }

        $beneficiary = $this->beneficiaryModel->getByNidhh($nidhh);

        if (!$beneficiary) {
            header('location: ' . URLROOT . '/admin/beneficiaries');
            exit;
        }

        $data = [
            'title' => 'View Beneficiary',
            'beneficiary' => $beneficiary
        ];

        $this->view('admin/view_beneficiary', $data);
    }

    public function settings() {
        $data = [
            'title' => 'System Settings',
            'description' => 'Configure system-wide settings'
        ];

        $this->view('admin/settings', $data);
    }

    public function reports() {
        // Handle CSV export
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            $this->exportReportCSV();
            return;
        }

        // Get filters
        $lga = isset($_GET['lga']) && !empty($_GET['lga']) ? $_GET['lga'] : null;
        $ward = isset($_GET['ward']) && !empty($_GET['ward']) ? $_GET['ward'] : null;
        $reportType = isset($_GET['report_type']) && !empty($_GET['report_type']) ? $_GET['report_type'] : 'overview';

        // Fetch overview stats
        $stats = $this->beneficiaryModel->getStatistics();
        
        // Fetch report data based on type
        $lgaSummary = $this->beneficiaryModel->getLGASummary();
        $wardSummary = $this->beneficiaryModel->getWardSummary($lga);
        $communitySummary = $this->beneficiaryModel->getCommunitySummary($lga, $ward);
        $bankDistribution = $this->beneficiaryModel->getBankDistribution();
        $ageDistribution = $this->beneficiaryModel->getAgeDistribution();
        $idTypeDistribution = $this->beneficiaryModel->getIDTypeDistribution();
        $trancheCompletion = $this->beneficiaryModel->getTrancheCompletionRate();
        $paymentDateAnalysis = $this->beneficiaryModel->getPaymentDateAnalysis();
        
        // Gender distributions
        $firstGender = $this->beneficiaryModel->getFirstTrancheGenderDistribution();
        $secondGender = $this->beneficiaryModel->getSecondTrancheGenderDistribution();
        $thirdGender = $this->beneficiaryModel->getThirdTrancheGenderDistribution();

        // Get filter options
        $lgas = $this->beneficiaryModel->getLGAs();
        $wards = $lga ? $this->beneficiaryModel->getWards($lga) : $this->beneficiaryModel->getWards();

        $data = [
            'title' => 'Reports & Analytics',
            'description' => 'Comprehensive reports and data analysis',
            'stats' => $stats,
            'lgaSummary' => $lgaSummary,
            'wardSummary' => $wardSummary,
            'communitySummary' => $communitySummary,
            'bankDistribution' => $bankDistribution,
            'ageDistribution' => $ageDistribution,
            'idTypeDistribution' => $idTypeDistribution,
            'trancheCompletion' => $trancheCompletion,
            'paymentDateAnalysis' => $paymentDateAnalysis,
            'firstGender' => $firstGender,
            'secondGender' => $secondGender,
            'thirdGender' => $thirdGender,
            'lgas' => $lgas,
            'wards' => $wards,
            'selectedLGA' => $lga,
            'selectedWard' => $ward,
            'reportType' => $reportType
        ];

        $this->view('admin/reports', $data);
    }

    private function exportReportCSV() {
        $reportType = isset($_GET['report_type']) ? $_GET['report_type'] : 'lga';
        $lga = isset($_GET['lga']) && !empty($_GET['lga']) ? $_GET['lga'] : null;
        $ward = isset($_GET['ward']) && !empty($_GET['ward']) ? $_GET['ward'] : null;

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="beneficiaries_report_' . date('Ymd_His') . '.csv"');
        
        $output = fopen('php://output', 'w');

        switch ($reportType) {
            case 'lga':
                fputcsv($output, ['LGA', 'Total Beneficiaries', 'Total Amount', 'First Tranche', 'First & Second', 'All Tranches']);
                $data = $this->beneficiaryModel->getLGASummary();
                foreach ($data as $row) {
                    fputcsv($output, [
                        $row->LGA,
                        $row->total_beneficiaries,
                        $row->total_amount,
                        $row->first_tranche,
                        $row->first_second_tranche,
                        $row->all_tranches
                    ]);
                }
                break;

            case 'ward':
                fputcsv($output, ['Ward', 'LGA', 'Total Beneficiaries', 'Total Amount', 'First Tranche', 'First & Second', 'All Tranches']);
                $data = $this->beneficiaryModel->getWardSummary($lga);
                foreach ($data as $row) {
                    fputcsv($output, [
                        $row->Ward,
                        $row->LGA,
                        $row->total_beneficiaries,
                        $row->total_amount,
                        $row->first_tranche,
                        $row->first_second_tranche,
                        $row->all_tranches
                    ]);
                }
                break;

            case 'community':
                fputcsv($output, ['Community', 'Ward', 'LGA', 'Total Beneficiaries', 'Total Amount', 'Completed']);
                $data = $this->beneficiaryModel->getCommunitySummary($lga, $ward);
                foreach ($data as $row) {
                    fputcsv($output, [
                        $row->Community,
                        $row->Ward,
                        $row->LGA,
                        $row->total_beneficiaries,
                        $row->total_amount,
                        $row->completed
                    ]);
                }
                break;

            default:
                // Full beneficiaries export
                fputcsv($output, ['NIDHH', 'LGA', 'Ward', 'Community', 'Tranche Status', 'Total Amount', 'First Recipient', 'First Gender', 'First Age']);
                $filters = [];
                if ($lga) $filters['lga'] = $lga;
                if ($ward) $filters['ward'] = $ward;
                $data = $this->beneficiaryModel->getAll($filters);
                foreach ($data as $row) {
                    fputcsv($output, [
                        $row->nidhh,
                        $row->LGA,
                        $row->Ward,
                        $row->Community,
                        $row->TrancheStatus,
                        $row->TotalAmount,
                        $row->FirstTrancheRecipient,
                        $row->FirstTrancheGender,
                        $row->FirstTrancheAge
                    ]);
                }
                break;
        }

        fclose($output);
        exit;
    }

    // View import jobs dashboard
    public function importJobs() {
        // Filters
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;
        // Pagination
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 25;

        $jobs = $this->importJobModel->getPaginated($status, $page, $perPage);
        $total = $this->importJobModel->getTotalCount($status);
        $totalPages = (int)ceil($total / $perPage);

        $data = [
            'title' => 'Import Jobs',
            'description' => 'Monitor background CSV imports',
            'jobs' => $jobs,
            'status' => $status,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $total,
            'statuses' => ['queued','processing','completed','failed']
        ];

        $this->view('admin/import_jobs', $data);
    }

    // AJAX endpoint: return only table rows HTML for import jobs
    public function importJobsData() {
        // Only allow GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            header('Allow: GET');
            echo 'Method Not Allowed';
            exit;
        }

        // Filters & pagination (same defaults as full page)
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 25;

        $jobs = $this->importJobModel->getPaginated($status, $page, $perPage);

        // Render partial rows
        header('Content-Type: text/html; charset=UTF-8');
        $jobsVar = $jobs; // alias for include scope
        // Isolate scope to avoid leaking $this, etc.
        (function($jobsVar){
            $jobs = $jobsVar;
            include PROJECTROOT . '/app/views/admin/partials/import_jobs_rows.php';
        })($jobsVar);
        exit;
    }
}