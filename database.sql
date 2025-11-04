CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('administrator', 'editor', 'reviewer', 'viewer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default administrator account (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Administrator', 'admin@example.com', '$2y$10$Yl7rHQaXmuGZhf3zKxSyDO6UMCKxfHQZ6oYaEBWl4YYqdOIj1TD5C', 'administrator');

-- Beneficiaries Table
CREATE TABLE beneficiaries (
    -- Primary Key
    nidhh VARCHAR(50) PRIMARY KEY,
    
    -- Location Information
    State VARCHAR(100) NOT NULL,
    LGA VARCHAR(100) NOT NULL,
    Ward VARCHAR(100) NOT NULL,
    Community VARCHAR(100) NOT NULL,
    
    -- Household Information
    HouseHoldNo VARCHAR(50),
    HAddress TEXT,
    
    -- Tranche Information
    TrancheStatus ENUM('First', 'FirstSecond', 'FirstSecondThird') NOT NULL DEFAULT 'First',
    TotalAmount DECIMAL(15, 2) NOT NULL DEFAULT 0.00 CHECK (TotalAmount >= 0),
    
    -- First Tranche Details
    FirstTrancheRecipient VARCHAR(255),
    FirstTrancheAccountNumber VARCHAR(20),
    FirstTrancheBankName VARCHAR(100),
    FirstTranchePaymentDate DATE,
    FirstTranchePhone VARCHAR(20),
    FirstTrancheGender ENUM('Male', 'Female', 'Other'),
    FirstTrancheAge INT,
    FirstTrancheIDType VARCHAR(50),
    
    -- Second Tranche Details
    SecondTrancheRecipient VARCHAR(255),
    SecondTrancheAccountNumber VARCHAR(20),
    SecondTrancheBankName VARCHAR(100),
    SecondTranchePaymentDate DATE,
    SecondTranchePhone VARCHAR(20),
    SecondTrancheGender ENUM('Male', 'Female', 'Other'),
    SecondTrancheAge INT,
    SecondTrancheIDType VARCHAR(50),
    
    -- Third Tranche Details
    ThirdTrancheRecipient VARCHAR(255),
    ThirdTrancheAccountNumber VARCHAR(20),
    ThirdTrancheBankName VARCHAR(100),
    ThirdTranchePaymentDate DATE,
    ThirdTranchePhone VARCHAR(20),
    ThirdTrancheGender ENUM('Male', 'Female', 'Other'),
    ThirdTrancheAge INT,
    ThirdTrancheIDType VARCHAR(50),
    
    -- Audit Fields
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    
    -- Indexes for better query performance
    INDEX idx_state (State),
    INDEX idx_lga (LGA),
    INDEX idx_ward (Ward),
    INDEX idx_tranche_status (TrancheStatus),
    INDEX idx_created_at (created_at),
    
    -- Foreign Keys
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;