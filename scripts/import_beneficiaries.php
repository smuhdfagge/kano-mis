<?php
// Run from CLI only
if (php_sapi_name() !== 'cli') {
    exit("This script must be run from CLI.\n");
}

$jobId = isset($argv[1]) ? (int)$argv[1] : 0;
if ($jobId <= 0) {
    exit("Invalid job id.\n");
}

// Bootstrap app
require_once __DIR__ . '/../app/init.php';

// Minimal autoload of core classes
$importJob = new ImportJob();
$db = new Database();

$job = $importJob->getById($jobId);
if (!$job) {
    exit("Job not found.\n");
}

$importJob->updateStatus($jobId, 'processing', [ 'started_at' => date('Y-m-d H:i:s') ]);
$file = $job->file_path;

if (!file_exists($file)) {
    $importJob->updateStatus($jobId, 'failed', [ 'error_message' => 'File not found', 'finished_at' => date('Y-m-d H:i:s') ]);
    exit(1);
}

// Try fast path: LOAD DATA LOCAL INFILE
try {
    $sql = <<<'SQL'
LOAD DATA LOCAL INFILE :file IGNORE INTO TABLE beneficiaries
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\r\n' IGNORE 1 LINES
(
    nidhh, State, LGA, Ward, Community, HouseHoldNo, HAddress,
    TrancheStatus, TotalAmount,
    FirstTrancheRecipient, FirstTrancheAccountNumber, FirstTrancheBankName, FirstTranchePaymentDate, FirstTranchePhone, FirstTrancheGender, FirstTrancheAge, FirstTrancheIDType,
    SecondTrancheRecipient, SecondTrancheAccountNumber, SecondTrancheBankName, SecondTranchePaymentDate, SecondTranchePhone, SecondTrancheGender, SecondTrancheAge, SecondTrancheIDType,
    ThirdTrancheRecipient, ThirdTrancheAccountNumber, ThirdTrancheBankName, ThirdTranchePaymentDate, ThirdTranchePhone, ThirdTrancheGender, ThirdTrancheAge, ThirdTrancheIDType
)
SET created_by = :created_by
SQL;
    // Replace :file with quoted literal path (binding is not supported for file path)
    $fileLiteral = "'" . str_replace("'", "\\'", $file) . "'";
    $sqlLiteral = str_replace(':file', $fileLiteral, $sql);
    $db->query($sqlLiteral);
    $db->bind(':created_by', (int)$job->created_by, PDO::PARAM_INT);
    $db->execute();

    $affected = $db->rowCount();
    $importJob->updateStatus($jobId, 'completed', [
        'processed_rows' => $affected,
        'total_rows' => $affected,
        'finished_at' => date('Y-m-d H:i:s')
    ]);
    exit(0);
} catch (Throwable $e) {
    // Record fast-path error but continue with fallback
    $importJob->updateStatus($jobId, 'processing', [ 'error_message' => 'LOAD DATA failed: ' . $e->getMessage() ]);
}

// Fallback: Stream CSV and batch insert
$handle = fopen($file, 'r');
if (!$handle) {
    $importJob->updateStatus($jobId, 'failed', [ 'error_message' => 'Unable to open file', 'finished_at' => date('Y-m-d H:i:s') ]);
    exit(1);
}

$headers = fgetcsv($handle);
if (!$headers) { fclose($handle); $importJob->updateStatus($jobId, 'failed', [ 'error_message' => 'Empty CSV', 'finished_at' => date('Y-m-d H:i:s') ]); exit(1); }
$headers = array_map('trim', $headers);

$expected = [
    'nidhh','State','LGA','Ward','Community','HouseHoldNo','HAddress','TrancheStatus','TotalAmount',
    'FirstTrancheRecipient','FirstTrancheAccountNumber','FirstTrancheBankName','FirstTranchePaymentDate','FirstTranchePhone','FirstTrancheGender','FirstTrancheAge','FirstTrancheIDType',
    'SecondTrancheRecipient','SecondTrancheAccountNumber','SecondTrancheBankName','SecondTranchePaymentDate','SecondTranchePhone','SecondTrancheGender','SecondTrancheAge','SecondTrancheIDType',
    'ThirdTrancheRecipient','ThirdTrancheAccountNumber','ThirdTrancheBankName','ThirdTranchePaymentDate','ThirdTranchePhone','ThirdTrancheGender','ThirdTrancheAge','ThirdTrancheIDType'
];

