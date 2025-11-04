<?php
class Beneficiary {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all beneficiaries with optional filters
    public function getAll($filters = []) {
        $sql = 'SELECT * FROM beneficiaries WHERE 1=1';
        
        if (!empty($filters['state'])) {
            $sql .= ' AND State = :state';
        }
        if (!empty($filters['lga'])) {
            $sql .= ' AND LGA = :lga';
        }
        if (!empty($filters['ward'])) {
            $sql .= ' AND Ward = :ward';
        }
        if (!empty($filters['tranche_status'])) {
            $sql .= ' AND TrancheStatus = :tranche_status';
        }
        
        $sql .= ' ORDER BY created_at DESC';
        
        $this->db->query($sql);
        
        if (!empty($filters['state'])) {
            $this->db->bind(':state', $filters['state']);
        }
        if (!empty($filters['lga'])) {
            $this->db->bind(':lga', $filters['lga']);
        }
        if (!empty($filters['ward'])) {
            $this->db->bind(':ward', $filters['ward']);
        }
        if (!empty($filters['tranche_status'])) {
            $this->db->bind(':tranche_status', $filters['tranche_status']);
        }
        
        return $this->db->resultSet();
    }

    // Get beneficiaries with pagination and filters
    public function getPaginated($filters = [], $page = 1, $perPage = 25) {
        $offset = ($page - 1) * $perPage;
        $sql = 'SELECT * FROM beneficiaries WHERE 1=1';
        
        if (!empty($filters['lga'])) {
            $sql .= ' AND LGA = :lga';
        }
        if (!empty($filters['ward'])) {
            $sql .= ' AND Ward = :ward';
        }
        if (!empty($filters['community'])) {
            $sql .= ' AND Community = :community';
        }
        if (!empty($filters['tranche_status'])) {
            $sql .= ' AND TrancheStatus = :tranche_status';
        }
        
        $sql .= ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        
        $this->db->query($sql);
        
        if (!empty($filters['lga'])) {
            $this->db->bind(':lga', $filters['lga']);
        }
        if (!empty($filters['ward'])) {
            $this->db->bind(':ward', $filters['ward']);
        }
        if (!empty($filters['community'])) {
            $this->db->bind(':community', $filters['community']);
        }
        if (!empty($filters['tranche_status'])) {
            $this->db->bind(':tranche_status', $filters['tranche_status']);
        }
        
        $this->db->bind(':limit', $perPage, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }

    // Get total count with filters for pagination
    public function getTotalCount($filters = []) {
        $sql = 'SELECT COUNT(*) as total FROM beneficiaries WHERE 1=1';
        
        if (!empty($filters['lga'])) {
            $sql .= ' AND LGA = :lga';
        }
        if (!empty($filters['ward'])) {
            $sql .= ' AND Ward = :ward';
        }
        if (!empty($filters['community'])) {
            $sql .= ' AND Community = :community';
        }
        if (!empty($filters['tranche_status'])) {
            $sql .= ' AND TrancheStatus = :tranche_status';
        }
        
        $this->db->query($sql);
        
        if (!empty($filters['lga'])) {
            $this->db->bind(':lga', $filters['lga']);
        }
        if (!empty($filters['ward'])) {
            $this->db->bind(':ward', $filters['ward']);
        }
        if (!empty($filters['community'])) {
            $this->db->bind(':community', $filters['community']);
        }
        if (!empty($filters['tranche_status'])) {
            $this->db->bind(':tranche_status', $filters['tranche_status']);
        }
        
        $result = $this->db->single();
        return $result->total;
    }

    // Get unique communities
    public function getCommunities($lga = null, $ward = null) {
        $sql = 'SELECT DISTINCT Community FROM beneficiaries WHERE 1=1';
        if ($lga) {
            $sql .= ' AND LGA = :lga';
        }
        if ($ward) {
            $sql .= ' AND Ward = :ward';
        }
        $sql .= ' ORDER BY Community';
        
        $this->db->query($sql);
        if ($lga) {
            $this->db->bind(':lga', $lga);
        }
        if ($ward) {
            $this->db->bind(':ward', $ward);
        }
        return $this->db->resultSet();
    }

    // Get beneficiary by nidhh
    public function getByNidhh($nidhh) {
        $this->db->query('SELECT * FROM beneficiaries WHERE nidhh = :nidhh');
        $this->db->bind(':nidhh', $nidhh);
        return $this->db->single();
    }

    // Check if nidhh exists
    public function nidhhExists($nidhh) {
        $this->db->query('SELECT nidhh FROM beneficiaries WHERE nidhh = :nidhh');
        $this->db->bind(':nidhh', $nidhh);
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    // Create new beneficiary
    public function create($data) {
        $this->db->query('INSERT INTO beneficiaries (
            nidhh, State, LGA, Ward, Community, HouseHoldNo, HAddress, 
            TrancheStatus, TotalAmount,
            FirstTrancheRecipient, FirstTrancheAccountNumber, FirstTrancheBankName, 
            FirstTranchePaymentDate, FirstTranchePhone, FirstTrancheGender, 
            FirstTrancheAge, FirstTrancheIDType,
            SecondTrancheRecipient, SecondTrancheAccountNumber, SecondTrancheBankName, 
            SecondTranchePaymentDate, SecondTranchePhone, SecondTrancheGender, 
            SecondTrancheAge, SecondTrancheIDType,
            ThirdTrancheRecipient, ThirdTrancheAccountNumber, ThirdTrancheBankName, 
            ThirdTranchePaymentDate, ThirdTranchePhone, ThirdTrancheGender, 
            ThirdTrancheAge, ThirdTrancheIDType,
            created_by
        ) VALUES (
            :nidhh, :state, :lga, :ward, :community, :household_no, :haddress,
            :tranche_status, :total_amount,
            :first_recipient, :first_account, :first_bank, :first_payment_date, 
            :first_phone, :first_gender, :first_age, :first_id_type,
            :second_recipient, :second_account, :second_bank, :second_payment_date, 
            :second_phone, :second_gender, :second_age, :second_id_type,
            :third_recipient, :third_account, :third_bank, :third_payment_date, 
            :third_phone, :third_gender, :third_age, :third_id_type,
            :created_by
        )');

        // Bind values
        $this->db->bind(':nidhh', $data['nidhh']);
        $this->db->bind(':state', $data['state']);
        $this->db->bind(':lga', $data['lga']);
        $this->db->bind(':ward', $data['ward']);
        $this->db->bind(':community', $data['community']);
        $this->db->bind(':household_no', $data['household_no'] ?? null);
        $this->db->bind(':haddress', $data['haddress'] ?? null);
        $this->db->bind(':tranche_status', $data['tranche_status']);
        $this->db->bind(':total_amount', $data['total_amount']);
        
        // First Tranche
        $this->db->bind(':first_recipient', $data['first_recipient'] ?? null);
        $this->db->bind(':first_account', $data['first_account'] ?? null);
        $this->db->bind(':first_bank', $data['first_bank'] ?? null);
        $this->db->bind(':first_payment_date', $data['first_payment_date'] ?? null);
        $this->db->bind(':first_phone', $data['first_phone'] ?? null);
        $this->db->bind(':first_gender', $data['first_gender'] ?? null);
        $this->db->bind(':first_age', $data['first_age'] ?? null);
        $this->db->bind(':first_id_type', $data['first_id_type'] ?? null);
        
        // Second Tranche
        $this->db->bind(':second_recipient', $data['second_recipient'] ?? null);
        $this->db->bind(':second_account', $data['second_account'] ?? null);
        $this->db->bind(':second_bank', $data['second_bank'] ?? null);
        $this->db->bind(':second_payment_date', $data['second_payment_date'] ?? null);
        $this->db->bind(':second_phone', $data['second_phone'] ?? null);
        $this->db->bind(':second_gender', $data['second_gender'] ?? null);
        $this->db->bind(':second_age', $data['second_age'] ?? null);
        $this->db->bind(':second_id_type', $data['second_id_type'] ?? null);
        
        // Third Tranche
        $this->db->bind(':third_recipient', $data['third_recipient'] ?? null);
        $this->db->bind(':third_account', $data['third_account'] ?? null);
        $this->db->bind(':third_bank', $data['third_bank'] ?? null);
        $this->db->bind(':third_payment_date', $data['third_payment_date'] ?? null);
        $this->db->bind(':third_phone', $data['third_phone'] ?? null);
        $this->db->bind(':third_gender', $data['third_gender'] ?? null);
        $this->db->bind(':third_age', $data['third_age'] ?? null);
        $this->db->bind(':third_id_type', $data['third_id_type'] ?? null);
        
        $this->db->bind(':created_by', $_SESSION['user_id'] ?? null);

        return $this->db->execute();
    }

    // Update beneficiary
    public function update($data) {
        $this->db->query('UPDATE beneficiaries SET
            State = :state, LGA = :lga, Ward = :ward, Community = :community,
            HouseHoldNo = :household_no, HAddress = :haddress,
            TrancheStatus = :tranche_status, TotalAmount = :total_amount,
            FirstTrancheRecipient = :first_recipient, FirstTrancheAccountNumber = :first_account,
            FirstTrancheBankName = :first_bank, FirstTranchePaymentDate = :first_payment_date,
            FirstTranchePhone = :first_phone, FirstTrancheGender = :first_gender,
            FirstTrancheAge = :first_age, FirstTrancheIDType = :first_id_type,
            SecondTrancheRecipient = :second_recipient, SecondTrancheAccountNumber = :second_account,
            SecondTrancheBankName = :second_bank, SecondTranchePaymentDate = :second_payment_date,
            SecondTranchePhone = :second_phone, SecondTrancheGender = :second_gender,
            SecondTrancheAge = :second_age, SecondTrancheIDType = :second_id_type,
            ThirdTrancheRecipient = :third_recipient, ThirdTrancheAccountNumber = :third_account,
            ThirdTrancheBankName = :third_bank, ThirdTranchePaymentDate = :third_payment_date,
            ThirdTranchePhone = :third_phone, ThirdTrancheGender = :third_gender,
            ThirdTrancheAge = :third_age, ThirdTrancheIDType = :third_id_type,
            updated_by = :updated_by
            WHERE nidhh = :nidhh
        ');

        // Bind all values (similar to create)
        $this->db->bind(':nidhh', $data['nidhh']);
        $this->db->bind(':state', $data['state']);
        $this->db->bind(':lga', $data['lga']);
        $this->db->bind(':ward', $data['ward']);
        $this->db->bind(':community', $data['community']);
        $this->db->bind(':household_no', $data['household_no'] ?? null);
        $this->db->bind(':haddress', $data['haddress'] ?? null);
        $this->db->bind(':tranche_status', $data['tranche_status']);
        $this->db->bind(':total_amount', $data['total_amount']);
        
        // First Tranche
        $this->db->bind(':first_recipient', $data['first_recipient'] ?? null);
        $this->db->bind(':first_account', $data['first_account'] ?? null);
        $this->db->bind(':first_bank', $data['first_bank'] ?? null);
        $this->db->bind(':first_payment_date', $data['first_payment_date'] ?? null);
        $this->db->bind(':first_phone', $data['first_phone'] ?? null);
        $this->db->bind(':first_gender', $data['first_gender'] ?? null);
        $this->db->bind(':first_age', $data['first_age'] ?? null);
        $this->db->bind(':first_id_type', $data['first_id_type'] ?? null);
        
        // Second Tranche
        $this->db->bind(':second_recipient', $data['second_recipient'] ?? null);
        $this->db->bind(':second_account', $data['second_account'] ?? null);
        $this->db->bind(':second_bank', $data['second_bank'] ?? null);
        $this->db->bind(':second_payment_date', $data['second_payment_date'] ?? null);
        $this->db->bind(':second_phone', $data['second_phone'] ?? null);
        $this->db->bind(':second_gender', $data['second_gender'] ?? null);
        $this->db->bind(':second_age', $data['second_age'] ?? null);
        $this->db->bind(':second_id_type', $data['second_id_type'] ?? null);
        
        // Third Tranche
        $this->db->bind(':third_recipient', $data['third_recipient'] ?? null);
        $this->db->bind(':third_account', $data['third_account'] ?? null);
        $this->db->bind(':third_bank', $data['third_bank'] ?? null);
        $this->db->bind(':third_payment_date', $data['third_payment_date'] ?? null);
        $this->db->bind(':third_phone', $data['third_phone'] ?? null);
        $this->db->bind(':third_gender', $data['third_gender'] ?? null);
        $this->db->bind(':third_age', $data['third_age'] ?? null);
        $this->db->bind(':third_id_type', $data['third_id_type'] ?? null);
        
        $this->db->bind(':updated_by', $_SESSION['user_id'] ?? null);

        return $this->db->execute();
    }

    // Delete beneficiary
    public function delete($nidhh) {
        $this->db->query('DELETE FROM beneficiaries WHERE nidhh = :nidhh');
        $this->db->bind(':nidhh', $nidhh);
        return $this->db->execute();
    }

    // Get statistics
    public function getStatistics() {
        $this->db->query('SELECT 
            COUNT(*) as total,
            SUM(TotalAmount) as total_amount,
            COUNT(CASE WHEN TrancheStatus = "First" THEN 1 END) as first_tranche,
            COUNT(CASE WHEN TrancheStatus = "FirstSecond" THEN 1 END) as first_second_tranche,
            COUNT(CASE WHEN TrancheStatus = "FirstSecondThird" THEN 1 END) as all_tranches
            FROM beneficiaries
        ');
        return $this->db->single();
    }

    // Get unique values for dropdowns
    public function getStates() {
        $this->db->query('SELECT DISTINCT State FROM beneficiaries ORDER BY State');
        return $this->db->resultSet();
    }

    public function getLGAs($state = null) {
        if ($state) {
            $this->db->query('SELECT DISTINCT LGA FROM beneficiaries WHERE State = :state ORDER BY LGA');
            $this->db->bind(':state', $state);
        } else {
            $this->db->query('SELECT DISTINCT LGA FROM beneficiaries ORDER BY LGA');
        }
        return $this->db->resultSet();
    }

    public function getWards($lga = null) {
        if ($lga) {
            $this->db->query('SELECT DISTINCT Ward FROM beneficiaries WHERE LGA = :lga ORDER BY Ward');
            $this->db->bind(':lga', $lga);
        } else {
            $this->db->query('SELECT DISTINCT Ward FROM beneficiaries ORDER BY Ward');
        }
        return $this->db->resultSet();
    }

    // Get gender distribution for First Tranche
    public function getFirstTrancheGenderDistribution() {
        $this->db->query('SELECT 
            FirstTrancheGender as gender,
            COUNT(*) as count
            FROM beneficiaries 
            WHERE FirstTrancheGender IS NOT NULL AND FirstTrancheGender != ""
            GROUP BY FirstTrancheGender
            ORDER BY count DESC
        ');
        return $this->db->resultSet();
    }

    // Get gender distribution for Second Tranche
    public function getSecondTrancheGenderDistribution() {
        $this->db->query('SELECT 
            SecondTrancheGender as gender,
            COUNT(*) as count
            FROM beneficiaries 
            WHERE SecondTrancheGender IS NOT NULL AND SecondTrancheGender != ""
            GROUP BY SecondTrancheGender
            ORDER BY count DESC
        ');
        return $this->db->resultSet();
    }

    // Get gender distribution for Third Tranche
    public function getThirdTrancheGenderDistribution() {
        $this->db->query('SELECT 
            ThirdTrancheGender as gender,
            COUNT(*) as count
            FROM beneficiaries 
            WHERE ThirdTrancheGender IS NOT NULL AND ThirdTrancheGender != ""
            GROUP BY ThirdTrancheGender
            ORDER BY count DESC
        ');
        return $this->db->resultSet();
    }

    // ========== REPORTING METHODS ==========

    // Get summary by LGA
    public function getLGASummary() {
        $this->db->query('SELECT 
            LGA,
            COUNT(*) as total_beneficiaries,
            SUM(TotalAmount) as total_amount,
            COUNT(CASE WHEN TrancheStatus = "First" THEN 1 END) as first_tranche,
            COUNT(CASE WHEN TrancheStatus = "FirstSecond" THEN 1 END) as first_second_tranche,
            COUNT(CASE WHEN TrancheStatus = "FirstSecondThird" THEN 1 END) as all_tranches
            FROM beneficiaries
            GROUP BY LGA
            ORDER BY total_beneficiaries DESC
        ');
        return $this->db->resultSet();
    }

    // Get summary by Ward
    public function getWardSummary($lga = null) {
        $sql = 'SELECT 
            Ward,
            LGA,
            COUNT(*) as total_beneficiaries,
            SUM(TotalAmount) as total_amount,
            COUNT(CASE WHEN TrancheStatus = "First" THEN 1 END) as first_tranche,
            COUNT(CASE WHEN TrancheStatus = "FirstSecond" THEN 1 END) as first_second_tranche,
            COUNT(CASE WHEN TrancheStatus = "FirstSecondThird" THEN 1 END) as all_tranches
            FROM beneficiaries';
        
        if ($lga) {
            $sql .= ' WHERE LGA = :lga';
        }
        
        $sql .= ' GROUP BY Ward, LGA ORDER BY total_beneficiaries DESC';
        
        $this->db->query($sql);
        
        if ($lga) {
            $this->db->bind(':lga', $lga);
        }
        
        return $this->db->resultSet();
    }

    // Get summary by Community
    public function getCommunitySummary($lga = null, $ward = null) {
        $sql = 'SELECT 
            Community,
            Ward,
            LGA,
            COUNT(*) as total_beneficiaries,
            SUM(TotalAmount) as total_amount,
            COUNT(CASE WHEN TrancheStatus = "FirstSecondThird" THEN 1 END) as completed
            FROM beneficiaries
            WHERE 1=1';
        
        if ($lga) {
            $sql .= ' AND LGA = :lga';
        }
        if ($ward) {
            $sql .= ' AND Ward = :ward';
        }
        
        $sql .= ' GROUP BY Community, Ward, LGA ORDER BY total_beneficiaries DESC';
        
        $this->db->query($sql);
        
        if ($lga) {
            $this->db->bind(':lga', $lga);
        }
        if ($ward) {
            $this->db->bind(':ward', $ward);
        }
        
        return $this->db->resultSet();
    }

    // Get payment date analysis
    public function getPaymentDateAnalysis() {
        $this->db->query('SELECT 
            DATE_FORMAT(FirstTranchePaymentDate, "%Y-%m") as payment_month,
            COUNT(*) as beneficiaries_count,
            SUM(TotalAmount) as amount
            FROM beneficiaries
            WHERE FirstTranchePaymentDate IS NOT NULL
            GROUP BY payment_month
            ORDER BY payment_month DESC
            LIMIT 12
        ');
        return $this->db->resultSet();
    }

    // Get bank distribution
    public function getBankDistribution() {
        $this->db->query('SELECT 
            FirstTrancheBankName as bank_name,
            COUNT(*) as count
            FROM beneficiaries
            WHERE FirstTrancheBankName IS NOT NULL AND FirstTrancheBankName != ""
            GROUP BY FirstTrancheBankName
            ORDER BY count DESC
        ');
        return $this->db->resultSet();
    }

    // Get age distribution
    public function getAgeDistribution() {
        $this->db->query('SELECT 
            CASE 
                WHEN FirstTrancheAge < 18 THEN "Under 18"
                WHEN FirstTrancheAge BETWEEN 18 AND 30 THEN "18-30"
                WHEN FirstTrancheAge BETWEEN 31 AND 45 THEN "31-45"
                WHEN FirstTrancheAge BETWEEN 46 AND 60 THEN "46-60"
                WHEN FirstTrancheAge > 60 THEN "Over 60"
                ELSE "Unknown"
            END as age_group,
            COUNT(*) as count
            FROM beneficiaries
            WHERE FirstTrancheAge IS NOT NULL
            GROUP BY age_group
            ORDER BY FIELD(age_group, "Under 18", "18-30", "31-45", "46-60", "Over 60", "Unknown")
        ');
        return $this->db->resultSet();
    }

    // Get ID type distribution
    public function getIDTypeDistribution() {
        $this->db->query('SELECT 
            FirstTrancheIDType as id_type,
            COUNT(*) as count
            FROM beneficiaries
            WHERE FirstTrancheIDType IS NOT NULL AND FirstTrancheIDType != ""
            GROUP BY FirstTrancheIDType
            ORDER BY count DESC
        ');
        return $this->db->resultSet();
    }

    // Get tranche completion rate
    public function getTrancheCompletionRate() {
        $this->db->query('SELECT 
            TrancheStatus,
            COUNT(*) as count,
            ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM beneficiaries)), 2) as percentage
            FROM beneficiaries
            GROUP BY TrancheStatus
            ORDER BY count DESC
        ');
        return $this->db->resultSet();
    }
}
