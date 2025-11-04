<?php require_once __DIR__ . '/../inc/admin_header.php'; ?>

<main class="admin-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mt-4">
                <h1><?php echo $data['title']; ?></h1>
                <p class="lead"><?php echo $data['description']; ?></p>
            </div>
        </div>

        <!-- Beneficiary KPI Cards -->
        <?php 
            $stats = $data['stats'] ?? null;
            $total = $stats ? (int)($stats->total ?? 0) : 0;
            $totalAmount = $stats ? (float)($stats->total_amount ?? 0) : 0;
            $first = $stats ? (int)($stats->first_tranche ?? 0) : 0;
            $firstSecond = $stats ? (int)($stats->first_second_tranche ?? 0) : 0;
            $all = $stats ? (int)($stats->all_tranches ?? 0) : 0;
            $pct = function($n, $d) { return $d > 0 ? round(($n / $d) * 100) : 0; };
        ?>
        <div class="row g-3 mt-2">
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Beneficiaries</h6>
                                <div class="h3 mb-0 fw-bold"><?php echo number_format($total); ?></div>
                            </div>
                            <div class="text-primary">
                                <i class="bi bi-people-fill" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="<?php echo URLROOT; ?>/admin/beneficiaries" class="small text-decoration-none">View beneficiaries</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Amount</h6>
                                <div class="h3 mb-0 fw-bold">₦<?php echo number_format($totalAmount, 2); ?></div>
                            </div>
                            <div class="text-success">
                                <i class="bi bi-cash-coin" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <span class="small text-muted">Sum of TotalAmount</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">First Tranche</h6>
                                <div class="h3 mb-0 fw-bold"><?php echo number_format($first); ?></div>
                            </div>
                            <div class="text-info">
                                <i class="bi bi-1-circle-fill" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $pct($first, $total); ?>%"></div>
                        </div>
                        <div class="small text-muted mt-1"><?php echo $pct($first, $total); ?>% of total</div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="<?php echo URLROOT; ?>/admin/beneficiaries?tranche_status=First" class="small text-decoration-none">View details</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">All Tranches</h6>
                                <div class="h3 mb-0 fw-bold"><?php echo number_format($all); ?></div>
                            </div>
                            <div class="text-success">
                                <i class="bi bi-3-circle-fill" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $pct($all, $total); ?>%"></div>
                        </div>
                        <div class="small text-muted mt-1"><?php echo $pct($all, $total); ?>% of total</div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="<?php echo URLROOT; ?>/admin/beneficiaries?tranche_status=FirstSecondThird" class="small text-decoration-none">View details</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Beneficiaries -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Beneficiaries</h5>
                        <a class="btn btn-sm btn-outline-primary" href="<?php echo URLROOT; ?>/admin/beneficiaries">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>NIDHH</th>
                                        <th>Community</th>
                                        <th>LGA</th>
                                        <th>Ward</th>
                                        <th>Tranche Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($data['recent'])): ?>
                                        <?php foreach ($data['recent'] as $b): ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($b->nidhh); ?></td>
                                                <td><?php echo htmlspecialchars($b->Community); ?></td>
                                                <td><?php echo htmlspecialchars($b->LGA); ?></td>
                                                <td><?php echo htmlspecialchars($b->Ward); ?></td>
                                                <td>
                                                    <?php 
                                                        $statusClass = 'bg-secondary';
                                                        switch($b->TrancheStatus) {
                                                            case 'First': $statusClass = 'bg-info'; break;
                                                            case 'FirstSecond': $statusClass = 'bg-warning text-dark'; break;
                                                            case 'FirstSecondThird': $statusClass = 'bg-success'; break;
                                                        }
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($b->TrancheStatus); ?></span>
                                                </td>
                                                <td class="text-end">
                                                    <a class="btn btn-sm btn-outline-primary" href="<?php echo URLROOT; ?>/admin/viewBeneficiary/<?php echo urlencode($b->nidhh); ?>">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No recent beneficiaries found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tranche Distribution Chart -->
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tranche Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">
                                    <i class="bi bi-1-circle-fill text-info"></i> First Tranche Only
                                </span>
                                <span class="badge bg-info"><?php echo number_format($first); ?> (<?php echo $pct($first, $total); ?>%)</span>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: <?php echo $pct($first, $total); ?>%"
                                     aria-valuenow="<?php echo $pct($first, $total); ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <?php if ($pct($first, $total) > 10): ?>
                                        <?php echo $pct($first, $total); ?>%
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">
                                    <i class="bi bi-2-circle-fill text-warning"></i> First & Second Tranche
                                </span>
                                <span class="badge bg-warning text-dark"><?php echo number_format($firstSecond); ?> (<?php echo $pct($firstSecond, $total); ?>%)</span>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: <?php echo $pct($firstSecond, $total); ?>%"
                                     aria-valuenow="<?php echo $pct($firstSecond, $total); ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <?php if ($pct($firstSecond, $total) > 10): ?>
                                        <?php echo $pct($firstSecond, $total); ?>%
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">
                                    <i class="bi bi-3-circle-fill text-success"></i> All Three Tranches
                                </span>
                                <span class="badge bg-success"><?php echo number_format($all); ?> (<?php echo $pct($all, $total); ?>%)</span>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?php echo $pct($all, $total); ?>%"
                                     aria-valuenow="<?php echo $pct($all, $total); ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <?php if ($pct($all, $total) > 10): ?>
                                        <?php echo $pct($all, $total); ?>%
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="text-muted small mt-3">
                            <i class="bi bi-info-circle"></i> 
                            Distribution of <?php echo number_format($total); ?> total beneficiaries across tranche statuses
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Total Beneficiaries</span>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($total); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Total Amount</span>
                                <span class="badge bg-success rounded-pill">₦<?php echo number_format($totalAmount, 0); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Completion Rate</span>
                                <span class="badge bg-success rounded-pill"><?php echo $pct($all, $total); ?>%</span>
                            </li>
                        </ul>
                        <div class="mt-3 d-grid">
                            <a href="<?php echo URLROOT; ?>/admin/beneficiaries" class="btn btn-outline-primary">
                                <i class="bi bi-list-ul"></i> View All Beneficiaries
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gender Distribution Pie Charts -->
        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">First Tranche Gender</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="firstGenderChart" style="max-height: 250px;"></canvas>
                        <div class="mt-3 text-center">
                            <?php if (!empty($data['firstGender'])): ?>
                                <?php foreach ($data['firstGender'] as $g): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small"><?php echo htmlspecialchars($g->gender ?: 'Unknown'); ?>:</span>
                                        <span class="badge bg-secondary"><?php echo number_format($g->count); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted small">No data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Second Tranche Gender</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="secondGenderChart" style="max-height: 250px;"></canvas>
                        <div class="mt-3 text-center">
                            <?php if (!empty($data['secondGender'])): ?>
                                <?php foreach ($data['secondGender'] as $g): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small"><?php echo htmlspecialchars($g->gender ?: 'Unknown'); ?>:</span>
                                        <span class="badge bg-secondary"><?php echo number_format($g->count); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted small">No data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Third Tranche Gender</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="thirdGenderChart" style="max-height: 250px;"></canvas>
                        <div class="mt-3 text-center">
                            <?php if (!empty($data['thirdGender'])): ?>
                                <?php foreach ($data['thirdGender'] as $g): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small"><?php echo htmlspecialchars($g->gender ?: 'Unknown'); ?>:</span>
                                        <span class="badge bg-secondary"><?php echo number_format($g->count); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted small">No data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">User Management</h5>
                        <p class="card-text">Manage system users and their roles</p>
                        <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-primary">Manage Users</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">System Settings</h5>
                        <p class="card-text">Configure system-wide settings</p>
                        <a href="<?php echo URLROOT; ?>/admin/settings" class="btn btn-primary">Settings</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Reports</h5>
                        <p class="card-text">View system reports and analytics</p>
                        <a href="<?php echo URLROOT; ?>/admin/reports" class="btn btn-primary">View Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Gender Distribution Pie Charts
