<?php
require_once __DIR__ . '/../config/database.php';

class BaseTest extends PHPUnit\Framework\TestCase {
    protected $conn;
    protected $testUser = [
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'Test123!',
        'first_name' => 'Test',
        'last_name' => 'User',
        'role' => 'user'
    ];

    protected function setUp(): void {
        $this->conn = getConnection();
        // Start transaction
        $this->conn->beginTransaction();
    }

    protected function tearDown(): void {
        // Rollback transaction
        $this->conn->rollBack();
        $this->conn = null;
    }

    protected function createTestUser() {
        $sql = "INSERT INTO users (username, email, password, first_name, last_name, role) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $hashedPassword = password_hash($this->testUser['password'], PASSWORD_DEFAULT);
        $stmt->execute([
            $this->testUser['username'],
            $this->testUser['email'],
            $hashedPassword,
            $this->testUser['first_name'],
            $this->testUser['last_name'],
            $this->testUser['role']
        ]);
        return $this->conn->lastInsertId();
    }

    protected function createTestProduct() {
        $sql = "INSERT INTO products (name, description, price, quantity, category_id) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'Test Product',
            'Test Description',
            10.99,
            100,
            1
        ]);
        return $this->conn->lastInsertId();
    }
} 