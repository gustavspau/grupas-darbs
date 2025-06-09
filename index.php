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
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Pievienot lietotāju
                    </button>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Vārds</th>
                                <th>E-pasts</th>
                                <th>Loma</th>
                                <th>Statuss</th>
                                <th>Darbības</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Jānis Bērziņš</td>
                                <td>janis.berzins@email.com</td>
                                <td>Noliktavas darbinieks</td>
                                <td><span class="status active">Aktīvs</span></td>
                                <td>
                                    <button class="btn-icon edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn-icon delete"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>Anna Ozoliņa</td>
                                <td>anna.ozolina@email.com</td>
                                <td>Plauktu kartotājs</td>
                                <td><span class="status active">Aktīvs</span></td>
                                <td>
                                    <button class="btn-icon edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn-icon delete"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Other admin sections -->
            <div id="admin-inventory" class="content-section">
                <h2>Inventāra pārvaldība</h2>
                <p>Šeit tiks attēlota inventāra pārvaldības saskarnes...</p>
            </div>

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
                <div class="receive-form">
                    <div class="form-group">
                        <label>Skenēt svītrkodu</label>
                        <input type="text" placeholder="Skenējiet vai ievadiet svītrkodu">
                    </div>
                    <div class="received-items">
                        <h3>Pieņemtās preces</h3>
                        <!-- Items will be listed here -->
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
</body>
</html> 