<?php
class Admin extends Controller {
    private \;

    public function __construct() {
        Session::requireRole('administrator');
        \->beneficiaryModel = \->model('Beneficiary');
    }

    public function dashboard() {
        \ = [
            'title' => 'Administrator Dashboard',
            'description' => 'Welcome to the Administrator Dashboard'
        ];
        
        \->view('admin/dashboard', \);
    }

    public function users() {
        \ = [
            'title' => 'User Management',
            'description' => 'Manage system users and their roles'
        ];

        \->view('admin/users', \);
    }

    public function beneficiaries() {
        // Get filter parameters
        \ = [];
        if (isset(\['lga']) && !empty(\['lga'])) {
            \['lga'] = \['lga'];
        }
        if (isset(\['ward']) && !empty(\['ward'])) {
            \['ward'] = \['ward'];
        }
        if (isset(\['community']) && !empty(\['community'])) {
            \['community'] = \['community'];
        }
        if (isset(\['tranche_status']) && !empty(\['tranche_status'])) {
            \['tranche_status'] = \['tranche_status'];
        }

        // Pagination
        \ = isset(\['page']) && is_numeric(\['page']) ? (int)\['page'] : 1;
        \ = 25;

        // Get beneficiaries from database with pagination
        \ = \->beneficiaryModel->getPaginated(\, \, \);
        \ = \->beneficiaryModel->getTotalCount(\);
        \ = ceil(\ / \);

        // Get filter options
        \ = \->beneficiaryModel->getLGAs();
        \ = \->beneficiaryModel->getWards();
        \ = \->beneficiaryModel->getCommunities();

        \ = [
            'title' => 'Beneficiaries',
            'description' => 'Manage beneficiaries and their information',
            'beneficiaries' => \,
            'currentPage' => \,
            'totalPages' => \,
            'totalRecords' => \,
            'lgas' => \,
            'wards' => \,
            'communities' => \,
            'filters' => \
        ];

        \->view('admin/beneficiaries', \);
    }

    public function viewBeneficiary(\ = null) {
        if (!\) {
            header('location: ' . URLROOT . '/admin/beneficiaries');
            exit;
        }

        \ = \->beneficiaryModel->getByNidhh(\);

        if (!\) {
            header('location: ' . URLROOT . '/admin/beneficiaries');
            exit;
        }

        \ = [
            'title' => 'View Beneficiary',
            'beneficiary' => \
        ];

        \->view('admin/view_beneficiary', \);
    }

    public function settings() {
        \ = [
            'title' => 'System Settings',
            'description' => 'Configure system-wide settings'
        ];

        \->view('admin/settings', \);
    }

    public function reports() {
        \ = [
            'title' => 'Reports',
            'description' => 'View system reports and analytics'
        ];

        \->view('admin/reports', \);
    }
}
