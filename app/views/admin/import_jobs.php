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

        <!-- Filters -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-funnel"></i> Filters
                        </h6>
                        <div class="d-flex gap-2">
                            <a href="<?php echo URLROOT; ?>/admin/importJobs" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?php echo URLROOT; ?>/admin/importJobs" class="row g-3">
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">-- All Statuses --</option>
                                    <?php foreach ($data['statuses'] as $s): ?>
                                        <option value="<?php echo $s; ?>" <?php echo ($data['status'] === $s) ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($s); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Apply Filters
                                </button>
                                <a href="<?php echo URLROOT; ?>/admin/importJobs" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jobs Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Import Jobs
                            <span class="badge bg-secondary"><?php echo $data['totalRecords']; ?> Total</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">ID</th>
                                        <th>File</th>
                                        <th>Status</th>
                                        <th style="width: 220px;">Progress</th>
                                        <th>Created By</th>
                                        <th>Created</th>
                                        <th>Started</th>
                                        <th>Finished</th>
                                        <th>Error</th>
                                    </tr>
                                </thead>
                                <tbody id="jobs-tbody">
                                    <?php 
                                        $jobs = $data['jobs'];
                                        include __DIR__ . '/partials/import_jobs_rows.php';
                                    ?>
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
                                <nav aria-label="Jobs pagination">
                                    <ul class="pagination mb-0">
                                        <li class="page-item <?php echo $data['currentPage'] <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo $data['currentPage'] > 1 ? URLROOT . '/admin/importJobs?page=' . ($data['currentPage'] - 1) . ($data['status'] ? '&status=' . urlencode($data['status']) : '') : '#'; ?>">Previous</a>
                                        </li>
                                        <?php 
                                            $startPage = max(1, $data['currentPage'] - 2);
                                            $endPage = min($data['totalPages'], $data['currentPage'] + 2);
                                            if ($startPage > 1): ?>
                                                <li class="page-item"><a class="page-link" href="<?php echo URLROOT . '/admin/importJobs?page=1' . ($data['status'] ? '&status=' . urlencode($data['status']) : ''); ?>">1</a></li>
                                                <?php if ($startPage > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                                        <?php endif; ?>
                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?php echo $i == $data['currentPage'] ? 'active' : ''; ?>">
                                                <a class="page-link" href="<?php echo URLROOT . '/admin/importJobs?page=' . $i . ($data['status'] ? '&status=' . urlencode($data['status']) : ''); ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <?php if ($endPage < $data['totalPages']): ?>
                                            <?php if ($endPage < $data['totalPages'] - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                                            <li class="page-item"><a class="page-link" href="<?php echo URLROOT . '/admin/importJobs?page=' . $data['totalPages'] . ($data['status'] ? '&status=' . urlencode($data['status']) : ''); ?>"><?php echo $data['totalPages']; ?></a></li>
                                        <?php endif; ?>
                                        <li class="page-item <?php echo $data['currentPage'] >= $data['totalPages'] ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo $data['currentPage'] < $data['totalPages'] ? URLROOT . '/admin/importJobs?page=' . ($data['currentPage'] + 1) . ($data['status'] ? '&status=' . urlencode($data['status']) : '') : '#'; ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
        (function(){
            var tbody = document.getElementById('jobs-tbody');
            if (!tbody) return;
            var status = <?php echo json_encode($data['status']); ?>;
            var page = <?php echo (int)$data['currentPage']; ?>;
            var baseUrl = '<?php echo URLROOT; ?>/admin/importJobsData';
            var url = baseUrl + '?page=' + encodeURIComponent(page) + (status ? '&status=' + encodeURIComponent(status) : '');

            function hasActiveJobs(container){
                return !!container.querySelector('tr[data-status="queued"], tr[data-status="processing"]');
            }

            var pollInterval = 5000;
            var timerId = null;

            function poll(){
                fetch(url, { cache: 'no-store', headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                    .then(function(r){ if (!r.ok) throw new Error('Network error'); return r.text(); })
                    .then(function(html){
                        tbody.innerHTML = html;
                        var active = hasActiveJobs(tbody);
                        // Start polling if active jobs appeared and we're not polling yet
                        if (active && !timerId) {
                            timerId = setInterval(poll, pollInterval);
                        }
                        // If no active jobs, stop polling to reduce load
                        if (!active && timerId) {
                            clearInterval(timerId);
                            timerId = null;
                        }
                    })
                    .catch(function(err){
                        // Optional: backoff or log; for now, keep trying
                        console.warn('Polling failed:', err);
                    });
            }

            // Start polling only if there are active jobs initially, else do a one-off check in 10s
            if (hasActiveJobs(tbody)) {
                timerId = setInterval(poll, pollInterval);
            } else {
                // One-off delayed refresh in case a job starts soon
                setTimeout(poll, 10000);
            }
        })();
        </script>
    </div>
</main>

<?php require_once __DIR__ . '/../inc/admin_footer.php'; ?>
