-- ========================================
-- WAREHOUSE MANAGEMENT DATABASE
-- Noliktavas vadības datubāze
-- ========================================

-- Izveidojam datubāzi
CREATE DATABASE IF NOT EXISTS warehouse_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE warehouse_management;

-- ========================================
-- 1. LIETOTĀJU TABULA (Users Table)
-- ========================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'warehouse', 'shelf') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- 2. PRODUKTU TABULA (Products Table)
-- ========================================
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_code VARCHAR(50) UNIQUE NOT NULL,
    barcode VARCHAR(100),
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    unit_price DECIMAL(10,2) DEFAULT 0,
    min_stock_level INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- 3. PLAUKTU TABULA (Shelves Table)
-- ========================================
CREATE TABLE shelves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shelf_code VARCHAR(20) UNIQUE NOT NULL,
    section VARCHAR(10) NOT NULL, -- A, B, C, D
    max_capacity INT DEFAULT 100,
    status ENUM('normal', 'low_stock', 'needs_organize', 'maintenance') DEFAULT 'normal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- 4. INVENTĀRA TABULA (Inventory Table)
-- ========================================
CREATE TABLE inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    shelf_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (shelf_id) REFERENCES shelves(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_shelf (product_id, shelf_id)
);

-- ========================================
-- 5. PASŪTĪJUMU TABULA (Orders Table)
-- ========================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    order_type ENUM('inbound', 'outbound') NOT NULL,
    customer_supplier VARCHAR(255),
    total_amount DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('normal', 'high', 'urgent') DEFAULT 'normal',
    assigned_user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ========================================
-- 6. UZDEVUMU TABULA (Tasks Table)
-- ========================================
CREATE TABLE tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    task_title VARCHAR(255) NOT NULL,
    task_description TEXT,
    task_type ENUM('receive', 'ship', 'organize', 'count', 'maintain') NOT NULL,
    assigned_user_id INT,
    priority ENUM('normal', 'high', 'urgent') DEFAULT 'normal',
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    due_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ========================================
-- 7. Iestatījumu TABULA (Settings Table)
-- ========================================
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    setting_key VARCHAR(50) NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_setting (user_id, setting_key)
);

-- ========================================
-- DATABASE READY!
-- Datubāze ir gatava!
-- ======================================== 