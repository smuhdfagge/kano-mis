<?php require_once __DIR__ . '/../inc/admin_header.php'; ?>

<style>
.info-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    margin-bottom: 0.25rem;
    font-weight: 600;
}
.info-value {
    font-size: 1rem;
    color: #212529;
    margin-bottom: 0;
    font-weight: 500;
}
.section-divider {
    border-top: 2px solid #e9ecef;
    margin: 1.5rem 0;
}
.beneficiary-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}
.stat-card {
    text-align: center;
    padding: 1.5rem;
    border-left: 4px solid;
}
.stat-card.primary { border-color: #0d6efd; }
.stat-card.success { border-color: #198754; }
.stat-card.info { border-color: #0dcaf0; }
.tranche-section {
    border-left: 4px solid;
    padding-left: 1rem;
    margin-bottom: 1.5rem;
}
.tranche-section.first { border-color: #0dcaf0; }
.tranche-section.second { border-color: #ffc107; }
.tranche-section.third { border-color: #198754; }
</style>

<main class="admin-main">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="beneficiary-header shadow-sm">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-1">Beneficiary Details</h2>
                            <p class="mb-0 opacity-75">NIDHH: <?php echo htmlspecialchars($data['beneficiary']->nidhh); ?></p>
                        </div>
                        <a href="<?php echo URLROOT; ?>/admin/beneficiaries" class="btn btn-light">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                            </svg>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm stat-card primary">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Tranche Status</h6>
                        <?php 
                            $statusClass = '';
                            $statusText = '';
                            switch($data['beneficiary']->TrancheStatus) {
                                case 'First':
                                    $statusClass = 'badge bg-info fs-6';
                                    $statusText = '1st Tranche';
                                    break;
                                case 'FirstSecond':
                                    $statusClass = 'badge bg-warning text-dark fs-6';
                                    $statusText = '1st & 2nd Tranche';
                                    break;
                                case 'FirstSecondThird':
                                    $statusClass = 'badge bg-success fs-6';
                                    $statusText = 'All Tranches';
                                    break;
                            }
                        ?>
                        <span class="<?php echo $statusClass; ?> px-3 py-2"><?php echo $statusText; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm stat-card success">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Amount</h6>
                        <h3 class="text-success mb-0"><?php echo number_format($data['beneficiary']->TotalAmount, 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm stat-card info">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Community</h6>
                        <h5 class="mb-0"><?php echo htmlspecialchars($data['beneficiary']->Community); ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" id="beneficiaryTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="location-tab" data-bs-toggle="tab" data-bs-target="#location" type="button">
                                     Location & Household
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tranches-tab" data-bs-toggle="tab" data-bs-target="#tranches" type="button">
                                     Tranche Details
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit" type="button">
                                     Audit Trail
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="beneficiaryTabContent">
                            
                            <!-- Location & Household Tab -->
                            <div class="tab-pane fade show active" id="location" role="tabpanel">
                                <div class="row g-4">
                                    <div class="col-md-6 col-lg-3">
                                        <div class="info-label">State</div>
                                        <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->State); ?></div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="info-label">LGA</div>
                                        <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->LGA); ?></div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="info-label">Ward</div>
                                        <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->Ward); ?></div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="info-label">Community</div>
                                        <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->Community); ?></div>
                                    </div>
                                </div>
                                
                                <div class="section-divider"></div>
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="info-label">Household Number</div>
                                        <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->HouseHoldNo ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Household Address</div>
                                        <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->HAddress ?? 'N/A'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tranche Details Tab -->
                            <div class="tab-pane fade" id="tranches" role="tabpanel">
                                
                                <!-- First Tranche -->
                                <div class="tranche-section first">
                                    <h5 class="text-info mb-3">
                                        <span class="badge bg-info">1st</span> First Tranche
                                    </h5>
                                    <div class="row g-4">
                                        <div class="col-md-6 col-lg-4">
                                            <div class="info-label">Recipient Name</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->FirstTrancheRecipient ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="info-label">Account Number</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->FirstTrancheAccountNumber ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="info-label">Bank Name</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->FirstTrancheBankName ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="info-label">Payment Date</div>
                                            <div class="info-value"><?php echo $data['beneficiary']->FirstTranchePaymentDate ?? 'N/A'; ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="info-label">Phone Number</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->FirstTranchePhone ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-4 col-lg-2">
                                            <div class="info-label">Gender</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->FirstTrancheGender ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-4 col-lg-2">
                                            <div class="info-label">Age</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->FirstTrancheAge ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-4 col-lg-2">
                                            <div class="info-label">ID Type</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->FirstTrancheIDType ?? 'N/A'); ?></div>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($data['beneficiary']->TrancheStatus == 'FirstSecond' || $data['beneficiary']->TrancheStatus == 'FirstSecondThird'): ?>
                                <div class="section-divider"></div>
                                
                                <!-- Second Tranche -->
                                <div class="tranche-section second">
                                    <h5 class="text-warning mb-3">
                                        <span class="badge bg-warning text-dark">2nd</span> Second Tranche
                                    </h5>
                                    <div class="row g-4">
                                        <div class="col-md-6 col-lg-4">
                                            <div class="info-label">Recipient Name</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->SecondTrancheRecipient ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="info-label">Account Number</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->SecondTrancheAccountNumber ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="info-label">Bank Name</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->SecondTrancheBankName ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="info-label">Payment Date</div>
                                            <div class="info-value"><?php echo $data['beneficiary']->SecondTranchePaymentDate ?? 'N/A'; ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="info-label">Phone Number</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->SecondTranchePhone ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-4 col-lg-2">
                                            <div class="info-label">Gender</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->SecondTrancheGender ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-4 col-lg-2">
                                            <div class="info-label">Age</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->SecondTrancheAge ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-4 col-lg-2">
                                            <div class="info-label">ID Type</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->SecondTrancheIDType ?? 'N/A'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($data['beneficiary']->TrancheStatus == 'FirstSecondThird'): ?>
                                <div class="section-divider"></div>
                                
                                <!-- Third Tranche -->
                                <div class="tranche-section third">
                                    <h5 class="text-success mb-3">
                                        <span class="badge bg-success">3rd</span> Third Tranche
                                    </h5>
                                    <div class="row g-4">
                                        <div class="col-md-6 col-lg-4">
                                            <div class="info-label">Recipient Name</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->ThirdTrancheRecipient ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="info-label">Account Number</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->ThirdTrancheAccountNumber ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="info-label">Bank Name</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->ThirdTrancheBankName ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="info-label">Payment Date</div>
                                            <div class="info-value"><?php echo $data['beneficiary']->ThirdTranchePaymentDate ?? 'N/A'; ?></div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="info-label">Phone Number</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->ThirdTranchePhone ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-4 col-lg-2">
                                            <div class="info-label">Gender</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->ThirdTrancheGender ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-4 col-lg-2">
                                            <div class="info-label">Age</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->ThirdTrancheAge ?? 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-4 col-lg-2">
                                            <div class="info-label">ID Type</div>
                                            <div class="info-value"><?php echo htmlspecialchars($data['beneficiary']->ThirdTrancheIDType ?? 'N/A'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Audit Trail Tab -->
                            <div class="tab-pane fade" id="audit" role="tabpanel">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card border-start border-primary border-4">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-2">Created</h6>
                                                <p class="mb-1 fw-bold"><?php echo date('F j, Y', strtotime($data['beneficiary']->created_at)); ?></p>
                                                <small class="text-muted"><?php echo date('g:i A', strtotime($data['beneficiary']->created_at)); ?></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-start border-success border-4">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-2">Last Updated</h6>
                                                <p class="mb-1 fw-bold"><?php echo date('F j, Y', strtotime($data['beneficiary']->updated_at)); ?></p>
                                                <small class="text-muted"><?php echo date('g:i A', strtotime($data['beneficiary']->updated_at)); ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../inc/admin_footer.php'; ?>