$map = [];
foreach ($expected as $col) {
    $idx = array_search($col, $headers);
    if ($idx === false) {
        fclose($handle);
        $importJob->updateStatus($jobId, 'failed', [ 'error_message' => 'Missing column: ' . $col, 'finished_at' => date('Y-m-d H:i:s') ]);
        exit(1);
    }
    $map[$col] = $idx;
}

$batchSize = 500;
$batch = [];
$total = 0; $attempted = 0; $inserted = 0;

function val($row, $map, $key) {
    $v = $row[$map[$key]] ?? null;
    $v = is_string($v) ? trim($v) : $v;
    return $v === '' ? null : $v;
}

while (($row = fgetcsv($handle)) !== false) {
    $total++;
    $data = [
        'nidhh' => val($row,$map,'nidhh'),
        'State' => val($row,$map,'State'),
        'LGA' => val($row,$map,'LGA'),
        'Ward' => val($row,$map,'Ward'),
        'Community' => val($row,$map,'Community'),
        'HouseHoldNo' => val($row,$map,'HouseHoldNo'),
        'HAddress' => val($row,$map,'HAddress'),
        'TrancheStatus' => val($row,$map,'TrancheStatus'),
        'TotalAmount' => val($row,$map,'TotalAmount'),
        'FirstTrancheRecipient' => val($row,$map,'FirstTrancheRecipient'),
        'FirstTrancheAccountNumber' => val($row,$map,'FirstTrancheAccountNumber'),
        'FirstTrancheBankName' => val($row,$map,'FirstTrancheBankName'),
        'FirstTranchePaymentDate' => val($row,$map,'FirstTranchePaymentDate'),
        'FirstTranchePhone' => val($row,$map,'FirstTranchePhone'),
        'FirstTrancheGender' => val($row,$map,'FirstTrancheGender'),
        'FirstTrancheAge' => val($row,$map,'FirstTrancheAge'),
        'FirstTrancheIDType' => val($row,$map,'FirstTrancheIDType'),
        'SecondTrancheRecipient' => val($row,$map,'SecondTrancheRecipient'),
        'SecondTrancheAccountNumber' => val($row,$map,'SecondTrancheAccountNumber'),
        'SecondTrancheBankName' => val($row,$map,'SecondTrancheBankName'),
        'SecondTranchePaymentDate' => val($row,$map,'SecondTranchePaymentDate'),
        'SecondTranchePhone' => val($row,$map,'SecondTranchePhone'),
        'SecondTrancheGender' => val($row,$map,'SecondTrancheGender'),
        'SecondTrancheAge' => val($row,$map,'SecondTrancheAge'),
        'SecondTrancheIDType' => val($row,$map,'SecondTrancheIDType'),
        'ThirdTrancheRecipient' => val($row,$map,'ThirdTrancheRecipient'),
        'ThirdTrancheAccountNumber' => val($row,$map,'ThirdTrancheAccountNumber'),
        'ThirdTrancheBankName' => val($row,$map,'ThirdTrancheBankName'),
        'ThirdTranchePaymentDate' => val($row,$map,'ThirdTranchePaymentDate'),
        'ThirdTranchePhone' => val($row,$map,'ThirdTranchePhone'),
        'ThirdTrancheGender' => val($row,$map,'ThirdTrancheGender'),
        'ThirdTrancheAge' => val($row,$map,'ThirdTrancheAge'),
        'ThirdTrancheIDType' => val($row,$map,'ThirdTrancheIDType'),
    ];
    $batch[] = $data;

    if (count($batch) >= $batchSize) {
        // Insert batch
        $placeholders = [];
        $values = [];
        foreach ($batch as $item) {
            $placeholders[] = '(' . implode(',', array_fill(0, 33, '?')) . ',?)'; // 33 cols + created_by
            $values = array_merge($values, array_values($item), [(int)$job->created_by]);
        }
    $sql = 'INSERT IGNORE INTO beneficiaries (
            nidhh, State, LGA, Ward, Community, HouseHoldNo, HAddress,
            TrancheStatus, TotalAmount,
            FirstTrancheRecipient, FirstTrancheAccountNumber, FirstTrancheBankName, FirstTranchePaymentDate, FirstTranchePhone, FirstTrancheGender, FirstTrancheAge, FirstTrancheIDType,
            SecondTrancheRecipient, SecondTrancheAccountNumber, SecondTrancheBankName, SecondTranchePaymentDate, SecondTranchePhone, SecondTrancheGender, SecondTrancheAge, SecondTrancheIDType,
            ThirdTrancheRecipient, ThirdTrancheAccountNumber, ThirdTrancheBankName, ThirdTranchePaymentDate, ThirdTranchePhone, ThirdTrancheGender, ThirdTrancheAge, ThirdTrancheIDType,
            created_by
        ) VALUES ' . implode(',', $placeholders);
        try {
            $db->query($sql);
            // bind sequentially
            $i = 1; // PDO positional binding starts at 1
            foreach ($values as $v) {
                $type = is_null($v) ? PDO::PARAM_NULL : (is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
                $db->bind($i++, $v, $type);
            }
            $db->execute();
            $attempted += count($batch);
            $inserted += (int)$db->rowCount();
            $batch = [];
            $importJob->updateStatus($jobId, 'processing', [ 'processed_rows' => $attempted, 'total_rows' => $total ]);
        } catch (Throwable $e) {
            fclose($handle);
            $importJob->updateStatus($jobId, 'failed', [ 'error_message' => 'Batch insert failed: ' . $e->getMessage(), 'finished_at' => date('Y-m-d H:i:s') ]);
            exit(1);
        }
    }
}

