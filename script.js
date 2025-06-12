// Global variables (set from PHP)
let currentUser = userName;
let currentRole = userRole;

// DOM Elements
const screens = document.querySelectorAll('.screen');

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    // Show appropriate dashboard based on PHP session
    showDashboard(currentRole);
    
    // Update user display
    updateUserDisplay(currentUser, currentRole);
    
    // Add keyboard navigation
    document.addEventListener('keydown', handleKeyboardNavigation);
    
    // Initialize specific features
    initializeShelfMap();
    initializeWarehouseOperations();
    startRealTimeUpdates();
}

// Login is now handled by PHP - this function is not needed

function showDashboard(role) {
    // Hide all screens
    screens.forEach(screen => screen.classList.remove('active'));
    
    // Show appropriate screen
    switch(role) {
        case 'admin':
            document.getElementById('admin-screen').classList.add('active');
            break;
        case 'warehouse':
            document.getElementById('warehouse-screen').classList.add('active');
            break;
        case 'shelf':
            document.getElementById('shelf-screen').classList.add('active');
            break;
        default:
            // Fallback to admin if role not recognized
            document.getElementById('admin-screen').classList.add('active');
    }
}

function updateUserDisplay(username, role) {
    const userElements = document.querySelectorAll('[id$="-username"]');
    userElements.forEach(element => {
        if (element.id.includes(role) || element.id === 'admin-username') {
            element.textContent = username;
        }
    });
}

// Logout is now handled by PHP - redirect to logout.php

