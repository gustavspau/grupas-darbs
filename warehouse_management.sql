-- ========================================
-- NOLIKTAVAS VADĪBAS SISTĒMAS DATUBĀZE
-- Warehouse Management System Database
-- ========================================

-- Izveidojam datubāzi
CREATE DATABASE IF NOT EXISTS warehouse_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE warehouse_management;

-- ========================================
-- LIETOTĀJU TABULA (Users Table)
-- ========================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'warehouse', 'shelf') NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    phone VARCHAR(20),
    hire_date DATE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- NOLIKTAVAS SEKCIJU TABULA (Warehouse Sections)
-- ========================================
CREATE TABLE warehouse_sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_code VARCHAR(10) UNIQUE NOT NULL,
    section_name VARCHAR(50) NOT NULL,
    description TEXT,
    max_capacity INT DEFAULT 0,
    current_capacity INT DEFAULT 0,
    status ENUM('active', 'maintenance', 'closed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- PLAUKTU TABULA (Shelves Table)
-- ========================================
CREATE TABLE shelves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shelf_code VARCHAR(20) UNIQUE NOT NULL,
    section_id INT NOT NULL,
    shelf_level INT NOT NULL,
    max_weight DECIMAL(10,2) DEFAULT 0,
    current_weight DECIMAL(10,2) DEFAULT 0,
    max_items INT DEFAULT 0,
    current_items INT DEFAULT 0,
    status ENUM('normal', 'low_stock', 'needs_organize', 'maintenance', 'damaged') DEFAULT 'normal',
    last_checked TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES warehouse_sections(id) ON DELETE CASCADE,
    INDEX idx_shelf_section (section_id),
    INDEX idx_shelf_status (status)
);

-- ========================================
-- PRODUKTU KATEGORIJU TABULA (Product Categories)
-- ========================================
CREATE TABLE product_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_category_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_category_id) REFERENCES product_categories(id) ON DELETE SET NULL
);

-- ========================================
-- PRODUKTU TABULA (Products Table)
-- ========================================
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_code VARCHAR(50) UNIQUE NOT NULL,
    barcode VARCHAR(100) UNIQUE,
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT,
    unit_price DECIMAL(10,2) DEFAULT 0,
    cost_price DECIMAL(10,2) DEFAULT 0,
    weight DECIMAL(10,3) DEFAULT 0,
    dimensions VARCHAR(50), -- Format: "length x width x height"
    min_stock_level INT DEFAULT 0,
    max_stock_level INT DEFAULT 0,
    reorder_point INT DEFAULT 0,
    supplier_info TEXT,
    status ENUM('active', 'discontinued', 'out_of_stock') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL,
    INDEX idx_product_code (product_code),
    INDEX idx_barcode (barcode),
    INDEX idx_product_category (category_id),
    INDEX idx_product_status (status)
);

-- ========================================
-- INVENTĀRA TABULA (Inventory Table)
-- ========================================
CREATE TABLE inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    shelf_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    reserved_quantity INT DEFAULT 0,
    available_quantity INT GENERATED ALWAYS AS (quantity - reserved_quantity) STORED,
    batch_number VARCHAR(50),
    expiry_date DATE NULL,
    last_counted TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (shelf_id) REFERENCES shelves(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_shelf_batch (product_id, shelf_id, batch_number),
    INDEX idx_inventory_product (product_id),
    INDEX idx_inventory_shelf (shelf_id),
    INDEX idx_inventory_expiry (expiry_date)
);

-- ========================================
-- PASŪTĪJUMU TABULA (Orders Table)
-- ========================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    order_type ENUM('inbound', 'outbound') NOT NULL,
    supplier_customer VARCHAR(255),
    contact_info TEXT,
    total_amount DECIMAL(12,2) DEFAULT 0,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    expected_date DATE,
    completed_date TIMESTAMP NULL,
    assigned_user_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_order_type (order_type),
    INDEX idx_order_status (status),
    INDEX idx_order_priority (priority),
    INDEX idx_order_assigned (assigned_user_id)
);

-- ========================================
-- PASŪTĪJUMU POZĪCIJU TABULA (Order Items Table)
-- ========================================
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_ordered INT NOT NULL,
    quantity_processed INT DEFAULT 0,
    unit_price DECIMAL(10,2) DEFAULT 0,
    total_price DECIMAL(12,2) GENERATED ALWAYS AS (quantity_ordered * unit_price) STORED,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_order_items_order (order_id),
    INDEX idx_order_items_product (product_id)
);

