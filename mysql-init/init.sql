CREATE DATABASE IF NOT EXISTS employee_db;
USE employee_db;

CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255),
    employee_name VARCHAR(255),
    email_address VARCHAR(255),
    salary INT
);