// Admin Dashboard Functions
function showAdminSection(sectionName) {
    // Hide all admin sections
    document.querySelectorAll('#admin-screen .content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(`admin-${sectionName}`).classList.add('active');
    
    // Update navigation
    updateNavigation('#admin-screen', sectionName);
}

// Warehouse Dashboard Functions
function showWarehouseSection(sectionName) {
    // Hide all warehouse sections
    document.querySelectorAll('#warehouse-screen .content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(`warehouse-${sectionName}`).classList.add('active');
    
    // Update navigation
    updateNavigation('#warehouse-screen', sectionName);
    
    // Initialize warehouse receive section if needed
    if (sectionName === 'receive' && typeof initializeWarehouseReceive === 'function') {
        initializeWarehouseReceive();
    }
}

// Shelf Dashboard Functions
function showShelfSection(sectionName) {
    // Hide all shelf sections
    document.querySelectorAll('#shelf-screen .content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(`shelf-${sectionName}`).classList.add('active');
    
    // Update navigation
    updateNavigation('#shelf-screen', sectionName);
}

function updateNavigation(screenSelector, activeSectionName) {
    // Remove active class from all nav links in current screen
    document.querySelectorAll(`${screenSelector} .nav-link`).forEach(link => {
        link.classList.remove('active');
    });
    
    // Add active class to current nav link
    const activeLink = document.querySelector(`${screenSelector} .nav-link[onclick*="${activeSectionName}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
}

// Keyboard Navigation
function handleKeyboardNavigation(e) {
    // ESC key to logout
    if (e.key === 'Escape' && currentUser) {
        if (confirm('Vai tiešām vēlaties izlogoties?')) {
            window.location.href = 'logout.php';
        }
    }
    
    // Enter key in login form
    if (e.key === 'Enter' && document.getElementById('login-screen').classList.contains('active')) {
        const loginBtn = document.querySelector('.login-btn');
        if (loginBtn) {
            loginBtn.click();
        }
    }
}

// Shelf Management Functions
function initializeShelfMap() {
    const shelves = document.querySelectorAll('.shelf');
    shelves.forEach(shelf => {
        shelf.addEventListener('click', function() {
            const shelfId = this.dataset.shelf;
            showShelfDetails(shelfId);
        });
    });
}

function showShelfDetails(shelfId) {
    // This would show detailed information about the selected shelf
    alert(`Plaukta ${shelfId} detalizēta informācija`);
}

// Warehouse Operations
function initializeWarehouseOperations() {
    // Initialize barcode scanning simulation
    const barcodeInputs = document.querySelectorAll('input[placeholder*="svītrkod"]');
    barcodeInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                simulateBarcodeScan(this.value);
                this.value = '';
            }
        });
    });
}

function simulateBarcodeScan(barcode) {
    if (barcode) {
        // Simulate product lookup
        const mockProduct = {
            name: `Produkts ${barcode}`,
            quantity: Math.floor(Math.random() * 100) + 1,
            location: `Sekcija ${String.fromCharCode(65 + Math.floor(Math.random() * 3))}, Plaukts ${Math.floor(Math.random() * 20) + 1}`
        };
        
        addScannedItem(mockProduct);
    }
}

function addScannedItem(product) {
    const receivedItems = document.querySelector('.received-items');
    if (receivedItems) {
        const itemElement = document.createElement('div');
        itemElement.className = 'activity-item';
        itemElement.innerHTML = `
            <i class="fas fa-box"></i>
            <div class="activity-details">
                <p><strong>${product.name}</strong> - Daudzums: ${product.quantity}</p>
                <span class="location">${product.location}</span>
            </div>
            <button class="btn btn-sm" onclick="confirmReceive(this)">Apstiprināt</button>
        `;
        receivedItems.appendChild(itemElement);
    }
}

function confirmReceive(button) {
    const item = button.closest('.activity-item');
    item.style.backgroundColor = '#d4edda';
    button.textContent = 'Apstiprināts';
    button.disabled = true;
    
    // Update statistics
    updateStats('received');
}

// Statistics Update
function updateStats(action) {
    // This would update the dashboard statistics
    console.log(`Statistika atjaunināta: ${action}`);
}

// Real-time Updates Simulation
function startRealTimeUpdates() {
    setInterval(() => {
        // Simulate random updates to statistics
        if (currentRole === 'admin') {
            updateRandomStats();
        }
    }, 30000); // Update every 30 seconds
}

function updateRandomStats() {
    const statCards = document.querySelectorAll('.stat-card h3');
    statCards.forEach(stat => {
        const currentValue = parseInt(stat.textContent.replace(/[€,]/g, ''));
        if (!isNaN(currentValue)) {
            const change = Math.floor(Math.random() * 10) - 5; // Random change between -5 and +5
            const newValue = Math.max(0, currentValue + change);
            
            if (stat.textContent.includes('€')) {
                stat.textContent = `€${newValue.toLocaleString()}`;
            } else {
                stat.textContent = newValue.toString();
            }
        }
    });
}

// Mobile responsiveness
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('open');
    }
}

// Initialize additional features when page loads
window.addEventListener('load', function() {
    initializeShelfMap();
    initializeWarehouseOperations();
    startRealTimeUpdates();
    
    // Add mobile menu button if needed
    if (window.innerWidth <= 768) {
        addMobileMenuButton();
    }
});

function addMobileMenuButton() {
    const headers = document.querySelectorAll('.main-header');
    headers.forEach(header => {
        const menuButton = document.createElement('button');
        menuButton.innerHTML = '<i class="fas fa-bars"></i>';
        menuButton.className = 'mobile-menu-btn';
        menuButton.onclick = toggleSidebar;
        header.querySelector('.header-left').prepend(menuButton);
    });
}

// Utility Functions
function formatDate(date) {
    return new Intl.DateTimeFormat('lv-LV', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function clearNotifications() {
    // Remove all existing notifications
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        notification.remove();
    });
}

// Export functions for global use
window.showAdminSection = showAdminSection;
window.showWarehouseSection = showWarehouseSection;
window.showShelfSection = showShelfSection;
window.logout = logout;
window.confirmReceive = confirmReceive;

// Settings functions
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
                applyTheme(data.settings.theme);
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
            if (key === 'theme') {
                applyTheme(value);
            }
            showNotification('Iestatījumi saglabāti', 'success');
        } else {
            showNotification('Kļūda saglabājot iestatījumus', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating setting:', error);
        showNotification('Kļūda saglabājot iestatījumus', 'error');
    });
}

function applyTheme(theme) {
    if (theme === 'dark') {
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
    }
}

// Load settings when the page loads
document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
}); 