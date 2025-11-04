<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function login($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($row) {
            $hashed_password = $row->password;
            if(password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        return false;
    }

    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        return ($this->db->rowCount() > 0);
    }

    public function getUserRole($id) {
        $this->db->query('SELECT role FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        
        $row = $this->db->single();
        return $row->role;
    }

    // Get all users with pagination
    public function getPaginated($page = 1, $perPage = 25, $role = null) {
        $offset = ($page - 1) * $perPage;
        $sql = 'SELECT id, name, email, role, created_at FROM users WHERE 1=1';
        
        if ($role) {
            $sql .= ' AND role = :role';
        }
        
        $sql .= ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        
        $this->db->query($sql);
        
        if ($role) {
            $this->db->bind(':role', $role);
        }
        
        $this->db->bind(':limit', $perPage, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }

    // Get total count
    public function getTotalCount($role = null) {
        $sql = 'SELECT COUNT(*) as total FROM users WHERE 1=1';
        
        if ($role) {
            $sql .= ' AND role = :role';
        }
        
        $this->db->query($sql);
        
        if ($role) {
            $this->db->bind(':role', $role);
        }
        
        $result = $this->db->single();
        return $result->total;
    }

    // Get user by ID
    public function getById($id) {
        $this->db->query('SELECT id, name, email, role, created_at FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Create new user
    public function create($data) {
        $this->db->query('INSERT INTO users (name, email, password, role) 
                          VALUES (:name, :email, :password, :role)');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':role', $data['role']);
        
        return $this->db->execute();
    }

    // Update user
    public function update($data) {
        if (!empty($data['password'])) {
            $this->db->query('UPDATE users 
                              SET name = :name, email = :email, password = :password, role = :role 
                              WHERE id = :id');
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        } else {
            $this->db->query('UPDATE users 
                              SET name = :name, email = :email, role = :role 
                              WHERE id = :id');
        }
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);
        
        return $this->db->execute();
    }

    // Delete user
    public function delete($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Check if email exists (excluding specific user ID for updates)
    public function emailExists($email, $excludeId = null) {
        if ($excludeId) {
            $this->db->query('SELECT id FROM users WHERE email = :email AND id != :id');
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query('SELECT id FROM users WHERE email = :email');
        }
        $this->db->bind(':email', $email);
        $this->db->single();
        return $this->db->rowCount() > 0;
    }
}