-- ========================================
-- UZDEVUMU TABULA (Tasks Table)
-- ========================================
CREATE TABLE tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    task_title VARCHAR(255) NOT NULL,
    task_description TEXT,
    task_type ENUM('receive', 'ship', 'organize', 'count', 'maintain', 'other') NOT NULL,
    assigned_user_id INT,
    related_order_id INT NULL,
    related_shelf_id INT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    due_date TIMESTAMP NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    estimated_duration INT, -- in minutes
    actual_duration INT, -- in minutes
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (related_order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (related_shelf_id) REFERENCES shelves(id) ON DELETE SET NULL,
    INDEX idx_task_assigned (assigned_user_id),
    INDEX idx_task_status (status),
    INDEX idx_task_priority (priority),
    INDEX idx_task_type (task_type),
    INDEX idx_task_due_date (due_date)
);

-- ========================================
-- AKTIVITĀŠU ŽURNĀLA TABULA (Activity Log Table)
-- ========================================
CREATE TABLE activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action_type VARCHAR(50) NOT NULL,
    action_description TEXT NOT NULL,
    related_table VARCHAR(50),
    related_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_activity_user (user_id),
    INDEX idx_activity_type (action_type),
    INDEX idx_activity_timestamp (timestamp),
    INDEX idx_activity_related (related_table, related_id)
);

-- ========================================
-- INVENTĀRA KUSTĪBAS TABULA (Inventory Movements Table)
-- ========================================
CREATE TABLE inventory_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    shelf_id INT NOT NULL,
    movement_type ENUM('in', 'out', 'transfer', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    reference_type ENUM('order', 'task', 'manual', 'system') NOT NULL,
    reference_id INT,
    batch_number VARCHAR(50),
    reason VARCHAR(255),
    performed_by INT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (shelf_id) REFERENCES shelves(id) ON DELETE CASCADE,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_movement_product (product_id),
    INDEX idx_movement_shelf (shelf_id),
    INDEX idx_movement_type (movement_type),
    INDEX idx_movement_timestamp (timestamp),
    INDEX idx_movement_reference (reference_type, reference_id)
);

-- ========================================
-- SISTĒMAS IESTATĪJUMU TABULA (System Settings Table)
-- ========================================
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'integer', 'decimal', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    category VARCHAR(50) DEFAULT 'general',
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_setting_key (setting_key),
    INDEX idx_setting_category (category)
);

-- ========================================
-- PIEGĀDĀTĀJU TABULA (Suppliers Table)
-- ========================================
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_code VARCHAR(20) UNIQUE NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    payment_terms VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_supplier_code (supplier_code),
    INDEX idx_supplier_status (status)
);

-- ========================================
-- PRODUKTU-PIEGĀDĀTĀJU SAITES TABULA (Product Suppliers Table)
-- ========================================
CREATE TABLE product_suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    supplier_id INT NOT NULL,
    supplier_product_code VARCHAR(100),
    lead_time_days INT DEFAULT 0,
    min_order_quantity INT DEFAULT 1,
    cost_price DECIMAL(10,2) DEFAULT 0,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_supplier (product_id, supplier_id),
    INDEX idx_product_suppliers_product (product_id),
    INDEX idx_product_suppliers_supplier (supplier_id)
);

-- ========================================
-- DATU IEVIETOŠANA (Sample Data Insert)
-- ========================================

