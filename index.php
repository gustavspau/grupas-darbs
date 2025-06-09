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
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="inventory.js"></script>
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
            <ul class="nav-menu">
                <li><a href="#" onclick="showAdminSection('dashboard')" class="nav-link active">
                    <i class="fas fa-chart-dashboard"></i>
                    Pārskats
                </a></li>
                <li><a href="#" onclick="showAdminSection('users')" class="nav-link">
                    <i class="fas fa-users"></i>
                    Lietotāji
                </a></li>
                <li><a href="#" onclick="showAdminSection('inventory')" class="nav-link">
                    <i class="fas fa-boxes"></i>
                    Inventārs
                </a></li>
                <li><a href="#" onclick="showAdminSection('reports')" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    Atskaites
                </a></li>
                <li><a href="#" onclick="showAdminSection('settings')" class="nav-link">
                    <i class="fas fa-cog"></i>
                    Iestatījumi
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
                <h2>Lietotāju pārvaldība</h2>
                <div class="section-header">
                    <button class="btn btn-primary" onclick="showAddUserModal()">
                        <i class="fas fa-plus"></i>
                        Pievienot lietotāju
                    </button>
                </div>
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
                            <label for="firstName">Vārds:</label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Uzvārds:</label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                        <div class="form-group">
                            <label for="email">E-pasts:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Parole:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Loma:</label>
                            <select id="role" name="role" required>
                                <option value="admin">Administrators</option>
                                <option value="warehouse">Noliktavas darbinieks</option>
                                <option value="shelf">Plauktu kārtotājs</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Saglabāt</button>
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
                    <span class="close">&times;</span>
                    <h2>Pievienot jaunu produktu</h2>
                    <form id="addProductForm">
                        <div class="form-group">
                            <label for="productCode">Produkta kods:</label>
                            <input type="text" id="productCode" name="productCode" required>
                        </div>
                        <div class="form-group">
                            <label for="productName">Nosaukums:</label>
                            <input type="text" id="productName" name="productName" required>
                        </div>
                        <div class="form-group">
                            <label for="productCategory">Kategorija:</label>
                            <select id="productCategory" name="productCategory" required>
                                <option value="Bulkīšu izstrādājumi">Bulkīšu izstrādājumi</option>
                                <option value="Šķidrums">Šķidrums</option>
                                <option value="Piena produkti">Piena produkti</option>
                                <option value="Dārzeņi">Dārzeņi</option>
                                <option value="Augļi">Augļi</option>
                                <option value="Sausie augļi un rieksti">Sausie augļi un rieksti</option>
                                <option value="Saldumi">Saldumi</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="barcode">Svītrkods:</label>
                            <input type="text" id="barcode" name="barcode" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Apraksts:</label>
                            <textarea id="description" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="unitPrice">Vienības cena (€):</label>
                            <input type="number" id="unitPrice" name="unitPrice" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="minStock">Min. krājums:</label>
                            <input type="number" id="minStock" name="minStock" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Saglabāt</button>
                    </form>
                </div>
            </div>

            <!-- Other admin sections -->
            <div id="admin-reports" class="content-section">
                <h2>Atskaites</h2>
                <p>Šeit tiks attēlotas dažādas atskaites...</p>
            </div>

            <div id="admin-settings" class="content-section">
                <h2>Sistēmas iestatījumi</h2>
                <p>Šeit tiks attēloti sistēmas iestatījumi...</p>
            </div>
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
                <p>Šeit tiks attēlota inventāra uzskaites saskarnes...</p>
            </div>

            <div id="warehouse-tasks" class="content-section">
                <h2>Mani uzdevumi</h2>
                <p>Šeit tiks attēloti darbinieka uzdevumi...</p>
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
                    <i class="fas fa-chart-dashboard"></i>
                    Pārskats
                </a></li>
                <li><a href="#" onclick="showShelfSection('organize')" class="nav-link">
                    <i class="fas fa-sort"></i>
                    Plauktu organizēšana
                </a></li>
                <li><a href="#" onclick="showShelfSection('restock')" class="nav-link">
                    <i class="fas fa-plus-square"></i>
                    Papildināšana
                </a></li>
                <li><a href="#" onclick="showShelfSection('check')" class="nav-link">
                    <i class="fas fa-clipboard-check"></i>
                    Plauktu pārbaude
                </a></li>
                <li><a href="#" onclick="showShelfSection('maintenance')" class="nav-link">
                    <i class="fas fa-tools"></i>
                    Apkope
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

            <div id="shelf-organize" class="content-section">
                <h2>Plauktu organizēšana</h2>
                <p>Šeit tiks attēlota plauktu organizēšanas saskarnes...</p>
            </div>

            <div id="shelf-restock" class="content-section">
                <h2>Papildināšana</h2>
                <p>Šeit tiks attēlota papildināšanas saskarnes...</p>
            </div>

            <div id="shelf-check" class="content-section">
                <h2>Plauktu pārbaude</h2>
                <p>Šeit tiks attēlota plauktu pārbaudes saskarnes...</p>
            </div>

            <div id="shelf-maintenance" class="content-section">
                <h2>Apkope</h2>
                <p>Šeit tiks attēlota apkopes saskarnes...</p>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
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
                console.log('Received products:', products); // Debug log
                
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

    // Initialize with some products
    document.addEventListener('DOMContentLoaded', function() {
        const warehouseReceive = document.getElementById('warehouse-receive');
        if (warehouseReceive) {
            console.log('Warehouse receive section found, generating products...'); // Debug log
            generateRandomProducts();
        } else {
            console.log('Warehouse receive section not found'); // Debug log
        }
    });
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
    </style>
</body>
</html> 