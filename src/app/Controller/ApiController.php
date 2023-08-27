<?php

namespace app\Controller;

use app\Model\EmployeeModel;
use Exception;

class ApiController {

    private function jsonResponse($code, $message, $data = []) {
        $response = [
            "code" => $code,
            "message" => $message
        ];

        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        return json_encode($response);
    }

    private function processBatch($batch) {
        $employeeModel = new EmployeeModel();
        $batchResult = $employeeModel->insertData($batch); 
        if(!$batchResult['success']) {
            return false;
        }
        return true;
    } 
    
    public function getEmployees(): string
    {
        $employeeModel = new EmployeeModel();
        $data = $employeeModel->getEmployees();
        return json_encode($data);
    }

    public function updateEmployeeEmail() {
        
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email_address']) || !isset($data['id'])) {
            return $this->jsonResponse(400, "Data is not correct.");
        }

        // Validate email format
        if (!filter_var($data['email_address'], FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse(400, "Invalid email format.");
        }

        $employeeModel = new EmployeeModel();
        $success = $employeeModel->updateEmployeeEmail($data['id'], $data['email_address']);

        if ($success) {
            return $this->jsonResponse(200, "Email address updated successfully.");
        } else {
            return $this->jsonResponse(500, "Failed to update email address.");
        }
    }

    public function uploadEmployees() {
        $importFailed = false;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
            return $this->jsonResponse(405, "Request is invalid");
        }
    
        // Handle the uploaded file
        $uploadedFile = $_FILES['file'];    
    
        // Check for errors during file upload
        if ($uploadedFile['error'] !== UPLOAD_ERR_OK || $uploadedFile['type'] !== "text/csv") {
            return $this->jsonResponse(500, "Please upload a valid file.");
        }

        $batchSize = 50; // Set an appropriate batch size
    
        $csv = [];
        
        if(($handle = fopen($uploadedFile['tmp_name'], 'r')) !== FALSE) {

            fgetcsv($handle, 1000, ','); // Skip the header row

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $csv[] = [
                    'company_name' => $data[0],
                    'employee_name' => $data[1],
                    'email_address' => $data[2],
                    'salary' => $data[3],
                ];
                
                // Insert/update data in batches
                if (count($csv) >= $batchSize) {
                    $res = $this->processBatch($csv);
                    if (!$res) {
                        $importFailed = true;
                        break; // Stop importing if a batch fails
                    }
                    $csv = [];
                }
            }
    
            // Process any remaining data in the last batch
            if (!empty($csv)) {
                $res = $this->processBatch($csv);
                if (!$res) {
                    $importFailed = true;
                }
            }
    
            fclose($handle);
        }
    
        // Return response based on import success/failure
        if ($importFailed) {
            return $this->jsonResponse(500, "Import failed. Please check the data and try again.");
        } else {
            return $this->jsonResponse(200, "File uploaded and processed successfully.", ["employees" => $this->getEmployees()]);
        }
    }
    

}