-- Lietotāji (Users)
INSERT INTO users (username, password_hash, email, first_name, last_name, role, phone, hire_date) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@warehouse.lv', 'Administratora', 'Konts', 'admin', '+371 20000001', '2023-01-01'),
('janis.berzins', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'janis.berzins@warehouse.lv', 'Jānis', 'Bērziņš', 'warehouse', '+371 20000002', '2023-02-15'),
('anna.ozolina', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'anna.ozolina@warehouse.lv', 'Anna', 'Ozoliņa', 'shelf', '+371 20000003', '2023-03-10'),
('peteris.kalnins', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'peteris.kalnins@warehouse.lv', 'Pēteris', 'Kalniņš', 'warehouse', '+371 20000004', '2023-04-05'),
('marta.liepa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'marta.liepa@warehouse.lv', 'Marta', 'Liepa', 'shelf', '+371 20000005', '2023-05-20');

-- Noliktavas sekcijas (Warehouse Sections)
INSERT INTO warehouse_sections (section_code, section_name, description, max_capacity) VALUES
('A', 'Sekcija A', 'Galvenā noliktavas sekcija elektronikai', 1000),
('B', 'Sekcija B', 'Sekcija sadzīves tehnikai', 800),
('C', 'Sekcija C', 'Sekcija rezerves daļām', 600),
('D', 'Sekcija D', 'Sekcija sezonālajām precēm', 400);

-- Plaukti (Shelves)
INSERT INTO shelves (shelf_code, section_id, shelf_level, max_weight, max_items, status) VALUES
('A1', 1, 1, 500.00, 50, 'normal'),
('A2', 1, 1, 500.00, 50, 'low_stock'),
('A3', 1, 2, 400.00, 40, 'normal'),
('A4', 1, 2, 400.00, 40, 'needs_organize'),
('B1', 2, 1, 600.00, 30, 'normal'),
('B2', 2, 1, 600.00, 30, 'normal'),
('B3', 2, 2, 500.00, 25, 'maintenance'),
('B4', 2, 2, 500.00, 25, 'normal'),
('C1', 3, 1, 300.00, 100, 'low_stock'),
('C2', 3, 1, 300.00, 100, 'normal'),
('C3', 3, 2, 250.00, 80, 'normal'),
('C4', 3, 2, 250.00, 80, 'needs_organize');

-- Produktu kategorijas (Product Categories)
INSERT INTO product_categories (category_name, description) VALUES
('Elektronika', 'Elektronikas produkti'),
('Datortehnika', 'Datori un to komponenti'),
('Telefoni', 'Mobilie telefoni un aksesuāri'),
('Sadzīves tehnika', 'Mājsaimniecības elektriskā tehnika'),
('Rezerves daļas', 'Rezerves daļas dažādiem produktiem');

-- Produkti (Products)
INSERT INTO products (product_code, barcode, product_name, description, category_id, unit_price, cost_price, weight, min_stock_level, max_stock_level, reorder_point) VALUES
('LAP001', '1234567890123', 'Laptop ASUS VivoBook', 'ASUS VivoBook 15.6" laptop', 2, 899.99, 650.00, 2.1, 5, 50, 10),
('PHN001', '1234567890124', 'Samsung Galaxy S23', 'Samsung Galaxy S23 viedtālrunis', 3, 999.99, 750.00, 0.168, 10, 100, 20),
('TAB001', '1234567890125', 'iPad Air', 'Apple iPad Air 10.9"', 1, 699.99, 500.00, 0.461, 5, 30, 8),
('SPK001', '1234567890126', 'JBL Charge 5', 'JBL Charge 5 Bluetooth skaļrunis', 1, 179.99, 120.00, 0.96, 15, 80, 25),
('HDD001', '1234567890127', 'Seagate 1TB HDD', 'Seagate 1TB ārējais cietais disks', 2, 79.99, 55.00, 0.25, 20, 100, 30);

-- Piegādātāji (Suppliers)
INSERT INTO suppliers (supplier_code, company_name, contact_person, email, phone, address, city, country, postal_code) VALUES
('SUP001', 'Tech Distribution SIA', 'Andris Kalniņš', 'andris@techdist.lv', '+371 67123456', 'Brīvības iela 123', 'Rīga', 'Latvija', 'LV-1001'),
('SUP002', 'Electronics Import Ltd', 'Maria Berzina', 'maria@eimport.lv', '+371 67234567', 'Daugavgrīvas iela 45', 'Rīga', 'Latvija', 'LV-1007'),
('SUP003', 'Global Tech Solutions', 'Roberts Ozols', 'roberts@globtech.eu', '+371 67345678', 'Elizabetes iela 67', 'Rīga', 'Latvija', 'LV-1050');

-- Inventārs (Inventory)
INSERT INTO inventory (product_id, shelf_id, quantity, batch_number) VALUES
(1, 1, 25, 'BATCH001'),
(1, 3, 15, 'BATCH002'),
(2, 2, 45, 'BATCH003'),
(2, 4, 30, 'BATCH004'),
(3, 5, 20, 'BATCH005'),
(4, 6, 35, 'BATCH006'),
(5, 9, 60, 'BATCH007');

-- Pasūtījumi (Orders)
INSERT INTO orders (order_number, order_type, supplier_customer, total_amount, status, priority, expected_date, assigned_user_id) VALUES
('ORD001', 'inbound', 'Tech Distribution SIA', 15000.00, 'pending', 'normal', '2024-01-15', 2),
('ORD002', 'outbound', 'Klients ABC SIA', 2500.00, 'processing', 'high', '2024-01-12', 2),
('ORD003', 'inbound', 'Electronics Import Ltd', 8000.00, 'completed', 'normal', '2024-01-10', 4),
('ORD004', 'outbound', 'Privātpersona', 899.99, 'pending', 'urgent', '2024-01-11', 2);

-- Uzdevumi (Tasks)
INSERT INTO tasks (task_title, task_description, task_type, assigned_user_id, priority, status, due_date) VALUES
('Pasūtījuma #ORD002 sagatavošana', 'Sagatavot pasūtījumu nosūtīšanai līdz 15:00', 'ship', 2, 'urgent', 'pending', '2024-01-11 15:00:00'),
('Plaukta A4 organizēšana', 'Pārkārtot produktus plauktā A4', 'organize', 3, 'normal', 'pending', '2024-01-12 17:00:00'),
('Inventāra pārbaude sekcijā C', 'Veikt inventāra pārbaudi visā C sekcijā', 'count', 5, 'normal', 'in_progress', '2024-01-13 12:00:00'),
('Plaukta B3 apkope', 'Veikt plaukta B3 tehnisko apkopi', 'maintain', 3, 'high', 'pending', '2024-01-14 10:00:00');

-- Sistēmas iestatījumi (System Settings)
INSERT INTO system_settings (setting_key, setting_value, setting_type, description, category) VALUES
('warehouse_name', 'Galvenā noliktava', 'string', 'Noliktavas nosaukums', 'general'),
('max_shelf_capacity', '100', 'integer', 'Maksimālais produktu skaits vienā plauktā', 'inventory'),
('low_stock_threshold', '10', 'integer', 'Zema atlikuma slieksnis', 'inventory'),
('backup_frequency', '24', 'integer', 'Datubāzes dublējuma biežums (stundās)', 'system'),
('currency', 'EUR', 'string', 'Sistēmas valūta', 'general'),
('language', 'lv', 'string', 'Sistēmas valoda', 'general');

-- ========================================
-- FUNKCIJAS UN PROCEDŪRAS (Functions and Procedures)
-- ========================================

-- Funkcija inventāra līmeņa aprēķināšanai
DELIMITER //
CREATE FUNCTION GetTotalStock(product_id INT) 
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE total INT DEFAULT 0;
    SELECT COALESCE(SUM(quantity), 0) INTO total 
    FROM inventory 
    WHERE inventory.product_id = product_id;
    RETURN total;
END //
DELIMITER ;

-- Procedūra inventāra kustības reģistrēšanai
DELIMITER //
CREATE PROCEDURE RecordInventoryMovement(
    IN p_product_id INT,
    IN p_shelf_id INT,
    IN p_movement_type ENUM('in', 'out', 'transfer', 'adjustment'),
    IN p_quantity INT,
    IN p_reference_type ENUM('order', 'task', 'manual', 'system'),
    IN p_reference_id INT,
    IN p_batch_number VARCHAR(50),
    IN p_reason VARCHAR(255),
    IN p_performed_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Ierakstām kustību žurnālā
    INSERT INTO inventory_movements (
        product_id, shelf_id, movement_type, quantity, 
        reference_type, reference_id, batch_number, reason, performed_by
    ) VALUES (
        p_product_id, p_shelf_id, p_movement_type, p_quantity,
        p_reference_type, p_reference_id, p_batch_number, p_reason, p_performed_by
    );
    
    -- Atjauninām inventāru
    IF p_movement_type = 'in' THEN
        INSERT INTO inventory (product_id, shelf_id, quantity, batch_number)
        VALUES (p_product_id, p_shelf_id, p_quantity, p_batch_number)
        ON DUPLICATE KEY UPDATE quantity = quantity + p_quantity;
    ELSEIF p_movement_type = 'out' THEN
        UPDATE inventory 
        SET quantity = quantity - p_quantity
        WHERE product_id = p_product_id AND shelf_id = p_shelf_id
        AND batch_number = p_batch_number;
    END IF;
    
    COMMIT;
END //
DELIMITER ;

-- ========================================
-- SKATI (Views)
-- ========================================

-- Produktu atlikumu pārskats
CREATE VIEW product_stock_summary AS
SELECT 
    p.id,
    p.product_code,
    p.product_name,
    pc.category_name,
    COALESCE(SUM(i.quantity), 0) as total_stock,
    COALESCE(SUM(i.reserved_quantity), 0) as reserved_stock,
    COALESCE(SUM(i.available_quantity), 0) as available_stock,
    p.min_stock_level,
    p.reorder_point,
    CASE 
        WHEN COALESCE(SUM(i.quantity), 0) <= p.reorder_point THEN 'Nepieciešama papildināšana'
        WHEN COALESCE(SUM(i.quantity), 0) <= p.min_stock_level THEN 'Zems atlikums'
        ELSE 'Normāls'
    END as stock_status
FROM products p
LEFT JOIN inventory i ON p.id = i.product_id
LEFT JOIN product_categories pc ON p.category_id = pc.id
WHERE p.status = 'active'
GROUP BY p.id, p.product_code, p.product_name, pc.category_name, p.min_stock_level, p.reorder_point;

-- Plauktu noslodzes pārskats
CREATE VIEW shelf_utilization AS
SELECT 
    s.shelf_code,
    ws.section_name,
    s.max_items,
    s.current_items,
    s.max_weight,
    s.current_weight,
    ROUND((s.current_items / s.max_items) * 100, 2) as item_utilization_percent,
    ROUND((s.current_weight / s.max_weight) * 100, 2) as weight_utilization_percent,
    s.status,
    s.last_checked
FROM shelves s
JOIN warehouse_sections ws ON s.section_id = ws.id;

-- Aktīvo uzdevumu pārskats
CREATE VIEW active_tasks_summary AS
SELECT 
    t.id,
    t.task_title,
    t.task_type,
    CONCAT(u.first_name, ' ', u.last_name) as assigned_user,
    t.priority,
    t.status,
    t.due_date,
    CASE 
        WHEN t.due_date < NOW() AND t.status != 'completed' THEN 'Kavēts'
        WHEN t.due_date < DATE_ADD(NOW(), INTERVAL 2 HOUR) AND t.status != 'completed' THEN 'Steidzams'
        ELSE 'Normāls'
    END as urgency_status
FROM tasks t
LEFT JOIN users u ON t.assigned_user_id = u.id
WHERE t.status IN ('pending', 'in_progress')
ORDER BY t.due_date ASC;

-- ========================================
-- TRIGGERI (Triggers)
-- ========================================

-- Atjaunina plaukta statistiku, kad mainās inventārs
DELIMITER //
CREATE TRIGGER update_shelf_stats_after_inventory_change
AFTER UPDATE ON inventory
FOR EACH ROW
BEGIN
    UPDATE shelves s
    SET 
        current_items = (
            SELECT COUNT(*) FROM inventory i WHERE i.shelf_id = s.id AND i.quantity > 0
        ),
        current_weight = (
            SELECT COALESCE(SUM(i.quantity * p.weight), 0)
            FROM inventory i
            JOIN products p ON i.product_id = p.id
            WHERE i.shelf_id = s.id
        )
    WHERE s.id = NEW.shelf_id OR s.id = OLD.shelf_id;
END //
DELIMITER ;

-- Reģistrē aktivitāti, kad tiek mainīts pasūtījuma statuss
DELIMITER //
CREATE TRIGGER log_order_status_change
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO activity_log (user_id, action_type, action_description, related_table, related_id)
        VALUES (
            NEW.assigned_user_id,
            'order_status_change',
            CONCAT('Pasūtījuma ', NEW.order_number, ' statuss mainīts no "', OLD.status, '" uz "', NEW.status, '"'),
            'orders',
            NEW.id
        );
    END IF;
END //
DELIMITER ;

-- ========================================
-- INDEKSI OPTIMIZĀCIJAI (Optimization Indexes)
-- ========================================

-- Papildu indeksi ātrai meklēšanai
CREATE INDEX idx_inventory_product_shelf ON inventory(product_id, shelf_id);
CREATE INDEX idx_orders_status_priority ON orders(status, priority);
CREATE INDEX idx_tasks_status_due_date ON tasks(status, due_date);
CREATE INDEX idx_activity_log_timestamp_user ON activity_log(timestamp, user_id);

-- ========================================
-- PILNĪGS!
-- Database schema ir gatavs lietošanai!
-- ======================================== 