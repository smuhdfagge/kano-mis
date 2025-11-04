<?php
class ImportJob {
    private $db;

    public function __construct() {
        $this->db = new Database;
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS import_jobs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            original_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            status ENUM('queued','processing','completed','failed') NOT NULL DEFAULT 'queued',
            total_rows INT NULL,
            processed_rows INT NULL,
            error_message TEXT NULL,
            created_by INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            started_at DATETIME NULL,
            finished_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->db->query($sql);
        $this->db->execute();
    }

    public function create($originalName, $filePath, $createdBy) {
        $this->db->query('INSERT INTO import_jobs (original_name, file_path, created_by, status) VALUES (:original, :path, :created_by, "queued")');
        $this->db->bind(':original', $originalName);
        $this->db->bind(':path', $filePath);
        $this->db->bind(':created_by', $createdBy);
        if ($this->db->execute()) {
            // Return last insert ID
            $this->db->query('SELECT LAST_INSERT_ID() AS id');
            $row = $this->db->single();
            return $row ? (int)$row->id : null;
        }
        return null;
    }

    public function updateStatus($id, $status, $fields = []) {
        $sets = ['status = :status'];
        if (isset($fields['total_rows'])) $sets[] = 'total_rows = :total_rows';
        if (isset($fields['processed_rows'])) $sets[] = 'processed_rows = :processed_rows';
        if (isset($fields['error_message'])) $sets[] = 'error_message = :error_message';
        if (!empty($fields['started_at'])) $sets[] = 'started_at = :started_at';
        if (!empty($fields['finished_at'])) $sets[] = 'finished_at = :finished_at';

        $sql = 'UPDATE import_jobs SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        if (strpos($sql, ':total_rows') !== false) $this->db->bind(':total_rows', (int)$fields['total_rows']);
        if (strpos($sql, ':processed_rows') !== false) $this->db->bind(':processed_rows', (int)$fields['processed_rows']);
        if (strpos($sql, ':error_message') !== false) $this->db->bind(':error_message', $fields['error_message']);
        if (strpos($sql, ':started_at') !== false) $this->db->bind(':started_at', $fields['started_at']);
        if (strpos($sql, ':finished_at') !== false) $this->db->bind(':finished_at', $fields['finished_at']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getById($id) {
        $this->db->query('SELECT * FROM import_jobs WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getPaginated($status = null, $page = 1, $perPage = 25) {
        $offset = ($page - 1) * $perPage;
        $sql = 'SELECT * FROM import_jobs WHERE 1=1';
        if (!empty($status)) {
            $sql .= ' AND status = :status';
        }
        $sql .= ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $this->db->query($sql);
        if (!empty($status)) {
            $this->db->bind(':status', $status);
        }
        $this->db->bind(':limit', (int)$perPage, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getTotalCount($status = null) {
        $sql = 'SELECT COUNT(*) as total FROM import_jobs WHERE 1=1';
        if (!empty($status)) {
            $sql .= ' AND status = :status';
        }
        $this->db->query($sql);
        if (!empty($status)) {
            $this->db->bind(':status', $status);
        }
        $row = $this->db->single();
        return $row ? (int)$row->total : 0;
    }
}