// Flush remaining
if (!empty($batch)) {
    $placeholders = [];
    $values = [];
    foreach ($batch as $item) {
        $placeholders[] = '(' . implode(',', array_fill(0, 33, '?')) . ',?)';
        $values = array_merge($values, array_values($item), [(int)$job->created_by]);
    }
    $sql = 'INSERT IGNORE INTO beneficiaries (
        nidhh, State, LGA, Ward, Community, HouseHoldNo, HAddress,
        TrancheStatus, TotalAmount,
        FirstTrancheRecipient, FirstTrancheAccountNumber, FirstTrancheBankName, FirstTranchePaymentDate, FirstTranchePhone, FirstTrancheGender, FirstTrancheAge, FirstTrancheIDType,
        SecondTrancheRecipient, SecondTrancheAccountNumber, SecondTrancheBankName, SecondTranchePaymentDate, SecondTranchePhone, SecondTrancheGender, SecondTrancheAge, SecondTrancheIDType,
        ThirdTrancheRecipient, ThirdTrancheAccountNumber, ThirdTrancheBankName, ThirdTranchePaymentDate, ThirdTranchePhone, ThirdTrancheGender, ThirdTrancheAge, ThirdTrancheIDType,
        created_by
    ) VALUES ' . implode(',', $placeholders);
    try {
        $db->query($sql);
        $i = 1;
        foreach ($values as $v) {
            $type = is_null($v) ? PDO::PARAM_NULL : (is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
            $db->bind($i++, $v, $type);
        }
        $db->execute();
        $attempted += count($batch);
        $inserted += (int)$db->rowCount();
    } catch (Throwable $e) {
        fclose($handle);
        $importJob->updateStatus($jobId, 'failed', [ 'error_message' => 'Final batch insert failed: ' . $e->getMessage(), 'finished_at' => date('Y-m-d H:i:s') ]);
        exit(1);
    }
}

fclose($handle);
$importJob->updateStatus($jobId, 'completed', [ 'processed_rows' => $attempted, 'total_rows' => $total, 'finished_at' => date('Y-m-d H:i:s') ]);
exit(0);
