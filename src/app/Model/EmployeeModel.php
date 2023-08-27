<?php

namespace app\Model;

use PDO;

class EmployeeModel {
    private $dbConnection;

    public function __construct() {
        $this->dbConnection = new DbConnection();
    }

    public function getEmployees() {
        $pdo = $this->dbConnection->getPDO();
        
        $query = "SELECT * FROM employees";

        try {
            $stmt = $pdo->query($query);
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $employees;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function updateEmployeeEmail($id, $email) {
        // Implement the SQL query to update the employee's email address
        $query = "UPDATE employees SET email_address = :email WHERE id = :id";

        // Bind parameters and execute the query
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true; // Update successful
        } else {
            return false; // Update failed
        }
    }

    public function generateData($data) {
        foreach ($data as $row) {
            yield $row;
        }
    }

    public function insertData($data) {
        $pdo = $this->dbConnection->getPDO();
    
        try {
            $pdo->beginTransaction();
    
            $dataGenerator = $this->generateData($data);
            $batchSize = 50; // Set an appropriate batch size
    
            $sql = "INSERT INTO employees (company_name, employee_name, email_address, salary) VALUES (:company_name, :employee_name, :email_address, :salary)";
            $stmt = $pdo->prepare($sql);
    
            $batch = [];
            foreach ($dataGenerator as $row) {
                $batch[] = $row;
    
                if (count($batch) >= $batchSize) {
                    $this->executeBatch($stmt, $batch);
                    $batch = [];
                }
            }
    
            // Insert any remaining data in the last batch
            if (!empty($batch)) {
                $this->executeBatch($stmt, $batch);
            }
    
            $pdo->commit();
            
            return [
                "success" => true
            ];

        } catch (PDOException $e) {
            $pdo->rollBack();
            return [
                "success" => false,
                "message" => "Error processing batch: " . $e->getMessage(),
            ];        
        }
    }
    
    private function executeBatch($stmt, $batch) {
        foreach ($batch as $row) {
            $stmt->bindParam(':company_name', $row['company_name']);
            $stmt->bindParam(':employee_name', $row['employee_name']);
            $stmt->bindParam(':email_address', $row['email_address']);
            $stmt->bindParam(':salary', $row['salary']);
            $stmt->execute();
        }
    }

}
