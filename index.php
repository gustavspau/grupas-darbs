<?php
require_once 'auth.php';

// Require user to be logged in
requireLogin();

// Get current user info
$user = getCurrentUser();
$userRole = $user['role'];
$userName = $user['first_name'] . ' ' . $user['last_name'];
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noliktavas Vadības Sistēma</title>
    <link rel="stylesheet" href="styles.css?v=3.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Set initial screen based on user role -->
    <script>
        const userRole = '<?php echo htmlspecialchars($userRole); ?>';
        const userName = '<?php echo htmlspecialchars($userName); ?>';
    </script>
    
    <!-- Show appropriate dashboard based on user role -->

    <!-- Admin Dashboard -->
    <div id="admin-screen" class="screen<?php echo ($userRole === 'admin') ? ' active' : ''; ?>">
        <header class="main-header">
            <div class="header-left">
                <i class="fas fa-warehouse"></i>
                <h1>Administrators</h1>
            </div>
            <div class="header-right">
                <span class="user-info">
                    <i class="fas fa-user-shield"></i>
                    <span id="admin-username"><?php echo htmlspecialchars($userName); ?></span>
                </span>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </header>

        <nav class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-archive"></i>
                <span>STASH</span>
            </div>
            <ul class="nav-menu">
                <li><a href="#" onclick="showAdminSection('dashboard')" class="nav-link active">
                    <i class="fas fa-home"></i>
                    Sākums
                </a></li>
                <li><a href="#" onclick="showAdminSection('inventory')" class="nav-link">
                    <i class="fas fa-plus"></i>
                    Pievienot produktu
                </a></li>
                <li><a href="#" onclick="showAddUserModal()" class="nav-link">
                    <i class="fas fa-plus"></i>
                    Pievienot lietotāju
                </a></li>
                <li><a href="#" onclick="showAdminSection('users')" class="nav-link">
                    <i class="fas fa-users"></i>
                    Lietotāji
                </a></li>
                <li><a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Iziet
                </a></li>
            </ul>
        </nav>

        <main class="main-content">
            <!-- Admin Dashboard Section -->
            <div id="admin-dashboard" class="content-section active">
                <h2>Pārskats</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <div class="stat-info">
                            <h3>25</h3>
                            <p>Aktīvi lietotāji</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-boxes"></i>
                        <div class="stat-info">
                            <h3>1,247</h3>
                            <p>Produkti noliktavā</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-truck"></i>
                        <div class="stat-info">
                            <h3>18</h3>
                            <p>Gaidošie pasūtījumi</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-chart-line"></i>
                        <div class="stat-info">
                            <h3>€15,240</h3>
                            <p>Mēneša apgrozījums</p>
                        </div>
                    </div>
                </div>
                
                <div class="recent-activity">
                    <h3>Pēdējās aktivitātes</h3>
                    <div class="activity-list">
                        <div class="activity-item">
                            <i class="fas fa-plus-circle"></i>
                            <div class="activity-details">
                                <p><strong>Jānis Bērziņš</strong> pievienoja jaunu produktu</p>
                                <span class="time">pirms 5 minūtēm</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <i class="fas fa-edit"></i>
                            <div class="activity-details">
                                <p><strong>Anna Ozoliņa</strong> atjaunināja inventāru</p>
                                <span class="time">pirms 15 minūtēm</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <i class="fas fa-shipping-fast"></i>
                            <div class="activity-details">
                                <p><strong>Pēteris Kalniņš</strong> nosūtīja pasūtījumu</p>
                                <span class="time">pirms 1 stundas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Users Section -->
            <div id="admin-users" class="content-section">
                <h2>Lietotāji</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Vārds</th>
                                <th>Uzvārds</th>
                                <th>E-pasts</th>
                                <th>Loma</th>
                                <th>Statuss</th>
                                <th>Darbības</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <!-- Users will be loaded here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add User Modal -->
            <div id="addUserModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeUserModal()">&times;</span>
                    <h2>Pievienot jaunu lietotāju</h2>
                    <form id="addUserForm">
                        <div class="form-group">
                            <label for="firstName">Vārds: *</label>
                            <input type="text" id="firstName" name="firstName" maxlength="50" placeholder="Ievadiet vārdu">
                            <div class="error-message" id="firstName-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Uzvārds: *</label>
                            <input type="text" id="lastName" name="lastName" maxlength="50" placeholder="Ievadiet uzvārdu">
                            <div class="error-message" id="lastName-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="email">E-pasts: *</label>
                            <input type="email" id="email" name="email" maxlength="254" placeholder="lietotajs@example.com">
                            <div class="error-message" id="email-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="password">Parole: *</label>
                            <input type="password" id="password" name="password" maxlength="128" placeholder="Vismaz 8 simboli ar lielajiem/mazajiem burtiem, cipariem un speciālajiem simboliem">
                            <div class="error-message" id="password-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="role">Loma: *</label>
                            <select id="role" name="role">
                                <option value="">-- Izvēlieties lomu --</option>
                                <option value="admin">Administrators</option>
                                <option value="warehouse">Noliktavas darbinieks</option>
                                <option value="shelf">Plauktu kārtotājs</option>
                            </select>
                            <div class="error-message" id="role-error"></div>
                        </div>
                        <button type="submit" class="btn btn-primary" onclick="submitUserForm(event)">Saglabāt</button>
                    </form>
                </div>
            </div>

            <!-- Admin Inventory Section -->
            <div id="admin-inventory" class="content-section">
                <h2>Inventāra pārvaldība</h2>
                <p>Šeit tiks attēlota inventāra pārvaldības saskarnes...</p>
                <div class="section-header">
                    <button class="btn btn-primary" onclick="showAddProductModal()">
                        <i class="fas fa-plus"></i>
                        Pievienot produktu
                    </button>
                    <div class="search-box">
                        <input type="text" id="productSearch" placeholder="Meklēt produktu...">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Produkta kods</th>
                                <th>Nosaukums</th>
                                <th>Kategorija</th>
                                <th>Svītrkods</th>
                                <th>Vienības cena</th>
                                <th>Min. krājums</th>
                                <th>Statuss</th>
                                <th>Darbības</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <!-- Products will be loaded here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Product Modal -->
            <div id="addProductModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <h2>Pievienot jaunu produktu</h2>
                    <form id="addProductForm" novalidate>
                        <div class="form-group">
                            <label for="productCode">Produkta kods: *</label>
                            <input 
                                type="text" 
                                id="productCode" 
                                name="productCode" 
                                minlength="6" 
                                maxlength="6" 
                                pattern="PRD[0-9]{3}" 
                                title="Formāts: PRD + 3 cipari (piemēram: PRD001)"
                                placeholder="PRD001"
                                style="text-transform: uppercase;">
                            <div class="error-message" id="productCode-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="productName">Nosaukums: *</label>
                            <input 
                                type="text" 
                                id="productName" 
                                name="productName" 
                                minlength="2" 
                                maxlength="50" 
                                placeholder="Produkta nosaukums">
                            <div class="error-message" id="productName-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="productCategory">Kategorija: *</label>
                            <select id="productCategory" name="productCategory">
                                <option value="">-- Izvēlieties kategoriju --</option>
                                <option value="Bulkīšu izstrādājumi">Bulkīšu izstrādājumi</option>
                                <option value="Šķidrums">Šķidrums</option>
                                <option value="Piena produkti">Piena produkti</option>
                                <option value="Dārzeņi">Dārzeņi</option>
                                <option value="Augļi">Augļi</option>
                                <option value="Sausie augļi un rieksti">Sausie augļi un rieksti</option>
                                <option value="Saldumi">Saldumi</option>
                            </select>
                            <div class="error-message" id="productCategory-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="barcode">Svītrkods (EAN-13):</label>
                            <input 
                                type="text" 
                                id="barcode" 
                                name="barcode" 
                                pattern="[0-9]{13}" 
                                title="Tieši 13 cipari (EAN-13 formāts)" 
                                placeholder="1234567890123"
                                minlength="13"
                                maxlength="13">
                            <div class="error-message" id="barcode-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="description">Apraksts:</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                maxlength="200" 
                                placeholder="Produkta apraksts (nav obligāts)"
                                rows="3"></textarea>
                            <div class="error-message" id="description-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="unitPrice">Vienības cena (€): *</label>
                            <input 
                                type="number" 
                                id="unitPrice" 
                                name="unitPrice" 
                                step="0.01" 
                                min="0" 
                                max="999999.99" 
                                placeholder="0.00">
                            <div class="error-message" id="unitPrice-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="minStock">Min. krājums: *</label>
                            <input 
                                type="number" 
                                id="minStock" 
                                name="minStock" 
                                min="0" 
                                max="999999" 
                                placeholder="0">
                            <div class="error-message" id="minStock-error"></div>
                        </div>
                        <button type="submit" class="btn btn-primary" onclick="submitProductForm(event)">Saglabāt</button>
                    </form>
                </div>
            </div>



            <!-- Admin Settings Section -->

        </main>
    </div>

    <!-- Warehouse Worker Dashboard -->
    <div id="warehouse-screen" class="screen<?php echo ($userRole === 'warehouse') ? ' active' : ''; ?>">
        <header class="main-header">
            <div class="header-left">
                <i class="fas fa-boxes"></i>
                <h1>Noliktavas darbinieks</h1>
            </div>
            <div class="header-right">
                <span class="user-info">
                    <i class="fas fa-user"></i>
                    <span id="warehouse-username"><?php echo htmlspecialchars($userName); ?></span>
                </span>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </header>

        <nav class="sidebar">
            <ul class="nav-menu">
                <li><a href="#" onclick="showWarehouseSection('dashboard')" class="nav-link active">
                    <i class="fas fa-chart-dashboard"></i>
                    Pārskats
                </a></li>
                <li><a href="#" onclick="showWarehouseSection('receive')" class="nav-link">
                    <i class="fas fa-truck-loading"></i>
                    Preču pieņemšana
                </a></li>
                <li><a href="#" onclick="showWarehouseSection('ship')" class="nav-link">
                    <i class="fas fa-shipping-fast"></i>
                    Preču nosūtīšana
                </a></li>
                <li><a href="#" onclick="showWarehouseSection('inventory')" class="nav-link">
                    <i class="fas fa-clipboard-list"></i>
                    Inventāra uzskaite
                </a></li>
                <li><a href="#" onclick="showWarehouseSection('tasks')" class="nav-link">
                    <i class="fas fa-tasks"></i>
                    Mani uzdevumi
                </a></li>
                <li><a href="#" onclick="showWarehouseSection('reports')" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    Atskaites
                </a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div id="warehouse-dashboard" class="content-section active">
                <h2>Noliktavas pārskats</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-truck-loading"></i>
                        <div class="stat-info">
                            <h3>12</h3>
                            <p>Gaidošie saņēmumi</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-shipping-fast"></i>
                        <div class="stat-info">
                            <h3>8</h3>
                            <p>Gaidošie sūtījumi</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-tasks"></i>
                        <div class="stat-info">
                            <h3>5</h3>
                            <p>Aktīvie uzdevumi</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-check-circle"></i>
                        <div class="stat-info">
                            <h3>23</h3>
                            <p>Pabeigti šodien</p>
                        </div>
                    </div>
                </div>

                <div class="urgent-tasks">
                    <h3>Steidzamie uzdevumi</h3>
                    <div class="task-list">
                        <div class="task-item urgent">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div class="task-details">
                                <p><strong>Pasūtījums #12345</strong> - nepieciešams nosūtīt līdz 15:00</p>
                                <span class="location">Sekcija A, Plaukts 15</span>
                            </div>
                            <button class="btn btn-sm">Sākt</button>
                        </div>
                        <div class="task-item">
                            <i class="fas fa-box"></i>
                            <div class="task-details">
                                <p><strong>Jauna prece</strong> - nepieciešams izvietot noliktavā</p>
                                <span class="location">Saņemšanas zona</span>
                            </div>
                            <button class="btn btn-sm">Sākt</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="warehouse-receive" class="content-section">
                <h2>Preču pieņemšana</h2>
                <div class="receive-container">
                    <div class="scan-section">
                        <div class="scan-box">
                            <input type="text" id="productScan" placeholder="Skenējiet vai ievadiet produkta kodu..." autofocus>
                            <button class="btn btn-primary" onclick="scanProduct()">
                                <i class="fas fa-barcode"></i>
                                Skenēt
                            </button>
                        </div>
                        <div class="scan-instructions">
                            <p><i class="fas fa-info-circle"></i> Skenējiet produkta kodu vai ievadiet to manuāli</p>
                        </div>
                    </div>

                    <div class="incoming-products">
                        <div class="section-header">
                            <h3>Gaidošās preces</h3>
                            <button class="btn btn-primary" onclick="generateRandomProducts()">
                                <i class="fas fa-random"></i>
                                Ģenerēt jaunas preces
                            </button>
                        </div>
                        <div id="incomingProductsList" class="incoming-list">
                            <!-- Random products will appear here -->
                        </div>
                    </div>
                    
                    <div class="received-items">
                        <div class="section-header">
                            <h3>Pieņemtās preces</h3>
                            <button class="btn btn-success" onclick="confirmAllItems()">
                                <i class="fas fa-check-double"></i>
                                Apstiprināt visu
                            </button>
                        </div>
                        <div id="receivedItemsList" class="items-list">
                            <!-- Items will be added here dynamically -->
                        </div>
                    </div>
                </div>
            </div>

            <div id="warehouse-ship" class="content-section">
                <h2>Preču nosūtīšana</h2>
                <p>Šeit tiks attēlota preču nosūtīšanas saskarnes...</p>
            </div>

            <div id="warehouse-inventory" class="content-section">
                <h2>Inventāra uzskaite</h2>
                <div class="inventory-management">
                    <div class="search-section">
                        <div class="search-box">
                            <input type="text" id="inventorySearch" placeholder="Meklēt produktu...">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Produkta kods</th>
                                    <th>Nosaukums</th>
                                    <th>Kategorija</th>
                                    <th>Krājums</th>
                                    <th>Cena</th>
                                    <th>Statuss</th>
                                    <th>Darbības</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody">
                                <!-- Inventory items will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="warehouse-tasks" class="content-section">
                <h2>Mani uzdevumi</h2>
                <p>Šeit tiks attēloti darbinieka uzdevumi...</p>
            </div>

            <div id="warehouse-reports" class="content-section">
                <h2>Atskaites un analīze</h2>
                <div class="reports-header">
                    <div class="reports-actions">
                        <button class="btn btn-secondary" onclick="printReport()">
                            <i class="fas fa-print"></i> Drukāt
                        </button>
                        <button class="btn btn-primary" onclick="exportReport('PDF')">
                            <i class="fas fa-file-pdf"></i> Eksportēt PDF
                        </button>
                    </div>
                </div>

                <!-- Overview Stats -->
                <div class="stats-grid reports-stats">
                    <div class="stat-card">
                        <i class="fas fa-boxes"></i>
                        <div class="stat-info">
                            <h3 id="totalProducts">-</h3>
                            <p>Kopējie produkti</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-truck-loading"></i>
                        <div class="stat-info">
                            <h3 id="receivedToday">-</h3>
                            <p>Pieņemts šodien</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-shipping-fast"></i>
                        <div class="stat-info">
                            <h3 id="shippedToday">-</h3>
                            <p>Nosūtīts šodien</p>
                        </div>
                    </div>
                    <div class="stat-card alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div class="stat-info">
                            <h3 id="lowStockItems">-</h3>
                            <p>Zems krājums</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Shelf Organizer Dashboard -->
    <div id="shelf-screen" class="screen<?php echo ($userRole === 'shelf') ? ' active' : ''; ?>">
        <header class="main-header">
            <div class="header-left">
                <i class="fas fa-layer-group"></i>
                <h1>Plauktu kartotājs</h1>
            </div>
            <div class="header-right">
                <span class="user-info">
                    <i class="fas fa-user"></i>
                    <span id="shelf-username"><?php echo htmlspecialchars($userName); ?></span>
                </span>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </header>

        <nav class="sidebar">
            <ul class="nav-menu">
                <li><a href="#" onclick="showShelfSection('dashboard')" class="nav-link active">
                    <i class="fas fa-home"></i>
                    Sākums
                </a></li>
                <li><a href="#" onclick="showShelfSection('inventory')" class="nav-link">
                    <i class="fas fa-boxes"></i>
                    Izvietot preces
                </a></li>
                <li><a href="#" onclick="showShelfSection('reports')" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    Sagatavot atskaites
                </a></li>
                <li><a href="#" onclick="showShelfSection('data')" class="nav-link">
                    <i class="fas fa-keyboard"></i>
                    Datu ievade sistēmā
                </a></li>
                <li><a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Iziet
                </a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div id="shelf-dashboard" class="content-section active">
                <h2>Plauktu pārskats</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-layer-group"></i>
                        <div class="stat-info">
                            <h3>156</h3>
                            <p>Kopējie plaukti</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div class="stat-info">
                            <h3>7</h3>
                            <p>Nepieciešama papildināšana</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-sort"></i>
                        <div class="stat-info">
                            <h3>3</h3>
                            <p>Nepieciešama reorganizācija</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-tools"></i>
                        <div class="stat-info">
                            <h3>2</h3>
                            <p>Apkopes vajag</p>
                        </div>
                    </div>
                </div>

                <div class="shelf-map">
                    <h3>Noliktavas karte</h3>
                    <div class="warehouse-grid">
                        <div class="section section-a">
                            <h4>Sekcija A</h4>
                            <div class="shelves">
                                <div class="shelf normal" data-shelf="A1">A1</div>
                                <div class="shelf low-stock" data-shelf="A2">A2</div>
                                <div class="shelf normal" data-shelf="A3">A3</div>
                                <div class="shelf needs-organize" data-shelf="A4">A4</div>
                            </div>
                        </div>
                        <div class="section section-b">
                            <h4>Sekcija B</h4>
                            <div class="shelves">
                                <div class="shelf normal" data-shelf="B1">B1</div>
                                <div class="shelf normal" data-shelf="B2">B2</div>
                                <div class="shelf maintenance" data-shelf="B3">B3</div>
                                <div class="shelf normal" data-shelf="B4">B4</div>
                            </div>
                        </div>
                        <div class="section section-c">
                            <h4>Sekcija C</h4>
                            <div class="shelves">
                                <div class="shelf low-stock" data-shelf="C1">C1</div>
                                <div class="shelf normal" data-shelf="C2">C2</div>
                                <div class="shelf normal" data-shelf="C3">C3</div>
                                <div class="shelf needs-organize" data-shelf="C4">C4</div>
                            </div>
                        </div>
                    </div>
                    <div class="legend">
                        <div class="legend-item">
                            <div class="color-box normal"></div>
                            <span>Normāls stāvoklis</span>
                        </div>
                        <div class="legend-item">
                            <div class="color-box low-stock"></div>
                            <span>Zems atlikums</span>
                        </div>
                        <div class="legend-item">
                            <div class="color-box needs-organize"></div>
                            <span>Nepieciešama organizēšana</span>
                        </div>
                        <div class="legend-item">
                            <div class="color-box maintenance"></div>
                            <span>Nepieciešama apkope</span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="shelf-inventory" class="content-section">
                <h2>Preču izvietošana</h2>
                <div class="shelf-organize-simple">
                    <div class="organize-instructions">
                        <h3>Uzdevumi šodienai:</h3>
                        <ul class="task-list-simple">
                            <li><i class="fas fa-check-circle"></i> Sakārtot preces sekcijā A</li>
                            <li><i class="fas fa-check-circle"></i> Pārvietot jaunās preces uz plauktiem</li>
                            <li><i class="fas fa-circle"></i> Pārbaudīt plauktu marķējumu</li>
                            <li><i class="fas fa-circle"></i> Sakārtot preces pēc kategorijām</li>
                        </ul>
            </div>

                    <div class="quick-actions">
                        <h3>Ātras darbības:</h3>
                        <div class="action-buttons">
                            <button class="btn btn-primary" onclick="alert('Produktu saraksts ielādēts')">
                                <i class="fas fa-list"></i> Skatīt produktu sarakstu
                            </button>
                            <button class="btn btn-success" onclick="alert('Kārtošana uzsākta')">
                                <i class="fas fa-sort"></i> Sākt kārtošanu
                            </button>
                            <button class="btn btn-info" onclick="alert('Plauktu karte atvērta')">
                                <i class="fas fa-map"></i> Plauktu karte
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="shelf-reports" class="content-section">
                <h2>Atskaišu sagatavošana</h2>
                <div class="reports-simple">
                    <div class="report-options">
                        <h3>Pieejamās atskaites:</h3>
                        <div class="report-list">
                            <div class="report-item">
                                <i class="fas fa-chart-bar"></i>
                                <div class="report-info">
                                    <h4>Plauktu stāvokļa atskaite</h4>
                                    <p>Pārskats par visu plauktu stāvokli un nepieciešamajām darbībām</p>
                                </div>
                                <button class="btn btn-primary" onclick="alert('Atskaite ģenerēta')">Ģenerēt</button>
                            </div>
                            <div class="report-item">
                                <i class="fas fa-boxes"></i>
                                <div class="report-info">
                                    <h4>Preču izvietojuma atskaite</h4>
                                    <p>Detalizēta informācija par preču izvietojumu plauktos</p>
                                </div>
                                <button class="btn btn-primary" onclick="alert('Atskaite ģenerēta')">Ģenerēt</button>
                            </div>
                            <div class="report-item">
                                <i class="fas fa-tasks"></i>
                                <div class="report-info">
                                    <h4>Dienas darbu atskaite</h4>
                                    <p>Kopsavilkums par šodienas veiktajiem darbiem</p>
                                </div>
                                <button class="btn btn-primary" onclick="alert('Atskaite ģenerēta')">Ģenerēt</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="shelf-data" class="content-section">
                <h2>Datu ievade sistēmā</h2>
                <div class="data-entry-simple">
                    <div class="entry-form">
                        <h3>Ievadīt informāciju par plauktu:</h3>
                        <form class="simple-form">
                            <div class="form-group">
                                <label>Plaukta kods:</label>
                                <select class="form-control">
                                    <option value="">Izvēlieties plauktu</option>
                                    <option value="A1">A1</option>
                                    <option value="A2">A2</option>
                                    <option value="A3">A3</option>
                                    <option value="A4">A4</option>
                                    <option value="B1">B1</option>
                                    <option value="B2">B2</option>
                                    <option value="B3">B3</option>
                                    <option value="B4">B4</option>
                                    <option value="C1">C1</option>
                                    <option value="C2">C2</option>
                                    <option value="C3">C3</option>
                                    <option value="C4">C4</option>
                                </select>
            </div>
                            <div class="form-group">
                                <label>Produktu skaits:</label>
                                <input type="number" class="form-control" placeholder="Ievadiet produktu skaitu">
                            </div>
                            <div class="form-group">
                                <label>Stāvoklis:</label>
                                <select class="form-control">
                                    <option value="normal">Normāls</option>
                                    <option value="low">Zems krājums</option>
                                    <option value="organize">Nepieciešama kārtošana</option>
                                    <option value="maintenance">Nepieciešama apkope</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Komentārs:</label>
                                <textarea class="form-control" rows="3" placeholder="Papildu informācija..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success" onclick="event.preventDefault(); alert('Dati saglabāti sistēmā!')">
                                <i class="fas fa-save"></i> Saglabāt
                            </button>
                        </form>
                    </div>
                </div>
            </div>




        </main>
    </div>

    <script src="script.js?v=8.0"></script>
    <script src="inventory.js?v=9.1"></script>
    <script src="shelf-organizer.js?v=7.0"></script>
    <script>
    function showAdminSection(section) {
        // Hide all sections
        document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
        
        // Show selected section
        document.getElementById('admin-' + section).classList.add('active');
        
        // Update navigation links
        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        event.currentTarget.classList.add('active');
        
        // Load data based on section
        if (section === 'inventory') {
            loadProducts();
        } else if (section === 'users') {
            loadUsers();
        } else if (section === 'reports') {
            loadOverviewStats();
        }
    }

    // Function to show the add user modal
    function showAddUserModal() {
        const modal = document.getElementById('addUserModal');
        modal.style.display = 'block';
    }

    // Function to close the user modal
    function closeUserModal() {
        const modal = document.getElementById('addUserModal');
        modal.style.display = 'none';
        document.getElementById('addUserForm').reset();
    }

    // Function to load users
    function loadUsers() {
        fetch('get_users.php')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('usersTableBody');
                tableBody.innerHTML = '';

                if (data.error) {
                    tableBody.innerHTML = `<tr><td colspan="6" class="text-center">Kļūda: ${data.error}</td></tr>`;
                    return;
                }

                data.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.first_name}</td>
                        <td>${user.last_name}</td>
                        <td>${user.email}</td>
                        <td>${user.role === 'admin' ? 'Administrators' : user.role === 'warehouse' ? 'Noliktavas darbinieks' : 'Plauktu kārtotājs'}</td>
                        <td><span class="status active">Aktīvs</span></td>
                        <td>
                            <button class="btn-icon edit" onclick="editUser(${user.id}, this)"><i class="fas fa-edit"></i></button>
                            <button class="btn-icon delete" onclick="deleteUser(${user.id})"><i class="fas fa-trash"></i></button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error loading users:', error);
                const tableBody = document.getElementById('usersTableBody');
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Kļūda ielādējot lietotājus</td></tr>';
            });
    }

    // Function to edit user
    function editUser(userId, button) {
        // Get user data from the table row
        const row = button.closest('tr');
        const cells = row.cells;
        
        // Create edit modal
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.style.display = 'block';
        modal.innerHTML = `
            <div class="modal-content">
                <span class="close" onclick="this.parentElement.parentElement.remove()">&times;</span>
                <h2>Rediģēt lietotāju</h2>
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" value="${userId}">
                    <div class="form-group">
                        <label for="editFirstName">Vārds:</label>
                        <input type="text" id="editFirstName" value="${cells[0].textContent}" required>
                    </div>
                    <div class="form-group">
                        <label for="editLastName">Uzvārds:</label>
                        <input type="text" id="editLastName" value="${cells[1].textContent}" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">E-pasts:</label>
                        <input type="email" id="editEmail" value="${cells[2].textContent}" required>
                    </div>
                    <div class="form-group">
                        <label for="editPassword">Jauna parole (atstājiet tukšu, lai nemainītu):</label>
                        <input type="password" id="editPassword">
                    </div>
                    <div class="form-group">
                        <label for="editRole">Loma:</label>
                        <select id="editRole" required>
                            <option value="admin" ${cells[3].textContent === 'Administrators' ? 'selected' : ''}>Administrators</option>
                            <option value="warehouse" ${cells[3].textContent === 'Noliktavas darbinieks' ? 'selected' : ''}>Noliktavas darbinieks</option>
                            <option value="shelf" ${cells[3].textContent === 'Plauktu kārtotājs' ? 'selected' : ''}>Plauktu kārtotājs</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Saglabāt</button>
                </form>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Add form submit handler
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                id: document.getElementById('editUserId').value,
                first_name: document.getElementById('editFirstName').value,
                last_name: document.getElementById('editLastName').value,
                email: document.getElementById('editEmail').value,
                password: document.getElementById('editPassword').value,
                role: document.getElementById('editRole').value
            };

            fetch('edit_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    modal.remove();
                    loadUsers();
                    alert('Lietotājs veiksmīgi atjaunināts!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Kļūda atjauninot lietotāju');
            });
        });

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    // Function to delete user
    function deleteUser(userId) {
        if (confirm('Vai tiešām vēlaties dzēst šo lietotāju?')) {
            fetch('delete_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    loadUsers();
                    alert('Lietotājs veiksmīgi dzēsts!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Kļūda dzēšot lietotāju');
            });
        }
    }

    // Add event listener for user form submission
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            first_name: document.getElementById('firstName').value,
            last_name: document.getElementById('lastName').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
            role: document.getElementById('role').value
        };

        fetch('add_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                closeUserModal();
                loadUsers();
                alert('Lietotājs veiksmīgi pievienots!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda pievienojot lietotāju');
        });
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('addUserModal');
        if (e.target === modal) {
            closeUserModal();
        }
    });

    // Add these new functions for product scanning
    let scannedProducts = new Map();

    function scanProduct() {
        const scanInput = document.getElementById('productScan');
        const productCode = scanInput.value.trim();
        
        if (!productCode) {
            alert('Lūdzu ievadiet produkta kodu');
            return;
        }

        // Simulate product lookup
        fetch(`get_product.php?code=${encodeURIComponent(productCode)}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                addProductToList(data);
                scanInput.value = '';
                scanInput.focus();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Kļūda meklējot produktu');
            });
    }

    function addProductToList(product) {
        const itemsList = document.getElementById('receivedItemsList');
        
        // Check if product already exists
        if (scannedProducts.has(product.id)) {
            const existingItem = scannedProducts.get(product.id);
            existingItem.quantity++;
            updateProductQuantity(product.id, existingItem.quantity);
            return;
        }

        // Create new item card
        const itemCard = document.createElement('div');
        itemCard.className = 'item-card';
        itemCard.id = `product-${product.id}`;
        itemCard.innerHTML = `
            <i class="fas fa-box item-icon"></i>
            <div class="item-details">
                <div class="item-name">${product.name}</div>
                <div class="item-info">
                    Kods: ${product.code} | Kategorija: ${product.category}
                </div>
            </div>
            <div class="quantity-control">
                <button onclick="updateQuantity(${product.id}, -1)">-</button>
                <input type="number" value="1" min="1" onchange="updateQuantity(${product.id}, this.value - ${scannedProducts.get(product.id)?.quantity || 1})">
                <button onclick="updateQuantity(${product.id}, 1)">+</button>
            </div>
            <div class="item-actions">
                <button class="btn btn-success btn-sm" onclick="confirmItem(${product.id})">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="removeItem(${product.id})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        itemsList.insertBefore(itemCard, itemsList.firstChild);
        
        // Store product in map
        scannedProducts.set(product.id, {
            product: product,
            quantity: 1,
            confirmed: false
        });
    }

    function updateQuantity(productId, change) {
        const item = scannedProducts.get(productId);
        if (!item) return;

        const newQuantity = item.quantity + parseInt(change);
        if (newQuantity < 1) return;

        item.quantity = newQuantity;
        updateProductQuantity(productId, newQuantity);
    }

    function updateProductQuantity(productId, quantity) {
        const itemCard = document.getElementById(`product-${productId}`);
        if (!itemCard) return;

        const input = itemCard.querySelector('input[type="number"]');
        input.value = quantity;
    }

    function confirmItem(productId) {
        const item = scannedProducts.get(productId);
        if (!item) return;

        item.confirmed = true;
        const itemCard = document.getElementById(`product-${productId}`);
        itemCard.classList.add('confirmed');
        
        // Disable quantity controls and confirm button
        const controls = itemCard.querySelector('.quantity-control');
        const confirmBtn = itemCard.querySelector('.btn-success');
        controls.style.opacity = '0.5';
        controls.style.pointerEvents = 'none';
        confirmBtn.disabled = true;
        confirmBtn.style.opacity = '0.5';
    }

    function removeItem(productId) {
        const itemCard = document.getElementById(`product-${productId}`);
        if (itemCard) {
            itemCard.remove();
        }
        scannedProducts.delete(productId);
    }

    function confirmAllItems() {
        if (scannedProducts.size === 0) {
            alert('Nav pievienotu produktu');
            return;
        }

        if (confirm('Vai vēlaties apstiprināt visas pieņemtās preces?')) {
            scannedProducts.forEach((item, productId) => {
                confirmItem(productId);
            });
        }
    }

    // Add keyboard event listener for scanning
    document.getElementById('productScan').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            scanProduct();
        }
    });

    // Add these new functions for random product generation
    const productCategories = [
        'Bulkīšu izstrādājumi',
        'Šķidrums',
        'Piena produkti',
        'Dārzeņi',
        'Augļi',
        'Sausie augļi un rieksti',
        'Saldumi'
    ];

    const productPrefixes = [
        'Premium', 'Classic', 'Organic', 'Fresh', 'Natural',
        'Deluxe', 'Basic', 'Special', 'Extra', 'Super'
    ];

    const productTypes = [
        'Milti', 'Ūdens', 'Piens', 'Kartupeļi', 'Rieksti',
        'Sviests', 'Siers', 'Jogurts', 'Āboli', 'Burkāni',
        'Banāni', 'Apelsīni', 'Mandeles', 'Rieksti', 'Šokolāde'
    ];

    function generateRandomProduct() {
        const prefix = productPrefixes[Math.floor(Math.random() * productPrefixes.length)];
        const type = productTypes[Math.floor(Math.random() * productTypes.length)];
        const category = productCategories[Math.floor(Math.random() * productCategories.length)];
        const code = `PRD${Math.floor(Math.random() * 10000).toString().padStart(4, '0')}`;
        
        return {
            id: Date.now() + Math.floor(Math.random() * 1000),
            code: code,
            name: `${prefix} ${type}`,
            category: category,
            barcode: Math.floor(Math.random() * 10000000000000).toString().padStart(13, '0')
        };
    }

    function generateRandomProducts() {
        const incomingList = document.getElementById('incomingProductsList');
        // Clear existing products
        incomingList.innerHTML = '';
        
        fetch('get_random_products.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(products => {
                // Products received successfully
                
                if (!Array.isArray(products) || products.length === 0) {
                    showNotification('Nav atrastas preces', 'warning');
                    return;
                }
                
                products.forEach(product => {
                    const productElement = document.createElement('div');
                    productElement.className = 'incoming-product new';
                    productElement.innerHTML = `
                        <div class="product-code">${product.code}</div>
                        <div class="product-name">${product.name}</div>
                        <div class="product-category">${product.category}</div>
                        <div class="scan-hint">
                            <i class="fas fa-barcode"></i> Skenēt
                        </div>
                    `;
                    
                    // Add click handler to simulate scanning
                    productElement.addEventListener('click', () => {
                        document.getElementById('productScan').value = product.code;
                        scanProduct();
                        productElement.remove();
                    });
                    
                    incomingList.appendChild(productElement);
                    
                    // Remove the 'new' class after animation
                    setTimeout(() => {
                        productElement.classList.remove('new');
                    }, 500);
                });
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                showNotification('Kļūda ielādējot preces: ' + error.message, 'error');
            });
    }

    // Initialize products only when warehouse receive section is shown
    function initializeWarehouseReceive() {
        const warehouseReceive = document.getElementById('warehouse-receive');
        if (warehouseReceive && warehouseReceive.classList.contains('active')) {
            // Generate products for warehouse receive section
            generateRandomProducts();
        }
    }
    
    // Function to edit a product - moved here to ensure global scope
    function editProduct(productId) {
        // Edit product functionality
        
        // Fetch product data first
        fetch(`get_product.php?id=${productId}`)
            .then(response => response.json())
            .then(product => {
                if (product.error) {
                    alert('Kļūda ielādējot produkta datus: ' + product.error);
                    return;
                }
                
                // Create edit modal
                const modal = document.createElement('div');
                modal.className = 'modal';
                modal.style.display = 'block';
                modal.innerHTML = `
                    <div class="modal-content">
                        <span class="close" onclick="this.parentElement.parentElement.remove()">&times;</span>
                        <h2>Rediģēt produktu</h2>
                        <form id="editProductForm">
                            <input type="hidden" id="editProductId" value="${product.id}">
                            <div class="form-group">
                                <label for="editProductCode">Produkta kods:</label>
                                <input type="text" id="editProductCode" value="${product.product_code || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="editProductName">Nosaukums:</label>
                                <input type="text" id="editProductName" value="${product.product_name || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="editCategory">Kategorija:</label>
                                <input type="text" id="editCategory" value="${product.category || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="editBarcode">Svītrkods:</label>
                                <input type="text" id="editBarcode" value="${product.barcode || ''}">
                            </div>
                            <div class="form-group">
                                <label for="editDescription">Apraksts:</label>
                                <textarea id="editDescription">${product.description || ''}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="editUnitPrice">Vienības cena (€):</label>
                                <input type="number" id="editUnitPrice" step="0.01" value="${product.unit_price || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="editMinStock">Min. krājums:</label>
                                <input type="number" id="editMinStock" value="${product.min_stock_level || ''}" required>
                            </div>
                            <button type="submit" class="btn btn-primary" onclick="submitProductForm(event)">Saglabāt</button>
                        </form>
                    </div>
                `;
                
                document.body.appendChild(modal);
                
                // Add form submit handler
                document.getElementById('editProductForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = {
                        id: document.getElementById('editProductId').value,
                        product_code: document.getElementById('editProductCode').value,
                        product_name: document.getElementById('editProductName').value,
                        category: document.getElementById('editCategory').value,
                        barcode: document.getElementById('editBarcode').value,
                        description: document.getElementById('editDescription').value,
                        unit_price: document.getElementById('editUnitPrice').value,
                        min_stock_level: document.getElementById('editMinStock').value
                    };
                    
                    fetch('edit_product.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Produkts veiksmīgi atjaunināts!');
                            modal.remove();
                            loadProducts(); // Reload the products table
        } else {
                            alert('Kļūda: ' + (data.error || 'Nezināma kļūda'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Kļūda saglabājot produktu');
                    });
                });
                
                // Close modal when clicking outside
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.remove();
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching product:', error);
                alert('Kļūda ielādējot produkta datus');
            });
    }

    // Inline form submission functions that will work regardless of other scripts
    function submitProductForm(event) {
        if (event) event.preventDefault();
        
        // Clear previous error styling
        clearFieldErrors();
        
        const formData = {
            product_code: document.getElementById('productCode').value.trim(),
            product_name: document.getElementById('productName').value.trim(),
            category: document.getElementById('productCategory').value,
            barcode: document.getElementById('barcode').value.trim(),
            description: document.getElementById('description').value.trim(),
            unit_price: document.getElementById('unitPrice').value,
            min_stock_level: document.getElementById('minStock').value
        };
        
        // Client-side validation
        const errors = validateFormData(formData);
        if (errors.length > 0) {
            showValidationErrors(errors);
            alert('Lūdzu izlabojiet atzīmētos laukus un mēģiniet vēlreiz');
            return;
        }
        
        const submitButton = event.target;
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Saglabā...';
        submitButton.disabled = true;
        
        fetch('add_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.text();
        })
        .then(text => {
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error('Servera atbilde nav derīgs JSON: ' + text.substring(0, 100));
            }
            if (data.success) {
                alert('Produkts veiksmīgi pievienots!');
                document.getElementById('addProductModal').style.display = 'none';
                document.getElementById('addProductForm').reset();
                clearFieldErrors();
                if (typeof loadProducts === 'function') {
                    loadProducts();
                }
            } else if (data.validation_errors && Array.isArray(data.validation_errors)) {
                showValidationErrors(data.validation_errors);
            } else {
                alert('Kļūda: ' + (data.error || 'Nezināma kļūda'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda pievienojot produktu: ' + error.message);
        })
        .finally(() => {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        });
    }
    
    function validateFormData(data) {
        const errors = [];
        
        // Helper function to check if value is empty
        const isEmpty = (value) => {
            return value === null || value === undefined || 
                   (typeof value === 'string' && value.trim() === '') ||
                   (typeof value === 'number' && isNaN(value));
        };
        
        // Valid categories list (must match server-side)
        const validCategories = [
            'Bulkīšu izstrādājumi', 'Šķidrums', 'Piena produkti', 
            'Dārzeņi', 'Augļi', 'Sausie augļi un rieksti', 'Saldumi'
        ];
        
        // Product code validation - ALWAYS check regardless of HTML attributes
        if (isEmpty(data.product_code)) {
            errors.push({field: 'productCode', message: 'Produkta kods ir obligāts un nedrīkst būt tukšs'});
        } else {
            const code = data.product_code.trim();
            if (code.length !== 6) {
                errors.push({field: 'productCode', message: 'Produkta kodam jābūt tieši 6 simboliem (PRD + 3 cipari)'});
            } else if (!/^PRD[0-9]{3}$/.test(code)) {
                errors.push({field: 'productCode', message: 'Produkta kods drīkst sākties tikai ar PRD + 3 cipari (piemēram: PRD001)'});
            }
        }
        
        // Product name validation - ALWAYS check
        if (isEmpty(data.product_name)) {
            errors.push({field: 'productName', message: 'Produkta nosaukums ir obligāts un nedrīkst būt tukšs'});
        } else {
            const name = data.product_name.trim();
            if (name.length < 2) {
                errors.push({field: 'productName', message: 'Produkta nosaukumam jābūt vismaz 2 simboliem'});
            } else if (name.length > 50) {
                errors.push({field: 'productName', message: 'Produkta nosaukums nedrīkst pārsniegt 50 simbolus'});
            }
        }
        
        // Category validation - ALWAYS check
        if (isEmpty(data.category) || data.category === '-- Izvēlieties kategoriju --') {
            errors.push({field: 'productCategory', message: 'Kategorija ir obligāta - lūdzu izvēlieties kategoriju'});
        } else if (!validCategories.includes(data.category)) {
            errors.push({field: 'productCategory', message: 'Izvēlētā kategorija nav derīga'});
        }
        
        // Unit price validation - ALWAYS check
        if (isEmpty(data.unit_price)) {
            errors.push({field: 'unitPrice', message: 'Vienības cena ir obligāta un nedrīkst būt tukša'});
        } else {
            const price = parseFloat(data.unit_price);
            if (isNaN(price)) {
                errors.push({field: 'unitPrice', message: 'Vienības cenai jābūt derīgam skaitlim'});
            } else if (price < 0) {
                errors.push({field: 'unitPrice', message: 'Vienības cena nedrīkst būt negatīva'});
            } else if (price > 999999.99) {
                errors.push({field: 'unitPrice', message: 'Vienības cena nedrīkst pārsniegt 999999.99 EUR'});
            }
        }
        
        // Min stock validation - ALWAYS check
        if (isEmpty(data.min_stock_level)) {
            errors.push({field: 'minStock', message: 'Minimālais krājums ir obligāts un nedrīkst būt tukšs'});
        } else {
            const stock = parseInt(data.min_stock_level);
            if (isNaN(stock)) {
                errors.push({field: 'minStock', message: 'Minimālajam krājumam jābūt derīgam skaitlim'});
            } else if (stock < 0) {
                errors.push({field: 'minStock', message: 'Minimālais krājums nedrīkst būt negatīvs'});
            } else if (parseFloat(data.min_stock_level) !== stock) {
                errors.push({field: 'minStock', message: 'Minimālajam krājumam jābūt veselam skaitlim'});
            } else if (stock > 999999) {
                errors.push({field: 'minStock', message: 'Minimālais krājums nedrīkst pārsniegt 999999'});
            }
        }
        
        // Barcode validation (optional but if provided must be valid)
        if (data.barcode && data.barcode.trim() !== '') {
            const barcode = data.barcode.trim();
            if (!/^[0-9]{13}$/.test(barcode)) {
                errors.push({field: 'barcode', message: 'Svītrkods drīkst saturēt tikai 13 ciparus (EAN-13 formāts)'});
            }
        }
        
        // Description validation (optional but if provided must be valid)
        if (data.description && data.description.trim() !== '') {
            const desc = data.description.trim();
            if (desc.length > 200) {
                errors.push({field: 'description', message: 'Apraksts nedrīkst pārsniegt 200 simbolus'});
            }
        }
        
        return errors;
    }
    
    function showValidationErrors(errors) {
        clearFieldErrors();
        
        let hasErrors = false;
        
        errors.forEach(error => {
            if (typeof error === 'string') {
                // General error - show as alert
                alert(error);
            } else if (error.field && error.message) {
                // Show error under specific field
                const field = document.getElementById(error.field);
                const errorDiv = document.getElementById(error.field + '-error');
                const formGroup = field ? field.closest('.form-group') : null;
                
                if (field && errorDiv && formGroup) {
                    formGroup.classList.add('has-error');
                    errorDiv.textContent = error.message;
                    hasErrors = true;
                }
            }
        });
        
        if (hasErrors) {
            // Scroll to first error
            const firstError = document.querySelector('.form-group.has-error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }
     
    function clearFieldErrors() {
        const fields = ['productCode', 'productName', 'productCategory', 'barcode', 'description', 'unitPrice', 'minStock'];
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '-error');
            const formGroup = field ? field.closest('.form-group') : null;
            
            if (field && formGroup) {
                formGroup.classList.remove('has-error', 'has-success');
                field.style.border = '';
                field.style.backgroundColor = '';
            }
            if (errorDiv) {
                errorDiv.textContent = '';
            }
        });
    }
     
    function validateFieldRealTime(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '-error');
        const formGroup = field ? field.closest('.form-group') : null;
        
        if (!field || !errorDiv || !formGroup) return;
        
        const value = field.value.trim();
        let error = null;
        
        switch(fieldId) {
            case 'productCode':
                if (!value) {
                    error = 'Produkta kods ir obligāts';
                } else if (value.length !== 6) {
                    error = 'Produkta kodam jābūt tieši 6 simboliem (PRD + 3 cipari)';
                } else if (!/^PRD[0-9]{3}$/.test(value)) {
                    error = 'Produkta kods drīkst sākties tikai ar PRD + 3 cipari (piemēram: PRD001)';
                }
                break;
                
            case 'productName':
                if (!value) {
                    error = 'Produkta nosaukums ir obligāts';
                } else if (value.length < 2 || value.length > 50) {
                    error = 'Produkta nosaukumam jābūt no 2 līdz 50 simboliem';
                }
                break;
                
            case 'productCategory':
                if (!value) {
                    error = 'Kategorija ir obligāta';
                }
                break;
                
            case 'barcode':
                if (value && !/^[0-9]{13}$/.test(value)) {
                    error = 'Svītrkods drīkst saturēt tikai 13 ciparus (EAN-13 formāts)';
                }
                break;
                
            case 'description':
                if (value && value.length > 200) {
                    error = 'Apraksts nedrīkst pārsniegt 200 simbolus';
                }
                break;
                
            case 'unitPrice':
                if (!value) {
                    error = 'Vienības cena ir obligāta';
                } else if (isNaN(value) || parseFloat(value) < 0) {
                    error = 'Vienības cenai jābūt pozitīvam skaitlim';
                } else if (parseFloat(value) > 999999.99) {
                    error = 'Vienības cena nedrīkst pārsniegt 999999.99 EUR';
                }
                break;
                
            case 'minStock':
                if (!value) {
                    error = 'Minimālais krājums ir obligāts';
                } else if (isNaN(value) || parseInt(value) < 0) {
                    error = 'Minimālajam krājumam jābūt pozitīvam skaitlim';
                } else if (parseInt(value) > 999999) {
                    error = 'Minimālais krājums nedrīkst pārsniegt 999999';
                }
                break;
        }
        
        if (error) {
            formGroup.classList.add('has-error');
            formGroup.classList.remove('has-success');
            errorDiv.textContent = error;
        } else if (value || fieldId === 'productCode' || fieldId === 'productName' || fieldId === 'productCategory' || fieldId === 'unitPrice' || fieldId === 'minStock') {
            formGroup.classList.add('has-success');
            formGroup.classList.remove('has-error');
            errorDiv.textContent = '';
        }
    }
     
    // Add real-time validation to form fields
    document.addEventListener('DOMContentLoaded', function() {
        const fields = ['productCode', 'productName', 'productCategory', 'barcode', 'description', 'unitPrice', 'minStock'];
        
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('blur', () => validateFieldRealTime(fieldId));
                field.addEventListener('input', () => {
                    // Clear error immediately when user starts typing
                    const errorDiv = document.getElementById(fieldId + '-error');
                    const formGroup = field.closest('.form-group');
                    if (formGroup && formGroup.classList.contains('has-error')) {
                        setTimeout(() => validateFieldRealTime(fieldId), 300);
                    }
                });
            }
        });
        
        // Special handling for product code - auto uppercase and PRD prefix
        const productCodeField = document.getElementById('productCode');
        if (productCodeField) {
            productCodeField.addEventListener('input', function(e) {
                let value = e.target.value.toUpperCase();
                
                // If user starts typing numbers, auto-add PRD prefix
                if (/^[0-9]/.test(value)) {
                    value = 'PRD' + value;
                }
                
                // Ensure it starts with PRD
                if (value && !value.startsWith('PRD')) {
                    // If user types something else, try to fix it
                    if (/^[A-Z]{1,3}[0-9]*$/.test(value)) {
                        // Replace first letters with PRD
                        value = 'PRD' + value.replace(/^[A-Z]+/, '');
                    }
                }
                
                // Limit to 6 characters total
                if (value.length > 6) {
                    value = value.slice(0, 6);
                }
                
                // Ensure PRD + only numbers after
                if (value.length > 3) {
                    const prdPart = value.slice(0, 3);
                    const numberPart = value.slice(3).replace(/[^0-9]/g, '');
                    value = prdPart + numberPart;
                }
                
                e.target.value = value;
            });
            
            // Auto-focus to end and add PRD if empty on focus
            productCodeField.addEventListener('focus', function(e) {
                if (!e.target.value) {
                    e.target.value = 'PRD';
                    setTimeout(() => {
                        e.target.setSelectionRange(3, 3);
                    }, 0);
                }
            });
        }
        
        // Special handling for barcode - only numbers
        const barcodeField = document.getElementById('barcode');
        if (barcodeField) {
            barcodeField.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                if (e.target.value.length > 13) {
                    e.target.value = e.target.value.slice(0, 13);
                }
            });
        }
    });

    // Comprehensive user validation function (Gmail-level)
    function validateUserData(data) {
        const errors = [];
        
        // Helper function to check if value is empty
        const isEmpty = (value) => {
            return value === null || value === undefined || 
                   (typeof value === 'string' && value.trim() === '') ||
                   (typeof value === 'number' && isNaN(value));
        };
        
        // First name validation
        if (isEmpty(data.first_name)) {
            errors.push({field: 'firstName', message: 'Vārds ir obligāts un nedrīkst būt tukšs'});
        } else {
            const name = data.first_name.trim();
            if (name.length < 2) {
                errors.push({field: 'firstName', message: 'Vārdam jābūt vismaz 2 simboliem'});
            } else if (name.length > 50) {
                errors.push({field: 'firstName', message: 'Vārds nedrīkst pārsniegt 50 simbolus'});
            } else if (!/^[\p{L}\s\-']+$/u.test(name)) {
                errors.push({field: 'firstName', message: 'Vārdā drīkst būt tikai burti, atstarpes un defises'});
            } else if (/^\s|\s$/.test(name)) {
                errors.push({field: 'firstName', message: 'Vārds nedrīkst sākties vai beigties ar atstarpi'});
            }
        }
        
        // Last name validation
        if (isEmpty(data.last_name)) {
            errors.push({field: 'lastName', message: 'Uzvārds ir obligāts un nedrīkst būt tukšs'});
        } else {
            const lastName = data.last_name.trim();
            if (lastName.length < 2) {
                errors.push({field: 'lastName', message: 'Uzvārdam jābūt vismaz 2 simboliem'});
            } else if (lastName.length > 50) {
                errors.push({field: 'lastName', message: 'Uzvārds nedrīkst pārsniegt 50 simbolus'});
            } else if (!/^[\p{L}\s\-']+$/u.test(lastName)) {
                errors.push({field: 'lastName', message: 'Uzvārdā drīkst būt tikai burti, atstarpes un defises'});
            } else if (/^\s|\s$/.test(lastName)) {
                errors.push({field: 'lastName', message: 'Uzvārds nedrīkst sākties vai beigties ar atstarpi'});
            }
        }
        
        // Email validation (Gmail-level)
        if (isEmpty(data.email)) {
            errors.push({field: 'email', message: 'E-pasta adrese ir obligāta'});
        } else {
            const email = data.email.trim().toLowerCase();
            // RFC 5322 compliant email regex
            const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
            
            if (email.length > 254) {
                errors.push({field: 'email', message: 'E-pasta adrese pārāk gara (maksimums 254 simboli)'});
            } else if (!emailRegex.test(email)) {
                errors.push({field: 'email', message: 'Nederīgs e-pasta formāts'});
            } else if (email.includes('..')) {
                errors.push({field: 'email', message: 'E-pasta adresē nedrīkst būt divi punkti pēc kārtas'});
            } else if (email.startsWith('.') || email.includes('@.') || email.includes('.@')) {
                errors.push({field: 'email', message: 'Nederīgs e-pasta formāts'});
            } else {
                // Check for suspicious patterns
                const localPart = email.split('@')[0];
                if (localPart.length > 64) {
                    errors.push({field: 'email', message: 'E-pasta vārds pārāk garš (maksimums 64 simboli)'});
                }
            }
        }
        
        // Password validation (secure)
        if (isEmpty(data.password)) {
            errors.push({field: 'password', message: 'Parole ir obligāta'});
        } else {
            const password = data.password;
            if (password.length < 8) {
                errors.push({field: 'password', message: 'Parolei jābūt vismaz 8 simboliem'});
            } else if (password.length > 128) {
                errors.push({field: 'password', message: 'Parole pārāk gara (maksimums 128 simboli)'});
            } else {
                let strengthErrors = [];
                if (!/[a-z]/.test(password)) {
                    strengthErrors.push('mazo burtu');
                }
                if (!/[A-Z]/.test(password)) {
                    strengthErrors.push('lielo burtu');
                }
                if (!/[0-9]/.test(password)) {
                    strengthErrors.push('ciparu');
                }
                if (!/[^a-zA-Z0-9]/.test(password)) {
                    strengthErrors.push('speciālo simbolu');
                }
                
                if (strengthErrors.length > 0) {
                    errors.push({field: 'password', message: `Parolei jāsatur: ${strengthErrors.join(', ')}`});
                }
                
                // Check for common weak passwords
                const commonPasswords = ['password', '123456', 'qwerty', 'admin', 'letmein'];
                if (commonPasswords.includes(password.toLowerCase())) {
                    errors.push({field: 'password', message: 'Parole pārāk vienkārša - izvēlieties sarežģītāku'});
                }
            }
        }
        
        // Role validation
        const validRoles = ['admin', 'warehouse', 'shelf'];
        if (isEmpty(data.role)) {
            errors.push({field: 'role', message: 'Loma ir obligāta'});
        } else if (!validRoles.includes(data.role)) {
            errors.push({field: 'role', message: 'Izvēlētā loma nav derīga'});
        }
        
        return errors;
    }
    
    // User validation error display functions
    function clearUserFieldErrors() {
        const errorFields = ['firstName', 'lastName', 'email', 'password', 'role'];
        errorFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '-error');
            const formGroup = field ? field.closest('.form-group') : null;
            
            if (formGroup) {
                formGroup.classList.remove('has-error', 'has-success');
            }
            if (errorDiv) {
                errorDiv.textContent = '';
            }
        });
    }
    
    function showUserValidationErrors(errors) {
        clearUserFieldErrors();
        
        let hasErrors = false;
        
        errors.forEach(error => {
            if (error.field && error.message) {
                const field = document.getElementById(error.field);
                const errorDiv = document.getElementById(error.field + '-error');
                const formGroup = field ? field.closest('.form-group') : null;
                
                if (field && errorDiv && formGroup) {
                    formGroup.classList.add('has-error');
                    errorDiv.textContent = error.message;
                    hasErrors = true;
                }
            }
        });
        
        // Scroll to first error
        if (hasErrors) {
            const firstErrorField = document.querySelector('.form-group.has-error');
            if (firstErrorField) {
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }

    function submitUserForm(event) {
        if (event) event.preventDefault();
        
        // Clear previous errors
        clearUserFieldErrors();
        
        const formData = {
            first_name: document.getElementById('firstName').value.trim(),
            last_name: document.getElementById('lastName').value.trim(),
            email: document.getElementById('email').value.trim(),
            password: document.getElementById('password').value,
            role: document.getElementById('role').value
        };
        
        // Client-side validation
        const errors = validateUserData(formData);
        if (errors.length > 0) {
            showUserValidationErrors(errors);
            alert('Lūdzu izlabojiet atzīmētos laukus un mēģiniet vēlreiz');
            return;
        }
        
        const submitButton = event.target;
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Saglabā...';
        submitButton.disabled = true;
        
        fetch('add_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert('Lietotājs veiksmīgi pievienots!');
                document.getElementById('addUserModal').style.display = 'none';
                document.getElementById('addUserForm').reset();
                if (typeof loadUsers === 'function') {
                    loadUsers();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda pievienojot lietotāju');
        })
        .finally(() => {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        });
    }
    </script>

    <style>
        /* Add these styles to your existing CSS */
        .incoming-products {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .incoming-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .incoming-product {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .incoming-product:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .incoming-product::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #667eea;
        }

        .incoming-product .product-code {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .incoming-product .product-name {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .incoming-product .product-category {
            font-size: 0.8rem;
            color: #888;
        }

        .incoming-product .scan-hint {
            position: absolute;
            bottom: 0.5rem;
            right: 0.5rem;
            color: #667eea;
            font-size: 0.8rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .incoming-product.new {
            animation: fadeIn 0.5s ease-out;
        }

        /* Reports Styles */
        .reports-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .reports-actions {
            display: flex;
            gap: 1rem;
        }

        .reports-stats {
            margin-bottom: 2rem;
        }

        .stat-card.alert {
            background: linear-gradient(135deg, #f5576c, #f093fb);
        }

        .simple-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .simple-section h3 {
            margin: 0 0 2rem 0;
            color: #333;
            font-size: 1.2rem;
        }

        .category-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 300px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
        }

        .placeholder-icon {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .category-placeholder p {
            color: #6c757d;
            margin: 0;
            font-style: italic;
        }

        .report-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .report-card.full-width {
            grid-column: 1 / -1;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .report-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.1rem;
        }

        .report-actions {
            display: flex;
            gap: 0.5rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #666;
        }

        .action-btn:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
        }

        .action-btn i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .action-btn span {
            font-size: 0.9rem;
            text-align: center;
        }

        .filter-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-control {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Error message styling */
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            min-height: 1.2rem;
            display: block;
        }

        .form-group.has-error input,
        .form-group.has-error select,
        .form-group.has-error textarea {
            border: 2px solid #dc3545;
            background-color: #fff5f5;
        }

        .form-group.has-error label {
            color: #dc3545;
        }

        .form-group.has-success input,
        .form-group.has-success select,
        .form-group.has-success textarea {
            border: 2px solid #28a745;
            background-color: #f8fff9;
        }

        .validation-summary {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        .validation-summary h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
        }

        .validation-summary ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        .validation-summary li {
            margin-bottom: 0.25rem;
        }

                 @media (max-width: 768px) {
             .reports-grid {
                 grid-template-columns: 1fr;
             }
             
             .quick-actions {
                 grid-template-columns: 1fr;
             }
             
             .reports-header {
                 flex-direction: column;
                 gap: 1rem;
                 align-items: stretch;
             }
         }

         /* Print Styles */
         @media print {
             .sidebar, .header-right, .reports-actions {
                 display: none !important;
             }
             
             .main-content {
                 margin-left: 0 !important;
                 padding: 20px !important;
             }
             
             .reports-header h2 {
                 text-align: center;
                 margin-bottom: 30px;
             }
             
             .stats-grid {
                 grid-template-columns: repeat(4, 1fr) !important;
                 gap: 15px !important;
             }
             
             .stat-card {
                 break-inside: avoid;
                 box-shadow: none !important;
                 border: 1px solid #ddd;
             }
        }
    </style>
</body>
</html> 