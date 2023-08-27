<?php
use app\Model\EmployeeModel;
use app\Model\DbConnection;
use PHPUnit\Framework\TestCase;

class EmployeeModelTest extends TestCase {

    private $pdo;
    private $employeeModel;

    protected function setUp(): void {
        $dbConnection = new DbConnection($this->pdo);
        $this->employeeModel = new EmployeeModel($dbConnection);
        $this->pdo = $dbConnection->getPDO();
    }

    protected function tearDown(): void {

        $sql = "DELETE FROM employees WHERE company_name = :company_name";
        $stmt = $this->pdo->prepare($sql);
        
        $companyName = 'Test@Model Company name random';
        
        $stmt->bindParam(':company_name', $companyName);

        $stmt->execute();

    }

    public function testGenerateData() {
        $data = [
            ['company_name' => 'Test@Model Company name random', 'employee_name' => 'John Doe', 'email_address' => 'john@example.com', 'salary' => 50000]
        ];

        try {

            $dataGenerator = $this->employeeModel->generateData($data);
            
            foreach ($dataGenerator as $row) {
                // Add assertions here to validate each row of data
                $this->assertArrayHasKey('company_name', $row);
                $this->assertArrayHasKey('employee_name', $row);
                $this->assertArrayHasKey('email_address', $row);
                $this->assertArrayHasKey('salary', $row);
            }

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function testGetEmployees() {
        $employees = $this->employeeModel->getEmployees();
        $this->assertIsArray($employees);
    }

    public function testUpdateEmployeeEmail() {
        $id = 1;
        $newEmail = 'new@example.com';
        $result = $this->employeeModel->updateEmployeeEmail($id, $newEmail);
        $this->assertTrue($result);
    }

    public function testInsertData() {
        $data = [
            ['company_name' => 'Test@Model Company name random', 'employee_name' => 'Jane Doe', 'email_address' => 'jane@example.com', 'salary' => 60000],
        ];

        $result = $this->employeeModel->insertData($data);
        $this->assertTrue($result['success']);
    }

}

?>