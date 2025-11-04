<?php require_once __DIR__ . '/../inc/admin_header.php'; ?>

<main class="admin-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mt-4">
                <h1><?php echo $data['title']; ?></h1>
                <p class="lead"><?php echo $data['description']; ?></p>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php 
        $success = Session::flash('success');
        $error = Session::flash('error');
        if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filter Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-funnel"></i> Filters
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?php echo URLROOT; ?>/admin/beneficiaries" class="row g-3">
                            <div class="col-md-3">
                                <label for="lga" class="form-label">LGA</label>
                                <select name="lga" id="lga" class="form-select">
                                    <option value="">-- All LGAs --</option>
                                    <?php foreach ($data['lgas'] as $lga): ?>
                                        <option value="<?php echo htmlspecialchars($lga->LGA); ?>" 
                                                <?php echo isset($data['filters']['lga']) && $data['filters']['lga'] == $lga->LGA ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($lga->LGA); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="ward" class="form-label">Ward</label>
                                <select name="ward" id="ward" class="form-select">
                                    <option value="">-- All Wards --</option>
                                    <?php foreach ($data['wards'] as $ward): ?>
                                        <option value="<?php echo htmlspecialchars($ward->Ward); ?>" 
                                                <?php echo isset($data['filters']['ward']) && $data['filters']['ward'] == $ward->Ward ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($ward->Ward); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="community" class="form-label">Community</label>
                                <select name="community" id="community" class="form-select">
                                    <option value="">-- All Communities --</option>
                                    <?php foreach ($data['communities'] as $community): ?>
                                        <option value="<?php echo htmlspecialchars($community->Community); ?>" 
                                                <?php echo isset($data['filters']['community']) && $data['filters']['community'] == $community->Community ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($community->Community); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tranche_status" class="form-label">Tranche Status</label>
                                <select name="tranche_status" id="tranche_status" class="form-select">
                                    <option value="">-- All Status --</option>
                                    <option value="First" <?php echo isset($data['filters']['tranche_status']) && $data['filters']['tranche_status'] == 'First' ? 'selected' : ''; ?>>First</option>
                                    <option value="FirstSecond" <?php echo isset($data['filters']['tranche_status']) && $data['filters']['tranche_status'] == 'FirstSecond' ? 'selected' : ''; ?>>FirstSecond</option>
                                    <option value="FirstSecondThird" <?php echo isset($data['filters']['tranche_status']) && $data['filters']['tranche_status'] == 'FirstSecondThird' ? 'selected' : ''; ?>>FirstSecondThird</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Apply Filters
                                </button>
                                <a href="<?php echo URLROOT; ?>/admin/beneficiaries" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Beneficiaries List 
                            <span class="badge bg-secondary"><?php echo $data['totalRecords']; ?> Total</span>
                        </h5>
                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-secondary btn-sm" href="<?php echo URLROOT; ?>/public/templates/ImportTemplate.csv" download>
                                <i class="bi bi-download"></i> Download Template
                            </a>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="bi bi-file-earmark-arrow-up"></i> Import Beneficiaries
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>NIDHH</th>
                                        <th>Community</th>
                                        <th>First Tranche Recipient</th>
                                        <th>Tranche Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($data['beneficiaries'])): ?>
                                        <?php $counter = ($data['currentPage'] - 1) * 25 + 1; ?>
                                        <?php foreach ($data['beneficiaries'] as $beneficiary): ?>
                                            <tr>
                                                <td><?php echo $counter++; ?></td>
                                                <td><?php echo htmlspecialchars($beneficiary->nidhh); ?></td>
                                                <td><?php echo htmlspecialchars($beneficiary->Community); ?></td>
                                                <td><?php echo htmlspecialchars($beneficiary->FirstTrancheRecipient ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php 
                                                        $statusClass = '';
                                                        switch($beneficiary->TrancheStatus) {
                                                            case 'First':
                                                                $statusClass = 'badge bg-info';
                                                                break;
                                                            case 'FirstSecond':
                                                                $statusClass = 'badge bg-warning';
                                                                break;
                                                            case 'FirstSecondThird':
                                                                $statusClass = 'badge bg-success';
                                                                break;
                                                        }
                                                    ?>
                                                    <span class="<?php echo $statusClass; ?>">
                                                        <?php echo htmlspecialchars($beneficiary->TrancheStatus); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo URLROOT; ?>/admin/viewBeneficiary/<?php echo urlencode($beneficiary->nidhh); ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="View Details">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                        </svg>
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                No beneficiaries found. Click "Add Beneficiary" to create one.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($data['totalPages'] > 1): ?>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Showing page <?php echo $data['currentPage']; ?> of <?php echo $data['totalPages']; ?>
                                    (<?php echo $data['totalRecords']; ?> total records)
                                </div>
                                <nav aria-label="Beneficiaries pagination">
                                    <ul class="pagination mb-0">
                                        <!-- Previous Button -->
                                        <li class="page-item <?php echo $data['currentPage'] <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo $data['currentPage'] > 1 ? URLROOT . '/admin/beneficiaries?page=' . ($data['currentPage'] - 1) . (isset($data['filters']['lga']) ? '&lga=' . urlencode($data['filters']['lga']) : '') . (isset($data['filters']['ward']) ? '&ward=' . urlencode($data['filters']['ward']) : '') . (isset($data['filters']['community']) ? '&community=' . urlencode($data['filters']['community']) : '') . (isset($data['filters']['tranche_status']) ? '&tranche_status=' . urlencode($data['filters']['tranche_status']) : '') : '#'; ?>">
                                                Previous
                                            </a>
                                        </li>

                                        <?php
                                        // Calculate page range to display
                                        $startPage = max(1, $data['currentPage'] - 2);
                                        $endPage = min($data['totalPages'], $data['currentPage'] + 2);
                                        
                                        // Show first page if not in range
                                        if ($startPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?php echo URLROOT . '/admin/beneficiaries?page=1' . (isset($data['filters']['lga']) ? '&lga=' . urlencode($data['filters']['lga']) : '') . (isset($data['filters']['ward']) ? '&ward=' . urlencode($data['filters']['ward']) : '') . (isset($data['filters']['community']) ? '&community=' . urlencode($data['filters']['community']) : '') . (isset($data['filters']['tranche_status']) ? '&tranche_status=' . urlencode($data['filters']['tranche_status']) : ''); ?>">
                                                    1
                                                </a>
                                            </li>
                                            <?php if ($startPage > 2): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?php echo $i == $data['currentPage'] ? 'active' : ''; ?>">
                                                <a class="page-link" href="<?php echo URLROOT . '/admin/beneficiaries?page=' . $i . (isset($data['filters']['lga']) ? '&lga=' . urlencode($data['filters']['lga']) : '') . (isset($data['filters']['ward']) ? '&ward=' . urlencode($data['filters']['ward']) : '') . (isset($data['filters']['community']) ? '&community=' . urlencode($data['filters']['community']) : '') . (isset($data['filters']['tranche_status']) ? '&tranche_status=' . urlencode($data['filters']['tranche_status']) : ''); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php
                                        // Show last page if not in range
                                        if ($endPage < $data['totalPages']): ?>
                                            <?php if ($endPage < $data['totalPages'] - 1): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?php echo URLROOT . '/admin/beneficiaries?page=' . $data['totalPages'] . (isset($data['filters']['lga']) ? '&lga=' . urlencode($data['filters']['lga']) : '') . (isset($data['filters']['ward']) ? '&ward=' . urlencode($data['filters']['ward']) : '') . (isset($data['filters']['community']) ? '&community=' . urlencode($data['filters']['community']) : '') . (isset($data['filters']['tranche_status']) ? '&tranche_status=' . urlencode($data['filters']['tranche_status']) : ''); ?>">
                                                    <?php echo $data['totalPages']; ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <!-- Next Button -->
                                        <li class="page-item <?php echo $data['currentPage'] >= $data['totalPages'] ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo $data['currentPage'] < $data['totalPages'] ? URLROOT . '/admin/beneficiaries?page=' . ($data['currentPage'] + 1) . (isset($data['filters']['lga']) ? '&lga=' . urlencode($data['filters']['lga']) : '') . (isset($data['filters']['ward']) ? '&ward=' . urlencode($data['filters']['ward']) : '') . (isset($data['filters']['community']) ? '&community=' . urlencode($data['filters']['community']) : '') . (isset($data['filters']['tranche_status']) ? '&tranche_status=' . urlencode($data['filters']['tranche_status']) : '') : '#'; ?>">
                                                Next
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Import Modal -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="<?php echo URLROOT; ?>/admin/importBeneficiaries" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title" id="importModalLabel">Import Beneficiaries</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2">Upload CSV using the required format. You can download the template above.</p>
                            <div class="mb-3">
                                <label for="csv_file" class="form-label">CSV File</label>
                                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                            </div>
                            <div class="alert alert-info small" role="alert">
                                Large files will be processed in the background. You can continue using the app.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Start Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../inc/admin_footer.php'; ?>