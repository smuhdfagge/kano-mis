<?php require_once __DIR__ . '/../inc/admin_header.php'; ?>

<main class="admin-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mt-4">
                <h1><?php echo $data['title']; ?></h1>
                <p class="lead"><?php echo $data['description']; ?></p>
            </div>
        </div>

        <!-- Overview Stats Cards -->
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
            <div class="col-md-3">
                <div class="card shadow-sm border-primary">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Beneficiaries</h6>
                        <div class="h2 mb-0 fw-bold text-primary"><?php echo number_format($total); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-success">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Amount</h6>
                        <div class="h2 mb-0 fw-bold text-success">₦<?php echo number_format($totalAmount, 0); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-warning">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">In Progress</h6>
                        <div class="h2 mb-0 fw-bold text-warning"><?php echo number_format($first + $firstSecond); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-success">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Completed</h6>
                        <div class="h2 mb-0 fw-bold text-success"><?php echo number_format($all); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Export -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filters & Export</h5>
                        <div class="btn-group" role="group">
                            <a href="<?php echo URLROOT; ?>/admin/reports?export=csv&report_type=lga" class="btn btn-success btn-sm">
                                <i class="bi bi-download"></i> Export LGA Summary
                            </a>
                            <a href="<?php echo URLROOT; ?>/admin/reports?export=csv&report_type=ward<?php echo $data['selectedLGA'] ? '&lga=' . urlencode($data['selectedLGA']) : ''; ?>" class="btn btn-success btn-sm">
                                <i class="bi bi-download"></i> Export Ward Summary
                            </a>
                            <a href="<?php echo URLROOT; ?>/admin/reports?export=csv&report_type=community<?php echo $data['selectedLGA'] ? '&lga=' . urlencode($data['selectedLGA']) : ''; ?><?php echo $data['selectedWard'] ? '&ward=' . urlencode($data['selectedWard']) : ''; ?>" class="btn btn-success btn-sm">
                                <i class="bi bi-download"></i> Export Community Summary
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?php echo URLROOT; ?>/admin/reports" class="row g-3">
                            <div class="col-md-4">
                                <label for="lga" class="form-label">LGA</label>
                                <select name="lga" id="lga" class="form-select">
                                    <option value="">-- All LGAs --</option>
                                    <?php foreach ($data['lgas'] as $lgaItem): ?>
                                        <option value="<?php echo htmlspecialchars($lgaItem->LGA); ?>" 
                                                <?php echo $data['selectedLGA'] == $lgaItem->LGA ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($lgaItem->LGA); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="ward" class="form-label">Ward</label>
                                <select name="ward" id="ward" class="form-select">
                                    <option value="">-- All Wards --</option>
                                    <?php foreach ($data['wards'] as $wardItem): ?>
                                        <option value="<?php echo htmlspecialchars($wardItem->Ward); ?>" 
                                                <?php echo $data['selectedWard'] == $wardItem->Ward ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($wardItem->Ward); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Apply Filters
                                </button>
                                <a href="<?php echo URLROOT; ?>/admin/reports" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Tabs -->
        <div class="row mt-4">
            <div class="col-12">
                <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="lga-tab" data-bs-toggle="tab" data-bs-target="#lga" type="button" role="tab">
                            <i class="bi bi-geo-alt"></i> LGA Summary
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ward-tab" data-bs-toggle="tab" data-bs-target="#ward" type="button" role="tab">
                            <i class="bi bi-pin-map"></i> Ward Summary
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="community-tab" data-bs-toggle="tab" data-bs-target="#community" type="button" role="tab">
                            <i class="bi bi-house"></i> Community Summary
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">
                            <i class="bi bi-graph-up"></i> Analytics
                        </button>
                    </li>
                </ul>

                <div class="tab-content border border-top-0 p-3" id="reportTabsContent">
                    <!-- LGA Summary Tab -->
                    <div class="tab-pane fade show active" id="lga" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>LGA</th>
                                        <th class="text-end">Total Beneficiaries</th>
                                        <th class="text-end">Total Amount (₦)</th>
                                        <th class="text-end">First Tranche</th>
                                        <th class="text-end">First & Second</th>
                                        <th class="text-end">All Tranches</th>
                                        <th class="text-end">Completion %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($data['lgaSummary'])): ?>
                                        <?php foreach ($data['lgaSummary'] as $row): ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($row->LGA); ?></td>
                                                <td class="text-end"><?php echo number_format($row->total_beneficiaries); ?></td>
                                                <td class="text-end"><?php echo number_format($row->total_amount, 2); ?></td>
                                                <td class="text-end"><span class="badge bg-info"><?php echo number_format($row->first_tranche); ?></span></td>
                                                <td class="text-end"><span class="badge bg-warning text-dark"><?php echo number_format($row->first_second_tranche); ?></span></td>
                                                <td class="text-end"><span class="badge bg-success"><?php echo number_format($row->all_tranches); ?></span></td>
                                                <td class="text-end"><?php echo $pct($row->all_tranches, $row->total_beneficiaries); ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center text-muted">No data available</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Ward Summary Tab -->
                    <div class="tab-pane fade" id="ward" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Ward</th>
                                        <th>LGA</th>
                                        <th class="text-end">Total Beneficiaries</th>
                                        <th class="text-end">Total Amount (₦)</th>
                                        <th class="text-end">First Tranche</th>
                                        <th class="text-end">First & Second</th>
                                        <th class="text-end">All Tranches</th>
                                        <th class="text-end">Completion %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($data['wardSummary'])): ?>
                                        <?php foreach ($data['wardSummary'] as $row): ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($row->Ward); ?></td>
                                                <td><?php echo htmlspecialchars($row->LGA); ?></td>
                                                <td class="text-end"><?php echo number_format($row->total_beneficiaries); ?></td>
                                                <td class="text-end"><?php echo number_format($row->total_amount, 2); ?></td>
                                                <td class="text-end"><span class="badge bg-info"><?php echo number_format($row->first_tranche); ?></span></td>
                                                <td class="text-end"><span class="badge bg-warning text-dark"><?php echo number_format($row->first_second_tranche); ?></span></td>
                                                <td class="text-end"><span class="badge bg-success"><?php echo number_format($row->all_tranches); ?></span></td>
                                                <td class="text-end"><?php echo $pct($row->all_tranches, $row->total_beneficiaries); ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8" class="text-center text-muted">No data available</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Community Summary Tab -->
                    <div class="tab-pane fade" id="community" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Community</th>
                                        <th>Ward</th>
                                        <th>LGA</th>
                                        <th class="text-end">Total Beneficiaries</th>
                                        <th class="text-end">Total Amount (₦)</th>
                                        <th class="text-end">Completed</th>
                                        <th class="text-end">Completion %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($data['communitySummary'])): ?>
                                        <?php foreach ($data['communitySummary'] as $row): ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($row->Community); ?></td>
                                                <td><?php echo htmlspecialchars($row->Ward); ?></td>
                                                <td><?php echo htmlspecialchars($row->LGA); ?></td>
                                                <td class="text-end"><?php echo number_format($row->total_beneficiaries); ?></td>
                                                <td class="text-end"><?php echo number_format($row->total_amount, 2); ?></td>
                                                <td class="text-end"><span class="badge bg-success"><?php echo number_format($row->completed); ?></span></td>
                                                <td class="text-end"><?php echo $pct($row->completed, $row->total_beneficiaries); ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center text-muted">No data available</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Analytics Tab -->
                    <div class="tab-pane fade" id="analytics" role="tabpanel">
                        <div class="row g-4">
                            <!-- Tranche Completion -->
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Tranche Completion Rate</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="trancheChart" style="max-height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Age Distribution -->
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Age Distribution</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="ageChart" style="max-height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Distribution -->
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Top Banks (First Tranche)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Bank Name</th>
                                                        <th class="text-end">Count</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($data['bankDistribution'])): ?>
                                                        <?php $count = 0; foreach ($data['bankDistribution'] as $bank): if (++$count > 10) break; ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($bank->bank_name); ?></td>
                                                                <td class="text-end"><span class="badge bg-primary"><?php echo number_format($bank->count); ?></span></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ID Type Distribution -->
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">ID Type Distribution</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="idTypeChart" style="max-height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    // Tranche Completion Chart
    var trancheCtx = document.getElementById('trancheChart');
    if (trancheCtx) {
        var trancheData = <?php echo json_encode($data['trancheCompletion'] ?? []); ?>;
        new Chart(trancheCtx, {
            type: 'doughnut',
            data: {
                labels: trancheData.map(function(item) { return item.TrancheStatus; }),
                datasets: [{
                    data: trancheData.map(function(item) { return parseInt(item.count); }),
                    backgroundColor: ['rgba(23, 162, 184, 0.8)', 'rgba(255, 193, 7, 0.8)', 'rgba(40, 167, 69, 0.8)']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' (' + 
                                    (trancheData[context.dataIndex].percentage || 0) + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Age Distribution Chart
    var ageCtx = document.getElementById('ageChart');
    if (ageCtx) {
        var ageData = <?php echo json_encode($data['ageDistribution'] ?? []); ?>;
        new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: ageData.map(function(item) { return item.age_group; }),
                datasets: [{
                    label: 'Beneficiaries',
                    data: ageData.map(function(item) { return parseInt(item.count); }),
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // ID Type Chart
    var idTypeCtx = document.getElementById('idTypeChart');
    if (idTypeCtx) {
        var idData = <?php echo json_encode($data['idTypeDistribution'] ?? []); ?>;
        new Chart(idTypeCtx, {
            type: 'pie',
            data: {
                labels: idData.map(function(item) { return item.id_type; }),
                datasets: [{
                    data: idData.map(function(item) { return parseInt(item.count); }),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
})();
</script>

<?php require_once __DIR__ . '/../inc/admin_footer.php'; ?>