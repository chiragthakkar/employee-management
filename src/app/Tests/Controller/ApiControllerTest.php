<?php
namespace app\Tests\Controller;

use app\Controller\ApiController;
use app\Model\DbConnection;
use PHPUnit\Framework\TestCase;

class ApiControllerTest extends TestCase
{
    private $apiController;
    private $DbConnection;
    private $pdo;
    private $insertedIds = []; // Store inserted IDs here

    protected function setUp(): void {
        $this->apiController = new ApiController();

        $this->DbConnection = new DbConnection();
        $this->pdo = $this->DbConnection->getPDO();

        $sampleData = [
            ['company_name' => 'Test@Controller Company name random', 'employee_name' => 'John Doe', 'email_address' => 'john@example.com', 'salary' => 50000],
            ['company_name' => 'Test@Controller Company name random', 'employee_name' => 'John Doe', 'email_address' => 'john@example.com', 'salary' => 60000],
        ];

        foreach ($sampleData as $data) {
            $sql = "INSERT INTO employees (company_name, employee_name, email_address, salary) VALUES (:company_name, :employee_name, :email_address, :salary)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);

            // Retrieve the last inserted ID
            $this->insertedIds[] = $this->pdo->lastInsertId();
        }

    }

    protected function tearDown(): void {
        
        $sql = "DELETE FROM employees WHERE company_name = :company_name";
        $stmt = $this->pdo->prepare($sql);
        
        $companyName = 'Test@Controller Company name random'; 
        
        $stmt->bindParam(':company_name', $companyName);

        $stmt->execute();
        $this->insertedIds = [];

    }

    public function testGetEmployees() {
        $response = $this->apiController->getEmployees();
        $this->assertIsString($response);
    }

    public function testUpdateEmployeeEmail() {
        $_REQUEST = [
            'id' => $this->insertedIds[0],
            'email_address' => 'test@test.com'
        ];

        $response = $this->apiController->updateEmployeeEmail();
        
        $this->assertStringContainsString('Email address updated successfully', $response);
    }
    
    public function testUpdateEmployeeEmailSuccess() {
        
        // Simulate a successful email update
        $_REQUEST = ['id' => $this->insertedIds[0], 'email_address' => 'new@example.com'];
        $response = $this->apiController->updateEmployeeEmail();
        $this->assertStringContainsString('Email address updated successfully', $response);
    }

    public function testUpdateEmployeeEmailInvalidData() {
        // Simulate invalid data for update
        $_REQUEST = ['id' => $this->insertedIds[0]];
        $response = $this->apiController->updateEmployeeEmail();
        $this->assertStringContainsString('Data is not correct', $response);
    }

    public function testUpdateEmployeeEmailInvalidFormat() {
        // Simulate invalid email format
        $_REQUEST = ['id' => $this->insertedIds[0], 'email_address' => 'invalid-email'];
        $response = $this->apiController->updateEmployeeEmail();
        $this->assertStringContainsString('Invalid email format', $response);
    }

    public function testUploadEmployeesInvalidRequest() {
        // Simulate invalid request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $response = $this->apiController->uploadEmployees();
        $this->assertStringContainsString('Request is invalid', $response);
    }

    public function testUploadEmployeesInvalidFile() {
        // Simulate invalid file upload
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_FILES['file']['error'] = UPLOAD_ERR_NO_FILE;
        $response = $this->apiController->uploadEmployees();
        $this->assertStringContainsString('Please upload a valid file', $response);
    }


}
