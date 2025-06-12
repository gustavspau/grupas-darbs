<?php
require_once 'config.php';
require_once 'auth.php';

// Ensure user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Function to get user settings
function getUserSettings($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM settings WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $settings = [];
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

// Function to update user settings
function updateUserSetting($userId, $key, $value) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO settings (user_id, setting_key, setting_value) 
                           VALUES (?, ?, ?) 
                           ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->bind_param("isss", $userId, $key, $value, $value);
    return $stmt->execute();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'get_settings':
                $settings = getUserSettings($_SESSION['user_id']);
                $response = ['success' => true, 'settings' => $settings];
                break;
                
            case 'update_setting':
                if (isset($_POST['key']) && isset($_POST['value'])) {
                    $success = updateUserSetting($_SESSION['user_id'], $_POST['key'], $_POST['value']);
                    $response = [
                        'success' => $success,
                        'message' => $success ? 'Setting updated successfully' : 'Failed to update setting'
                    ];
                }
                break;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Warehouse Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>User Settings</h1>
        
        <div class="settings-container">
            <div class="setting-group">
                <h2>Display Settings</h2>
                <div class="setting-item">
                    <label for="theme">Theme:</label>
                    <select id="theme" onchange="updateSetting('theme', this.value)">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                    </select>
                </div>
                
                <div class="setting-item">
                    <label for="itemsPerPage">Items per page:</label>
                    <select id="itemsPerPage" onchange="updateSetting('items_per_page', this.value)">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            
            <div class="setting-group">
                <h2>Notification Settings</h2>
                <div class="setting-item">
                    <label for="emailNotifications">Email Notifications:</label>
                    <input type="checkbox" id="emailNotifications" onchange="updateSetting('email_notifications', this.checked)">
                </div>
                
                <div class="setting-item">
                    <label for="lowStockAlert">Low Stock Alerts:</label>
                    <input type="checkbox" id="lowStockAlert" onchange="updateSetting('low_stock_alert', this.checked)">
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load settings when page loads
        document.addEventListener('DOMContentLoaded', loadSettings);

        function loadSettings() {
            fetch('settings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_settings'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Apply settings to form elements
                    if (data.settings.theme) {
                        document.getElementById('theme').value = data.settings.theme;
                    }
                    if (data.settings.items_per_page) {
                        document.getElementById('itemsPerPage').value = data.settings.items_per_page;
                    }
                    if (data.settings.email_notifications) {
                        document.getElementById('emailNotifications').checked = data.settings.email_notifications === '1';
                    }
                    if (data.settings.low_stock_alert) {
                        document.getElementById('lowStockAlert').checked = data.settings.low_stock_alert === '1';
                    }
                }
            })
            .catch(error => console.error('Error loading settings:', error));
        }

        function updateSetting(key, value) {
            fetch('settings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_setting&key=${key}&value=${value}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('Setting updated successfully');
                } else {
                    alert('Failed to update setting');
                }
            })
            .catch(error => console.error('Error updating setting:', error));
        }
    </script>
</body>
</html> 