(function() {
    // First Tranche Gender Chart
    var firstCtx = document.getElementById('firstGenderChart');
    if (firstCtx) {
        var firstData = <?php echo json_encode($data['firstGender'] ?? []); ?>;
        var firstLabels = firstData.map(function(item) { return item.gender || 'Unknown'; });
        var firstCounts = firstData.map(function(item) { return parseInt(item.count); });
        
        new Chart(firstCtx, {
            type: 'pie',
            data: {
                labels: firstLabels,
                datasets: [{
                    data: firstCounts,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Second Tranche Gender Chart
    var secondCtx = document.getElementById('secondGenderChart');
    if (secondCtx) {
        var secondData = <?php echo json_encode($data['secondGender'] ?? []); ?>;
        var secondLabels = secondData.map(function(item) { return item.gender || 'Unknown'; });
        var secondCounts = secondData.map(function(item) { return parseInt(item.count); });
        
        new Chart(secondCtx, {
            type: 'pie',
            data: {
                labels: secondLabels,
                datasets: [{
                    data: secondCounts,
                    backgroundColor: [
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(201, 203, 207, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 159, 64, 1)',
                        'rgba(201, 203, 207, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Third Tranche Gender Chart
    var thirdCtx = document.getElementById('thirdGenderChart');
    if (thirdCtx) {
        var thirdData = <?php echo json_encode($data['thirdGender'] ?? []); ?>;
        var thirdLabels = thirdData.map(function(item) { return item.gender || 'Unknown'; });
        var thirdCounts = thirdData.map(function(item) { return parseInt(item.count); });
        
        new Chart(thirdCtx, {
            type: 'pie',
            data: {
                labels: thirdLabels,
                datasets: [{
                    data: thirdCounts,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
})();
</script>

<?php require_once __DIR__ . '/../inc/admin_footer.php'; ?>