<?php if (!empty($jobs)): ?>
    <?php foreach ($jobs as $job): ?>
        <?php 
            $processed = (int)($job->processed_rows ?? 0);
            $total = (int)($job->total_rows ?? 0);
            $percent = $total > 0 ? min(100, round(($processed / $total) * 100)) : 0;
            $badge = 'bg-secondary';
            switch ($job->status) {
                case 'queued': $badge = 'bg-secondary'; break;
                case 'processing': $badge = 'bg-info'; break;
                case 'completed': $badge = 'bg-success'; break;
                case 'failed': $badge = 'bg-danger'; break;
            }
            $filename = basename($job->file_path);
            $err = trim((string)($job->error_message ?? ''));
            $shortErr = $err !== '' ? mb_strimwidth($err, 0, 40, '…') : '';
        ?>
        <tr data-status="<?php echo htmlspecialchars($job->status); ?>">
            <td>#<?php echo (int)$job->id; ?></td>
            <td class="text-break"><?php echo htmlspecialchars($filename); ?></td>
            <td>
                <span class="badge <?php echo $badge; ?>"><?php echo ucfirst(htmlspecialchars($job->status)); ?></span>
            </td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $percent; ?>%;" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="small text-nowrap"><?php echo $processed; ?> / <?php echo $total; ?></span>
                </div>
            </td>
            <td><?php echo htmlspecialchars($job->created_by_name ?? ($job->created_by ?? '')); ?></td>
            <td><?php echo $job->created_at ? date('M d, Y H:i', strtotime($job->created_at)) : '-'; ?></td>
            <td><?php echo $job->started_at ? date('M d, Y H:i', strtotime($job->started_at)) : '-'; ?></td>
            <td><?php echo $job->finished_at ? date('M d, Y H:i', strtotime($job->finished_at)) : '-'; ?></td>
            <td class="text-danger">
                <?php if ($err !== ''): ?>
                    <span title="<?php echo htmlspecialchars($err); ?>"><?php echo htmlspecialchars($shortErr); ?></span>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="9" class="text-center text-muted">No import jobs found.</td>
    </tr>
<?php endif; ?>
